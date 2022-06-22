<?php

namespace App\Entity;

use App\Entity\Tag;
use App\Entity\Post;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\PostTagRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: PostTagRepository::class)]
class PostTag
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToMany(targetEntity: Post::class, inversedBy: 'postTags')]
    private $tags;

    #[ORM\ManyToMany(targetEntity: Tag::class, inversedBy: 'postTags')]
    private $tagPost;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
        $this->tagPost = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, Post>
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Post $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
        }

        return $this;
    }

    public function removeTag(Post $tag): self
    {
        $this->tags->removeElement($tag);

        return $this;
    }

    /**
     * @return Collection<int, tag>
     */
    public function getTagPost(): Collection
    {
        return $this->tagPost;
    }

    public function addTagPost(tag $tagPost): self
    {
        if (!$this->tagPost->contains($tagPost)) {
            $this->tagPost[] = $tagPost;
        }

        return $this;
    }

    public function removeTagPost(tag $tagPost): self
    {
        $this->tagPost->removeElement($tagPost);

        return $this;
    }
}
