<?php

namespace App\Service\Admin;

use Symfony\Component\Security\Core\Security;

class AssociationServices {

    private $security;

    public function __construct( Security $security )
    {
        $this->security = $security;
    }

    public function getAssocFromUser() {

        /**@var Account */
        $user = $this->security->getUser();

        if(in_array("ROLE_ASSOC", $user->getRoles()) ) {
            $association = $user->getAssociation();
        } else {
            $association = $user->getProfil()[0]->getAssociation();
        }

        return $association;
    }

}