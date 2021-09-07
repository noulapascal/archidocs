<?php

namespace App\Entity;

use App\Repository\FileRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass=FileRepository::class)
 */
class File
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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $extension;

    

    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */

     private $fileType;

     /**
      * @ORM\Column(type="datetime_immutable", nullable=true)
      */
     private $createAt;

     

     /**
      * @ORM\Column(type="integer")
      */
     private $level;

     /**
      * @ORM\ManyToOne(targetEntity=User::class, inversedBy="directories")
      */
     private $author;



     /**
      * @ORM\ManyToOne(targetEntity=Directory::class, inversedBy="childrenFiles")
      */
      private $parentFolder;
     /**
      * @ORM\ManyToMany(targetEntity=User::class, inversedBy="directoriesAccess")
      */
     private $specialAccess;

     /**
      * @ORM\Column(type="string", length=255, nullable=true)
      */
     private $referenceNumber;

     public function __construct()
     {
         $this->companyDivision = new ArrayCollection();
         $this->child = new ArrayCollection();
         $this->specialAccess = new ArrayCollection();
         $this->childrenFiles = new ArrayCollection();
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
        return $this->getName()."";
    }

    public function getParent()
    {
        return $this->parentFolder;
    }

    public function setParent(Directory $parent): self
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

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getSpecialAccess(): Collection
    {
        return $this->specialAccess;
    }

    public function addSpecialAccess(User $specialAccess): self
    {
        if (!$this->specialAccess->contains($specialAccess)) {
            $this->specialAccess[] = $specialAccess;
        }

        return $this;
    }

    public function removeSpecialAccess(User $specialAccess): self
    {
        $this->specialAccess->removeElement($specialAccess);

        return $this;
    }

    /**
     * @return Collection|files[]
     */
    public function getChildrenFiles(): Collection
    {
        return $this->childrenFiles;
    }

    public function addChildrenFile(File $childrenFile): self
    {
        if (!$this->childrenFiles->contains($childrenFile)) {
            $this->childrenFiles[] = $childrenFile;
            $childrenFile->setParentFolder($this);
        }

        return $this;
    }

    public function removeChildrenFile(FIle $childrenFile): self
    {
        if ($this->childrenFiles->removeElement($childrenFile)) {
            // set the owning side to null (unless already changed)
            if ($childrenFile->getParentFolder() === $this) {
                $childrenFile->setParentFolder(null);
            }
        }

        return $this;
    }

    public function getParentFolder(): ?Directory
    {
        return $this->parentFolder;
    }

    public function setParentFolder(?Directory $parentFolder): self
    {
        $this->parentFolder = $parentFolder;

        return $this;
    }

    public function getReferenceNumber(): ?string
    {
        return $this->referenceNumber;
    }

    public function setReferenceNumber(?string $referenceNumber): self
    {
        $this->referenceNumber = $referenceNumber;

        return $this;
    }
}
