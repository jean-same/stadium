<?php

namespace App\Controller\Api\V1\BackOffice\SuperAdmin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


//* BREAD Associations

class AssociationsController extends AbstractController
{
    /**
     * @Route("/api/v1/back/office/super/admin/associations", name="api_v1_back_office_super_admin_associations")
     */
    public function index(): Response
    {
        return $this->render('api/v1/back_office/super_admin/associations/index.html.twig', [
            'controller_name' => 'AssociationsController',
        ]);
    }
}
