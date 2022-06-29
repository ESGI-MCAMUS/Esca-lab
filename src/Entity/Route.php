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
   * @ORM\Column(type="string", length="3", nullable=true)
   */
  private $difficulty;

  /**
   * @ORM\ManyToMany(targetEntity=User::class, mappedBy="routes")
   */
  private $users;

  /**
   * @ORM\OneToMany(targetEntity=Message::class, mappedBy="routeId", orphanRemoval=true)
   */
  private $messages;

  /**
   * @ORM\OneToMany(targetEntity=Reactions::class, mappedBy="routeId", orphanRemoval=true)
   */
  private $reactions;

  /**
   * @ORM\Column(type="string", length=255)
   */
  private $picture;

  public function __construct()
  {
    $this->opener = new ArrayCollection();
    $this->users = new ArrayCollection();
    $this->messages = new ArrayCollection();
    $this->reactions = new ArrayCollection();
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

  public function getDifficulty(): ?string
  {
    return $this->difficulty;
  }

  public function setDifficulty(?string $difficulty): self
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

  /**
   * @return Collection<int, Message>
   */
  public function getMessages(): Collection
  {
      return $this->messages;
  }

  public function addMessage(Message $message): self
  {
      if (!$this->messages->contains($message)) {
          $this->messages[] = $message;
          $message->setRouteId($this);
      }

      return $this;
  }

  public function removeMessage(Message $message): self
  {
      if ($this->messages->removeElement($message)) {
          // set the owning side to null (unless already changed)
          if ($message->getRouteId() === $this) {
              $message->setRouteId(null);
          }
      }

      return $this;
  }

  /**
   * @return Collection<int, Reactions>
   */
  public function getReactions(): Collection
  {
      return $this->reactions;
  }

  public function addReaction(Reactions $reaction): self
  {
      if (!$this->reactions->contains($reaction)) {
          $this->reactions[] = $reaction;
          $reaction->setRouteId($this);
      }

      return $this;
  }

  public function removeReaction(Reactions $reaction): self
  {
      if ($this->reactions->removeElement($reaction)) {
          // set the owning side to null (unless already changed)
          if ($reaction->getRouteId() === $this) {
              $reaction->setRouteId(null);
          }
      }

      return $this;
  }

  public function getPicture(): ?string
  {
      return $this->picture;
  }

  public function setPicture(string $picture): self
  {
      $this->picture = $picture;

      return $this;
  }
}