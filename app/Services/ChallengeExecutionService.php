<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChallengeExecutionService
{
    private const DEFAULT_LANGUAGE_ID = 68; // PHP 7.4 on Judge0 CE; override via config

    private const CPU_TIME_LIMIT = 10;

    private const MEMORY_LIMIT_KB = 262144; // 256 MB

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
     * Execute a challenge submission against its test suite via Judge0.
     *
     * @return array{passed: bool, exit_code: int, output: string, duration_ms: int, tests: list<array{name: string, passed: bool, message: string|null}>}
     */
    public function run(string $solutionCode, string $testsCode): array
    {
        $script = $this->buildScript($solutionCode, $testsCode);

        $response = Http::withHeaders($this->buildHeaders())
            ->timeout(30)
            ->post("{$this->baseUrl}/submissions?base64_encoded=true&wait=true", [
                'source_code' => base64_encode($script),
                'language_id' => $this->languageId,
                'stdin' => '',
                'cpu_time_limit' => self::CPU_TIME_LIMIT,
                'memory_limit' => self::MEMORY_LIMIT_KB,
            ]);

        if ($response->failed()) {
            Log::error('Judge0 submission failed', ['status' => $response->status(), 'body' => $response->body()]);

            return $this->errorResult("Judge0 API request failed ({$response->status()}).");
        }

        return $this->parseResponse($response->json());
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

    private function buildScript(string $solutionCode, string $testsCode): string
    {
        $solution = $this->stripPhpTag($solutionCode);
        $tests = $this->stripPhpTag($testsCode);

        return '<?php'."\n\n"
            .$this->buildPhpUnitBootstrap()."\n\n"
            .'namespace {'."\n\n"
            .$solution."\n\n"
            .$tests."\n\n"
            .$this->buildTestRunner()."\n\n"
            .'}'."\n";
    }

    private function stripPhpTag(string $code): string
    {
        return (string) preg_replace('/^<\?php\s*/i', '', trim($code));
    }

    /**
     * Minimal PHPUnit\Framework\TestCase implementation so challenge tests run
     * without PHPUnit installed in the Judge0 sandbox.
     */
    private function buildPhpUnitBootstrap(): string
    {
        return <<<'BOOTSTRAP'
namespace PHPUnit\Framework {
    class AssertionFailedError extends \RuntimeException {}

    abstract class TestCase
    {
        protected function assertSame(mixed $expected, mixed $actual, string $message = ''): void
        {
            if ($expected !== $actual) {
                throw new AssertionFailedError(
                    $message ?: sprintf(
                        "Failed asserting that %s is identical to %s.",
                        json_encode($actual),
                        json_encode($expected)
                    )
                );
            }
        }

        protected function assertEquals(mixed $expected, mixed $actual, string $message = ''): void
        {
            if ($expected != $actual) {
                throw new AssertionFailedError(
                    $message ?: sprintf(
                        "Failed asserting that %s equals %s.",
                        json_encode($actual),
                        json_encode($expected)
                    )
                );
            }
        }

        protected function assertTrue(mixed $condition, string $message = ''): void
        {
            if ($condition !== true) {
                throw new AssertionFailedError($message ?: 'Failed asserting that false is true.');
            }
        }

        protected function assertFalse(mixed $condition, string $message = ''): void
        {
            if ($condition !== false) {
                throw new AssertionFailedError($message ?: 'Failed asserting that true is false.');
            }
        }

        protected function assertNull(mixed $actual, string $message = ''): void
        {
            if ($actual !== null) {
                throw new AssertionFailedError(
                    $message ?: sprintf("Failed asserting that %s is null.", json_encode($actual))
                );
            }
        }

        protected function assertNotNull(mixed $actual, string $message = ''): void
        {
            if ($actual === null) {
                throw new AssertionFailedError('Failed asserting that null is not null.');
            }
        }

        protected function assertCount(int $expected, array|\Countable $haystack, string $message = ''): void
        {
            $actual = count($haystack);
            if ($expected !== $actual) {
                throw new AssertionFailedError(
                    $message ?: sprintf(
                        "Failed asserting that actual size %d matches expected size %d.",
                        $actual,
                        $expected
                    )
                );
            }
        }

        protected function assertInstanceOf(string $expected, mixed $actual, string $message = ''): void
        {
            if (! ($actual instanceof $expected)) {
                throw new AssertionFailedError(
                    $message ?: sprintf(
                        "Failed asserting that %s is an instance of %s.",
                        is_object($actual) ? get_class($actual) : gettype($actual),
                        $expected
                    )
                );
            }
        }

        protected function assertContains(mixed $needle, array $haystack, string $message = ''): void
        {
            if (! in_array($needle, $haystack, true)) {
                throw new AssertionFailedError(
                    $message ?: sprintf("Failed asserting that array contains %s.", json_encode($needle))
                );
            }
        }

        protected function assertGreaterThan(mixed $expected, mixed $actual, string $message = ''): void
        {
            if (! ($actual > $expected)) {
                throw new AssertionFailedError(
                    $message ?: sprintf(
                        "Failed asserting that %s is greater than %s.",
                        json_encode($actual),
                        json_encode($expected)
                    )
                );
            }
        }

        protected function assertLessThan(mixed $expected, mixed $actual, string $message = ''): void
        {
            if (! ($actual < $expected)) {
                throw new AssertionFailedError(
                    $message ?: sprintf(
                        "Failed asserting that %s is less than %s.",
                        json_encode($actual),
                        json_encode($expected)
                    )
                );
            }
        }

        protected function assertIsArray(mixed $actual, string $message = ''): void
        {
            if (! is_array($actual)) {
                throw new AssertionFailedError($message ?: 'Failed asserting that value is an array.');
            }
        }

        protected function assertIsString(mixed $actual, string $message = ''): void
        {
            if (! is_string($actual)) {
                throw new AssertionFailedError($message ?: 'Failed asserting that value is a string.');
            }
        }

        protected function assertIsInt(mixed $actual, string $message = ''): void
        {
            if (! is_int($actual)) {
                throw new AssertionFailedError($message ?: 'Failed asserting that value is an integer.');
            }
        }

        protected function assertNotSame(mixed $expected, mixed $actual, string $message = ''): void
        {
            if ($expected === $actual) {
                throw new AssertionFailedError(
                    $message ?: sprintf(
                        "Failed asserting that %s is not identical to %s.",
                        json_encode($actual),
                        json_encode($expected)
                    )
                );
            }
        }

        protected function assertStringContainsString(string $needle, string $haystack, string $message = ''): void
        {
            if (! str_contains($haystack, $needle)) {
                throw new AssertionFailedError(
                    $message ?: sprintf("Failed asserting that string contains %s.", json_encode($needle))
                );
            }
        }

        protected function assertStringNotContainsString(string $needle, string $haystack, string $message = ''): void
        {
            if (str_contains($haystack, $needle)) {
                throw new AssertionFailedError(
                    $message ?: sprintf("Failed asserting that string does not contain %s.", json_encode($needle))
                );
            }
        }

        protected function assertStringStartsWith(string $prefix, string $string, string $message = ''): void
        {
            if (! str_starts_with($string, $prefix)) {
                throw new AssertionFailedError(
                    $message ?: sprintf("Failed asserting that string starts with %s.", json_encode($prefix))
                );
            }
        }

        protected function assertArrayHasKey(string|int $key, array $array, string $message = ''): void
        {
            if (! array_key_exists($key, $array)) {
                throw new AssertionFailedError(
                    $message ?: sprintf("Failed asserting that array has key %s.", json_encode($key))
                );
            }
        }

        protected function expectException(string $exception): void
        {
            throw new \RuntimeException('expectException not supported in sandbox');
        }

        protected function assertGreaterThanOrEqual(mixed $expected, mixed $actual, string $message = ''): void
        {
            if (! ($actual >= $expected)) {
                throw new AssertionFailedError(
                    $message ?: sprintf(
                        "Failed asserting that %s is greater than or equal to %s.",
                        json_encode($actual),
                        json_encode($expected)
                    )
                );
            }
        }

        protected function assertLessThanOrEqual(mixed $expected, mixed $actual, string $message = ''): void
        {
            if (! ($actual <= $expected)) {
                throw new AssertionFailedError(
                    $message ?: sprintf(
                        "Failed asserting that %s is less than or equal to %s.",
                        json_encode($actual),
                        json_encode($expected)
                    )
                );
            }
        }
    }
}
BOOTSTRAP;
    }

    /**
     * Reflection-based runner that discovers test_ methods and emits JSON results.
     */
    private function buildTestRunner(): string
    {
        return <<<'RUNNER'
(function () {
    $testClasses = array_filter(
        get_declared_classes(),
        fn ($c) => is_subclass_of($c, \PHPUnit\Framework\TestCase::class)
    );

    $results = [];

    foreach ($testClasses as $class) {
        $ref     = new \ReflectionClass($class);
        $methods = array_filter(
            array_map(fn ($m) => $m->getName(), $ref->getMethods(\ReflectionMethod::IS_PUBLIC)),
            fn ($m) => str_starts_with($m, 'test')
        );

        foreach ($methods as $method) {
            $label = preg_replace('/^test_?/', '', $method);
            $label = (string) preg_replace_callback(
                '/(?<=[a-z])([A-Z])/',
                fn ($m) => ' ' . strtolower($m[1]),
                $label
            );
            $label = ucfirst(str_replace('_', ' ', $label));

            try {
                $instance = $ref->newInstance();
                $instance->$method();
                $results[] = ['name' => $label, 'passed' => true, 'message' => null];
            } catch (\PHPUnit\Framework\AssertionFailedError $e) {
                $results[] = ['name' => $label, 'passed' => false, 'message' => $e->getMessage()];
            } catch (\Throwable $e) {
                $results[] = ['name' => $label, 'passed' => false, 'message' => get_class($e) . ': ' . $e->getMessage()];
            }
        }
    }

    echo json_encode($results, JSON_UNESCAPED_UNICODE);
})();
RUNNER;
    }

    /**
     * @return array{passed: bool, exit_code: int, output: string, duration_ms: int, tests: list<array{name: string, passed: bool, message: string|null}>}
     */
    private function parseResponse(array $data): array
    {
        $stdout = isset($data['stdout']) ? (string) base64_decode($data['stdout']) : '';
        $stderr = isset($data['stderr']) ? (string) base64_decode($data['stderr']) : '';
        $exitCode = (int) ($data['exit_code'] ?? 0);
        $durationMs = (int) ((float) ($data['time'] ?? 0) * 1000);
        $statusId = (int) ($data['status']['id'] ?? 0);

        // Judge0 status IDs: 6 = Compilation Error, 5 = Time Limit Exceeded
        if ($statusId === 6) {
            return [
                'passed' => false,
                'exit_code' => $exitCode,
                'output' => $stderr,
                'duration_ms' => $durationMs,
                'tests' => [['name' => 'Syntax check', 'passed' => false, 'message' => $stderr]],
            ];
        }

        if ($statusId === 5) {
            return [
                'passed' => false,
                'exit_code' => $exitCode,
                'output' => 'Time limit exceeded.',
                'duration_ms' => $durationMs,
                'tests' => [[
                    'name' => 'Execution',
                    'passed' => false,
                    'message' => 'Your code exceeded the time limit of '.self::CPU_TIME_LIMIT.' seconds.',
                ]],
            ];
        }

        $tests = $this->parseTestOutput($stdout, $stderr);
        $passed = $tests !== [] && collect($tests)->every(fn ($t) => $t['passed']);

        return [
            'passed' => $passed,
            'exit_code' => $exitCode,
            'output' => $stdout,
            'duration_ms' => $durationMs,
            'tests' => $tests,
        ];
    }

    private function parseTestOutput(string $stdout, string $stderr): array
    {
        $decoded = json_decode(trim($stdout), true);

        if (is_array($decoded) && $decoded !== []) {
            return $decoded;
        }

        $message = trim($stdout ?: $stderr) ?: 'Unexpected output format.';

        return [['name' => 'Execution', 'passed' => false, 'message' => $message]];
    }

    /**
     * @return array{passed: bool, exit_code: int, output: string, duration_ms: int, tests: list<array{name: string, passed: bool, message: string|null}>}
     */
    private function errorResult(string $message): array
    {
        return [
            'passed' => false,
            'exit_code' => -1,
            'output' => $message,
            'duration_ms' => 0,
            'tests' => [['name' => 'Runner error', 'passed' => false, 'message' => $message]],
        ];
    }
}
