<?php

namespace App\Controller;

use App\Entity\Capacitacion;
use App\Entity\ContactosEmergencia;
use App\Entity\FormacionAcademica;
use App\Entity\InformacionLaboral;
use App\Entity\InformacionPersonal;
use App\Form\InformacionPersonalEditType;
use App\Form\InformacionPersonalType;
use App\Repository\InformacionLaboralRepository;
use App\Repository\InformacionPersonalRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException; 
use Symfony\Component\String\Slugger\SluggerInterface;// Excepción para errores de archivos



#[Route('/usuario/informacion/personal')]
final class InformacionPersonalControllerUsuario extends AbstractController
{
    #[Route(name: 'usuario_informacion_personal_index', methods: ['GET'])]
    public function index(
        InformacionPersonalRepository $repo
    ): Response
    {
        // Filtramos SOLO la información del usuario autenticado
        $user = $this->getUser();
/** @var \App\Entity\Usuario $user */
$informacionPersonal = $user->getTrabajador();



        if (!$informacionPersonal) {
            $this->addFlash('warning', 'No tienes información registrada en el sistema.');
            return $this->redirectToRoute('usuario_dashboard');
        }

        return $this->render('user/informacion_personal/show.html.twig', [
            'informacion_personal' => $informacionPersonal,
        ]);
    }

    #[Route('/{id}', name: 'usuario_informacion_personal_show', methods: ['GET'])]
    public function show(InformacionPersonal $informacionPersonal): Response
    {
        // ⚠️ Seguridad: impedir acceso a otros usuarios
        if ($informacionPersonal->getUsuario() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('user/informacion_personal/show.html.twig', [
            'informacion_personal' => $informacionPersonal,
        ]);
    }

    #[Route('/{id}/edit', name: 'usuario_informacion_personal_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, InformacionPersonal $informacionPersonal, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        if ($informacionPersonal->getUsuario() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        // Usamos un form reducido (solo los campos permitidos)
        $form = $this->createForm(InformacionPersonalEditType::class, $informacionPersonal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imagenFile = $form->get('imagen')->getData();

            if ($imagenFile) {
                $originalFilename = pathinfo($imagenFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imagenFile->guessExtension();

                try {
                    $imagenFile->move($this->getParameter('uploads_directory'), $newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Error al subir la nueva imagen: '.$e->getMessage());
                }

                $informacionPersonal->setImagen($newFilename);
            }

            $em->flush();

            $this->addFlash('success', 'Información actualizada correctamente.');
            return $this->redirectToRoute('usuario_informacion_personal_show', ['id' => $informacionPersonal->getId()]);
        }

        return $this->render('user/informacion_personal/edit.html.twig', [
            'informacion_personal' => $informacionPersonal,
            'form' => $form,
        ]);
    }
}
