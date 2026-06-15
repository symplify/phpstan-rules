<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Enum;

final class ClassName
{
    /**
     * @var string
     */
    public const SNIFF = 'PHP_CodeSniffer\Sniffs\Sniff';

    /**
     * @var string
     */
    public const RECTOR = 'Rector\Contract\Rector\RectorInterface';

    /**
     * @var string
     */
    public const ABSTRACT_RECTOR = 'Rector\Rector\AbstractRector';

    /**
     * @var string
     */
    public const CONFIGURABLE_RECTOR = 'Rector\Contract\Rector\ConfigurableRectorInterface';

    /**
     * @var string
     */
    public const RECTOR_ATTRIBUTE_KEY = 'Rector\NodeTypeResolver\Node\AttributeKey';

    /**
     * @var string
     */
    public const MOCK_OBJECT_CLASS = 'PHPUnit\Framework\MockObject\MockObject';
}
