<?php

namespace App\Controllers;

use App\Models\WealthModel;
use App\Libraries\WealthAgent;
use App\Models\SimLeadModel;

class WealthManagerController extends BaseController
{
    protected $wm;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->wm = new WealthModel();
    }

    // Public: Landing /wealth
    public function index()
    {
        $data = $this->getWealthMarketingShell([
            'title' => 'Wealth advisory para estruturar patrimônio, renda e legado',
            'description' => 'Diagnóstico patrimonial, alocação, liquidez, proteção e plano consultivo para famílias, executivos e empresários.',
            'keywords' => trim($this->settings->keywords . ', wealth advisory, planejamento patrimonial, consultoria financeira, gestão de patrimônio', ' ,'),
        ]);

        // CMS content from settings
        $landingJson = $this->wm->getSetting('wm_landing_content', null);
        $data['landing'] = $landingJson ? @json_decode($landingJson, true) : null;

        // Copy/labels JSON (opcional)
        $copyJson = $this->wm->getSetting('wm_copy_json', '{}');
        $copyArr = @json_decode($copyJson, true);
        $data['copy'] = is_array($copyArr) ? $copyArr : [];
        $data['memberProgress'] = null;

        if ($data['isAuthenticated']) {
            $progress = $this->computeProgress(user()->id);
            if (!empty($progress['total']) && !empty($progress['score'])) {
                $progress['pct'] = (int)round(($progress['score'] / $progress['total']) * 100);
                $data['memberProgress'] = $progress;
            }
        }

        // Build FAQPage + Service schema for wealth landing
        $landingArr = is_array($data['landing'] ?? null) ? $data['landing'] : [];
        $faqItems = !empty($landingArr['faq']) && is_array($landingArr['faq']) ? $landingArr['faq'] : [
            ['q' => 'Para quem a consultoria é indicada?', 'a' => 'Para famílias, executivos e empresários que querem organizar patrimônio, renda, liquidez e decisões financeiras com visão integrada.'],
            ['q' => 'Preciso transferir a carteira para ter um diagnóstico?', 'a' => 'Não. O diagnóstico inicial parte do seu contexto atual e identifica onde estão travas, desalinhamentos e prioridades.'],
            ['q' => 'O que recebo após o primeiro contato?', 'a' => 'Uma leitura consultiva do caso, hipóteses de ganho de eficiência e indicação objetiva dos próximos movimentos possíveis.'],
            ['q' => 'A análise inclui fluxo de caixa, patrimônio e metas?', 'a' => 'Sim. O foco é conectar patrimônio, liquidez, objetivos e ritmo de construção para evitar decisões isoladas.'],
        ];

        $faqEntities = [];
        foreach ($faqItems as $i => $faq) {
            $q = trim((string)($faq['q'] ?? ''));
            $a = trim((string)($faq['a'] ?? ''));
            if ($q !== '' && $a !== '') {
                $faqEntities[] = [
                    '@type' => 'Question',
                    'name' => $q,
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => $a,
                    ],
                    'position' => $i + 1,
                ];
            }
        }

        $wealthCanonical = base_url('wealth');
        $data['marketingSchema'] = [
            '@context' => 'https://schema.org',
            '@graph' => [
                [
                    '@type' => 'WebPage',
                    '@id' => $wealthCanonical . '#webpage',
                    'url' => $wealthCanonical,
                    'name' => $data['title'],
                    'description' => $data['description'],
                    'inLanguage' => $this->activeLang->language_code ?? 'pt',
                    'isPartOf' => ['@id' => base_url() . '/#website'],
                    'about' => ['@id' => $wealthCanonical . '#service'],
                ],
                [
                    '@type' => 'FinancialService',
                    '@id' => $wealthCanonical . '#service',
                    'name' => 'Wealth Advisory GX Capital',
                    'description' => 'Diagnóstico patrimonial, alocação, liquidez, proteção e plano consultivo para famílias, executivos e empresários.',
                    'provider' => ['@id' => base_url() . '/#organization'],
                    'areaServed' => 'BR',
                    'serviceType' => [
                        'Diagnóstico patrimonial',
                        'Consultoria de alocação',
                        'Planejamento de liquidez e renda',
                        'Proteção patrimonial e legado',
                    ],
                ],
                [
                    '@type' => 'FAQPage',
                    '@id' => $wealthCanonical . '#faq',
                    'mainEntity' => $faqEntities,
                ],
            ],
        ];

        $this->wm->incrementCounter('wm_analytics_view_landing', 1);
        if (function_exists('metaConversions') && metaConversions()->isEventEnabled('PageView')) {
            $pageUrl = current_url();
            deferAfterResponse(function () use ($pageUrl) {
                @trackMetaPageView($pageUrl);
            });
        }

        echo loadView('partials/_header', $data);
        echo loadView('wealth/landing', $data);
        echo loadView('partials/_footer', $data);
    }

    public function trackEvent()
    {
        $name = trim((string)inputPost('name'));
        $map = [
            'start_signup' => 'wm_analytics_start_signup',
            'wealth_diagnostic_interaction' => 'wm_analytics_diagnostic_interaction',
            'wealth_lead_submit' => 'wm_analytics_lead_submit',
            'wealth_continue_area' => 'wm_analytics_continue_area',
        ];

        if (isset($map[$name])) {
            $this->wm->incrementCounter($map[$name], 1);
            return $this->response->setJSON(['ok'=>true]);
        }
        return $this->response->setJSON(['ok'=>false]);
    }

    public function leadCapture()
    {
        $lead = $this->collectLeadRequest();
        if (!empty($lead['errors'])) {
            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' => implode(' ', $lead['errors']),
                ]);
        }

        try {
            $this->persistLeadSubmission($lead);
            $this->wm->incrementCounter('wm_analytics_lead_submit', 1);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Recebemos seu diagnóstico inicial. Nosso time vai analisar e retornar com os próximos passos.',
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Wealth lead capture failed: {message}', ['message' => $e->getMessage()]);

            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' => 'Não foi possível enviar seus dados agora. Tente novamente em alguns instantes.',
                ]);
        }
    }

    // Public: Conversa /wealth/conversa
    public function conversa()
    {
        if (!authCheck()) {
            setErrorMessage('message_post_auth');
            return redirect()->to(generateURL('register'));
        }
        $userId = user()->id;
        // Ensure initial token
        $tokens = $this->wm->ensureInitialToken($userId);

        // Check if user has tokens or active session
        $active = $this->wm->getActiveSessionByUser($userId);
        if (!$active && $tokens <= 0) {
            $data = setPageMeta('Wealth Manager - Conversa');
            $data['userSession'] = getUserSession();
            $data['noTokens'] = true;
            $data['tokens'] = 0;
            echo loadView('partials/_header', $data);
            echo loadView('wealth/chat', $data);
            echo loadView('partials/_footer', $data);
            return;
        }

        // Start new session if none
        if (!$active) {
            $sessionId = $this->wm->startSession($userId);
            $active = $this->wm->getSession($sessionId);
            $this->wm->addMessage($active->id, 'agent', (new WealthAgent())->firstQuestion());
            $this->wm->incrementCounter('wm_analytics_start_session', 1);
        }

        $data = setPageMeta('Wealth Manager - Conversa');
        $data['userSession'] = getUserSession();
        $data['session'] = $active;
        $data['messages'] = $this->wm->getMessages($active->id);
        $data['noTokens'] = false;
        $data['progress'] = $this->computeProgress($userId);
        $data['next_step'] = $this->determineNextStep($userId);
        $data['steps'] = $this->buildStepList($userId, $data['next_step']);

        // Prefill data for forms to improve UX
        $db = $this->wm->db;
        $profile = $db->table('wm_user_profile')->where('user_id', $userId)->get()->getRow();
        $data['profile'] = $profile;

        // Income and expenses
        $rowsInc = $db->table('wm_income_expense')->where('user_id', $userId)->where('tipo', 'renda')->get()->getResult();
        $rowsExp = $db->table('wm_income_expense')->where('user_id', $userId)->where('tipo', 'despesa')->get()->getResult();
        $existingIncomes = [];
        foreach ($rowsInc as $r) { $existingIncomes[$r->categoria] = (float)$r->valor_mensal; }
        $existingExpenses = [];
        foreach ($rowsExp as $r) { $existingExpenses[] = ['categoria' => $r->categoria, 'valor' => (float)$r->valor_mensal]; }
        $data['existing_incomes'] = $existingIncomes;
        $data['existing_expenses'] = $existingExpenses;

        // Financial assets allocation prefill
        $rowsFin = $db->table('wm_assets_financial')->select('classe, SUM(valor_atual) AS total')->where('user_id', $userId)->groupBy('classe')->get()->getResultArray();
        $allocTotal = 0.0; $allocByClass = [];
        foreach ($rowsFin as $row) { $v = (float)$row['total']; $allocByClass[$row['classe']] = $v; $allocTotal += $v; }
        $map = ['caixa','CDB','fundos','ações','previdência','ETFs','internacional'];
        $existingAllocPct = [];
        foreach ($map as $k) {
            $val = $allocByClass[$k] ?? 0.0;
            $existingAllocPct[$k] = ($allocTotal > 0) ? (100.0 * $val / $allocTotal) : 0.0;
        }
        $data['alloc_total_financeiro'] = $allocTotal;
        $data['existing_alloc_pct'] = $existingAllocPct;

        // Real estate
        $data['existing_realestate'] = $db->table('wm_assets_realestate')->where('user_id', $userId)->orderBy('id', 'ASC')->get()->getResult();
        // Liabilities
        $data['existing_liabilities'] = $db->table('wm_liabilities')->where('user_id', $userId)->orderBy('id', 'ASC')->get()->getResult();
        // Goals
        $data['existing_goals'] = $db->table('wm_goals')->where('user_id', $userId)->orderBy('id', 'ASC')->get()->getResult();

        // Dependents in observacoes
        $obs = $this->getProfileObservacoes($profile);
        $data['existing_dependentes'] = $obs['dependentes'] ?? [];

        echo loadView('partials/_header', $data);
        echo loadView('wealth/chat', $data);
        echo loadView('partials/_footer', $data);
    }

    // AJAX: send message in conversation
    public function sendMessage()
    {
        if (!authCheck()) {
            return $this->response->setJSON(['success' => false, 'error' => 'auth']);
        }
        $userId = user()->id;
        $sessionId = (int)inputPost('session_id');
        $msg = trim((string)inputPost('message'));
        $session = $this->wm->getSession($sessionId);
        if (empty($session) || $session->user_id != $userId || $session->status != 'ativa') {
            return $this->response->setJSON(['success' => false, 'error' => 'invalid_session']);
        }
        if ($msg === '') {
            return $this->response->setJSON(['success' => false, 'error' => 'empty']);
        }
        $this->wm->addMessage($sessionId, 'user', $msg);

        $agent = new WealthAgent();
        $reply = $agent->handleUserMessage($userId, $msg);
        $this->wm->addMessage($sessionId, 'agent', $reply);

        // End session when agent says finalizada
        if (stripos($reply, 'Sessão finalizada') !== false) {
            $this->wm->endSession($sessionId, true);
            $this->wm->incrementCounter('wm_analytics_end_session', 1);
        }
        return $this->response->setJSON(['success' => true, 'reply' => $reply]);
    }

    // Public: Resultado /wealth/resultado
    public function resultado()
    {
        if (!authCheck()) {
            setErrorMessage('message_post_auth');
            return redirect()->to(generateURL('register'));
        }
        $userId = user()->id;
        $data = setPageMeta('Wealth Manager - Resultado');
        $data['userSession'] = getUserSession();
        $data['profile'] = $this->wm->db->table('wm_user_profile')->where('user_id', $userId)->get()->getRow();
        $data['agg'] = $this->wm->getAggregates($userId);
        $data['goals'] = $this->wm->db->table('wm_goals')->where('user_id', $userId)->orderBy('id', 'DESC')->get()->getResult();

        // Copy/labels JSON (opcional)
        $copyJson = $this->wm->getSetting('wm_copy_json', '{}');
        $copyArr = @json_decode($copyJson, true);
        $data['copy'] = is_array($copyArr) ? $copyArr : [];

        // Settings thresholds
        $limit = floatval($this->wm->getSetting('wm_limit_senior', '1000000'));
        $data['limit_senior'] = $limit;
        $data['show_cta_senior'] = ($data['agg']['net_worth'] >= $limit);
        $data['inflacao'] = $this->wm->getSetting('wm_inflacao', '4.0');
        $data['returns_by_class'] = $this->wm->getSetting('wm_returns_by_class', null);
        $data['expected_return'] = $this->computeExpectedReturn($data['agg'], $data['inflacao'], $data['returns_by_class']);

        // FI computation and projection
        $fi = $this->computeFinancialIndependence($data['agg'], $data['expected_return']);
        $data['fi'] = $fi;

        $this->wm->incrementCounter('wm_analytics_view_results', 1);

        echo loadView('partials/_header', $data);
        echo loadView('wealth/results', $data);
        echo loadView('partials/_footer', $data);
    }

    private function computeProgress($userId)
    {
        $db = $this->wm->db;
        $score = 0; $total = 10;
        $profile = $db->table('wm_user_profile')->where('user_id', $userId)->get()->getRow();
        if ($profile && $profile->consent_accepted_at) $score++;
        if ($profile && $profile->estado_civil) $score++;
        if ($profile && $profile->ano_nascimento) $score++;
        if ($db->table('wm_income_expense')->where('user_id',$userId)->where('tipo','renda')->countAllResults() > 0) $score++;
        if ($db->table('wm_income_expense')->where('user_id',$userId)->where('tipo','despesa')->countAllResults() > 0) $score++;
        if ($db->table('wm_assets_financial')->where('user_id',$userId)->countAllResults() > 0) $score++;
        if ($db->table('wm_assets_realestate')->where('user_id',$userId)->countAllResults() > 0) $score++;
        if ($db->table('wm_liabilities')->where('user_id',$userId)->countAllResults() > 0) $score++;
        if ($db->table('wm_goals')->where('user_id',$userId)->countAllResults() > 0) $score++;
        if ($profile && $profile->perfil_risco) $score++;
        return ['score'=>$score,'total'=>$total];
    }

    private function computeExpectedReturn($agg, $inflacao, $returnsJson)
    {
        $infl = floatval($inflacao ?? '4.0');
        $alloc = $agg['allocation'] ?? [];
        if (empty($alloc)) return 0.02; // 2% real anual default
        $total = array_sum($alloc);
        if ($total <= 0) return 0.02;
        $returns = [];
        if (!empty($returnsJson)) {
            $parsed = json_decode($returnsJson, true);
            if (is_array($parsed)) $returns = $parsed;
        }
        $weightedNominal = 0.0;
        foreach ($alloc as $classe => $valor) {
            $w = $valor / $total;
            $nom = isset($returns[$classe]) ? floatval($returns[$classe]) : 4.0; // fallback 4%
            $weightedNominal += $w * $nom;
        }
        // real return annual
        $real = (1+$weightedNominal/100)/(1+$infl/100)-1;
        return $real; // annual real
    }

    private function computeFinancialIndependence(array $agg, float $realAnnual)
    {
        $nw = floatval($agg['net_worth'] ?? 0);
        $income = floatval($agg['income'] ?? 0);
        $expense = floatval($agg['expense'] ?? 0);
        $savings = max(0.0, floatval($agg['savings'] ?? 0));
        $realMonthly = pow(1.0 + $realAnnual, 1.0/12.0) - 1.0;

        $nwNeeded = null; $monthsToFI = null;
        if ($realAnnual > 0) {
            $nwNeeded = ($expense * 12.0) / $realAnnual; // patrimônio necessário em termos reais
            // Resolver tempo para atingir nwNeeded com aportes mensais e retorno i
            $PV = $nw; $P = $savings; $i = $realMonthly;
            if ($PV >= $nwNeeded) {
                $monthsToFI = 0;
            } else if ($i > 0 && ($PV + ($P > 0 ? $P/$i : 0)) > 0) {
                $x = ($nwNeeded + ($P > 0 ? $P/$i : 0)) / ($PV + ($P > 0 ? $P/$i : 0));
                if ($x < 1) { $monthsToFI = 0; }
                else { $monthsToFI = (int)ceil(log($x) / log(1+$i)); }
            } else if ($i == 0 && $savings > 0) {
                $monthsToFI = (int)ceil(max(0.0, $nwNeeded - $PV) / $savings);
            } else {
                $monthsToFI = null; // inalcançável nas condições atuais
            }
        }

        // Projeção anual por até 30 anos
        $years = [];
        $proj = [];
        $threshold = [];
        $cur = $nw;
        for ($y=0; $y<=30; $y++) {
            $years[] = $y;
            $proj[] = round($cur, 2);
            $threshold[] = $nwNeeded ?? 0;
            // evolve um ano em meses
            for ($m=0; $m<12; $m++) {
                $cur = $cur * (1+$realMonthly) + $savings;
            }
        }

        return [
            'real_annual' => $realAnnual,
            'real_monthly' => $realMonthly,
            'nw_needed' => $nwNeeded,
            'months_to_fi' => $monthsToFI,
            'years' => $years,
            'proj' => $proj,
            'threshold' => $threshold,
            'expense_monthly' => $expense,
            'savings_monthly' => $savings,
        ];
    }

    private function determineNextStep($userId)
    {
        $db = $this->wm->db;
        $profile = $db->table('wm_user_profile')->where('user_id',$userId)->get()->getRow();
        if (empty($profile) || empty($profile->consent_accepted_at)) return 'consent';
        if (empty($profile->estado_civil)) return 'estado_civil';
        if (empty($profile->ano_nascimento)) return 'ano_nascimento';
        if ($db->table('wm_income_expense')->where('user_id',$userId)->where('tipo','renda')->countAllResults() == 0) return 'income';
        if ($db->table('wm_income_expense')->where('user_id',$userId)->where('tipo','despesa')->countAllResults() == 0) return 'expenses';
        if ($db->table('wm_assets_financial')->where('user_id',$userId)->countAllResults() == 0) return 'assets_financial';
        if ($db->table('wm_assets_realestate')->where('user_id',$userId)->countAllResults() == 0) return 'realestate';
        // dependentes from observacoes JSON
        $obs = $this->getProfileObservacoes($profile);
        if (empty($obs['dependentes']) || count($obs['dependentes']) == 0) return 'dependentes';
        if ($db->table('wm_liabilities')->where('user_id',$userId)->countAllResults() == 0) return 'liabilities';
        if ($db->table('wm_goals')->where('user_id',$userId)->countAllResults() == 0) return 'goals';
        if (empty($profile->perfil_risco)) return 'risk';
        return 'final';
    }

    private function getProfileObservacoes($profile)
    {
        $obs = [];
        if (!empty($profile) && !empty($profile->observacoes)) {
            $decoded = json_decode($profile->observacoes, true);
            if (is_array($decoded)) $obs = $decoded;
        }
        return $obs;
    }

    private function buildStepList($userId, $current)
    {
        $db = $this->wm->db;
        $profile = $db->table('wm_user_profile')->where('user_id',$userId)->get()->getRow();
        $obs = $this->getProfileObservacoes($profile);
        $checks = [
            'consent' => !empty($profile) && !empty($profile->consent_accepted_at),
            'estado_civil' => !empty($profile) && !empty($profile->estado_civil),
            'ano_nascimento' => !empty($profile) && !empty($profile->ano_nascimento),
            'income' => $db->table('wm_income_expense')->where('user_id',$userId)->where('tipo','renda')->countAllResults() > 0,
            'expenses' => $db->table('wm_income_expense')->where('user_id',$userId)->where('tipo','despesa')->countAllResults() > 0,
            'assets_financial' => $db->table('wm_assets_financial')->where('user_id',$userId)->countAllResults() > 0,
            'realestate' => $db->table('wm_assets_realestate')->where('user_id',$userId)->countAllResults() > 0,
            'dependentes' => !empty($obs['dependentes']) && is_array($obs['dependentes']) && count($obs['dependentes'])>0,
            'liabilities' => $db->table('wm_liabilities')->where('user_id',$userId)->countAllResults() > 0,
            'goals' => $db->table('wm_goals')->where('user_id',$userId)->countAllResults() > 0,
            'risk' => !empty($profile) && !empty($profile->perfil_risco),
        ];
        $labels = [
            'consent' => 'Consentimento',
            'estado_civil' => 'Estado civil',
            'ano_nascimento' => 'Ano de nascimento',
            'income' => 'Rendas',
            'expenses' => 'Despesas',
            'assets_financial' => 'Alocação financeira',
            'realestate' => 'Imóveis',
            'dependentes' => 'Dependentes',
            'liabilities' => 'Passivos',
            'goals' => 'Metas',
            'risk' => 'Perfil de risco',
        ];
        $order = ['consent','estado_civil','ano_nascimento','income','expenses','assets_financial','realestate','dependentes','liabilities','goals','risk'];
        $out = [];
        foreach ($order as $k) {
            $out[] = [
                'key' => $k,
                'label' => $labels[$k],
                'status' => $checks[$k] ? 'done' : ($current === $k ? 'current' : 'pending')
            ];
        }
        return $out;
    }

    private function saveProfileObservacoes($userId, array $data)
    {
        $profile = $this->wm->db->table('wm_user_profile')->where('user_id',$userId)->get()->getRow();
        $obs = $this->getProfileObservacoes($profile);
        $merged = array_merge($obs, $data);
        $this->wm->upsertProfile($userId, ['observacoes' => json_encode($merged, JSON_UNESCAPED_UNICODE)]);
    }

    // Structured endpoints for hybrid UX
    public function acceptConsent()
    {
        if (!authCheck()) return $this->response->setJSON(['success'=>false]);
        $this->wm->upsertProfile(user()->id, ['consent_accepted_at' => date('Y-m-d H:i:s')]);
        $active = $this->wm->getActiveSessionByUser(user()->id);
        if ($active) { $this->wm->addMessage($active->id, 'agent', 'Consentimento registrado.'); }
        return $this->response->setJSON(['success'=>true]);
    }

    public function saveProfileBasic()
    {
        if (!authCheck()) return $this->response->setJSON(['success'=>false]);
        $estado = inputPost('estado_civil');
        $ano = (int)inputPost('ano_nascimento');
        $data = [];
        if (!empty($estado)) $data['estado_civil'] = $estado;
        if ($ano > 1900 && $ano <= (int)date('Y')) $data['ano_nascimento'] = $ano;
        $perfil = inputPost('perfil_risco'); if (!empty($perfil)) $data['perfil_risco'] = $perfil;
        $horiz = inputPost('horizonte'); if (!empty($horiz)) $data['horizonte'] = $horiz;
        if (!empty($data)) $this->wm->upsertProfile(user()->id, $data);
        $active = $this->wm->getActiveSessionByUser(user()->id);
        if ($active) { $this->wm->addMessage($active->id, 'agent', 'Perfil atualizado.'); }
        return $this->response->setJSON(['success'=>true]);
    }

    public function saveIncomeForm()
    {
        if (!authCheck()) return $this->response->setJSON(['success'=>false]);
        $uid = user()->id;
        $db = $this->wm->db;
        $db->transBegin();
        try {
            $db->table('wm_income_expense')->where('user_id',$uid)->where('tipo','renda')->delete();
            $items = inputPost('income_items'); // array of keys
            if (is_array($items)) {
                foreach ($items as $key) {
                    $val = floatval(inputPost('income_val_'.$key) ?? 0);
                    if ($val > 0) $this->wm->addIncomeExpense($uid, 'renda', $key, $val);
                }
            }
            $db->transCommit();
        } catch (\Throwable $e) {
            $db->transRollback();
            return $this->response->setJSON(['success'=>false, 'error'=>'db']);
        }
        $active = $this->wm->getActiveSessionByUser($uid);
        if ($active) { $this->wm->addMessage($active->id, 'agent', 'Rendas registradas.'); }
        return $this->response->setJSON(['success'=>true]);
    }

    public function saveExpenseForm()
    {
        if (!authCheck()) return $this->response->setJSON(['success'=>false]);
        $uid = user()->id;
        $db = $this->wm->db;
        $db->transBegin();
        try {
            $db->table('wm_income_expense')->where('user_id',$uid)->where('tipo','despesa')->delete();
            $cats = inputPost('expense_cat');
            $vals = inputPost('expense_val');
            if (is_array($cats) && is_array($vals)) {
                foreach ($cats as $i => $cat) {
                    $cat = trim((string)$cat);
                    $val = floatval($vals[$i] ?? 0);
                    if ($cat !== '' && $val > 0) $this->wm->addIncomeExpense($uid, 'despesa', $cat, $val);
                }
            }
            $db->transCommit();
        } catch (\Throwable $e) {
            $db->transRollback();
            return $this->response->setJSON(['success'=>false, 'error'=>'db']);
        }
        $active = $this->wm->getActiveSessionByUser($uid);
        if ($active) { $this->wm->addMessage($active->id, 'agent', 'Despesas registradas.'); }
        return $this->response->setJSON(['success'=>true]);
    }

    public function saveDependentsForm()
    {
        if (!authCheck()) return $this->response->setJSON(['success'=>false]);
        $uid = user()->id;
        $has = inputPost('has_children');
        $num = (int)inputPost('num_children');
        $ages = inputPost('child_age');
        $deps = [];
        if ($has == '1' && $num > 0 && is_array($ages)) {
            for ($i=0;$i<$num;$i++) {
                $age = intval($ages[$i] ?? 0);
                if ($age > 0) $deps[] = ['idade' => $age];
            }
        }
        $this->saveProfileObservacoes($uid, ['dependentes' => $deps]);
        $active = $this->wm->getActiveSessionByUser($uid);
        if ($active) { $this->wm->addMessage($active->id, 'agent', 'Dependentes atualizados.'); }
        return $this->response->setJSON(['success'=>true]);
    }

    public function saveAllocationForm()
    {
        if (!authCheck()) return $this->response->setJSON(['success'=>false]);
        $uid = user()->id;
        $total = floatval(inputPost('total_financeiro') ?? 0);
        $map = ['caixa','CDB','fundos','ações','previdência','ETFs','internacional'];
        $db = $this->wm->db;
        $db->transBegin();
        try {
            $db->table('wm_assets_financial')->where('user_id',$uid)->delete();
            $sumPct = 0.0; $values = [];
            foreach ($map as $k) {
                $pct = floatval(inputPost('alloc_'.$k) ?? 0);
                if ($pct < 0) $pct = 0; $sumPct += $pct; $values[$k] = $pct;
            }
            if ($total > 0 && $sumPct > 0) {
                foreach ($values as $k=>$pct) {
                    if ($pct <= 0) continue;
                    $amt = ($pct/100.0)*$total;
                    $this->wm->addFinancialAsset($uid, $k, null, $amt);
                }
            }
            $db->transCommit();
        } catch (\Throwable $e) {
            $db->transRollback();
            return $this->response->setJSON(['success'=>false, 'error'=>'db']);
        }
        $active = $this->wm->getActiveSessionByUser($uid);
        if ($active) { $this->wm->addMessage($active->id, 'agent', 'Alocação financeira registrada.'); }
        return $this->response->setJSON(['success'=>true]);
    }

    public function saveRealEstateForm()
    {
        if (!authCheck()) return $this->response->setJSON(['success'=>false]);
        $uid = user()->id;
        $db = $this->wm->db;
        $db->transBegin();
        try {
            $db->table('wm_assets_realestate')->where('user_id',$uid)->delete();
            $tipos = inputPost('re_tipo');
            $valores = inputPost('re_valor');
            $aluguels = inputPost('re_aluguel');
            $dividas = inputPost('re_divida');
            $n = is_array($tipos) ? count($tipos) : 0;
            for ($i=0; $i<$n; $i++) {
                $tipo = trim((string)($tipos[$i] ?? ''));
                $valor = floatval($valores[$i] ?? 0);
                $alug = floatval($aluguels[$i] ?? 0);
                $div = floatval($dividas[$i] ?? 0);
                if ($tipo !== '' && $valor > 0) {
                    $this->wm->addRealEstate($uid, $tipo, $valor, $alug, $div);
                }
            }
            $db->transCommit();
        } catch (\Throwable $e) {
            $db->transRollback();
            return $this->response->setJSON(['success'=>false, 'error'=>'db']);
        }
        $active = $this->wm->getActiveSessionByUser($uid);
        if ($active) { $this->wm->addMessage($active->id, 'agent', 'Imóveis registrados.'); }
        return $this->response->setJSON(['success'=>true]);
    }

    public function saveLiabilitiesForm()
    {
        if (!authCheck()) return $this->response->setJSON(['success'=>false]);
        $uid = user()->id;
        $db = $this->wm->db;
        $db->transBegin();
        try {
            $db->table('wm_liabilities')->where('user_id',$uid)->delete();
            $tipos = inputPost('liab_tipo');
            $saldos = inputPost('liab_saldo');
            $taxas = inputPost('liab_taxa');
            $prazos = inputPost('liab_prazo');
            $n = is_array($tipos) ? count($tipos) : 0;
            for ($i=0; $i<$n; $i++) {
                $tipo = trim((string)($tipos[$i] ?? ''));
                $saldo = floatval($saldos[$i] ?? 0);
                $taxa = floatval($taxas[$i] ?? 0);
                $prazo = intval($prazos[$i] ?? 0);
                if ($tipo !== '' && $saldo > 0) {
                    $this->wm->addLiability($uid, $tipo, $saldo, $taxa, $prazo);
                }
            }
            $db->transCommit();
        } catch (\Throwable $e) {
            $db->transRollback();
            return $this->response->setJSON(['success'=>false, 'error'=>'db']);
        }
        $active = $this->wm->getActiveSessionByUser($uid);
        if ($active) { $this->wm->addMessage($active->id, 'agent', 'Passivos registrados.'); }
        return $this->response->setJSON(['success'=>true]);
    }

    public function saveGoalsForm()
    {
        if (!authCheck()) return $this->response->setJSON(['success'=>false]);
        $uid = user()->id;
        $db = $this->wm->db;
        $db->transBegin();
        try {
            $db->table('wm_goals')->where('user_id',$uid)->delete();
            $nomes = inputPost('goal_nome');
            $valores = inputPost('goal_valor');
            $prazos = inputPost('goal_prazo');
            $prios = inputPost('goal_prio');
            $n = is_array($nomes) ? count($nomes) : 0;
            for ($i=0; $i<$n; $i++) {
                $nome = trim((string)($nomes[$i] ?? ''));
                $valor = floatval($valores[$i] ?? 0);
                $prazo = intval($prazos[$i] ?? 0);
                $prio = (string)($prios[$i] ?? null);
                if ($nome !== '' && $valor > 0) {
                    $this->wm->addGoal($uid, $nome, $valor, $prazo, $prio);
                }
            }
            $db->transCommit();
        } catch (\Throwable $e) {
            $db->transRollback();
            return $this->response->setJSON(['success'=>false, 'error'=>'db']);
        }
        $active = $this->wm->getActiveSessionByUser($uid);
        if ($active) { $this->wm->addMessage($active->id, 'agent', 'Metas registradas.'); }
        return $this->response->setJSON(['success'=>true]);
    }

    // Public: Agendar /wealth/agendar
    public function agendar()
    {
        $data = $this->getWealthMarketingShell([
            'title' => 'Agende um diagnóstico patrimonial consultivo',
            'description' => 'Envie seu contexto, objetivos e melhor horário. A equipe da GX Capital retorna com um plano inicial de próximos passos.',
            'keywords' => trim($this->settings->keywords . ', wealth advisory, diagnóstico patrimonial, agendar consultoria financeira', ' ,'),
        ]);

        echo loadView('partials/_header', $data);
        echo loadView('wealth/schedule', $data);
        echo loadView('partials/_footer', $data);
    }

    public function agendarPost()
    {
        $lead = $this->collectLeadRequest();
        if (!empty($lead['errors'])) {
            setErrorMessage(implode(' ', $lead['errors']), false);
            return redirect()->back()->withInput();
        }

        try {
            $this->persistLeadSubmission($lead);
            setSuccessMessage('Recebemos sua solicitação. Nosso time vai retornar com os próximos passos.', false);
        } catch (\Throwable $e) {
            log_message('error', 'Wealth appointment post failed: {message}', ['message' => $e->getMessage()]);
            setErrorMessage('Não foi possível enviar sua solicitação agora. Tente novamente em alguns instantes.', false);
            return redirect()->back()->withInput();
        }

        $target = ($lead['source_page'] ?? '') === 'landing'
            ? base_url('wealth') . '#fale-com-especialista'
            : base_url('wealth/agendar') . '#gx-wealth-schedule-form';

        return redirect()->to($target);
    }

    // Public: PDF básico do resumo
    public function resumoPdf()
    {
        if (!authCheck()) {
            return redirect()->to(generateURL('register'));
        }
        // Monta dados
        $userId = user()->id;
        $agg = $this->wm->getAggregates($userId);
        $inflacao = $this->wm->getSetting('wm_inflacao', '4.0');
        $returns_by_class = $this->wm->getSetting('wm_returns_by_class', null);
        $expected = $this->computeExpectedReturn($agg, $inflacao, $returns_by_class);
        $fi = $this->computeFinancialIndependence($agg, $expected);

        $data = [
            'title' => 'Resumo Financeiro',
            'agg' => $agg,
            'expected' => $expected,
            'fi' => $fi,
            'generated_at' => date('d/m/Y H:i'),
            'logo' => base_url('assets/img/logo.png'),
        ];
        $html = view('wealth/pdf_template', $data);

        // Tenta gerar via Dompdf se instalado (opcional)
        if (class_exists('Dompdf\\Dompdf')) {
            try {
                $dompdf = new \Dompdf\Dompdf([ 'isRemoteEnabled' => true, 'isHtml5ParserEnabled' => true ]);
                $dompdf->loadHtml($html, 'UTF-8');
                $dompdf->setPaper('A4', 'portrait');
                $dompdf->render();
                $output = $dompdf->output();
                return $this->response
                    ->setHeader('Content-Type', 'application/pdf')
                    ->setHeader('Content-Disposition', 'attachment; filename="resumo_wealth_manager.pdf"')
                    ->setBody($output);
            } catch (\Throwable $e) { /* fallback abaixo */ }
        }

        // Fallback premium: HTML estilizado para impressão/salvar em PDF
        return $this->response
            ->setHeader('Content-Type', 'application/octet-stream')
            ->setHeader('Content-Disposition', 'attachment; filename="resumo_wealth_manager.html"')
            ->setBody($html);
    }

    private function getWealthMarketingShell(array $overrides = [])
    {
        $title = $overrides['title'] ?? 'Wealth advisory GX Capital';
        $data = setPageMeta($title);
        $defaults = [
            'title' => $title,
            'description' => 'Consultoria patrimonial para organizar patrimônio, renda, liquidez e decisões financeiras com visão integrada.',
            'keywords' => trim($this->settings->keywords . ', wealth advisory, consultoria financeira, planejamento patrimonial', ' ,'),
            'bodyClass' => 'gx-marketing-home gx-wealth-page',
            'pageHeadView' => 'wealth/_shared_styles',
            'userSession' => getUserSession(),
            'isAuthenticated' => authCheck(),
            'blogUrl' => langBaseUrl('blog'),
            'simulatorsHubUrl' => langBaseUrl('simuladores'),
            'wealthConversationUrl' => base_url('wealth/conversa'),
            'wealthResultsUrl' => base_url('wealth/resultado'),
            'wealthScheduleUrl' => base_url('wealth/agendar'),
            'wealthLeadUrl' => base_url('wealth/lead'),
        ];

        return array_merge($data, $defaults, $overrides);
    }

    private function collectLeadRequest()
    {
        $honeypot = trim((string)inputPost('company_website'));
        $name = trim((string)inputPost('name'));
        $email = trim((string)inputPost('email'));
        $phone = trim((string)inputPost('phone'));
        $goal = trim((string)(inputPost('goal') ?: inputPost('diagnosis_objective')));
        $patrimonyRange = trim((string)inputPost('patrimony_range'));
        $preferredSlot = trim((string)inputPost('preferred_slot'));
        $message = trim((string)inputPost('message'));
        $sourcePage = trim((string)inputPost('source_page'));
        $phoneDigits = preg_replace('/\D+/', '', $phone);
        $diagnostic = [
            'invested' => $this->normalizeLeadNumber(inputPost('diagnosis_invested')),
            'monthly_invest' => $this->normalizeLeadNumber(inputPost('diagnosis_monthly_invest')),
            'monthly_cost' => $this->normalizeLeadNumber(inputPost('diagnosis_monthly_cost')),
            'target_capital' => $this->normalizeLeadNumber(inputPost('diagnosis_target_capital')),
            'projection_10y' => $this->normalizeLeadNumber(inputPost('diagnosis_projection_10y')),
            'gap' => $this->normalizeLeadNumber(inputPost('diagnosis_gap')),
            'coverage_pct' => $this->normalizeLeadNumber(inputPost('diagnosis_coverage_pct')),
            'objective' => trim((string)inputPost('diagnosis_objective')),
        ];

        $errors = [];

        if ($honeypot !== '') {
            $errors[] = 'Solicitação inválida.';
        }
        if ($name === '') {
            $errors[] = 'Informe seu nome.';
        }
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Informe um e-mail válido.';
        }
        if ($phone === '' || strlen($phoneDigits) < 10) {
            $errors[] = 'Informe um telefone com DDD.';
        }
        if ($goal === '') {
            $errors[] = 'Selecione seu principal objetivo.';
        }
        if ($patrimonyRange === '') {
            $errors[] = 'Selecione a faixa patrimonial.';
        }

        return [
            'errors' => $errors,
            'user_id' => authCheck() ? user()->id : null,
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'goal' => $goal,
            'patrimony_range' => $patrimonyRange,
            'preferred_slot' => $preferredSlot,
            'message' => $message,
            'source_page' => $sourcePage,
            'landing_page' => trim((string)(inputPost('landing_page') ?: current_url())),
            'utm_source' => trim((string)inputPost('utm_source')),
            'utm_medium' => trim((string)inputPost('utm_medium')),
            'utm_campaign' => trim((string)inputPost('utm_campaign')),
            'utm_term' => trim((string)inputPost('utm_term')),
            'utm_content' => trim((string)inputPost('utm_content')),
            'diagnostic' => $diagnostic,
        ];
    }

    private function persistLeadSubmission(array $lead)
    {
        $saved = $this->wm->addAppointment([
            'user_id' => $lead['user_id'],
            'nome' => $lead['name'],
            'email' => $lead['email'],
            'telefone' => $lead['phone'],
            'preferencia_horario' => $lead['preferred_slot'] ?: $lead['goal'],
            'status' => 'novo',
        ]);

        if (!$saved) {
            throw new \RuntimeException('Unable to persist wealth appointment lead.');
        }

        $this->wm->incrementCounter('wm_analytics_appointment_created', 1);

        try {
            $simLead = new SimLeadModel();
            $simLead->addSimLead([
                'name' => $lead['name'],
                'email' => $lead['email'],
                'phone' => $lead['phone'],
                'sim_data' => json_encode([
                    'source' => 'wealth_landing',
                    'goal' => $lead['goal'],
                    'patrimony_range' => $lead['patrimony_range'],
                    'preferred_slot' => $lead['preferred_slot'],
                    'diagnostic' => $lead['diagnostic'],
                ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'observations' => $this->buildLeadObservationText($lead),
                'origem' => 'Wealth Advisory GX Capital',
                'utm_source' => $lead['utm_source'],
                'utm_medium' => $lead['utm_medium'],
                'utm_campaign' => $lead['utm_campaign'],
                'utm_term' => $lead['utm_term'],
                'utm_content' => $lead['utm_content'],
                'landing_page' => $lead['landing_page'],
                'source_system' => 'site-gx-php-wealth',
                'meta_content_name' => 'Wealth Advisory GX Capital',
                'meta_content_category' => 'Consultoria Financeira',
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Wealth lead mirror failed: {message}', ['message' => $e->getMessage()]);
        }
    }

    private function buildLeadObservationText(array $lead)
    {
        $lines = [
            'Objetivo principal: ' . $lead['goal'],
            'Faixa patrimonial: ' . $lead['patrimony_range'],
        ];

        if (!empty($lead['preferred_slot'])) {
            $lines[] = 'Melhor horário para contato: ' . $lead['preferred_slot'];
        }

        if (!empty($lead['message'])) {
            $lines[] = 'Contexto informado: ' . $lead['message'];
        }

        $diagnostic = $lead['diagnostic'] ?? [];
        $diagParts = [];
        if ($diagnostic['invested'] !== null) {
            $diagParts[] = 'patrimônio informado ' . $this->formatCurrencyForLead($diagnostic['invested']);
        }
        if ($diagnostic['monthly_invest'] !== null) {
            $diagParts[] = 'aporte mensal ' . $this->formatCurrencyForLead($diagnostic['monthly_invest']);
        }
        if ($diagnostic['monthly_cost'] !== null) {
            $diagParts[] = 'custo de vida ' . $this->formatCurrencyForLead($diagnostic['monthly_cost']);
        }
        if ($diagnostic['target_capital'] !== null) {
            $diagParts[] = 'capital alvo ' . $this->formatCurrencyForLead($diagnostic['target_capital']);
        }
        if ($diagnostic['projection_10y'] !== null) {
            $diagParts[] = 'projeção 10 anos ' . $this->formatCurrencyForLead($diagnostic['projection_10y']);
        }
        if ($diagnostic['gap'] !== null) {
            $diagParts[] = 'gap estimado ' . $this->formatCurrencyForLead($diagnostic['gap']);
        }
        if ($diagnostic['coverage_pct'] !== null) {
            $diagParts[] = 'cobertura estimada ' . number_format($diagnostic['coverage_pct'], 1, ',', '.') . '%';
        }

        if (!empty($diagParts)) {
            $lines[] = 'Diagnóstico rápido: ' . implode(' | ', $diagParts);
        }

        return implode("\n", $lines);
    }

    private function normalizeLeadNumber($value)
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string)$value);
        if ($value === '') {
            return null;
        }

        $normalized = str_replace(',', '.', $value);
        if (!is_numeric($normalized)) {
            return null;
        }

        return round((float)$normalized, 2);
    }

    private function formatCurrencyForLead($value)
    {
        return 'R$ ' . number_format((float)$value, 2, ',', '.');
    }

}
