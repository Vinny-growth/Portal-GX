<?php

namespace App\Libraries;

use Config\Globals;

class MarketingSimulatorsDefaults
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
            'hero' => [
                'badge' => lang('Fx.def_hero_badge'),
                'title' => lang('Fx.def_hero_title'),
                'subtitle' => brandLang('Fx.def_hero_subtitle'),
                'primary_cta_label' => lang('Fx.def_hero_primary_cta'),
                'primary_cta_url' => '#lead-cambio',
                'secondary_cta_label' => lang('Fx.def_hero_secondary_cta'),
                'secondary_cta_url' => '#laboratorio-cambio',
            ],
            'hero_proof' => lang('Fx.def_hero_proof'),
            'technology' => [
                'label' => brandLang('Fx.def_tech_label'),
                'title' => lang('Fx.def_tech_title'),
                'description' => lang('Fx.def_tech_desc'),
                'stat_primary_value' => '10+',
                'stat_primary_label' => lang('Fx.def_tech_stat1_label'),
                'stat_secondary_value' => '16+',
                'stat_secondary_label' => lang('Fx.def_tech_stat2_label'),
                'stat_tertiary_value' => '360°',
                'stat_tertiary_label' => lang('Fx.def_tech_stat3_label'),
                'signals' => lang('Fx.def_tech_signals'),
            ],
            'indicators' => [
                'reference_label' => lang('Fx.def_ind_ref_label'),
                'reference_date' => date('m/Y'),
                'usd_brl' => '5.20',
                'commercial_spread' => '1.10',
                'iof' => '0.38',
                'selic' => '14.25',
                'cdi' => '14.15',
                'ipca_12m' => '4.80',
                'sofr' => '4.70',
                'hedge_cost_monthly' => '0.65',
                'onshore_spread' => '4.50',
                'offshore_spread' => '3.20',
                'trade_finance_fee' => '1.20',
                'stress_scenario' => '7.50',
                'importer_target_margin' => '18.00',
                'exporter_floor_rate' => '5.00',
                'note' => lang('Fx.def_ind_note'),
            ],
            'lead' => [
                'label' => lang('Fx.def_lead_label'),
                'title' => brandLang('Fx.def_lead_title'),
                'description' => lang('Fx.def_lead_desc'),
                'form_title' => lang('Fx.def_lead_form_title'),
                'form_description' => lang('Fx.def_lead_form_desc'),
                'button_label' => lang('Fx.def_lead_button'),
                'success_message' => brandLang('Fx.def_lead_success'),
            ],
        ];
    }
}
