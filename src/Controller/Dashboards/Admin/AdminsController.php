<?php

namespace App\Controller\Dashboards\Admin;

use App\Entity\Account;
use App\Entity\Profil;
use App\Form\ProfilType;
use App\Security\EmailVerifier;
use App\Form\RegistrationFormType;
use App\Repository\AccountRepository;
use Symfony\Component\Mime\Address;
use App\Repository\ProfilRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\Admin\AssociationServices;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/dashboards/admin/admins', name: 'dashboards_admin_admins_')]
class AdminsController extends AbstractController
{

    private $em;
    private $flashy;
    private $session;
    private $profilRepository;
    private $accountRepository;
    private $associationServices;
    private $userPasswordHasher;
    private EmailVerifier $emailVerifier;


    public function __construct(EmailVerifier $emailVerifier, AccountRepository $accountRepository,  SessionInterface $session, FlashyNotifier $flashy, UserPasswordHasherInterface $userPasswordHasher,  EntityManagerInterface $em, ProfilRepository $profilRepository, AssociationServices $associationServices)
    {
        $this->em = $em;
        $this->flashy = $flashy;
        $this->session = $session;
        $this->emailVerifier = $emailVerifier;
        $this->accountRepository = $accountRepository;
        $this->profilRepository = $profilRepository;
        $this->associationServices = $associationServices;
        $this->userPasswordHasher = $userPasswordHasher;
    }

    #[Route('/', name: 'browse')]
    public function index(): Response
    {
        $admins = $this->associationServices->getAdminFromAssoc();

        return $this->render('dashboards/admin/admins/admins.html.twig', compact('admins'));
    }

    #[Route('/edit/{slug}', name: 'edit')]
    public function edit($slug, Request $request)
    {
        $admin = $this->profilRepository->findOneBySlug(['slug' => $slug]);

        if (!$admin) {
            throw new NotFoundHttpException("Cet admin n'existe pas");
        }

        $editAdminForm = $this->createForm(ProfilType::class, $admin);

        $editAdminForm->handleRequest($request);

        if ($editAdminForm->isSubmitted() && $editAdminForm->isValid()) {
            $adminToEdit = $editAdminForm->getData();

            $adminToEditPicture = $editAdminForm->get('picture')->getData();

            if ($adminToEditPicture) {
                $pictureUploaded = $this->slugger->slug($adminToEdit->getlastName() . $adminToEdit->getfirstName() . '-' . uniqid()) . '.' . $adminToEditPicture->guessExtension();

                $adminToEditPicture->move(
                    //__DIR__ . '/../../../../public/pictures/profilPicture/',
                    $_SERVER['DOCUMENT_ROOT'] . '/pictures/admin/',
                    $pictureUploaded
                );

                $adminToEdit->setPicture($pictureUploaded);
            }

            $this->em->flush();

            $this->flashy->success('Admin modifié avec success!');

            return $this->redirectToRoute('dashboards_admin_admins_browse');
        }

        $formAdmin = $editAdminForm->createView();

        return $this->render('dashboards/admin/admins/edit.html.twig', compact('formAdmin'));
    }

    #[Route('/add', name: 'add')]
    public function add(Request $request)
    {
        $user = new Account();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $user->setRoles(["ROLE_ADMIN"]);

            $user->setPassword(
                $this->userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $this->em->persist($user);
            $this->em->flush();

            // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation(
                'app_verify_email',
                $user,
                (new TemplatedEmail())
                    ->from(new Address('stadiumplatform@gmail.com', 'Stadium Bot'))
                    ->to($user->getEmail())
                    ->subject('Veuillez confirmer votre email')
                    ->htmlTemplate('dashboards/admin/admins/confirmation_email.html.twig')
            );
            // do anything else you need here, like send an email
            $this->flashy->success('Le compte a été crée avec succes!');

            return $this->redirectToRoute('dashboards_admin_admins_new_profile', ['accountId' => $user->getId()]);
        }

        return $this->render('dashboards/admin/admins/add.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/{accountId}/new/profile', name: 'new_profile')]
    public function addNewAdminProfil($accountId, Request $request)
    {

        $profil = new Profil();
        $profilForm = $this->createForm(ProfilType::class, $profil);

        $profilForm->handleRequest($request);

        if ($profilForm->isSubmitted() && $profilForm->isValid()) {

            $account = $this->accountRepository->find($accountId);
            $association = $this->associationServices->getAssocFromUser();
            $profil->setAccount($account);
            $profil->setAssociation($association);


            $newAdminPicture = $profilForm->get('picture')->getData();

            if ($newAdminPicture) {
                $pictureUploaded = $this->slugger->slug($profil->getlastName() . $profil->getfirstName() . '-' . uniqid()) . '.' . $newAdminPicture->guessExtension();

                $newAdminPicture->move(
                    $_SERVER['DOCUMENT_ROOT'] . '/pictures/admin/',
                    $pictureUploaded
                );

                $profil->setPicture($pictureUploaded);
            } else {
                $profil->setPicture("random.jpg");
            }

            $this->em->persist($profil);
            $this->em->flush();

            $this->flashy->success('Profil crée avec succès');
            return $this->redirectToRoute('dashboards_admin_admins_browse');
        }

        $formNewAdminProfil = $profilForm->createView();

        return $this->render('dashboards/admin/admins/newAdminProfil.html.twig', compact('formNewAdminProfil'));
    }

    #[Route('/delete/{id}', name: 'delete')]
    public function delete($id)
    {
        $admin = $this->profilRepository->find($id);

        if (!$admin) {
            throw new NotFoundHttpException("Cet admin n'existe pas");
        }

        $accountId = $admin->getAccount()->getId();

        $account = $this->accountRepository->find($accountId);

        $this->em->remove($admin);
        $this->em->remove($account);

        $this->em->flush();

        $this->flashy->success('Admin supprimé avec succes');

        return $this->redirectToRoute('dashboards_admin_admins_browse');
    }
}
