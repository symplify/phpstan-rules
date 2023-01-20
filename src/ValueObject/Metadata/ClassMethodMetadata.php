<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\ValueObject\Metadata;

final class ClassMethodMetadata
{
    /**
     * @readonly
     * @var string
     */
    private $methodName;
    /**
     * @readonly
     * @var int
     */
    private $lineCount;
    /**
     * @readonly
     * @var string
     */
    private $fileName;
    /**
     * @readonly
     * @var int
     */
    private $line;
    public function __construct(string $methodName, int $lineCount, string $fileName, int $line)
    {
        $this->methodName = $methodName;
        $this->lineCount = $lineCount;
        $this->fileName = $fileName;
        $this->line = $line;
    }
    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function getMethodName(): string
    {
        return $this->methodName;
    }

    public function getLine(): int
    {
        return $this->line;
    }

    public function getLineCount(): int
    {
        return $this->lineCount;
    }
}
