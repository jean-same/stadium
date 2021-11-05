<?php

namespace App\Controller\Api\V1\BackOffice\SuperAdmin;

use App\Entity\Profil;
use App\Repository\AccountRepository;
use App\Repository\AssociationRepository;
use App\Repository\ProfilRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

//* BREAD Profiles

/**
* @Route("/api/v1/backoffice/superadmin/profiles", name="api_v1_backoffice_superadmin_profiles")
*/
class ProfilesController extends AbstractController
{

    protected $profilRepository;
    protected $accountRepository;
    protected $associationRepository;
    protected $validator;
    protected $serializer;
    protected $entityManager;

    public function __construct(ValidatorInterface $validator, ProfilRepository $profilRepository, AccountRepository $accountRepository,AssociationRepository $associationRepository , SerializerInterface $serializer, EntityManagerInterface $entityManager)
    {
        $this->profilRepository         = $profilRepository;
        $this->accountRepository        = $accountRepository;
        $this->associationRepository    = $associationRepository;
        $this->validator                = $validator;
        $this->serializer               = $serializer;
        $this->entityManager            = $entityManager;
    }

    /**
    * @Route("/{order}", name="browse" , methods={"GET"}, priority=-1)
    */
    public function browse($order = "asc"): Response
    {
        $profiles = $this->profilRepository->findBy([] , ["lastName" => $order]);

        return $this->json($profiles, Response::HTTP_OK, [], ['groups' => "api_backoffice_superadmin_profiles_browse"]);
    }

    /**
    * @Route("/{id}", name="read", methods={"GET"}, requirements={"id"="\d+"})
    */
    public function read($id): Response
    {
        $profil = $this->profilRepository->find($id);

        if (is_null($profil)) {
            return $this->getNotFoundResponse();
        }

        return $this->json($profil, Response::HTTP_OK, [], ['groups' => 'api_backoffice_superadmin_profiles_browse']);
    }

    /**
    * @Route("/{id}", name="edit", methods={"PATCH"}, requirements={"id"="\d+"})
    */
    public function edit(int $id, Request $request): Response
    {

        $profil = $this->profilRepository->find($id);

        if (is_null($profil)) {
            return $this->getNotFoundResponse();
        }

        $jsonContent = $request->getContent();

        $this->serializer->deserialize($jsonContent, Profil::class, 'json', [
            AbstractNormalizer::OBJECT_TO_POPULATE => $profil
        ]);

        $errors = $this->validator->validate($profil);

        if (count($errors) > 0) {
            $reponseAsArray = [
                'error' => true,
                'message' => $errors,
            ];

            return $this->json($reponseAsArray, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $this->entityManager->flush();

        $reponseAsArray = [
            'message' => 'Profil mis à jour',
            'firstname' => $profil->getFirstName(),
            'lastname' => $profil->getLastName()
        ];

        return $this->json($reponseAsArray, Response::HTTP_CREATED);
    }


    /**
     * @Route("", name="add", methods={"POST"})
     */
    public function add(Request $request): Response
    {
        $jsonContent = $request->getContent();
        $profil = $this->serializer->deserialize($jsonContent, Profil::class, 'json');
        
        $accountId = json_decode($jsonContent)->accountId;
        $account = $this->accountRepository->find($accountId);

        $accountRoles = $account->getRoles();

        $notAnAdherent = null;
        foreach($accountRoles as $role){
            if($role == "ROLE_ASSOC"){
                $notAnAdherent = "This account can't create a profil";
            }
        }

        if ($notAnAdherent) {
            $reponseAsArray = [
                'error' => true,
                'message' => $notAnAdherent,
            ];

            return $this->json($reponseAsArray, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $profil->setAccount($account);
        
        $errors = $this->validator->validate($profil);

        if (count($errors) > 0) {
            $reponseAsArray = [
                'error' => true,
                'message' => $errors,
            ];

            return $this->json($reponseAsArray, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $this->entityManager->persist($profil);
        $this->entityManager->flush();

        $reponseAsArray = [
            'message' => 'Profil créé',
            'firstname' => $profil->getFirstName(),
            'lastname' => $profil->getLastName()
        ];

        return $this->json($reponseAsArray, Response::HTTP_CREATED);
    }

    /**
    * @Route("/{id}", name="delete", methods={"DELETE"}, requirements={"id"="\d+"})
    */
    public function delete(int $id): Response
    {
        $profil = $this->profilRepository->find($id);

        if (is_null($profil)) {
            return $this->getNotFoundResponse();
        }

        $this->entityManager->remove($profil);
        $this->entityManager->flush();
        
        $reponseAsArray = [
            'message' => 'Profil supprimé',
            'id' => $id
        ];

        return $this->json($reponseAsArray);
    }


    private function getNotFoundResponse() {

        $responseArray = [
            'error' => true,
            'userMessage' => 'Ressource non trouvé',
            'internalMessage' => 'Ce profil n\'existe pas',
        ];

        return $this->json($responseArray, Response::HTTP_GONE);
    }
}
