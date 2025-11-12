<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UsuarioDashboardController extends AbstractController
{
    #[Route('/user/dashboard', name: 'usuario_dashboard')]
    public function index(): Response
    {
        // Plantilla principal del dashboard
        return $this->render('user/dashboard/dashboard.html.twig');
    }
}
