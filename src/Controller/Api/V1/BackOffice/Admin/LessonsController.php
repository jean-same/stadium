<?php

namespace App\Controller\Api\V1\BackOffice\Admin;

use App\Repository\AssociationRepository;
use App\Repository\LessonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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

    public function __construct(AssociationRepository $associationRepository, LessonRepository $lessonRepository, ValidatorInterface $validator, SerializerInterface $serializer, EntityManagerInterface $entityManager)
    {
        $this->associationRepository = $associationRepository;
        $this->lessonRepository = $lessonRepository;;
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

        $activitiesAssociation = $association->getActivities();
        //dd($activities);
        foreach($activitiesAssociation as $activities)
        {
            $listLessons[] = $activities->getLessons();
        }
        return $this->json($listLessons, Response::HTTP_OK, [], ['groups'=> 'api_backoffice_admin_association_lessons_browse']);

    }

    /**
     * @Route("/{lessonId"}, name="read", methods={"GET})
     */
}
