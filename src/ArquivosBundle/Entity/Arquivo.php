<?php

namespace ArquivosBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Arquivo
 *
 * @ORM\Table(name="arquivo")
 * @ORM\Entity(repositoryClass="ArquivosBundle\Repository\ArquivoRepository")
 */
class Arquivo
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
     * @ORM\Column(name="nome", type="string", length=255)
     */
    private $nome;

    /**
     * @var string
     *
     * @ORM\Column(name="arquivo", type="string", length=255)
     */
    private $arquivo;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="data", type="datetimetz")
     */
    private $data;
    
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
     * Set string
     *
     * @param string $nome
     *
     * @return Arquivo
     */
    public function setNome($nome)
    {
        $this->nome = $nome;

        return $this;
    }

    /**
     * Get string
     *
     * @return string
     */
    public function getNome()
    {
        return $this->nome;
    }

    /**
     * Set arquivo
     *
     * @param string $arquivo
     *
     * @return Arquivo
     */
    public function setArquivo($arquivo)
    {
        $this->arquivo = $arquivo;

        return $this;
    }

    /**
     * Get arquivo
     *
     * @return string
     */
    public function getArquivo()
    {
        return $this->arquivo;
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
}

