<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BookingRepository")
 */
class Booking
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $spectacle;

    /**
     * @ORM\Column(type="integer")
     */
    private $seatNumber;

    /**
     * @ORM\Column(type="string", length=1)
     */
    private $rang;

    // Getters and Setters
    public function getId(): int
    {
        return $this->id;
    }

    public function getSpectacle(): string
    {
        return $this->spectacle;
    }

    public function setSpectacle(string $spectacle): self
    {
        $this->spectacle = $spectacle;
        return $this;
    }

    public function getSeatNumber(): int
    {
        return $this->seatNumber;
    }

    public function setSeatNumber(int $seatNumber): self
    {
        $this->seatNumber = $seatNumber;
        return $this;
    }

    public function getRang(): string
    {
        return $this->rang;
    }

    public function setRang(string $rang): self
    {
        $this->rang = $rang;
        return $this;
    }
}
