<?php

namespace App\Controller\Api\V1\Member;

use App\Entity\Profil;
use App\Repository\ProfilRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/api/v1/member/profil/{profilId}/events", name="api_v1_member_account_profil_events")
 */
class EventsController extends AbstractController
{


    private $profilRepository;

    public function __construct( ProfilRepository $profilRepository )
    {
        $this->profilRepository = $profilRepository;
    }
    
    /**
     * @Route("/", name="read", methods={"GET"})
     */
    public function read($profilId): Response
    {

        $profil = $this->profilRepository->find($profilId);

        $this->denyAccessUnlessGranted('CAN_READ', $profil , "Accès interdit");

        $events = $profil->getEvent();

        return $this->json($events, Response::HTTP_OK, [], ['groups' => 'api_backoffice_member_events_browse']);
    }

    /**
     * @Route("/{eventId}/register", name="register", methods={"POST"})
     */
    public function register(Request $request): Response
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
