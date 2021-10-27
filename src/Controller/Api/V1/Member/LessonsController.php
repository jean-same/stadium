<?php

namespace App\Controller\Api\V1\Member;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LessonsController extends AbstractController
{
    /**
     * @Route("/api/v1/member/lessons", name="api_v1_member_lessons")
     */
    public function index(): Response
    {
        return $this->render('api/v1/member/lessons/index.html.twig', [
            'controller_name' => 'LessonsController',
        ]);
    }
}
