<?php

namespace App\Controller\Api\V1\Member;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/v1/member/account/{accountId}", name="api_v1_member_account")
 */
class AccountController extends AbstractController
{
    /**
     * @Route("/", name="read", methods={"GET"})
     */
    public function read(): Response
    {
        return $this->render('api/v1/member/account/index.html.twig', [
            'controller_name' => 'AccountController',
        ]);
    }

     /**
     * @Route("/", name="edit", methods={"PATCH"})
     */
    public function edit(): Response
    {
        return $this->render('api/v1/member/account/index.html.twig', [
            'controller_name' => 'AccountController',
        ]);
    }
}
