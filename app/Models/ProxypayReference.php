<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model ProxypayReference - Reutilizável
 *
 * Armazena referências de pagamento ProxyPay EMIS
 *
 * @property int $id
 * @property string $reference_id ID único da referência
 * @property string $entity Entidade ProxyPay
 * @property string $reference Número da referência EMIS
 * @property float $amount Valor em Kwanzas
 * @property string $end_datetime Data/hora de expiração
 * @property string $status Status: pending, paid, expired
 * @property string|null $order_id ID do pedido/transação
 * @property array|null $custom_fields Campos personalizados
 * @property string|null $payment_id ID do pagamento ProxyPay
 * @property \Carbon\Carbon|null $paid_at Data/hora do pagamento
 */
class ProxypayReference extends Model
{
    protected $table = 'proxypay_references';

    protected $fillable = [
        'reference_id',
        'entity',
        'reference',
        'amount',
        'end_datetime',
        'status',
        'order_id',
        'custom_fields',
        'payment_id',
        'paid_at'
    ];

    protected $casts = [
        'custom_fields' => 'array',
        'paid_at' => 'datetime',
        'end_datetime' => 'datetime',
        'amount' => 'decimal:2'
    ];

    /**
     * Scopes
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired')
                     ->orWhere(function($q) {
                         $q->where('status', 'pending')
                           ->where('end_datetime', '<', now());
                     });
    }

    /**
     * Marcar referência como paga
     *
     * @param array $paymentData Dados do pagamento
     * @return $this
     */
    public function markAsPaid($paymentData = [])
    {
        $this->update([
            'status' => 'paid',
            'payment_id' => $paymentData['id'] ?? $paymentData['payment_id'] ?? null,
            'paid_at' => now()
        ]);

        \Log::info('ProxyPay: Reference marked as paid', [
            'reference_id' => $this->reference_id,
            'payment_id' => $this->payment_id,
            'order_id' => $this->order_id
        ]);

        return $this;
    }

    /**
     * Marcar referência como expirada
     *
     * @return $this
     */
    public function markAsExpired()
    {
        $this->update(['status' => 'expired']);

        \Log::info('ProxyPay: Reference marked as expired', [
            'reference_id' => $this->reference_id,
            'end_datetime' => $this->end_datetime
        ]);

        return $this;
    }

    /**
     * Verificar se referência está paga
     *
     * @return bool
     */
    public function isPaid()
    {
        return $this->status === 'paid';
    }

    /**
     * Verificar se referência está pendente
     *
     * @return bool
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Verificar se referência está expirada
     *
     * @return bool
     */
    public function isExpired()
    {
        return $this->status === 'expired' ||
               ($this->status === 'pending' && $this->end_datetime < now());
    }

    /**
     * Obter tempo restante em segundos
     *
     * @return int
     */
    public function getTimeRemaining()
    {
        if ($this->isExpired() || $this->isPaid()) {
            return 0;
        }

        return max(0, $this->end_datetime->diffInSeconds(now()));
    }

    /**
     * Relacionamento com Order (opcional - adaptar ao seu projeto)
     */
    // public function order()
    // {
    //     return $this->belongsTo(Order::class, 'order_id');
    // }

    /**
     * Accessor: Valor formatado
     */
    public function getFormattedAmountAttribute()
    {
        return number_format($this->amount, 2, ',', '.') . ' Kz';
    }
}
