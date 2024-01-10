# PHPStan Rules

[![Downloads](https://img.shields.io/packagist/dt/symplify/phpstan-rules.svg?style=flat-square)](https://packagist.org/packages/symplify/phpstan-rules/stats)

Set of rules for PHPStan used by Symplify projects

- See [Rules Overview](docs/rules_overview.md)

<br>

## Install

```bash
composer require symplify/phpstan-rules --dev
```

*Note: Make sure you use [`phpstan/extension-installer`](https://github.com/phpstan/extension-installer#usage) to load necessary service configs.*

<br>

## 1. Add Prepared Sets

Sets are bunch of rules grouped by a common area, e.g. improve naming. You can pick from 5 sets:

```yaml
includes:
    - vendor/symplify/phpstan-rules/config/code-complexity-rules.neon
    - vendor/symplify/phpstan-rules/config/collector-rules.neon
    - vendor/symplify/phpstan-rules/config/naming-rules.neon
    - vendor/symplify/phpstan-rules/config/regex-rules.neon
    - vendor/symplify/phpstan-rules/config/static-rules.neon
```

Add sets one by one, fix what you find useful and ignore the rest.

<br>

Do you write custom [Rector](http://github.com/rectorphp/rector-src) rules? Add rules for them too:

```yaml
includes:
    - vendor/symplify/phpstan-rules/config/rector-rules.neon
```

## 2. Cherry-pick Configurable Rules

There is one set with pre-configured configurable rules. Include it and see what is errors are found:

```yaml
# phpstan.neon
includes:
    - vendor/symplify/phpstan-rules/config/configurable-rules.neon
```

<br>

Would you like to **tailor it to fit your taste**? Pick one PHPStan rule and configure it manually ↓

```yaml
services:
    -
        class: Symplify\PHPStanRules\Rules\ForbiddenNodeRule
        tags: [phpstan.rules.rule]
        arguments:
            forbiddenNodes:
                - PhpParser\Node\Expr\Empty_
                - PhpParser\Node\Stmt\Switch_
```

<br>

Happy coding!
