<?php

namespace App\Controller\Api\V1\BackOffice\SuperAdmin;

use App\Entity\Association;
use App\Repository\AccountRepository;
use App\Repository\AssociationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

//* BREAD Associations

/**
 * @Route("/api/v1/backoffice/superadmin/associations", name="api_v1_backoffice_superadmin_associations_")
 */

class AssociationsController extends AbstractController
{
    protected $associationRepository;
    protected $serializer;
    protected $validator;
    protected $entityManager;

    public function __construct(AssociationRepository $associationRepository, SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $entityManager)
    {
        $this->associationRepository = $associationRepository;
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->entityManager = $entityManager;
    }
    /**
     * @Route("/", name="browse", methods={"GET"})
     */
    public function browse(): Response
    {
        $allAssociations = $this->associationRepository->findAll();

        return $this->json($allAssociations, Response::HTTP_OK, [], ['groups' => 'api_backoffice_superadmin_associations_browse']);
    }

    /**
     * @Route("/{id}", name="read", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function read(int $id): Response
    {
        $association = $this->associationRepository->find($id);

        // If an association is null, then we return an error message in JSON with the method getNotFoundResponse
        if (is_null($association)) {
            return $this->getNotFoundResponse();
        }
        // Else we display the answer found
        return $this->json($association, Response::HTTP_OK, [], ['groups' => 'api_backoffice_superadmin_associations_browse']);
    }

    /**
     * @Route("/{id}", name="edit", methods={"PATCH"}, requirements={"id"="\d+"})
     */
    public function edit(int $id): Response
    {
        $association = $this->associationRepository->find($id);

        if (is_null($association)) {
            return $this->getNotFoundResponse();
        }

        // Retrieving the client's JSON
        $jsonContent = $this->request->getContent();

        $this->serializer->deserialize($jsonContent, Association::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $association]);

        $errors = $this->validator->validate($association);

        if (count($errors) > 0) {
            $responseAsArray = [
                'error' => true,
                'message' => $errors
            ];
            return $this->json($responseAsArray, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $this->entityManager->flush();

        $responseAsArray = [
            'message' => 'Association mise à jour',
            'name' => $association->getName(),
            'presidentLastName' => $association->getPresidentLastName(),
            'presidentFirstName' => $association->getPresidentFirstName()
        ];
        return $this->json($responseAsArray, Response::HTTP_OK);
    }

    /**
     * @Route("", name="add", methods={"POST"})
     */
    public function add(Request $request): Response
    {
        $jsonContent = $request->getContent();

        $association = $this->serializer->deserialize($jsonContent, Association::class, 'json');

        $errors = $this->validator->validate($association);

        if (count($errors) > 0) {
            $responseAsArray = [
                'error' => true,
                'message' => $errors
            ];
            return $this->json($responseAsArray, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $this->entityManager->persist($association);
        $this->entityManager->flush();

        $responseAsArray = [
            'message' => 'Association crée',
            'name' => $association->getName(),
            'presidentLastName' => $association->getPresidentLastName(),
            'presidentFirstName' => $association->getPresidentFirstName(),
            'address' => $association->getAddress(),
            'phoneNumber' => $association->getPhoneNumber()

        ];
        return $this->json($responseAsArray, Response::HTTP_CREATED);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"}, requirements={"id"="\d+"})
     */
    public function delete(int $id): Response
    {
        $association = $this->associationRepository->find($id);

        if (is_null($association)) {
            return $this->getNotFoundResponse();
        }

        $this->entityManager->remove($association);
        $this->entityManager->flush();

        $responseAsArray = [
            'message' => 'Association supprimée',
            'name' => $association->getName()
        ];
        return $this->json($responseAsArray);
    }

    private function getNotFoundResponse()
    {
        $responseArray = [
            'error' => true,
            'userMessage' => 'Ressource non trouvée',
            'internalMessage' => 'Cette association n\'existe pas dans la BDD',
        ];

        return $this->json($responseArray, Response::HTTP_GONE);
    }
}
