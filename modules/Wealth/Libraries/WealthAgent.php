<?php

namespace Modules\Wealth\Libraries;

use Modules\Wealth\Models\WealthModel;

class WealthAgent
{
    protected $wm;

    // Sequence of question steps with simple keys
    protected $steps = [
        'consent',
        'perfil_estado_civil',
        'perfil_ano_nascimento',
        'perfil_dependentes',
        'perfil_profissao',
        'renda_fontes',
        'despesas',
        'ativos_financeiros',
        'ativos_imobiliarios',
        'negocios',
        'passivos',
        'metas',
        'risco',
        'preferencias',
        'finalizar',
    ];

    public function __construct()
    {
        $this->wm = new WealthModel();
    }

    public function firstQuestion()
    {
        return "Antes de começarmos, você autoriza o uso dos seus dados para planejamento financeiro e contato comercial? Responda 'Sim' para prosseguir.";
    }

    public function getNextPrompt($userId)
    {
        $profile = $this->getProfile($userId);

        if (empty($profile) || empty($profile->consent_accepted_at)) {
            return $this->firstQuestion();
        }
        if (empty($profile->estado_civil)) {
            return "Qual seu estado civil? (solteiro, casado, divorciado, viúvo)";
        }
        if (empty($profile->ano_nascimento)) {
            return "Qual seu ano de nascimento aproximado? (ex: 1985)";
        }
        // income at least one
        $hasIncome = $this->hasAtLeastOne($userId, 'wm_income_expense', ['tipo' => 'renda']);
        if (!$hasIncome) {
            return "Sobre sua renda mensal líquida: informe fontes e valores. Ex: salário 12000, aluguéis 2000";
        }
        // expenses
        $hasExpense = $this->hasAtLeastOne($userId, 'wm_income_expense', ['tipo' => 'despesa']);
        if (!$hasExpense) {
            return "Agora despesas mensais: informe categorias e valores. Ex: moradia 4000, educação 1500";
        }
        // assets financial minimal
        $hasAssetsFin = $this->tableHas($userId, 'wm_assets_financial');
        if (!$hasAssetsFin) {
            return "Patrimônio financeiro aproximado por classe: caixa 50000, CDB 80000, fundos 30000, ações 40000, previdência 20000";
        }
        // real estate optional
        $hasReal = $this->tableHas($userId, 'wm_assets_realestate');
        if (!$hasReal) {
            return "Possui imóveis? Informe tipo e valores. Ex: apartamento 800000, dívida 200000, aluguel 2500 (se houver)";
        }
        // liabilities
        $hasDebt = $this->tableHas($userId, 'wm_liabilities');
        if (!$hasDebt) {
            return "Tem empréstimos/financiamentos? Informe tipo, saldo, taxa e prazo em meses. Ex: financiamento 200000, taxa 10%, 180 meses";
        }
        // goals
        $hasGoals = $this->tableHas($userId, 'wm_goals');
        if (!$hasGoals) {
            return "Quais suas metas? Ex: aposentadoria 2.000.000 em 180 meses (prioridade alta), faculdade filhos 300.000 em 120 meses";
        }
        if (empty($profile->perfil_risco)) {
            return "Como descreve seu perfil de risco? (conservador, moderado, arrojado). E horizonte de investimento principal? (curto, médio, longo)";
        }
        // preferences captured in observacoes
        return "Obrigado! Se tiver preferências (ex: evitar setores, liquidez, necessidade de renda), escreva agora. Caso contrário, digite 'Finalizar'";
    }

    public function handleUserMessage($userId, $message)
    {
        $message = trim($message ?? '');
        $lower = mb_strtolower($message, 'UTF-8');

        // Consent
        $profile = $this->getProfile($userId);
        if (empty($profile) || empty($profile->consent_accepted_at)) {
            if (strpos($lower, 'sim') !== false || strpos($lower, 'aceito') !== false || strpos($lower, 'concordo') !== false) {
                $this->wm->upsertProfile($userId, ['consent_accepted_at' => date('Y-m-d H:i:s')]);
                return "Perfeito! Vamos começar. " . $this->getNextPrompt($userId);
            }
            return "Para prosseguir, por favor responda 'Sim'.";
        }

        // Try parsers
        if ($this->parseEstadoCivil($userId, $lower)) {
            return "Certo. " . $this->getNextPrompt($userId);
        }
        if ($this->parseAnoNascimento($userId, $lower)) {
            return "Obrigado. " . $this->getNextPrompt($userId);
        }
        if ($this->parseRendas($userId, $message)) {
            return "Anotado. " . $this->getNextPrompt($userId);
        }
        if ($this->parseDespesas($userId, $message)) {
            return "Ok. " . $this->getNextPrompt($userId);
        }
        if ($this->parseAtivosFinanceiros($userId, $message)) {
            return "Certo. " . $this->getNextPrompt($userId);
        }
        if ($this->parseImoveis($userId, $message)) {
            return "Entendido. " . $this->getNextPrompt($userId);
        }
        if ($this->parsePassivos($userId, $message)) {
            return "Obrigado. " . $this->getNextPrompt($userId);
        }
        if ($this->parseMetas($userId, $message)) {
            return "Registrado. " . $this->getNextPrompt($userId);
        }
        if ($this->parseRiscoHorizonte($userId, $lower)) {
            return "Perfeito. " . $this->getNextPrompt($userId);
        }
        if (strpos($lower, 'finalizar') !== false) {
            return "Sessão finalizada. Veja o resumo na página de resultados.";
        }
        // Save preferences merging into JSON observacoes
        $this->mergeObservacoes($userId, ['preferencias' => $message]);
        return "Anotei suas preferências. " . $this->getNextPrompt($userId);
    }

    protected function getProfile($userId)
    {
        return $this->wm->db->table('wm_user_profile')->where('user_id', clrNum($userId))->get()->getRow();
    }

    protected function hasAtLeastOne($userId, $table, $where = [])
    {
        $builder = $this->wm->db->table($table)->where('user_id', clrNum($userId));
        foreach ($where as $k => $v) { $builder->where($k, $v); }
        return (bool)$builder->limit(1)->get()->getRowArray();
    }

    protected function tableHas($userId, $table)
    {
        return (bool)$this->wm->db->table($table)->where('user_id', clrNum($userId))->limit(1)->get()->getRowArray();
    }

    protected function mergeObservacoes($userId, array $data)
    {
        $row = $this->wm->db->table('wm_user_profile')->where('user_id', clrNum($userId))->get()->getRow();
        $obs = [];
        if (!empty($row) && !empty($row->observacoes)) {
            $decoded = json_decode($row->observacoes, true);
            if (is_array($decoded)) { $obs = $decoded; }
        }
        $merged = array_merge($obs, $data);
        $this->wm->upsertProfile($userId, ['observacoes' => json_encode($merged, JSON_UNESCAPED_UNICODE)]);
    }

    // Parsers
    protected function parseEstadoCivil($userId, $text)
    {
        $options = ['solteiro', 'solteira', 'casado', 'casada', 'divorciado', 'divorciada', 'viúvo', 'viuva', 'viúva'];
        foreach ($options as $opt) {
            if (strpos($text, $opt) !== false) {
                $this->wm->upsertProfile($userId, ['estado_civil' => $opt]);
                return true;
            }
        }
        return false;
    }

    protected function parseAnoNascimento($userId, $text)
    {
        if (preg_match('/(19\d{2}|20\d{2})/', $text, $m)) {
            $ano = intval($m[1]);
            if ($ano >= 1900 && $ano <= intval(date('Y'))) {
                $this->wm->upsertProfile($userId, ['ano_nascimento' => $ano]);
                return true;
            }
        }
        return false;
    }

    protected function parsePairs($text)
    {
        // Parse "categoria valor" pairs separated by commas
        $pairs = [];
        $parts = preg_split('/[,\n]+/', $text);
        foreach ($parts as $p) {
            if (preg_match('/([\p{L} ]+)\s+([\d\.,]+)/u', trim($p), $m)) {
                $label = trim($m[1]);
                $val = floatval(str_replace(['.', ','], ['', '.'], $m[2]));
                if ($val > 0) $pairs[] = [$label, $val];
            }
        }
        return $pairs;
    }

    protected function parseRendas($userId, $text)
    {
        $pairs = $this->parsePairs($text);
        $ok = false;
        foreach ($pairs as [$cat, $val]) {
            $this->wm->addIncomeExpense($userId, 'renda', $cat, $val);
            $ok = true;
        }
        return $ok;
    }

    protected function parseDespesas($userId, $text)
    {
        $pairs = $this->parsePairs($text);
        $ok = false;
        foreach ($pairs as [$cat, $val]) {
            $this->wm->addIncomeExpense($userId, 'despesa', $cat, $val);
            $ok = true;
        }
        return $ok;
    }

    protected function parseAtivosFinanceiros($userId, $text)
    {
        $pairs = $this->parsePairs($text);
        $ok = false;
        foreach ($pairs as [$cat, $val]) {
            $this->wm->addFinancialAsset($userId, $cat, null, $val);
            $ok = true;
        }
        return $ok;
    }

    protected function parseImoveis($userId, $text)
    {
        // Try to capture: tipo X, valor Y, (dívida Z), (aluguel W)
        $ok = false;
        // Find numeric values
        if (preg_match_all('/([\p{L} ]*?)(\d[\d\.,]+)/u', $text, $matches, PREG_SET_ORDER)) {
            $tipo = 'Imóvel';
            $valor = 0; $divida = 0; $aluguel = 0;
            foreach ($matches as $m) {
                $label = mb_strtolower(trim($m[1]), 'UTF-8');
                $num = floatval(str_replace(['.', ','], ['', '.'], $m[2]));
                if ($num <= 0) continue;
                if (strpos($label, 'dívida') !== false || strpos($label, 'divida') !== false || strpos($label, 'financi') !== false) {
                    $divida = $num; $ok = true; continue;
                }
                if (strpos($label, 'alug') !== false || strpos($label, 'renda') !== false) {
                    $aluguel = $num; $ok = true; continue;
                }
                if ($valor == 0) { $valor = $num; $ok = true; }
            }
            if ($valor > 0) {
                $this->wm->addRealEstate($userId, $tipo, $valor, $aluguel, $divida);
            }
        }
        return $ok;
    }

    protected function parsePassivos($userId, $text)
    {
        // tipo X, saldo Y, taxa Z%, prazo N meses
        $ok = false;
        $saldo = 0; $taxa = 0; $prazo = 0; $tipo = 'Empréstimo';
        if (preg_match('/(\d[\d\.,]+)/u', $text, $m)) {
            $saldo = floatval(str_replace(['.', ','], ['', '.'], $m[1]));
        }
        if (preg_match('/(\d+[\.,]?\d*)\s*%/u', $text, $m)) {
            $taxa = floatval(str_replace([','], ['.'], $m[1]));
        }
        if (preg_match('/(\d+)\s*mes/u', mb_strtolower($text, 'UTF-8'), $m)) {
            $prazo = intval($m[1]);
        }
        if ($saldo > 0) {
            $this->wm->addLiability($userId, $tipo, $saldo, $taxa, $prazo);
            $ok = true;
        }
        return $ok;
    }

    protected function parseMetas($userId, $text)
    {
        // nome valor prazo meses prioridade
        $ok = false;
        // split by commas
        $parts = preg_split('/[,\n]+/', $text);
        foreach ($parts as $p) {
            if (preg_match('/([\p{L} ]+)\s+(\d[\d\.,]+).*?(\d{1,4})\s*mes/u', trim($p), $m)) {
                $nome = trim($m[1]);
                $valor = floatval(str_replace(['.', ','], ['', '.'], $m[2]));
                $prazo = intval($m[3]);
                $prioridade = (mb_stripos($p, 'alta') !== false) ? 'alta' : ((mb_stripos($p, 'média') !== false || mb_stripos($p, 'media') !== false) ? 'media' : (mb_stripos($p, 'baixa') !== false ? 'baixa' : null));
                $this->wm->addGoal($userId, $nome, $valor, $prazo, $prioridade);
                $ok = true;
            }
        }
        return $ok;
    }

    protected function parseRiscoHorizonte($userId, $text)
    {
        $perfil = null; $horizonte = null;
        foreach (['conservador', 'moderado', 'arrojado'] as $p) {
            if (strpos($text, $p) !== false) { $perfil = $p; break; }
        }
        foreach (['curto', 'médio', 'medio', 'longo'] as $h) {
            if (strpos($text, $h) !== false) { $horizonte = $h; break; }
        }
        if ($perfil || $horizonte) {
            $data = [];
            if ($perfil) $data['perfil_risco'] = $perfil;
            if ($horizonte) $data['horizonte'] = $horizonte;
            $this->wm->upsertProfile($userId, $data);
            return true;
        }
        return false;
    }
}
