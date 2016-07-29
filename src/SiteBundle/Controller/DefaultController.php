<?php

namespace SiteBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use SiteBundle\Entity\Conteudo;


class DefaultController extends Controller
{
    /**
     * @Route("/", name="site_index")
     * @Route("/pagina/{pagina}", name="site_index_paginacao")
     */
    public function indexAction($pagina = 1)
    {
        $limit = 20;
        $em = $this->getDoctrine()->getManager();
        $conteudos = $em->getRepository('SiteBundle:Conteudo')->getAllConteudo($pagina, $limit); // Returns 5 conteudos out of 20

        // You can also call the count methods (check PHPDoc for `paginate()`)
        # Total fetched (ie: `5` conteudos)
        $totalConteudosRetornados = $conteudos->getIterator()->count();

        # Count of ALL conteudos (ie: `20` conteudos)
        $totalConteudos = $conteudos->count();

        $maxPaginas = ceil($totalConteudos / $limit);
        $estaPagina = $pagina;
        // Pass through the 3 above variables to calculate pages in twig
        return $this->render('site/index.html.twig',array(
                'conteudos' => $conteudos
                ,'maxPaginas' => $maxPaginas
                ,'estaPagina' => $estaPagina
                ,'totalConteudosRetornados' => $totalConteudosRetornados
            )
        );
    }
    /*public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $conteudos = $em->getRepository('SiteBundle:Conteudo')
                ->findBy(
                        array('ativo' => true)
                        ,array('id' => 'DESC')
                    )
                ;

        return $this->render('site/index.html.twig', array(
            'conteudos' => $conteudos,
        ));
    }*/
    
    /**
     * Finds and displays a Conteudo entity.
     *
     * @Route("/{id}", name="site_show")
     * @Route("/conteudo/{chave}", name="site_show")
     * @Method("GET")
     */
    public function showAction(Conteudo $conteudo)
    {
        return $this->render('site/show.html.twig', array(
            'conteudo' => $conteudo
        ));
    }
}
