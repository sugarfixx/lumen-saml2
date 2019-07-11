<?php
namespace Sugarfixx\Saml2\Solo\Facades;

use Illuminate\Support\Facades\Facade;

class SoloSaml2Auth extends Facade
{

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'Sugarfixx\Saml2\Solo\Saml2Auth';
    }

}
