<?php

namespace Acme\DemoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ExecutionContext;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Practo\ApiBundle\Entity\Users.php
 *
 * @ORM\Table(name="users")
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
     * @ORM\Column(name="token", type="string", nullable=true)
     * @var string $token
     */
    protected $token;

    /**
     * @ORM\OneToMany(targetEntity="LocationUpdate")
     */
    protected $locationUpdates;

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

    public function setToken($token)
    {
        $this->token = $token;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function getLocationUpdates()
    {
        return $this->locationUpdates;
    }

    /**
     * @param LocationUpdate $locationUpdate
     */
    public function addLocationUpdate(LocationUpdate $locationUpdate)
    {
        return $this->locationUpdates;
    }

    public function serialise()
    {
        return array(
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'token' => $this->token
        );
    }
}