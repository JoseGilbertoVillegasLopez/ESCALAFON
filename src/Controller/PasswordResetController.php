<?php

namespace App\Controller;

use App\Entity\Usuario;
use App\Repository\UsuarioRepository;
use App\Repository\InformacionPersonalRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class PasswordResetController extends AbstractController
{
    #[Route('/recuperar-password', name: 'app_password_reset')]
public function reset(
    Request $request,
    UsuarioRepository $usuarioRepository,
    InformacionPersonalRepository $infoRepository,
    UserPasswordHasherInterface $passwordHasher,
    EntityManagerInterface $em
): Response {
    $mensaje = null;
    $error = null;
    $correo = '';

    if ($request->isMethod('POST')) {
        $correo = trim($request->request->get('correo'));
        $pass1 = trim($request->request->get('password'));
        $pass2 = trim($request->request->get('password_confirm'));

        $info = $infoRepository->findOneBy(['correo' => $correo]);

        if (!$info) {
    $this->addFlash('error', 'No se encontró ningún trabajador con ese correo.');
} elseif ($pass1 !== $pass2) {
    $this->addFlash('error', 'Las contraseñas no coinciden.');
} elseif (!$usuario = $info->getUsuario()) {
    $this->addFlash('error', 'No hay una cuenta asociada a ese trabajador.');
} else {
    $hashedPassword = $passwordHasher->hashPassword($usuario, $pass1);
    $usuario->setPassword($hashedPassword);

    $em->persist($usuario);
    $em->flush();

    $this->addFlash('mensaje', '✅ Tu contraseña se ha actualizado correctamente. Ya puedes iniciar sesión.');

    return $this->redirectToRoute('app_password_reset');
}
    }

   return $this->render('security/password_reset.html.twig', [
    'correo' => $correo,
]);
}

}
