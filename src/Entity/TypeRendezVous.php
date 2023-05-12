<?php

namespace App\Entity;

use App\Repository\TypeRendezVousRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TypeRendezVousRepository::class)]
class TypeRendezVous
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $type_rdv = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTypeRdv(): ?string
    {
        return $this->type_rdv;
    }

    public function setTypeRdv(string $type_rdv): self
    {
        $this->type_rdv = $type_rdv;

        return $this;
    }
}
