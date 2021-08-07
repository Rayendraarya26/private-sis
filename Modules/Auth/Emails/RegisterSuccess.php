<?php

namespace Modules\Auth\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class RegisterSuccess extends Mailable
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
            ->view('auth::mails.register_success')
            ->with([
                'name' => $this->data->user_fullname,
                'link' => route('auth.verify', encrypt($this->data->user_token))
            ]);
    }
}
