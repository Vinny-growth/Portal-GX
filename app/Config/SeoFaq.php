<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Conteúdo de FAQ das páginas-pilar e simuladores (GEO/SEO — Fase 2).
 *
 * Fonte única e editável do FAQ, sem tocar em templates. As MESMAS perguntas
 * aqui alimentam (a) o acordeão visível (view common/_faq_section) e (b) o schema
 * FAQPage (helper jsonldFaqPage) — mantendo texto visível e dado estruturado em
 * sincronia, como exige o Google.
 *
 * Compliance YMYL: respostas informativas, sem promessa de retorno/aprovação;
 * câmbio = estruturação/assessoria (não instituição emissora); crédito sempre
 * menciona CET e sujeito a análise; simuladores são ferramentas informativas.
 */
class SeoFaq extends BaseConfig
{
    /** FAQ indexado por ID de categoria (páginas-pilar / verticais). */
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

        // 8 = Crédito Empresarial
        8 => [
            [
                'q' => 'O que é o CET (Custo Efetivo Total) de uma operação de crédito?',
                'a' => 'O CET é o custo total de uma operação de crédito expresso em taxa anual, incluindo juros, tarifas, tributos e demais encargos. É o número que permite comparar propostas de forma justa: duas linhas com a mesma taxa nominal podem ter CET bem diferente. Toda operação de crédito está sujeita a análise, e o CET deve sempre ser verificado antes de contratar.',
            ],
            [
                'q' => 'O que é crédito estruturado?',
                'a' => 'Crédito estruturado é o desenho de uma operação de dívida sob medida para a empresa — combinando linha, prazo, garantias e instrumentos mais adequados ao fluxo de caixa e ao objetivo, em vez de aceitar um produto padrão de balcão. Pode envolver recebíveis, garantias reais, debêntures, FIDC ou funding internacional.',
            ],
            [
                'q' => 'Vale a pena antecipar recebíveis via FIDC?',
                'a' => 'Depende do custo de antecipação (deságio) frente à alternativa de crédito e à necessidade de caixa. Um FIDC pode oferecer condições competitivas para carteiras de recebíveis pulverizadas, mas o custo efetivo precisa ser comparado com outras linhas. O ideal é simular o custo real antes de decidir.',
            ],
            [
                'q' => 'O que são covenants em um contrato de crédito?',
                'a' => 'Covenants são cláusulas de compromisso que a empresa assume com o credor — por exemplo, manter índices financeiros (alavancagem, cobertura de juros) dentro de limites. Descumpri-los pode acelerar a dívida. Entender e negociar os covenants e o headroom faz parte da estruturação da operação.',
            ],
            [
                'q' => 'Como reduzir o spread bancário da minha empresa?',
                'a' => 'O spread reflete o risco percebido pelo credor. Melhorar garantias, organizar as demonstrações financeiras, diversificar credores e apresentar a operação de forma estruturada tendem a reduzir o spread. Não há garantia de aprovação nem de taxa — cada operação passa por análise de crédito.',
            ],
            [
                'q' => 'A GX Capital empresta dinheiro?',
                'a' => 'Não. A GX Capital é uma boutique que estrutura e assessora operações de crédito junto a instituições financeiras — não é a credora. Ajudamos a empresa a escolher a linha certa, organizar garantias e negociar condições, sempre observando o CET.',
            ],
        ],
    ];

    /** FAQ indexado por slug de página (simuladores servidos pelo CMS). */
    public array $bySlug = [
        'aurum-simulador-de-custo-de-capital' => [
            [
                'q' => 'O que é custo de capital (WACC)?',
                'a' => 'O custo de capital, ou WACC (Custo Médio Ponderado de Capital), é a taxa que representa o custo combinado das fontes de financiamento da empresa — dívida e capital próprio — ponderado pela participação de cada uma. É a referência mínima de retorno que um projeto precisa superar para gerar valor.',
            ],
            [
                'q' => 'Por que o WACC importa nas decisões de investimento?',
                'a' => 'Porque um projeto só cria valor quando o retorno esperado supera o custo de capital. O WACC é usado para descontar fluxos de caixa em valuation e para comparar alternativas de investimento e de financiamento de forma técnica.',
            ],
            [
                'q' => 'O simulador substitui uma análise financeira profissional?',
                'a' => 'Não. O simulador é uma ferramenta educativa que estima o custo de capital com base nos dados informados. Decisões de investimento e de financiamento devem considerar a realidade completa da empresa e passar por análise dedicada.',
            ],
        ],
        'simulador-mercado-de-capitais' => [
            [
                'q' => 'O que é o mercado de capitais?',
                'a' => 'É o ambiente em que empresas captam recursos diretamente de investidores por meio de instrumentos como debêntures, notas comerciais, CRI, CRA e FIDC, em vez de tomar crédito bancário tradicional. Costuma ser usado para volumes maiores e prazos mais longos.',
            ],
            [
                'q' => 'Quando faz sentido captar via mercado de capitais em vez de banco?',
                'a' => 'Em geral quando o volume, o prazo ou o custo justificam a estruturação de uma emissão — e a empresa tem porte e governança para acessá-lo. A comparação com o crédito bancário deve considerar o custo efetivo total e os prazos de estruturação.',
            ],
            [
                'q' => 'Este simulador é uma oferta de investimento?',
                'a' => 'Não. É uma ferramenta informativa de estruturação. Não constitui oferta, recomendação ou garantia de captação; qualquer operação depende de análise e das condições de mercado.',
            ],
        ],
        'simulador-de-custo-de-antecipacao' => [
            [
                'q' => 'Como funciona a antecipação de recebíveis?',
                'a' => 'A empresa recebe hoje, com desconto, valores que teria a prazo (duplicatas, cartão, contratos). O desconto aplicado é o deságio, que remunera quem antecipa. É uma forma de capital de giro que não gera dívida tradicional, mas tem custo.',
            ],
            [
                'q' => 'O que é deságio?',
                'a' => 'Deságio é o desconto aplicado sobre o valor de face do recebível na antecipação. Convertido em taxa e prazo, ele forma o custo efetivo da operação — que deve ser comparado com outras linhas de capital de giro.',
            ],
            [
                'q' => 'Como o simulador calcula o custo da antecipação?',
                'a' => 'A partir do valor a antecipar, do prazo médio e da taxa/deságio informados, o simulador estima o custo da operação para você comparar cenários. Os números são estimativos e não representam proposta.',
            ],
        ],
        'linhas-credito-bndes' => [
            [
                'q' => 'O que são as linhas de crédito do BNDES?',
                'a' => 'São programas de financiamento do Banco Nacional de Desenvolvimento Econômico e Social voltados a investimento, capital de giro associado e projetos específicos, geralmente com prazos mais longos. As condições variam por programa e porte da empresa.',
            ],
            [
                'q' => 'Qual a diferença entre BNDES direto e indireto?',
                'a' => 'No modo direto, a empresa acessa o BNDES diretamente, normalmente em operações maiores. No indireto, o crédito é intermediado por um agente financeiro credenciado (banco), que faz a análise e a concessão. A maior parte das operações é indireta.',
            ],
            [
                'q' => 'As condições do BNDES são garantidas para qualquer empresa?',
                'a' => 'Não. O acesso depende de enquadramento no programa, de análise de crédito e da disponibilidade de recursos. O simulador ajuda a comparar cenários, mas não garante aprovação nem condições.',
            ],
        ],
    ];

    /** Título da seção de FAQ por categoria (usado no H2 visível). */
    public array $titleByCategoryId = [
        6 => 'Câmbio e hedge: perguntas frequentes',
        8 => 'Crédito empresarial: perguntas frequentes',
    ];

    /** Título da seção de FAQ por slug de simulador. */
    public array $titleBySlug = [
        'aurum-simulador-de-custo-de-capital' => 'Custo de capital: perguntas frequentes',
        'simulador-mercado-de-capitais'       => 'Mercado de capitais: perguntas frequentes',
        'simulador-de-custo-de-antecipacao'   => 'Antecipação de recebíveis: perguntas frequentes',
        'linhas-credito-bndes'                => 'Linhas de crédito BNDES: perguntas frequentes',
    ];

    /** Retorna o FAQ de uma categoria (array vazio se não houver). */
    public function forCategoryId(int $id): array
    {
        return $this->byCategoryId[$id] ?? [];
    }

    /** Retorna o FAQ de um slug de página/simulador (array vazio se não houver). */
    public function forSlug(string $slug): array
    {
        return $this->bySlug[$slug] ?? [];
    }

    /** Título da seção de FAQ da categoria (fallback genérico). */
    public function titleForCategoryId(int $id): string
    {
        return $this->titleByCategoryId[$id] ?? 'Perguntas frequentes';
    }

    /** Título da seção de FAQ do slug (fallback genérico). */
    public function titleForSlug(string $slug): string
    {
        return $this->titleBySlug[$slug] ?? 'Perguntas frequentes';
    }
}
