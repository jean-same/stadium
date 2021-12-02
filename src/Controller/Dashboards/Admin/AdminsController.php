<?php

namespace App\Controller\Dashboards\Admin;

use App\Form\ProfilType;
use App\Repository\ProfilRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\Admin\AssociationServices;
use Symfony\Component\HttpFoundation\Request;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[Route('/dashboards/admin/admins', name: 'dashboards_admin_admins_')]
class AdminsController extends AbstractController
{

    private $em;
    private $flashy;
    private $profilRepository;
    private $associationServices;


    public function __construct(FlashyNotifier $flashy,  EntityManagerInterface $em, ProfilRepository $profilRepository, AssociationServices $associationServices)
    {
        $this->em = $em;
        $this->flashy = $flashy;
        $this->profilRepository = $profilRepository;
        $this->associationServices = $associationServices;
    }

    #[Route('/', name: 'browse')]
    public function index(): Response
    {
        $admins = $this->associationServices->getAdminFromAssoc();

        return $this->render('dashboards/admin/admins/admins.html.twig', compact('admins'));
    }

    #[Route('/edit/{slug}', name: 'edit')]
    public function edit($slug, Request $request)
    {
        $adminToEdit = $this->profilRepository->findBy(['slug' => $slug]);

        if (!$adminToEdit) {
            throw new NotFoundHttpException("Cet admin n'existe pas");
        }

        $admin = $adminToEdit[0];

        $editAdminForm = $this->createForm(ProfilType::class, $admin);

        $editAdminForm->handleRequest($request);

        if ($editAdminForm->isSubmitted() && $editAdminForm->isValid()) {
            $adminToEdit = $editAdminForm->getData();

            $adminToEditPicture = $editAdminForm->get('picture')->getData();

            if ($adminToEditPicture) {
                $pictureUploaded = $this->slugger->slug($adminToEdit->getlastName() . $adminToEdit->getfirstName() . '-' . uniqid()) . '.' . $adminToEditPicture->guessExtension();

                $adminToEditPicture->move(
                    //__DIR__ . '/../../../../public/pictures/profilPicture/',
                    $_SERVER['DOCUMENT_ROOT'] . '/pictures/admin/',
                    $pictureUploaded
                );

                $adminToEdit->setPicture($pictureUploaded);
            }

            $this->em->flush();

        $this->flashy->success('Admin modifié avec success!');

            return $this->redirectToRoute('dashboards_admin_admins_browse');
        }

        $formAdmin = $editAdminForm->createView();

        return $this->render('dashboards/admin/admins/edit.html.twig', compact('formAdmin'));
    }

    #[Route('/delete/{id}', name: 'delete')]
    public function delete($id)
    {
        $admin = $this->profilRepository->find($id);

        if (!$admin) {
            throw new NotFoundHttpException("Cet admin n'existe pas");
        }

        $this->em->remove($admin);

        $this->em->flush();

        $this->flashy->success('Admin supprimé avec succes');

        return $this->redirectToRoute('dashboards_admin_admins_browse');
    }
}
