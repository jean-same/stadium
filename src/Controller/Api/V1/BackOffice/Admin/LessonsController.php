<?php

namespace App\Controller\Api\V1\BackOffice\Admin;

use App\Entity\Lesson;
use App\Repository\LessonRepository;
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
 * @Route("/api/v1/backoffice/admin/association/{associationId}/lessons", name="api_v1_back_office_admin_association_lessons_")
 */
class LessonsController extends AbstractController
{
    protected $associationRepository;
    protected $lessonRepository;
    protected $validator;
    protected $serializer;
    protected $entityManager;
    protected $associationServices;

    public function __construct(AssociationRepository $associationRepository, LessonRepository $lessonRepository, ValidatorInterface $validator, SerializerInterface $serializer, EntityManagerInterface $entityManager,  AssociationServices $associationServices)
    {
        $this->associationRepository = $associationRepository;
        $this->lessonRepository = $lessonRepository;;
        $this->validator = $validator;
        $this->serializer = $serializer;
        $this->entityManager = $entityManager;
        $this->associationServices = $associationServices;
    }
    /**
     * @Route("/", name="browse", methods={"GET"})
     */
    public function browse(int $associationId): Response
    {
        $association = $this->associationServices->getAssocFromUser();

        $activitiesAssociation = $association->getActivities();

        foreach ($activitiesAssociation as $activities) {
            $listLessons[] = $activities->getLessons();
        }


        return $this->json($listLessons, Response::HTTP_OK, [], ['groups' => 'api_backoffice_admin_association_lessons_browse']);
    }

    /**
     * @Route("/{lessonId}", name="read", methods={"GET"}, requirements={"lessonId"="\d+"})
     */
    public function read(int $associationId, int $lessonId): Response
    {
        $lesson = $this->lessonRepository->find($lessonId);

        if (is_null($lesson)) {
            return $this->getNotFoundResponse();
        }

        $match = $this->associationServices->checkAssocMatch($lesson);

        if (!$match) {
            return $this->json("Accès interdit", Response::HTTP_FORBIDDEN);
        }


        return $this->json($lesson, Response::HTTP_OK, [], ['groups' => 'api_backoffice_admin_association_lessons_browse']);
    }

    /**
     * @Route("/{lessonId}", name="edit", methods={"PATCH"}, requirements={"lessonId"="\d+"})
     */
    public function edit(int $associationId, int $lessonId, Request $request): Response
    {
        $lesson = $this->lessonRepository->find($lessonId);

        if (is_null($lesson)) {
            return $this->getNotFoundResponse();
        }

        $match = $this->associationServices->checkAssocMatch($lesson);

        if (!$match) {
            return $this->json("Accès interdit", Response::HTTP_FORBIDDEN);
        }

        $jsonContent = $request->getContent();

        $this->serializer->deserialize($jsonContent, Lesson::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $lesson]);

        $errors = $this->validator->validate($lesson);

        if (count($errors) > 0) {
            $responseAsArray = [
                'error' => true,
                'message' => $errors
            ];
            return $this->json($responseAsArray, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $this->entityManager->flush();

        $responseAsArray = [
            'message' => 'Lesson mis à jour',
            'level' => $lesson->getLevel(),
            'place' => $lesson->getPlace(),
        ];
        return $this->json($responseAsArray, Response::HTTP_OK);
    }

    /**
     * @Route("/", name="add", methods={"POST"})
     */
    public function add(Request $request, int $associationId): Response
    {
        $jsonContent = $request->getContent();

        $lesson = $this->serializer->deserialize($jsonContent, Lesson::class, 'json');

        if ($lesson->getActivity()->getAssociation()->getId() != $associationId) {
            return $this->json("Accès interdit", Response::HTTP_FORBIDDEN);
        }
        $errors = $this->validator->validate($lesson);

        if (count($errors) > 0) {
            $responseAsArray = [
                'error' => true,
                'message' => $errors
            ];
            return $this->json($responseAsArray, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $this->entityManager->persist($lesson);
        $this->entityManager->flush();

        $responseAsArray = [
            'message' => 'Lesson crée',
            'level' => $lesson->getLevel(),
            'startTime' => $lesson->getStartTime(),
            'endTime' => $lesson->getEndTime(),
            'day' => $lesson->getDay(),
            'place' => $lesson->getPlace()
        ];
        return $this->json($responseAsArray, Response::HTTP_CREATED);
    }

    /**
     * @Route("/{lessonId}", name="delete", methods={"DELETE"}, requirements={"lessonId"="\d+"})
     */
    public function delete(int $lessonId, int $associationId): Response
    {
        $lesson = $this->lessonRepository->find($lessonId);

        if (is_null($lesson)) {
            return $this->getNotFoundResponse();
        }

        $match = $this->associationServices->checkAssocMatch($lesson);

        if (!$match) {
            return $this->json("Accès interdit", Response::HTTP_FORBIDDEN);
        }

        $this->entityManager->remove($lesson);
        $this->entityManager->flush();

        $responseAsArray = [
            'message' => 'Lesson supprimé',
            'level' => $lesson->getLevel(),
            'activity' => $lesson->getActivity()->getName()
        ];
        return $this->json($responseAsArray);
    }

    private function getNotFoundResponse()
    {

        $responseArray = [
            'error' => true,
            'userMessage' => 'Ressource non trouvé',
            'internalMessage' => 'Cette lesson n\'existe pas',
        ];

        return $this->json($responseArray, Response::HTTP_GONE);
    }
}
