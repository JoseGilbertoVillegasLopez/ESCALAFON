<?php
// src/Controller/Admin/EscalafonController.php

namespace App\Controller;

use App\Entity\HistorialAscenso;
use App\Entity\Vacantes;
use App\Repository\CategoriaRepository;
use App\Service\EscalafonService;
use App\Service\NotificacionAscensoMailer;
use Doctrine\ORM\EntityManagerInterface;
use Proxies\__CG__\App\Entity\InformacionPersonal;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
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
        public function index(Request $request, LoggerInterface $logger): Response
        {
            $categoriaId = $request->query->get('categoria');
            $categoriaId = ($categoriaId === '' ? null : (is_numeric($categoriaId) ? (int)$categoriaId : null));
            $nombre   = trim((string) $request->query->get('nombre', '')); // <- puede venir vacío
            $page    = max(1, $request->query->getInt('page', 1));
            $perPage = 20;

            $data = $this->escalafonService->getEscalafonData(
                categoriaId: $categoriaId,
                page: $page,
                perPage: $perPage,
                nombre: $nombre
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
                    'categoria' => $categoriaId,
                    'nombre'    => $nombre,
                ],
                'categorias' => $this->categoriaRepo->findAll(),
            ]);
        }

    #[Route('/notificar/{id}/{vacante}', name: 'admin_escalafon_notificar', methods: ['GET'])]
        public function notificar(
            int $id,
            string $vacante,
            EntityManagerInterface $em,
            NotificacionAscensoMailer $mailer
        ): RedirectResponse {
            $trabajador = $em->getRepository(InformacionPersonal::class)->find($id);

            if (!$trabajador) {
                $this->addFlash('danger', 'No se encontró el trabajador.');
                return $this->redirectToRoute('admin_escalafon_index');
            }

            // 👉 Ahora sí usamos la vacante que viene desde la vista
            $puestoDestino = $vacante;

            $mailer->sendNotificacion($trabajador, $puestoDestino);

            $this->addFlash('success', sprintf(
                'Se notificó a %s sobre la vacante "%s".',
                $trabajador,
                $puestoDestino
            ));

            return $this->redirectToRoute('admin_escalafon_index');
        }

        #[Route('/ver/{id}', name: 'admin_escalafon_show', methods: ['GET'])]
        public function show(
            int $id,
            EntityManagerInterface $em,
            EscalafonService $escalafonService
        ): Response {
            $trabajador = $em->getRepository(\App\Entity\InformacionPersonal::class)->find($id);

            if (!$trabajador) {
                $this->addFlash('danger', 'No se encontró el trabajador.');
                return $this->redirectToRoute('admin_escalafon_index');
            }

            // 🔹 Obtener todos los datos relacionados del servicio
            $detalle = $escalafonService->getDetalleEscalafon($trabajador);

            return $this->render('admin/escalafon/show.html.twig', [
                'trabajador' => $trabajador,
                'detalle'    => $detalle,
            ]);
        }
        #[Route('/ascender/{vacanteId}/{trabajadorId}', name: 'admin_escalafon_ascender', methods: ['GET'])]
public function ascender(
    int $vacanteId,
    int $trabajadorId,
    EntityManagerInterface $em
): Response {
    // 🔹 Buscar las entidades
    $vacante = $em->getRepository(Vacantes::class)->find($vacanteId);
    $trabajador = $em->getRepository(\App\Entity\InformacionPersonal::class)->find($trabajadorId);

    if (!$vacante || !$trabajador) {
        $this->addFlash('danger', 'No se encontró la vacante o el trabajador.');
        return $this->redirectToRoute('admin_escalafon_index');
    }

    // 🔹 Obtener la información laboral actual
    $laboral = $trabajador->getInformacionLaboral();
    if (!$laboral) {
        $this->addFlash('danger', 'El trabajador no tiene información laboral registrada.');
        return $this->redirectToRoute('admin_escalafon_index');
    }

    // 🔹 Crear el registro de ascenso
    $ascenso = new HistorialAscenso();
    $ascenso->setFecha(new \DateTime());
    $ascenso->setPuestoAnterior($laboral->getPuesto()?->getNombre() ?? 'No definido');
    $ascenso->setPuestoAscenso($vacante->getNombre());
    $ascenso->setInformacionPersonal($trabajador);

    // 🔹 Actualizar la información laboral del trabajador
    $laboral->setPuesto($vacante->getPuesto() ?? null);
    if (method_exists($laboral, 'setCategoria') && $vacante->getCategoria()) {
        $laboral->setCategoria($vacante->getCategoria());
    }

    // 🔹 Guardar los cambios
    $em->persist($ascenso);
    $em->persist($laboral);
    $em->flush();

    // 🔹 Confirmación
    $this->addFlash('success', sprintf(
        '✅ Ascenso efectuado correctamente para %s al puesto "%s".',
        (string) $trabajador,
        $vacante->getNombre()
    ));

    return $this->redirectToRoute('admin_escalafon_index');
}




}
