<?php

namespace App\Controller\Dashboards\Admin;

use App\Form\ProfilType;
use App\Repository\ProfilRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\Admin\AssociationServices;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[Route('/dashboards/admin/profiles', name: 'dashboards_admin_profiles_')]
class ProfilesController extends AbstractController
{
    private $em;
    private $flashy;
    private $profilRepository;
    private $associationServices;


    public function __construct(FlashyNotifier $flashy,  EntityManagerInterface $em, ProfilRepository $profilRepository, AssociationServices $associationServices)
    {
        $this->em = $em;
        $this->flashy = $flashy;
        $this->profilRepository = $profilRepository;
        $this->associationServices = $associationServices;
    }


    #[Route('/', name: 'profiles')]
    public function index(): Response
    {
        $profiles = $this->associationServices->getAdherentFromAssoc();

        return $this->render('dashboards/admin/profiles/profiles.html.twig', compact('profiles'));
    }

    #[Route('/edit/{slug}', name: 'edit')]
    public function edit($slug, Request $request)
    {
        $profil = $this->profilRepository->findOneBySlug(['slug' => $slug]);

        if (!$profil) {
            throw new NotFoundHttpException("Cet adhérent n'existe pas");
        }

        $editProfileForm = $this->createForm(ProfilType::class, $profil);

        $editProfileForm->handleRequest($request);

        if ($editProfileForm->isSubmitted() && $editProfileForm->isValid()) {
            $profilToEdit = $editProfileForm->getData();

            $profileToEditPicture = $editProfileForm->get('picture')->getData();

            if ($profileToEditPicture) {
                $pictureUploaded = $this->slugger->slug($profilToEdit->getlastName() . $profilToEdit->getfirstName() . '-' . uniqid()) . '.' . $profileToEditPicture->guessExtension();

                $profileToEditPicture->move(
                    //__DIR__ . '/../../../../public/pictures/profilPicture/',
                    $_SERVER['DOCUMENT_ROOT'] . '/pictures/profilPicture/',
                    $pictureUploaded
                );

                $profilToEdit->setPicture($pictureUploaded);
            }

            $this->em->flush();

            $this->flashy->success('Profil modifié avec success!');

            return $this->redirectToRoute('dashboards_admin_profiles_profiles');
        }

        $formProfil = $editProfileForm->createView();

        return $this->render('dashboards/admin/profiles/edit.html.twig', compact('formProfil'));
    }

    #[Route('/delete/{id}', name: 'delete')]
    public function delete($id)
    {
        $profil = $this->profilRepository->find($id);

        $this->em->remove($profil);

        $this->em->flush();

        $this->flashy->success('Adhérent supprimé avec succes');

        return $this->redirectToRoute('dashboards_admin_profiles_profiles');
    }
}
