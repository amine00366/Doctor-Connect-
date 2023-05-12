<?php

namespace App\Entity;

use App\Repository\ReclamationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ReclamationRepository::class)]
class Reclamation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private $date;    
    public function __construct()
    {
        $this->date = new \DateTime();
    }

    #[ORM\Column(length: 255)]
   #[Assert\NotBlank (message:"L'adresse mail est obligatoire")]
 #[Assert\Email(message: 'The email "{{ value }}" is not a valid email address.')]
    private string $email = '';

    #[ORM\Column(type: Types::BIGINT)]
    private ?int $telephone = null;
    #[Assert\NotBlank (message:"Veuillez indiquer votre num tel")]

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank (message:"Il faut expliquer votre problème")]
    private ?string $cmnt = null;

    #[ORM\Column(length: 255)]
    private $etat = "traitement en cours";


    #[ORM\ManyToOne(inversedBy: 'reclamations')]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $id_user = null;

    #[ORM\ManyToOne(inversedBy: 'reclamations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?TypeReclamation $id_tr = null;

  
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getCmnt(): ?string
    {
        return $this->cmnt;
    }

    public function setCmnt(string $cmnt): self
    {
        $this->cmnt = $cmnt;

        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(string $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    public function getIdUser(): ?User
    {
        return $this->id_user;
    }

    public function setIdUser(?User $id_user): self
    {
        $this->id_user = $id_user;

        return $this;
    }

    public function getIdTr(): ?TypeReclamation
    {
        return $this->id_tr;
    }

    public function setIdTr(?TypeReclamation $id_tr): self
    {
        $this->id_tr = $id_tr;

        return $this;
    }

  
}
