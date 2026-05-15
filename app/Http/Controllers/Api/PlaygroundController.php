<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PlaygroundExecutionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PlaygroundController extends Controller
{
    public function run(Request $request, PlaygroundExecutionService $executor): JsonResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:10000'],
            'language' => ['nullable', 'string', 'in:php'],
        ]);

        $result = $executor->run($validated['code']);

        return response()->json($result);
    }
}
