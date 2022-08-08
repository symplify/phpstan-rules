<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\ValueObject;

final class VariableNameMetadata
{
    /**
     * @var string
     */
    private $variableName;
    /**
     * @var string
     */
    private $filePath;
    /**
     * @var int
     */
    private $lineNumber;
    public function __construct(string $variableName, string $filePath, int $lineNumber)
    {
        $this->variableName = $variableName;
        $this->filePath = $filePath;
        $this->lineNumber = $lineNumber;
    }

    public function getVariableName(): string
    {
        return $this->variableName;
    }

    public function getFilePath(): string
    {
        return $this->filePath;
    }

    public function getLineNumber(): int
    {
        return $this->lineNumber;
    }
}
