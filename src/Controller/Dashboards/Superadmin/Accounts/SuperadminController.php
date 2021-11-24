<?php

namespace App\Controller\Dashboards\Superadmin\Accounts;

use App\Repository\AccountRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/dashboards/superadmin/accounts/superadmins', name: 'dashboards_superadmin_accounts_superadmins_')]
class SuperadminController extends AbstractController
{

    private $em;
    private $accountRepository;

    public function __construct(AccountRepository $accountRepository, EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->accountRepository = $accountRepository;
    }

    #[Route('/', name: 'browse')]
    public function browse(): Response
    {
        $superAdmins = $this->accountRepository->findByRole("ROLE_SUPER_ADMIN");

        return $this->render('dashboards/superadmin/accounts/superadmin/index.html.twig', compact('superAdmins'));
    }

    #[Route('/delete/{id}', name: 'delete')]
    public function delete($id): Response
    {
        $superAdmin = $this->accountRepository->find($id);

        if (!$superAdmin) {
            return $this->json("Ce profil n'existe pas", Response::HTTP_NOT_FOUND);
        }
        $this->em->remove($superAdmin);
        $this->em->flush();

        $this->addFlash("success", "SuperAdmin supprimé avec succès");

        return $this->redirect($_SERVER['HTTP_REFERER']);
    }
}
