<?php

namespace App\Controller\Api\V1\BackOffice\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LessonsController extends AbstractController
{
    /**
     * @Route("/api/v1/back/office/admin/lessons", name="api_v1_back_office_admin_lessons")
     */
    public function index(): Response
    {
        return $this->render('api/v1/back_office/admin/lessons/index.html.twig', [
            'controller_name' => 'LessonsController',
        ]);
    }
}
