<?php

namespace App\Service;

class ImageService
{
    public function getImageExtensionFromBinary(string $image) : string
    {
        $mime = getimagesizefromstring($image)['mime'];

        $segments = explode('/', $mime);

        return $segments[1];
    }
}
