<?php

namespace App\Controller\Dashboards\Superadmin\Associations;

use App\Form\ActivityType;
use App\Repository\ProfilRepository;
use App\Repository\AssociationRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Service\General\ChartGeneratorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
* @Route("/dashboards/superadmin/associations", name="dashboard_superadmin_associations_")
*/
class AssociationsController extends AbstractController
{

    private $associationRepository;
    private $chartGeneratorService;
    private $profilRepository;
    private $paginator;
    private $em;

    public function __construct(AssociationRepository $associationRepository , ChartGeneratorService $chartGeneratorService, ProfilRepository $profilRepository, PaginatorInterface $paginator, EntityManagerInterface $em)
    {
        $this->associationRepository = $associationRepository;
        $this->chartGeneratorService = $chartGeneratorService;
        $this->profilRepository = $profilRepository;
        $this->paginator = $paginator;
        $this->em = $em;
    }
    
    /**
     * @Route("/", name="browse")
     */
    public function browse(Request $request): Response
    {

        $associations = $this->paginator->paginate(
            $this->associationRepository->findAll(),
            $request->query->getInt('page', 1),
            8
        );

        return $this->render('dashboards/superadmin/associations/index.html.twig',compact('associations'));
    }
    
    /**
    * @Route("/{id}", name="read")
    */
    public function read($id, Request $request) {
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

        //form activity
        $activityForm = $this->createForm(ActivityType::class);

        $activityForm->handleRequest($request);

        if($activityForm->isSubmitted() && $activityForm->isValid() ){
            $activity = $activityForm->getData();
            $activity->setAssociation($association);

            $this->em->persist($activity);
            $this->em->flush();
            
            $this->addFlash("success" , "Activité ajoutée avec success");

            return $this->redirect($_SERVER['HTTP_REFERER']);
        }
    

        $form = $activityForm->createView();

        $chart = $this->chartGeneratorService->generateChart($dataMonth);

        return $this->render('dashboards/superadmin/associations/read.html.twig', compact('association', 'chart' , 'form'));

    }

    /**
    * @Route("/profil/{id}", name="readOneProfil")
    */
    public function readOneProfil($id){
        $profil = $this->profilRepository->find($id);

        return $this->render('dashboards/superadmin/associations/readOneProfil.html.twig', compact('profil'));
    }

}
