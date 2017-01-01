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
    public function indexAction($pagina = 1, $categoria = '', $pesquisar = false)
    {
        if($categoria == '')
        {
            $categoria = $this->parseSubdomain();
        }
        
        $limite = 20;
        $dados = $this->getConteudo($pagina, $limite, $categoria, $pesquisar);
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
                'pagina' => 'index'
                ,'conteudos' => $conteudos
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
    private function getConteudo($pagina = 1, $limite = 20, $categoria = '', $pesquisar = false){
        $em = $this->getDoctrine()->getManager();
        $conteudos = $em->getRepository('SiteBundle:Conteudo')->getAllConteudo($pagina, $limite, $categoria, $pesquisar); // Returns 5 conteudos out of 20
        $categorias = $em->getRepository('SiteBundle:Categoria')->findAllOrderByName();
        
        $arr_cat = array();
        foreach($categorias as $categoria)
        {
            if($categoria->getNConteudos() > 0)
            {
                $arr_cat[] = $categoria;
            }
        }
        
        return array(
                 'conteudos' => $conteudos
                , 'categorias' => $arr_cat
            );
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
        $categorias[] = $conteudo->getCategoria();
        $categorias[0]->getNome(); //apenas para "hidratar" a entidade categoria
        return $this->render('site/show.html.twig', array(
            'pagina' => 'artigo'
            ,'conteudo' => $conteudo
            ,'categorias' => $categorias
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
     * @Route("/buscar", name="buscar")
     */
    public function buscarAction(Request $request)
    {
        $pesquisar = $request->query->get('palavra_chave');
        
        return $this->indexAction($pagina = 1, '', $pesquisar);
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
                    ,'phpdev'
                    ,'pro'
                    ,'br'
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
    
    /**
     * Vota em um conteudo.
     *
     * o parametro $metodo serve para que, no caso de robos acessarem o link, nao ocorra o voto.
     * @Route("votar/{id}/{metodo}", name="conteudo_votar")
     * @Method("GET")
     */
    public function votarAction(Conteudo $conteudo, $metodo='')
    {
        if($metodo!='ajax')
        {
            die;
        }
        $conteudo->votar();
        $em = $this->getDoctrine()->getManager();
        $em->persist($conteudo);
        $em->flush();
        
        return $this->render('site/artigo_voto.html.twig',array(
                'conteudo' => $conteudo
            )
        );
    }
    
    /**
     * @Route("/sobre", name="sobre")
     */
    public function sobreAction()
    {
        return $this->render('site/sobre.html.twig');
    }
    
    /**
     * @Route("/sobre_o_site", name="sobre_o_site")
     */
    public function siteAction()
    {
        return $this->render('site/sobre_o_site.html.twig');
    }
}
