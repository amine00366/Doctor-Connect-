<?php

namespace App\Entity;
use App\Repository\PostcommentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use App\Service\CommentNotificationService;

#[ORM\Entity(repositoryClass: PostcommentRepository::class)]
#[ORM\Table(name: "postcomment")]
#[ORM\HasLifecycleCallbacks]
class Postcomment
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(type: "integer")]
    #[Groups("Comment")]

    private $id;

    #[ORM\Column(type: "text", unique: false)]
    #[Assert\NotBlank (message: "content is required") ]
    #[Assert\Length(min:3,minMessage: "lenght min 3")]
    #[Groups("Comment")]

    private $content;
   

    #[ORM\ManyToOne(targetEntity: Post::class, inversedBy: "comments")]
    #[ORM\JoinColumn(name: "post_id", referencedColumnName: "id")]
    private $post;
    #[ORM\Column(type:"boolean", options:['default' => false])]
    #[Groups("Comment")]
    private $approved = false;

   public function isApproved(): bool
   {
       return $this->approved;
   }

   public function setApproved(bool $approved): self
   {
       $this->approved = $approved;

       return $this;
   }



    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "comments")]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "id")]
    private $user;
    
    #[ORM\ManyToOne(targetEntity: Doctor::class, inversedBy: "comments")]
    #[ORM\JoinColumn(name: "doctor_id", referencedColumnName: "id")]
    private $doctoruser;

    #[ORM\Column(type: "datetime")]
    #[Assert\DateTime]
    private $posted_at ;
  
    #[ORM\PrePersist]
    public function setPostedAt(): void
    {
        $this->posted_at  = new \DateTime('now');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
        
    }

    public function setContent(string $content): void
    {   
        
        $this->content = $content;
    }

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(?Post $post): void
    {
        $this->post = $post;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): void
    {
        $this->user = $user;
    }
    public function getdoctoruser(): ?Doctor
    {
        return $this->doctoruser;
    }

    public function setdoctoruser(?Doctor $doctoruser): void
    {
        $this->doctoruser = $doctoruser;
    }

    public function getPostedAt(): ?\DateTimeInterface
    {
        return $this->posted_at ;
    }
    
}
