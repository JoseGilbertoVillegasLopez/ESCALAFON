<?php

namespace App\Controller;

use App\Entity\Cursos;
use App\Form\CursosType;
use App\Repository\CursosRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/cursos')]
final class CursosControllerUsuario extends AbstractController
{
    #[Route(name: 'app_cursos_index', methods: ['GET'])]
    public function index(Request $request,CursosRepository $cursosRepository, EntityManagerInterface $entityManager): Response
    {
         $nombre = $request->query->get('nombre');
    $categoria = $request->query->get('categoria');

    // 🔹 Filtramos los cursos según los parámetros
    $cursos = $cursosRepository->findByFilters($nombre, $categoria);

    // 🔹 Obtenemos todas las categorías para llenar el select
    $categorias = $entityManager->getRepository(\App\Entity\Categoria::class)->findAll();

    // 🔹 Enviamos ambos datos a la vista
    return $this->render('admin/cursos/index.html.twig', [
        'cursos' => $cursos,
        'categorias' => $categorias,
    ]);
    }

    #[Route('/new', name: 'app_cursos_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $curso = new Cursos();
        $form = $this->createForm(CursosType::class, $curso);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($curso);
            $entityManager->flush();

            return $this->redirectToRoute('app_cursos_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/cursos/new.html.twig', [
            'curso' => $curso,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_cursos_show', methods: ['GET'])]
    public function show(Cursos $curso): Response
    {
        return $this->render('admin/cursos/show.html.twig', [
            'curso' => $curso,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_cursos_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Cursos $curso, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CursosType::class, $curso);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_cursos_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/cursos/edit.html.twig', [
            'curso' => $curso,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_cursos_delete', methods: ['POST'])]
    public function delete(Request $request, Cursos $curso, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$curso->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($curso);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_cursos_index', [], Response::HTTP_SEE_OTHER);
    }
}
