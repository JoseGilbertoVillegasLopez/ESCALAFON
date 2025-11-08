<?php

namespace App\Controller;

use App\Entity\HistorialSanciones;
use App\Form\HistorialSancionesType;
use App\Repository\HistorialSancionesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/historial/sanciones')]
final class HistorialSancionesController extends AbstractController
{
    #[Route(name: 'app_historial_sanciones_index', methods: ['GET'])]
    public function index(Request $request, HistorialSancionesRepository $historialSancionesRepository): Response
    {
        $nombre = $request->query->get('nombre');
    $desde = $request->query->get('desde');
    $hasta = $request->query->get('hasta');

    $fechaDesde = $desde ? new \DateTime($desde) : null;
    $fechaHasta = $hasta ? new \DateTime($hasta) : null;

    $sanciones = $historialSancionesRepository->findByFilters($nombre, $fechaDesde, $fechaHasta);

    return $this->render('admin/historial_sanciones/index.html.twig', [
        'historial_sanciones' => $sanciones,
        'nombre' => $nombre,
        'desde' => $desde,
        'hasta' => $hasta,
    ]);
    }

    #[Route('/new', name: 'app_historial_sanciones_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $historialSancione = new HistorialSanciones();
    $form = $this->createForm(HistorialSancionesType::class, $historialSancione);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

        $file = $form->get('comprovante')->getData();
        if ($file) {
            $nombreArchivo = uniqid('sancion_') . '.' . $file->guessExtension();
            $file->move($this->getParameter('kernel.project_dir') . '/public/uploads/sanciones', $nombreArchivo);
            $historialSancione->setComprovante($nombreArchivo);
        }

        $entityManager->persist($historialSancione);
        $entityManager->flush();

        $this->addFlash('success', 'Sanción registrada correctamente.');
        return $this->redirectToRoute('app_historial_sanciones_index');
        }

        return $this->render('admin/historial_sanciones/new.html.twig', [
            'historial_sancione' => $historialSancione,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_historial_sanciones_show', methods: ['GET'])]
    public function show(HistorialSanciones $historialSancione): Response
    {
        return $this->render('admin/historial_sanciones/show.html.twig', [
            'historial_sancione' => $historialSancione,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_historial_sanciones_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, HistorialSanciones $historialSancione, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(HistorialSancionesType::class, $historialSancione);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

        $file = $form->get('comprovante')->getData();
        if ($file) {
            // 🔄 Si ya había un archivo, lo reemplazamos
            $nombreArchivo = uniqid('sancion_') . '.' . $file->guessExtension();
            $file->move($this->getParameter('kernel.project_dir') . '/public/uploads/sanciones', $nombreArchivo);
            $historialSancione->setComprovante($nombreArchivo);
        }

        $entityManager->flush();

        $this->addFlash('success', 'Sanción actualizada correctamente.');
        return $this->redirectToRoute('app_historial_sanciones_index');
        }

        return $this->render('admin/historial_sanciones/edit.html.twig', [
            'historial_sancione' => $historialSancione,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_historial_sanciones_delete', methods: ['POST'])]
    public function delete(Request $request, HistorialSanciones $historialSancione, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$historialSancione->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($historialSancione);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_historial_sanciones_index', [], Response::HTTP_SEE_OTHER);
    }
}
