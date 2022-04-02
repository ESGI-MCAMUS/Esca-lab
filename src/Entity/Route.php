<?php

namespace App\Entity;

use App\Repository\RouteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RouteRepository::class)
 */
class Route
{
  /**
   * @ORM\Id
   * @ORM\GeneratedValue
   * @ORM\Column(type="integer")
   */
  private $id;

  /**
   * @ORM\Column(type="smallint", nullable=true)
   */
  private $opened;

  /**
   * @ORM\ManyToOne(targetEntity=Gym::class, inversedBy="routes")
   */
  private $gym;

  /**
   * @ORM\Column(type="string", length=255, nullable=true)
   */
  private $name;

  /**
   * @ORM\Column(type="integer", nullable=true)
   */
  private $difficulty;

  /**
   * @ORM\ManyToMany(targetEntity=User::class, mappedBy="routes")
   */
  private $users;

  public function __construct()
  {
    $this->opener = new ArrayCollection();
    $this->users = new ArrayCollection();
  }

  public function getId(): ?int
  {
    return $this->id;
  }

  public function setId(int $id): self
  {
    $this->id = $id;
    return $this;
  }

  public function getOpened(): ?int
  {
    return $this->opened;
  }

  public function setOpened(?int $opened): self
  {
    $this->opened = $opened;

    return $this;
  }

  public function getGym(): ?Gym
  {
    return $this->gym;
  }

  public function setGym(?Gym $gym): self
  {
    $this->gym = $gym;

    return $this;
  }

  public function getName(): ?string
  {
    return $this->name;
  }

  public function setName(?string $name): self
  {
    $this->name = $name;

    return $this;
  }

  public function getDifficulty(): ?int
  {
    return $this->difficulty;
  }

  public function setDifficulty(?int $difficulty): self
  {
    $this->difficulty = $difficulty;

    return $this;
  }

  /**
   * @return Collection<int, User>
   */
  public function getUsers(): Collection
  {
      return $this->users;
  }

  public function addUser(User $user): self
  {
      if (!$this->users->contains($user)) {
          $this->users[] = $user;
          $user->addRoute($this);
      }

      return $this;
  }

  public function removeUser(User $user): self
  {
      if ($this->users->removeElement($user)) {
          $user->removeRoute($this);
      }

      return $this;
  }
}