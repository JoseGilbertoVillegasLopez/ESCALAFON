<?php

namespace App\Controller;

use App\Repository\InformacionPersonalRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/escalafon/dias')]
class Escalafon30DiasController extends AbstractController
{
    #[Route('/30-dias', name: 'admin_escalafon_30dias', methods: ['GET', 'POST'])]
    public function gestionar30Dias(
        Request $request,
        InformacionPersonalRepository $personalRepo,
        EntityManagerInterface $em
    ): Response {
        $yearActual = (int) (new \DateTime())->format('Y');

        // POST: guardar lo que marcó el encargado
        if ($request->isMethod('POST')) {
            /** @var array<string,string> $estado */
            $estado = $request->request->all('estado'); // estado[idTrabajador] = "si" | "no"

            foreach ($estado as $id => $valor) {
                $trabajador = $personalRepo->find((int)$id);
                if (!$trabajador) {
                    continue;
                }

                $laboral = $trabajador->getInformacionLaboral();
                if (!$laboral) {
                    continue;
                }

                $json = $laboral->getTrabajo30Dias() ?? [];

                // true si cumplió, false si no cumplió
                $json[(string)$yearActual] = ($valor === 'si');

                $laboral->setTrabajo30Dias($json);
            }

            $em->flush();

            $this->addFlash('success', 'Revisión de 30 días guardada correctamente.');
            return $this->redirectToRoute('admin_escalafon_30dias');
        }

        // GET: mostrar solo los que NO tienen registro para el año actual (o está en null)
        $trabajadores = $personalRepo->createQueryBuilder('p')
            ->leftJoin('p.informacionLaboral', 'il')
            ->addSelect('il')
            ->where('il IS NOT NULL')
            ->getQuery()
            ->getResult();

        $pendientes = [];

        foreach ($trabajadores as $t) {
            $laboral = $t->getInformacionLaboral();
            if (!$laboral) {
                continue;
            }

            $json = $laboral->getTrabajo30Dias() ?? [];
            $valor = $json[(string)$yearActual] ?? null;

            // Solo mostramos los que no han sido revisados en este año
            if ($valor === null) {
                $pendientes[] = $t;
            }
        }

        return $this->render('admin/escalafon/diasTrabajados.html.twig', [
            'trabajadores' => $pendientes,
            'year' => $yearActual,
        ]);
    }
}
