<?php

namespace App\Controller\Dashboards\Admin;

use App\Repository\ProfilRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\Admin\AssociationServices;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/dashboards/admin/admins', name: 'dashboards_admin_admins_')]
class AdminsController extends AbstractController
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

    #[Route('/', name: 'browse')]
    public function index(): Response
    {
        $admins = $this->associationServices->getAdminFromAssoc();

        return $this->render('dashboards/admin/admins/admins.html.twig', compact('admins'));
    }
}
