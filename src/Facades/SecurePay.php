<?php

namespace Glsb\SecurePay\Facades;

use Illuminate\Support\Facades\Facade;

class SecurePay extends Facade
{
    /**
     * Get the registered name for the component.
     *
     * @return string
     */
    public function getFacadeAccessor()
    {
        return 'securepay';
    }
}
