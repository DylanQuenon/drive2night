<?php
namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class UserImgModify
{
    #[Assert\NotBlank(message: "Please add an image")]
    #[Assert\Image(mimeTypes:['image/png','image/jpeg', 'image/jpg', 'image/gif'], mimeTypesMessage: "You must upload a jpg, jpeg, png or gif file")]
    #[Assert\File(maxSize: "1024k", maxSizeMessage: "The file size is too large")]
    private $newPicture;

    public function getNewPicture(): ?string
    {
        return $this->newPicture;
    }

    public function setNewPicture(?string $newPicture): self
    {
        $this->newPicture = $newPicture;

        return $this;
    }
}

?>