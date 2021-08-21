<?php

namespace Modules\Email\Http\Traits;

trait EmailTrait
{

    public function commonParser()
    {
        return ['FULLNAME', 'EMAIL'];
    }
}
