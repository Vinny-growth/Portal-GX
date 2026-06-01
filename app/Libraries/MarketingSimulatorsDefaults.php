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
                'badge' => 'Mesa de câmbio com tecnologia e leitura consultiva',
                'title' => 'Simuladores de câmbio para importar, exportar, proteger margem e estruturar funding com mais clareza.',
                'subtitle' => 'Use cenários para importação, exportação, hedge, trade finance e operações 4131. Depois, a GX Capital compara sua operação em mais de 10 instituições financeiras e apresenta a alternativa mais aderente ao seu momento.',
                'primary_cta_label' => 'Agendar com especialista',
                'primary_cta_url' => '#lead-cambio',
                'secondary_cta_label' => 'Abrir simuladores',
                'secondary_cta_url' => '#laboratorio-cambio',
            ],
            'hero_proof' => [
                ['text' => 'Mais de 10 instituições financeiras entre bancos de câmbio e corretoras no processo de cotação.'],
                ['text' => 'Mais de 16 anos de experiência do time no mercado financeiro e em operações internacionais.'],
                ['text' => 'Leitura integrada de preço, prazo, proteção, funding e execução operacional.'],
            ],
            'technology' => [
                'label' => 'Mesa GX Capital',
                'title' => 'Tecnologia para comparar. Experiência para fechar a operação certa.',
                'description' => 'A mesa usa agente de IA para cruzar cotação, spread, prazo, documentação e aderência operacional entre múltiplas instituições. O foco não é a menor taxa isolada. É a estrutura mais eficiente para a necessidade real do cliente.',
                'stat_primary_value' => '10+',
                'stat_primary_label' => 'instituições financeiras monitoradas',
                'stat_secondary_value' => '16+',
                'stat_secondary_label' => 'anos de experiência no mercado financeiro',
                'stat_tertiary_value' => '360°',
                'stat_tertiary_label' => 'visão sobre câmbio, hedge, trade finance e funding',
                'signals' => [
                    ['text' => 'Cotação comparativa entre bancos de câmbio e corretoras, com leitura de custo total e não só de taxa nominal.'],
                    ['text' => 'Estruturação para importadores e exportadores com foco em margem, previsibilidade de caixa e timing operacional.'],
                    ['text' => 'Diagnóstico consultivo para hedge, ACC, ACE, FINIMP, supplier credit e alternativas 4131.'],
                ],
            ],
            'indicators' => [
                'reference_label' => 'Indicadores de referência',
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
                'note' => 'Os resultados usam referências de mercado atualizadas periodicamente. O fechamento final depende da leitura da mesa, das instituições consultadas e das condições da operação.',
            ],
            'lead' => [
                'label' => 'Leve o cenário para a mesa',
                'title' => 'Agende uma conversa com a mesa de câmbio GX Capital.',
                'description' => 'Preencha os dados abaixo e nossa mesa entra em contato para agendar a conversa. O retorno considera cotação, hedge, trade finance e melhor janela de execução.',
                'form_title' => 'Agende sua reunião com a mesa',
                'form_description' => 'Preencha os dados para reservar sua conversa. Retorno em até 1 dia útil.',
                'button_label' => 'Quero falar com um especialista',
                'success_message' => 'Recebemos o seu cenário. A mesa de câmbio da GX Capital vai entrar em contato para agendar a conversa.',
            ],
        ];
    }
}
