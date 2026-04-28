<?php

namespace App\Http\Controllers;

use App\Services\GrpcCttService;
use Illuminate\Http\Request;

class GrpcController extends Controller
{
    protected $grpcService;

    public function __construct(GrpcCttService $grpcService)
    {
        $this->grpcService = $grpcService;
    }

    public function getPlayerResult(Request $request)
    {
        $licenseId = $request->input('license_id', 0);

        try {
            $result = $this->grpcService->getPlayerResult($licenseId);

            return response()->json([
                'total' => $result->getTotal(),
                'win' => $result->getWin(),
                'lost' => $result->getLost(),
                'winrate' => $result->getWinrate(),
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}