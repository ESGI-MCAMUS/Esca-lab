<?php

namespace App\Entity;

use App\Repository\FranchiseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FranchiseRepository::class)
 * @ORM\Table(name="`Franchise`", schema="public")
 */
class Franchise {
  /**
   * @var int
   *
   * @ORM\Column(name="id", type="integer", nullable=false)
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="SEQUENCE")
   * @ORM\SequenceGenerator(sequenceName="Franchise_id_seq", allocationSize=1, initialValue=1)
   */
  private $id;

  /**
   * @var int|null
   *
   * @ORM\Column(name="admin", type="integer", nullable=true)
   */
  private $admin;

  /**
   * @ORM\OneToMany(targetEntity=User::class, mappedBy="franchise")
   */
  private $users;

  /**
   * @ORM\Column(type="string", length=255)
   */
  private $name;

  /**
   * @ORM\OneToMany(targetEntity=Gym::class, mappedBy="franchise")
   */
  private $gyms;

  /**
   * @ORM\OneToMany(targetEntity=Payments::class, mappedBy="franchise", orphanRemoval=true)
   */
  private $payments;

  /**
   * @ORM\Column(type="string", length=255)
   */
  private $picture;

  public function __construct() {
    $this->users = new ArrayCollection();
    $this->gyms = new ArrayCollection();
    $this->payments = new ArrayCollection();
  }

  public function getId(): ?int {
    return $this->id;
  }

  public function setId(int $id): self {
    $this->id = $id;
    return $this;
  }

  public function getAdmin(): ?int {
    return $this->admin;
  }

  public function setAdmin(?int $admin): self {
    $this->admin = $admin;

    return $this;
  }

  /**
   * @return Collection|User[]
   */
  public function getUsers(): Collection {
    return $this->users;
  }

  public function addUser(User $user): self {
    if (!$this->users->contains($user)) {
      $this->users[] = $user;
      $user->setFranchise($this);
    }

    return $this;
  }

  public function removeUser(User $user): self {
    if ($this->users->removeElement($user)) {
      // set the owning side to null (unless already changed)
      if ($user->getFranchise() === $this) {
        $user->setFranchise(null);
      }
    }

    return $this;
  }

  public function getName(): ?string {
    return $this->name;
  }

  public function setName(string $name): self {
    $this->name = $name;

    return $this;
  }

  /**
   * @return Collection|Gym[]
   */
  public function getGyms(): Collection {
    return $this->gyms;
  }

  public function addGym(Gym $gym): self {
    if (!$this->gyms->contains($gym)) {
      $this->gyms[] = $gym;
      $gym->setFranchise($this);
    }

    return $this;
  }

  public function removeGym(Gym $gym): self {
    if ($this->gyms->removeElement($gym)) {
      // set the owning side to null (unless already changed)
      if ($gym->getFranchise() === $this) {
        $gym->setFranchise(null);
      }
    }
  }

  public function getNumberOfGyms(): int {
    return sizeof($this->gyms);
  }

  /**
   * @return Collection<int, Payments>
   */
  public function getPayments(): Collection {
    return $this->payments;
  }

  public function addPayment(Payments $payment): self {
    if (!$this->payments->contains($payment)) {
      $this->payments[] = $payment;
      $payment->setFranchise($this);
    }

    return $this;
  }

  public function removePayment(Payments $payment): self {
    if ($this->payments->removeElement($payment)) {
      // set the owning side to null (unless already changed)
      if ($payment->getFranchise() === $this) {
        $payment->setFranchise(null);
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
}