<?php

namespace App\Command;

use App\Repository\FileRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FileUpdateCommand extends Command
{
    protected static $defaultName = 'app:file-update';
    protected static $defaultDescription = 'Add a short description for your command';

    private $entityManager;
    private $fileRepository;

    public function __construct( EntityManagerInterface $entityManager, FileRepository $fileRepository)
    {
        parent::__construct();

        $this->entityManager = $entityManager;
        $this->fileRepository = $fileRepository;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('fileId', InputArgument::OPTIONAL, 'To complete a file')
            ->addOption('paid', null, InputOption::VALUE_NONE, 'To set the paid field to true')
            ->addOption('valid', null, InputOption::VALUE_NONE, 'To set the valid field to true')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $fileId = $input->getArgument('fileId');
        $paid = $input->getOption('paid');
        $valid = $input->getOption('valid');
        $validate = false;
        //$file = $this->fileRepository->find($fileId);

        if(!$fileId) {
            $io->error("You need to pass a valid id");
        } else {

            $file = $this->fileRepository->find($fileId);
            if(!$file){
             $io->error("No file");
            } else {
                if ($fileId && !$paid && !$valid) {
                    $io->note(sprintf('You passed an argument: %s', $fileId));
                    $file->setIsPaid(true);
                    $file->setIsValid(true);
                    $file->setIsComplete(true);
                    $validate = "complete";
                }

                if ($valid && $fileId) {
                    $file->setIsValid(true);

                    if ($file->getIsPaid()) {
                        $file->setIsComplete(true);
                    }
                    $validate = "valid";
                }

                if ($paid && $fileId) {
                    $file->setIsPaid(true);

                    if ($file->getIsValid()) {
                        $file->setIsComplete(true);
                    }
                    $validate = "paid";
                }

                if ($validate) {
                    $this->entityManager->flush($file);
                    $io->success("Your $validate field is set to true.");
                } else {
                    $io->error("Pass an argument file id to complete a file, plus an option paid or valid to pass a specific field to true");
                }
            }
        }

        
        return Command::SUCCESS;
    }
}
