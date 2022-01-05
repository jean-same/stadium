<?php

namespace App\Controller\Dashboards\Superadmin;

use App\Repository\FileRepository;
use App\Repository\EventRepository;
use App\Repository\LessonRepository;
use App\Repository\AccountRepository;
use App\Repository\ActivityRepository;
use App\Repository\AssociationRepository;
use App\Service\General\ChartGeneratorService;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
* @Route("/dashboards/superadmin", name="dashboards_superadmin_")
*/
class HomeController extends AbstractController
{
    private $flashy;
    private $accountRepository;
    private $activityRepository;
    private $eventRepository;
    private $fileRepository;
    private $lessonRepository;
    private $chartGeneratorService;

    public function __construct(AssociationRepository $associationRepository,FlashyNotifier $flashy , AccountRepository $accountRepository, ActivityRepository $activityRepository, EventRepository $eventRepository, FileRepository $fileRepository, LessonRepository $lessonRepository, ChartGeneratorService $chartGeneratorService)
    {
        $this->accountRepository = $accountRepository;
        $this->associationRepository = $associationRepository;
        $this->activityRepository = $activityRepository;
        $this->eventRepository = $eventRepository;
        $this->fileRepository = $fileRepository;
        $this->lessonRepository = $lessonRepository;
        $this->chartGeneratorService = $chartGeneratorService;
    }

    /**
     * @Route("/", name="home")
     */
    public function home(): Response
    {
        
        $activities = $this->activityRepository->findAll();
        $events = $this->eventRepository->findAll();
        $files = $this->fileRepository->findAll();
        $lessons = $this->lessonRepository->findAll();

        $associations = $this->accountRepository->findByRole("ROLE_ASSOC");
        $superAdmins = $this->accountRepository->findByRole("ROLE_SUPER_ADMIN");
        $admins = $this->accountRepository->findByRole("ROLE_ADMIN");
        $adherents = $this->accountRepository->findByRole("ROLE_ADHERENT");

        $dataMonth = $this->chartGeneratorService->monthInitialize();

        foreach($associations as $association){
            $joinedAt = $association->getJoinedUsAt();
            $joinedAtMonth = $joinedAt->format('M');
            $year = $joinedAt->format('Y');
            $currentYear = date('Y');
            
            if($currentYear == $year){
                $dataMonth[$joinedAtMonth]++;
            }
            
        }
        
        $chart = $this->chartGeneratorService->generateChart($dataMonth, "Association");

    return $this->render('dashboards/superadmin/home/index.html.twig',  compact('associations', 'superAdmins', 'admins', 'adherents', 'activities', 'events', 'files', 'lessons' , 'chart', 'currentYear') );
    }
}
