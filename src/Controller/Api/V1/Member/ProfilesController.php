<?php

namespace App\Controller\Api\V1\Member;

use App\Entity\Profil;
use App\Repository\ProfilRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
     * @Route("/api/v1/member/profiles", name="api_v1_member_account_profiles")
     */
class ProfilesController extends AbstractController
{
    private $security;
    private $validator;
    private $serializer;
    private $entityManager;
    private $profilRepository;

    public function __construct( ValidatorInterface $validator, Security $security, SerializerInterface $serializer, ProfilRepository $profilRepository , EntityManagerInterface $entityManager)
    {
        $this->security = $security;
        $this->validator = $validator;
        $this->serializer = $serializer;
        $this->entityManager = $entityManager;
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
     * @Route("/{profilId}", name="edit", methods={"PATCH"})
     */
    public function edit($profilId , Request $request): Response
    {
        $profil = $this->profilRepository->find($profilId);

        if (is_null($profil)) {
            return $this->getNotFoundResponse();
        }

        $jsonContent = $request->getContent();

        $this->serializer->deserialize($jsonContent, Profil::class, 'json', [
            AbstractNormalizer::OBJECT_TO_POPULATE => $profil
        ]);

        $errors = $this->validator->validate($profil);

        if (count($errors) > 0) {
            $reponseAsArray = [
                'error' => true,
                'message' => $errors,
            ];

            return $this->json($reponseAsArray, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $this->entityManager->flush();

        $reponseAsArray = [
            'message' => 'Profil mis à jour',
            'firstname' => $profil->getFirstName(),
            'lastname' => $profil->getLastName()
        ];

        return $this->json($reponseAsArray, Response::HTTP_CREATED);
    }

    /**
     * @Route("/", name="add", methods={"POST"})
     */
    public function add(Request $request): Response
    {
        $jsonContent = $request->getContent();
        $profil = $this->serializer->deserialize($jsonContent, Profil::class, 'json');

        $account = $this->security->getUser();

        $profil->setAccount($account);
        
        $errors = $this->validator->validate($profil);

        if (count($errors) > 0) {
            $reponseAsArray = [
                'error' => true,
                'message' => $errors,
            ];

            return $this->json($reponseAsArray, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $this->entityManager->persist($profil);
        $this->entityManager->flush();

        $reponseAsArray = [
            'message' => 'Profil créé',
            'firstname' => $profil->getFirstName(),
            'lastname' => $profil->getLastName()
        ];

        return $this->json($reponseAsArray, Response::HTTP_CREATED);
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
