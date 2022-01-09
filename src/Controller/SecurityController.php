<?php

namespace App\Controller;

use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils, Security $security): Response
    {
        if ($this->getUser()) {
            $roleSuperAdmin = $this->isGranted("ROLE_SUPER_ADMIN", $this->getUser());
            $roleAdmin = $this->isGranted("ROLE_ADMIN", $this->getUser());
            $roleAdherent = $this->isGranted("ROLE_ADHERENT", $this->getUser());

            if ($roleSuperAdmin) {
                return $this->redirectToRoute('dashboards_superadmin_home');
            } elseif ($roleAdmin) {
                return $this->redirectToRoute('dashboards_admin_home');
            } elseif ($roleAdherent) {
                return $this->redirectToRoute('dashboards_adherent_home');
            }
        }
        //$this->isGranted("ROLE_SUPER_ADMIN", "Vous n'etes pas autoriser à acceder  à cet ressource");
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
