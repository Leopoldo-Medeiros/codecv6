<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Runs arbitrary PHP code in a Judge0 sandbox and returns whatever the
 * script printed. Unlike {@see ChallengeExecutionService} there is no
 * test framework wrapper — the input code is executed verbatim, which
 * makes this suitable for the step-page playground where the user just
 * wants to experiment.
 */
class PlaygroundExecutionService
{
    private const DEFAULT_LANGUAGE_ID = 68; // PHP 7.4 on Judge0 CE; override via config

    private const CPU_TIME_LIMIT = 5;

    private const MEMORY_LIMIT_KB = 131072; // 128 MB

    private string $baseUrl;

    private string $authToken;

    private int $languageId;

    public function __construct()
    {
        $this->baseUrl = rtrim((string) config('services.judge0.url', 'https://judge0-ce.p.rapidapi.com'), '/');
        $this->authToken = (string) config('services.judge0.token', '');
        $this->languageId = (int) config('services.judge0.language_id', self::DEFAULT_LANGUAGE_ID);
    }

    /**
     * @return array{
     *     ok: bool,
     *     stdout: string,
     *     stderr: string,
     *     exit_code: int,
     *     duration_ms: int,
     *     status: string,
     * }
     */
    public function run(string $code): array
    {
        $response = Http::withHeaders($this->buildHeaders())
            ->timeout(30)
            ->post("{$this->baseUrl}/submissions?base64_encoded=true&wait=true", [
                'source_code' => base64_encode($code),
                'language_id' => $this->languageId,
                'stdin' => '',
                'cpu_time_limit' => self::CPU_TIME_LIMIT,
                'memory_limit' => self::MEMORY_LIMIT_KB,
            ]);

        if ($response->failed()) {
            Log::error('Judge0 playground request failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'ok' => false,
                'stdout' => '',
                'stderr' => "Sandbox request failed ({$response->status()}).",
                'exit_code' => -1,
                'duration_ms' => 0,
                'status' => 'request_failed',
            ];
        }

        $data = (array) $response->json();
        $stdout = isset($data['stdout']) ? (string) base64_decode($data['stdout']) : '';
        $stderr = isset($data['stderr']) ? (string) base64_decode($data['stderr']) : '';
        $compileOutput = isset($data['compile_output']) ? (string) base64_decode($data['compile_output']) : '';
        $exitCode = (int) ($data['exit_code'] ?? 0);
        $durationMs = (int) ((float) ($data['time'] ?? 0) * 1000);
        $statusId = (int) ($data['status']['id'] ?? 0);

        return match (true) {
            $statusId === 6 => [
                'ok' => false,
                'stdout' => '',
                'stderr' => $compileOutput ?: $stderr ?: 'Compilation error.',
                'exit_code' => $exitCode,
                'duration_ms' => $durationMs,
                'status' => 'compile_error',
            ],
            $statusId === 5 => [
                'ok' => false,
                'stdout' => $stdout,
                'stderr' => 'Time limit exceeded ('.self::CPU_TIME_LIMIT.'s).',
                'exit_code' => $exitCode,
                'duration_ms' => $durationMs,
                'status' => 'timeout',
            ],
            default => [
                'ok' => $exitCode === 0,
                'stdout' => $stdout,
                'stderr' => $stderr,
                'exit_code' => $exitCode,
                'duration_ms' => $durationMs,
                'status' => $exitCode === 0 ? 'ok' : 'runtime_error',
            ],
        };
    }

    private function buildHeaders(): array
    {
        $host = (string) parse_url($this->baseUrl, PHP_URL_HOST);

        if (str_contains($host, 'rapidapi')) {
            return [
                'Content-Type' => 'application/json',
                'X-RapidAPI-Key' => $this->authToken,
                'X-RapidAPI-Host' => $host,
            ];
        }

        return [
            'Content-Type' => 'application/json',
            'X-Auth-Token' => $this->authToken,
        ];
    }
}
