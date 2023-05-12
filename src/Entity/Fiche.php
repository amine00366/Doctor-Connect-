<?php

namespace App\Entity;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\FicheRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FicheRepository::class)]
class Fiche
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotNull(message: 'Le champ ne doit pas être vide')]
    private ?string $note = null;

    #[ORM\OneToOne(inversedBy: 'fiche', cascade: ['persist', 'remove'])]
    private ?Consultation $IdConsultation = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(string $note): self
    {
        $this->note = $note;

        return $this;
    }

    
    public function __toString(){
        return (string) $this->note;
    }

    public function getIdConsultation(): ?Consultation
    {
        return $this->IdConsultation;
    }

    public function setIdConsultation(?Consultation $IdConsultation): self
    {
        $this->IdConsultation = $IdConsultation;

        return $this;
    }
}
