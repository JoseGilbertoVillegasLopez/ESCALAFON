<?php
// src/Controller/Admin/EscalafonController.php

namespace App\Controller;

use App\Repository\CategoriaRepository;
use App\Service\EscalafonService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Controlador del módulo Escalafón (área administrativa).
 */
#[Route('/admin/escalafon')]
class EscalafonController extends AbstractController
{
    private EscalafonService $escalafonService;
    private CategoriaRepository $categoriaRepo;

    public function __construct(EscalafonService $escalafonService, CategoriaRepository $categoriaRepo)
    {
        $this->escalafonService = $escalafonService;
        $this->categoriaRepo    = $categoriaRepo;
    }

    #[Route('', name: 'admin_escalafon_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
         $categoriaId = $request->query->getInt('categoria', 0) ?: null;
    $nombre      = trim((string) $request->query->get('nombre', ''));
    $page        = max(1, $request->query->getInt('page', 1));
    $perPage     = 20;

    // 🔹 Llamamos al servicio y le pasamos también el nombre
    $data = $this->escalafonService->getEscalafonData(
        categoriaId: $categoriaId ?: null,
        page: $page,
        perPage: $perPage,
        nombre: $nombre // ← ¡este era el que faltaba!
    );

    return $this->render('admin/escalafon/index.html.twig', [
        'items'      => $data['items'],
        'pagination' => [
            'page'        => $page,
            'per_page'    => $perPage,
            'total'       => $data['total'],
            'total_pages' => $data['total_pages'],
        ],
        'filters'    => [
            'categoria' => $categoriaId ?: null,
            'nombre'    => $nombre,
        ],
        'categorias' => $this->categoriaRepo->findAll(),
    ]);
    }
}
