<?php

namespace Sugarfixx\Saml2\Events;

use Sugarfixx\Saml2\Saml2User;
use Sugarfixx\Saml2\Saml2Auth;

class Saml2LoginEvent extends Saml2Event {

    protected $user;
    protected $auth;

    function __construct($idp, Saml2User $user, Saml2Auth $auth)
    {
        parent::__construct($idp);
        $this->user = $user;
        $this->auth = $auth;
    }

    public function getSaml2User()
    {
        return $this->user;
    }

    public function getSaml2Auth()
    {
        return $this->auth;
    }
}
