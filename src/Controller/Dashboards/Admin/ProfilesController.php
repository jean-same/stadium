<?php

namespace App\Controller\Dashboards\Admin;

use App\Repository\ProfilRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\Admin\AssociationServices;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/dashboards/admin/profiles', name: 'dashboards_admin_profiles_')]
class ProfilesController extends AbstractController
{
    private $em;
    private $flashy;
    private $profilRepository;
    private $associationServices;


    public function __construct(FlashyNotifier $flashy,  EntityManagerInterface $em, ProfilRepository $profilRepository , AssociationServices $associationServices)
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

    #[Route('/delete/{id}', name: 'delete')]
    public function delete($id){
        $profil = $this->profilRepository->find($id);

        $this->em->remove($profil);

        $this->em->flush();

        $this->flashy->success('Adhérent supprimé avec succes');

        return $this->redirectToRoute('dashboards_admin_profiles_profiles');
    }
}
