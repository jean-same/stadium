<?php

namespace App\Controller\Dashboards\Adherent;

use App\Form\ProfilType;
use App\Repository\AccountRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/dashboards/adherent', name: 'dashboards_adherent_')]
class HomeController extends AbstractController
{

    private $em;
    private $slugger;

    public function __construct(EntityManagerInterface $em, SluggerInterface $slugger)
    {
        $this->em = $em;
        $this->slugger = $slugger;
    }

    #[Route('/', name: 'home')]
    public function browse(Request $request): Response
    {
        $user = $this->getUser();

        $userForm = $this->createForm(ProfilType::class);

        $userForm->handleRequest($request);
        if ($userForm->isSubmitted() && $userForm->isValid()) {
            $newProfil = $userForm->getData();

            $newProfilPicture = $userForm->get('picture')->getData();

            if ($newProfilPicture) {
                $pictureUploaded = $this->slugger->slug($newProfil->getlastName() . $newProfil->getfirstName() . '-' . uniqid()) . '.' . $newProfilPicture->guessExtension();

                $newProfilPicture->move(
                    __DIR__ . '/../../../../public/pictures/profilPicture/',
                    $pictureUploaded
                );

                $newProfil->setPicture($pictureUploaded);
            } else {
                $newProfil->setPicture("random.jpg");
            }

            $newProfil->setAccount($user);
            $this->em->persist($newProfil);
            $this->em->flush();

            $this->addFlash("success", "Profil ajouté avec success");

            return $this->redirect($_SERVER['HTTP_REFERER']);
        }

        $formProfil = $userForm->createView();

        return $this->render('dashboards/adherent/home/index.html.twig', compact('user', 'formProfil'));
    }
}
