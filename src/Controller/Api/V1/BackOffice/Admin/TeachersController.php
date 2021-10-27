<?php

namespace App\Controller\Api\V1\BackOffice\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TeachersController extends AbstractController
{
    /**
     * @Route("/api/v1/back/office/admin/teachers", name="api_v1_back_office_admin_teachers")
     */
    public function index(): Response
    {
        return $this->render('api/v1/back_office/admin/teachers/index.html.twig', [
            'controller_name' => 'TeachersController',
        ]);
    }
}
