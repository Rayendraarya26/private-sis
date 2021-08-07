<?php

namespace Modules\Auth\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ForgetPassword extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->from([env('MAIL_FROM_ADDRESS', env('MAIL_FROM_NAME'))])
            ->view('auth::mails.reset_password')
            ->with([
                'name' => $this->data->user_fullname,
                'link' => route('auth.reset_password', encrypt($this->data->user_token))
            ]);
    }
}
