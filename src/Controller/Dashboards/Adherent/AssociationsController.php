<?php

namespace App\Controller\Dashboards\Adherent;

use Doctrine\ORM\EntityManagerInterface;
use App\Repository\AssociationRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/dashboards/adherent/{slug}/associations', name: 'dashboards_adherent_associations_')]
class AssociationsController extends AbstractController
{
    private $associationRepository;
    private $em;

    public function __construct(AssociationRepository $associationRepository, EntityManagerInterface $em)
    {
        $this->associationRepository = $associationRepository;
        $this->entityManager = $em;
    }

    #[Route('/', name: 'browse')]
    public function browse(): Response
    {
        $associations = $this->associationRepository->findAll();

        return $this->render('dashboards/adherent/associations/index.html.twig', compact('associations'));
    }
}
