<?php

namespace App\Controller;

use App\Entity\Vacantes;
use App\Form\VacantesType;
use App\Repository\VacantesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/vacantes')]
final class VacantesController extends AbstractController
{
    #[Route(name: 'app_vacantes_index', methods: ['GET'])]
    public function index(VacantesRepository $vacantesRepository): Response
    {
        return $this->render('admin/vacantes/index.html.twig', [
            'vacantes' => $vacantesRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_vacantes_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $vacante = new Vacantes();
        $form = $this->createForm(VacantesType::class, $vacante);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($vacante);
            $entityManager->flush();

            return $this->redirectToRoute('app_vacantes_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/vacantes/new.html.twig', [
            'vacante' => $vacante,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_vacantes_show', methods: ['GET'])]
    public function show(Vacantes $vacante): Response
    {
        return $this->render('admin/vacantes/show.html.twig', [
            'vacante' => $vacante,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_vacantes_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Vacantes $vacante, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(VacantesType::class, $vacante);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_vacantes_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/vacantes/edit.html.twig', [
            'vacante' => $vacante,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_vacantes_delete', methods: ['POST'])]
    public function delete(Request $request, Vacantes $vacante, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$vacante->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($vacante);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_vacantes_index', [], Response::HTTP_SEE_OTHER);
    }
}
