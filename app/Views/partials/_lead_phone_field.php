<?php

use App\Libraries\LeadPhoneFormatter;

$fieldIdPrefix = trim((string) ($fieldIdPrefix ?? 'gx-phone'));
$countryName = trim((string) ($countryName ?? 'phone_country'));
$phoneName = trim((string) ($phoneName ?? 'phone'));
$wrapperClass = trim((string) ($wrapperClass ?? ''));
$rowClass = trim((string) ($rowClass ?? ''));
$countryClass = trim((string) ($countryClass ?? ''));
$inputClass = trim((string) ($inputClass ?? ''));
$hintClass = trim((string) ($hintClass ?? ''));
$labelClass = trim((string) ($labelClass ?? ''));
$hint = trim((string) ($hint ?? ''));
$showLabel = !isset($label) || $label !== false;
$fieldLabel = trim((string) ($label ?? trans('phone')));
$required = !isset($required) || $required !== false;
$selectedCountry = LeadPhoneFormatter::sanitizeCountry($countryValue ?? old($countryName) ?? LeadPhoneFormatter::DEFAULT_COUNTRY);
$phoneValue = trim((string) ($phoneValue ?? old($phoneName)));
$countries = LeadPhoneFormatter::getCountries();
$placeholder = $countries[$selectedCountry]['placeholder'] ?? '';
$formattedPhoneValue = $phoneValue !== '' ? LeadPhoneFormatter::formatNational($selectedCountry, $phoneValue) : '';
$countrySelectId = $fieldIdPrefix . '-country';
$phoneInputId = $fieldIdPrefix . '-number';
$countryMetadata = [];

foreach ($countries as $code => $country) {
    $countryMetadata[$code] = [
        'dialCode' => $country['dial_code'],
        'dialLabel' => $country['dial_label'],
        'placeholder' => $country['placeholder'],
        'minDigits' => $country['min_digits'],
        'maxDigits' => $country['max_digits'],
    ];
}
?>
<div class="<?= esc(trim('gx-phone-field ' . $wrapperClass)); ?>" data-gx-phone-field>
    <?php if ($showLabel): ?>
        <label class="<?= esc($labelClass); ?>" for="<?= esc($phoneInputId); ?>"><?= esc($fieldLabel); ?></label>
    <?php endif; ?>
    <div class="<?= esc(trim('gx-phone-row ' . $rowClass)); ?>">
        <select
            id="<?= esc($countrySelectId); ?>"
            name="<?= esc($countryName); ?>"
            class="<?= esc(trim('gx-phone-country ' . $countryClass)); ?>"
            aria-label="País do telefone"
            data-gx-phone-country
            <?= $required ? 'required' : ''; ?>>
            <?php foreach ($countries as $code => $country): ?>
                <option value="<?= esc($code); ?>" <?= $selectedCountry === $code ? 'selected' : ''; ?>>
                    <?= esc($country['name'] . ' (' . $country['dial_label'] . ')'); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <input
            id="<?= esc($phoneInputId); ?>"
            type="tel"
            name="<?= esc($phoneName); ?>"
            class="<?= esc(trim('gx-phone-number ' . $inputClass)); ?>"
            value="<?= esc($formattedPhoneValue); ?>"
            placeholder="<?= esc($placeholder); ?>"
            autocomplete="tel-national"
            inputmode="numeric"
            maxlength="24"
            aria-label="Número do telefone"
            data-gx-phone-input
            <?= $required ? 'required' : ''; ?>>
    </div>
    <?php if ($hint !== ''): ?>
        <small class="<?= esc(trim('gx-phone-hint ' . $hintClass)); ?>"><?= esc($hint); ?></small>
    <?php endif; ?>
</div>

<?php if (!defined('GX_LEAD_PHONE_FIELD_ASSETS')): ?>
    <?php define('GX_LEAD_PHONE_FIELD_ASSETS', true); ?>
    <style>
        .gx-phone-field {
            display: grid;
            gap: 4px;
        }

        .gx-phone-row {
            display: grid;
            grid-template-columns: minmax(136px, 0.46fr) minmax(0, 1fr);
            gap: 10px;
        }

        .gx-phone-country,
        .gx-phone-number {
            width: 100%;
        }

        .gx-phone-country {
            background-position: right 12px center;
            background-repeat: no-repeat;
            background-size: 10px 6px;
            padding-right: 36px;
        }

        .gx-phone-hint {
            font-size: 12px;
            opacity: 0.78;
        }

        .gx-form-field .gx-phone-country {
            height: 44px;
            padding: 10px 36px 10px 14px;
            border-radius: var(--gx-radius);
            border: 1px solid var(--gx-border);
            background-color: var(--gx-bg);
            color: var(--gx-text);
            font-family: var(--gx-font-body);
            font-size: 15px;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6' viewBox='0 0 10 6' fill='none'%3E%3Cpath d='M1 1L5 5L9 1' stroke='%23606E7C' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
        }

        .gx-form-field .gx-phone-country:focus {
            outline: 0;
            border-color: var(--gx-navy-50);
            box-shadow: 0 0 0 3px var(--gx-navy-08);
        }

        @media (max-width: 640px) {
            .gx-phone-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
    <script>
        (function () {
            var countryMetadata = <?= json_encode($countryMetadata, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;

            function digits(value) {
                return String(value || '').replace(/\D+/g, '');
            }

            function getCountryMeta(countryCode) {
                return countryMetadata[countryCode] || countryMetadata.BR;
            }

            function stripDialCode(countryCode, value) {
                var meta = getCountryMeta(countryCode);
                var numericValue = digits(value);

                if (!numericValue) {
                    return '';
                }

                if (meta.dialCode === '1' && numericValue.length === meta.maxDigits + 1 && numericValue.charAt(0) === '1') {
                    numericValue = numericValue.substring(1);
                } else if (numericValue.length > meta.maxDigits && numericValue.indexOf(meta.dialCode) === 0) {
                    var withoutDialCode = numericValue.substring(meta.dialCode.length);
                    if (withoutDialCode && withoutDialCode.length <= meta.maxDigits) {
                        numericValue = withoutDialCode;
                    }
                }

                return numericValue.substring(0, meta.maxDigits);
            }

            function formatGrouped(value, groups) {
                var numericValue = digits(value);
                var parts = [];
                var offset = 0;

                groups.forEach(function (size) {
                    if (offset >= numericValue.length) {
                        return;
                    }

                    parts.push(numericValue.substring(offset, offset + size));
                    offset += size;
                });

                if (offset < numericValue.length) {
                    parts.push(numericValue.substring(offset));
                }

                return parts.join(' ');
            }

            function formatBrazil(value) {
                var numericValue = digits(value).substring(0, 11);

                if (numericValue.length <= 2) {
                    return '(' + numericValue;
                }

                var ddd = numericValue.substring(0, 2);
                var remaining = numericValue.substring(2);

                if (numericValue.length <= 6) {
                    return '(' + ddd + ') ' + remaining;
                }

                var firstBlockLength = numericValue.length > 10 ? 5 : 4;
                var firstBlock = remaining.substring(0, firstBlockLength);
                var tail = remaining.substring(firstBlockLength);

                return '(' + ddd + ') ' + firstBlock + (tail ? '-' + tail : '');
            }

            function formatNanp(value) {
                var numericValue = digits(value).substring(0, 10);

                if (numericValue.length <= 3) {
                    return '(' + numericValue;
                }

                if (numericValue.length <= 6) {
                    return '(' + numericValue.substring(0, 3) + ') ' + numericValue.substring(3);
                }

                return '(' + numericValue.substring(0, 3) + ') ' + numericValue.substring(3, 6) + '-' + numericValue.substring(6);
            }

            function formatByCountry(countryCode, value) {
                var numericValue = stripDialCode(countryCode, value);

                switch (countryCode) {
                    case 'BR':
                        return formatBrazil(numericValue);
                    case 'US':
                    case 'CA':
                        return formatNanp(numericValue);
                    case 'PT':
                    case 'ES':
                        return formatGrouped(numericValue, [3, 3, 3]);
                    case 'MX':
                    case 'AR':
                        return formatGrouped(numericValue, [2, 4, 4]);
                    case 'CL':
                        return formatGrouped(numericValue, [1, 4, 4]);
                    case 'CO':
                        return formatGrouped(numericValue, [3, 3, 4]);
                    case 'GB':
                        return formatGrouped(numericValue, [5, 6]);
                    case 'FR':
                        return formatGrouped(numericValue, [2, 2, 2, 2, 2]);
                    case 'DE':
                        return formatGrouped(numericValue, [3, 3, 4, 4]);
                    case 'IT':
                        return formatGrouped(numericValue, [3, 3, 4]);
                    case 'CH':
                        return formatGrouped(numericValue, [2, 3, 2, 2]);
                    case 'AE':
                        return formatGrouped(numericValue, [2, 3, 4]);
                    default:
                        return formatGrouped(numericValue, [3, 3, 4, 4]);
                }
            }

            function bindPhoneField(field) {
                if (!field || field.getAttribute('data-gx-phone-bound') === '1') {
                    return;
                }

                var countryInput = field.querySelector('[data-gx-phone-country]');
                var phoneInput = field.querySelector('[data-gx-phone-input]');

                if (!countryInput || !phoneInput) {
                    return;
                }

                var syncPhoneField = function () {
                    var meta = getCountryMeta(countryInput.value);
                    phoneInput.placeholder = meta.placeholder;
                    phoneInput.value = formatByCountry(countryInput.value, phoneInput.value);
                };

                field.setAttribute('data-gx-phone-bound', '1');
                countryInput.addEventListener('change', syncPhoneField);
                phoneInput.addEventListener('input', syncPhoneField);
                syncPhoneField();
            }

            window.__gxLeadPhoneFieldInit = function (root) {
                var scope = root || document;
                var fields = scope.querySelectorAll('[data-gx-phone-field]');
                fields.forEach(bindPhoneField);
            };

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function () {
                    window.__gxLeadPhoneFieldInit(document);
                });
            } else {
                window.__gxLeadPhoneFieldInit(document);
            }
        })();
    </script>
<?php endif; ?>
