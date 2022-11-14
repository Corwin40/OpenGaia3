<?php

namespace App\Entity\Admin;

use App\Repository\Admin\TeamRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TeamRepository::class)
 */
class Team
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=25)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity=Member::class, inversedBy="contact")
     */
    private $Anims;

    /**
     * @ORM\Column(type="string", length=14, nullable=true)
     */
    private $contactTel;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $contactMail;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $place;

    /**
     * @ORM\ManyToMany(targetEntity=Structure::class, mappedBy="Team")
     */
    private $structures;

    public function __construct()
    {
        $this->structures = new ArrayCollection();
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

    public function getAnims(): ?Member
    {
        return $this->Anims;
    }

    public function setAnims(?Member $Anims): self
    {
        $this->Anims = $Anims;

        return $this;
    }

    public function getContactTel(): ?string
    {
        return $this->contactTel;
    }

    public function setContactTel(?string $contactTel): self
    {
        $this->contactTel = $contactTel;

        return $this;
    }

    public function getContactMail(): ?string
    {
        return $this->contactMail;
    }

    public function setContactMail(?string $contactMail): self
    {
        $this->contactMail = $contactMail;

        return $this;
    }

    public function getPlace(): ?string
    {
        return $this->place;
    }

    public function setPlace(?string $place): self
    {
        $this->place = $place;

        return $this;
    }

    /**
     * @return Collection|Structure[]
     */
    public function getStructures(): Collection
    {
        return $this->structures;
    }

    public function addStructure(Structure $structure): self
    {
        if (!$this->structures->contains($structure)) {
            $this->structures[] = $structure;
            $structure->addTeam($this);
        }

        return $this;
    }

    public function removeStructure(Structure $structure): self
    {
        if ($this->structures->removeElement($structure)) {
            $structure->removeTeam($this);
        }

        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }
}
