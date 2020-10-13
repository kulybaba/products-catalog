<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ImageRepository")
 */
class Image
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string $url
     *
     * @ORM\Column(type="string", length=255)
     */
    private $url;

    /**
     * @var string $s3Key
     *
     * @ORM\Column(type="string", length=255)
     */
    private $s3Key;

    /**
     * @var UploadedFile
     * @Assert\File(
     *      maxSize = "2M",
     *      mimeTypes = {"image/*"},
     *      maxSizeMessage = "The file is too large ({{ size }}). Allowed maximum size is {{ limit }}",
     *      mimeTypesMessage = "The mime type of the file is invalid ({{ type }}). Allowed mime types are {{ types }}",
     * )
     */
    private $file;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @param string $url
     *
     * @return Image
     */
    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getS3Key(): ?string
    {
        return $this->s3Key;
    }

    /**
     * @param string $s3Key
     *
     * @return Image
     */
    public function setS3Key(string $s3Key): self
    {
        $this->s3Key = $s3Key;

        return $this;
    }

    public function getFile(): ?string
    {
        return $this->file;
    }

    public function setFile(?string $file): self
    {
        $this->file = $file;

        return $this;
    }
}
