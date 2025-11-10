<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BusinessProfile extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'company_name',
        'trade_name',
        'tax_id',
        'registration_number',
        'company_type',
        'industry',
        'address',
        'city',
        'postal_code',
        'province',
        'company_phone',
        'company_email',
        'website',
        'annual_revenue_range',
        'employee_count',
        'estimated_monthly_purchases',
        'credit_limit_requested',
        'payment_terms_preference',
        'business_license_path',
        'tax_certificate_path',
        'proof_address_path',
        'credit_limit',
        'credit_available',
        'payment_terms',
        'status',
        'rejection_reason',
        'notes',
    ];

    protected $casts = [
        'credit_limit' => 'decimal:2',
        'credit_available' => 'decimal:2',
        'estimated_monthly_purchases' => 'decimal:2',
        'credit_limit_requested' => 'decimal:2',
        'employee_count' => 'integer',
        'payment_terms' => 'integer',
        'payment_terms_preference' => 'integer',
    ];

    /**
     * Relacionamento com User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scopes
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Accessors
     */
    public function getBusinessLicenseUrlAttribute()
    {
        return $this->business_license_path ? asset('storage/' . $this->business_license_path) : null;
    }

    public function getTaxCertificateUrlAttribute()
    {
        return $this->tax_certificate_path ? asset('storage/' . $this->tax_certificate_path) : null;
    }

    public function getProofAddressUrlAttribute()
    {
        return $this->proof_address_path ? asset('storage/' . $this->proof_address_path) : null;
    }

    /**
     * Helpers
     */
    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function hasAvailableCredit($amount)
    {
        return $this->credit_available >= $amount;
    }

    public function reduceCredit($amount)
    {
        if ($this->hasAvailableCredit($amount)) {
            $this->credit_available -= $amount;
            $this->save();
            return true;
        }
        return false;
    }

    public function restoreCredit($amount)
    {
        $this->credit_available = min(
            $this->credit_available + $amount,
            $this->credit_limit
        );
        $this->save();
    }
}
