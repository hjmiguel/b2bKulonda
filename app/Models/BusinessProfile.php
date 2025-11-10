<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'company_name',
        'trade_name',
        'nif',
        'company_registration_number',
        'company_type',
        'industry',
        'company_address',
        'company_city',
        'company_state',
        'company_postal_code',
        'company_country',
        'company_phone',
        'company_email',
        'website',
        'logo',
        'business_license_doc',
        'tax_certificate_doc',
        'annual_revenue_range',
        'employee_count_range',
        'purchasing_volume_estimate',
        'credit_limit_requested',
        'credit_limit_approved',
        'payment_terms_preference',
        'bank_name',
        'bank_account_number',
        'delivery_notes',
        'status',
        'admin_notes',
        'verified_at',
        'verified_by',
    ];

    protected $casts = [
        'purchasing_volume_estimate' => 'decimal:2',
        'credit_limit_requested' => 'decimal:2',
        'credit_limit_approved' => 'decimal:2',
        'verified_at' => 'datetime',
    ];

    /**
     * Relacionamento: Perfil pertence a um usuário
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relacionamento: Admin que verificou o perfil
     */
    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Verificar se o perfil está aprovado
     */
    public function isApproved()
    {
        return $this->status === 'approved';
    }

    /**
     * Verificar se o perfil está pendente
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Verificar se o perfil está incompleto
     */
    public function isIncomplete()
    {
        return $this->status === 'incomplete';
    }

    /**
     * Verificar se o perfil está rejeitado
     */
    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    /**
     * Verificar se pode acessar funcionalidades B2B
     */
    public function canAccessB2BFeatures()
    {
        return $this->isApproved();
    }

    /**
     * Aprovar o perfil
     */
    public function approve($adminId, $creditLimit = null)
    {
        $this->status = 'approved';
        $this->verified_at = now();
        $this->verified_by = $adminId;
        
        if ($creditLimit !== null) {
            $this->credit_limit_approved = $creditLimit;
        }
        
        return $this->save();
    }

    /**
     * Rejeitar o perfil
     */
    public function reject($adminId, $reason = null)
    {
        $this->status = 'rejected';
        $this->verified_at = now();
        $this->verified_by = $adminId;
        
        if ($reason) {
            $this->admin_notes = $reason;
        }
        
        return $this->save();
    }

    /**
     * Enviar para aprovação
     */
    public function submitForApproval()
    {
        if ($this->isReadyForSubmission()) {
            $this->status = 'pending';
            return $this->save();
        }
        
        return false;
    }

    /**
     * Verificar se está pronto para submissão
     */
    public function isReadyForSubmission()
    {
        return !empty($this->company_name) &&
               !empty($this->nif) &&
               !empty($this->company_address) &&
               !empty($this->company_phone) &&
               !empty($this->business_license_doc) &&
               !empty($this->tax_certificate_doc);
    }

    /**
     * Accessor: Status Badge HTML
     */
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'incomplete' => '<span class="badge badge-danger">Incompleto</span>',
            'pending' => '<span class="badge badge-warning">Pendente</span>',
            'approved' => '<span class="badge badge-success">Aprovado</span>',
            'rejected' => '<span class="badge badge-danger">Rejeitado</span>',
        ];

        return $badges[$this->status] ?? '<span class="badge badge-secondary">Desconhecido</span>';
    }

    /**
     * Accessor: Status Text
     */
    public function getStatusTextAttribute()
    {
        $texts = [
            'incomplete' => 'Incompleto',
            'pending' => 'Pendente',
            'approved' => 'Aprovado',
            'rejected' => 'Rejeitado',
        ];

        return $texts[$this->status] ?? 'Desconhecido';
    }

    /**
     * Mutator: Formatar NIF antes de salvar
     */
    public function setNifAttribute($value)
    {
        // Remove todos os caracteres não numéricos
        $this->attributes['nif'] = preg_replace('/\D/', ''', $value);
    }

    /**
     * Accessor: Formatar NIF para exibição
     */
    public function getFormattedNifAttribute()
    {
        return $this->nif;
    }

    /**
     * Scope: Apenas perfis aprovados
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope: Apenas perfis pendentes
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope: Apenas perfis rejeitados
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Scope: Apenas perfis incompletos
     */
    public function scopeIncomplete($query)
    {
        return $query->where('status', 'incomplete');
    }

    /**
     * Get logo URL
     */
    public function getLogoUrlAttribute()
    {
        if ($this->logo) {
            return uploaded_asset($this->logo);
        }
        return static_asset('assets/img/placeholder-company.png');
    }

    /**
     * Get business license document URL
     */
    public function getBusinessLicenseUrlAttribute()
    {
        if ($this->business_license_doc) {
            return uploaded_asset($this->business_license_doc);
        }
        return null;
    }

    /**
     * Get tax certificate document URL
     */
    public function getTaxCertificateUrlAttribute()
    {
        if ($this->tax_certificate_doc) {
            return uploaded_asset($this->tax_certificate_doc);
        }
        return null;
    }

    /**
     * Verificar se tem crédito disponível
     */
    public function hasAvailableCredit()
    {
        return $this->isApproved() && $this->credit_limit_approved > 0;
    }

    /**
     * Get crédito disponível
     */
    public function getAvailableCreditAttribute()
    {
        if (!$this->isApproved()) {
            return 0;
        }
        
        // TODO: Calcular crédito usado
        $creditUsed = 0; // Implementar lógica de calcular crédito usado
        
        return max(0, $this->credit_limit_approved - $creditUsed);
    }
}
