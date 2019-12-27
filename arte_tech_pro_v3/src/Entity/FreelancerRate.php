<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FreelancerRateRepository")
 */
class FreelancerRate
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="float")
     */
    private $hourRate;

    /**
     * @ORM\Column(type="float")
     */
    private $TransportCost;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getHourRate(): ?float
    {
        return $this->hourRate;
    }

    public function setHourRate(float $hourRate): self
    {
        $this->hourRate = $hourRate;

        return $this;
    }

    public function getTransportCost(): ?float
    {
        return $this->TransportCost;
    }

    public function setTransportCost(float $TransportCost): self
    {
        $this->TransportCost = $TransportCost;

        return $this;
    }
}
