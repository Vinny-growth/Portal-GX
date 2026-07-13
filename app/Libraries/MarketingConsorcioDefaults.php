<?php

namespace App\Libraries;

use Config\Globals;

class MarketingConsorcioDefaults
{
    protected $langId;

    public function __construct($langId = null)
    {
        $activeLang = Globals::$activeLang ?? null;
        $this->langId = !empty($langId) ? (int)$langId : (int)(!empty($activeLang) && !empty($activeLang->id) ? $activeLang->id : 1);
    }

    public function getPageConfigDefaults()
    {
        return [
            'seo' => [
                'og_image' => '',
            ],
            'testimonials' => [
                'enabled' => 1,
                'label' => lang('Consorcio.testi_label'),
                'title' => lang('Consorcio.testi_title'),
                'items' => [
                    [
                        'enabled' => 1,
                        'name' => lang('Consorcio.testi_i1_name'),
                        'city' => lang('Consorcio.testi_i1_city'),
                        'text' => brandLang('Consorcio.testi_i1_text'),
                        'photo_url' => '',
                    ],
                    [
                        'enabled' => 1,
                        'name' => lang('Consorcio.testi_i2_name'),
                        'city' => lang('Consorcio.testi_i2_city'),
                        'text' => brandLang('Consorcio.testi_i2_text'),
                        'photo_url' => '',
                    ],
                    [
                        'enabled' => 1,
                        'name' => lang('Consorcio.testi_i3_name'),
                        'city' => lang('Consorcio.testi_i3_city'),
                        'text' => brandLang('Consorcio.testi_i3_text'),
                        'photo_url' => '',
                    ],
                ],
            ],
        ];
    }
}
