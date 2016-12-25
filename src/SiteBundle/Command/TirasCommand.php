<?php

namespace SiteBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\File\File;

use SiteBundle\Entity\Conteudo;
use SiteBundle\Entity\Categoria;
use ArquivosBundle\Entity\Arquivo;


class TirasCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('tiras:importar')
            ->setDescription('...')
            ->addArgument('tipo', InputArgument::REQUIRED, 'Tipo de Tira (Calvin, Dilbert etc..)')
            ->addArgument('data', InputArgument::REQUIRED, 'Data da Tira')
            ->addArgument('imagem', InputArgument::REQUIRED, 'URL da imagem da Tira')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $tipo       = $input->getArgument('tipo');
        $str_data   = $input->getArgument('data');
        $url_imagem = $input->getArgument('imagem');
        $titulo     = "Tiras {$tipo} {$str_data}";

        $conteudo = new Conteudo();
        // 1) get the real class for the entity with the Doctrine Utility.
        $class = \Doctrine\Common\Util\ClassUtils::getRealClass(get_class(new Conteudo));
        $class_arquivo = \Doctrine\Common\Util\ClassUtils::getRealClass(get_class(new Arquivo));

        // 2) get the manager for that class. 
        $container = $this->getContainer();
        $em = $container->get('doctrine')->getManagerForClass($class);
        $em_arquivo = $container->get('doctrine')->getManagerForClass($class_arquivo);
        
        $conteudo_repository = $container->get('doctrine')->getRepository('SiteBundle:Conteudo');
        $categoria_repository = $container->get('doctrine')->getRepository('SiteBundle:Categoria');
        
        $categoria_tmp  = new Categoria();
        
        $conteudo->setData(new \DateTime());
        $conteudo->setTitulo($titulo);
        $conteudo->setChave($titulo);
        
        $conteudo->setAtivo(1);
        $existe_conteudo = $conteudo_repository->findOneByChave($conteudo->getChave());

        if(!$existe_conteudo)
        {
            $label = $categoria_tmp->formatarChave('tiras');
            $existe_categoria = $categoria_repository->findOneByChave($label);

            if(!$existe_categoria)
            {
                $categoria = new Categoria;
                $categoria->setChave($label);
                $categoria->setNome($label);
                $em->persist($categoria);
                $em->flush();
                $categoria = $categoria_repository->findOneByChave($label);
            } else {
                $categoria = $existe_categoria;
            }
            $conteudo->setCategoria($categoria);
            
            //escrevendo no arquivo temporario
            $temp = tempnam(sys_get_temp_dir(), "tira");
            $handle = fopen($temp, "w");
            fwrite($handle, file_get_contents($url_imagem));
            fclose($handle);

            $arquivo = new File($temp);
            
            $fileName = md5(uniqid()).'.'.$arquivo->guessExtension();
            
            // Move the file to the directory where brochures are stored
            
            
            $arquivo->move(
                $this->getContainer()->getParameter('arquivos_directory'),
                $fileName
            );
            
            $imagem = new Arquivo();
            $imagem->setNome($titulo);
            $imagem->setData(new \DateTime);
            $imagem->setArquivo($fileName);
            
            $em_arquivo->persist($imagem);
            $em_arquivo->flush();

            $url_conteudo = $this->getContainer()->getParameter('base_url')
                    .'arquivos/'
                    .$imagem->getId();
//            $url_conteudo = $this->getContainer()->getParameter('base_url')
//                    .$this->getContainer()->getParameter('arquivos_url')
//                    .$imagem->getArquivo();
            $conteudo->setConteudo("<img src='{$url_conteudo}'>");
            
            $em->persist($conteudo);
            $em->flush();
            $output->writeln("Tira '{$titulo}' cadastrada!");
        } else {
            $output->writeln("Tira '{$titulo}' ja existe!");
        }
    }
}
