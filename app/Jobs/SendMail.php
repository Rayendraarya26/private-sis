<?php

namespace App\Jobs;

use App\Mail\RawMailable;
use App\Models\BbkkpSisLog\LogEmailOutbox;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class SendMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $data;

    public function __construct($data)
    {
        $this->data = $data;
        $this->data['uuid'] = Str::uuid();
        $this->data['url_read'] = url("/email/open/" . $this->data['uuid']);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $title = $this->data['title'];
        $body = $this->data['body'];
        $to = $this->data['to'];

        Mail::to($to)->send(new RawMailable($this->data));
        LogEmailOutbox::create([
            "outbox_uuid" => $this->data['uuid'],
            "outbox_reply_to" => env("MAIL_FROM_ADDRESS"),
            "outbox_from_name" => env("MAIL_FROM_NAME"),
            "outbox_from_email" => env("MAIL_FROM_ADDRESS"),
            "outbox_to_name" => "",
            "outbox_to_email" => $this->data['to'],
            "outbox_title" => $this->data['title'],
            "outbox_message" => $this->data['body'],
            "outbox_read" => "no",
            "outbox_type" => "system",
            "outbox_created_at" => date("Y-m-d H:i:s"),
        ]);
    }
}
