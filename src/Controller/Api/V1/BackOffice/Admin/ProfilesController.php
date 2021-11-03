<?php

namespace App\Controller\Api\V1\BackOffice\Admin;

use App\Entity\Profil;
use App\Repository\AssociationRepository;
use App\Repository\ProfilRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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

    public function __construct(ProfilRepository $profilRepository, AssociationRepository $associationRepository, ValidatorInterface $validator, SerializerInterface $serializer, EntityManagerInterface $entityManager)
    {
        $this->profilRepository = $profilRepository;
        $this->associationRepository = $associationRepository;
        $this->validator = $validator;
        $this->serializer = $serializer;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/", name="browse", methods={"GET"})
     */
    public function browse(int $associationId): Response
    {
        $association = $this->associationRepository->find($associationId);

        $profiles = $association->getProfils();
        //dd($activities);

        return $this->json($profiles, Response::HTTP_OK, [], ['groups' => 'api_backoffice_admin_association_profiles_browse']);
    }

    /**
     * @Route("/{profilId}", name="read", methods={"GET"})
     */
    public function read(int $associationId, int $profilId): Response
    {
        $profil = $this->profilRepository->find($profilId);

        if ($profil->getAssociation()->getId() != $associationId) {
            return $this->json('Accès interdit', Response::HTTP_FORBIDDEN);
        }

        if (is_null($profil)) {
            return $this->getNotFoundResponse();
        }

        return $this->json($profil, Response::HTTP_OK, [], ['groups' => 'api_backoffice_admin_association_profiles_browse']);
    }

    /**
     * @Route("/{profilId}", name="edit", methods={"PATCH"}, requirements={"id"="\d+"})
     */
    public function edit(int $associationId, int $profilId, Request $request): Response
    {
        $profil = $this->profilRepository->find($profilId);

        if ($profil->getAssociation()->getId() != $associationId) {
            return $this->json('Accès interdit', Response::HTTP_FORBIDDEN);
        }

        if (is_null($profil)) {
            return $this->getNotFoundResponse();
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
     * @Route("/{profilId}", name="delete", methods={"DELETE"}, requirements={"id"="\d+"})
     */
    public function delete(int $associationId, int $profilId):Response
    {
        $profil = $this->profilRepository->find($profilId);

        if ($profil->getAssociation()->getId() != $associationId) {
            return $this->json('Accès interdit', Response::HTTP_FORBIDDEN);
        }

        if (is_null($profil)) {
            return $this->getNotFoundResponse();
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
