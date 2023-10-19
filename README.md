# PHPStan Rules

[![Downloads](https://img.shields.io/packagist/dt/symplify/phpstan-rules.svg?style=flat-square)](https://packagist.org/packages/symplify/phpstan-rules/stats)

Set of rules for PHPStan used by Symplify projects

- See [Rules Overview](docs/rules_overview.md)

## Install

```bash
composer require symplify/phpstan-rules --dev
```

*Note: Make sure you use [`phpstan/extension-installer`](https://github.com/phpstan/extension-installer) to load necessary service configs.*

<br>

## 1. Add Static Rules to `phpstan.neon`

We recommend to start with rules that do not require any configuration, because there is exactly 1 way to use them:

```yaml
# phpstan.neon
includes:
    - vendor/symplify/phpstan-rules/config/static-rules.neon
```

Give it couple of days, before extending.

<br>

## 2. Pick from Prepared Sets

Do you know prepared sets from ECS or Rector? Bunch of rules in single set. We use the same approach here:

```yaml
includes:
    - vendor/symplify/phpstan-rules/config/code-complexity-rules.neon
    - vendor/symplify/phpstan-rules/config/collector-rules.neon
    - vendor/symplify/phpstan-rules/config/naming-rules.neon
    - vendor/symplify/phpstan-rules/config/regex-rules.neon
    - vendor/symplify/phpstan-rules/config/static-rules.neon
```

Pick what you need, drop the rest.

<br>

## 3. How we use Configurable Rules

The configurable set contains rules with *saints defaults*.

```yaml
# phpstan.neon
includes:
    - vendor/symplify/phpstan-rules/config/configurable-rules.neon
```

Would you like to **tailor it to fit your taste**? Pick one and put it to your `phpstan.neon` manually ↓

```yaml
services:
    -
        class: Symplify\PHPStanRules\Rules\ForbiddenNodeRule
        tags: [phpstan.rules.rule]
        arguments:
            forbiddenNodes:
                - PhpParser\Node\Expr\Empty_
                - PhpParser\Node\Stmt\Switch_
                - PhpParser\Node\Expr\ErrorSuppress
```

<br>

Happy coding!
