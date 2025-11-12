<?php

namespace App\Controller;

use App\Repository\HistorialAscensoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/usuario/historial/ascensos')]
class HistorialAscensoControllerUsuario extends AbstractController
{
    #[Route('', name: 'usuario_historial_ascensos_index', methods: ['GET'])]
    public function index(HistorialAscensoRepository $historialAscensoRepository): Response
    {
        $user = $this->getUser();

        /** @var \App\Entity\Usuario $user */
        $personal = $user->getTrabajador();

        if (!$personal) {
            $this->addFlash('warning', 'No se encontró tu registro de información personal.');
            return $this->redirectToRoute('usuario_dashboard');
        }

        // 🔹 Obtener ascensos solo del usuario autenticado
        $ascensos = $historialAscensoRepository->findBy(
            ['informacionPersonal' => $personal],
            ['fecha' => 'DESC']
        );

        return $this->render('user/historial_ascensos/index.html.twig', [
            'ascensos' => $ascensos,
            'personal' => $personal,
        ]);
    }
}
