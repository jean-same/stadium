<?php

namespace App\Controller\Dashboards\Superadmin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


    /**
     * @Route("/dashboards/superadmin", name="dashboards_superadmin_")
     */
class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        return $this->render('dashboards/superadmin/home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
}
