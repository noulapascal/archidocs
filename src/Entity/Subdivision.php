<?php

namespace App\Entity;

use App\Repository\SubdivisionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SubdivisionRepository::class)
 */
class Subdivision
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
     * @ORM\ManyToOne(targetEntity=City::class, inversedBy="subdivisions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $city;

    /**
     * @ORM\OneToMany(targetEntity=Locality::class, mappedBy="subdivision")
     */
    private $localities;

    public function __construct()
    {
        $this->localities = new ArrayCollection();
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

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setCity(?City $city): self
    {
        $this->city = $city;

        return $this;
    }

    /**
     * @return Collection|Locality[]
     */
    public function getLocalities(): Collection
    {
        return $this->localities;
    }

    public function addLocality(Locality $locality): self
    {
        if (!$this->localities->contains($locality)) {
            $this->localities[] = $locality;
            $locality->setSubdivision($this);
        }

        return $this;
    }

    public function removeLocality(Locality $locality): self
    {
        if ($this->localities->removeElement($locality)) {
            // set the owning side to null (unless already changed)
            if ($locality->getSubdivision() === $this) {
                $locality->setSubdivision(null);
            }
        }

        return $this;
    }
}
