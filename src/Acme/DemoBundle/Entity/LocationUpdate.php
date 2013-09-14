<?php

namespace Acme\DemoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LocationUpdate
 *
 * @ORM\Table(name='location_updates')
 * @ORM\Entity(repositoryClass="Acme\DemoBundle\Entity\LocationUpdateRepository")
 */
class LocationUpdate
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="timestamp", type="integer")
     */
    private $timestamp;

    /**
     * @var float
     *
     * @ORM\Column(name="latitude", type="float")
     */
    private $latitude;

    /**
     * @var float
     *
     * @ORM\Column(name="longitude", type="float")
     */
    private $longitude;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     */
    private $user;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set timestamp
     *
     * @param integer $timestamp
     * @return LocationUpdate
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    /**
     * Get timestamp
     *
     * @return integer
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * Set latitude
     *
     * @param float $latitude
     * @return LocationUpdate
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * Get latitude
     *
     * @return float
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set longitude
     *
     * @param float $longitude
     * @return LocationUpdate
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * Get longitude
     *
     * @return float
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Set user
     *
     * @param User $user
     * @return LocationUpdate
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Get user id
     *
     * @return integer
     */
    public function getUserId()
    {
        return ($this->user)?$this->user->getId():null;
    }

    /**
     * Serialise
     *
     * @return array
     */
    public function serialise()
    {
        return array(
            'id' => $this->getId(),
            'timestamp' => $this->getTimestamp(),
            'latitude' => $this->getLatitude(),
            'longitude' => $this->getLongitude(),
            'user_id' => $this->getUserId(),
        );
    }
}
