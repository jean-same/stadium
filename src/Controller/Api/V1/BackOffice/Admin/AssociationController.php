<?php

namespace App\Controller\Api\V1\BackOffice\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AssociationController extends AbstractController
{
    /**
     * @Route("/api/v1/back/office/admin/association", name="api_v1_back_office_admin_association")
     */
    public function index(): Response
    {
        return $this->render('api/v1/back_office/admin/association/index.html.twig', [
            'controller_name' => 'AssociationController',
        ]);
    }
}
