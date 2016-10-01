<?php

namespace SiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Categoria
 *
 * @ORM\Table(name="categoria")
 * @ORM\Entity(repositoryClass="SiteBundle\Repository\CategoriaRepository")
 */
class Categoria
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
     * @ORM\Column(name="nome", type="string", length=255, unique=true)
     */
    private $nome;

    /**
     * @var string
     *
     * @ORM\Column(name="chave", type="string", length=32, unique=true)
     */
    private $chave;


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
     * Set nome
     *
     * @param string $nome
     *
     * @return Categoria
     */
    public function setNome($nome)
    {
        $this->nome = $nome;

        return $this;
    }

    /**
     * Get nome
     *
     * @return string
     */
    public function getNome()
    {
        return $this->nome;
    }

    /**
     * Set chave
     *
     * @param string $chave
     *
     * @return Categoria
     */
    public function setChave($chave)
    {
        $this->chave = $this->formatarChave($chave);

        return $this;
    }

    /**
     * Get chave
     *
     * @return string
     */
    public function getChave()
    {
        if($this->chave == '')
        {
            $this->chave = $this->formatarChave($this->getNome()); //se nao informar a chave, gerar uma com base no nome
        }
        return $this->chave;
    }
    
    public function formatarChave($chave)
    {
        $chave = preg_replace("/[ ]{1,}/", "-", trim($chave));
        $chave = preg_replace('/[^a-zA-Z0-9\-_]/', '', $chave);
        $chave = preg_replace("/-$/", "", $chave);
        $chave = strtolower(substr($chave, 0, 32));
        return $chave;
    }
    
    /**
     * @ORM\OneToMany(targetEntity="Conteudo", mappedBy="categoria")
     */
    private $conteudos;

    public function __construct()
    {
        $this->conteudos = new ArrayCollection();
    }

    /**
     * Add conteudo
     *
     * @param \SiteBundle\Entity\Conteudo $conteudo
     *
     * @return Categoria
     */
    public function addConteudo(\SiteBundle\Entity\Conteudo $conteudo)
    {
        $this->conteudos[] = $conteudo;

        return $this;
    }

    /**
     * Remove conteudo
     *
     * @param \SiteBundle\Entity\Conteudo $conteudo
     */
    public function removeConteudo(\SiteBundle\Entity\Conteudo $conteudo)
    {
        $this->conteudos->removeElement($conteudo);
    }

    /**
     * Get conteudos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getConteudos()
    {
        return $this->conteudos;
    }
    
    public function getNConteudos()
    {
        return 1;
    }
    
    public function __toString() {
        return $this->getNome();
    }
}
