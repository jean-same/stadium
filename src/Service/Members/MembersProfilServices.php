<?php

namespace App\Service\Members;

use Symfony\Component\Security\Core\Security;

class MembersProfilServices{

    private $security;

    public function __construct( Security $security )
    {
        $this->security = $security;
    }
    public function getProfilFromUser($slug){
        /**@var Account */
        $user = $this->security->getUser();
        $profiles = $user->getProfil();
        foreach($profiles as $profil){
            if($profil->getSlug() == $slug ){
                $profilToShow = $profil;
            }
        }

        if($profilToShow){
            return $profilToShow;
        }

        return false;
    }
}