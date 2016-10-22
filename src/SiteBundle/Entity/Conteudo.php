<?php

namespace SiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Conteudo
 *
 * @ORM\Table(name="conteudo")
 * @ORM\Entity(repositoryClass="SiteBundle\Repository\ConteudoRepository")
 */
class Conteudo
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="titulo", type="string", length=255, unique=true)
     */
    private $titulo;

    /**
     * @var string
     *
     * @ORM\Column(name="conteudo", type="text")
     */
    private $conteudo;

    /**
     * @var bool
     *
     * @ORM\Column(name="ativo", type="boolean")
     */
    private $ativo;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="data", type="datetimetz")
     */
    private $data;

    
    /**
     * @var string
     *
     * @ORM\Column(name="chave", type="string", length=32, unique=true)
     */
    private $chave;
    
    public function getChave()
    {
        return $this->chave;
    }
    
    public function setChave($chave){
        $this->chave = $this->formatarChave($chave);

        return $this;
    }
    
    private function formatarChave($chave)
    {
        $chave = strtolower($chave);
        $chave = preg_replace("/[ ]{1,}/", "-", trim($chave));
        $chave = preg_replace('/[^a-z0-9\-_]/', '', $chave);
        $chave = preg_replace("/-$/", "", $chave);
        $chave = substr($chave, 0, 32);
        return $chave;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set titulo
     *
     * @param string $titulo
     *
     * @return Conteudo
     */
    public function setTitulo($titulo)
    {
        $this->titulo = $titulo;

        return $this;
    }

    /**
     * Get titulo
     *
     * @return string
     */
    public function getTitulo()
    {
        return $this->titulo;
    }

    /**
     * Set conteudo
     *
     * @param string $conteudo
     *
     * @return Conteudo
     */
    public function setConteudo($conteudo)
    {
        $this->conteudo = $conteudo;

        return $this;
    }

    /**
     * Get conteudo
     *
     * @return string
     */
    public function getConteudo()
    {
        if ($this->conteudo != '')
        {
            return stream_get_contents($this->conteudo);
        }
    }

    /**
     * Set ativo
     *
     * @param boolean $ativo
     *
     * @return Conteudo
     */
    public function setAtivo($ativo)
    {
        $this->ativo = $ativo;

        return $this;
    }

    /**
     * Get ativo
     *
     * @return bool
     */
    public function getAtivo()
    {
        return $this->ativo;
    }

    /**
     * Set data
     *
     * @param \DateTime $data
     *
     * @return Conteudo
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get data
     *
     * @return \DateTime
     */
    public function getData()
    {
        return $this->data;
    }
    
    /**
     * @ORM\ManyToOne(targetEntity="Categoria", inversedBy="conteudo")
     * @ORM\JoinColumn(name="categoria", referencedColumnName="id")
     */
    private $categoria;

    /**
     * Set categoria
     *
     * @param \SiteBundle\Entity\Categoria $categoria
     *
     * @return Conteudo
     */
    public function setCategoria(\SiteBundle\Entity\Categoria $categoria = null)
    {
        $this->categoria = $categoria;

        return $this;
    }

    /**
     * Get categoria
     *
     * @return \SiteBundle\Entity\Categoria
     */
    public function getCategoria()
    {
        return $this->categoria;
    }
    
    /**
     * @var int
     *
     * @ORM\Column(name="votos", type="integer", nullable=true)
     */
    private $votos;
    
    /**
     * Get votos
     *
     * @return int
     */
    public function getVotos()
    {
        return $this->votos;
    }

    /**
     * Votar
     *
     * @param string $titulo
     *
     * @return Conteudo
     */
    public function votar()
    {
        $this->votos++;

        return $this->votos;
    }
    
}
