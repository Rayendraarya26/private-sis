<?php

namespace App\Http\Structs;

class NotifStruct
{
    public string $title;
    public string $message;
    public int $user_id;
    public string $click_url;

    public function __construct()
    {
        $this->click_url = url("/dashboard");
    }
}
