<?php

namespace App\Controllers;

use App\Models\AuthModel;
use App\Models\WealthModel;

class WealthAdminController extends BaseAdminController
{
    protected $wm;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->wm = new WealthModel();
    }

    public function index()
    {
        checkPermission('admin_panel');
        $data['title'] = 'Wealth Manager';
        $data['wmCounters'] = [
            'view_landing' => intval($this->wm->getSetting('wm_analytics_view_landing', 0)),
            'start_session' => intval($this->wm->getSetting('wm_analytics_start_session', 0)),
            'end_session' => intval($this->wm->getSetting('wm_analytics_end_session', 0)),
            'view_results' => intval($this->wm->getSetting('wm_analytics_view_results', 0)),
            'appointment_created' => intval($this->wm->getSetting('wm_analytics_appointment_created', 0)),
        ];
        echo view('admin/includes/_header', $data);
        echo view('admin/wealth/index', $data);
        echo view('admin/includes/_footer');
    }

    public function settings()
    {
        checkPermission('admin_panel');
        $data['title'] = 'Wealth Manager - Configurações';
        $data['model'] = $this->wm->getSetting('wm_model', 'GPT-5');
        $data['inflacao'] = $this->wm->getSetting('wm_inflacao', '4.0');
        $data['crescimento_renda'] = $this->wm->getSetting('wm_crescimento_renda', '2.0');
        $data['limit_senior'] = $this->wm->getSetting('wm_limit_senior', '1000000');
        $data['credit_after_confirm'] = $this->wm->getSetting('wm_credit_after_confirm', '1');
        $data['credit_amount'] = $this->wm->getSetting('wm_credit_amount', '1');
        $data['copy_json'] = $this->wm->getSetting('wm_copy_json', '{}');
        $data['returns_by_class'] = $this->wm->getSetting('wm_returns_by_class', '{"caixa":1.5,"CDB":3.0,"fundos":4.0,"ações":6.0,"previdência":4.0}');
        echo view('admin/includes/_header', $data);
        echo view('admin/wealth/settings', $data);
        echo view('admin/includes/_footer');
    }

    public function settingsPost()
    {
        checkPermission('admin_panel');
        $this->wm->setSetting('wm_model', inputPost('model') ?? 'GPT-5');
        $this->wm->setSetting('wm_inflacao', inputPost('inflacao') ?? '4.0');
        $this->wm->setSetting('wm_crescimento_renda', inputPost('crescimento_renda') ?? '2.0');
        $this->wm->setSetting('wm_limit_senior', inputPost('limit_senior') ?? '1000000');
        $this->wm->setSetting('wm_credit_after_confirm', inputPost('credit_after_confirm') ?? '1');
        $this->wm->setSetting('wm_credit_amount', inputPost('credit_amount') ?? '1');
        $this->wm->setSetting('wm_copy_json', inputPost('copy_json') ?? '{}');
        $returns = inputPost('returns_by_class');
        if (!empty($returns)) { $this->wm->setSetting('wm_returns_by_class', $returns); }
        setSuccessMessage('Configurações atualizadas');
        return redirect()->back();
    }

    public function tokens()
    {
        checkPermission('admin_panel');
        $data['title'] = 'Wealth Manager - Tokens';
        $data['query'] = inputGet('q');
        $data['users'] = [];
        if ($data['query']) {
            $auth = new AuthModel();
            $q = '%' . $data['query'] . '%';
            $data['users'] = $auth->db->table('users')->groupStart()->like('email', $q)->orLike('username', $q)->groupEnd()->limit(50)->get()->getResult();
        }
        echo view('admin/includes/_header', $data);
        echo view('admin/wealth/tokens', $data);
        echo view('admin/includes/_footer');
    }

    public function tokensPost()
    {
        checkPermission('admin_panel');
        $userId = (int)inputPost('user_id');
        $delta = (int)inputPost('delta');
        $note = inputPost('note');
        $this->wm->addTokens($userId, $delta, $note, user()->id);
        setSuccessMessage('Tokens atualizados');
        return redirect()->back();
    }

    public function appointments()
    {
        checkPermission('admin_panel');
        $data['title'] = 'Wealth Manager - Agendamentos';
        $data['appointments'] = $this->wm->getAppointments();
        echo view('admin/includes/_header', $data);
        echo view('admin/wealth/appointments', $data);
        echo view('admin/includes/_footer');
    }

    public function appointmentStatusPost()
    {
        checkPermission('admin_panel');
        $id = (int)inputPost('id');
        $status = inputPost('status');
        $this->wm->updateAppointmentStatus($id, $status);
        if ($status == 'confirmado') {
            $row = $this->wm->db->table('wm_appointments')->where('id', $id)->get()->getRow();
            if ($row && $row->user_id) {
                if (intval($this->wm->getSetting('wm_credit_after_confirm', '1')) == 1) {
                    $amount = intval($this->wm->getSetting('wm_credit_amount', '1'));
                    $this->wm->addTokens($row->user_id, $amount, 'Auto crédito após confirmação', user()->id);
                    $this->wm->incrementCounter('wm_analytics_appointment_confirmed', 1);
                }
            }
        }
        setSuccessMessage('Status atualizado');
        return redirect()->back();
    }

    public function cms()
    {
        checkPermission('admin_panel');
        $data['title'] = 'Wealth Manager - CMS';
        $data['landing'] = $this->wm->getSetting('wm_landing_content', '{"headline":"","subheadline":"","faq":[],"testimonials":[],"cta":"Começar Agora"}');
        echo view('admin/includes/_header', $data);
        echo view('admin/wealth/cms', $data);
        echo view('admin/includes/_footer');
    }

    public function cmsPost()
    {
        checkPermission('admin_panel');
        $this->wm->setSetting('wm_landing_content', inputPost('landing_json') ?? '{}');
        setSuccessMessage('Landing atualizada');
        return redirect()->back();
    }

    public function export()
    {
        checkPermission('admin_panel');
        $data['title'] = 'Wealth Manager - Exportação';
        echo view('admin/includes/_header', $data);
        echo view('admin/wealth/export', $data);
        echo view('admin/includes/_footer');
    }

    public function exportCsv()
    {
        checkPermission('admin_panel');
        $userId = (int)inputPost('user_id');
        $out = [];
        $profile = $this->wm->db->table('wm_user_profile')->where('user_id', $userId)->get()->getRowArray();
        $income = $this->wm->db->table('wm_income_expense')->where('user_id', $userId)->where('tipo', 'renda')->get()->getResultArray();
        $expense = $this->wm->db->table('wm_income_expense')->where('user_id', $userId)->where('tipo', 'despesa')->get()->getResultArray();
        $fin = $this->wm->db->table('wm_assets_financial')->where('user_id', $userId)->get()->getResultArray();
        $re = $this->wm->db->table('wm_assets_realestate')->where('user_id', $userId)->get()->getResultArray();
        $bus = $this->wm->db->table('wm_business_holdings')->where('user_id', $userId)->get()->getResultArray();
        $deb = $this->wm->db->table('wm_liabilities')->where('user_id', $userId)->get()->getResultArray();
        $goals = $this->wm->db->table('wm_goals')->where('user_id', $userId)->get()->getResultArray();
        $out[] = ['Seção', 'Campo', 'Valor'];
        foreach (($profile ?? []) as $k => $v) { $out[] = ['Perfil', $k, is_scalar($v)?$v:json_encode($v)]; }
        foreach ($income as $row) { $out[] = ['Renda', $row['categoria'], $row['valor_mensal']]; }
        foreach ($expense as $row) { $out[] = ['Despesa', $row['categoria'], $row['valor_mensal']]; }
        foreach ($fin as $row) { $out[] = ['Ativo Financeiro', $row['classe'], $row['valor_atual']]; }
        foreach ($re as $row) { $out[] = ['Imóvel', $row['tipo'], $row['valor_estimado']]; }
        foreach ($bus as $row) { $out[] = ['Negócio', $row['nome'], $row['participacao_pct']]; }
        foreach ($deb as $row) { $out[] = ['Passivo', $row['tipo'], $row['saldo_atual']]; }
        foreach ($goals as $row) { $out[] = ['Meta', $row['nome_meta'], $row['valor_objetivo']]; }
        $csv = '';
        foreach ($out as $r) {
            $csv .= implode(',', array_map(function($x){ return '"' . str_replace('"', '""', (string)$x) . '"'; }, $r)) . "\n";
        }
        return $this->response->setHeader('Content-Type', 'text/csv')
            ->setHeader('Content-Disposition', 'attachment; filename="dossie_usuario_'.$userId.'.csv"')
            ->setBody($csv);
    }

    public function viewResult($userId)
    {
        checkPermission('admin_panel');
        $userId = (int)$userId;
        $data['title'] = 'Wealth Manager - Resultado do Usuário';
        $data['profile'] = $this->wm->db->table('wm_user_profile')->where('user_id', $userId)->get()->getRow();
        $data['agg'] = $this->wm->getAggregates($userId);
        $data['goals'] = $this->wm->db->table('wm_goals')->where('user_id', $userId)->orderBy('id', 'DESC')->get()->getResult();
        $data['limit_senior'] = floatval($this->wm->getSetting('wm_limit_senior', '1000000'));
        $data['show_cta_senior'] = ($data['agg']['net_worth'] >= $data['limit_senior']);
        $data['inflacao'] = $this->wm->getSetting('wm_inflacao', '4.0');
        echo view('admin/includes/_header', $data);
        echo view('wealth/results', $data);
        echo view('admin/includes/_footer');
    }

    public function logs()
    {
        checkPermission('admin_panel');
        $data['title'] = 'Wealth Manager - Logs/Auditoria';
        $data['logs'] = $this->wm->db->table('wm_audit_logs')->orderBy('id', 'DESC')->limit(200)->get()->getResult();
        echo view('admin/includes/_header', $data);
        echo view('admin/wealth/logs', $data);
        echo view('admin/includes/_footer');
    }

    public function sessions()
    {
        checkPermission('admin_panel');
        $data['title'] = 'Wealth Manager - Sessões';
        $data['sessions'] = $this->wm->db->table('wm_sessions')->orderBy('id','DESC')->limit(200)->get()->getResult();
        echo view('admin/includes/_header', $data);
        echo view('admin/wealth/sessions', $data);
        echo view('admin/includes/_footer');
    }

    public function session($id)
    {
        checkPermission('admin_panel');
        $id = (int)$id;
        $data['title'] = 'Wealth Manager - Mensagens da Sessão';
        $data['session'] = $this->wm->getSession($id);
        $data['messages'] = $this->wm->getMessages($id);
        echo view('admin/includes/_header', $data);
        echo view('wealth/chat', $data);
        echo view('admin/includes/_footer');
    }

    public function diagnostics()
    {
        checkPermission('admin_panel');
        $db = \Config\Database::connect();
        $data['title'] = 'Wealth Manager - Diagnósticos';
        try { $data['database'] = $db->query('SELECT DATABASE() AS db')->getRow('db'); } catch (\Throwable $e) { $data['database'] = 'Erro: '.$e->getMessage(); }
        try {
            $tables = $db->query('SHOW TABLES')->getResultArray();
            $data['wm_tables'] = [];
            foreach ($tables as $row) { $name = array_values($row)[0] ?? ''; if (strpos($name, 'wm_') === 0) { $data['wm_tables'][] = $name; } }
        } catch (\Throwable $e) { $data['wm_tables'] = ['Erro: '.$e->getMessage()]; }
        try { $data['users_count'] = (int)$db->table('users')->countAllResults(); } catch (\Throwable $e) { $data['users_count'] = -1; }
        try { $data['has_migrations'] = $db->tableExists('migrations') ? 'sim' : 'não'; } catch (\Throwable $e) { $data['has_migrations'] = 'erro'; }
        echo view('admin/includes/_header', $data);
        echo view('admin/wealth/diagnostics', $data);
        echo view('admin/includes/_footer');
    }

    public function runSetup()
    {
        // Allow CLI usage; require admin in web
        if (php_sapi_name() !== 'cli') {
            checkPermission('admin_panel');
        }
        $db = \Config\Database::connect();
        $forge = \Config\Database::forge();
        $created = [];
        $ensure = function(string $table, callable $define) use ($db, $forge, &$created) {
            if (! $db->tableExists($table)) { $define(); $forge->createTable($table, true); $created[] = $table; }
        };
        $ensure('wm_user_profile', function() use($forge){ $forge->addField(['user_id'=>['type'=>'INT','constraint'=>11,'unsigned'=>true],'estado_civil'=>['type'=>'VARCHAR','constraint'=>50,'null'=>true],'ano_nascimento'=>['type'=>'INT','constraint'=>4,'null'=>true],'perfil_risco'=>['type'=>'VARCHAR','constraint'=>30,'null'=>true],'horizonte'=>['type'=>'VARCHAR','constraint'=>50,'null'=>true],'observacoes'=>['type'=>'TEXT','null'=>true],'consent_accepted_at'=>['type'=>'DATETIME','null'=>true],'updated_at'=>['type'=>'DATETIME','null'=>true],'created_at'=>['type'=>'DATETIME','null'=>true],]); $forge->addKey('user_id', true); });
        $ensure('wm_income_expense', function() use($forge){ $forge->addField(['id'=>['type'=>'INT','constraint'=>11,'unsigned'=>true,'auto_increment'=>true],'user_id'=>['type'=>'INT','constraint'=>11,'unsigned'=>true],'tipo'=>['type'=>'VARCHAR','constraint'=>10],'categoria'=>['type'=>'VARCHAR','constraint'=>100,'null'=>true],'valor_mensal'=>['type'=>'DECIMAL','constraint'=>'15,2','default'=>'0.00'],]); $forge->addKey('id', true); $forge->addKey('user_id'); $forge->addKey('tipo'); });
        $ensure('wm_assets_financial', function() use($forge){ $forge->addField(['id'=>['type'=>'INT','constraint'=>11,'unsigned'=>true,'auto_increment'=>true],'user_id'=>['type'=>'INT','constraint'=>11,'unsigned'=>true],'classe'=>['type'=>'VARCHAR','constraint'=>50],'subtipo'=>['type'=>'VARCHAR','constraint'=>100,'null'=>true],'valor_atual'=>['type'=>'DECIMAL','constraint'=>'18,2','default'=>'0.00'],]); $forge->addKey('id', true); $forge->addKey('user_id'); $forge->addKey('classe'); });
        $ensure('wm_assets_realestate', function() use($forge){ $forge->addField(['id'=>['type'=>'INT','constraint'=>11,'unsigned'=>true,'auto_increment'=>true],'user_id'=>['type'=>'INT','constraint'=>11,'unsigned'=>true],'tipo'=>['type'=>'VARCHAR','constraint'=>100,'null'=>true],'valor_estimado'=>['type'=>'DECIMAL','constraint'=>'18,2','default'=>'0.00'],'renda_aluguel'=>['type'=>'DECIMAL','constraint'=>'15,2','default'=>'0.00'],'saldo_divida'=>['type'=>'DECIMAL','constraint'=>'18,2','default'=>'0.00'],]); $forge->addKey('id', true); $forge->addKey('user_id'); });
        $ensure('wm_business_holdings', function() use($forge){ $forge->addField(['id'=>['type'=>'INT','constraint'=>11,'unsigned'=>true,'auto_increment'=>true],'user_id'=>['type'=>'INT','constraint'=>11,'unsigned'=>true],'nome'=>['type'=>'VARCHAR','constraint'=>255,'null'=>true],'participacao_pct'=>['type'=>'DECIMAL','constraint'=>'7,2','default'=>'0.00'],'observacoes'=>['type'=>'TEXT','null'=>true],]); $forge->addKey('id', true); $forge->addKey('user_id'); });
        $ensure('wm_liabilities', function() use($forge){ $forge->addField(['id'=>['type'=>'INT','constraint'=>11,'unsigned'=>true,'auto_increment'=>true],'user_id'=>['type'=>'INT','constraint'=>11,'unsigned'=>true],'tipo'=>['type'=>'VARCHAR','constraint'=>100,'null'=>true],'saldo_atual'=>['type'=>'DECIMAL','constraint'=>'18,2','default'=>'0.00'],'taxa_aprox'=>['type'=>'DECIMAL','constraint'=>'7,3','default'=>'0.000'],'prazo_meses'=>['type'=>'INT','constraint'=>11,'default'=>0],]); $forge->addKey('id', true); $forge->addKey('user_id'); });
        $ensure('wm_goals', function() use($forge){ $forge->addField(['id'=>['type'=>'INT','constraint'=>11,'unsigned'=>true,'auto_increment'=>true],'user_id'=>['type'=>'INT','constraint'=>11,'unsigned'=>true],'nome_meta'=>['type'=>'VARCHAR','constraint'=>255],'valor_objetivo'=>['type'=>'DECIMAL','constraint'=>'18,2','default'=>'0.00'],'prazo_meses'=>['type'=>'INT','constraint'=>11,'default'=>0],'prioridade'=>['type'=>'VARCHAR','constraint'=>20,'null'=>true],'observacoes'=>['type'=>'TEXT','null'=>true],]); $forge->addKey('id', true); $forge->addKey('user_id'); });
        $ensure('wm_sessions', function() use($forge){ $forge->addField(['id'=>['type'=>'INT','constraint'=>11,'unsigned'=>true,'auto_increment'=>true],'user_id'=>['type'=>'INT','constraint'=>11,'unsigned'=>true],'started_at'=>['type'=>'DATETIME','null'=>true],'ended_at'=>['type'=>'DATETIME','null'=>true],'status'=>['type'=>'VARCHAR','constraint'=>20,'default'=>'ativa'],'messages_count'=>['type'=>'INT','constraint'=>11,'default'=>0],]); $forge->addKey('id', true); $forge->addKey('user_id'); $forge->addKey('status'); });
        $ensure('wm_messages', function() use($forge){ $forge->addField(['id'=>['type'=>'INT','constraint'=>11,'unsigned'=>true,'auto_increment'=>true],'session_id'=>['type'=>'INT','constraint'=>11,'unsigned'=>true],'role'=>['type'=>'VARCHAR','constraint'=>10],'content'=>['type'=>'TEXT'],'created_at'=>['type'=>'DATETIME','null'=>true],]); $forge->addKey('id', true); $forge->addKey('session_id'); });
        $ensure('wm_tokens', function() use($forge){ $forge->addField(['user_id'=>['type'=>'INT','constraint'=>11,'unsigned'=>true],'tokens_disponiveis'=>['type'=>'INT','constraint'=>11,'default'=>0],'updated_at'=>['type'=>'DATETIME','null'=>true],]); $forge->addKey('user_id', true); });
        $ensure('wm_settings', function() use($forge){ $forge->addField(['chave'=>['type'=>'VARCHAR','constraint'=>191],'valor'=>['type'=>'TEXT','null'=>true],]); $forge->addKey('chave', true); });
        $ensure('wm_appointments', function() use($forge){ $forge->addField(['id'=>['type'=>'INT','constraint'=>11,'unsigned'=>true,'auto_increment'=>true],'user_id'=>['type'=>'INT','constraint'=>11,'unsigned'=>true,'null'=>true],'nome'=>['type'=>'VARCHAR','constraint'=>255,'null'=>true],'email'=>['type'=>'VARCHAR','constraint'=>255,'null'=>true],'telefone'=>['type'=>'VARCHAR','constraint'=>50,'null'=>true],'preferencia_horario'=>['type'=>'VARCHAR','constraint'=>255,'null'=>true],'status'=>['type'=>'VARCHAR','constraint'=>20,'default'=>'novo'],'created_at'=>['type'=>'DATETIME','null'=>true],'updated_at'=>['type'=>'DATETIME','null'=>true],]); $forge->addKey('id', true); $forge->addKey('user_id'); $forge->addKey('status'); });
        $ensure('wm_audit_logs', function() use($forge){ $forge->addField(['id'=>['type'=>'INT','constraint'=>11,'unsigned'=>true,'auto_increment'=>true],'admin_id'=>['type'=>'INT','constraint'=>11,'unsigned'=>true],'acao'=>['type'=>'VARCHAR','constraint'=>100],'detalhes'=>['type'=>'TEXT','null'=>true],'created_at'=>['type'=>'DATETIME','null'=>true],]); $forge->addKey('id', true); $forge->addKey('admin_id'); });
        setSuccessMessage('Tabelas criadas: ' . implode(', ', $created));
        return redirect()->to(adminUrl('wealth'));
    }
}
