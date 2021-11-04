<?php

namespace App\Controller\Api\V1\Member;

use App\Entity\Account;
use App\Repository\AccountRepository;
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
 * @Route("/api/v1/member/profil/{profilId}/account", name="api_v1_member_account")
 */
class AccountController extends AbstractController
{
    private $profilRepository;
    private $accountRepository;
    private $serializer;
    private $validator;
    private $entityManager;


    public function __construct(ProfilRepository $profilRepository, AccountRepository $accountRepository, SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $entityManager)
    {
        $this->profilRepository = $profilRepository;
        $this->accountRepository = $accountRepository;
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->entityManager = $entityManager;
    }
    /**
     * @Route("/", name="read", methods={"GET"})
     */
    public function read($profilId): Response
    {
        $profil = $this->profilRepository->find($profilId);

        $this->denyAccessUnlessGranted('CAN_READ', $profil, "Accès interdit");
        $account = $profil->getAccount();

        return $this->json($account, Response::HTTP_OK, [], ['groups' => "api_member_account_read"]);
    }

    /**
     * @Route("/", name="edit", methods={"PATCH"})
     */
    public function edit($profilId, Request $request): Response
    {
        $profil = $this->profilRepository->find($profilId);

        $this->denyAccessUnlessGranted('CAN_READ', $profil, "Accès interdit");
        $account = $profil->getAccount();

        //dd($account);
        $jsonContent = $request->getContent();
        $this->serializer->deserialize($jsonContent, Account::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $account]);

        $errors = $this->validator->validate($account);

        if (count($errors) > 0) {
            $responseAsArray = [
                'error' => true,
                'message' => $errors
            ];
            return $this->json($responseAsArray, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $this->entityManager->flush();

        $responseAsArray = [
            'message' => 'Compte mis à jour',
            'email' => $account->getEmail(),
        ];
        return $this->json($responseAsArray, Response::HTTP_OK);
    }
}
