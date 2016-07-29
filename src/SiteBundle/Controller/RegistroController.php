<?php

// src/SiteBundle/Controller/RegistrationController.php
namespace SiteBundle\Controller;

use SiteBundle\Form\UsuarioType;
use SiteBundle\Entity\Usuario;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class RegistroController extends Controller
{
    /**
     * @Route("/registro/formulario", name="user_registration")
     */
    public function registerAction(Request $request)
    {
        // 1) build the form
        $user = new Usuario();
        $form = $this->createForm(UsuarioType::class, $user);

        // 2) handle the submit (will only happen on POST)
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // 3) Encode the password (you could also do this via Doctrine listener)
            $password = $this->get('security.password_encoder')
                ->encodePassword($user, $user->getSenhaTxt());
            $user->setSenha($password);

            // 4) save the User!
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            // ... do any other work - like sending them an email, etc
            // maybe set a "flash" success message for the user

            return $this->redirectToRoute('painel_conteudo_index');
        }

        return $this->render(
            'registro/registro.html.twig',
            array('form' => $form->createView())
        );
    }
}