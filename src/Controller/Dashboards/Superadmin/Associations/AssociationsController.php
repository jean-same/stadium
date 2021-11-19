<?php

namespace App\Controller\Dashboards\Superadmin\Associations;

use App\Repository\AssociationRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


/**
* @Route("/dashboards/superadmin/associations", name="dashboard_superadmin_associations_")
*/
class AssociationsController extends AbstractController
{

    private $associationRepository;

    public function __construct(AssociationRepository $associationRepository)
    {
        $this->associationRepository = $associationRepository;
    }
    
    /**
     * @Route("/", name="browse")
     */
    public function browse(): Response
    {

        $associations = $this->associationRepository->findAll();
        $admins = 0;
        $adherents = 0;

        foreach ($associations as $association) {
            foreach ($association->getProfils() as $profil) {
                $account = $profil->getAccount();
                if (in_array("ROLE_ADHERENT", $account->getRoles())) {
                    $adherents++;
                }

                if (in_array("ROLE_ADMIN", $account->getRoles())) {
                    $admins++;
                }
            }
        }

        return $this->render('dashboards/superadmin/associations/index.html.twig',compact('associations'));
    }
}
