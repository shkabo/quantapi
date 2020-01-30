<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EventRepository")
 */
class Event
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $datum;

    /**
     * @ORM\Column(type="string", length=2)
     */
    private $countryCode;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $eventType;

    /**
     * @ORM\Column(type="integer")
     */
    private $ammount;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDatum(): ?\DateTimeInterface
    {
        return $this->datum;
    }

    public function setDatum(\DateTimeInterface $datum): self
    {
        $this->datum = $datum;

        return $this;
    }

    public function getCountryCode(): ?string
    {
        return $this->countryCode;
    }

    public function setCountryCode(string $countryCode): self
    {
        $this->countryCode = $countryCode;

        return $this;
    }

    public function getEventType(): ?string
    {
        return $this->eventType;
    }

    public function setEventType(string $eventType): self
    {
        $this->eventType = $eventType;

        return $this;
    }

    public function getAmmount(): ?int
    {
        return $this->ammount;
    }

    public function setAmmount(int $ammount): self
    {
        $this->ammount = $ammount;

        return $this;
    }
}
