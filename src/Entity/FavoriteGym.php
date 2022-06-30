<?php

namespace App\Entity;

use App\Repository\FavoriteGymRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FavoriteGymRepository::class)
 */
class FavoriteGym
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="favoriteGyms")
     * @ORM\JoinColumn(nullable=false)
     */
    private $userId;

    /**
     * @ORM\ManyToOne(targetEntity=Gym::class, inversedBy="favoriteGyms")
     * @ORM\JoinColumn(nullable=false)
     */
    private $gymId;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?User
    {
        return $this->userId;
    }

    public function setUserId(?User $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    public function getGymId(): ?Gym
    {
        return $this->gymId;
    }

    public function setGymId(?Gym $gymId): self
    {
        $this->gymId = $gymId;

        return $this;
    }
}
