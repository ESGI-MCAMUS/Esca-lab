<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EventRepository::class)
 */
class Event
{
  /**
   * @ORM\Id
   * @ORM\GeneratedValue
   * @ORM\Column(type="integer")
   */
  private $id;

  /**
   * @ORM\Column(type="string", length=128)
   */
  private $title;

  /**
   * @ORM\Column(type="text", nullable=true)
   */
  private $description;

  /**
   * @ORM\Column(type="datetime")
   */
  private $eventDate;

  /**
   * @ORM\Column(type="datetime")
   */
  private $endDate;

  /**
   * @ORM\ManyToOne(targetEntity=Gym::class, inversedBy="events")
   * @ORM\JoinColumn(nullable=false)
   */
  private $gym;

  /**
   * @ORM\ManyToMany(targetEntity=User::class, inversedBy="events")
   */
  private $participants;

  public function __construct()
  {
    $this->participants = new ArrayCollection();
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

  public function getTitle(): ?string
  {
    return $this->title;
  }

  public function setTitle(string $title): self
  {
    $this->title = $title;

    return $this;
  }

  public function getDescription(): ?string
  {
    return $this->description;
  }

  public function setDescription(?string $description): self
  {
    $this->description = $description;

    return $this;
  }

  public function getEventDate(): ?\DateTimeInterface
  {
    return $this->eventDate;
  }

  public function setEventDate(\DateTimeInterface $eventDate): self
  {
    $this->eventDate = $eventDate;

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

  /**
   * @return Collection<int, User>
   */
  public function getParticipants(): Collection
  {
    return $this->participants;
  }

  public function addParticipant(User $participant): self
  {
    if (!$this->participants->contains($participant)) {
      $this->participants[] = $participant;
    }

    return $this;
  }

  public function removeParticipant(User $participant): self
  {
    $this->participants->removeElement($participant);

    return $this;
  }

  public function getEndDate(): ?\DateTimeInterface
  {
    return $this->endDate;
  }

  public function setEndDate(\DateTimeInterface $endDate): self
  {
    $this->endDate = $endDate;

    return $this;
  }
}