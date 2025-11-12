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
            $error = 'No se encontró ningún trabajador con ese correo.';
        } elseif ($pass1 !== $pass2) {
            $error = 'Las contraseñas no coinciden.';
        } else {
            $usuario = $info->getUsuario();

            if (!$usuario) {
                $error = 'No hay una cuenta asociada a ese trabajador.';
            } else {
                $hashedPassword = $passwordHasher->hashPassword($usuario, $pass1);
                $usuario->setPassword($hashedPassword);

                $em->persist($usuario);
                $em->flush();

                $mensaje = '✅ Tu contraseña se ha actualizado correctamente. Ya puedes iniciar sesión.';
                $correo = ''; // limpia los campos
            }
        }
    }

    return $this->render('security/password_reset.html.twig', [
        'mensaje' => $mensaje,
        'error' => $error,
        'correo' => $correo,
    ]);
}

}
