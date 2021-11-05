<?php

namespace App\Controller\Api\V1\Member;

use App\Entity\Profil;
use App\Repository\EventRepository;
use App\Repository\ProfilRepository;
use App\Service\Members\MembersEventsServices;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/api/v1/member/profil/{profilId}/events", name="api_v1_member_account_profil_events")
 */
class EventsController extends AbstractController
{
    private $entityManager;
    private $eventsRepository;
    private $profilRepository;
    private $membersEventsServices;

    public function __construct( ProfilRepository $profilRepository, EventRepository $eventsRepository , EntityManagerInterface $entityManager , MembersEventsServices $membersEventsServices )
    {
        $this->eventsRepository = $eventsRepository;
        $this->profilRepository = $profilRepository;
        $this->entityManager = $entityManager;
        $this->membersEventsServices = $membersEventsServices;
    }
    
    /**
     * @Route("/", name="read", methods={"GET"})
     */
    public function read($profilId): Response
    {

        $profil = $this->profilRepository->find($profilId);

        $this->denyAccessUnlessGranted('CAN_BE_HERE', $profil , "Accès interdit");

        $events = $profil->getEvent();

        return $this->json($events, Response::HTTP_OK, [], ['groups' => 'api_backoffice_member_events_browse']);
    }

    /**
     * @Route("/{eventId}/register", name="register", methods={"POST"})
     */
    public function register( $eventId , $profilId ): Response
    {

        $event = $this->eventsRepository->find($eventId);
        $profil = $this->profilRepository->find($profilId);

        $this->denyAccessUnlessGranted('CAN_BE_HERE', $profil , "Accès interdit");

        $this->membersEventsServices->canRegisterOrUnregister($event, $profil );

        $profil->addEvent($event);

        $this->entityManager->persist($profil);
        $this->entityManager->flush();
        
        $reponseAsArray = [
            'message' => 'Inscription prise en compte'
        ];

        return $this->json($reponseAsArray, Response::HTTP_CREATED);
    }

    /**
     * @Route("/{eventId}/unregister", name="unregister", methods={"POST"})
     */
    public function unregister( $eventId , $profilId ): Response
    {

        $event = $this->eventsRepository->find($eventId);
        $profil = $this->profilRepository->find($profilId);

        $this->denyAccessUnlessGranted('CAN_BE_HERE', $profil , "Accès interdit");

        $this->membersEventsServices->canRegisterOrUnregister($event, $profil );

        $profil->removeEvent($event);

        $this->entityManager->persist($profil);
        $this->entityManager->flush();

        $reponseAsArray = [
            'message' => 'Desinscription prise en compte'
        ];

        return $this->json($reponseAsArray, Response::HTTP_CREATED);
    }
}
