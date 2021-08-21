<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RawMailable extends Mailable
{
    use Queueable, SerializesModels;

    protected array $data;

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
            ->subject($this->data['title'])
            ->to($this->data['to'])
            ->view('emails.raw')->with([
                'url_read' => $this->data['url_read'],
                'content' => $this->data['body'],
            ]);
    }
}
