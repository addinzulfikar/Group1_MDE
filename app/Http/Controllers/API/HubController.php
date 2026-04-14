<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\HubRepositoryInterface;

class HubController extends Controller
{
    protected $hubRepo;

    public function __construct(HubRepositoryInterface $hubRepo)
    {
        $this->hubRepo = $hubRepo;
    }

    public function index()
    {
        return response()->json([
            'status' => 'success',
            'data' => $this->hubRepo->getAllHubs()
        ]);
    }

    public function checkCapacity($id)
    {
        return response()->json([
            'status' => 'success',
            'data' => $this->hubRepo->checkCapacity($id)
        ]);
    }
}
