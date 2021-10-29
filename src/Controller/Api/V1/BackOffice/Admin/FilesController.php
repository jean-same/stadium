<?php

namespace App\Controller\Api\V1\BackOffice\Admin;

use App\Entity\File;
use App\Repository\AssociationRepository;
use App\Repository\FileRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/api/v1/backoffice/admin/association/{associationId}/profiles/{profilId}/files", name="api_v1_back_office_admin_files_")
 */
class FilesController extends AbstractController
{
    protected $fileRepository;
    protected $associationRepository;
    protected $validator;
    protected $serializer;
    protected $entityManager;

    public function __construct(FileRepository $fileRepository, AssociationRepository $associationRepository, ValidatorInterface $validator, SerializerInterface $serializer, EntityManagerInterface $entityManager)
    {
        $this->fileRepository = $fileRepository;
        $this->associationRepository = $associationRepository;
        $this->validator = $validator;
        $this->serializer = $serializer;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/{fileId}", name="edit", methods={"PATCH"}, requirements={"fileId"="\d+"})
     */
    public function edit(int $associationId, int $profilId, int $fileId, Request $request): Response
    {
        $file = $this->fileRepository->find($fileId);

        if (($file->getProfil()->getId() != $profilId )|| ($file->getProfil()->getAssociation()->getId()!= $associationId) ){
            return $this->json('Accès interdit', Response::HTTP_FORBIDDEN, [], ['groups' => 'api_backoffice_admin_association_files']);
        }

        $jsonContent = $request->getContent();

        $this->serializer->deserialize($jsonContent, File::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $file]);

        $errors = $this->validator->validate($file);

        if (count($errors) > 0) {
            $responseAsArray = [
                'error' => true,
                'message' => $errors
            ];
            return $this->json($responseAsArray, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $this->entityManager->flush();

        $responseAsArray = [
            'message' => 'File mis à jour',
            'phoneNumber' => $file->getPhoneNumber(),
            'address' => $file->getAddress(),
        ];
        return $this->json($responseAsArray, Response::HTTP_OK);
    }

    /**
     * @Route("/", name="add", methods={"POST"})
     */
    public function add(int $profilId, Request $request): Response
    {
        $jsonContent = $request->getContent();

        $file = $this->serializer->deserialize($jsonContent, File::class, 'json');

        // if ($file->getProfil()->getId() != $profilId) {
        //     return $this->json('Accès interdit', Response::HTTP_FORBIDDEN, [], ['groups' => 'api_backoffice_admin_files']);
        // }
        $errors = $this->validator->validate($file);

        if (count($errors) > 0) {
            $responseAsArray = [
                'error' => true,
                'message' => $errors
            ];
            return $this->json($responseAsArray, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $this->entityManager->persist($file);
        $this->entityManager->flush();

        $responseAsArray = [
            'message' => 'Document crée',
            'phoneNumber' => $file->getPhoneNumber(),
            'dateOfBirth' => $file->getDateOfBirth(),
            'address' => $file->getAddress(),
            'emergencyContactName' => $file->getEmergencyContactName(),
            'emergencyContactPhoneNumber' => $file->getEmergencyContactPhoneNumber()
        ];
        return $this->json($responseAsArray, Response::HTTP_CREATED);
    }

     /**
     * @Route("/{fileId}", name="delete", methods={"DELETE"}, requirements={"id"="\d+"})
     */
    public function delete(int $fileId, int $profilId):Response
    {
        $file = $this->fileRepository->find($fileId);

        if ($file->getProfil()->getId() != $profilId) {
            return $this->json('Accès interdit', Response::HTTP_FORBIDDEN, [], ['groups' => 'api_backoffice_admin_association_files']);
        }

        $this->entityManager->remove($file);
        $this->entityManager->flush();

        $responseAsArray = [
            'message' => 'Document supprimé',
            'file' => $file->getId(),
        ];
        return $this->json($responseAsArray);
    }

    private function getNotFoundResponse()
    {
        $responseArray = [
            'error' => true,
            'userMessage' => 'Ressource non trouvé',
            'internalMessage' => 'Ce document n\'existe pas dans la BDD',
        ];

        return $this->json($responseArray, Response::HTTP_GONE);
    }
}
