<?php namespace App\Models;

class MarketingHomeModel extends BaseModel
{
    protected $tableName = 'marketing_settings';

    public function __construct()
    {
        parent::__construct();
        $this->ensureTable();
    }

    public function getHomeConfig($langId, array $defaults = [])
    {
        return $this->getJsonSetting(
            'institutional_home_content',
            $langId,
            $defaults,
            $this->getConfigCacheKey($langId)
        );
    }

    public function saveHomeConfig($langId, array $config)
    {
        return $this->saveJsonSetting(
            'institutional_home_content',
            $langId,
            $config,
            $this->getConfigCacheKey($langId)
        );
    }

    public function getSimulatorsHubConfig($langId, array $defaults = [])
    {
        return $this->getJsonSetting(
            'simulators_hub_content',
            $langId,
            $defaults,
            $this->getSimulatorsCacheKey($langId)
        );
    }

    public function saveSimulatorsHubConfig($langId, array $config)
    {
        return $this->saveJsonSetting(
            'simulators_hub_content',
            $langId,
            $config,
            $this->getSimulatorsCacheKey($langId)
        );
    }

    public function getConsorcioPageConfig($langId, array $defaults = [])
    {
        return $this->getJsonSetting(
            'consorcio_page_content',
            $langId,
            $defaults,
            $this->getConsorcioCacheKey($langId)
        );
    }

    public function saveConsorcioPageConfig($langId, array $config)
    {
        return $this->saveJsonSetting(
            'consorcio_page_content',
            $langId,
            $config,
            $this->getConsorcioCacheKey($langId)
        );
    }

    public function getValue($key, $langId, $default = null)
    {
        try {
            $row = $this->db->table($this->tableName)
                ->where('lang_id', (int)$langId)
                ->where('setting_key', $key)
                ->get()
                ->getRow();
            return !empty($row) ? $row->setting_value : $default;
        } catch (\Throwable $e) {
            log_message('error', 'MarketingHomeModel::getValue failed: ' . $e->getMessage());
            return $default;
        }
    }

    public function setValue($key, $langId, $value)
    {
        try {
            $builder = $this->db->table($this->tableName);
            $row = $builder->where('lang_id', (int)$langId)->where('setting_key', $key)->get()->getRow();
            $data = [
                'lang_id' => (int)$langId,
                'setting_key' => $key,
                'setting_value' => $value,
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            if (!empty($row)) {
                return (bool)$builder->where('id', (int)$row->id)->update($data);
            }
            $data['created_at'] = date('Y-m-d H:i:s');
            return (bool)$builder->insert($data);
        } catch (\Throwable $e) {
            log_message('error', 'MarketingHomeModel::setValue failed: ' . $e->getMessage());
            return false;
        }
    }

    protected function ensureTable()
    {
        $tableCacheKey = $this->getTableCacheKey();
        if (cache($tableCacheKey) === '1') {
            return;
        }

        try {
            if ($this->db->tableExists($this->tableName)) {
                cache()->save($tableCacheKey, '1', 86400);
                return;
            }
            $forge = \Config\Database::forge();
            $forge->addField([
                'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'lang_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'default' => 1],
                'setting_key' => ['type' => 'VARCHAR', 'constraint' => 191],
                'setting_value' => ['type' => 'LONGTEXT', 'null' => true],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
                'updated_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $forge->addKey('id', true);
            $forge->addKey(['lang_id', 'setting_key']);
            $forge->createTable($this->tableName, true);
            cache()->save($tableCacheKey, '1', 86400);
        } catch (\Throwable $e) {
            log_message('error', 'MarketingHomeModel::ensureTable failed: ' . $e->getMessage());
        }
    }

    protected function getConfigCacheKey($langId)
    {
        return 'marketing_home_config_lang_' . (int)$langId;
    }

    protected function getSimulatorsCacheKey($langId)
    {
        return 'marketing_simulators_config_lang_' . (int)$langId;
    }

    protected function getConsorcioCacheKey($langId)
    {
        return 'marketing_consorcio_config_lang_' . (int)$langId;
    }

    protected function getTableCacheKey()
    {
        return 'marketing_home_settings_table_ready';
    }

    protected function getJsonSetting($settingKey, $langId, array $defaults = [], $cacheKey = null)
    {
        if ($cacheKey !== null) {
            $cached = cache($cacheKey);
            if (is_array($cached)) {
                return $this->mergeConfig($defaults, $cached);
            }
        }

        $raw = $this->getValue($settingKey, $langId);
        $decoded = @json_decode((string)$raw, true);
        if (!is_array($decoded)) {
            return $defaults;
        }

        if ($cacheKey !== null) {
            cache()->save($cacheKey, $decoded, 600);
        }

        return $this->mergeConfig($defaults, $decoded);
    }

    protected function saveJsonSetting($settingKey, $langId, array $config, $cacheKey = null)
    {
        $json = json_encode($config, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $result = $this->setValue($settingKey, $langId, (string)$json);

        if ($cacheKey === null) {
            return $result;
        }

        if ($result) {
            cache()->save($cacheKey, $config, 600);
        } else {
            cache()->delete($cacheKey);
        }

        return $result;
    }

    protected function mergeConfig($defaults, $stored)
    {
        if (!is_array($defaults) || !is_array($stored)) {
            return $stored;
        }
        if ($this->isListArray($defaults) || $this->isListArray($stored)) {
            return $stored;
        }

        $merged = $defaults;
        foreach ($stored as $key => $value) {
            if (array_key_exists($key, $defaults)) {
                $merged[$key] = $this->mergeConfig($defaults[$key], $value);
            } else {
                $merged[$key] = $value;
            }
        }
        return $merged;
    }

    protected function isListArray(array $array)
    {
        if (empty($array)) {
            return true;
        }
        return array_keys($array) === range(0, count($array) - 1);
    }
}
