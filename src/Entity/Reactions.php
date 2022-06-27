<?php

namespace App\Entity;

use App\Repository\ReactionsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ReactionsRepository::class)
 */
class Reactions
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $htmlReaction;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="reactions")
     */
    private $userId;

    /**
     * @ORM\ManyToOne(targetEntity=Route::class, inversedBy="reactions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $routeId;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHtmlReaction(): ?string
    {
        return $this->htmlReaction;
    }

    public function setHtmlReaction(string $htmlReaction): self
    {
        $this->htmlReaction = $htmlReaction;

        return $this;
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

    public function getRouteId(): ?Route
    {
        return $this->routeId;
    }

    public function setRouteId(?Route $routeId): self
    {
        $this->routeId = $routeId;

        return $this;
    }
}
