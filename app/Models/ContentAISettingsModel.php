<?php namespace App\Models;

class ContentAISettingsModel extends BaseModel
{
    protected $table = 'content_ai_settings';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'auto_publish',
        'posts_per_day',
        'run_time_1',
        'run_time_2',
        'run_time_3',
        'last_run_1',
        'last_run_2',
        'last_run_3',
        'lang_id',
        'default_tone',
        'default_length',
        'allowed_category_ids',
        'auto_add_trends',
        'trends_per_day',
        'popular_enabled',
        'popular_posts_per_day',
        'popular_window_days',
        'popular_metric',
        'popular_min_pageviews',
        'popular_editor_prompt',
        'last_run_popular',
        'trend_keywords_json',
        'x_pulse_enabled',
        'x_window_hours',
        'x_themes_per_day',
        'x_min_mentions',
        'x_grok_model',
        'x_pulse_prompt',
        'last_run_x_pulse',
        'default_user_id',
        'voice_guidelines',
        'seo_guidelines',
        'prompt_template',
        'length_short_words',
        'length_medium_words',
        'length_long_words',
        'category_rules_json',
        'category_guidelines_json',
        'topic_weights_json',
        'editor_prompt',
        'image_guidelines',
        'image_prompt_template',
        'created_at',
        'updated_at',
    ];

    public function getSettings()
    {
        $row = $this->builder()->get()->getFirstRow();
        if (!empty($row)) {
            return $this->backfillDefaults($row);
        }
        $now = date('Y-m-d H:i:s');
        $defaultPrompt = $this->getDefaultPromptTemplate();
        $defaultImagePrompt = $this->getDefaultImagePromptTemplate();
        $defaultCategoryRules = $this->getDefaultCategoryRulesJson();
        $defaultCategoryGuidelines = $this->getDefaultCategoryGuidelinesJson();
        $data = [
            'auto_publish' => 0,
            'posts_per_day' => 1,
            'run_time_1' => '09:00:00',
            'run_time_2' => null,
            'run_time_3' => null,
            'lang_id' => $this->activeLang->id ?? null,
            'default_tone' => 'professional',
            'default_length' => 'medium',
            'allowed_category_ids' => null,
            'auto_add_trends' => 0,
            'trends_per_day' => 3,
            'popular_enabled' => 0,
            'popular_posts_per_day' => 0,
            'popular_window_days' => 7,
            'popular_metric' => 'mixed',
            'popular_min_pageviews' => 5,
            'popular_editor_prompt' => $this->getDefaultPopularEditorPrompt(),
            'trend_keywords_json' => json_encode(self::getDefaultTrendKeywords(), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
            'x_pulse_enabled' => 0,
            'x_window_hours' => 6,
            'x_themes_per_day' => 10,
            'x_min_mentions' => 100,
            'x_grok_model' => 'grok-4.3',
            'x_pulse_prompt' => $this->getDefaultXPulsePrompt(),
            'default_user_id' => 1,
            'voice_guidelines' => 'Tom de voz analitico, confiavel e didatico. Linguagem acessivel, sem jargoes excessivos. Evite sensacionalismo.',
            'seo_guidelines' => 'Use titulo curto (ate 60 caracteres), meta descricao com 150-160 caracteres, H2 com palavras-chave e variacoes naturais.',
            'prompt_template' => $defaultPrompt,
            'length_short_words' => 1000,
            'length_medium_words' => 1600,
            'length_long_words' => 2200,
            'category_rules_json' => $defaultCategoryRules,
            'category_guidelines_json' => $defaultCategoryGuidelines,
            'topic_weights_json' => $this->getDefaultTopicWeightsJson(),
            'editor_prompt' => $this->getDefaultEditorPrompt(),
            'image_guidelines' => 'Imagem editorial realista, sem textos ou logos, com composicao limpa e profissional.',
            'image_prompt_template' => $defaultImagePrompt,
            'created_at' => $now,
            'updated_at' => $now,
        ];
        $this->builder()->insert($data);
        return $this->builder()->get()->getFirstRow();
    }

    protected function backfillDefaults($row)
    {
        if (empty($row) || empty($row->id)) {
            return $row;
        }
        $patch = [];
        if (empty(trim((string) ($row->popular_editor_prompt ?? '')))) {
            $patch['popular_editor_prompt'] = $this->getDefaultPopularEditorPrompt();
        }
        if (empty(trim((string) ($row->editor_prompt ?? '')))) {
            $patch['editor_prompt'] = $this->getDefaultEditorPrompt();
        }
        if (!isset($row->popular_window_days) || (int) $row->popular_window_days <= 0) {
            $patch['popular_window_days'] = 7;
        }
        if (empty($row->popular_metric)) {
            $patch['popular_metric'] = 'mixed';
        }
        if (empty(trim((string) ($row->trend_keywords_json ?? '')))) {
            $patch['trend_keywords_json'] = json_encode(self::getDefaultTrendKeywords(), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }
        if (empty(trim((string) ($row->x_pulse_prompt ?? '')))) {
            $patch['x_pulse_prompt'] = $this->getDefaultXPulsePrompt();
        }
        if (empty($row->x_grok_model)) {
            $patch['x_grok_model'] = 'grok-4.3';
        }
        if (!isset($row->x_window_hours) || (int) $row->x_window_hours <= 0) {
            $patch['x_window_hours'] = 6;
        }
        if (!empty($patch)) {
            $this->builder()->where('id', $row->id)->update($patch);
            foreach ($patch as $k => $v) {
                $row->$k = $v;
            }
        }
        return $row;
    }

    /**
     * Canonical defaults for the 3 relevance keyword buckets.
     * Mirrors the previously-hardcoded list in ContentAIService::isTrendRelevant().
     */
    public static function getDefaultTrendKeywords(): array
    {
        return [
            'phrases' => [
                'mercado financeiro', 'balanca comercial', 'taxa de cambio', 'exchange rate',
                'capital de giro', 'spread bancario', 'carta de credito', 'banco central',
                'renda fixa', 'renda variavel', 'tesouro direto', 'divida publica',
                'reforma tributaria', 'guerra comercial', 'open banking', 'open finance',
                'banco digital', 'wall street', 'taxa selic', 'taxa de juros',
                'bolsa de valores', 'mercado de acoes', 'mercado de cambio',
                'politica monetaria', 'politica fiscal', 'comercio exterior',
                'balanca de pagamentos', 'reserva cambial', 'fluxo cambial',
                'operacao de credito', 'mercado de credito', 'mercado de capitais',
            ],
            'words' => [
                'cambio', 'cambial', 'dolar', 'forex', 'comex', 'exportacao', 'importacao',
                'exportadoras', 'importadoras', 'hedge', 'remessa', 'ptax',
                'credito', 'emprestimo', 'emprestimos', 'financiamento', 'juros',
                'recebiveis', 'bndes', 'duplicata', 'fidc', 'debenture', 'inadimplencia',
                'consorcio', 'consorcios',
                'economia', 'economica', 'economico', 'inflacao', 'ipca', 'igpm',
                'selic', 'bacen', 'copom', 'ibovespa', 'deficit', 'superavit',
                'investimento', 'investimentos', 'investir', 'investidores',
                'bitcoin', 'ethereum', 'criptomoeda', 'criptomoedas', 'cripto',
                'dividendo', 'dividendos', 'rentabilidade',
                'petrobras', 'nubank',
                'bolsa', 'commodities', 'petroleo',
                'compliance',
                'fintech', 'drex', 'pix',
                'desemprego',
                'trading',
            ],
            'context_words' => [
                'pib', 'fed', 'fomc', 'cdi', 'etf', 'ipo', 'fii', 'fiis', 'cri', 'cra', 'b3', 'nasdaq',
            ],
        ];
    }

    public function saveSettings(array $data): bool
    {
        $now = date('Y-m-d H:i:s');
        $data['updated_at'] = $now;
        $row = $this->builder()->get()->getFirstRow();
        if (empty($row)) {
            $data['created_at'] = $now;
            return (bool) $this->builder()->insert($data);
        }
        return (bool) $this->builder()->where('id', $row->id)->update($data);
    }

    public function updateLastRun(int $slot, string $datetime): bool
    {
        if ($slot < 1 || $slot > 3) {
            return false;
        }
        $field = 'last_run_' . $slot;
        return (bool) $this->builder()->set($field, $datetime)->update();
    }

    public function updateLastRunPopular(string $datetime): bool
    {
        return (bool) $this->builder()->set('last_run_popular', $datetime)->update();
    }

    public function updateLastRunXPulse(string $datetime): bool
    {
        return (bool) $this->builder()->set('last_run_x_pulse', $datetime)->update();
    }

    public function getDefaultXPulsePrompt(): string
    {
        return "Voce e um analista de pulso do mercado financeiro brasileiro.\n"
            . "Sua missao: usar a busca em tempo real no X (Twitter) para identificar os {themes_per_day} temas financeiros mais QUENTES nas ultimas {window_hours} horas, especificamente relevantes ao publico da GX Capital (empresarios, CFOs, tesoureiros, investidores brasileiros).\n\n"

            . "=== ESCOPO RIGIDO ===\n"
            . "Analise apenas temas dentro destes pilares:\n"
            . "- Cambio e trade finance (dolar, ptax, exportadores, importadores, hedge, comex, 4131, FX loans)\n"
            . "- Credito empresarial (BNDES, capital de giro, FIDC, debentures, juros para PJ, recebiveis)\n"
            . "- Consorcio (consorcio imobiliario, lance, contemplacao)\n"
            . "- Investimentos (renda fixa, renda variavel, FII, cripto, ETF, tesouro direto, B3)\n"
            . "- Economia e politica monetaria (Copom, Selic, inflacao, IPCA, Bacen, Fed, decisoes regulatorias)\n"
            . "- Empresas listadas com impacto sistemico (Petrobras, Vale, bancos, Nubank, fintechs)\n\n"

            . "PROIBIDO incluir: politica partidaria, esportes, celebridades, entretenimento, religiao, eventos sem impacto financeiro direto.\n\n"

            . "=== FONTES NO X ===\n"
            . "Priorize posts de:\n"
            . "- Contas institucionais (@bcbgovbr, @CVM_oficial, @PortalBacen, @Anbima)\n"
            . "- Jornalistas/analistas financeiros consagrados (BR e internacional)\n"
            . "- Cashtags relevantes (\$IBOV, \$PETR4, \$VALE3, \$ITUB4, \$BBDC4, \$USD, \$BTC, \$ETH)\n"
            . "- Hashtags do mercado (#mercadofinanceiro, #ibovespa, #dolar, #selic, #copom, #renda fixa, #investimentos)\n"
            . "- Posts com volume real de engajamento (centenas+ de interacoes), nao virais ocasionais\n\n"

            . "=== O QUE EXTRAIR (para cada tema) ===\n"
            . "- theme: descricao curta do tema/evento (max 120 chars)\n"
            . "- summary: 2-3 frases explicando o que esta sendo discutido E por que importa para o publico-alvo\n"
            . "- mentions_estimate: estimativa numerica de menções/posts no periodo (>= {min_mentions} para entrar na lista)\n"
            . "- sentiment: 'positive' | 'neutral' | 'negative' | 'mixed'\n"
            . "- tickers: array de cashtags/tickers envolvidos (ex: [\"PETR4\", \"USD\"])\n"
            . "- entities: array de instituicoes/atores citados (ex: [\"Bacen\", \"Fed\", \"Petrobras\"])\n"
            . "- relevance_score: 0-100, quao alinhado ao publico GX Capital (CFO/investidor PJ)\n\n"

            . "=== CRITERIOS DE QUALIDADE ===\n"
            . "1. PRIORIZE temas que ainda nao explodiram na imprensa tradicional — vantagem editorial vem da velocidade.\n"
            . "2. DIVERSIFIQUE: nao retorne 5 temas sobre dolar; pegue diferentes angulos do mercado.\n"
            . "3. IGNORE memes, especulacoes sem fundamento, fake news evidentes.\n"
            . "4. Se um tema tem polemica/controversia genuina entre analistas, marque sentiment='mixed' e detalhe no summary.\n"
            . "5. Se nao houver {themes_per_day} temas com qualidade suficiente, retorne menos — qualidade > quantidade.\n\n"

            . "=== FORMATO DE RESPOSTA (OBRIGATORIO) ===\n"
            . "Responda APENAS com JSON valido. Sem texto antes ou depois. Sem markdown.\n"
            . "{\"pulse\": [\n"
            . "  {\n"
            . "    \"theme\": \"...\",\n"
            . "    \"summary\": \"...\",\n"
            . "    \"mentions_estimate\": 0,\n"
            . "    \"sentiment\": \"positive|neutral|negative|mixed\",\n"
            . "    \"tickers\": [\"...\"],\n"
            . "    \"entities\": [\"...\"],\n"
            . "    \"relevance_score\": 0\n"
            . "  }\n"
            . "]}";
    }

    private function getDefaultPromptTemplate(): string
    {
        return "Voce e um editor da GX Capital com foco em SEO e credibilidade.\n"
            . "Categoria: {category_name}\n"
            . "Guidelines da categoria: {category_guidelines}\n"
            . "Tema/Titulo base: {title}\n"
            . "Instrucoes adicionais: {instructions}\n"
            . "Tom: {tone}\n"
            . "Tamanho: {length_words}\n"
            . "Guidelines de voz: {voice}\n"
            . "Guidelines de SEO: {seo}\n"
            . "Regras gerais:\n{rules}\n"
            . "Responda APENAS com JSON valido no formato:\n"
            . "{\n"
            . "  \"title\": \"...\",\n"
            . "  \"summary\": \"...\",\n"
            . "  \"content_html\": \"<p>...</p>\",\n"
            . "  \"tags\": [\"...\"],\n"
            . "  \"image_prompt\": \"...\"\n"
            . "}";
    }

    private function getDefaultCategoryRulesJson(): string
    {
        $rules = [
            11 => ['o que e', 'entenda', 'guia', 'glossario', 'jargao', 'termos', 'explica', 'definicao', 'como funciona'],
            6 => ['cambio', 'dolar', 'usd', 'forex', 'comex', 'exportacao', 'importacao', 'balanca comercial', 'swap', 'moeda'],
            8 => ['credito', 'emprestimo', 'financiamento', 'capital de giro', 'juros empresariais', 'recebiveis', 'bndes', 'duplicatas'],
            13 => ['investimento', 'carteira', 'renda fixa', 'acoes', 'fiis', 'fii', 'cripto', 'renda variavel', 'alocacao', 'portfolio', 'etf'],
            7 => ['economia', 'mercado financeiro', 'inflacao', 'pib', 'selic', 'banco central', 'bolsa', 'tecnologia', 'mundo'],
        ];
        return json_encode($rules);
    }

    private function getDefaultCategoryGuidelinesJson(): string
    {
        $guidelines = [
            7 => 'Radar Economico: noticias amplas de mercado financeiro, economia, mundo e tecnologia.',
            11 => 'GX Explica: linha editorial educacional, explica termos e noticias com linguagem simples e acessivel.',
            6 => 'Cambio: comex e noticias internacionais que impactam o cambio e o dolar.',
            13 => 'Investimentos: conteudos praticos e analises para carteira (renda fixa, acoes, FIIs, cambio, cripto e alternativos).',
            8 => 'Credito Empresarial: foco em credito, financiamento e capital de giro para empresas.',
        ];
        return json_encode($guidelines);
    }

    private function getDefaultTopicWeightsJson(): string
    {
        return json_encode([
            'cambio' => 20,
            'credito' => 20,
            'consorcio' => 20,
            'investimentos' => 15,
            'economia' => 25,
        ]);
    }

    public function getDefaultEditorPrompt(): string
    {
        return "Voce e o EDITOR-CHEFE da GX Capital, portal de inteligencia financeira referencia no Brasil.\n"
            . "Publico-alvo: empresarios, CFOs, tesoureiros, investidores e profissionais do mercado financeiro.\n"
            . "Missao: curar a pauta diaria selecionando os temas mais relevantes e estrategicos.\n\n"

            . "=== OBJETIVO ===\n"
            . "Selecione exatamente {posts_per_day} artigos da lista de candidatos para publicacao hoje.\n\n"

            . "=== DISTRIBUICAO DE TOPICOS ===\n"
            . "{topic_weights}\n"
            . "Respeite esses percentuais na selecao. Se houver 5 artigos e Cambio = 20%, pelo menos 1 deve ser de cambio.\n\n"

            . "=== CATEGORIAS DISPONIVEIS (use o ID exato) ===\n"
            . "{categories}\n\n"

            . "=== X PULSE (sinal complementar do que esta sendo discutido no X agora) ===\n"
            . "{x_pulse_context}\n"
            . "Se um candidato converge com um tema do X Pulse, ele tem MAIOR potencial de virar busca no Google nas proximas horas — priorize-o.\n\n"

            . "=== CRITERIOS DE SELECAO (em ordem de prioridade) ===\n"
            . "1. CONVERGENCIA DE SINAIS: se um candidato tambem aparece no X Pulse, ele e prioridade ALTA — sinal duplo de demanda.\n"
            . "2. RELEVANCIA TEMPORAL: priorize noticias quentes e fatos que impactam o mercado HOJE.\n"
            . "3. IMPACTO FINANCEIRO: o tema deve afetar decisoes de negocios, investimentos ou operacoes financeiras.\n"
            . "4. DIVERSIDADE: cada artigo deve ter um angulo UNICO. Nunca selecione dois temas muito parecidos.\n"
            . "5. POTENCIAL DE ENGAJAMENTO: prefira temas que gerem interesse e compartilhamento do publico-alvo.\n"
            . "6. AUTORIDADE EDITORIAL: inclua pelo menos 1 artigo educacional/analitico (tipo 'GX Explica') quando possivel.\n\n"

            . "=== RESTRICOES ===\n"
            . "- NAO selecione temas ja publicados recentemente: {recent_titles}\n"
            . "- NAO selecione temas de entretenimento, esportes, celebridades ou fofocas.\n"
            . "- NAO repita o mesmo angulo: se dois candidatos falam de 'dolar subindo', escolha apenas o mais completo.\n"
            . "- Se nenhum candidato for bom o suficiente para um topico, escolha o melhor disponivel e de instrucoes claras para o redator.\n\n"

            . "=== INSTRUCOES POR ARTIGO ===\n"
            . "Para cada artigo selecionado, escreva instrucoes DETALHADAS para o redator:\n"
            . "- Qual angulo editorial seguir (ex: impacto para exportadores, oportunidade de investimento, risco para PMEs)\n"
            . "- Que dados ou contexto incluir (ex: mencionar a variacao do dolar na semana, citar decisao do Copom)\n"
            . "- Tom sugerido (analitico, didatico, urgente, estrategico)\n"
            . "- Se deve incluir comparacoes, graficos descritivos ou exemplos praticos\n\n"

            . "=== FORMATO DE RESPOSTA ===\n"
            . "Responda APENAS com JSON valido. Sem texto antes ou depois.\n"
            . "{\"articles\": [\n"
            . "  {\n"
            . "    \"title\": \"Titulo otimizado para SEO (ate 60 caracteres)\",\n"
            . "    \"category_id\": N,\n"
            . "    \"instructions\": \"Instrucoes detalhadas para o redator...\",\n"
            . "    \"priority\": 1,\n"
            . "    \"reasoning\": \"Breve justificativa da selecao\"\n"
            . "  }\n"
            . "]}";
    }

    public function getDefaultPopularEditorPrompt(): string
    {
        return "=== IDENTIDADE ===\n"
            . "Voce e o EDITOR-CHEFE da GX Capital, portal brasileiro de inteligencia financeira referencia em cambio, credito empresarial, consorcio, investimentos e economia.\n"
            . "Atua como uma boutique de Porto Alegre/RS com 15+ anos de mesa em cambio, credito estruturado, trade finance e wealth management.\n"
            . "Publico-alvo: empresarios, CFOs, tesoureiros, controllers, exportadores/importadores, investidores qualificados e profissionais do mercado financeiro brasileiro.\n\n"

            . "=== CONTEXTO DESTA TAREFA ===\n"
            . "Voce NAO esta avaliando tendencias externas (Google Trends, RSS) — isto e feito por outro editor IA.\n"
            . "Voce esta analisando dados REAIS de comportamento dos leitores do nosso proprio portal:\n"
            . "uma lista dos posts MAIS POPULARES dos ultimos {window_days} dias, com pageviews, visitantes unicos e interacoes (comentarios + reacoes).\n"
            . "Metrica usada para ranquear: {metric} (mixed = 60% views + 40% engajamento; pageviews = apenas trafego; engagement = apenas interacoes).\n"
            . "O score reflete demanda REAL — leitores que JA chegaram e consumiram esse conteudo. Isto e o sinal mais valioso que temos.\n\n"

            . "=== MISSAO ===\n"
            . "Propor EXATAMENTE {popular_per_day} NOVOS artigos DERIVADOS dos posts populares acima.\n"
            . "Cada artigo proposto deve capitalizar o interesse demonstrado sem canibalizar o post original — voce esta construindo um CLUSTER de conteudo em volta de cada hit, nao reciclando.\n\n"

            . "=== COMO INTERPRETAR OS SINAIS ===\n"
            . "Use a combinacao de pageviews + interacoes + posicao no ranking para escolher a estrategia derivada:\n"
            . "- ALTO views + ALTA interacao -> tema quente e polarizante: gere CONTRAPONTO, RISCOS ou GUIA DE DECISAO.\n"
            . "- ALTO views + BAIXA interacao -> tema informacional consumido passivamente: gere APROFUNDAMENTO TECNICO ou TUTORIAL pratico.\n"
            . "- BAIXO views + ALTA interacao -> nicho engajado: gere CASO PRATICO de cliente ou ANGULO B2B/CFO.\n"
            . "- VARIOS posts no mesmo cluster semantico (ex: 3 posts sobre dolar) -> gere UM unico GUIA-MAE consolidando, NUNCA 3 derivados redundantes.\n"
            . "- Post antigo bombando agora -> identifique o GATILHO atual (noticia, regulacao, dado macro) e gere um UPDATE com data.\n\n"

            . "=== 8 ESTRATEGIAS DE DERIVACAO (escolha a melhor para cada caso) ===\n"
            . "1. APROFUNDAMENTO TECNICO: post explicou 'o que e X' -> escreva 'como X funciona na pratica' ou 'X passo a passo para empresas'.\n"
            . "2. ATUALIZACAO COM DADO NOVO: identifique a virada recente do cenario (Copom, Bacen, PTAX, dado IBGE, decisao do Fed) e atualize a tese.\n"
            . "3. CASO PRATICO: transforme o conceito em um exemplo numerico realista de uma empresa fictícia (PME exportadora, holding patrimonial, etc.).\n"
            . "4. ERROS E ARMADILHAS: 'X erros que empresarios cometem ao usar [tema do post popular]'.\n"
            . "5. COMPARATIVO/MATRIZ DE DECISAO: 'X vs Y — qual escolher em cada cenario'.\n"
            . "6. ANGULO B2B/CFO: pegue um tema generico e adapte para a otica de tesouraria, hedge, capital de giro ou MTM.\n"
            . "7. RECORTE SETORIAL: aplique o tema a um setor especifico (agro, industria, varejo, servicos exportadores, construcao).\n"
            . "8. CONTRAPONTO/RISCOS: se o post original e otimista sobre uma estrategia, escreva sobre os riscos, limites e contraindicacoes (sempre dentro do compliance YMYL).\n\n"

            . "=== CATEGORIAS DISPONIVEIS (use o ID exato em category_id) ===\n"
            . "{categories}\n\n"

            . "=== RESTRICOES CRITICAS ===\n"
            . "- NUNCA copie nem parafraseie o titulo original. O titulo derivado deve ter angulo claramente diferente.\n"
            . "- NAO selecione temas ja publicados/agendados recentemente: {recent_titles}\n"
            . "- ESCOPO RIGIDO: somente cambio, credito empresarial, consorcio, investimentos e economia/mercado financeiro brasileiro. NADA de entretenimento, esportes, celebridades, politica partidaria, religiao, opiniao pessoal.\n"
            . "- DIVERSIDADE OBRIGATORIA: no maximo 1 derivado por tema/cluster. Se 3 posts populares falam de dolar, escolha o MELHOR e gere 1 unico derivado.\n"
            . "- YMYL/COMPLIANCE: NUNCA prometer rentabilidade, garantir retorno, dar recomendacao personalizada nem solicitar servico. Conteudo informativo.\n"
            . "- E-E-A-T: cada pauta deve permitir ao redator demonstrar experiencia pratica de mesa (cambio/credito/wealth) — evite temas onde nao temos autoridade.\n"
            . "- Se um post popular for OFF-TOPIC para o escopo financeiro, IGNORE-O. Nao force um derivado fora do nosso pilar editorial.\n"
            . "- Se houver menos posts populares relevantes do que {popular_per_day}, retorne apenas os que fazem sentido — qualidade > quantidade.\n\n"

            . "=== INSTRUCOES POR ARTIGO (campo 'instructions') ===\n"
            . "Para cada artigo, escreva instrucoes DETALHADAS e ACIONAVEIS para o redator. Deve obrigatoriamente conter:\n"
            . "1. POST-BASE: 'Derivado do post popular ID=X — \"<titulo original>\" (pageviews=N, interacoes=N).'\n"
            . "2. ANGULO DERIVADO: uma das 8 estrategias acima, declarada explicitamente. Ex: 'Estrategia: APROFUNDAMENTO TECNICO.'\n"
            . "3. PROMESSA AO LEITOR: o que ele vai conseguir fazer/entender depois de ler (uma frase objetiva).\n"
            . "4. DADOS/CONTEXTO OBRIGATORIOS: liste 2-4 elementos concretos que devem aparecer (numero macro recente, normativo Bacen/CVM, instrumento financeiro, ator institucional).\n"
            . "5. ESTRUTURA SUGERIDA: bullet com os 3-5 H2 propostos.\n"
            . "6. TOM: analitico / didatico / estrategico / urgente.\n"
            . "7. CTA DE SIMULADOR (quando couber): indique qual simulador GX faz sentido linkar (risco cambial, FX Loan 4131, Aurum custo de capital, mercado de capitais, antecipacao FIDC, consorcio).\n\n"

            . "=== TITULO (campo 'title') ===\n"
            . "- Maximo 60 caracteres.\n"
            . "- Otimizado para SEO 2026 / AI Overviews: declarativo, especifico, com numero ou palavra-chave forte quando possivel.\n"
            . "- PROIBIDO clickbait, sensacionalismo e formulas batidas ('voce sabia', 'descubra agora', 'o segredo que ninguem te conta').\n"
            . "- PROIBIDO repetir a estrutura do titulo original.\n\n"

            . "=== FORMATO DE RESPOSTA (OBRIGATORIO) ===\n"
            . "Responda APENAS com JSON valido. Sem texto antes ou depois. Sem markdown. Sem comentarios.\n"
            . "{\"articles\": [\n"
            . "  {\n"
            . "    \"title\": \"Titulo SEO ate 60 caracteres\",\n"
            . "    \"category_id\": N,\n"
            . "    \"derived_from_post_id\": N,\n"
            . "    \"strategy\": \"aprofundamento_tecnico | atualizacao | caso_pratico | erros_armadilhas | comparativo | angulo_cfo | recorte_setorial | contraponto\",\n"
            . "    \"instructions\": \"Texto seguindo os 7 pontos da secao INSTRUCOES POR ARTIGO acima.\",\n"
            . "    \"reasoning\": \"1-2 frases: por que este derivado capitaliza o interesse demonstrado pelo post-base.\"\n"
            . "  }\n"
            . "]}";
    }

    private function getDefaultImagePromptTemplate(): string
    {
        return "Crie uma imagem editorial realista e sem texto.\n"
            . "Tema: {title}\n"
            . "Categoria: {category_name}\n"
            . "Direcao: {image_prompt}\n"
            . "Contexto curto: {summary}\n"
            . "Guidelines da categoria: {category_guidelines}\n"
            . "Guidelines de imagem: {image_guidelines}\n"
            . "Requisitos: composicao limpa, foco central, iluminacao natural, estilo profissional.";
    }
}
