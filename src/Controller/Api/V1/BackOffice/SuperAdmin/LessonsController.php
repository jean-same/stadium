<?php

namespace App\Controller\Api\V1\BackOffice\SuperAdmin;

use App\Entity\Lesson;
use App\Repository\ActivityRepository;
use App\Repository\LessonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
* @Route("/api/v1/backoffice/superadmin/lessons", name="api_v1_backoffice_superadmin_lessons")
*/
class LessonsController extends AbstractController
{


    protected $lessonRepository;
    protected $activityRepository;
    protected $validator;
    protected $serializer;
    protected $entityManager;

    public function __construct(ValidatorInterface $validator, LessonRepository $lessonRepository, ActivityRepository $activityRepository , SerializerInterface $serializer, EntityManagerInterface $entityManager)
    {
        $this->lessonRepository = $lessonRepository;
        $this->activityRepository = $activityRepository;
        $this->validator = $validator;
        $this->serializer = $serializer;
        $this->entityManager = $entityManager;
    }

    /**
    * @Route("/{order}", name="browse" , methods={"GET"}, priority=-1)
    */
    public function browse($order="asc"): Response
    {

        $lessons = $this->lessonRepository->findBy([], ["startTime"=> $order]);

        return $this->json($lessons, Response::HTTP_OK, [], ['groups' => "api_backoffice_superadmin_lessons_browse"]);
    }

        /**
     * @Route("/{id}", name="read", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function read($id): Response
    {
        $lesson = $this->lessonRepository->find($id);

        if (is_null($lesson)) {
            return $this->getNotFoundResponse();
        }

        return $this->json($lesson, Response::HTTP_OK, [], ['groups' => 'api_backoffice_superadmin_lessons_browse']);
    }

    /**
    * @Route("/{id}", name="edit", methods={"PATCH"}, requirements={"id"="\d+"})
    */
    public function edit(int $id, Request $request): Response
    {

        $lesson = $this->lessonRepository->find($id);

        if (is_null($lesson)) {
            return $this->getNotFoundResponse();
        }

        $jsonContent = $request->getContent();

        $this->serializer->deserialize($jsonContent, Lesson::class, 'json', [
            AbstractNormalizer::OBJECT_TO_POPULATE => $lesson
        ]);

        $errors = $this->validator->validate($lesson);

        if (count($errors) > 0) {
            $reponseAsArray = [
                'error' => true,
                'message' => $errors,
            ];

            return $this->json($reponseAsArray, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $this->entityManager->flush();

        $reponseAsArray = [
            'message' => 'Lesson mis à jour',
            'id' => $lesson->getId()
        ];

        return $this->json($reponseAsArray, Response::HTTP_CREATED);
    }

    /**
    * @Route("", name="add", methods={"POST"})
    */
    public function add(Request $request): Response
    {
        $jsonContent = $request->getContent();
        $lesson = $this->serializer->deserialize($jsonContent, Lesson::class, 'json');

        $errors = $this->validator->validate($lesson);

        if (count($errors) > 0) {
            $reponseAsArray = [
                'error' => true,
                'message' => $errors,
            ];

            return $this->json($reponseAsArray, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $this->entityManager->persist($lesson);
        $this->entityManager->flush();

        $reponseAsArray = [
            'message' => 'Lesson créé',
            'id' => $lesson->getId()
        ];

        return $this->json($reponseAsArray, Response::HTTP_CREATED);
    }


    /**
    * @Route("/{id}", name="delete", methods={"DELETE"}, requirements={"id"="\d+"})
    */
    public function delete(int $id): Response
    {
        $lesson = $this->lessonRepository->find($id);

        if (is_null($lesson)) {
            return $this->getNotFoundResponse();
        }

        $this->entityManager->remove($lesson);
        $this->entityManager->flush();
        
        $reponseAsArray = [
            'message' => 'Lesson supprimé',
            'id' => $id
        ];

        return $this->json($reponseAsArray);
    }

    private function getNotFoundResponse() {

        $responseArray = [
            'error' => true,
            'userMessage' => 'Ressource non trouvé',
            'internalMessage' => 'Cette lesson n\'existe pas',
        ];

        return $this->json($responseArray, Response::HTTP_GONE);
    }
}
