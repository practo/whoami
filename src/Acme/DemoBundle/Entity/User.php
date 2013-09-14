<?php

namespace Acme\DemoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ExecutionContext;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Practo\ApiBundle\Entity\User.php
 *
 * @ORM\Table(name="user")
 * @ORM\Entity()
 * @UniqueEntity(fields={"id"},
 * message="Client settings already exist for the practice")
 */
class User
{
	/**
     * @var integer $id
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="name", type="string", nullable=true)
     * @var string $name
     */
    protected $name;

    /**
     * @ORM\Column(name="email", type="string", nullable=true)
     * @var string $email
     */
    protected $email;

    /**
     * @ORM\Column(name="key", type="string", nullable=true)
     * @var string $key
     */
    protected $key;

    public function setId($id)
    {
    	$this->id = $id;
    }

    public function getId()
    {
    	return $this->id;
    }

    public function getName()
    {
    	return $this->name;
    }

    public function setName($name)
    {
    	$this->name = $name;
    }

    public function getEmail()
    {
    	return $this->email;
    }

    public function setEmail($email)
    {
    	$this->email = $email;
    }

    public function setKey($key)
    {
    	$this->key = $key;
    }

    public function getKey()
    {
    	return $this->key;
    }

    public function serialise()
    {
    	return array('id' => $this->id);
    }
}