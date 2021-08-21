<?php

namespace App\Http\Structs;

class EmailStruct
{
    public string $to;
    public string $subject;
    public string $body;
    public string $type;
    private string $uuid;
    private string $url_read;

    public function __construct()
    {
        $this->type = 'system';
    }

    public function setUUID(string $uuid)
    {
        $this->uuid = $uuid;
        $this->url_read = url("/email/open/" . $uuid);
    }

    public function getUUID()
    {
        return $this->uuid;
    }

    public function getUrlRead()
    {
        return $this->url_read;
    }
}
