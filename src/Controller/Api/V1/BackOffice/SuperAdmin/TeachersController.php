<?php

namespace App\Controller\Api\V1\BackOffice\SuperAdmin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TeachersController extends AbstractController
{
    /**
     * @Route("/api/v1/back/office/super/admin/teachers", name="api_v1_back_office_super_admin_teachers")
     */
    public function index(): Response
    {
        return $this->render('api/v1/back_office/super_admin/teachers/index.html.twig', [
            'controller_name' => 'TeachersController',
        ]);
    }
}
