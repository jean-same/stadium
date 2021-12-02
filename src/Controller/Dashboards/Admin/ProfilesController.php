<?php

namespace App\Controller\Dashboards\Admin;

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
    private $associationServices;


    public function __construct(FlashyNotifier $flashy,  EntityManagerInterface $em, AssociationServices $associationServices)
    {
        $this->em = $em;
        $this->flashy = $flashy;
        $this->associationServices = $associationServices;
    }


    #[Route('/', name: 'profiles')]
    public function index(): Response
    {
        $profiles = $this->associationServices->getAdherentFromAssoc();

        return $this->render('dashboards/admin/profiles/profiles.html.twig', compact('profiles'));
    }
}
