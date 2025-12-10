<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminDashboardController extends AbstractController
{
    #[Route('/admin/dashboard', name: 'admin_dashboard')]
    public function index(): Response
    {
        // Plantilla principal del dashboard
        return $this->render('admin/dashboard/dashboard.html.twig');
    }

    #[Route('/admin/dashboard/load/{section}', name: 'admin_dashboard_load')]
    public function loadSection(string $section): Response
    {
        // Mapeo de secciones → vistas Twig reales
        $viewMap = [
            'vacantes' => 'admin/vacantes/index.html.twig',
            'personal' => 'admin/personal/index.html.twig',
            'categorias' => 'admin/categoria/index.html.twig',
            'puestos' => 'admin/puesto/index.html.twig',
            'cursos' => 'admin/cursos/index.html.twig',
            'sanciones' => 'admin/historial_sanciones/index.html.twig',
            'escalafon' => 'admin/escalafon/index.html.twig',
            'ascensos' => 'admin/historial_ascenso/index.html.twig',
            'Dias30' => 'admin/escalafon/diasTrabajados.html.twig',
        ];

        if (!isset($viewMap[$section])) {
            return new Response('<h2>❌ Sección no encontrada</h2>', 404);
        }

        // Renderiza solo el fragmento (sin base)
        $html = $this->renderView($viewMap[$section]);
        return new Response($html);
    }
}
