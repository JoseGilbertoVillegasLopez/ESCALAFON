<?php

namespace App\Controller;

use App\Repository\HistorialSancionesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/usuario/historial/sanciones')]
class HistorialSancionesControllerUsuario extends AbstractController
{
    #[Route('', name: 'usuario_historial_sanciones_index', methods: ['GET'])]
    public function index(HistorialSancionesRepository $historialSancionesRepository): Response
    {
        $user = $this->getUser();
/** @var \App\Entity\Usuario $user */
$personal = $user->getTrabajador();


        if (!$personal) {
            $this->addFlash('warning', 'No se encontró tu registro de información personal.');
            return $this->redirectToRoute('usuario_dashboard');
        }

        // 🔹 Obtener todas las sanciones del usuario actual
        $sanciones = $historialSancionesRepository->findBy(
            ['informacionPersonal' => $personal],
            ['fecha' => 'DESC']
        );

        return $this->render('user/historial_sanciones/index.html.twig', [
            'sanciones' => $sanciones,
            'personal'  => $personal,
        ]);
    }
}
