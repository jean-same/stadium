<?php

namespace App\Controller\Api\V1\Member;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EventsController extends AbstractController
{
    /**
     * @Route("/api/v1/member/events", name="api_v1_member_events")
     */
    public function index(): Response
    {
        return $this->render('api/v1/member/events/index.html.twig', [
            'controller_name' => 'EventsController',
        ]);
    }
}
