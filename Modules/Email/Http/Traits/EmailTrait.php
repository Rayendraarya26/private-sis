<?php

namespace Modules\Email\Http\Traits;

trait EmailTrait
{

    public function commonParser()
    {
        return ['FULLNAME', 'EMAIL'];
    }

    public function parse(string $text, string $parser, $value = null)
    {
        if (!empty($parser)) {
            $regex = '/[{]' . $parser . '?[}]/';
            return preg_replace($regex, $value, $text);
        }
        return 0;
    }

    public function findVariable(string $text)
    {
        $regex = '/[{][A-Z_]+[}]/';
        preg_match_all($regex, $text, $match);
        return array_unique($match[0]);
    }
}
