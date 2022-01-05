<?php

namespace App\Controller\Dashboards\Adherent;


use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\Members\MembersEventsServices;
use App\Service\Members\MembersProfilServices;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\Members\MembersNotSubscribeEventsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[Route('/dashboards/adherent/{slug}/events', name: 'dashboards_adherent_events_')]
class EventsController extends AbstractController
{

    private $em;
    private $flashy;
    private $eventsRepository;
    private $membersEventsServices;
    private $membersProfilServices;

    public function __construct(EntityManagerInterface $em, FlashyNotifier $flashy, MembersProfilServices $membersProfilServices,  EventRepository $eventsRepository, MembersEventsServices $membersEventsServices)
    {
        $this->em = $em;
        $this->flashy = $flashy;
        $this->eventsRepository = $eventsRepository;
        $this->membersEventsServices = $membersEventsServices;
        $this->membersProfilServices = $membersProfilServices;
    }

    #[Route('/', name: 'events')]
    public function events($slug, MembersNotSubscribeEventsService $membersNotSubscribeEventsService)
    {
        $profile = $this->membersProfilServices->getProfilFromUser($slug);

        $eventNotSubscribedByTheProfile = $membersNotSubscribeEventsService->getEventsNotSubsribedByConnecteduser($profile);

        return $this->render('dashboards/adherent/events/events.html.twig', compact('profile', 'eventNotSubscribedByTheProfile'));
    }

    #[Route('/{eventId}/register', name: 'register')]
    public function register($eventId,  $slug): Response
    {

        $event = $this->eventsRepository->find($eventId);
        $profile = $this->membersProfilServices->getProfilFromUser($slug);

        if (!$event) {
            throw $this->createNotFoundException("Cet evenement n'existe pas");
        }

        $this->denyAccessUnlessGranted('CAN_READ', $profile, "Accès interdit");

        $this->membersEventsServices->canRegisterOrUnregister($event, $profile);

        $profile->addEvent($event);

        $this->em->flush();
        $this->flashy->success('Inscription prise en compte!');

        return $this->redirect($_SERVER['HTTP_REFERER']);
    }

    #[Route('/{eventId}/unregister', name: 'unregister')]
    public function unregister($eventId, $slug): Response
    {

        $event = $this->eventsRepository->find($eventId);
        $profile = $this->membersProfilServices->getProfilFromUser($slug);

        if (!$event) {
            throw $this->createNotFoundException("Cet evenement n'existe pas");
        }


        $this->denyAccessUnlessGranted('CAN_READ', $profile, "Accès interdit");

        $this->membersEventsServices->canRegisterOrUnregister($event, $profile);

        $profile->removeEvent($event);

        $this->em->flush();
        $this->flashy->success('Désinscription prise en compte!');

        return $this->redirect($_SERVER['HTTP_REFERER']);
    }
}
