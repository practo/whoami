<?php

namespace Acme\DemoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LocationSummary
 *
 * @ORM\Table("location_summaries")
 * @ORM\Entity
 */
class LocationSummary
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="duration_seconds", type="integer")
     */
    private $durationSeconds;

    /**
     * @var string
     *
     * @ORM\Column(name="aggregation_unit", type="string", length=255)
     */
    private $aggregationUnit;

    /**
     * @var integer
     *
     * @ORM\Column(name="start_time", type="integer")
     */
    private $startTime;

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
     * Set name
     *
     * @param string $name
     * @return LocationSummary
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set durationSeconds
     *
     * @param integer $durationSeconds
     * @return LocationSummary
     */
    public function setDurationSeconds($durationSeconds)
    {
        $this->durationSeconds = $durationSeconds;

        return $this;
    }

    /**
     * Get durationSeconds
     *
     * @return integer
     */
    public function getDurationSeconds()
    {
        return $this->durationSeconds;
    }

    /**
     * Set aggregationUnit
     *
     * @param string $aggregationUnit
     * @return LocationSummary
     */
    public function setAggregationUnit($aggregationUnit)
    {
        $this->aggregationUnit = $aggregationUnit;

        return $this;
    }

    /**
     * Get aggregationUnit
     *
     * @return string
     */
    public function getAggregationUnit()
    {
        return $this->aggregationUnit;
    }

    /**
     * Set startTime
     *
     * @param integer $startTime
     * @return LocationSummary
     */
    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;

        return $this;
    }

    /**
     * Get startTime
     *
     * @return integer
     */
    public function getStartTime()
    {
        return $this->startTime;
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
            'name' => $this->getName(),
            'duration_seconds' => $this->getDurationSeconds(),
            'aggregation_unit' => $this->getAggregationUnit(),
            'start_time' => $this->getStartTime(),
            'user_id' => $this->getUserId(),
        );
    }
}
