<?php

namespace SiteBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use SiteBundle\Entity\Conteudo;
use SiteBundle\Entity\Categoria;


class DefaultController extends Controller
{
    /**
     * @Route("/", name="site_index")
     * @Route("/pagina/{pagina}", name="site_index_paginacao")
     * @Route("/categoria/{categoria}", name="site_index_categoria")
     */
    public function indexAction($pagina = 1, $categoria = '')
    {
        if($categoria == '')
        {
            $categoria = $this->parseSubdomain();
        }
        
        $limite = 20;
        $dados = $this->getConteudo($pagina, $limite, $categoria);
        $conteudos = $dados['conteudos'];
        $categorias = $dados['categorias'];

        // You can also call the count methods (check PHPDoc for `paginate()`)
        # Total fetched (ie: `5` conteudos)
        $totalConteudosRetornados = $conteudos->getIterator()->count();

        # Count of ALL conteudos (ie: `20` conteudos)
        $totalConteudos = $conteudos->count();

        $maxPaginas = ceil($totalConteudos / $limite);
        $estaPagina = $pagina;
        
        return $this->render('site/index.html.twig',array(
                'conteudos' => $conteudos
                ,'categorias' => $categorias
                ,'categoria' => $categoria
                ,'maxPaginas' => $maxPaginas
                ,'estaPagina' => $estaPagina
                ,'totalConteudosRetornados' => $totalConteudosRetornados
            )
        );
    }
    /*
     * busca o conteudo
     */
    private function getConteudo($pagina = 1, $limite = 20, $categoria = ''){
        $em = $this->getDoctrine()->getManager();
        $conteudos = $em->getRepository('SiteBundle:Conteudo')->getAllConteudo($pagina, $limite, $categoria); // Returns 5 conteudos out of 20
        $categorias = $em->getRepository('SiteBundle:Categoria')->findAllOrderByName();
        
        $arr_cat = array();
        foreach($categorias as $categoria)
        {
            if($categoria->getNConteudos() > 0)
            {
                $arr_cat[] = $categoria;
            }
        }
        
        return array('conteudos' => $conteudos, 'categorias' => $arr_cat);
    }
    
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
    /**
     * @Route("/feed", name="feed_default")
     * @Route("/feed/{categoria}", name="feed_categoria")
     * @Route("/rss", name="feed_rss_default")
     * @Route("/rss/{categoria}", name="feed_rss_categoria")
     */
    public function feedAction($categoria = '')
    {
        $pagina = 1;
        $limite = 50;
        $dados = $this->getConteudo($pagina, $limite, $categoria);
        $conteudos = $dados['conteudos'];
        
        return $this->render('site/feed.xml.twig',array(
                'conteudos' => $conteudos
            )
        );
    }
    
    /**
     * verifica se o site foi chamado atraves de um subdominio
     * se foi, retorna o subdominio, para usá-lo como filtro de categoria
     * @return string
     */
    private function parseSubdomain()
    {
        $request = Request::createFromGlobals();
        $host = str_replace(
                array(
                    'www'
                    ,'.phpdev'
                    ,'.pro'
                    ,'.br'
                )
                , ''
                , $request->server->get('HTTP_HOST')
            );
        
        $arr = explode('.',$host);
        if(count($arr) < 2 || $arr[0] == 'www')
        {
            return '';
        } else {
            return $arr[0];
        }
    }
}
