<?php
namespace App\Mail\B2B;
use App\Models\User;
use App\Models\BusinessProfile;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ApplicationApproved extends Mailable
{
    use Queueable, SerializesModels;
    public $user, $businessProfile, $creditLimit, $paymentTerms;

    public function __construct(User $user, BusinessProfile $businessProfile, $creditLimit, $paymentTerms)
    {
        $this->user = $user;
        $this->businessProfile = $businessProfile;
        $this->creditLimit = $creditLimit;
        $this->paymentTerms = $paymentTerms;
    }

    public function build()
    {
        return $this->subject('Cadastro B2B Aprovado - Kulonda')->view('emails.b2b.application_approved');
    }
}
