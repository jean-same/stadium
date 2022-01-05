<?php

namespace App\Service\Members;

use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MembersProfilServices
{

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * Returns a NotFoundHttpException.
     *
     * This will result in a 404 response code. Usage example:
     *
     *     throw $this->createNotFoundException('Page not found!');
     */
    protected function createNotFoundException(string $message = 'Not Found', \Throwable $previous = null): NotFoundHttpException
    {
        return new NotFoundHttpException($message, $previous);
    }


    public function getProfilFromUser($slug)
    {
        /**@var Account */
        $user = $this->security->getUser();
        $profiles = $user->getProfil();
        $profilToShow = null;
        foreach ($profiles as $profil) {
            if ($profil->getSlug() == $slug) {
                $profilToShow = $profil;
            }
        }

        if (!$profilToShow) {
            throw $this->createNotFoundException("Cet adherent n'existe pas");
        }

        if ($profilToShow) {
            return $profilToShow;
        }

        return false;
    }
}
