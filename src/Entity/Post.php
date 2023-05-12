<?php

namespace App\Entity;

use App\Entity\Postcomment;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Self_;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: "App\Repository\PostRepository")]
#[ORM\Table(name: "post")]
#[ORM\HasLifecycleCallbacks()]
class Post
{

    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[Groups("Post")]
    protected $id;
    
    #[ORM\Column(type: "string", length: 255)]
    #[Assert\NotBlank (message: "title is required") ]
    #[Assert\Length(min:3,minMessage: "lenght min 3")]
    #[Groups("Post")]
    private $title;
 

  
    #[ORM\Column(type: "string", length: 255)]
    #[Assert\NotBlank (message: "description is required") ]
    #[Assert\Length(min:3,minMessage: "lenght min 3")]
    #[Groups("Post")]
    private $description;

    #[ORM\Column(name: "photo", type: "string", length: 255, nullable:true)]
    #[Assert\File(maxSize: "500k", mimeTypes: ["image/jpeg", "image/jpg", "image/png", "image/GIF"])]
    #[Groups("Post")]
    private $photo;

    #[ORM\ManyToOne(targetEntity: "App\Entity\User")]
    #[ORM\JoinColumn(name: "creator", referencedColumnName: "id",nullable:true)]
    private $creator;
    #[ORM\ManyToOne(targetEntity: "App\Entity\Doctor")]
    #[ORM\JoinColumn(name: "creatordoc", referencedColumnName: "id",nullable:true)]
    private $creatordoc;

    #[ORM\Column(name: "postdate", type: "date")]
    #[Groups("Post")]
    private $postdate;

    #[ORM\OneToMany(targetEntity: Postcomment::class, mappedBy: "post", cascade: ["remove"], orphanRemoval: true)]
    private $comments;

    public function getCreator()
    {
        return $this->creator;
    }

    public function setCreator($creator)
    {
        $this->creator = $creator;
    }
    public function getCreatordoc()
    {
        return $this->creatordoc;
    }

    public function setCreatordoc($creatordoc)
    {
        $this->creatordoc = $creatordoc;
    }
    public function getComments()
    {
        return $this->comments;
    }

    public function setComments($comments)
    {
        $this->comments = $comments;
    }

    public function setPostdate($postdate)
    {
        $this->postdate = $postdate;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): self
    {
        $this->photo = $photo;
        return $this;
    }

    public function getPostdate()
    {
        return $this->postdate;
    }

}