<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\JobRequest;
use App\Http\Resources\JobResource;
use App\Models\Job;
use App\Services\JobService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class JobController extends Controller
{
    public function __construct(
        private readonly JobService $jobService
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $jobs = $this->jobService->paginate(
            $request->input('search'),
            $request->input('per_page', 10)
        );

        return JobResource::collection($jobs);
    }

    public function show(Job $job): JobResource
    {
        $job = $this->jobService->find($job->id);

        return new JobResource($job);
    }

    public function store(JobRequest $request): JsonResponse
    {
        $job = $this->jobService->create($request->validated());

        return response()->json([
            'message' => 'Job created successfully',
            'job' => new JobResource($job),
        ], 201);
    }

    public function update(JobRequest $request, Job $job): JsonResponse
    {
        $job = $this->jobService->update($job, $request->validated());

        return response()->json([
            'message' => 'Job updated successfully',
            'job' => new JobResource($job),
        ]);
    }

    public function destroy(Job $job): JsonResponse
    {
        $this->jobService->delete($job);

        return response()->json(['message' => 'Job deleted successfully']);
    }
}
