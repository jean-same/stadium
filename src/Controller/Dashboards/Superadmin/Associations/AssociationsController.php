<?php

namespace App\Controller\Dashboards\Superadmin\Associations;

use App\Form\EventType;
use App\Form\ActivityType;
use App\Repository\ProfilRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\AssociationRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Service\General\ChartGeneratorService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
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
    private $slugger;
    private $em;

    public function __construct(AssociationRepository $associationRepository , ChartGeneratorService $chartGeneratorService, ProfilRepository $profilRepository, PaginatorInterface $paginator, EntityManagerInterface $em , SluggerInterface $slugger )
    {
        $this->associationRepository = $associationRepository;
        $this->chartGeneratorService = $chartGeneratorService;
        $this->profilRepository = $profilRepository;
        $this->paginator = $paginator;
        $this->slugger = $slugger;
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

            $activityPicture = $activityForm->get('picture')->getData();

            if($activityPicture) {
                $pictureUploaded = $this->slugger->slug($activity->getName() . '-' .uniqid()).'.' .$activityPicture->guessExtension();
                /*$test = __DIR__ . '/../../../../../public/pictures/activity/';
 
                dd($test);*/

                $activityPicture->move(
                    __DIR__ . '/../../../../../public/pictures/activity/',
                    $pictureUploaded
                );

                $activity->setPicture($pictureUploaded);
            }

            $this->em->persist($activity);
            $this->em->flush();
            
            $this->addFlash("success" , "Activité ajoutée avec success");

            return $this->redirect($_SERVER['HTTP_REFERER']);
        }

        //form event

        $eventForm = $this->createForm(EventType::class);
        $eventForm->handleRequest($request);

        if($eventForm->isSubmitted() && $eventForm->isValid()){
            $event = $eventForm->getData();
            $event->setAssociation($association);

            $this->em->persist($event);
            $this->em->flush();
            
            $this->addFlash("success" , "Evenement ajouté avec success");

            return $this->redirect($_SERVER['HTTP_REFERER']);
        }

        $formActivity = $activityForm->createView();
        $formEvent = $eventForm->createView();

        $chart = $this->chartGeneratorService->generateChart($dataMonth);

        return $this->render('dashboards/superadmin/associations/read.html.twig', compact('association', 'chart' , 'formActivity' , 'formEvent'));

    }

    /**
    * @Route("/profil/{id}", name="readOneProfil")
    */
    public function readOneProfil($id){
        $profil = $this->profilRepository->find($id);

        return $this->render('dashboards/superadmin/associations/readOneProfil.html.twig', compact('profil'));
    }

}
