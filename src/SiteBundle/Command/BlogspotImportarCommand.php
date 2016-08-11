<?php

namespace SiteBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


use SiteBundle\Entity\Conteudo;
use SiteBundle\Entity\Categoria;
use SiteBundle\Repository\CategoriaRepository;


class BlogspotImportarCommand extends ContainerAwareCommand
{
    private $key = 'AIzaSyBW9cdpKSfwQVKdiP_XRFnA9IRqDprZGfY';
    private $blogId = 3385314853323526342;
    
    protected function configure()
    {
        $this
            ->setName('blogspot:importar')
            ->setDescription('...')
            ->addArgument('argument', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /*$argument = $input->getArgument('argument');

        if ($input->getOption('option')) {
            // ...
        }*/
        
        $conteudo = new Conteudo();
        // 1) get the real class for the entity with the Doctrine Utility.
        $class = \Doctrine\Common\Util\ClassUtils::getRealClass(get_class(new Conteudo));
        unset($conteudo);

        // 2) get the manager for that class. 
        $container = $this->getContainer();
        $em = $container->get('doctrine')->getManagerForClass($class);
        
        $conteudo_repository = $container->get('doctrine')->getRepository('SiteBundle:Conteudo');  
        $categoria_repository = $container->get('doctrine')->getRepository('SiteBundle:Categoria');  
        
        $posts = $this->buscar();
        
        $encontrados = 0;
        $categorias = 0;
        $conteudos = 0;
        foreach($posts->items as $post)
        {
            $encontrados++;
            $data = new \DateTime($post->published);
            
            $conteudo = new Conteudo();
            $categoria_tmp = new Categoria();
            
            $conteudo->setData($data);
            $conteudo->setTitulo($post->title);
            $conteudo->setChave($post->title);
            $conteudo->setConteudo($post->content);
            $conteudo->setAtivo(1);
            
            $existe_conteudo = $conteudo_repository->findOneByChave($conteudo->getChave());
            
            if(!$existe_conteudo)
            {
                foreach($post->labels as $label)
                {
                    $label = $categoria_tmp->formatarChave($label);
                    $existe_categoria = $categoria_repository->findOneByChave($label);

                    if(!$existe_categoria)
                    {
                        $categoria = new Categoria;
                        $categoria->setChave($label);
                        $categoria->setNome($label);
                        $em->persist($categoria);
                        $em->flush();
                        $categorias++;
                        $categoria = $categoria_repository->findOneByChave($label);
                    } else {
                        $categoria = $existe_categoria;
                    }
                }
                
                $conteudo->setCategoria($categoria);
                $em->persist($conteudo);
                $em->flush();
                $conteudos++;
            }
            unset($conteudo);
            unset($categoria);
        }

        $output->writeln("Encontrados: {$encontrados}, cadastrados: {$conteudos}");
    }
    
    function faixa()
    {
        $inicio = new \DateTime('2016-08-11T00:00:00');
        $agora = new \DateTime;
        $diff = $agora->diff($inicio);
        $x = $diff->days;
        $prazo = 90;
        $dias = $prazo * $x;
        
        $faixa_inicio = new \DateTime;
        $faixa_inicio->setTime(0, 0, 0);
        $faixa_inicio->sub(new \DateInterval("P{$dias}D"));
        $prazo++;
        
        $faixa_fim = clone $faixa_inicio;
        $faixa_fim->setTime(23, 59, 59);
        $faixa_fim->sub(new \DateInterval("P{$prazo}D"));
        
        $retorno['endDate']  = $faixa_inicio->format('Y-m-d\TH:i:sP');
        $retorno['startDate']     = $faixa_fim->format('Y-m-d\TH:i:sP');
                //2014-02-21T16:36:00-08:00
        return $retorno;
    }
    
    protected function buscar(){
        
        $faixas = $this->faixa();
        $faixas['endDate'] = urlencode($faixas['endDate']);
        $faixas['startDate'] = urlencode($faixas['startDate']);
        $url = "https://www.googleapis.com/blogger/v3/blogs/{$this->blogId}/posts?endDate={$faixas['endDate']}&startDate={$faixas['startDate']}&key={$this->key}&fields=items(id,published,title,content,labels)";
        echo "importando de {$url}:";
        $posts = file_get_contents($url);
        return json_decode($posts);
    }

}
