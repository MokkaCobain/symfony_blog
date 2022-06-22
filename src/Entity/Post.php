<?php

namespace App\Entity;

use App\Entity\User;
use DateTimeImmutable;
use App\Entity\Category;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\PostRepository;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation\Uploadable;
use Vich\UploaderBundle\Mapping\Annotation\UploadableField;

#[ORM\Entity(repositoryClass: PostRepository::class)]
#[Vich\Uploadable] 
class Post
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    
    #[Assert\NotBlank(
        message: 'Le titre est obligatoire.'
    )]
    #[Assert\Length(
        min: 2,
        max: 255,
        minMessage: 'Le titre doit contenir {{ limit }} caractères minimum.',
        maxMessage: 'Le titre doit contenir {{ limit }} caractères maximum.',
    )]
    #[ORM\Column(type: 'string', length: 255)]
    /**
     * Le titre
     *
     * @var [string]
     */
    private $title;



    #[Gedmo\Slug(fields: ['title'])]
    #[ORM\Column(type: 'string', length: 255)]
    /**
     * Le slug du titre
     *
     * @var [string]
     */
    private $slug;



    #[Assert\NotBlank(
        message: 'La catégorie est obligatoire.'
    )]
    #[Assert\Type(
        type: Category::class,
        message: 'Sélectionnez une catégorie.',
    )]
    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'posts')]
    #[ORM\JoinColumn(nullable: false)]
    /**
     * Clé étrangère provenant de la table category
     *
     * @var [category::class]
     */
    private $category;




    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    /**
     * Clé étrangère provenant de la table user
     *
     * @var [User::class]
     */
    private $author;



    
    #[ORM\Column(type: 'string', length: 255, nullable: true, unique: true)]
    /**
     * Chemin de l'image mappé dans la database
     *
     * @var [string]
     */
    private $image;


    
     /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     */
    #[Assert\File(
        maxSize: '10240k', // 1 mega
        mimeTypes: ['image/gif', 'image/png', 'image/jpeg', 'image/bmp', 'image/webp', 'image/svg+xml'], 
        mimeTypesMessage: 'Les formats acceptés sont : gif, png, jpeg, bmp, webp, svg',
    )]
    #[Vich\UploadableField(mapping: 'post_image', fileNameProperty: 'image')]
    /**
     * Fichier récupéré dans le formulaire (pas mappé dans la database)
     *
     * @var File|null
     */
    private ?File $imageFile = null;




    #[Assert\NotBlank(
        message: 'Le contenu est obligatoire.'
    )]
    #[Assert\Length(
        max: 500000,
        maxMessage: 'L\'article doit contenir {{ limit }} caractères maximum.',
    )]
    #[ORM\Column(type: 'text')]
    /**
     * Contenu de l'article type longText
     *
     * @var [text]
     */
    private $content;




    #[ORM\Column(type: 'boolean', options: array('default' => false))]
    /**
     * Statut de publication de l'article
     *
     * @var [boolean]
     */
    private $isPublished;




        /**
     * @var \DateTimeImmutable
     */
    #[Gedmo\Timestampable(on: 'create')]
    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private $createdAt;




        /**
     * @var \DateTimeImmutable
     */
    #[Gedmo\Timestampable(on: 'update')]
    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private $updatedAt;



            /**
     * @var \DateTimeImmutable
     */
    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private $publishedAt;

    #[ORM\ManyToMany(targetEntity: PostTag::class, mappedBy: 'tags')]
    private $postTags;


    /** Pour donner l'information par défaut au moment de l'insert into dans la db */
    public function __construct()
    {
        return $this->isPublished = false;
        $this->postTags = new ArrayCollection();
    }
  
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }


    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }
    

       /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $imageFile
     */
    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }


    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getIsPublished(): ?bool
    {
        return $this->isPublished;
    }

    public function setIsPublished(bool $isPublished): self
    {
        $this->isPublished = $isPublished;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }


    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }


    public function getPublishedAt(): ?\DateTimeImmutable
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(?\DateTimeImmutable $publishedAt): self
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    /**
     * @return Collection<int, PostTag>
     */
    public function getPostTags(): Collection
    {
        return $this->postTags;
    }

    public function addPostTag(PostTag $postTag): self
    {
        if (!$this->postTags->contains($postTag)) {
            $this->postTags[] = $postTag;
            $postTag->addTag($this);
        }

        return $this;
    }

    public function removePostTag(PostTag $postTag): self
    {
        if ($this->postTags->removeElement($postTag)) {
            $postTag->removeTag($this);
        }

        return $this;
    }
}
