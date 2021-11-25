<?php

namespace App\Controller\Dashboards\Adherent;

use App\Form\ProfilModifyType;
use App\Form\ProfilType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Service\Members\MembersProfilServices;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/dashboards/adherent/{slug}', name: 'dashboards_adherent_')]
class ProfilesController extends AbstractController
{

    private $em;
    private $slugger;
    private $membersProfilServices;

    public function __construct(EntityManagerInterface $em, SluggerInterface $slugger, MembersProfilServices $membersProfilServices)
    {
        $this->em = $em;
        $this->slugger = $slugger;
        $this->membersProfilServices = $membersProfilServices;
    }

    #[Route('/edit', name: 'edit')]
    public function edit($slug, Request $request): Response
    {
        $profile = $this->membersProfilServices->getProfilFromUser($slug);

        $this->denyAccessUnlessGranted('CAN_READ', $profile, "Accès interdit");

        $editProfileForm = $this->createForm(ProfilModifyType::class, $profile);

        $editProfileForm->handleRequest($request);

        if ($editProfileForm->isSubmitted() && $editProfileForm->isValid()) {

            $this->em->flush();
            $this->addFlash("success", "Profil modifié avec success");

            return $this->redirectToRoute('dashboards_adherent_home');
        }

        $formProfil = $editProfileForm->createView();

        return $this->render('dashboards/adherent/profiles/edit.html.twig', compact('formProfil'));
    }

    #[Route('/delete', name: 'delete')]
    public function delete($slug): Response
    {
        $profile = $this->membersProfilServices->getProfilFromUser($slug);

        $this->em->remove($profile);
        $this->em->flush();
        $this->addFlash("success", "Profil supprimé avec success");

        return $this->redirect($_SERVER['HTTP_REFERER']);
    }
}
