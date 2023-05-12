<?php

namespace App\Entity;

use App\Repository\OrdonnanceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: OrdonnanceRepository::class)]
class Ordonnance
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("Ordonnances")]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups("Ordonnances")]

    private ?string $frequence = null;

    #[ORM\Column(length: 255)] 
    #[Groups("Ordonnances")]

    private ?string $dose = null;
    #[ORM\Column(length: 255)] 
    private String $Nom_Medicament;
    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]

    private ?Consultation $id_Consultation = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]

    #[Assert\Type("\DateTimeInterface")]
#[Assert\GreaterThanOrEqual(value: "today", message: "La date de création doit être supérieure ou égale à la date d'aujourd'hui.")]
#[Groups("Ordonnances")]

private ?\DateTimeInterface $date_creation = null;

  


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFrequence(): ?string
    {
        return $this->frequence;
    }

    public function setFrequence(string $frequence): self
    {
        $this->frequence = $frequence;

        return $this;
    }

    public function __toString(){
        return (string) $this->id;
    }
    public function getDose(): ?string
    {
        return $this->dose;
    }

    public function setDose(string $dose): self
    {
        $this->dose = $dose;

        return $this;
    }

    public function getIdConsultation(): ?Consultation
    {
        return $this->id_Consultation;
    }

    public function setIdConsultation(Consultation $id_Consultation): self
    {
        $this->id_Consultation = $id_Consultation;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->date_creation;
    }

    public function setDateCreation(\DateTimeInterface $date_creation): self
    {
        $this->date_creation = $date_creation;

        return $this;
    }

   
    public function getNomMedicament(): String
    {
        return $this->Nom_Medicament;
    }

    public function setNomMedicament(String $Nom_Medicament): self
    {
            $this->Nom_Medicament= $Nom_Medicament;
        

        return $this;
    }
}
