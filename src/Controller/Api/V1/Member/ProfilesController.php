<?php

namespace App\Controller\Api\V1\Member;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
     * @Route("/api/v1/member/account/{accountId}/profiles", name="api_v1_member_account_profiles")
     */
class ProfilesController extends AbstractController
{
    /**
     * @Route("/", name="browse", methods={"GET"})
     */
    public function browse(): Response
    {
        return $this->render('api/v1/member/profiles/index.html.twig', [
            'controller_name' => 'ProfilesController',
        ]);
    }

    /**
     * @Route("/{profilId}", name="read", methods={"GET"})
     */
    public function read(): Response
    {
        return $this->render('api/v1/member/profiles/index.html.twig', [
            'controller_name' => 'ProfilesController',
        ]);
    }

    /**
     * @Route("/{profiId}", name="edit", methods={"PATCH"})
     */
    public function edit(): Response
    {
        return $this->render('api/v1/member/profiles/index.html.twig', [
            'controller_name' => 'ProfilesController',
        ]);
    }

    /**
     * @Route("/", name="add", methods={"POST"})
     */
    public function add(): Response
    {
        return $this->render('api/v1/member/profiles/index.html.twig', [
            'controller_name' => 'ProfilesController',
        ]);
    }
}
