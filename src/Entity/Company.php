<?php

namespace App\Entity;

use App\Repository\CompanyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass=CompanyRepository::class)
 * @UniqueEntity(fields={"code"}, message="There is already an account with this username")
 */
class Company
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
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $mailAddress;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $phoneNumber;

    /**
     * @ORM\OneToMany(targetEntity=CompanyDivision::class, mappedBy="company", cascade={"remove"}, orphanRemoval=true)
     */
    private $companyDivisions;

    /**
     * @ORM\ManyToOne(targetEntity=City::class, inversedBy="companies")
     * @ORM\JoinColumn(nullable=false)
     */
    private $city;

    /**
     * @ORM\ManyToOne(targetEntity=Locality::class, inversedBy="companies")
     */
    private $locality;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $createAt;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $updateAt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $acronym;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $code;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $sigle;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $logo;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $banner;

    public function __construct()
    {
        $this->companyDivisions = new ArrayCollection();
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

    public function getMailAddress(): ?string
    {
        return $this->mailAddress;
    }

    public function setMailAddress(?string $mailAddress): self
    {
        $this->mailAddress = $mailAddress;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    /**
     * @return Collection|CompanyDivision[]
     */
    public function getCompanyDivisions(): Collection
    {
        return $this->companyDivisions;
    }

    public function addCompanyDivision(CompanyDivision $companyDivision): self
    {
        if (!$this->companyDivisions->contains($companyDivision)) {
            $this->companyDivisions[] = $companyDivision;
            $companyDivision->setCompany($this);
        }

        return $this;
    }

    public function removeCompanyDivision(CompanyDivision $companyDivision): self
    {
        if ($this->companyDivisions->removeElement($companyDivision)) {
            // set the owning side to null (unless already changed)
            if ($companyDivision->getCompany() === $this) {
                $companyDivision->setCompany(null);
            }
        }

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

    public function getLocality(): ?Locality
    {
        return $this->locality;
    }

    public function setLocality(?Locality $locality): self
    {
        $this->locality = $locality;

        return $this;
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

    public function getUpdateAt(): ?\DateTimeImmutable
    {
        return $this->updateAt;
    }

    public function setUpdateAt(?\DateTimeImmutable $updateAt): self
    {
        $this->updateAt = $updateAt;

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

    public function getAcronym(): ?string
    {
        return $this->acronym;
    }

    public function setAcronym(?string $acronym): self
    {
        $this->acronym = $acronym;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
        }
    public function __toString()
    {
        return $this->getName()    ;    
    }

    public function getSigle(): ?string
    {
        return $this->sigle;
    }

    public function setSigle(?string $sigle): self
    {
        $this->sigle = $sigle;

        return $this;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(?string $logo): self
    {
        $this->logo = $logo;

        return $this;
    }

    public function getBanner(): ?string
    {
        return $this->banner;
    }

    public function setBanner(?string $banner): self
    {
        $this->banner = $banner;

        return $this;
    }

}
