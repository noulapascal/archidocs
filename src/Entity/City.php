<?php

namespace App\Entity;

use App\Repository\CityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass=CityRepository::class)
 */
class City
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
    private $description;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $updateAt;

    /**
     * @ORM\ManyToOne(targetEntity=Division::class, inversedBy="cities",cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $division;

    /**
     * @ORM\OneToMany(targetEntity=Subdivision::class, mappedBy="city", orphanRemoval=true)
     */
    private $subdivisions;

    /**
     * @ORM\OneToMany(targetEntity=Company::class, mappedBy="city")
     */
    private $companies;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $createAt;

    /**
     * @ORM\OneToMany(targetEntity=Contact::class, mappedBy="city")
     */
    private $contacts;

    public function __construct()
    {
        $this->subdivisions = new ArrayCollection();
        $this->companies = new ArrayCollection();
        $this->contacts = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getUpdateAt(): ?\DateTimeImmutable
    {
        return $this->updateAt;
    }

    public function setUpdateAt(\DateTimeImmutable $updateAt): self
    {
        $this->updateAt = $updateAt;

        return $this;
    }

    public function getDivision(): ?Division
    {
        return $this->division;
    }

    public function setDivision(?Division $division): self
    {
        $this->division = $division;

        return $this;
    }

    /**
     * @return Collection|Subdivision[]
     */
    public function getSubdivisions(): Collection
    {
        return $this->subdivisions;
    }

    public function addSubdivision(Subdivision $subdivision): self
    {
        if (!$this->subdivisions->contains($subdivision)) {
            $this->subdivisions[] = $subdivision;
            $subdivision->setCity($this);
        }

        return $this;
    }

    public function removeSubdivision(Subdivision $subdivision): self
    {
        if ($this->subdivisions->removeElement($subdivision)) {
            // set the owning side to null (unless already changed)
            if ($subdivision->getCity() === $this) {
                $subdivision->setCity(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Company[]
     */
    public function getCompanies(): Collection
    {
        return $this->companies;
    }

    public function addCompany(Company $company): self
    {
        if (!$this->companies->contains($company)) {
            $this->companies[] = $company;
            $company->setCity($this);
        }

        return $this;
    }

    public function removeCompany(Company $company): self
    {
        if ($this->companies->removeElement($company)) {
            // set the owning side to null (unless already changed)
            if ($company->getCity() === $this) {
                $company->setCity(null);
            }
        }

        return $this;
    }



    public function __toString()
    {
 
     return $this->getName();
    }

    
    public function getCreateAt(): ?\DateTimeImmutable
    {
        return $this->createAt;
    }

    
    
   
    public function setCreateAt(?\DateTimeImmutable $createAt): self
    {
        $this->createAt = $createAt;

        return $this;
    }
    
    
    /**
     * @ORM\PrePersist
     */

    public function setCreateAtValue()

    {
        $this->createAt = new \DateTimeImmutable();
    }
    
    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */

    public function setUpdateAtValue()

    {
        $this->updateAt = new \DateTimeImmutable();
    }

    /**
     * @return Collection|Contact[]
     */
    public function getContacts(): Collection
    {
        return $this->contacts;
    }

    public function addContact(Contact $contact): self
    {
        if (!$this->contacts->contains($contact)) {
            $this->contacts[] = $contact;
            $contact->setCity($this);
        }

        return $this;
    }

    public function removeContact(Contact $contact): self
    {
        if ($this->contacts->removeElement($contact)) {
            // set the owning side to null (unless already changed)
            if ($contact->getCity() === $this) {
                $contact->setCity(null);
            }
        }

        return $this;
    }

}
