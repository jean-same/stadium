<?php

namespace App\Controller\Dashboards\Admin;

use App\Form\ActivityType;
use App\Repository\ActivityRepository;
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
    private $activityRepository;
    private $associationServices;

    public function __construct(FlashyNotifier $flashy, SluggerInterface $slugger, EntityManagerInterface $em, AssociationServices $associationServices, ActivityRepository $activityRepository)
    {
        $this->em = $em;
        $this->flashy = $flashy;
        $this->slugger = $slugger;
        $this->activityRepository = $activityRepository;
        $this->associationServices = $associationServices;
    }


    #[Route('/', name: 'activities')]
    #[Route('/{id}/edit', name: 'edit')]
    public function activities(Request $request, $id = null): Response
    {
        $association = $this->associationServices->getAssocFromUser();
        $activities = $association->getActivities();

        $newActivityForm = $this->createForm(ActivityType::class);

        if ($request->attributes->get('_route') == 'dashboards_admin_activities_activities') {

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

                $this->flashy->success("Activit?? ajout??e avec success");

                return $this->redirect($_SERVER['HTTP_REFERER']);
            }
            $formActivity = $newActivityForm->createView();
        }

        if ($request->attributes->get('_route') == 'dashboards_admin_activities_edit') {
            $activity = $this->activityRepository->find($id);

            if (is_null($activity)) {
                throw $this->createNotFoundException("Cet activit?? n'existe pas");
            }

            $match = $this->associationServices->checkAssocMatch($activity);

            if (!$match) {
                throw $this->createAccessDeniedException("Vous n'etes pas autoriser ?? r??aliser cet action");
            }
            $activityFormEdit = $this->createForm(ActivityType::class, $activity);

            $activityFormEdit->handleRequest($request);

            if ($activityFormEdit->isSubmitted() && $activityFormEdit->isValid()) {

                $activity = $activityFormEdit->getData();
                //$activity->setAssociation($association);

                $activityPicture = $activityFormEdit->get('picture')->getData();

                if ($activityPicture) {
                    $pictureUploaded = $this->slugger->slug($activity->getName() . '-' . uniqid()) . '.' . $activityPicture->guessExtension();

                    $activityPicture->move(
                        __DIR__ . '/../../../../../public/pictures/activity/',
                        $pictureUploaded
                    );

                    $activity->setPicture($pictureUploaded);
                }

                $this->em->flush();

                $this->flashy->success("Activit?? modifi?? avec success");

                return $this->redirect($_SERVER['HTTP_REFERER']);
            }

            $formActivity = $activityFormEdit->createView();
        }

        return $this->render('dashboards/admin/activities/activities.html.twig', compact('association', 'activities', 'formActivity'));
    }

    #[Route('/delete/{id}', name: 'delete')]
    public function delete($id)
    {

        $activity = $this->activityRepository->find($id);

        if (is_null($activity)) {
            throw $this->createNotFoundException("Cet activit?? n'existe pas");
        }

        $match = $this->associationServices->checkAssocMatch($activity);

        if (!$match) {
            throw $this->createAccessDeniedException("Vous n'etes pas autoriser ?? r??aliser cet action");
        }

        $this->em->remove($activity);
        $this->em->flush();

        $this->flashy->error("Activit?? supprim?? avec success");

        return $this->redirectToRoute("dashboards_admin_activities_activities");
    }
}
