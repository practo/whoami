<?php

namespace Acme\DemoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ExecutionContext;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Practo\ApiBundle\Entity\Activity.php
 *
 * @ORM\Table(name="activities")
 * @ORM\Entity()
 * @UniqueEntity(fields={"id"},
 * message="Client settings already exist for the practice")
 */
class Activity
{
    /**
     * @var integer $id
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="start_time", type="string", nullable=true)
     * @var string $startTime
     */
    protected $startTime;

    /**
     * @ORM\Column(name="end_time", type="string", nullable=true)
     * @var string $endTime
     */
    protected $endTime;

    /**
     * @ORM\Column(name="activity", type="string", nullable=true)
     * @var string $activity
     */
    protected $activity;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @var string $user
     */
    protected $user;

    /**
     * @ORM\Column(name="device", type="string", nullable=true)
     * @var string $device
     */
    protected $device;

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getStartTime()
    {
        return $this->startTime;
    }

    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;
    }

    public function getEndTime()
    {
        return $this->endTime;
    }

    public function setEndTime($endTime)
    {
        $this->endTime = $endTime;
    }

    public function setActivity($activity)
    {
        $this->activity = $activity;
    }

    public function getActivity()
    {
        return $this->activity;
    }

    public function setDevice($device)
    {
        $this->device = $device;
    }

    public function getDevice()
    {
        return $this->device;
    }

    public function setUser(User $user)
    {
        $this->user = $user;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function serialise()
    {
        return array(
            'id' => $this->getId(),
            'start_time' => $this->getStartTime(),
            'end_time' => $this->getEndTime(),
            'activity' => $this->getActivity(),
            'device' => $this->getDevice(),
            'user_id' => $this->getUser()->getId()
        );
    }
}