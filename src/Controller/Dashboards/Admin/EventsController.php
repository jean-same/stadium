<?php

namespace App\Controller\Dashboards\Admin;

use App\Form\EventType;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\Admin\AssociationServices;
use Symfony\Component\HttpFoundation\Request;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/dashboards/admin/events', name: 'dashboards_admin_events_')]
class EventsController extends AbstractController
{
    private $em;
    private $flashy;
    private $slugger;
    private $eventRepository;
    private $associationServices;


    public function __construct(FlashyNotifier $flashy, SluggerInterface $slugger, EntityManagerInterface $em, AssociationServices $associationServices, EventRepository $eventRepository)
    {
        $this->em = $em;
        $this->flashy = $flashy;
        $this->slugger = $slugger;
        $this->eventRepository = $eventRepository;
        $this->associationServices = $associationServices;
    }

    #[Route('/', name: 'events')]
    public function events(Request $request): Response
    {
        $association = $this->associationServices->getAssocFromUser();
        $events = $association->getEvents();

        $newEventForm = $this->createForm(EventType::class);

        if ($request->attributes->get('_route') == 'dashboards_admin_events_events') {

            $event = $newEventForm->getData();

            $newEventForm->handleRequest($request);

            if ($newEventForm->isSubmitted() && $newEventForm->isValid()) {
                $event = $newEventForm->getData();

                $event->setAssociation($association);

                $activityPicture = $newEventForm->get('picture')->getData();

                if ($activityPicture) {
                    $pictureUploaded = $this->slugger->slug($event->getName() . '-' . uniqid()) . '.' . $activityPicture->guessExtension();

                    $activityPicture->move(
                        __DIR__ . '/../../../../../public/pictures/event/',
                        $pictureUploaded
                    );

                    $event->setPicture($pictureUploaded);
                } else {
                    $event->setPicture("event.png");
                }

                $this->em->persist($event);
                $this->em->flush();

                $this->flashy->success("Evenement ajoutée avec success");

                return $this->redirect($_SERVER['HTTP_REFERER']);
            }


            $formEvent = $newEventForm->createView();
        }

        return $this->render('dashboards/admin/events/events.html.twig', compact('events', 'formEvent'));
    }

    #[Route('/delete/{id}', name: 'delete')]
    public function delete($id)
    {
        $event = $this->eventRepository->find($id);

        if (is_null($event)) {
            throw $this->createNotFoundException("Cet activité n'existe pas");
        }

        $this->em->remove($event);
        $this->em->flush();

        $this->flashy->success("Evenement supprimé avec success");

        return $this->redirectToRoute('dashboards_admin_events_events');
    }
}
