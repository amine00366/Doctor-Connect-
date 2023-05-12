<?php

namespace App\Entity;

use App\Repository\ConsultationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ConsultationRepository::class)]
class Consultation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("Ordonnances")]

    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'Consultations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Doctor $id_medcin = null;
   
    #[ORM\ManyToOne(inversedBy: 'Consultations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $id_User = null;
    
    #[ORM\Column(length: 255)]
    private ?string $etat_Consultation = null;
    
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_debut = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_fin = null;
   
     
    #[ORM\OneToOne(mappedBy: 'IdConsultation', cascade: ['persist', 'remove'])]
    private ?Fiche $fiche = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdMedcin(): ?Doctor
    {
        return $this->id_medcin;
    }

    public function setIdMedcin(?Doctor $id_medcin): self
    {
        $this->id_medcin = $id_medcin;

        return $this;
    }

    public function getIdUser(): ?User
    {
        return $this->id_User;
    }

    public function setIdUser(?User $id_User): self
    {
        $this->id_User = $id_User;

        return $this;
    }

    public function getEtatConsultation(): ?string
    {
        return $this->etat_Consultation;
    }

    public function setEtatConsultation(string $etat_Consultation): self
    {
        $this->etat_Consultation = $etat_Consultation;

        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->date_debut;
    }

    public function setDateDebut(\DateTimeInterface $date_debut): self
    {
        $this->date_debut = $date_debut;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->date_fin;
    }

    public function setDateFin(\DateTimeInterface $date_fin): self
    {
        $this->date_fin = $date_fin;

        return $this;
    }
    public function __toString() {
        return (string) $this->id;
    }

    public function getFiche(): ?Fiche
    {
        return $this->fiche;
    }

    public function setFiche(?Fiche $fiche): self
    {
        // unset the owning side of the relation if necessary
        if ($fiche === null && $this->fiche !== null) {
            $this->fiche->setIdConsultation(null);
        }

        // set the owning side of the relation if necessary
        if ($fiche !== null && $fiche->getIdConsultation() !== $this) {
            $fiche->setIdConsultation($this);
        }

        $this->fiche = $fiche;

        return $this;
    }
}
