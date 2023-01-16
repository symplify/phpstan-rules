<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\ValueObject;

final class ClassConstRegexMetadata
{
    public function __construct(
        private readonly string $constantName,
        private readonly string $regexValue,
        private readonly string $filePath,
        private readonly int $line
    ) {
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
