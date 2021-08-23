<?php

namespace App\Entity;

use App\Repository\CompanyDivisionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Directory;
/**
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass=CompanyDivisionRepository::class)
 */
class CompanyDivision
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Company::class, inversedBy="companyDivisions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $company;

    /**
     * @ORM\ManyToMany(targetEntity=Directory::class, mappedBy="companyDivision")
     */
    private $folders;

    /**
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="division", orphanRemoval=true)
     */
    private $users;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    public function __construct()
    {
        $this->folders = new ArrayCollection();
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): self
    {
        $this->company = $company;

        return $this;
    }

    /**
     * @return Collection|Directory[]
     */
    public function getFolders(): Collection
    {
        return $this->folders;
    }

    public function addFolder(Directory $folder): self
    {
        if (!$this->folders->contains($folder)) {
            $this->folders[] = $folder;
            $folder->addCompanyDivision($this);
        }

        return $this;
    }

    public function removeFolder(Directory $folder): self
    {
        if ($this->folders->removeElement($folder)) {
            $folder->removeCompanyDivision($this);
        }

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setDivison($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getDivison() === $this) {
                $user->setDivison(null);
            }
        }

        return $this;
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

    public function __toString()
    {
    return $this->getName();
    }    

}
