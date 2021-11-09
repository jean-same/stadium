<?php

namespace App\Service\Members;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class MembersActivitiesServices
{
    public function canRegisterOrUnregister($activity, $profil)
    {
        if ($activity->getAssociation() !== $profil->getAssociation()) {
            throw new AccessDeniedException("Accès interdit");
        }
    }
}
