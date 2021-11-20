<?php

namespace App\Controller\Dashboards\Superadmin\Associations;

use App\Repository\ProfilRepository;
use App\Repository\AssociationRepository;
use Knp\Component\Pager\PaginatorInterface;
use App\Service\General\ChartGeneratorService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

/**
* @Route("/dashboards/superadmin/associations", name="dashboard_superadmin_associations_")
*/
class AssociationsController extends AbstractController
{

    private $associationRepository;
    private $chartGeneratorService;
    private $profilRepository;
    private $paginator;

    public function __construct(AssociationRepository $associationRepository , ChartGeneratorService $chartGeneratorService, ProfilRepository $profilRepository, PaginatorInterface $paginator)
    {
        $this->associationRepository = $associationRepository;
        $this->chartGeneratorService = $chartGeneratorService;
        $this->profilRepository = $profilRepository;
        $this->paginator = $paginator;
    }
    
    /**
     * @Route("/", name="browse")
     */
    public function browse(Request $request): Response
    {

        //$associations = $this->associationRepository->findAll();

        $associations = $this->paginator->paginate(
            $this->associationRepository->findAll(),
            $request->query->getInt('page', 1),
            8
        );

        /*
        foreach ($associations as $association) {
            foreach ($association->getProfils() as $profil) {
                $account = $profil->getAccount();
                if (in_array("ROLE_ADHERENT", $account->getRoles())) {
                    $adherents++;
                }

                if (in_array("ROLE_ADMIN", $account->getRoles())) {
                    $admins++;
                }
            }
        }
        */
        return $this->render('dashboards/superadmin/associations/index.html.twig',compact('associations'));
    }
    
    /**
    * @Route("/{id}", name="read")
    */
    public function read($id) {
        $association = $this->associationRepository->find($id);

        $profiles = $association->getProfils();
        $dataMonth = $this->chartGeneratorService->monthInitialize();

        foreach ($profiles as $profil) {
            if ($profil->getJoinedAssocAt()) {
                $joinedAt = $profil->getJoinedAssocAt();
                $joinedAtMonth = $joinedAt->format('M');

                $dataMonth[$joinedAtMonth]++;
            }
        }

        $chart = $this->chartGeneratorService->generateChart($dataMonth);

        return $this->render('dashboards/superadmin/associations/read.html.twig', compact('association', 'chart'));

    }

    /**
    * @Route("/profil/{id}", name="readOneProfil")
    */
    public function readOneProfil($id){
        $profil = $this->profilRepository->find($id);

        return $this->render('dashboards/superadmin/associations/readOneProfil.html.twig', compact('profil'));
    }
}
