<?php

namespace App\Jobs;

use App\Http\Structs\EmailStruct;
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

    protected EmailStruct $struct;

    public function __construct(EmailStruct $struct)
    {
        $this->struct = $struct;
        $this->struct->setUUID(Str::uuid());
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::send(new RawMailable($this->struct));
        LogEmailOutbox::create([
            "outbox_uuid"       => $this->struct->getUUID(),
            "outbox_reply_to"   => config("mail.from.address"),
            "outbox_from_name"  => config("mail.from.name"),
            "outbox_from_email" => config("mail.from.address"),
            "outbox_to_name"    => "",
            "outbox_to_email"   => $this->struct->to,
            "outbox_title"      => $this->struct->subject,
            "outbox_message"    => $this->struct->body,
            "outbox_read"       => "no",
            "outbox_type"       => $this->struct->type,
            "outbox_created_at" => date("Y-m-d H:i:s"),
        ]);
    }
}
