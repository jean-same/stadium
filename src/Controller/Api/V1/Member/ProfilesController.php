<?php

namespace App\Controller\Api\V1\Member;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfilesController extends AbstractController
{
    /**
     * @Route("/api/v1/member/profiles", name="api_v1_member_profiles")
     */
    public function index(): Response
    {
        return $this->render('api/v1/member/profiles/index.html.twig', [
            'controller_name' => 'ProfilesController',
        ]);
    }
}
