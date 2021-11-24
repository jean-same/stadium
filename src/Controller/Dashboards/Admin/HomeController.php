<?php

namespace App\Controller\Dashboards\Admin;

use App\Repository\AssociationRepository;
use App\Service\Admin\AssociationServices;
use Symfony\Component\Security\Core\Security;
use App\Service\General\ChartGeneratorService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/dashboards/admin', name: 'dashboards_admin_')]
class HomeController extends AbstractController
{

    private $security;
    private $associationServices;
    private $associationRepository;
    private $chartGeneratorService;

    public function __construct(Security $security, AssociationRepository $associationRepository, ChartGeneratorService $chartGeneratorService, AssociationServices $associationServices)
    {
        $this->security = $security;
        $this->associationServices = $associationServices;
        $this->associationRepository = $associationRepository;
        $this->chartGeneratorService = $chartGeneratorService;
    }

    #[Route('/', name: 'home')]
    public function index(): Response
    {
        $association = $this->associationServices->getAssocFromUser();

        $profiles = $association->getProfils();
        $dataMonth = $this->chartGeneratorService->monthInitialize();

        foreach ($profiles as $profil) {
            if ($profil->getJoinedAssocAt()) {
                $joinedAt = $profil->getJoinedAssocAt();
                $joinedAtMonth = $joinedAt->format('M');

                $dataMonth[$joinedAtMonth]++;
            }
        }
        $chart = $this->chartGeneratorService->generateChart($dataMonth, "Adherent");
        return $this->render('dashboards/admin/home/index.html.twig', compact('association', 'chart'));
    }
}
