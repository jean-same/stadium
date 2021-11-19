<?php

namespace App\Controller\Dashboards\Superadmin;


use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;
use App\Repository\AccountRepository;
use App\Repository\ActivityRepository;
use App\Repository\AssociationRepository;
use App\Repository\EventRepository;
use App\Repository\FileRepository;
use App\Repository\LessonRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
* @Route("/dashboards/superadmin", name="dashboards_superadmin_")
*/
class HomeController extends AbstractController
{
    private $accountRepository;
    private $activityRepository;
    private $eventRepository;
    private $fileRepository;
    private $lessonRepository;

    public function __construct(AssociationRepository $associationRepository, AccountRepository $accountRepository, ActivityRepository $activityRepository, EventRepository $eventRepository, FileRepository $fileRepository, LessonRepository $lessonRepository)
    {
        $this->accountRepository = $accountRepository;
        $this->associationRepository = $associationRepository;
        $this->activityRepository = $activityRepository;
        $this->eventRepository = $eventRepository;
        $this->fileRepository = $fileRepository;
        $this->lessonRepository = $lessonRepository;
    }

    /**
     * @Route("/", name="home")
     */
    public function home(ChartBuilderInterface $chartBuilder): Response
    {
        
        $activities = $this->activityRepository->findAll();
        $events = $this->eventRepository->findAll();
        $files = $this->fileRepository->findAll();
        $lessons = $this->lessonRepository->findAll();

        $associations = $this->accountRepository->findByRole("ROLE_ASSOC");
        $superAdmins = $this->accountRepository->findByRole("ROLE_SUPER_ADMIN");
        $admins = $this->accountRepository->findByRole("ROLE_ADMIN");
        $adherents = $this->accountRepository->findByRole("ROLE_ADHERENT");

        $dataMonth = [
            "Jan" => 0,
            "Feb" => 0,
            "Mar" => 0,
            "Apr" => 0,
            "May" => 0,
            "Jun" => 0,
            "Jul" => 0,
            "Aug" => 0,
            "Sep" => 0,
            "Oct" => 0,
            "Sep" => 0,
            "Oct" => 0,
            "Nov" => 0,
            "Dec" => 0
        ];

        foreach($associations as $association){
            $joinedAt = $association->getJoinedUsAt();
            $joinedAtMonth = $joinedAt->format('M');

            $dataMonth[$joinedAtMonth]++;

        }

        $chart = $chartBuilder->createChart(Chart::TYPE_LINE);
        $chart->setData([
            'labels' => ['Janvier', 'Fevrier', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet' , 'Aout' , 'Septembre' , 'Octobre' , 'Novembre' , 'Decembre'],
            'datasets' => [
                [
                    'height' => '350px',
                    'label' => 'Association',
                    'backgroundColor' => '#d87444',
                    'borderColor' => '#074666;',
                    'data' => [ $dataMonth["Jan"] , $dataMonth["Feb"] , $dataMonth["Mar"] , $dataMonth["Apr"] , $dataMonth["May"] , $dataMonth["Jun"] , $dataMonth["Jul"] , $dataMonth["Aug"] , $dataMonth["Sep"] , $dataMonth["Oct"] , $dataMonth["Nov"] , $dataMonth["Dec"] ],
                ],
            ],
        ]);


        $chart->setOptions([
            'scales' => [
                'yAxes' => [
                    ['ticks' => ['min' => 0, 'max' => 10]],
                ],
            ],
        ]);

    return $this->render('dashboards/superadmin/home/index.html.twig',  compact('associations', 'superAdmins', 'admins', 'adherents', 'activities', 'events', 'files', 'lessons' , 'chart') );
    }
}
