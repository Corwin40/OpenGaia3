<?php

namespace App\Entity\Admin;

use App\Repository\Admin\ParrainageRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ParrainageRepository::class)
 */
class Parrainage
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Member::class, inversedBy="parraingesAuthor")
     */
    private $author;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $parrainageFirstname;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $parrainageLastname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $parrainageEmail;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isCotisation = false;

    /**
     * @ORM\Column(type="string", length=14, nullable=true)
     */
    private $phoneDesk;

    /**
     * @ORM\Column(type="string", length=14, nullable=true)
     */
    private $phoneGsm;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nameSociety;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $firstMeeting;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $secondMeeting;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAuthor(): ?Member
    {
        return $this->author;
    }

    public function setAuthor(?Member $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getParrainageFirstname(): ?string
    {
        return $this->parrainageFirstname;
    }

    public function setParrainageFirstname(?string $parrainageFirstname): self
    {
        $this->parrainageFirstname = $parrainageFirstname;

        return $this;
    }

    public function getParrainageLastname(): ?string
    {
        return $this->parrainageLastname;
    }

    public function setParrainageLastname(?string $parrainageLastname): self
    {
        $this->parrainageLastname = $parrainageLastname;

        return $this;
    }

    public function getParrainageEmail(): ?string
    {
        return $this->parrainageEmail;
    }

    public function setParrainageEmail(string $parrainageEmail): self
    {
        $this->parrainageEmail = $parrainageEmail;

        return $this;
    }

    public function getIsCotisation(): ?bool
    {
        return $this->isCotisation;
    }

    public function setIsCotisation(bool $isCotisation): self
    {
        $this->isCotisation = $isCotisation;

        return $this;
    }

    public function getPhoneDesk(): ?string
    {
        return $this->phoneDesk;
    }

    public function setPhoneDesk(?string $phoneDesk): self
    {
        $this->phoneDesk = $phoneDesk;

        return $this;
    }

    public function getPhoneGsm(): ?string
    {
        return $this->phoneGsm;
    }

    public function setPhoneGsm(?string $phoneGsm): self
    {
        $this->phoneGsm = $phoneGsm;

        return $this;
    }

    public function getNameSociety(): ?string
    {
        return $this->nameSociety;
    }

    public function setNameSociety(string $nameSociety): self
    {
        $this->nameSociety = $nameSociety;

        return $this;
    }

    public function getFirstMeeting(): ?\DateTimeInterface
    {
        return $this->firstMeeting;
    }

    public function setFirstMeeting(?\DateTimeInterface $firstMeeting): self
    {
        $this->firstMeeting = $firstMeeting;

        return $this;
    }

    public function getSecondMeeting(): ?\DateTimeInterface
    {
        return $this->secondMeeting;
    }

    public function setSecondMeeting(?\DateTimeInterface $secondMeeting): self
    {
        $this->secondMeeting = $secondMeeting;

        return $this;
    }
}
