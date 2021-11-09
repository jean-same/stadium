<?php

namespace App\Controller\Api\V1\BackOffice\Admin;

use App\Entity\Association;
use App\Repository\AccountRepository;
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
 * @Route("/api/v1/backoffice/admin/association/{associationId}", name="api_v1_backoffice_admin_association")
 */
class AssociationController extends AbstractController
{

    protected $accountRepository;
    protected $associationRepository;
    protected $validator;
    protected $serializer;
    protected $entityManager;
    protected $associationServices;

    public function __construct(ValidatorInterface $validator, AccountRepository $accountRepository, AssociationRepository $associationRepository, SerializerInterface $serializer, EntityManagerInterface $entityManager, AssociationServices $associationServices)
    {
        $this->accountRepository        = $accountRepository;
        $this->associationRepository    = $associationRepository;
        $this->validator                = $validator;
        $this->serializer               = $serializer;
        $this->entityManager            = $entityManager;
        $this->associationServices      = $associationServices;
    }
    /**
    * @Route("/", name="browse", methods={"GET"})
    */
    public function browse($associationId): Response
    {

        $association = $this->associationServices->getAssocFromUser();

        return $this->json($association, Response::HTTP_OK, [], ['groups' => "api_backoffice_admin_association_browse"]);
    }

    /**
    * @Route("/", name="edit", methods={"PATCH"}, requirements={"id"="\d+"})
    */
    public function edit( $associationId , Request $request): Response
    {
        $association = $this->associationServices->getAssocFromUser();

        $jsonContent = $request->getContent();

        $this->serializer->deserialize($jsonContent, Association::class, 'json', [
            AbstractNormalizer::OBJECT_TO_POPULATE => $association
        ]);

        
        $errors = $this->validator->validate($association);
        
        if (count($errors) > 0) {
            $reponseAsArray = [
                'error' => true,
                'message' => $errors,
            ];

            return $this->json($reponseAsArray, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $this->entityManager->flush();

        $reponseAsArray = [
            'message' => 'Association mis à jour',
            'name' => $association->getName()
        ];

        return $this->json($reponseAsArray, Response::HTTP_CREATED);
    }
}
