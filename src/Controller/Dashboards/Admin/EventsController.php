<?php

namespace App\Controller\Dashboards\Admin;

use App\Form\EventType;
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
    private $associationServices;


    public function __construct(FlashyNotifier $flashy, SluggerInterface $slugger, EntityManagerInterface $em, AssociationServices $associationServices)
    {
        $this->em = $em;
        $this->flashy = $flashy;
        $this->slugger = $slugger;
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
}
