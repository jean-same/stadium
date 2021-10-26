<?php

namespace App\Controller\Api\V1\BackOffice\SuperAdmin;

use App\Entity\File;
use App\Repository\FileRepository;
use App\Repository\EventRepository;
use App\Repository\ProfilRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

/**
* @Route("/api/v1/back/office/super/admin/files", name="api_v1_back_office_super_admin_files")
*/
class FilesController extends AbstractController
{


    protected $fileRepository;
    protected $validator;
    protected $serializer;
    protected $entityManager;
    protected $profilRepository;

    public function __construct(ValidatorInterface $validator, FileRepository $fileRepository, ProfilRepository $profilRepository, SerializerInterface $serializer, EntityManagerInterface $entityManager)
    {
        $this->fileRepository = $fileRepository;
        $this->profilRepository = $profilRepository;
        $this->validator = $validator;
        $this->serializer = $serializer;
        $this->entityManager = $entityManager;
    }
    
    /**
     * @Route("", name="browse" , methods={"GET"})
     */
    public function browse(): Response
    {
        $files = $this->fileRepository->findAll();

        return $this->json($files, Response::HTTP_OK, [], ['groups' => "file_browse"]);
    }

        /**
     * @Route("/{id}", name="read", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function read($id): Response
    {
        $files = $this->fileRepository->find($id);

        if (is_null($files)) {
            return $this->getNotFoundResponse();
        }

        return $this->json($files, Response::HTTP_OK, [], ['groups' => 'file_browse']);
    }


    /**
     * @Route("/{id}", name="edit", methods={"PATCH"}, requirements={"id"="\d+"})
     */
    public function edit(int $id, Request $request): Response
    {
        $file = $this->fileRepository->find($id);

        if (is_null($file)) {
            return $this->getNotFoundResponse();
        }

        $jsonContent = $request->getContent();

        $this->serializer->deserialize($jsonContent, File::class, 'json', [
            AbstractNormalizer::OBJECT_TO_POPULATE => $file
        ]);

        $errors = $this->validator->validate($file);
        if (count($errors) > 0) {
            $reponseAsArray = [
                'error' => true,
                'message' => $errors,
            ];

            return $this->json($reponseAsArray, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $this->entityManager->persist($file);
        $this->entityManager->flush();

        $reponseAsArray = [
            'message' => 'Files mis à jour',
            'id' => $file->getId()
        ];

        return $this->json($reponseAsArray, Response::HTTP_CREATED);
    }

    /**
    * @Route("", name="add", methods={"POST"})
    */
    public function add(Request $request): Response
    {
        $jsonContent = $request->getContent();
        $file = $this->serializer->deserialize($jsonContent, File::class, 'json');

        $profil = $this->profilRepository->find(6);

        $file->setProfil($profil);

        $errors = $this->validator->validate($file);

        if (count($errors) > 0) {
            $reponseAsArray = [
                'error' => true,
                'message' => $errors,
            ];

            return $this->json($reponseAsArray, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $this->entityManager->persist($file);
        $this->entityManager->flush();

        $reponseAsArray = [
            'message' => 'File créé',
            'id' => $file->getId()
        ];

        return $this->json($reponseAsArray, Response::HTTP_CREATED);
    }

    /**
    * @Route("/{id}", name="delete", methods={"DELETE"}, requirements={"id"="\d+"})
    */
    public function delete(int $id): Response
    {
        $file = $this->fileRepository->find($id);

        if (is_null($file)) {
            return $this->getNotFoundResponse();
        }

        $this->entityManager->remove($file);
        $this->entityManager->flush();
        
        $reponseAsArray = [
            'message' => 'file supprimé',
            'id' => $id
        ];

        return $this->json($reponseAsArray);
    }


    private function getNotFoundResponse() {

        $responseArray = [
            'error' => true,
            'userMessage' => 'Ressource non trouvé',
            'internalMessage' => 'Ce document n\'existe pas',
        ];

        return $this->json($responseArray, Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
