<?php

declare(strict_types=1);

namespace PhpCfdi\CsfScraper\Exceptions\PdfReader;

use PhpCfdi\CsfScraper\PdfReader\ShellExecResult;

final class ShellExecException extends \RuntimeException
{
    /** @var string[] */
    private array $command;
    private ShellExecResult $result;

    /**
     * @param string[] $command
     */
    public function __construct(string $message, array $command, ShellExecResult $result)
    {
        parent::__construct($message);

        $this->command = $command;
        $this->result = $result;
    }

    /** @return string[] */
    public function getCommand(): array
    {
        return $this->command;
    }

    public function getResult(): ShellExecResult
    {
        return $this->result;
    }
}
