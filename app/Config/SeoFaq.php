<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Conteúdo de FAQ das páginas-pilar (GEO/SEO — Fase 2).
 *
 * Fonte única e editável do FAQ, sem tocar em templates. As MESMAS perguntas
 * aqui alimentam (a) o acordeão visível (view common/_faq_section) e (b) o schema
 * FAQPage (helper jsonldFaqPage) — mantendo texto visível e dado estruturado em
 * sincronia, como exige o Google.
 *
 * Compliance YMYL: respostas informativas, sem promessa de retorno/aprovação;
 * câmbio descrito como estruturação/assessoria (não instituição emissora).
 */
class SeoFaq extends BaseConfig
{
    /** FAQ indexado por ID de categoria. */
    public array $byCategoryId = [
        // 6 = Câmbio
        6 => [
            [
                'q' => 'O que é hedge cambial e quando faz sentido para a minha empresa?',
                'a' => 'Hedge cambial é a proteção contra a variação da taxa de câmbio sobre operações em moeda estrangeira. Faz sentido para importadores, exportadores e empresas com receita ou dívida em dólar/euro que querem travar o custo ou a receita e proteger a margem operacional. Os instrumentos mais comuns são NDF, contrato a termo, opções e swap; a escolha depende do prazo, do fluxo e da tolerância a risco. É uma estruturação sob medida, não um produto de prateleira.',
            ],
            [
                'q' => 'Qual a diferença entre NDF, ACC e ACE?',
                'a' => 'O NDF (Non Deliverable Forward) é um derivativo que trava uma taxa futura sem entrega física da moeda, liquidado pela diferença contra a PTAX — serve para hedge. O ACC (Adiantamento sobre Contrato de Câmbio) antecipa recursos ao exportador antes do embarque, e o ACE (Adiantamento sobre Cambiais Entregues) antecipa após o embarque — ambos são financiamento à exportação com câmbio embutido. Em resumo: NDF protege a taxa; ACC e ACE antecipam caixa.',
            ],
            [
                'q' => 'O que é a PTAX e por que ela importa no fechamento de câmbio?',
                'a' => 'PTAX é a taxa de referência do dólar apurada e divulgada pelo Banco Central a partir de consultas ao mercado ao longo do dia. Ela é usada como referência na liquidação de contratos como o NDF e em diversas obrigações contratuais. Conhecer a PTAX ajuda a empresa a comparar o custo real de fechamento e a evitar surpresas na liquidação.',
            ],
            [
                'q' => 'O que é uma operação 4131?',
                'a' => 'É uma captação de recursos no exterior regulada pela Resolução CMN 4.131, na qual a empresa toma um empréstimo internacional, geralmente em moeda estrangeira. Pode ter custo competitivo, mas cria exposição cambial que costuma ser combinada com hedge. É avaliada por empresas que buscam diversificar fontes de funding ou que já têm receita em moeda forte. As condições dependem de análise de crédito.',
            ],
            [
                'q' => 'A GX Capital é um banco ou uma corretora de câmbio?',
                'a' => 'Não. A GX Capital é uma boutique financeira que estrutura e assessora operações de câmbio e trade finance — não é a instituição que emite ou liquida o câmbio. Atuamos junto às instituições autorizadas pelo Banco Central para desenhar a estrutura mais eficiente para cada operação da empresa.',
            ],
            [
                'q' => 'Como a minha empresa começa a estruturar câmbio com a GX Capital?',
                'a' => 'O primeiro passo é um diagnóstico do fluxo em moeda estrangeira: volumes, prazos, exposição e objetivos. A partir disso desenhamos a estrutura mais adequada (hedge, antecipação ou funding) e apresentamos os cenários. Você pode começar pelos simuladores de câmbio da GX Capital ou falar diretamente com um especialista.',
            ],
        ],
    ];

    /** Título da seção de FAQ por categoria (usado no H2 visível). */
    public array $titleByCategoryId = [
        6 => 'Câmbio e hedge: perguntas frequentes',
    ];

    /** Retorna o FAQ de uma categoria (array vazio se não houver). */
    public function forCategoryId(int $id): array
    {
        return $this->byCategoryId[$id] ?? [];
    }

    /** Título da seção de FAQ da categoria (fallback genérico). */
    public function titleForCategoryId(int $id): string
    {
        return $this->titleByCategoryId[$id] ?? 'Perguntas frequentes';
    }
}
