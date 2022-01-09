<?php

namespace App\Controller\Dashboards\Admin;

use App\Form\AssociationType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\AssociationRepository;
use App\Service\Admin\AssociationServices;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use App\Service\General\ChartGeneratorService;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/dashboards/admin', name: 'dashboards_admin_')]
class HomeController extends AbstractController
{
    private $em;
    private $flashy;
    private $slugger;
    private $security;
    private $associationServices;
    private $associationRepository;
    private $chartGeneratorService;

    public function __construct(Security $security, FlashyNotifier $flashy, SluggerInterface $slugger,  EntityManagerInterface $em, AssociationRepository $associationRepository, ChartGeneratorService $chartGeneratorService, AssociationServices $associationServices)
    {
        $this->em = $em;
        $this->flashy = $flashy;
        $this->slugger = $slugger;
        $this->security = $security;
        $this->associationServices = $associationServices;
        $this->associationRepository = $associationRepository;
        $this->chartGeneratorService = $chartGeneratorService;
    }

    #[Route('/', name: 'home')]
    public function index(Request $request): Response
    {
        $chart = null;
        $formAssociation = null;
        $currentYear = date('Y');
        $association = $this->associationServices->getAssocFromUser();
        $associationForm = $this->createForm(AssociationType::class);

        if ($association != null) {
            $profiles = $association->getProfils();
            $dataMonth = $this->chartGeneratorService->monthInitialize();

            foreach ($profiles as $profil) {
                if ($profil->getJoinedAssocAt()) {
                    $joinedAt = $profil->getJoinedAssocAt();
                    $joinedAtMonth = $joinedAt->format('M');
                    $year = $joinedAt->format('Y');

                    if ($currentYear == $year) {
                        $dataMonth[$joinedAtMonth]++;
                    }
                }
            }


            $chart = $this->chartGeneratorService->generateChart($dataMonth, "Adherent");
        } else {

            $associationForm->handleRequest($request);

            if ($associationForm->isSubmitted() && $associationForm->isValid()) {
                $account = $this->getUser();
                $newAssociation = $associationForm->getData();
                $newAssociationPicture = $associationForm->get('image')->getData();

                if ($newAssociationPicture) {
                    $pictureUploaded = $this->slugger->slug($newAssociation->getName() . '-' . uniqid()) . '.' . $newAssociationPicture->guessExtension();
                    $newAssociationPicture->move(
                        //__DIR__ . '/../../../../public/pictures/associations/',
                        $_SERVER['DOCUMENT_ROOT'] . '/pictures/associations/',
                        $pictureUploaded
                    );

                    $newAssociation->setImage($pictureUploaded);
                } else {
                    $newAssociation->setImage("asso.png");
                }

                $newAssociation->setAccount($account);

                $this->em->persist($newAssociation);
                $this->em->flush();
                $this->flashy->success('Félicitations, vos informations ont bien été sauvegardés!');

                return $this->redirect($_SERVER['HTTP_REFERER']);
            }
            $formAssociation = $associationForm->createView();
        }
        return $this->render('dashboards/admin/home/index.html.twig', compact('association', 'chart', 'formAssociation', 'currentYear'));
    }
}
