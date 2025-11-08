<?php

namespace App\Controller;

use App\Entity\Vacantes;
use App\Form\VacantesType;
use App\Repository\CategoriaRepository;
use App\Repository\PuestoRepository;
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
    public function index(Request $request,
        VacantesRepository $vacantesRepository,
        PuestoRepository $puestoRepository,
        CategoriaRepository $categoriaRepository): Response
    {
        $filtros = [
            'nombre' => $request->query->get('nombre', ''),
            'puesto' => $request->query->get('puesto', ''),
            'categoria' => $request->query->get('categoria', ''),
        ];

        // 🔍 Lógica de filtrado flexible
        $query = $vacantesRepository->createQueryBuilder('v')
            ->leftJoin('v.puesto', 'p')
            ->leftJoin('v.categoria', 'c')
            ->addSelect('p', 'c');

        if ($filtros['nombre']) {
            $query->andWhere('LOWER(v.nombre) LIKE LOWER(:nombre)')
                  ->setParameter('nombre', '%' . $filtros['nombre'] . '%');
        }

        if ($filtros['puesto']) {
            $query->andWhere('p.id = :puesto')
                  ->setParameter('puesto', $filtros['puesto']);
        }

        if ($filtros['categoria']) {
            $query->andWhere('c.id = :categoria')
                  ->setParameter('categoria', $filtros['categoria']);
        }

        $vacantes = $query->getQuery()->getResult();

        return $this->render('admin/vacantes/index.html.twig', [
            'vacantes' => $vacantes,
            'filtros' => $filtros,
            'puestos' => $puestoRepository->findAll(),
            'categorias' => $categoriaRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_vacantes_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $vacante = new Vacantes();
         // ✅ Agregamos un requisito por defecto al cargar el formulario
    if ($vacante->getRequisitos()->isEmpty()) {
        $requisito = new \App\Entity\RequisitosVacantes();
        $vacante->addRequisito($requisito);
    }
        $form = $this->createForm(VacantesType::class, $vacante);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Asignar activo por defecto
        $vacante->setActivo(true);

        // Calcular vacantes libres si aplica
        $libres = max($vacante->getNumeroVacantes() - ($vacante->getVacantesUsadas() ?? 0), 0);
        $vacante->setVacantesLibres($libres);
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
