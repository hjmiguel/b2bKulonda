<?php
namespace App\Mail\B2B;
use App\Models\User;
use App\Models\BusinessProfile;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RequestMoreInfo extends Mailable
{
    use Queueable, SerializesModels;
    public $user, $businessProfile, $message;

    public function __construct(User $user, BusinessProfile $businessProfile, $message)
    {
        $this->user = $user;
        $this->businessProfile = $businessProfile;
        $this->message = $message;
    }

    public function build()
    {
        return $this->subject('Cadastro B2B - Informações Adicionais Necessárias - Kulonda')->view('emails.b2b.request_more_info');
    }
}
