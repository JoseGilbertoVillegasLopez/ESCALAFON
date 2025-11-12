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
        $user = $this->getUser();
/** @var \App\Entity\Usuario $user */
$personal = $user->getTrabajador();


        if (!$personal) {
            $this->addFlash('warning', 'No se encontró tu registro de información personal.');
            return $this->redirectToRoute('usuario_dashboard');
        }

        // 🔹 Obtener categoría del usuario
        $categoria = $personal->getInformacionLaboral()?->getCategoria();
        if (!$categoria) {
            $this->addFlash('warning', 'No se encontró tu categoría actual.');
            return $this->redirectToRoute('usuario_dashboard');
        }

        // 🔹 Filtramos por categoría
        $nombre = trim((string)$request->query->get('nombre', ''));
        $page   = max(1, $request->query->getInt('page', 1));
        $perPage = 50; // para mostrar todos los de su categoría

        $data = $this->escalafonService->getEscalafonData(
            categoriaId: $categoria->getId(),
            page: $page,
            perPage: $perPage,
            nombre: $nombre
        );

        $items = $data['items'];

        // 🔹 Determinar la posición real del usuario dentro del ranking
        $posicion = null;
        foreach ($items as $index => $t) {
            if ($t['id'] === $personal->getId()) {
                $posicion = $index + 1 + (($page - 1) * $perPage);
                break;
            }
        }

        
        return $this->render('user/escalafon/index.html.twig', [
            'items'      => $items,
            'pagination' => $data['pagination'] ?? [],
            'filters'    => ['nombre' => $nombre],
            'categoria'  => $categoria,
            'posicion'   => $posicion,
            'mi_id'      => $personal->getId(),
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
