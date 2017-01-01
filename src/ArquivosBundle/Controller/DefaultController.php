<?php

namespace ArquivosBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use ArquivosBundle\Entity\Arquivo;
use Symfony\Component\HttpFoundation\File\File;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;


class DefaultController extends Controller
{
    /**
     * 
     * @Route("/painel/arquivos/", name="painel_arquivo_index")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager('arquivos');

        $arquivos = $em->getRepository('ArquivosBundle:Arquivo')->findBy(array(),  array('id' => 'DESC'));

        return $this->render('arquivos/index.html.twig', array(
            'arquivos' => $arquivos,
        ));
    }
    
    private function enviarArquivo($arquivo)
    {
        $file = $arquivo->getArquivo();

        // Generate a unique name for the file before saving it
        $fileName = md5(uniqid()).'.'.$file->guessExtension();

        // Move the file to the directory where brochures are stored
        $file->move(
            $this->getParameter('arquivos_directory'),
            $fileName
        );
        $arquivo->setArquivo($fileName);
        return $fileName;
    }
    
    /**
     * Creates a new Arquivo entity.
     *
     * @Route("/painel/arquivos/new", name="painel_arquivo_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $arquivo = new Arquivo();
        $arquivo->setData(new \DateTime); //data padrao
        
        $form = $this->createForm('ArquivosBundle\Form\ArquivosType', $arquivo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $this->enviarArquivo($arquivo);
            
            $em = $this->getDoctrine()->getManager('arquivos');
            $em->persist($arquivo);
            $em->flush();

            return $this->redirectToRoute('painel_arquivo_show', array('id' => $arquivo->getId()));
        }

        return $this->render('arquivos/new.html.twig', array(
            'arquivo' => $arquivo,
            'form' => $form->createView(),
        ));
    }
    
    /**
     * Finds and displays a Arquivo entity.
     *
     * @Route("/painel/arquivos/{id}", name="painel_arquivo_show")
     * @Method("GET")
     */
    public function showAction(Arquivo $arquivo)
    {
        $deleteForm = $this->createDeleteForm($arquivo);

        return $this->render('arquivos/show.html.twig', array(
            'arquivo' => $arquivo,
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Finds and displays a Arquivo file.
     *
     * @Route("/arquivos/{id}", name="arquivo_show")
     * @Method("GET")
     */
    public function showFileAction(Arquivo $arquivo)
    {
        if($arquivo){
//            $file = new File($this->getParameter('arquivos_directory').'/'.$arquivo->getArquivo());
//            header('Content-Type: '.$file->getMimeType());
////            header('Content-Disposition: attachment; filename="'.$arquivo->getNome().'";'); //forçar download
//            header('Content-Transfer-Encoding: binary');
//            header('Content-Length: '.$file->getSize());
//            readfile($this->getParameter('arquivos_directory').'/'.$arquivo->getArquivo());
            
            $fileContent = readfile($this->getParameter('arquivos_directory').'/'.$arquivo->getArquivo());
            $response = new Response($fileContent);

            $disposition = $response->headers->makeDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                $arquivo->getNome()
            );

            $response->headers->set('Content-Disposition', $disposition);
            return $response;
        }
    }

    /**
     * Displays a form to edit an existing Arquivo entity.
     *
     * @Route("/painel/arquivos/{id}/edit", name="painel_arquivo_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Arquivo $arquivo)
    {
        $arquivo->src = $arquivo->getArquivo();//guarda o nome atual do arquivo, antes de alterar abaixo
        $arquivo->setArquivo(
            new File($this->getParameter('arquivos_directory').'/'.$arquivo->getArquivo())
        );
        $deleteForm = $this->createDeleteForm($arquivo);
        $editForm = $this->createForm('ArquivosBundle\Form\ArquivosType', $arquivo);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            unlink($this->getParameter('arquivos_directory').'/'.$arquivo->src);
            $this->enviarArquivo($arquivo);
            $em = $this->getDoctrine()->getManager('arquivos');
            $em->persist($arquivo);
            $em->flush();

            return $this->redirectToRoute('painel_arquivo_edit', array('id' => $arquivo->getId()));
        }

        
        return $this->render('arquivos/edit.html.twig', array(
            'arquivo' => $arquivo,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    
    /**
     * Creates a form to delete a Arquivo entity.
     *
     * @param Arquivo $arquivo The Arquivo entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Arquivo $arquivo)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('painel_arquivo_delete', array('id' => $arquivo->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
    /**
     * Deletes a Arquivo entity.
     *
     * @Route("/painel/arquivos/{id}", name="painel_arquivo_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Arquivo $arquivo)
    {
        $form = $this->createDeleteForm($arquivo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            unlink($this->getParameter('arquivos_directory').'/'.$arquivo->getArquivo());
            $em = $this->getDoctrine()->getManager('arquivos');
            $em->remove($arquivo);
            $em->flush();
        }

        return $this->redirectToRoute('painel_arquivo_index');
    }
}
