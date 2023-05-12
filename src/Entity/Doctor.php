<?php

namespace App\Entity;

use App\Repository\DoctorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: DoctorRepository::class)]
class Doctor implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $Diplome = null;

    #[ORM\OneToMany(mappedBy: 'id_doctor', targetEntity: Post::class)]
    private Collection $posts;

    #[ORM\OneToMany(mappedBy: 'id_doctor', targetEntity: Reclamation::class)]
    private Collection $reclamations;

    #[ORM\OneToMany(mappedBy: 'id_medcin', targetEntity: Reservation::class, orphanRemoval: true)]
    private Collection $reservations;

    #[ORM\OneToMany(mappedBy: 'id_medcin', targetEntity: Consultation::class, orphanRemoval: true)]
    private Collection $consultations;
    
    #[ORM\OneToMany(mappedBy: 'doctor', targetEntity: Appointment::class)]
    private Collection $appointments;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: '4')]
    private ?string $latitude = "1.5";

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: '4')]
    private ?string $longitude = "2.0";

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $Specialite = null;
    #[ORM\Column(length: 255)]
    private ?int $age = null;

    #[ORM\Column(length: 255)]
    private ?string $cin = null;

    #[ORM\Column(length: 255)]
    private ?string $prenom = null;
    #[ORM\Column(length: 255)]
    private ?string $email = null;
    
    #[ORM\Column(length: 255)]
    private ?string $adresse = null;
    #[ORM\Column(length: 255)]
    private ?string $Roleperm = null;
 /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;
        public function getPassword(): string
        {
            return $this->password;
        }
    
        public function setPassword(string $password): self
        {
            $this->password = $password;
    
            return $this;
        }

    public function __construct()
    {
        $this->posts = new ArrayCollection();
        $this->reclamations = new ArrayCollection();
        $this->reservations = new ArrayCollection();
        $this->consultations = new ArrayCollection();
        $this->appointments = new ArrayCollection();
    }
    public function getSpecialite(): ?string
    {
        return $this->Specialite;
    }

    public function setSpecialite(string $Specialite): self
    {
        $this->Specialite = $Specialite;

        return $this;
    }
    public function getRoleperm(): ?string
    {
        return $this->Roleperm;
    }

    public function setRoleperm(string $Roleperm): self
    {
        $this->Roleperm = $Roleperm;

        return $this;
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDiplome(): ?string
    {
        return $this->Diplome;
    }
    public function getSalt(): ?string
    {
        return null;
    }
    public function setDiplome(string $Diplome): self
    {
        $this->Diplome = $Diplome;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getCin(): ?string
    {
        return $this->cin;
    }

    public function setCin(string $cin): self
    {
        $this->cin = $cin;

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

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(int $age): self
    {
        $this->age = $age;

        return $this;
    }

       /**
     * @return Collection<int, Appointment>
     */
    public function getAppointments(): Collection
    {
        return $this->appointments;
    }

    public function addAppointment(Appointment $appointment): self
    {
        if (!$this->appointments->contains($appointment)) {
            $this->appointments->add($appointment);
            $appointment->setDoctor($this);
        }

        return $this;
    }

    public function removeAppointment(Appointment $appointment): self
    {
        if ($this->appointments->removeElement($appointment)) {
            // set the owning side to null (unless already changed)
            if ($appointment->getDoctor() === $this) {
                $appointment->setDoctor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Post>
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }
    public function __toString()
    {
        return $this->nom;
    }

    public function addPost(Post $post): self
    {
        if (!$this->posts->contains($post)) {
            $this->posts->add($post);
            $post->setCreatordoc($this);
        }

        return $this;
    }

    public function removePost(Post $post): self
    {
        if ($this->posts->removeElement($post)) {
            // set the owning side to null (unless already changed)
            if ($post->getCreatordoc() === $this) {
                $post->setCreatordoc(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Reclamation>
     */
    public function getReclamations(): Collection
    {
        return $this->reclamations;
    }

    //public function addReclamation(Reclamation $reclamation): self
    //{
     //   if (!$this->reclamations->contains($reclamation)) {
      //      $this->reclamations->add($reclamation);
       //     $reclamation->setIdDoctor($this);
        //}

        //return $this;
    //}

   // public function removeReclamation(Reclamation $reclamation): self
    //{
       // if ($this->reclamations->removeElement($reclamation)) {
            // set the owning side to null (unless already changed)
         ///   if ($reclamation->getIdDoctor() === $this) {
            //    $reclamation->setIdDoctor(null);
            //}
        //}

        //return $this;
    //}
    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function setLatitude(string $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(string $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

   
    public function getUsername(): string
    {
        return (string) $this->email;
    }
    #[ORM\Column]
    private array $roles = ["ROLE_DOCTOR"];

 public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }
    public function eraseCredentials()
    {
     
    }


    /**
     * @return Collection<int, Reservation>
     */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): self
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations->add($reservation);
            $reservation->setIdMedcin($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): self
    {
        if ($this->reservations->removeElement($reservation)) {
            // set the owning side to null (unless already changed)
            if ($reservation->getIdMedcin() === $this) {
                $reservation->setIdMedcin(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Consultation>
     */
    public function getConsultations(): Collection
    {
        return $this->consultations;
    }

    public function addConsultation(Consultation $consultation): self
    {
        if (!$this->consultations->contains($consultation)) {
            $this->consultations->add($consultation);
            $consultation->setIdMedcin($this);
        }

        return $this;
    }

    public function removeConsultation(Consultation $consultation): self
    {
        if ($this->consultations->removeElement($consultation)) {
            // set the owning side to null (unless already changed)
            if ($consultation->getIdMedcin() === $this) {
                $consultation->setIdMedcin(null);
            }
        }

        return $this;
    }
}
