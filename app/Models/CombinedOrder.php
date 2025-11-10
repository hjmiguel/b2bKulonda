<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\PreventDemoModeChanges;

class CombinedOrder extends Model
{
    use PreventDemoModeChanges;

    public function orders(){
    	return $this->hasMany(Order::class);
    }

    public function user(){
    	return $this->belongsTo(User::class);
    }

    /**
     * Relacionamento com ProxyPay Reference
     * A coluna é "order_id" (não "combined_order_id")
     */
    public function proxypayReference()
    {
        return $this->hasOne(\App\Models\ProxypayReference::class, 'order_id', 'id');
    }

    /**
     * Verificar se tem pagamento ProxyPay pendente
     */
    public function hasPendingProxyPayment()
    {
        $reference = $this->proxypayReference;
        if (!$reference) {
            return false;
        }
        return $reference->status === 'pending';
    }
}
