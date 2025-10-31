<?php

namespace App\Controller;

use App\Entity\Capacitacion;
use App\Entity\ContactosEmergencia;
use App\Entity\FormacionAcademica;
use App\Entity\InformacionLaboral;
use App\Entity\InformacionPersonal;
use App\Form\InformacionPersonalType;
use App\Repository\InformacionPersonalRepository;
use App\Service\UserCreator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException; 
use Symfony\Component\String\Slugger\SluggerInterface;// Excepción para errores de archivos


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
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $informacionPersonal = new InformacionPersonal();
        // 👇 Agregamos al menos un contacto vacío antes de crear el formulario
        $contacto = new ContactosEmergencia(); // creamos un nuevo objeto vacío
        $informacionPersonal->addContactosEmergencia($contacto); // lo asociamos al objeto principal
        $informacionPersonal->addCapacitacion(new Capacitacion());// Agregamos una capacitación vacía
        $informacionPersonal->setInformacionLaboral(new InformacionLaboral());
        $informacionPersonal->setFormacionAcademica(new FormacionAcademica());

        $form = $this->createForm(InformacionPersonalType::class, $informacionPersonal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // 📸 4. Obtener el archivo subido (del campo no mapeado)
            $imagenFile = $form->get('imagen')->getData();

            // Verificamos que efectivamente se haya seleccionado una imagen
            if ($imagenFile) {
                // 🧩 5. Extraer el nombre original del archivo sin extensión
                $originalFilename = pathinfo($imagenFile->getClientOriginalName(), PATHINFO_FILENAME);

                // 🧩 6. Generar un nombre seguro sin espacios ni caracteres raros
                $safeFilename = $slugger->slug($originalFilename);

                // 🧩 7. Crear nombre único (ejemplo: juan-perez-654asd789.png)
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imagenFile->guessExtension();

                try {
                    // 🧩 8. Mover el archivo desde la carpeta temporal a la carpeta definitiva
                    $imagenFile->move(
                        $this->getParameter('uploads_directory'), // Usa el parámetro que creamos en services.yaml
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ⚠️ Si algo falla, mostramos mensaje y no detenemos el flujo
                    $this->addFlash('error', 'Error al subir la imagen: ' . $e->getMessage());
                }

                // 🧩 9. Guardar el nombre del archivo en la entidad (no el binario)
                $informacionPersonal->setImagen($newFilename);
            }
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

        return $this->render('admin/informacion_personal/edit.html.twig', [
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
