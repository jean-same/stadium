<?php

namespace App\Command;

use App\Repository\ActivityRepository;
use App\Service\IconActivity;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ActivityIconCommand extends Command
{
    protected static $defaultName = 'app:activity-icon';
    protected static $defaultDescription = 'Add a short description for your command';

    private $iconActivity;
    private $entityManager;
    private $activityRepository;

    public function __construct( IconActivity $iconActivity, EntityManagerInterface $entityManager, ActivityRepository $activityRepository)
    {
        parent::__construct();

        $this->iconActivity = $iconActivity;
        $this->entityManager = $entityManager;
        $this->activityRepository = $activityRepository;
    }


    protected function configure(): void
    {
        $this
            ->addArgument('activityId', InputArgument::OPTIONAL, 'Update the icon of one activity')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $activityId = $input->getArgument('activityId');
        $nbActivities = 0;

        if ($activityId) {
            $io->note(sprintf('You passed an argument: %s', $activityId));
            $activities = [$this->activityRepository->find($activityId)] ;
        } else {
            $activities = $this->activityRepository->findAll();
        }

        if ($input->getOption('option1')) {
            // ...
        }

        foreach($activities as $activity) {
            $icon = $this->iconActivity->getIconForActivity($activity->getName());
            $activity->setIcon($icon);
            $nbActivities++;
        }

        $this->entityManager->flush();

        $suffix = $nbActivities > 1 ? "ies" : "y";

        $io->success(" $nbActivities activit$suffix have been updated");

        return Command::SUCCESS;
    }
}
