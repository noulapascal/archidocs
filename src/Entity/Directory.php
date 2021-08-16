<?php

namespace App\Entity;

use App\Repository\DirectoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass=DirectoryRepository::class)
 */
class Directory
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
     * @ORM\Column(type="datetime")
     */
    private $updateAt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $size;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $extension;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $permissions;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $path;

    /**
     * @ORM\ManyToMany(targetEntity=CompanyDivision::class, inversedBy="folders")
     */
    private $companyDivision;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isFIle;

    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */

     private $fileType;

     /**
      * @ORM\Column(type="datetime_immutable", nullable=true)
      */
     private $createAt;

     /**
      * @ORM\ManyToOne(targetEntity=Directory::class, inversedBy="children")
      */
     private $parent;

     /**
      * @ORM\OneToMany(targetEntity=Directory::class, mappedBy="parent")
      */
     private $children;

     /**
      * @ORM\Column(type="integer")
      */
     private $level;

     public function __construct()
     {
         $this->companyDivision = new ArrayCollection();
         $this->children = new ArrayCollection();
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

    public function getUpdateAt(): ?\DateTimeInterface
    {
        return $this->updateAt;
    }

    public function setUpdateAt(\DateTimeInterface $updateAt): self
    {
        $this->updateAt = $updateAt;

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

    public function getSize(): ?string
    {
        return $this->size;
    }

    public function setSize(?string $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getExtension(): ?string
    {
        return $this->extension;
    }

    public function setExtension(?string $extension): self
    {
        $this->extension = $extension;

        return $this;
    }

    public function getPermissions(): ?string
    {
        return $this->permissions;
    }

    public function setPermissions(?string $permissions): self
    {
        $this->permissions = $permissions;

        return $this;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getIsFIle(): ?bool
    {
        return $this->isFIle;
    }

    public function setIsFIle(?bool $isFIle): self
    {
        $this->isFIle = $isFIle;

        return $this;
    }

    public function getFileType(): ?string
    {
        return $this->fileType;
    }

    public function setFileType(?string $fileType): self
    {
        $this->fileType = $fileType;

        return $this;
    }

    /**
     * @return Collection|CompanyDivision[]
     */
    public function getCompanyDivision(): Collection
    {
        return $this->companyDivision;
    }

    public function addCompanyDivision(CompanyDivision $companyDivision): self
    {
        if (!$this->companyDivision->contains($companyDivision)) {
            $this->companyDivision[] = $companyDivision;
        }

        return $this;
    }

    public function removeCompanyDivision(CompanyDivision $companyDivision): self
    {
        $this->companyDivision->removeElement($companyDivision);

        return $this;
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

        if(!empty($this->getParent())){
            $level = $this->getParent()->getLevel();
            $this->setLevel($level + 1);
        }else{
            $this->setLevel(0);
        }
    }
    
    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */

    public function setUpdateAtValue()

    {
        $this->updateAt = new \DateTimeImmutable();
    }

    public function getCreateAt(): ?\DateTimeImmutable
    {
        return $this->createAt;
    }

    public function __toString()
    {
        return $this->getName();
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function addChild(self $child): self
    {
        if (!$this->children->contains($child)) {
            $this->children[] = $child;
            $child->setParent($this);
        }

        return $this;
    }

    public function removeChild(self $child): self
    {
        if ($this->children->removeElement($child)) {
            // set the owning side to null (unless already changed)
            if ($child->getParent() === $this) {
                $child->setParent(null);
            }
        }

        return $this;
    }

    public function getLevel(): ?int
    {
        return $this->level;
    }

    public function setLevel(int $level): self
    {
        $this->level = $level;

        return $this;
    }
}
