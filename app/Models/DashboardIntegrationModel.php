<?php namespace App\Models;

class DashboardIntegrationModel extends BaseModel
{
    private const SECRET_PREFIX_ENCRYPTED = 'enc:';
    private const SECRET_PREFIX_PLAIN = 'plain:';

    protected $encrypter = null;
    protected bool $encrypterInitialized = false;

    public function getSetting($key, $default = null, $decrypt = false)
    {
        $row = $this->db->table('dashboard_settings')
            ->where('chave', (string) $key)
            ->get()
            ->getRow();

        if (!$row) {
            return $default;
        }

        $value = $row->valor;
        if ($decrypt) {
            $value = $this->revealValue($value);
        }

        return $value === null || $value === '' ? $default : $value;
    }

    public function setSetting($key, $value, $encrypt = false)
    {
        $key = (string) $key;

        if ($value === null || $value === '') {
            return $this->deleteSetting($key);
        }

        $storedValue = $encrypt ? $this->protectValue((string) $value) : (string) $value;
        $existing = $this->db->table('dashboard_settings')
            ->where('chave', $key)
            ->get()
            ->getRow();

        if ($existing) {
            return $this->db->table('dashboard_settings')
                ->where('chave', $key)
                ->update(['valor' => $storedValue]);
        }

        return $this->db->table('dashboard_settings')->insert([
            'chave' => $key,
            'valor' => $storedValue,
        ]);
    }

    public function deleteSetting($key)
    {
        return $this->db->table('dashboard_settings')
            ->where('chave', (string) $key)
            ->delete();
    }

    public function getGoogleAnalyticsConnection()
    {
        return [
            'client_id' => (string) $this->getSetting('ga4_client_id', ''),
            'client_secret' => (string) $this->getSetting('ga4_client_secret', '', true),
            'access_token' => (string) $this->getSetting('ga4_access_token', '', true),
            'refresh_token' => (string) $this->getSetting('ga4_refresh_token', '', true),
            'token_expires_at' => $this->getSetting('ga4_token_expires_at'),
            'scope' => (string) $this->getSetting('ga4_scope', ''),
            'connected_email' => (string) $this->getSetting('ga4_connected_email', ''),
            'connected_at' => $this->getSetting('ga4_connected_at'),
            'property_id' => (string) $this->getSetting('ga4_property_id', ''),
            'property_name' => (string) $this->getSetting('ga4_property_name', ''),
            'property_resource' => (string) $this->getSetting('ga4_property_resource', ''),
            'account_id' => (string) $this->getSetting('ga4_account_id', ''),
            'account_name' => (string) $this->getSetting('ga4_account_name', ''),
            'last_error' => (string) $this->getSetting('ga4_last_error', ''),
        ];
    }

    public function getGoogleAnalyticsStatus()
    {
        $connection = $this->getGoogleAnalyticsConnection();
        $status = $connection;

        unset(
            $status['client_secret'],
            $status['access_token'],
            $status['refresh_token']
        );

        $status['credentials_configured'] = !empty($connection['client_id']) && !empty($connection['client_secret']);
        $status['is_connected'] = !empty($connection['refresh_token']);
        $status['property_selected'] = !empty($connection['property_id']);
        $status['is_ready'] = $status['credentials_configured'] && $status['is_connected'] && $status['property_selected'];
        $status['has_client_secret'] = !empty($connection['client_secret']);
        $status['has_refresh_token'] = !empty($connection['refresh_token']);
        $status['client_id_masked'] = $this->maskValue($connection['client_id']);

        return $status;
    }

    public function saveGoogleAnalyticsCredentials($clientId, $clientSecret)
    {
        $result = $this->setSetting('ga4_client_id', trim((string) $clientId));
        if ($clientSecret !== null && $clientSecret !== '') {
            $result = $this->setSetting('ga4_client_secret', trim((string) $clientSecret), true) && $result;
        }

        return $result;
    }

    public function saveGoogleAnalyticsToken(array $tokenPayload)
    {
        $existing = $this->getGoogleAnalyticsConnection();
        $refreshToken = !empty($tokenPayload['refresh_token']) ? $tokenPayload['refresh_token'] : ($existing['refresh_token'] ?? '');
        $connectedAt = !empty($existing['connected_at']) ? $existing['connected_at'] : date('Y-m-d H:i:s');

        $this->setSetting('ga4_access_token', $tokenPayload['access_token'] ?? '', true);
        $this->setSetting('ga4_refresh_token', $refreshToken, true);
        $this->setSetting('ga4_token_expires_at', $tokenPayload['token_expires_at'] ?? null);
        $this->setSetting('ga4_scope', $tokenPayload['scope'] ?? '');
        $this->setSetting('ga4_connected_email', $tokenPayload['connected_email'] ?? ($existing['connected_email'] ?? ''));
        $this->setSetting('ga4_connected_at', $tokenPayload['connected_at'] ?? $connectedAt);

        if (array_key_exists('last_error', $tokenPayload)) {
            $this->setSetting('ga4_last_error', $tokenPayload['last_error'] ?? '');
        }

        return true;
    }

    public function saveGoogleAnalyticsProperty(array $property)
    {
        $this->setSetting('ga4_property_id', $property['property_id'] ?? '');
        $this->setSetting('ga4_property_name', $property['property_name'] ?? '');
        $this->setSetting('ga4_property_resource', $property['property_resource'] ?? '');
        $this->setSetting('ga4_account_id', $property['account_id'] ?? '');
        $this->setSetting('ga4_account_name', $property['account_name'] ?? '');

        return true;
    }

    public function clearGoogleAnalyticsConnection($clearCredentials = false)
    {
        $keys = [
            'ga4_access_token',
            'ga4_refresh_token',
            'ga4_token_expires_at',
            'ga4_scope',
            'ga4_connected_email',
            'ga4_connected_at',
            'ga4_property_id',
            'ga4_property_name',
            'ga4_property_resource',
            'ga4_account_id',
            'ga4_account_name',
            'ga4_last_error',
        ];

        if ($clearCredentials) {
            $keys[] = 'ga4_client_id';
            $keys[] = 'ga4_client_secret';
        }

        foreach ($keys as $key) {
            $this->deleteSetting($key);
        }

        return true;
    }

    public function setGoogleAnalyticsLastError($message)
    {
        if (empty($message)) {
            return $this->clearGoogleAnalyticsLastError();
        }

        return $this->setSetting('ga4_last_error', $message);
    }

    public function clearGoogleAnalyticsLastError()
    {
        return $this->deleteSetting('ga4_last_error');
    }

    private function maskValue($value)
    {
        $value = (string) $value;
        if ($value === '') {
            return '';
        }

        $length = strlen($value);
        if ($length <= 10) {
            return str_repeat('*', max(0, $length - 4)) . substr($value, -4);
        }

        return substr($value, 0, 6) . str_repeat('*', max(0, $length - 10)) . substr($value, -4);
    }

    private function protectValue($value)
    {
        if ($value === null || $value === '') {
            return '';
        }

        $encrypter = $this->getEncrypter();
        if ($encrypter) {
            try {
                return self::SECRET_PREFIX_ENCRYPTED . base64_encode($encrypter->encrypt($value));
            } catch (\Throwable $e) {
                log_message('error', 'DashboardIntegrationModel: encryption failed — ' . $e->getMessage());
            }
        }

        log_message('warning', 'DashboardIntegrationModel: storing credential with base64 only — encryption unavailable');
        return self::SECRET_PREFIX_PLAIN . base64_encode($value);
    }

    private function revealValue($value)
    {
        $value = (string) $value;
        if ($value === '') {
            return '';
        }

        if (strpos($value, self::SECRET_PREFIX_ENCRYPTED) === 0) {
            $payload = substr($value, strlen(self::SECRET_PREFIX_ENCRYPTED));
            $encrypter = $this->getEncrypter();
            if (!$encrypter) {
                return '';
            }

            try {
                return (string) $encrypter->decrypt(base64_decode($payload));
            } catch (\Throwable $e) {
                return '';
            }
        }

        if (strpos($value, self::SECRET_PREFIX_PLAIN) === 0) {
            return (string) base64_decode(substr($value, strlen(self::SECRET_PREFIX_PLAIN)));
        }

        return $value;
    }

    private function getEncrypter()
    {
        if ($this->encrypterInitialized) {
            return $this->encrypter;
        }

        $this->encrypterInitialized = true;
        try {
            $this->encrypter = \Config\Services::encrypter();
        } catch (\Throwable $e) {
            $this->encrypter = null;
        }

        return $this->encrypter;
    }
}
