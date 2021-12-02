<?php

namespace App\Service\Admin;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;

class AssociationServices
{

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function getAssocFromUser()
    {

        /**@var Account */
        $user = $this->security->getUser();

        if (in_array("ROLE_ASSOC", $user->getRoles())) {
            $association = $user->getAssociation();
        } else {
            $association = $user->getProfil()[0]->getAssociation();
        }

        return $association;
    }

    public function checkAssocMatch($entity)
    {
        $association = $this->getAssocFromUser();

        if ($entity->getAssociation() == $association) {
            return true;
        } else {
            return false;
        }
    }

    public function checkAssocMatchFiles($file)
    {
        $association = $this->getAssocFromUser();
        
        if ($file->getProfil()->getAssociation() == $association) {
            return true;
        } else {
            return false;
        }
    }

    public function checkAssocMatchLessons($entity)
    {
        $association = $this->getAssocFromUser();

        if ($entity->getActivity()->getAssociation() == $association) {
            return true;
        } else {
            return false;
        }
    }

    public function getAdherentFromAssoc(){
        $users = $this->getAssocFromUser()->getProfils();
        $adherents = [];
        foreach($users as $user){
            if(in_array("ROLE_ADHERENT", $user->getAccount()->getRoles() )){
                $adherents[] = $user;
            }
        }
        return $adherents;
    }
}
