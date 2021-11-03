<?php

namespace App\Controller\Api\V1\Member;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/v1/member/account/{accountId}/profil/{profilId}/activities", name="api_v1_member_account_profil_activities")
 */
class ActivitiesController extends AbstractController
{
    /**
     * @Route("/", name="browse", methods={"GET"})
     */
    public function browse(): Response
    {
        return $this->render('api/v1/member/activities/index.html.twig', [
            'controller_name' => 'ActivitiesController',
        ]);
    }

    /**
     * @Route("/{activityId}/register", name="register", methods={"POST"})
     */
    public function register(): Response
    {
        return $this->render('api/v1/member/activities/index.html.twig', [
            'controller_name' => 'ActivitiesController',
        ]);
    }

    /**
     * @Route("/{activityId}/unregister", name="unregister", methods={"POST"})
     */
    public function unregister(): Response
    {
        return $this->render('api/v1/member/activities/index.html.twig', [
            'controller_name' => 'ActivitiesController',
        ]);
    }
}
