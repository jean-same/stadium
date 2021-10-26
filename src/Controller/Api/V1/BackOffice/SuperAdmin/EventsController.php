<?php

namespace App\Controller\Api\V1\BackOffice\SuperAdmin;

use App\Entity\Event;
use App\Repository\AssociationRepository;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

    
/**
* @Route("/api/v1/back/office/super/admin/events", name="api_v1_back_office_super_admin_events")
*/
class EventsController extends AbstractController
{

    protected $eventsRepository;
    protected $validator;
    protected $serializer;
    protected $entityManager;
    protected $associationRepository;

    public function __construct(ValidatorInterface $validator, EventRepository $eventRepository, AssociationRepository $associationRepository, SerializerInterface $serializer, EntityManagerInterface $entityManager)
    {
        $this->eventsRepository = $eventRepository;
        $this->associationRepository = $associationRepository;
        $this->validator = $validator;
        $this->serializer = $serializer;
        $this->entityManager = $entityManager;
    }

    /**
    * @Route("", name="browse" , methods={"GET"})
    */
    public function browse(): Response
    {
        $events = $this->eventsRepository->findAll();

        return $this->json($events, Response::HTTP_OK, [], ['groups' => "event_browse"]);
    }

    /**
    * @Route("/{id}", name="read", methods={"GET"}, requirements={"id"="\d+"})
    */
    public function read($id): Response
    {
        $event = $this->eventsRepository->find($id);

        if (is_null($event)) {
            return $this->getNotFoundResponse();
        }

        return $this->json($event, Response::HTTP_OK, [], ['groups' => 'event_browse']);
    }


    /**
     * @Route("/{id}", name="edit", methods={"PATCH"}, requirements={"id"="\d+"})
     */
    public function edit(ValidatorInterface $validator, int $id, Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager): Response
    {
        
        $event = $this->eventsRepository->find($id);

        if (is_null($event)) {
            return $this->getNotFoundResponse();
        }
        
        $jsonContent = $request->getContent();

        $serializer->deserialize($jsonContent, Event::class, 'json', [
            AbstractNormalizer::OBJECT_TO_POPULATE => $event
        ]);

        
        $errors = $validator->validate($event);
        
        if (count($errors) > 0) {
            $reponseAsArray = [
                'error' => true,
                'message' => $errors,
            ];

            return $this->json($reponseAsArray, Response::HTTP_UNPROCESSABLE_ENTITY);
        }


        // lancer le flush
        $entityManager->persist($event);
        $entityManager->flush();

        $reponseAsArray = [
            'message' => 'Event mis à jour',
            'id' => $event->getId()
        ];

        return $this->json($reponseAsArray, Response::HTTP_CREATED);
    }


    /**
     * @Route("", name="add", methods={"POST"})
     */
    public function add(Request $request, SerializerInterface $serializer): Response
    {
        $jsonContent = $request->getContent();
        $event = $serializer->deserialize($jsonContent, Event::class, 'json');

        //dd($event);
        $association = $this->associationRepository->find(4);

        $event->setAssociation($association);

        $errors = $this->validator->validate($event);

        if (count($errors) > 0) {
            $reponseAsArray = [
                'error' => true,
                'message' => $errors,
            ];

            return $this->json($reponseAsArray, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $this->entityManager->persist($event);
        $this->entityManager->flush();

        $reponseAsArray = [
            'message' => 'Event créé',
            'id' => $event->getId()
        ];

        return $this->json($reponseAsArray, Response::HTTP_CREATED);
    }


    /**
     * @Route("/{id}", name="delete", methods={"DELETE"}, requirements={"id"="\d+"})
     */
    public function delete(int $id): Response
    {
        $event = $this->eventsRepository->find($id);

        if (is_null($event)) {
            return $this->getNotFoundResponse();
        }

        $this->entityManager->remove($event);
        $this->entityManager->flush();
        
        $reponseAsArray = [
            'message' => 'Event supprimé',
            'id' => $id
        ];

        return $this->json($reponseAsArray);
    }

    private function getNotFoundResponse() {

        $responseArray = [
            'error' => true,
            'userMessage' => 'Ressource non trouvé',
            'internalMessage' => 'Cet evenement n\'existe pas',
        ];

        return $this->json($responseArray, Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
