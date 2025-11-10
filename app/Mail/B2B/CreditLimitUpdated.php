<?php
namespace App\Mail\B2B;
use App\Models\User;
use App\Models\BusinessProfile;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CreditLimitUpdated extends Mailable
{
    use Queueable, SerializesModels;
    public $user, $businessProfile, $oldCreditLimit, $newCreditLimit;

    public function __construct(User $user, BusinessProfile $businessProfile, $oldCreditLimit, $newCreditLimit)
    {
        $this->user = $user;
        $this->businessProfile = $businessProfile;
        $this->oldCreditLimit = $oldCreditLimit;
        $this->newCreditLimit = $newCreditLimit;
    }

    public function build()
    {
        return $this->subject('Limite de Crédito Atualizado - Kulonda')->view('emails.b2b.credit_limit_updated');
    }
}
