<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Enum;

final class RuleIdentifier
{
    /**
     * @var string
     */
    public const UPPERCASE_CONSTANT = 'symplify.uppercaseConstant';

    /**
     * @var string
     */
    public const SEE_ANNOTATION_TO_TEST = 'symplify.seeAnnotationToTest';

    /**
     * @var string
     */
    public const REGEX_SUFFIX_IN_REGEX_CONSTANT = 'symplify.regexSuffixInRegexConstant';

    /**
     * @var string
     */
    public const REQUIRE_ATTRIBUTE_NAME = 'symplify.requireAttributeName';

    /**
     * @var string
     */
    public const RECTOR_PHP_RULE_IMPLEMENTS_MIN_VERSION = 'rector.phpRuleImplementsMinVersion';

    /**
     * @var string
     */
    public const RECTOR_UPGRADE_DOWNGRADE_REGISTERED_IN_SET = 'rector.upgradeDowngradeRegisteredInSet';

    /**
     * @var string
     */
    public const PHP_PARSER_NO_LEADING_BACKSLASH_IN_NAME = 'phpParser.noLeadingBackslashInName';

    /**
     * @var string
     */
    public const NO_SINGLE_INTERFACE_IMPLEMENTER = 'symplify.noSingleInterfaceImplementer';

    /**
     * @var string
     */
    public const NO_RETURN_ARRAY_VARIABLE_LIST = 'symplify.noReturnArrayVariableList';
}
