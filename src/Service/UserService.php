<?php

namespace App\Service;

use Ramsey\Uuid\Uuid;

class UserService
{
    public function generateApiToken()
    {
        return Uuid::uuid4()->toString();
    }
}
