<?php
namespace App\Mail\B2B;
use App\Models\User;
use App\Models\BusinessProfile;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ApplicationRejected extends Mailable
{
    use Queueable, SerializesModels;
    public $user, $businessProfile, $reason;

    public function __construct(User $user, BusinessProfile $businessProfile, $reason)
    {
        $this->user = $user;
        $this->businessProfile = $businessProfile;
        $this->reason = $reason;
    }

    public function build()
    {
        return $this->subject('Cadastro B2B - Informações Adicionais Necessárias - Kulonda')->view('emails.b2b.application_rejected');
    }
}
