<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TaskRepository")
 */
class Task
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"worker","taskInfo"})
     */
    private $id;


    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="tasks")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Client", inversedBy="tasks")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\Choice(
     *     choices = { "fiction", "non-fiction" },
     *     message = "Choose a valid genre."
     * )
     * @MaxDepth(2)
     * @Groups({"worker","taskInfo"})
     */
    private $client;

    /**
     * @ORM\Column(type="date")
     * @Assert\NotBlank
     * @Groups({"worker","taskInfo"})
     */
    private $date;

    /**
     * @ORM\Column(type="time")
     * @Groups({"worker","taskInfo"})
     */
    private $startTime;

    /**
     * @ORM\Column(type="time")
     * @Assert\NotBlank
     * @Groups({"worker","taskInfo"})
     */
    private $endTime;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank
     * @Groups({"worker","taskInfo"})
     */
    private $description;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"worker","taskInfo"})
     */
    private $used;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Groups({"worker","taskInfo"})
     */
    private $transportKM;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Period", inversedBy="tasks")
     */
    private $period;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Groups({"taskInfo"})
     */
    private $TotalHours;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Groups({"taskInfo"})
     */
    private $totalCost;

    public function __construct()
    {

    }


    public function getId(): ?int
    {
        return $this->id;
    }


    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getStartTime(): ?\DateTimeInterface
    {
        return $this->startTime;
    }

    public function setStartTime(\DateTimeInterface $startTime): self
    {
        $this->startTime = $startTime;

        return $this;
    }

    public function getEndTime(): ?\DateTimeInterface
    {
        return $this->endTime;
    }

    public function setEndTime(\DateTimeInterface $endTime): self
    {
        $this->endTime = $endTime;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getUsed(): ?string
    {
        return $this->used;
    }

    public function setUsed(?string $used): self
    {
        $this->used = $used;

        return $this;
    }

    public function getTransportKM(): ?float
    {
        return $this->transportKM;
    }

    public function setTransportKM(?float $transportKM): self
    {
        $this->transportKM = $transportKM;

        return $this;
    }

    public function getPeriod(): ?Period
    {
        return $this->period;
    }

    public function setPeriod(?Period $period): self
    {
        $this->period = $period;

        return $this;
    }

    public function getTotalHours(): ?string
    {
        return $this->TotalHours;
    }

    public function setTotalHours(?string $TotalHours): self
    {
        $this->TotalHours = $TotalHours;

        return $this;
    }

    public function getTotalCost(): ?float
    {
        return $this->totalCost;
    }

    public function setTotalCost(?float $totalCost): self
    {
        $this->totalCost = $totalCost;

        return $this;
    }


}
