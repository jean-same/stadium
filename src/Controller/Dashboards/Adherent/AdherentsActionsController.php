<?php

namespace App\Controller\Dashboards\Adherent;

use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\Members\MembersEventsServices;
use App\Service\Members\MembersProfilServices;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\Members\MembersNotSubscribeEventsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/dashboards/adherent/adherents', name: 'dashboards_adherent_adherents_actions_')]
class AdherentsActionsController extends AbstractController
{

    private $em;
    private $eventsRepository;
    private $membersEventsServices;
    private $membersProfilServices;

    public function __construct(EntityManagerInterface $em, MembersProfilServices $membersProfilServices,  EventRepository $eventsRepository, MembersEventsServices $membersEventsServices)
    {
        $this->em = $em;
        $this->eventsRepository = $eventsRepository;
        $this->membersEventsServices = $membersEventsServices;
        $this->membersProfilServices = $membersProfilServices;
    }

    #[Route('/{slug}', name: 'read')]
    public function read($slug): Response
    {
        $profile = $this->membersProfilServices->getProfilFromUser($slug);

        return $this->render('dashboards/adherent/adherents_actions/read.html.twig', compact('profile'));
    }

    #[Route('/{slug}/events', name: 'events')]
    public function events($slug , MembersNotSubscribeEventsService $membersNotSubscribeEventsService )
    {
        $profile = $this->membersProfilServices->getProfilFromUser($slug);

        $eventNotSubscribedByTheProfile = $membersNotSubscribeEventsService->getEventsNotSubsribedByConnecteduser($profile);

        return $this->render('dashboards/adherent/adherents_actions/events.html.twig', compact('profile', 'eventNotSubscribedByTheProfile'));
    }

    #[Route('/{slug}/events/{eventId}/register', name: 'register')]
    public function register($eventId,  $slug): Response
    {

        $event = $this->eventsRepository->find($eventId);
        $profile = $this->membersProfilServices->getProfilFromUser($slug);

        $this->denyAccessUnlessGranted('CAN_READ', $profile, "Accès interdit");

        $this->membersEventsServices->canRegisterOrUnregister($event, $profile);

        $profile->addEvent($event);

        $this->em->flush();
        $this->addFlash("success", "Inscription prise en compte");

        return $this->redirect($_SERVER['HTTP_REFERER']);
    }

    #[Route('/{slug}/events/{eventId}/unregister', name: 'unregister')]
    public function unregister($eventId, $slug): Response
    {

        $event = $this->eventsRepository->find($eventId);
        $profile = $this->membersProfilServices->getProfilFromUser($slug);

        $this->denyAccessUnlessGranted('CAN_READ', $profile, "Accès interdit");

        $this->membersEventsServices->canRegisterOrUnregister($event, $profile);

        $profile->removeEvent($event);

        $this->em->flush();

        $this->addFlash("success", "Désinscription prise en compte");

        return $this->redirect($_SERVER['HTTP_REFERER']);
    }
}
