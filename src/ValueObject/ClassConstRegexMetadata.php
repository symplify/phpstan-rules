<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\ValueObject;

final class ClassConstRegexMetadata
{
    /**
     * @var string
     */
    private $constantName;
    /**
     * @var string
     */
    private $regexValue;
    /**
     * @var string
     */
    private $filePath;
    /**
     * @var int
     */
    private $line;
    public function __construct(string $constantName, string $regexValue, string $filePath, int $line)
    {
        $this->constantName = $constantName;
        $this->regexValue = $regexValue;
        $this->filePath = $filePath;
        $this->line = $line;
    }

    public function getConstantName(): string
    {
        return $this->constantName;
    }

    public function getFilePath(): string
    {
        return $this->filePath;
    }

    public function getRegexValue(): string
    {
        return $this->regexValue;
    }

    public function getLine(): int
    {
        return $this->line;
    }
}
