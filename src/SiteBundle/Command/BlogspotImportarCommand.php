<?php

namespace SiteBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


use SiteBundle\Entity\Conteudo;


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
        
        $em = $this->getDoctrine()->getManager();
        
        $posts = $this->buscar();
        
        foreach($posts as $post)
        {
            $conteudo = new Conteudo();
            $conteudo->setData($post->published);
            $conteudo->setTitulo($post->title);
            $conteudo->setChave($post->title);
            $conteudo->setConteudo($post->content);
            $conteudo->setAtivo(1);
        }

//        $output->writeln('Command result.');
    }
    
    function faixa()
    {
        $inicio = new \DateTime('2016-08-09T00:00:00');
        $agora = new \DateTime;
        $diff = $agora->diff($inicio);
        $x = $diff->days;
        $x = 3;
        $prazo = 365;
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
        
        $url = "https://www.googleapis.com/blogger/v3/blogs/{$this->blogId}/posts?endDate={$faixas['endDate']}&startDate={$faixas['startDate']}&key={$this->key}&fields=items(id,published,title,content)";
        $posts = file_get_contents($url);
        return json_decode($posts);
    }

}
