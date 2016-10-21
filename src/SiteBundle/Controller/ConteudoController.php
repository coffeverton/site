<?php

namespace SiteBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SiteBundle\Entity\Conteudo;
use SiteBundle\Form\ConteudoType;

/**
 * Conteudo controller.
 *
 * @Route("/painel")
 * @Route("/painel/conteudo")
 */
class ConteudoController extends Controller
{
    /**
     * Lists all Conteudo entities.
     *
     * @Route("/", name="painel_conteudo_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

//        $conteudos = $em->getRepository('SiteBundle:Conteudo')->findAll();
        $conteudos = $em->getRepository('SiteBundle:Conteudo')->findBy(array(),  array('id' => 'DESC')); //ordem inversa

        return $this->render('conteudo/index.html.twig', array(
            'conteudos' => $conteudos,
        ));
    }

    /**
     * Creates a new Conteudo entity.
     *
     * @Route("/new", name="painel_conteudo_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $conteudo = new Conteudo();
        $conteudo->setData(new \DateTime); //data padrao
        
        
        $form = $this->createForm('SiteBundle\Form\ConteudoType', $conteudo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($conteudo);
            $em->flush();

            return $this->redirectToRoute('painel_conteudo_show', array('id' => $conteudo->getId()));
        }

        return $this->render('conteudo/new.html.twig', array(
            'conteudo' => $conteudo,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Conteudo entity.
     *
     * @Route("/{id}", name="painel_conteudo_show")
     * @Method("GET")
     */
    public function showAction(Conteudo $conteudo)
    {
        $deleteForm = $this->createDeleteForm($conteudo);

        return $this->render('conteudo/show.html.twig', array(
            'conteudo' => $conteudo,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Conteudo entity.
     *
     * @Route("/{id}/edit", name="painel_conteudo_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Conteudo $conteudo)
    {
        $deleteForm = $this->createDeleteForm($conteudo);
        $editForm = $this->createForm('SiteBundle\Form\ConteudoType', $conteudo);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($conteudo);
            $em->flush();

            return $this->redirectToRoute('painel_conteudo_edit', array('id' => $conteudo->getId()));
        }

        return $this->render('conteudo/edit.html.twig', array(
            'conteudo' => $conteudo,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Conteudo entity.
     *
     * @Route("/{id}", name="painel_conteudo_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Conteudo $conteudo)
    {
        $form = $this->createDeleteForm($conteudo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($conteudo);
            $em->flush();
        }

        return $this->redirectToRoute('painel_conteudo_index');
    }

    /**
     * Creates a form to delete a Conteudo entity.
     *
     * @param Conteudo $conteudo The Conteudo entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Conteudo $conteudo)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('painel_conteudo_delete', array('id' => $conteudo->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
