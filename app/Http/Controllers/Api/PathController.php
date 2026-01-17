<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PathRequest;
use App\Http\Resources\PathResource;
use App\Models\Path;
use App\Services\PathService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PathController extends Controller
{
    public function __construct(
        private readonly PathService $pathService
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $paths = $this->pathService->paginate(
            $request->input('search'),
            $request->input('per_page', 10)
        );

        return PathResource::collection($paths);
    }

    public function show(Path $path): PathResource
    {
        $path = $this->pathService->find($path->id);

        return new PathResource($path);
    }

    public function store(PathRequest $request): JsonResponse
    {
        $path = $this->pathService->create($request->validated());

        return response()->json([
            'message' => 'Path created successfully',
            'path' => new PathResource($path),
        ], 201);
    }

    public function update(PathRequest $request, Path $path): JsonResponse
    {
        $path = $this->pathService->update($path, $request->validated());

        return response()->json([
            'message' => 'Path updated successfully',
            'path' => new PathResource($path),
        ]);
    }

    public function destroy(Path $path): JsonResponse
    {
        $this->pathService->delete($path);

        return response()->json(['message' => 'Path deleted successfully']);
    }
}
