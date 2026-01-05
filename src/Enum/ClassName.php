<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Enum;

final class ClassName
{
    public const string SNIFF = 'PHP_CodeSniffer\Sniffs\Sniff';

    public const string RECTOR = 'Rector\Contract\Rector\RectorInterface';

    public const string ABSTRACT_RECTOR = 'Rector\Rector\AbstractRector';

    public const string CONFIGURABLE_RECTOR = 'Rector\Contract\Rector\ConfigurableRectorInterface';

    public const string RECTOR_ATTRIBUTE_KEY = 'Rector\NodeTypeResolver\Node\AttributeKey';

    public const string MOCK_OBJECT_CLASS = 'PHPUnit\Framework\MockObject\MockObject';
}
