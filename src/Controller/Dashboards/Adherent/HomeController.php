<?php

namespace App\Controller\Dashboards\Adherent;

use App\Form\ProfilType;
use App\Repository\AccountRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Service\Members\MembersProfilServices;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/dashboards/adherent', name: 'dashboards_adherent_')]
class HomeController extends AbstractController
{

    private $em;
    private $flashy;
    private $slugger;
    private $membersProfilServices;

    public function __construct(EntityManagerInterface $em, SluggerInterface $slugger , FlashyNotifier $flashy, MembersProfilServices $membersProfilServices)
    {
        $this->em = $em;
        $this->flashy = $flashy;
        $this->slugger = $slugger;
        $this->membersProfilServices = $membersProfilServices;
    }

    #[Route('/', name: 'home')]
    public function browse(Request $request): Response
    {
        $file = dirname(__FILE__, 4);
        $test = $_SERVER['DOCUMENT_ROOT'];

        /*dd( $test*/
        $user = $this->getUser(); 

        $userForm = $this->createForm(ProfilType::class);

        $userForm->handleRequest($request);
        if ($userForm->isSubmitted() && $userForm->isValid()) {
            $newProfil = $userForm->getData();

            $newProfilPicture = $userForm->get('picture')->getData();

            if ($newProfilPicture) {
                $pictureUploaded = $this->slugger->slug($newProfil->getlastName() . $newProfil->getfirstName() . '-' . uniqid()) . '.' . $newProfilPicture->guessExtension();

                $newProfilPicture->move(
                    //__DIR__ . '/../../../../public/pictures/profilPicture/',
                    $_SERVER['DOCUMENT_ROOT'] . '/pictures/profilPicture/',
                    $pictureUploaded
                );

                $newProfil->setPicture($pictureUploaded);
            } else {
                $newProfil->setPicture("random.jpg");
            }

            $newProfil->setAccount($user);
            $this->em->persist($newProfil);
            $this->em->flush();

            $this->flashy->success('Profil ajouté avec success!');
            //$this->addFlash("success", "Profil ajouté avec success");

            return $this->redirect($_SERVER['HTTP_REFERER']);
        }
        

        $formProfil = $userForm->createView();

        return $this->render('dashboards/adherent/index.html.twig', compact('user', 'formProfil'));
    }

    #[Route('/{slug}', name: 'read')]
    public function read($slug): Response
    {
        $profile = $this->membersProfilServices->getProfilFromUser($slug);

        return $this->render('dashboards/adherent/read.html.twig', compact('profile'));
    }
}
