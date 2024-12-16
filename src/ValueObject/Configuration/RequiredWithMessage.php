<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\ValueObject\Configuration;

final readonly class RequiredWithMessage
{
    public function __construct(
        private string $required,
        private ?string $message
    ) {
    }

    public function getRequired(): string
    {
        return $this->required;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }
}
