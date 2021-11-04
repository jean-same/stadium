<?php

namespace App\Controller\Api\V1\Member;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
     * @Route("/api/v1/member/profiles", name="api_v1_member_account_profiles")
     */
class ProfilesController extends AbstractController
{
    private $security;

    public function __construct( Security $security )
    {
        $this->security = $security;
    }
    /**
     * @Route("/", name="browse", methods={"GET"})
     */
    public function browse(): Response
    {
        /**@var Account */
        $user = $this->security->getUser();

        $profiles = $user->getProfil();

        return $this->json($profiles, Response::HTTP_OK, [], ['groups' => 'api_backoffice_member_profiles_browse']);

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
