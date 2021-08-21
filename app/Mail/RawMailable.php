<?php

namespace App\Mail;

use App\Http\Structs\EmailStruct;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RawMailable extends Mailable
{
    use Queueable, SerializesModels;

    protected EmailStruct $struct;

    public function __construct($struct)
    {
        $this->struct = $struct;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->to($this->struct->to)
            ->subject($this->struct->subject)
            ->view('emails.raw')->with([
                'url_read' => $this->struct->getUrlRead(),
                'content' => $this->struct->body,
            ]);
    }
}
