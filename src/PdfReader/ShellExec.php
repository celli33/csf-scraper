<?php

declare(strict_types=1);

namespace PhpCfdi\CsfScraper\PdfReader;

use InvalidArgumentException;
use Symfony\Component\Process\Process;
use Throwable;

/**
 * Execute a command and retrieve results
 *
 * NOTE: Changes will not be considering a bracking compatibility change since this utility is for internal usage only
 * @internal
 */
final class ShellExec
{
    /** @var array<mixed> */
    private readonly array $command;

    /** @var array<mixed> */
    private readonly array $environment;

    /**
     * @param array<mixed> $command
     * @param array<mixed> $environment
     */
    public function __construct(array $command, array $environment = [])
    {
        // command validation
        if ([] === $command) {
            throw new InvalidArgumentException('Command definition is empty');
        }
        $command = array_values($command);
        $this->checkArrayStrings($command, true, 'Command definition has elements that are invalid');
        // executable must not be empty
        if ('' === reset($command)) {
            throw new InvalidArgumentException('Command executable is empty');
        }
        // environment keys validation
        $this->checkArrayStrings(array_keys($environment), false, 'Environment has keys that are invalid');
        // environment values validation
        $this->checkArrayStrings($environment, true, 'Environment has values that are invalid');

        // command allocation
        $this->command = $command;
        $this->environment = $environment;
    }

    /**
     *
     * @param array<int, mixed> $input
     */
    private function checkArrayStrings(array $input, bool $allowEmpty, string $exceptionMessage): void
    {
        try {
            foreach ($input as $index => $value) {
                $this->checkString($value, $allowEmpty, "Element $index");
            }
        } catch (Throwable $exception) {
            throw new InvalidArgumentException($exceptionMessage, 0, $exception);
        }
    }

    private function checkString(mixed $value, bool $allowEmpty, string $exceptionMessagePrefix): void
    {
        if (! is_string($value)) {
            throw new InvalidArgumentException($exceptionMessagePrefix . ' is not a string');
        }
        if (! $allowEmpty && '' === $value) {
            throw new InvalidArgumentException($exceptionMessagePrefix . ' is empty');
        }
        if (boolval(preg_match('/[\x00-\x09\x0B\x0C\x0E-\x1F\x7F]/u', $value))) {
            throw new InvalidArgumentException($exceptionMessagePrefix . ' contains control characters');
        }
    }

    /**
     *
     * @return array<mixed>
     */
    public function getCommand(): array
    {
        return $this->command;
    }

    /**
     *
     * @return array<mixed>
     */
    public function getEnvironment(): array
    {
        return $this->environment;
    }

    public function run(): ShellExecResult
    {
        // Set environment on run command for compatibility with symfony/process ^3.4 & php ~ 7.0
        $process = new Process($this->getCommand());
        $process->run(null, $this->getEnvironment());
        return new ShellExecResult(
            $process->getCommandLine(),
            $process->getExitCode() ?? -1,
            $process->getOutput(),
            $process->getErrorOutput()
        );
    }
}
