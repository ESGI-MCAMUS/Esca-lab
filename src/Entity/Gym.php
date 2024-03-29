<?php

namespace App\Entity;

use App\Repository\GymRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=GymRepository::class)
 * @ORM\Table(name="`Gym`", schema="public")
 */
class Gym {
  /**
   * @ORM\Id
   * @ORM\GeneratedValue
   * @ORM\Column(type="integer")
   */
  private $id;

  /**
   * @ORM\Column(type="string", length=255)
   */
  private $name;

  /**
   * @ORM\Column(type="bigint", nullable=true)
   */
  private $size;

  /**
   * @ORM\Column(type="string", length=255, nullable=true)
   */
  private $pc;

  /**
   * @ORM\Column(type="string", length=255, nullable=true)
   */
  private $address;

  /**
   * @ORM\Column(type="string", length=255, nullable=true)
   */
  private $city;

  /**
   * @ORM\ManyToOne(targetEntity=Franchise::class, inversedBy="gyms")
   */
  private $franchise;

  /**
   * @ORM\ManyToOne(targetEntity=User::class, inversedBy="gyms")
   */
  private $admin;

  /**
   * @ORM\OneToMany(targetEntity=Route::class, mappedBy="gym")
   */
  private $routes;

  /**
   * @ORM\OneToMany(targetEntity=User::class, mappedBy="gym")
   */
  private $openers;

  /**
   * @ORM\OneToMany(targetEntity=Event::class, mappedBy="gym")
   */
  private $events;

  /**
   * @ORM\Column(type="string", length=255)
   */
  private $picture;

  /**
   * @ORM\OneToMany(targetEntity=Payments::class, mappedBy="gym")
   */
  private $payments;

  /**
   * @ORM\Column(type="datetime", options={"default": "CURRENT_TIMESTAMP"})
   */
  private $created_at;

  /**
   * @ORM\OneToMany(targetEntity=FavoriteGym::class, mappedBy="gymId", orphanRemoval=true)
   */
  private $favoriteGyms;

  public function __construct() {
    $this->routes = new ArrayCollection();
    $this->openers = new ArrayCollection();
    $this->events = new ArrayCollection();
    $this->payments = new ArrayCollection();
    $this->favoriteGyms = new ArrayCollection();
  }

  public function getId(): ?int {
    return $this->id;
  }

  public function setId(int $id): self {
    $this->id = $id;
    return $this;
  }

  public function getName(): ?string {
    return $this->name;
  }

  public function setName(string $name): self {
    $this->name = $name;

    return $this;
  }

  public function getSize(): ?string {
    return $this->size;
  }

  public function setSize(?string $size): self {
    $this->size = $size;

    return $this;
  }

  public function getPc(): ?string {
    return $this->pc;
  }

  public function setPc(?string $pc): self {
    $this->pc = $pc;

    return $this;
  }

  public function getAddress(): ?string {
    return $this->address;
  }

  public function setAddress(?string $address): self {
    $this->address = $address;

    return $this;
  }

  public function getCity(): ?string {
    return $this->city;
  }

  public function setCity(?string $city): self {
    $this->city = $city;

    return $this;
  }

  public function getFranchise(): ?Franchise {
    return $this->franchise;
  }

  public function setFranchise(?Franchise $franchise): self {
    $this->franchise = $franchise;

    return $this;
  }

  public function getAdmin(): ?User {
    return $this->admin;
  }

  public function setAdmin(?User $admin): self {
    $this->admin = $admin;

    return $this;
  }

  /**
   * @return Collection|Route[]
   */
  public function getRoutes(): Collection {
    return $this->routes;
  }

  public function addRoute(Route $route): self {
    if (!$this->routes->contains($route)) {
      $this->routes[] = $route;
      $route->setGym($this);
    }

    return $this;
  }

  public function removeRoute(Route $route): self {
    if ($this->routes->removeElement($route)) {
      // set the owning side to null (unless already changed)
      if ($route->getGym() === $this) {
        $route->setGym(null);
      }
    }

    return $this;
  }

  /**
   * @return Collection|User[]
   */
  public function getOpeners(): Collection {
    return $this->openers;
  }

  public function addOpener(User $opener): self {
    if (!$this->openers->contains($opener)) {
      $this->openers[] = $opener;
      $opener->setGym($this);
    }

    return $this;
  }

  public function removeOpener(User $opener): self {
    if ($this->openers->removeElement($opener)) {
      // set the owning side to null (unless already changed)
      if ($opener->getGym() === $this) {
        $opener->setGym(null);
      }
    }

    return $this;
  }

  /**
   * @return Collection<int, Event>
   */
  public function getEvents(): Collection {
    return $this->events;
  }

  public function addEvent(Event $event): self {
    if (!$this->events->contains($event)) {
      $this->events[] = $event;
      $event->setGym($this);
    }

    return $this;
  }

  public function removeEvent(Event $event): self {
    if ($this->events->removeElement($event)) {
      // set the owning side to null (unless already changed)
      if ($event->getGym() === $this) {
        $event->setGym(null);
      }
    }

    return $this;
  }

  public function getPicture(): ?string {
    return $this->picture;
  }

  public function setPicture(string $picture): self {
    $this->picture = $picture;

    return $this;
  }

  /**
   * @return Collection<int, Payments>
   */
  public function getPayments(): Collection
  {
      return $this->payments;
  }

  public function addPayment(Payments $payment): self
  {
      if (!$this->payments->contains($payment)) {
          $this->payments[] = $payment;
          $payment->setGym($this);
      }

      return $this;
  }

  public function removePayment(Payments $payment): self
  {
      if ($this->payments->removeElement($payment)) {
          // set the owning side to null (unless already changed)
          if ($payment->getGym() === $this) {
              $payment->setGym(null);
          }
      }

      return $this;
  }

  public function getCreatedAt(): ?\DateTimeInterface
  {
      return $this->created_at;
  }

  public function setCreatedAt(\DateTimeInterface $created_at): self
  {
      $this->created_at = $created_at;

      return $this;
  }

  /**
   * @return Collection<int, FavoriteGym>
   */
  public function getFavoriteGyms(): Collection
  {
      return $this->favoriteGyms;
  }

  public function addFavoriteGym(FavoriteGym $favoriteGym): self
  {
      if (!$this->favoriteGyms->contains($favoriteGym)) {
          $this->favoriteGyms[] = $favoriteGym;
          $favoriteGym->setGymId($this);
      }

      return $this;
  }

  public function removeFavoriteGym(FavoriteGym $favoriteGym): self
  {
      if ($this->favoriteGyms->removeElement($favoriteGym)) {
          // set the owning side to null (unless already changed)
          if ($favoriteGym->getGymId() === $this) {
              $favoriteGym->setGymId(null);
          }
      }

      return $this;
  }
}