<?php

namespace App\Controller;

use App\Entity\HistorialAscenso;
use App\Repository\HistorialAscensoRepository;
use App\Repository\CategoriaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/historial/ascenso')]
final class HistorialAscensoController extends AbstractController
{
    #[Route('', name: 'app_historial_ascenso_index', methods: ['GET'])]
public function index(
    Request $request,
    EntityManagerInterface $em,
    CategoriaRepository $categoriaRepo
): Response {
    $nombre = trim((string) $request->query->get('nombre', ''));
    $categoriaId = $request->query->get('categoria');
    $categoriaId = ($categoriaId === '' ? null : (is_numeric($categoriaId) ? (int)$categoriaId : null));

    // 🧠 Query más simple: no hacemos join profundo
    $qb = $em->createQueryBuilder()
        ->select('a', 'p')
        ->from(HistorialAscenso::class, 'a')
        ->leftJoin('a.informacionPersonal', 'p')
        ->orderBy('a.fecha', 'DESC');

    if ($nombre !== '') {
        $qb->andWhere('p.nombre LIKE :q OR p.apellidoPaterno LIKE :q OR p.apellidoMaterno LIKE :q')
           ->setParameter('q', "%{$nombre}%");
    }

    $ascensos = $qb->getQuery()->getResult();

    // 🔹 Si se filtró por categoría, lo hacemos en PHP
    if ($categoriaId) {
        $ascensos = array_filter($ascensos, function ($ascenso) use ($categoriaId) {
            $trabajador = $ascenso->getInformacionPersonal();
            $laboral = $trabajador?->getInformacionLaboral();
            $categoria = $laboral?->getCategoria();
            return $categoria && $categoria->getId() === $categoriaId;
        });
    }

    return $this->render('admin/historial_ascenso/index.html.twig', [
        'historial_ascensos' => $ascensos,
        'categorias' => $categoriaRepo->findAll(),
        'filters' => [
            'nombre' => $nombre,
            'categoria' => $categoriaId,
        ],
    ]);
}


    #[Route('/{id}', name: 'app_historial_ascenso_show', methods: ['GET'])]
    public function show(HistorialAscenso $historialAscenso, EntityManagerInterface $em): Response
    {
        $trabajador = $historialAscenso->getInformacionPersonal();

        // Cargar todos los ascensos del mismo trabajador
        $historialCompleto = [];
        if ($trabajador) {
            $historialCompleto = $em->getRepository(HistorialAscenso::class)->findBy(
                ['informacionPersonal' => $trabajador],
                ['fecha' => 'DESC']
            );
        }

        return $this->render('admin/historial_ascenso/show.html.twig', [
            'historial_ascenso' => $historialAscenso,
            'trabajador' => $trabajador,
            'historial_completo' => $historialCompleto,
        ]);
    }
}
