<?php

namespace App\Controller\Api\V1\Member;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ActivitiesController extends AbstractController
{
    /**
     * @Route("/api/v1/member/activities", name="api_v1_member_activities")
     */
    public function index(): Response
    {
        return $this->render('api/v1/member/activities/index.html.twig', [
            'controller_name' => 'ActivitiesController',
        ]);
    }
}
