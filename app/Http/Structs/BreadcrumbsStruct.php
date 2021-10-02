<?php

namespace App\Http\Structs;

class BreadcrumbsStruct
{
    public string $title;
    public string $link;

    public function __construct(string $title = "", string $link = "#")
    {
        $this->title = $title;
        $this->link  = $link;
    }
}
