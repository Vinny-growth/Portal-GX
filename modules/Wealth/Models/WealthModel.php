<?php namespace Modules\Wealth\Models;

use CodeIgniter\Model;
use App\Models\BaseModel;

class WealthModel extends BaseModel
{
    protected $builderProfile;
    protected $builderIncomeExpense;
    protected $builderAssetsFinancial;
    protected $builderAssetsRealEstate;
    protected $builderBusiness;
    protected $builderLiabilities;
    protected $builderGoals;
    protected $builderSessions;
    protected $builderMessages;
    protected $builderTokens;
    protected $builderSettings;
    protected $builderAppointments;
    protected $builderAudits;

    public function __construct()
    {
        parent::__construct();
        $this->builderProfile = $this->db->table('wm_user_profile');
        $this->builderIncomeExpense = $this->db->table('wm_income_expense');
        $this->builderAssetsFinancial = $this->db->table('wm_assets_financial');
        $this->builderAssetsRealEstate = $this->db->table('wm_assets_realestate');
        $this->builderBusiness = $this->db->table('wm_business_holdings');
        $this->builderLiabilities = $this->db->table('wm_liabilities');
        $this->builderGoals = $this->db->table('wm_goals');
        $this->builderSessions = $this->db->table('wm_sessions');
        $this->builderMessages = $this->db->table('wm_messages');
        $this->builderTokens = $this->db->table('wm_tokens');
        $this->builderSettings = $this->db->table('wm_settings');
        $this->builderAppointments = $this->db->table('wm_appointments');
        $this->builderAudits = $this->db->table('wm_audit_logs');
    }

    // Settings (key-value)
    public function getSetting($key, $default = null)
    {
        $row = $this->builderSettings->where('chave', $key)->get()->getRow();
        return $row ? $row->valor : $default;
    }

    public function setSetting($key, $value)
    {
        $exists = $this->builderSettings->where('chave', $key)->get()->getRow();
        if ($exists) {
            return $this->builderSettings->where('chave', $key)->update(['valor' => $value]);
        }
        return $this->builderSettings->insert(['chave' => $key, 'valor' => $value]);
    }

    public function incrementCounter($key, $by = 1)
    {
        $current = intval($this->getSetting($key, 0));
        return $this->setSetting($key, (string)($current + $by));
    }

    // Tokens
    public function getTokens($userId)
    {
        $row = $this->builderTokens->where('user_id', clrNum($userId))->get()->getRow();
        return $row ? (int)$row->tokens_disponiveis : 0;
    }

    public function ensureInitialToken($userId)
    {
        $row = $this->builderTokens->where('user_id', clrNum($userId))->get()->getRow();
        if (!$row) {
            $this->builderTokens->insert([
                'user_id' => clrNum($userId),
                'tokens_disponiveis' => 1,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            return 1;
        }
        return (int)$row->tokens_disponiveis;
    }

    public function addTokens($userId, $amount, $note = null, $adminId = null)
    {
        $tokens = $this->getTokens($userId);
        $new = max(0, $tokens + (int)$amount);
        $exists = $this->builderTokens->where('user_id', clrNum($userId))->get()->getRow();
        if ($exists) {
            $this->builderTokens->where('user_id', clrNum($userId))->update(['tokens_disponiveis' => $new, 'updated_at' => date('Y-m-d H:i:s')]);
        } else {
            $this->builderTokens->insert(['user_id' => clrNum($userId), 'tokens_disponiveis' => $new, 'updated_at' => date('Y-m-d H:i:s')]);
        }
        if ($adminId) {
            $this->addAudit($adminId, 'tokens_update', json_encode(['user_id' => $userId, 'delta' => $amount, 'note' => $note]));
        }
        return $new;
    }

    public function consumeToken($userId)
    {
        $uid = clrNum($userId);
        // Atomic decrement: only if tokens_disponiveis > 0
        $this->builderTokens
            ->set('tokens_disponiveis', 'tokens_disponiveis-1', false)
            ->set('updated_at', date('Y-m-d H:i:s'))
            ->where('user_id', $uid)
            ->where('tokens_disponiveis >', 0)
            ->update();
        return $this->db->affectedRows() > 0;
    }

    // Sessions & messages
    public function startSession($userId)
    {
        $this->builderSessions->insert([
            'user_id' => clrNum($userId),
            'started_at' => date('Y-m-d H:i:s'),
            'status' => 'ativa',
            'messages_count' => 0
        ]);
        return $this->db->insertID();
    }

    public function endSession($sessionId, $consumeToken = false)
    {
        $session = $this->getSession($sessionId);
        if (!$session) return false;
        $this->builderSessions->where('id', clrNum($sessionId))->update([
            'ended_at' => date('Y-m-d H:i:s'),
            'status' => 'concluida'
        ]);
        if ($consumeToken && !empty($session->user_id)) {
            $this->consumeToken($session->user_id);
        }
        return true;
    }

    public function getSession($sessionId)
    {
        return $this->builderSessions->where('id', clrNum($sessionId))->get()->getRow();
    }

    public function getActiveSessionByUser($userId)
    {
        return $this->builderSessions->where('user_id', clrNum($userId))->where('status', 'ativa')->orderBy('id', 'DESC')->get()->getRow();
    }

    public function addMessage($sessionId, $role, $content)
    {
        $this->builderMessages->insert([
            'session_id' => clrNum($sessionId),
            'role' => $role,
            'content' => $content,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        $this->builderSessions->where('id', clrNum($sessionId))->set('messages_count', 'messages_count+1', false)->update();
    }

    public function getMessages($sessionId)
    {
        return $this->builderMessages->where('session_id', clrNum($sessionId))->orderBy('id', 'ASC')->get()->getResult();
    }

    // Profiles & structured data
    public function upsertProfile($userId, $data)
    {
        $row = $this->builderProfile->where('user_id', clrNum($userId))->get()->getRow();
        $data['updated_at'] = date('Y-m-d H:i:s');
        if ($row) {
            return $this->builderProfile->where('user_id', clrNum($userId))->update($data);
        }
        $data['user_id'] = clrNum($userId);
        $data['created_at'] = date('Y-m-d H:i:s');
        return $this->builderProfile->insert($data);
    }

    public function addIncomeExpense($userId, $tipo, $categoria, $valor)
    {
        return $this->builderIncomeExpense->insert([
            'user_id' => clrNum($userId),
            'tipo' => $tipo,
            'categoria' => $categoria,
            'valor_mensal' => floatval($valor)
        ]);
    }

    public function addFinancialAsset($userId, $classe, $subtipo, $valor)
    {
        return $this->builderAssetsFinancial->insert([
            'user_id' => clrNum($userId),
            'classe' => $classe,
            'subtipo' => $subtipo,
            'valor_atual' => floatval($valor)
        ]);
    }

    public function addRealEstate($userId, $tipo, $valor, $renda = 0, $divida = 0)
    {
        return $this->builderAssetsRealEstate->insert([
            'user_id' => clrNum($userId),
            'tipo' => $tipo,
            'valor_estimado' => floatval($valor),
            'renda_aluguel' => floatval($renda),
            'saldo_divida' => floatval($divida)
        ]);
    }

    public function addBusiness($userId, $nome, $pct, $obs = null)
    {
        return $this->builderBusiness->insert([
            'user_id' => clrNum($userId),
            'nome' => $nome,
            'participacao_pct' => floatval($pct),
            'observacoes' => $obs
        ]);
    }

    public function addLiability($userId, $tipo, $saldo, $taxa, $prazo)
    {
        return $this->builderLiabilities->insert([
            'user_id' => clrNum($userId),
            'tipo' => $tipo,
            'saldo_atual' => floatval($saldo),
            'taxa_aprox' => floatval($taxa),
            'prazo_meses' => intval($prazo)
        ]);
    }

    public function addGoal($userId, $nome, $valor, $prazo, $prioridade = null, $obs = null)
    {
        return $this->builderGoals->insert([
            'user_id' => clrNum($userId),
            'nome_meta' => $nome,
            'valor_objetivo' => floatval($valor),
            'prazo_meses' => intval($prazo),
            'prioridade' => $prioridade,
            'observacoes' => $obs
        ]);
    }

    // Aggregations for result page
    public function getAggregates($userId)
    {
        $income = $this->db->table('wm_income_expense')->selectSum('valor_mensal', 'total')->where('user_id', clrNum($userId))->where('tipo', 'renda')->get()->getRow()->total ?? 0;
        $expense = $this->db->table('wm_income_expense')->selectSum('valor_mensal', 'total')->where('user_id', clrNum($userId))->where('tipo', 'despesa')->get()->getRow()->total ?? 0;
        $assetsFin = $this->db->table('wm_assets_financial')->selectSum('valor_atual', 'total')->where('user_id', clrNum($userId))->get()->getRow()->total ?? 0;
        $assetsRe = $this->db->table('wm_assets_realestate')->selectSum('valor_estimado', 'total')->where('user_id', clrNum($userId))->get()->getRow()->total ?? 0;
        $liabilities = $this->db->table('wm_liabilities')->selectSum('saldo_atual', 'total')->where('user_id', clrNum($userId))->get()->getRow()->total ?? 0;
        $patrimonio = floatval($assetsFin) + floatval($assetsRe) - floatval($liabilities);

        // allocation by class
        $rows = $this->db->table('wm_assets_financial')->select('classe, SUM(valor_atual) AS total')->where('user_id', clrNum($userId))->groupBy('classe')->get()->getResultArray();
        $alloc = [];
        foreach ($rows as $r) { $alloc[$r['classe']] = (float)$r['total']; }

        return [
            'income' => (float)$income,
            'expense' => (float)$expense,
            'savings' => max(0, (float)$income - (float)$expense),
            'assets_financial' => (float)$assetsFin,
            'assets_realestate' => (float)$assetsRe,
            'liabilities' => (float)$liabilities,
            'net_worth' => (float)$patrimonio,
            'allocation' => $alloc,
        ];
    }

    // Appointments
    public function addAppointment($data)
    {
        $data['status'] = $data['status'] ?? 'novo';
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->builderAppointments->insert($data);
    }

    public function updateAppointmentStatus($id, $status)
    {
        return $this->builderAppointments->where('id', clrNum($id))->update([
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }

    public function getAppointments($status = null)
    {
        if ($status) {
            $this->builderAppointments->where('status', $status);
        }
        return $this->builderAppointments->orderBy('id', 'DESC')->get()->getResult();
    }

    // Audit
    public function addAudit($adminId, $action, $details = null)
    {
        return $this->builderAudits->insert([
            'admin_id' => clrNum($adminId),
            'acao' => $action,
            'detalhes' => $details,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }
}
