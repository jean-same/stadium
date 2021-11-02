<?php

namespace App\Controller\Api\V1\BackOffice\SuperAdmin;

use App\Entity\Account;
use App\Repository\AccountRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

//* BREAD Accounts

/**
 * @Route("/api/v1/backoffice/superadmin/accounts", name="api_v1_backoffice_superadmin_accounts_")
 */
class AccountsController extends AbstractController
{
    protected $accountRepository;
    protected $serializer;
    protected $validator;
    protected $entityManager;
    protected $passwordHasher;

    public function __construct(AccountRepository $accountRepository, SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        $this->accountRepository = $accountRepository;
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }
    /**
     * @Route("/", name="browse", methods={"GET"})
     */
    public function browse(): Response
    {
        $allAccounts = $this->accountRepository->findAll();
        return $this->json($allAccounts, Response::HTTP_OK, [], ['groups' => 'api_backoffice_superadmin_accounts_browse']);
    }

    /**
     * @Route("/{id}", name="read", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function read(int $id): Response
    {
        $account = $this->accountRepository->find($id);
        // If an association is null, then we return an error message in JSON with the method getNotFoundResponse
        if (is_null($account)) {
            return $this->getNotFoundResponse();
        }
        // Else we display the answer found
        return $this->json($account, Response::HTTP_OK, [], ['groups' => 'api_backoffice_superadmin_accounts_browse']);
    }

    /**
     * @Route("/{id}", name="edit", methods={"PATCH"}, requirements={"id"="\d+"})
     */
    public function edit(int $id, Request $request): Response
    {
        $account = $this->accountRepository->find($id);

        if (is_null($account)) {
            return $this->getNotFoundResponse();
        }

        // Retrieving the client's JSON
        $jsonContent = $request->getContent();

        $this->serializer->deserialize($jsonContent, Account::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $account]);

        /*$clearPassword = $account->getPassword();
        $hashedPassord = $this->passwordHasher->hashPassword($account, $clearPassword);
        $account->setPassword($hashedPassord);*/

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
            'password' => 'Password mis à jour',
            'roles' => $account->getRoles()
        ];
        return $this->json($responseAsArray, Response::HTTP_OK);
    }

    /**
     * @Route("", name="add", methods={"POST"})
     */
    public function add(Request $request): Response
    {
        $jsonContent = $request->getContent();
        // dd($jsonContent);

        $account = $this->serializer->deserialize($jsonContent, Account::class, 'json');

        /*$clearPassword = $account->getPassword();
        $hashedPassord = $this->passwordHasher->hashPassword($account, $clearPassword);
        $account->setPassword($hashedPassord);*/


        $errors = $this->validator->validate($account);

        if (count($errors) > 0) {
            $responseAsArray = [
                'error' => true,
                'message' => $errors
            ];
            return $this->json($responseAsArray, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $this->entityManager->persist($account);
        $this->entityManager->flush();

        $responseAsArray = [
            'message' => 'Compte cré',
            'email' => $account->getEmail(),
            //'password' => $account->getPassword(),
            'roles' => $account->getRoles(),
        ];
        return $this->json($responseAsArray, Response::HTTP_CREATED);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"}, requirements={"id"="\d+"})
     */
    public function delete(int $id): Response
    {
        $account = $this->accountRepository->find($id);

        if (is_null($account)) {
            return $this->getNotFoundResponse();
        }

        $this->entityManager->remove($account);
        $this->entityManager->flush();

        $responseAsArray = [
            'message' => 'Compte supprimé',
            'email' => $account->getEmail()
        ];
        return $this->json($responseAsArray);
    }


    private function getNotFoundResponse()
    {
        $responseArray = [
            'error' => true,
            'userMessage' => 'Ressource non trouvé',
            'internalMessage' => 'Ce compte n\'existe pas dans la BDD',
        ];

        return $this->json($responseArray, Response::HTTP_GONE);
    }
}
