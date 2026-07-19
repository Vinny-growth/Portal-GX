<?php namespace Modules\Courses\Models;

use App\Models\BaseModel;

class PaymentModel extends BaseModel
{
    protected $table         = 'payments';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'membership_id', 'user_id', 'document', 'gateway', 'gateway_payment_id',
        'amount', 'currency', 'status', 'period_start', 'period_end', 'raw_json',
        'created_at', 'updated_at',
    ];

    public function getByGatewayId(string $gateway, string $gatewayPaymentId): ?array
    {
        return $this->where('gateway', $gateway)->where('gateway_payment_id', $gatewayPaymentId)->first();
    }

    public function forUser(int $userId): array
    {
        return $this->where('user_id', $userId)->orderBy('id', 'DESC')->findAll();
    }
}
