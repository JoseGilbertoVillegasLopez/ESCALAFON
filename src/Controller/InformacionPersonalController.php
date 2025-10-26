<?php

namespace App\Controller;

use App\Entity\InformacionPersonal;
use App\Form\InformacionPersonalType;
use App\Repository\InformacionPersonalRepository;
use App\Service\UserCreator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/admin/informacion/personal')]
final class InformacionPersonalController extends AbstractController
{

    private UserCreator $userCreator;

    public function __construct(UserCreator $userCreator)
    {
        $this->userCreator = $userCreator;
    }

    #[Route(name: 'app_informacion_personal_index', methods: ['GET'])]
    public function index(InformacionPersonalRepository $informacionPersonalRepository): Response
    {
        return $this->render('admin/informacion_personal/index.html.twig', [
            'informacion_personals' => $informacionPersonalRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_informacion_personal_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $informacionPersonal = new InformacionPersonal();
        $form = $this->createForm(InformacionPersonalType::class, $informacionPersonal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($informacionPersonal);
            $entityManager->flush();
            $this->userCreator->createFromPersonal($informacionPersonal);

            return $this->redirectToRoute('app_informacion_personal_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/informacion_personal/new.html.twig', [
            'informacion_personal' => $informacionPersonal,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_informacion_personal_show', methods: ['GET'])]
    public function show(InformacionPersonal $informacionPersonal): Response
    {
        return $this->render('admin/informacion_personal/show.html.twig', [
            'informacion_personal' => $informacionPersonal,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_informacion_personal_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, InformacionPersonal $informacionPersonal, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(InformacionPersonalType::class, $informacionPersonal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_informacion_personal_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('informacion_personal/edit.html.twig', [
            'informacion_personal' => $informacionPersonal,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_informacion_personal_delete', methods: ['POST'])]
    public function delete(Request $request, InformacionPersonal $informacionPersonal, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$informacionPersonal->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($informacionPersonal);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_informacion_personal_index', [], Response::HTTP_SEE_OTHER);
    }
}
