<?php
namespace App\Mail\B2B;
use App\Models\User;
use App\Models\BusinessProfile;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RegistrationReceived extends Mailable
{
    use Queueable, SerializesModels;
    public $user;
    public $businessProfile;

    public function __construct(User $user, BusinessProfile $businessProfile)
    {
        $this->user = $user;
        $this->businessProfile = $businessProfile;
    }

    public function build()
    {
        return $this->subject('Cadastro B2B Recebido - Kulonda')
                    ->view('emails.b2b.registration_received');
    }
}
