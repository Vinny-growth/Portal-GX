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
        'default_user_id',
        'voice_guidelines',
        'seo_guidelines',
        'prompt_template',
        'length_short_words',
        'length_medium_words',
        'length_long_words',
        'category_rules_json',
        'category_guidelines_json',
        'image_guidelines',
        'image_prompt_template',
        'created_at',
        'updated_at',
    ];

    public function getSettings()
    {
        $row = $this->builder()->get()->getFirstRow();
        if (!empty($row)) {
            return $row;
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
            'default_user_id' => 1,
            'voice_guidelines' => 'Tom de voz analitico, confiavel e didatico. Linguagem acessivel, sem jargoes excessivos. Evite sensacionalismo.',
            'seo_guidelines' => 'Use titulo curto (ate 60 caracteres), meta descricao com 150-160 caracteres, H2 com palavras-chave e variacoes naturais.',
            'prompt_template' => $defaultPrompt,
            'length_short_words' => 1000,
            'length_medium_words' => 1600,
            'length_long_words' => 2200,
            'category_rules_json' => $defaultCategoryRules,
            'category_guidelines_json' => $defaultCategoryGuidelines,
            'image_guidelines' => 'Imagem editorial realista, sem textos ou logos, com composicao limpa e profissional.',
            'image_prompt_template' => $defaultImagePrompt,
            'created_at' => $now,
            'updated_at' => $now,
        ];
        $this->builder()->insert($data);
        return $this->builder()->get()->getFirstRow();
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
