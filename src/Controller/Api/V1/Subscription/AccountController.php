<?php

namespace App\Controller\Api\V1\Subscription;

use App\Entity\Account;
use App\Entity\Association;
use App\Repository\AccountRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
* @Route("/api/v1/subscription/account", name="api_v1_subscription_account")
*/
class AccountController extends AbstractController
{
    protected $serializer;
    protected $validator;
    protected $entityManager;
    protected $passwordHasher;
    protected $accountRepository;

    public function __construct(AccountRepository $accountRepository,SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher )
    {
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
        $this->accountRepository = $accountRepository;
    }

    /**
    * @Route("/new", name="new", methods={"POST"})
    */
    public function addAccount(Request $request): Response
    {
        $jsonContent = $request->getContent();

        $account = $this->serializer->deserialize($jsonContent, Account::class, 'json');
        $errors = $this->validator->validate($account);
 
        $roleAccount = json_decode($jsonContent)->role;

        if($roleAccount ==  "ADHERENT"){
            $account->setRoles(["ROLE_ADHERENT"]);
        }

        if($roleAccount ==  "ASSOC"){
            $account->setRoles(["ROLE_ASSOC"]);
        }

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
            'message' => 'Compte crée',
            'id' => $account->getId(),
            'email' => $account->getEmail(),
            'roles' => $account->getRoles(),
        ];
        return $this->json($responseAsArray, Response::HTTP_CREATED);

        return $this->render('api/v1/subscription/account/index.html.twig', [
            'controller_name' => 'AccountController',
        ]);
    }

    /**
    * @Route("/association/infos", name="", methods={"POST"})
    */
    public function addAssocInfos(Request $request): Response
    {
        $jsonContent = $request->getContent();
        //dd($jsonContent);

        $association = $this->serializer->deserialize($jsonContent, Association::class, 'json');

        $errors = $this->validator->validate($association);

        if (count($errors) > 0) {
            $responseAsArray = [
                'error' => true,
                'message' => $errors
            ];
            return $this->json($responseAsArray, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $this->entityManager->persist($association);
        $this->entityManager->flush();

        $responseAsArray = [
            'message' => 'Association crée',
            'id' => $association->getId(),
            'name' => $association->getName(),
            'presidentLastName' => $association->getPresidentLastName(),
            'presidentFirstName' => $association->getPresidentFirstName(),
            'address' => $association->getAddress(),
            'phoneNumber' => $association->getPhoneNumber()
        ];
        return $this->json($responseAsArray, Response::HTTP_CREATED);
    }
}
