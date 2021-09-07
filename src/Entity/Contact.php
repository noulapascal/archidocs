<?php

namespace App\Entity;

use App\Repository\ContactRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ContactRepository::class)
 */
class Contact
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
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $phoneNumberOne;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $phoneNumberTwo;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $phoneNumberThree;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $Email;

    /**
     * @ORM\ManyToOne(targetEntity=City::class, inversedBy="contacts")
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $address;

    /**
     * @ORM\ManyToMany(targetEntity=Annuary::class, inversedBy="contacts")
     */
    private $annuaire;

    public function __construct()
    {
        $this->annuaire = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPhoneNumberOne(): ?string
    {
        return $this->phoneNumberOne;
    }

    public function setPhoneNumberOne(?string $phoneNumberOne): self
    {
        $this->phoneNumberOne = $phoneNumberOne;

        return $this;
    }

    public function getPhoneNumberTwo(): ?string
    {
        return $this->phoneNumberTwo;
    }

    public function setPhoneNumberTwo(?string $phoneNumberTwo): self
    {
        $this->phoneNumberTwo = $phoneNumberTwo;

        return $this;
    }

    public function getPhoneNumberThree(): ?string
    {
        return $this->phoneNumberThree;
    }

    public function setPhoneNumberThree(?string $phoneNumberThree): self
    {
        $this->phoneNumberThree = $phoneNumberThree;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->Email;
    }

    public function setEmail(?string $Email): self
    {
        $this->Email = $Email;

        return $this;
    }

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setCity(?City $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return Collection|Annuary[]
     */
    public function getAnnuaire(): Collection
    {
        return $this->annuaire;
    }

    public function addAnnuaire(Annuary $annuaire): self
    {
        if (!$this->annuaire->contains($annuaire)) {
            $this->annuaire[] = $annuaire;
        }

        return $this;
    }

    public function removeAnnuaire(Annuary $annuaire): self
    {
        $this->annuaire->removeElement($annuaire);

        return $this;
    }
}
