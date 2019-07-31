<?php
/**
 * Created by PhpStorm.
 * User: sugarfixx
 * Date: 31/07/2019
 * Time: 23:36
 */

namespace Sugarfixx\Saml2\Events;

class Saml2Event {

    protected $idp;

    function __construct($idp)
    {
        $this->idp = $idp;
    }

    public function getSaml2Idp()
    {
        return $this->idp;
    }

}

