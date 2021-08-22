<?php

namespace Modules\Email\Http\Traits;

trait EmailTrait
{

    public function commonParser()
    {
        return ['FULLNAME', 'EMAIL'];
    }

    public function parse(string $text, string $parser, string $value)
    {
        $regex = '/[{]' . $parser . '?[}]/';
        return preg_replace($regex, $value, $text);
    }
}
