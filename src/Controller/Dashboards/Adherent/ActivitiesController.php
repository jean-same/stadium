<?php

namespace App\Controller\Dashboards\Adherent;

use App\Repository\ActivityRepository;
use App\Service\Members\MembersActivitiesServices;
use App\Service\Members\MembersNotSubscribeActivitiesService;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\Members\MembersProfilServices;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/dashboards/adherent/{slug}/activities', name: 'dashboards_adherent_activities_')]
class ActivitiesController extends AbstractController
{
    private $activityRepository;
    private $membersProfilServices;
    private $membersActivitiesServices;
    private $membersNotSubscribeActivitiesService;

    public function __construct(EntityManagerInterface $em, MembersProfilServices $membersProfilServices, MembersActivitiesServices $membersActivitiesServices, ActivityRepository $activityRepository, MembersNotSubscribeActivitiesService $membersNotSubscribeActivitiesService)
    {
        $this->em = $em;
        $this->activityRepository = $activityRepository;
        $this->membersProfilServices = $membersProfilServices;
        $this->membersActivitiesServices = $membersActivitiesServices;
        $this->membersNotSubscribeActivitiesService = $membersNotSubscribeActivitiesService;
    }

    #[Route('/', name: 'activities')]
    public function activities($slug): Response
    {
        $profile = $this->membersProfilServices->getProfilFromUser($slug);

        $activityNotSubscribedByTheProfile = $this->membersNotSubscribeActivitiesService->getActivitiesNotSubsribedByConnecteduser($profile);

        return $this->render('dashboards/adherent/activities/activities.html.twig', compact('profile', 'activityNotSubscribedByTheProfile'));
    }


    #[Route('/{activityId}/register', name: 'register')]
    public function register($slug, $activityId): Response
    {
        $profile = $this->membersProfilServices->getProfilFromUser($slug);
        $activity = $this->activityRepository->find($activityId);

        if (!$activity) {
            throw $this->createNotFoundException("Cet activité n'existe pas");
        }

        $this->denyAccessUnlessGranted('CAN_READ', $profile, "Accès interdit");

        $this->membersActivitiesServices->canRegisterOrUnregister($activity, $profile);

        $profile->addActivity($activity);

        $this->em->flush();
        $this->addFlash("success", "Inscription prise en compte");

        return $this->redirect($_SERVER['HTTP_REFERER']);
    }

    #[Route('/{activityId}/unregister', name: 'unregister')]
    public function unregister($slug, $activityId): Response
    {
        $profile = $this->membersProfilServices->getProfilFromUser($slug);
        $activity = $this->activityRepository->find($activityId);

        if (!$activity) {
            throw $this->createNotFoundException("Cet activité n'existe pas");
        }

        $this->denyAccessUnlessGranted('CAN_READ', $profile, "Accès interdit");
        $this->membersActivitiesServices->canRegisterOrUnregister($activity, $profile);

        $profile->removeActivity($activity);

        $this->em->flush();
        $this->addFlash("success", "Désinscription prise en compte");
        
        return $this->redirect($_SERVER['HTTP_REFERER']);
    }
}
