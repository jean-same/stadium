<?php

namespace App\Controller\Api\V1\BackOffice\Admin;

use App\Entity\Profil;
use App\Repository\ProfilRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\AssociationRepository;
use App\Service\Admin\AssociationServices;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/api/v1/backoffice/admin/association/{associationId}/profiles", name="api_v1_backoffice_admin_profiles_")
 */
class ProfilesController extends AbstractController
{
    protected $profilRepository;
    protected $associationRepository;
    protected $validator;
    protected $serializer;
    protected $entityManager;
    protected $associationServices;

    public function __construct(ProfilRepository $profilRepository, AssociationRepository $associationRepository, ValidatorInterface $validator, SerializerInterface $serializer, EntityManagerInterface $entityManager,  AssociationServices $associationServices)
    {
        $this->profilRepository = $profilRepository;
        $this->associationRepository = $associationRepository;
        $this->validator = $validator;
        $this->serializer = $serializer;
        $this->entityManager = $entityManager;
        $this->associationServices = $associationServices;
    }

    /**
     * @Route("/", name="browse", methods={"GET"})
     */
    public function browse(): Response
    {
        $association = $this->associationServices->getAssocFromUser();

        $profiles = $association->getProfils();
        //dd($activities);

        return $this->json($profiles, Response::HTTP_OK, [], ['groups' => 'api_backoffice_admin_association_profiles_browse']);
    }

    /**
     * @Route("/{profilId}", name="read", methods={"GET"}, requirements={"profilId"="\d+"})
     */
    public function read(int $profilId): Response
    {
        $profil = $this->profilRepository->find($profilId);

        if (is_null($profil)) {
            return $this->getNotFoundResponse();
        }

        $match = $this->associationServices->checkAssocMatch($profil);

        if (!$match) {
            return $this->json("Accès interdit", Response::HTTP_FORBIDDEN);
        }       
        
        return $this->json($profil, Response::HTTP_OK, [], ['groups' => 'api_backoffice_admin_association_profiles_browse']);
    }

    /**
     * @Route("/{profilId}", name="edit", methods={"PATCH"}, requirements={"profilId"="\d+"})
     */
    public function edit(int $profilId, Request $request): Response
    {
        $profil = $this->profilRepository->find($profilId);

        if (is_null($profil)) {
            return $this->getNotFoundResponse();
        }

        $match = $this->associationServices->checkAssocMatch($profil);

        if (!$match) {
            return $this->json("Accès interdit", Response::HTTP_FORBIDDEN);
        }

        $jsonContent = $request->getContent();

        $this->serializer->deserialize($jsonContent, Profil::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $profil]);

        $errors = $this->validator->validate($profil);

        if (count($errors) > 0) {
            $responseAsArray = [
                'error' => true,
                'message' => $errors
            ];
            return $this->json($responseAsArray, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $this->entityManager->flush();

        $responseAsArray = [
            'message' => 'Profil mis à jour',
            'firstName' => $profil->getFirstName(),
            'lastName' => $profil->getLastName(),
            'picture' => $profil->getPicture(),
        ];
        return $this->json($responseAsArray, Response::HTTP_OK);
    }

    /**
     * @Route("/", name="add", methods={"POST"})
     */
    public function add(int $associationId, Request $request):Response
    {
        $jsonContent = $request->getContent();

        $profil = $this->serializer->deserialize($jsonContent, Profil::class, 'json');
        if ($profil->getAssociation()->getId() != $associationId) {
            return $this->json('Accès interdit', Response::HTTP_FORBIDDEN);
        }
        $errors = $this->validator->validate($profil);

        if (count($errors) > 0) {
            $responseAsArray = [
                'error' => true,
                'message' => $errors
            ];
            return $this->json($responseAsArray, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $this->entityManager->persist($profil);
        $this->entityManager->flush();

        $responseAsArray = [
            'message' => 'Profil crée',
            'firstName' => $profil->getFirstName(),
            'lastName' => $profil->getLastName(),
            'picture' => $profil->getPicture(),
            'association' => $associationId
        ];
        return $this->json($responseAsArray, Response::HTTP_CREATED);
    }

    /**
     * @Route("/{profilId}", name="delete", methods={"DELETE"}, requirements={"profilId"="\d+"})
     */
    public function delete(int $profilId):Response
    {
        $profil = $this->profilRepository->find($profilId);

        if (is_null($profil)) {
            return $this->getNotFoundResponse();
        }

        $match = $this->associationServices->checkAssocMatch($profil);

        if (!$match) {
            return $this->json("Accès interdit", Response::HTTP_FORBIDDEN);
        }
        $this->entityManager->remove($profil);
        $this->entityManager->flush();

        $responseAsArray = [
            'message' => 'Profil supprimé',
            'firstName' => $profil->getFirstName(),
            'lastName' => $profil->getLastName()
        ];
        return $this->json($responseAsArray);
    }

    private function getNotFoundResponse()
    {
        $responseArray = [
            'error' => true,
            'userMessage' => 'Ressource non trouvé',
            'internalMessage' => 'Ce compte n\'existe pas dans la BDD',
        ];

        return $this->json($responseArray, Response::HTTP_GONE);
    }
}
