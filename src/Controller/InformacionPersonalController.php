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
use App\Service\UserCreator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException; 
use App\Repository\PuestoRepository;
use App\Repository\CategoriaRepository;
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
    public function index(
        Request $request,
        InformacionPersonalRepository $repo,
        CategoriaRepository $categoriaRepo,
        PuestoRepository $puestoRepo): Response
    {
        $search = $request->query->get('search');
        $categoria = $request->query->get('categoria');
        $puesto = $request->query->get('puesto');
        $antiguedad = $request->query->get('antiguedad');

        $informacion_personals = $repo->buscarFiltrado($search, $categoria, $puesto, $antiguedad);

        return $this->render('admin/informacion_personal/index.html.twig', [
    'informacion_personals' => $informacion_personals,
    'categorias' => $categoriaRepo->findAll(),
    'puestos' => $puestoRepo->findAll(),
]);

    }

    #[Route('/new', name: 'app_informacion_personal_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $informacionPersonal = new InformacionPersonal();

// contacto vacío
$informacionPersonal->addContactosEmergencia(new ContactosEmergencia());

// capacitación vacía
$informacionPersonal->addCapacitacion(new Capacitacion());

// laboral
$informacionPersonal->setInformacionLaboral(new InformacionLaboral());

// formación académica
$formacion = new FormacionAcademica();
$formacion->setInformacionPersonal($informacionPersonal);
$informacionPersonal->setFormacionAcademica($formacion);


        

        $form = $this->createForm(InformacionPersonalType::class, $informacionPersonal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // 📸 1. Procesar la imagen principal del trabajador
            $imagenFile = $form->get('imagen')->getData();

            if ($imagenFile) {
                $originalFilename = pathinfo($imagenFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imagenFile->guessExtension();

                try {
                    $imagenFile->move(
                        $this->getParameter('uploads_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', 'Error al subir la imagen: ' . $e->getMessage());
                }

                $informacionPersonal->setImagen($newFilename);
            }


            // 📄 2. Procesar los certificados del subformulario "formacionesAcademicas"
            $formacion = $informacionPersonal->getFormacionAcademica(); // no array

            $certificadoFile = $form->get('formacionAcademica')->get('certificado')->getData();

            if ($certificadoFile) {
                $originalFilename = pathinfo($certificadoFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $certificadoFile->guessExtension();

                try {
                    $certificadoFile->move(
                        $this->getParameter('certificados_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', 'Error al subir el certificado: ' . $e->getMessage());
                }

                $formacion->setCertificado($newFilename);
            }
            else {
                    // Para nuevos registros, garantizar null explícito
                    if ($formacion->getCertificado() === null) {
                        $formacion->setCertificado(null);
                    }
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
    public function edit(Request $request, InformacionPersonal $informacionPersonal, EntityManagerInterface $entityManager, SluggerInterface $slugger ): Response
    {
        $form = $this->createForm(InformacionPersonalType::class, $informacionPersonal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // 📸 1. Procesar la imagen principal del trabajador
            $imagenFile = $form->get('imagen')->getData();

            if ($imagenFile) {
                // 🧩 1.1 Eliminar imagen anterior si existe
                $imagenAnterior = $informacionPersonal->getImagen();
                if ($imagenAnterior) {
                    $rutaAnterior = $this->getParameter('uploads_directory') . '/' . $imagenAnterior;
                    if (file_exists($rutaAnterior)) {
                        unlink($rutaAnterior);
                    }
                }

                // 🧩 1.2 Generar nombre nuevo y guardar
                $originalFilename = pathinfo($imagenFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imagenFile->guessExtension();

                try {
                    $imagenFile->move(
                        $this->getParameter('uploads_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', 'Error al subir la nueva imagen: ' . $e->getMessage());
                }

                $informacionPersonal->setImagen($newFilename);
            }
            // 📄 2. Procesar los certificados del subformulario "formacionesAcademicas"
            $formacion = $informacionPersonal->getFormacionAcademica(); // no array

            $certificadoFile = $form->get('formacionAcademica')->get('certificado')->getData();

            if ($certificadoFile) {
                $originalFilename = pathinfo($certificadoFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $certificadoFile->guessExtension();

                try {
                    $certificadoFile->move(
                        $this->getParameter('certificados_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', 'Error al subir el certificado: ' . $e->getMessage());
                }

                $formacion->setCertificado($newFilename);
            }
            



            


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

    #[Route('/buscar/trabajador', name: 'buscar_trabajador', methods: ['GET'])]
public function buscarTrabajador(Request $request, InformacionPersonalRepository $repo): Response
{
    $term = $request->query->get('q');

    $resultados = $repo->createQueryBuilder('p')
        ->leftJoin('p.informacionLaboral', 'i')
        ->where('p.nombre LIKE :t 
                 OR p.apellidoPaterno LIKE :t 
                 OR i.numeroAfiliado LIKE :t')
        ->setParameter('t', "$term%")
        ->setMaxResults(10)
        ->getQuery()
        ->getResult();

    $json = [];

    foreach ($resultados as $p) {
        $json[] = [
            'id' => $p->getId(),
            'nombre' => $p->getNombre() . ' ' . $p->getApellidoPaterno(),
            'numero' => $p->getInformacionLaboral()
    ? $p->getInformacionLaboral()->getNumeroAfiliado()
    : 'Sin número'

        ];
    }

    return $this->json($json);
}


}

