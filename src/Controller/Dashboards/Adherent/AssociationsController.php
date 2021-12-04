<?php

namespace App\Controller\Dashboards\Adherent;

use Doctrine\ORM\EntityManagerInterface;
use App\Repository\AssociationRepository;
use App\Service\Members\MembersProfilServices;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/dashboards/adherent/{slug}/associations', name: 'dashboards_adherent_associations_')]
class AssociationsController extends AbstractController
{
    private $membersProfilServices;
    private $associationRepository;
    private $flashy;
    private $em;

    public function __construct(AssociationRepository $associationRepository, FlashyNotifier $flashy, EntityManagerInterface $em, MembersProfilServices $membersProfilServices)
    {
        $this->associationRepository = $associationRepository;
        $this->membersProfilServices = $membersProfilServices;
        $this->flashy = $flashy;
        $this->em = $em;
    }

    #[Route('/', name: 'browse')]
    public function browse($slug): Response
    {
        $associations = $this->associationRepository->findAll();
        $profileSlug = $slug;

        return $this->render('dashboards/adherent/associations/index.html.twig', compact('associations', 'profileSlug'));
    }

    #[Route('/{assocSlug}', name: 'read')]
    public function read($assocSlug, $slug): Response
    {
        $profile = $this->membersProfilServices->getProfilFromUser($slug);
        $association = $this->associationRepository->findOneBySlug(['slug' => $assocSlug]);
        if (!$association) {
            throw $this->createNotFoundException("Cette association n'existe pas");
        }

        $profileSlug = $slug;
        return $this->render('dashboards/adherent/associations/read.html.twig', compact('association', 'profileSlug', 'profile'));
    }

    #[Route('/{assocSlug}/register', name: 'register')]
    public function register($slug, $assocSlug): Response
    {
        $association = $this->associationRepository->findOneBySlug(['slug' => $assocSlug]);

        if (!$association) {
            throw $this->createNotFoundException("Cette association n'existe pas");
        }

        $profile = $this->membersProfilServices->getProfilFromUser($slug);

        $this->denyAccessUnlessGranted('CAN_READ', $profile, "Accès interdit");

        if ($profile->getAssociation() == null) {
            $profile->setAssociation($association);

            $this->em->flush();

            $this->flashy->success("Vous avez rejoint l'association avec succès!");

            return $this->redirectToRoute('dashboards_adherent_read', ['slug' => $slug]);
        } else {
            return $this->redirectToRoute('dashboards_adherent_read', ['slug' => $slug]);
        }
    }
}
