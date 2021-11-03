<?php

namespace App\Controller\Api\V1\Member;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/v1/member/account/{accountId}/profil/{profilId}/association", name="api_v1_member_account_profil_association")
 */
class AssociationController extends AbstractController
{
    /**
     * @Route("/", name="read", methods={"GET"})
     */
    public function read(): Response
    {
        return $this->render('api/v1/member/association/index.html.twig', [
            'controller_name' => 'AssociationController',
        ]);
    }

    /**
     * @Route("/", name="add", methods={"POST"})
     */
    public function add(): Response
    {
        return $this->render('api/v1/member/association/index.html.twig', [
            'controller_name' => 'AssociationController',
        ]);
    }
}
