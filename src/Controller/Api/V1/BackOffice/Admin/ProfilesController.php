<?php

namespace App\Controller\Api\V1\BackOffice\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfilesController extends AbstractController
{
    /**
     * @Route("/api/v1/back/office/admin/profiles", name="api_v1_back_office_admin_profiles")
     */
    public function index(): Response
    {
        return $this->render('api/v1/back_office/admin/profiles/index.html.twig', [
            'controller_name' => 'ProfilesController',
        ]);
    }
}
