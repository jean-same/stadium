<?php

namespace App\Controller\Dashboards\Admin;

use App\Form\ActivityType;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\Admin\AssociationServices;
use Symfony\Component\HttpFoundation\Request;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/dashboards/admin/activities', name: 'dashboards_admin_activities_')]
class ActivitiesController extends AbstractController
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


    #[Route('/', name: 'activities')]
    public function activities(Request $request): Response
    {
        $association = $this->associationServices->getAssocFromUser();
        $activities = $association->getActivities();
        //dd($activities);

        $newActivityForm = $this->createForm(ActivityType::class);

        $newActivityForm->handleRequest($request);

        if ($newActivityForm->isSubmitted() && $newActivityForm->isValid()) {

            $activity = $newActivityForm->getData();
            $activity->setAssociation($association);

            $activityPicture = $newActivityForm->get('picture')->getData();

            if ($activityPicture) {
                $pictureUploaded = $this->slugger->slug($activity->getName() . '-' . uniqid()) . '.' . $activityPicture->guessExtension();

                $activityPicture->move(
                    __DIR__ . '/../../../../../public/pictures/activity/',
                    $pictureUploaded
                );

                $activity->setPicture($pictureUploaded);
            } else {
                $activity->setPicture("activity.png");
            }

            $this->em->persist($activity);
            $this->em->flush();

            $this->flashy->success("Activité ajoutée avec success");

            return $this->redirect($_SERVER['HTTP_REFERER']);
        }

        $formActivity = $newActivityForm->createView();

        return $this->render('dashboards/admin/activities/activities.html.twig', compact('association', 'activities', 'formActivity'));
    }

}
