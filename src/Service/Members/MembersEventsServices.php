<?php

namespace App\Service\Members;

use App\Repository\EventRepository;
use App\Repository\ProfilRepository;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class MembersEventsServices {


    public function canRegisterOrUnregister($event, $profil){

        if($event->getAssociation() !== $profil->getAssociation()) {
            throw new AccessDeniedException("Accès interdit");
        }
        
    }
}