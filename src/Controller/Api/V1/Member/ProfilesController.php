<?php

namespace App\Controller\Api\V1\Member;

use App\Repository\ProfilRepository;
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
    private $profilRepository;

    public function __construct( Security $security, ProfilRepository $profilRepository )
    {
        $this->security = $security;
        $this->profilRepository = $profilRepository;
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
    public function read($profilId): Response
    {
        $profile = $this->profilRepository->find($profilId);

                if (is_null($profile)) {
                    return $this->getNotFoundResponse();
                }

                return $this->json($profile, Response::HTTP_OK, [], ['groups' => 'api_backoffice_member_profiles_browse']);
    }

    /**
     * @Route("/{profiId}", name="edit", methods={"PATCH"})
     */
    public function edit($profilId): Response
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

    private function getNotFoundResponse()
    {
        $responseArray = [
            'error' => true,
            'userMessage' => 'Ressource non trouvée',
            'internalMessage' => 'Ce profil n\'existe pas dans la BDD',
        ];

        return $this->json($responseArray, Response::HTTP_GONE);
    }
}
