<?php

namespace App\Controller\Api\V1\Member;

use App\Entity\File;
use App\Repository\FileRepository;
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
 * @Route("/api/v1/member/profil/{profilId}/files", name="api_v1_member_account_profil_files")
 */
class FilesController extends AbstractController
{
    private $profilRespository;
    private $fileRepository;
    private $serializer;
    private $validator;
    private $entityManager;

    public function __construct(ProfilRepository $profilRespository, FileRepository $fileRepository, SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $entityManager)
    {
        $this->profilRespository = $profilRespository;
        $this->fileRepository = $fileRepository;
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->entityManager = $entityManager;
    }
    /**
     * @Route("/", name="read", methods={"GET"})
     */
    public function read($profilId): Response
    {
        $profil = $this->profilRespository->find($profilId);

        $this->denyAccessUnlessGranted('CAN_READ', $profil, "Accès interdit");
        $file = $profil->getFile();

        return $this->json($file, Response::HTTP_OK, [], ['groups' => "api_member_files_read"]);
    }

    /**
     * @Route("/", name="edit", methods={"PATCH"})
     */
    public function edit($profilId, Request $request): Response
    {
        $profil = $this->profilRespository->find($profilId);
        $this->denyAccessUnlessGranted('CAN_READ', $profil, "Accès interdit");
        $file = $profil->getFile();

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
            'message' => 'Document mis à jour',
            'phoneNumber' => $file->getPhoneNumber(),
            'dateOfBirth' => $file->getDateOfBirth()
        ];
        return $this->json($responseAsArray, Response::HTTP_OK);
    }

    /**
     * @Route("/", name="add", methods={"POST"})
     */
    public function add($profilId, Request $request): Response
    {
        $profil = $this->profilRespository->find($profilId);
        $this->denyAccessUnlessGranted('CAN_READ', $profil, "Accès interdit");

        $jsonContent = $request->getContent();

        $file = $this->serializer->deserialize($jsonContent, File::class, 'json');

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
        ];
        return $this->json($responseAsArray, Response::HTTP_CREATED);
    }

    /**
     * @Route("/", name="delete", methods={"DELETE"})
     */
    public function delete($profilId): Response
    {
        $profil = $this->profilRespository->find($profilId);

        $this->denyAccessUnlessGranted('CAN_READ', $profil, "Accès interdit");
        $file=$profil->getFile();

        $profil->setFile(null);
        $this->entityManager->flush();

        $this->entityManager->remove($file);
        $this->entityManager->flush();

        $reponseAsArray = [
            'message' => 'file supprimé',
            'file' => $file->getId()
        ];

        return $this->json($reponseAsArray);
    }
}
