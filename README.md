# PHPStan Rules

[![Downloads](https://img.shields.io/packagist/dt/symplify/phpstan-rules.svg?style=flat-square)](https://packagist.org/packages/symplify/phpstan-rules/stats)

Set of rules for PHPStan used by Symplify projects

- See [Rules Overview](docs/rules_overview.md)

## Install

```bash
composer require symplify/phpstan-rules --dev
```

## 1. Add Static Rules to `phpstan.neon`

Some of rules here require configuration, some not. We recommend to start with rules that do not require any configuration, because there is just one way to use them:

```yaml
# phpstan.neon
includes:
    - vendor/symplify/phpstan-rules/config/static-rules.neon
```

Give it couple of days, before extending.

<br>

Some rules require extra services. To avoid service duplications, they're in the separate config that you can easily include:

```yaml
includes:
    - vendor/symplify/phpstan-rules/config/services/services.neon
```

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

## 3. How we use Configurable Rules

Last but not least, configurable rules with *saints defaults*:

```yaml
# phpstan.neon
includes:
    - vendor/symplify/phpstan-rules/config/configurable-rules.neon
```

Would you like to **configure them yourself?**

- **Pick one and put it to your `phpstan.neon` manually**.
- Configure it to your specific needs and re-run PHPStan.

E.g. `ForbiddenNodeRule`:

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
