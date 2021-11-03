<?php

namespace App\Controller\Api\V1\Member;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/api/v1/member/account/{accountId}/profil/{profilId}/events", name="api_v1_member_account_profil_events")
 */
class EventsController extends AbstractController
{
    /**
     * @Route("/", name="browse", methods={"GET"})
     */
    public function browse(): Response
    {
        return $this->render('api/v1/member/events/index.html.twig', [
            'controller_name' => 'EventsController',
        ]);
    }

    /**
     * @Route("/{eventId}/register", name="register", methods={"POST"})
     */
    public function register(): Response
    {
        return $this->render('api/v1/member/events/index.html.twig', [
            'controller_name' => 'EventsController',
        ]);
    }

    /**
     * @Route("/{eventId}/unregister", name="unregister", methods={"POST"})
     */
    public function unregister(): Response
    {
        return $this->render('api/v1/member/events/index.html.twig', [
            'controller_name' => 'EventsController',
        ]);
    }
}
