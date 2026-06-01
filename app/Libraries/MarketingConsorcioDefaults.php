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
                'label' => 'Quem já simulou',
                'title' => 'Quem já simulou, aprovou',
                'items' => [
                    [
                        'enabled' => 1,
                        'name' => 'Maria S.',
                        'city' => 'São Paulo',
                        'text' => 'Com a simulação da GX Capital, descobri que economizaria mais de R$ 40 mil comparado ao financiamento. Fechei meu consórcio com confiança.',
                        'photo_url' => '',
                    ],
                    [
                        'enabled' => 1,
                        'name' => 'Carlos R.',
                        'city' => 'Porto Alegre',
                        'text' => 'O simulador mostrou exatamente o lance que eu precisava dar para ser contemplado em 8 meses. Estratégia certeira.',
                        'photo_url' => '',
                    ],
                    [
                        'enabled' => 1,
                        'name' => 'Ana L.',
                        'city' => 'Belo Horizonte',
                        'text' => 'Achava que financiamento era minha única opção. A GX Capital me mostrou um caminho que cabe no meu bolso.',
                        'photo_url' => '',
                    ],
                ],
            ],
        ];
    }
}
