<?php

namespace App\Controller\Dashboards\Adherent;

use App\Form\FilesType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Service\Members\MembersProfilServices;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/dashboards/adherent/{slug}/file', name: 'dashboards_adherent_file_')]
class FileController extends AbstractController
{
    private $em;
    private $slugger;
    private $membersProfilServices;

    public function __construct(EntityManagerInterface $em, SluggerInterface $slugger, MembersProfilServices $membersProfilServices)
    {
        $this->em = $em;
        $this->slugger = $slugger;
        $this->membersProfilServices = $membersProfilServices;
    }

    #[Route('/', name: 'file')]
    public function index(): Response
    {
        return $this->render('dashboards/adherent/file/file.html.twig', [
            'controller_name' => 'FileController',
        ]);
    }

    #[Route('/add', name: 'add')]
    public function add($slug, Request $request): Response
    {
        $profileForm = $this->createForm(FilesType::class);

        $profileForm->handleRequest($request);

        if ($profileForm->isSubmitted() && $profileForm->isValid()) {
            $profile = $this->membersProfilServices->getProfilFromUser($slug);
            $newFileFormSubmitted = $profileForm->getData();

            $newProfilMedicalCertificate = $profileForm->get('medicalCertificate')->getData();
            $newProfilRulesOfProcedure = $profileForm->get('rulesOfProcedure')->getData();

            if ($newProfilMedicalCertificate) {
                $medicalCertificateUploaded = $this->slugger->slug($profile->getlastName() . $profile->getfirstName() . 'MC-' . uniqid()) . '.' . $newProfilMedicalCertificate->guessExtension();

                $newProfilMedicalCertificate->move(
                    __DIR__ . '/../../../../public/documents/',
                    $medicalCertificateUploaded
                );

                $newFileFormSubmitted->setMedicalCertificate($medicalCertificateUploaded);
            }

            if ($newProfilRulesOfProcedure) {
                $rulesOfProcedureUploaded = $this->slugger->slug($profile->getlastName() . $profile->getfirstName() . 'RP-' . uniqid()) . '.' . $newProfilRulesOfProcedure->guessExtension();

                $newProfilRulesOfProcedure->move(
                    __DIR__ . '/../../../../public/documents/',
                    $rulesOfProcedureUploaded
                );

                $newFileFormSubmitted->setRulesOfProcedure($rulesOfProcedureUploaded);
            }

            $newFileFormSubmitted->setProfil($profile);

            $this->em->persist($newFileFormSubmitted);
            $this->em->flush();

            $this->addFlash("success", "Dossier ajouté avec success");

            return $this->redirectToRoute('dashboards_adherent_read' ,[ 'slug' => $profile->getSlug()]  );
        }

        $formFile = $profileForm->createView();
        return $this->render('dashboards/adherent/file/newFile.html.twig', compact('formFile'));
    }
}
