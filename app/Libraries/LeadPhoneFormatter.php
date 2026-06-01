<?php namespace App\Libraries;

class LeadPhoneFormatter
{
    public const DEFAULT_COUNTRY = 'BR';

    public static function getCountries()
    {
        return [
            'BR' => [
                'name' => 'Brasil',
                'dial_code' => '55',
                'dial_label' => '+55',
                'min_digits' => 10,
                'max_digits' => 11,
                'placeholder' => '(11) 99999-9999',
            ],
            'US' => [
                'name' => 'Estados Unidos',
                'dial_code' => '1',
                'dial_label' => '+1',
                'min_digits' => 10,
                'max_digits' => 10,
                'placeholder' => '(201) 555-0123',
            ],
            'CA' => [
                'name' => 'Canadá',
                'dial_code' => '1',
                'dial_label' => '+1',
                'min_digits' => 10,
                'max_digits' => 10,
                'placeholder' => '(416) 555-0123',
            ],
            'PT' => [
                'name' => 'Portugal',
                'dial_code' => '351',
                'dial_label' => '+351',
                'min_digits' => 9,
                'max_digits' => 9,
                'placeholder' => '912 345 678',
            ],
            'ES' => [
                'name' => 'Espanha',
                'dial_code' => '34',
                'dial_label' => '+34',
                'min_digits' => 9,
                'max_digits' => 9,
                'placeholder' => '612 345 678',
            ],
            'MX' => [
                'name' => 'México',
                'dial_code' => '52',
                'dial_label' => '+52',
                'min_digits' => 10,
                'max_digits' => 10,
                'placeholder' => '55 1234 5678',
            ],
            'AR' => [
                'name' => 'Argentina',
                'dial_code' => '54',
                'dial_label' => '+54',
                'min_digits' => 10,
                'max_digits' => 10,
                'placeholder' => '11 2345 6789',
            ],
            'CL' => [
                'name' => 'Chile',
                'dial_code' => '56',
                'dial_label' => '+56',
                'min_digits' => 9,
                'max_digits' => 9,
                'placeholder' => '9 1234 5678',
            ],
            'CO' => [
                'name' => 'Colômbia',
                'dial_code' => '57',
                'dial_label' => '+57',
                'min_digits' => 10,
                'max_digits' => 10,
                'placeholder' => '320 123 4567',
            ],
            'GB' => [
                'name' => 'Reino Unido',
                'dial_code' => '44',
                'dial_label' => '+44',
                'min_digits' => 10,
                'max_digits' => 11,
                'placeholder' => '7400 123456',
            ],
            'FR' => [
                'name' => 'França',
                'dial_code' => '33',
                'dial_label' => '+33',
                'min_digits' => 9,
                'max_digits' => 10,
                'placeholder' => '06 12 34 56 78',
            ],
            'DE' => [
                'name' => 'Alemanha',
                'dial_code' => '49',
                'dial_label' => '+49',
                'min_digits' => 10,
                'max_digits' => 11,
                'placeholder' => '151 234 5678',
            ],
            'IT' => [
                'name' => 'Itália',
                'dial_code' => '39',
                'dial_label' => '+39',
                'min_digits' => 9,
                'max_digits' => 10,
                'placeholder' => '312 345 6789',
            ],
            'CH' => [
                'name' => 'Suíça',
                'dial_code' => '41',
                'dial_label' => '+41',
                'min_digits' => 9,
                'max_digits' => 9,
                'placeholder' => '79 123 45 67',
            ],
            'AE' => [
                'name' => 'Emirados Árabes',
                'dial_code' => '971',
                'dial_label' => '+971',
                'min_digits' => 9,
                'max_digits' => 9,
                'placeholder' => '50 123 4567',
            ],
        ];
    }

    public static function getCountryCodes()
    {
        return array_keys(self::getCountries());
    }

    public static function sanitizeCountry($countryCode)
    {
        $countryCode = strtoupper(trim((string) $countryCode));
        $countries = self::getCountries();

        if (!array_key_exists($countryCode, $countries)) {
            return self::DEFAULT_COUNTRY;
        }

        return $countryCode;
    }

    public static function getCountry($countryCode = null)
    {
        $countryCode = self::sanitizeCountry($countryCode);
        $countries = self::getCountries();

        return $countries[$countryCode];
    }

    public static function extractDigits($value)
    {
        return preg_replace('/\D+/', '', (string) $value);
    }

    public static function normalizeDigits($countryCode, $phone)
    {
        $countryCode = self::sanitizeCountry($countryCode);
        $country = self::getCountry($countryCode);
        $digits = self::extractDigits($phone);

        if ($digits === '') {
            return '';
        }

        $dialCode = $country['dial_code'];
        $maxDigits = (int) $country['max_digits'];

        if ($dialCode === '1' && strlen($digits) === $maxDigits + 1 && strpos($digits, '1') === 0) {
            $digits = substr($digits, 1);
        } elseif (strlen($digits) > $maxDigits && strpos($digits, $dialCode) === 0) {
            $withoutDialCode = substr($digits, strlen($dialCode));
            if ($withoutDialCode !== '' && strlen($withoutDialCode) <= $maxDigits) {
                $digits = $withoutDialCode;
            }
        }

        return $digits;
    }

    public static function isValid($countryCode, $phone)
    {
        $digits = self::normalizeDigits($countryCode, $phone);
        if ($digits === '') {
            return false;
        }

        $country = self::getCountry($countryCode);
        $length = strlen($digits);

        return $length >= (int) $country['min_digits'] && $length <= (int) $country['max_digits'];
    }

    public static function formatNational($countryCode, $phone)
    {
        $countryCode = self::sanitizeCountry($countryCode);
        $digits = self::normalizeDigits($countryCode, $phone);

        if ($digits === '') {
            return '';
        }

        switch ($countryCode) {
            case 'BR':
                return self::formatBrazil($digits);
            case 'US':
            case 'CA':
                return self::formatNanp($digits);
            case 'PT':
            case 'ES':
                return self::formatGrouped($digits, [3, 3, 3]);
            case 'MX':
            case 'AR':
                return self::formatGrouped($digits, [2, 4, 4]);
            case 'CL':
                return self::formatGrouped($digits, [1, 4, 4]);
            case 'CO':
                return self::formatGrouped($digits, [3, 3, 4]);
            case 'GB':
                return self::formatGrouped($digits, [5, 6]);
            case 'FR':
                return self::formatGrouped($digits, [2, 2, 2, 2, 2]);
            case 'DE':
                return self::formatGrouped($digits, [3, 3, 4, 4]);
            case 'IT':
                return self::formatGrouped($digits, [3, 3, 4]);
            case 'CH':
                return self::formatGrouped($digits, [2, 3, 2, 2]);
            case 'AE':
                return self::formatGrouped($digits, [2, 3, 4]);
            default:
                return self::formatGeneric($digits);
        }
    }

    public static function toInternational($countryCode, $phone)
    {
        $countryCode = self::sanitizeCountry($countryCode);
        if (!self::isValid($countryCode, $phone)) {
            return null;
        }

        $country = self::getCountry($countryCode);
        $formattedPhone = self::formatNational($countryCode, $phone);

        return trim($country['dial_label'] . ' ' . $formattedPhone);
    }

    protected static function formatBrazil($digits)
    {
        $digits = substr($digits, 0, 11);
        $length = strlen($digits);

        if ($length <= 2) {
            return '(' . $digits;
        }

        $ddd = substr($digits, 0, 2);
        $rest = substr($digits, 2);

        if ($length <= 6) {
            return '(' . $ddd . ') ' . $rest;
        }

        $firstBlock = $length > 10 ? 5 : 4;
        $main = substr($rest, 0, $firstBlock);
        $tail = substr($rest, $firstBlock);

        return '(' . $ddd . ') ' . $main . ($tail !== '' ? '-' . $tail : '');
    }

    protected static function formatNanp($digits)
    {
        $digits = substr($digits, 0, 10);
        $length = strlen($digits);

        if ($length <= 3) {
            return '(' . $digits;
        }

        if ($length <= 6) {
            return '(' . substr($digits, 0, 3) . ') ' . substr($digits, 3);
        }

        return '(' . substr($digits, 0, 3) . ') ' . substr($digits, 3, 3) . '-' . substr($digits, 6);
    }

    protected static function formatGrouped($digits, $groups)
    {
        $digits = self::extractDigits($digits);
        $parts = [];
        $offset = 0;

        foreach ($groups as $groupLength) {
            if ($offset >= strlen($digits)) {
                break;
            }

            $chunk = substr($digits, $offset, $groupLength);
            if ($chunk === '') {
                break;
            }

            $parts[] = $chunk;
            $offset += $groupLength;
        }

        if ($offset < strlen($digits)) {
            $parts[] = substr($digits, $offset);
        }

        return implode(' ', $parts);
    }

    protected static function formatGeneric($digits)
    {
        return self::formatGrouped($digits, [3, 3, 4, 4]);
    }
}
