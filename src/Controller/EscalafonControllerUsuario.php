<?php

namespace App\Controller;

use App\Entity\InformacionPersonal;
use App\Repository\CategoriaRepository;
use App\Service\EscalafonService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controlador del módulo Escalafón (panel usuario).
 */
#[Route('/usuario/escalafon')]
class EscalafonControllerUsuario extends AbstractController
{
    public function __construct(
        private EscalafonService $escalafonService,
        private CategoriaRepository $categoriaRepo
    ) {}

    #[Route('', name: 'usuario_escalafon_index', methods: ['GET'])]
public function index(Request $request): Response
{
    /** @var \App\Entity\Usuario $user */
    $user = $this->getUser();
    $personal = $user->getTrabajador();

    if (!$personal) {
        $this->addFlash('warning', 'No se encontró tu registro de información personal.');
        return $this->redirectToRoute('usuario_dashboard');
    }

    // ===============================
    // 1️⃣ ESCALAFÓN GLOBAL COMPLETO
    // ===============================
    $data = $this->escalafonService->getEscalafonData(
        categoriaId: null,   // 🔥 CLAVE: NUNCA filtrar aquí
        page: 1,
        perPage: 10000,      // suficiente para todo el escalafón
        nombre: null
    );

    $itemsGlobales = $data['items'];

    // ===============================
    // 2️⃣ POSICIÓN REAL DEL USUARIO
    // ===============================
    $posicionReal = null;
    foreach ($itemsGlobales as $row) {
        if ($row['id'] === $personal->getId()) {
            $posicionReal = $row['posicion_final'];
            break;
        }
    }

    // ===============================
    // 3️⃣ FILTROS (SOLO VISUALES)
    // ===============================
    $categoriaId = $request->query->get('categoria');
    $nombre      = trim((string)$request->query->get('nombre', ''));

    $itemsFiltrados = array_filter($itemsGlobales, function ($row) use ($categoriaId, $nombre) {

        if ($categoriaId && ($row['categoria_id'] ?? null) != $categoriaId) {
            return false;
        }

        if ($nombre && stripos($row['nombre'], $nombre) === false) {
            return false;
        }

        return true;
    });

    // ===============================
    // 4️⃣ PAGINACIÓN VISUAL
    // ===============================
    $page    = max(1, $request->query->getInt('page', 1));
    $perPage = 50;

    $total   = count($itemsFiltrados);
    $items   = array_slice(
        array_values($itemsFiltrados),
        ($page - 1) * $perPage,
        $perPage
    );

    // ===============================
    // 5️⃣ RENDER
    // ===============================
    return $this->render('user/escalafon/index.html.twig', [
        'items'      => $items,
        'posicion'   => $posicionReal,          // 🔥 REAL
        'mi_id'      => $personal->getId(),
        'categorias' => $this->categoriaRepo->findAll(),
        'filters'    => [
            'categoria' => $categoriaId,
            'nombre'    => $nombre,
        ],
        'pagination' => [
            'page'        => $page,
            'total_pages' => (int)ceil($total / $perPage),
        ],
    ]);
}


    #[Route('/ver/{id}', name: 'usuario_escalafon_show', methods: ['GET'])]
    public function show(int $id, EntityManagerInterface $em, EscalafonService $escalafonService): Response
    {
        $user = $this->getUser();
/** @var \App\Entity\Usuario $user */
    $personal = $user->getTrabajador();


        if ($personal->getId() !== $id) {
            throw $this->createAccessDeniedException('Solo puedes ver tu propio detalle de escalafón.');
        }

        $detalle = $escalafonService->getDetalleEscalafon($personal);

        return $this->render('user/escalafon/show.html.twig', [
            'trabajador' => $personal,
            'detalle'    => $detalle,
        ]);
    }
}
