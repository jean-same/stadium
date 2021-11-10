<?php

namespace App\Controller\Api\V1\BackOffice\Admin;

use App\Entity\File;
use App\Repository\AssociationRepository;
use App\Repository\FileRepository;
use App\Repository\ProfilRepository;
use App\Service\Admin\AssociationServices;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/api/v1/backoffice/admin/association/profiles/{profilId}/files", name="api_v1_backoffice_admin_association_files_")
 */
class FilesController extends AbstractController
{
    protected $fileRepository;
    protected $associationRepository;
    protected $profilRepository;
    protected $validator;
    protected $serializer;
    protected $entityManager;
    protected $associationServices;

    public function __construct(FileRepository $fileRepository, AssociationRepository $associationRepository, ValidatorInterface $validator, SerializerInterface $serializer, EntityManagerInterface $entityManager, ProfilRepository $profilRepository, AssociationServices $associationServices)
    {
        $this->fileRepository = $fileRepository;
        $this->associationRepository = $associationRepository;
        $this->validator = $validator;
        $this->serializer = $serializer;
        $this->entityManager = $entityManager;
        $this->profilRepository = $profilRepository;
        $this->associationServices = $associationServices;
    }

    /**
     * @Route("/{fileId}", name="edit", methods={"PATCH"}, requirements={"fileId"="\d+"})
     */
    public function edit( int $fileId, Request $request): Response
    {
        $file = $this->fileRepository->find($fileId);

        if (is_null($file)) {
            return $this->getNotFoundResponse();
        }

        $match = $this->associationServices->checkAssocMatchFiles($file);

        if (!$match) {
            return $this->json("Accès interdit", Response::HTTP_FORBIDDEN);
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
    public function add(Request $request, $profilId): Response
    {
        $jsonContent = $request->getContent();

        $file = $this->serializer->deserialize($jsonContent, File::class, 'json');
        $profil = $this->profilRepository->find($profilId);
        $file->setProfil($profil);

        $match = $this->associationServices->checkAssocMatchFiles($file);

        if (!$match) {
            return $this->json("Accès interdit", Response::HTTP_FORBIDDEN);
        }

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
     * @Route("/{fileId}", name="delete", methods={"DELETE"}, requirements={"fileId"="\d+"})
     */
    public function delete(int $fileId, int $profilId): Response
    {
        $file = $this->fileRepository->find($fileId);
        $profil = $this->profilRepository->find($profilId);

        if (is_null($file)) {
            return $this->getNotFoundResponse();
        }

        $match = $this->associationServices->checkAssocMatchFiles($file);

        if (!$match) {
            return $this->json("Accès interdit", Response::HTTP_FORBIDDEN);
        }

        if($profil != $file->getProfil()){
            return $this->json("Le dossier que vous voulez supprimer n'appartient pas à ce profil", Response::HTTP_FORBIDDEN);
        }

        // Profil is set to null to be able to delete after the second flush
        $profil->setFile(null);
        $this->entityManager->flush();
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
