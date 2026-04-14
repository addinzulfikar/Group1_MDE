<?php

namespace App\Repositories\Contracts;

interface HubRepositoryInterface
{
    public function getAllHubs($search = null);
    public function checkCapacity($hubId);
}
