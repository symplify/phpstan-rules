# PHPStan Rules

[![Downloads](https://img.shields.io/packagist/dt/symplify/phpstan-rules.svg?style=flat-square)](https://packagist.org/packages/symplify/phpstan-rules/stats)

Set of 35 custom PHPStan rules that check architecture, typos, class namespace locations, accidental visibility override and more. Useful for any type of PHP project, from legacy to modern stack.

<br>

## Install

```bash
composer require symplify/phpstan-rules --dev
```

*Note: Make sure you use [`phpstan/extension-installer`](https://github.com/phpstan/extension-installer#usage) to load necessary service configs.*

<br>


## Usage

Configuration should be added to your `phpstan.neon` file.

<br>

Once you have most rules applied, it's best practice to include whole sets:

```yaml
includes:
    - vendor/symplify/phpstan-rules/config/code-complexity-rules.neon
    - vendor/symplify/phpstan-rules/config/configurable-rules.neon
    - vendor/symplify/phpstan-rules/config/naming-rules.neon
    - vendor/symplify/phpstan-rules/config/static-rules.neon

    # project specific
    - vendor/symplify/phpstan-rules/config/rector-rules.neon
```

<br>

But at start, make baby steps with one rule at a time:

Jump to: [Symfony-specific rules](#3-symfony-specific-rules), [Doctrine-specific rules](#2-doctrine-specific-rules) or [PHPUnit-specific rules](#4-phpunit-specific-rules).

<br>

## Special rules

Tired of ever growing ignored error count in your `phpstan.neon`? Set hard limit to keep them low:

```yaml
parameters:
    maximumIgnoredErrorCount: 50
```

<br>

### ParamNameToTypeConventionRule

By convention, we can define parameter type by its name. If we know the "userId" is always an `int`, PHPStan can warn us about it and let us know to fill the type.

```yaml
services:
    -
        class: Symplify\PHPStanRules\Rules\Convention\ParamNameToTypeConventionRule
        tags: [phpstan.rules.rule]
        arguments:
            paramNamesToTypes:
                userId: int
```

```php
function run($userId)
{
}
```

:x:

<br>

```php
function run(int $userId)
{
}
```

:+1:

<br>

### CheckRequiredInterfaceInContractNamespaceRule

Interface must be located in "Contract" or "Contracts" namespace

```yaml
rules:
    - Symplify\PHPStanRules\Rules\CheckRequiredInterfaceInContractNamespaceRule
```

```php
namespace App\Repository;

interface ProductRepositoryInterface
{
}
```

:x:

<br>

```php
namespace App\Contract\Repository;

interface ProductRepositoryInterface
{
}
```

:+1:

<br>

### ClassNameRespectsParentSuffixRule

Class should have suffix "%s" to respect parent type

:wrench: **configure it!**

```yaml
services:
    -
        class: Symplify\PHPStanRules\Rules\ClassNameRespectsParentSuffixRule
        tags: [phpstan.rules.rule]
        arguments:
            parentClasses:
                - Symfony\Component\Console\Command\Command
```

↓

```php
class Some extends Command
{
}
```

:x:

<br>

```php
class SomeCommand extends Command
{
}
```

:+1:

<br>

### StringFileAbsolutePathExistsRule

Absolute file path must exist. Checked suffixes are "yaml", "yml", "sql", "php" and "json".

```yaml
rules:
    - Symplify\PHPStanRules\Rules\StringFileAbsolutePathExistsRule
```

```php
// missing file path
return __DIR__  . '/some_file.yml';
```

:x:

<br>

```php
// correct file path
return __DIR__  . '/../fixtures/some_file.yml';
```

:+1:

<br>

```php
final class SomeClass extends ParentClass
{
    public function __construct(private string $dependency)
    {
    }
}
```

:+1:

<br>



### ExplicitClassPrefixSuffixRule

Interface have suffix of "Interface", trait have "Trait" suffix exclusively

```yaml
rules:
    - Symplify\PHPStanRules\Rules\Explicit\ExplicitClassPrefixSuffixRule
```

```php
<?php

interface NotSuffixed
{
}

trait NotSuffixed
{
}

abstract class NotPrefixedClass
{
}
```

:x:

<br>

```php
<?php

interface SuffixedInterface
{
}

trait SuffixedTrait
{
}

abstract class AbstractClass
{
}
```

:+1:

<br>

### ForbiddenArrayMethodCallRule

Array method calls [$this, "method"] are not allowed. Use explicit method instead to help PhpStorm, PHPStan and Rector understand your code

```yaml
rules:
    - Symplify\PHPStanRules\Rules\Complexity\ForbiddenArrayMethodCallRule
```

```php
usort($items, [$this, "method"]);
```

:x:

<br>

```php
usort($items, function (array $apples) {
    return $this->method($apples);
};
```

:+1:

<br>

```php
class SomeClass
{
    // ...

    public function run()
    {
        $this->someService->run();
    }
}
```

:+1:

<br>

### ForbiddenExtendOfNonAbstractClassRule

Only abstract classes can be extended

```yaml
rules:
    - Symplify\PHPStanRules\Rules\ForbiddenExtendOfNonAbstractClassRule
```

```php
final class SomeClass extends ParentClass
{
}

class ParentClass
{
}
```

:x:

<br>

```php
abstract class ParentClass
{
}
```

:+1:

<br>

```php
class SomeService
{
    public function __construct(private RepositoryService $repositoryService)
    {
    }

    public function run()
    {
        $item = $this->repositoryService->get(1);
    }
}
```

:+1:

<br>

### ForbiddenFuncCallRule

Function `"%s()"` cannot be used/left in the code

:wrench: **configure it!**

```yaml
services:
    -
        class: Symplify\PHPStanRules\Rules\ForbiddenFuncCallRule
        tags: [phpstan.rules.rule]
        arguments:
            forbiddenFunctions:
                - dump

                # or with custom error message
                dump: 'seems you missed some debugging function'
```

↓

```php
dump('...');
```

:x:

<br>

```php
echo '...';
```

:+1:

<br>

### ForbiddenMultipleClassLikeInOneFileRule

Multiple class/interface/trait is not allowed in single file

```yaml
rules:
    - Symplify\PHPStanRules\Rules\ForbiddenMultipleClassLikeInOneFileRule
```

```php
// src/SomeClass.php
class SomeClass
{
}

interface SomeInterface
{
}
```

:x:

<br>

```php
// src/SomeClass.php
class SomeClass
{
}

// src/SomeInterface.php
interface SomeInterface
{
}
```

:+1:

<br>

### ForbiddenNodeRule

"%s" is forbidden to use

:wrench: **configure it!**

```yaml
services:
    -
        class: Symplify\PHPStanRules\Rules\ForbiddenNodeRule
        tags: [phpstan.rules.rule]
        arguments:
            forbiddenNodes:
                - PhpParser\Node\Expr\ErrorSuppress
```

↓

```php
return @strlen('...');
```

:x:

<br>

```php
return strlen('...');
```

:+1:

<br>

### ForbiddenStaticClassConstFetchRule

Avoid static access of constants, as they can change value. Use interface and contract method instead

```yaml
rules:
    - Symplify\PHPStanRules\Rules\ForbiddenStaticClassConstFetchRule
```

```php
class SomeClass
{
    public function run()
    {
        return static::SOME_CONST;
    }
}
```

:x:

<br>

```php
class SomeClass
{
    public function run()
    {
        return self::SOME_CONST;
    }
}
```

:+1:

<br>

### NoDynamicNameRule

Use explicit names over dynamic ones

```yaml
rules:
    - Symplify\PHPStanRules\Rules\NoDynamicNameRule
```

```php
class SomeClass
{
    public function old(): bool
    {
        return $this->${variable};
    }
}
```

:x:

<br>

```php
class SomeClass
{
    public function old(): bool
    {
        return $this->specificMethodName();
    }
}
```

:+1:

<br>

### NoEntityOutsideEntityNamespaceRule

Class with #[Entity] attribute must be located in "Entity" namespace to be loaded by Doctrine

```yaml
rules:
    - Symplify\PHPStanRules\Rules\NoEntityOutsideEntityNamespaceRule
```

```php
namespace App\ValueObject;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Product
{
}
```

:x:

<br>

```php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Product
{
}
```

:+1:

<br>

### NoGlobalConstRule

Global constants are forbidden. Use enum-like class list instead

```yaml
rules:
    - Symplify\PHPStanRules\Rules\NoGlobalConstRule
```

```php
const SOME_GLOBAL_CONST = 'value';
```

:x:

<br>

```php
class SomeClass
{
    public function run()
    {
        return self::SOME_CONST;
    }
}
```

:+1:

<br>

### NoReferenceRule

Use explicit return value over magic &reference

```yaml
rules:
    - Symplify\PHPStanRules\Rules\NoReferenceRule
```

```php
class SomeClass
{
    public function run(&$value)
    {
    }
}
```

:x:

<br>

```php
class SomeClass
{
    public function run($value)
    {
        return $value;
    }
}
```

:+1:

<br>

### NoReturnSetterMethodRule

Setter method cannot return anything, only set value

```yaml
rules:
    - Symplify\PHPStanRules\Rules\NoReturnSetterMethodRule
```

```php
final class SomeClass
{
    private $name;

    public function setName(string $name): int
    {
        return 1000;
    }
}
```

:x:

<br>

```php
final class SomeClass
{
    private $name;

    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
```

:+1:

<br>

### PreferredClassRule

Instead of "%s" class/interface use "%s"

:wrench: **configure it!**

```yaml
services:
    -
        class: Symplify\PHPStanRules\Rules\PreferredClassRule
        tags: [phpstan.rules.rule]
        arguments:
            oldToPreferredClasses:
                SplFileInfo: CustomFileInfo
```

↓

```php
class SomeClass
{
    public function run()
    {
        return new SplFileInfo('...');
    }
}
```

:x:

<br>

```php
class SomeClass
{
    public function run()
    {
        return new CustomFileInfo('...');
    }
}
```

:+1:

<br>

### PreventParentMethodVisibilityOverrideRule

Change `"%s()"` method visibility to "%s" to respect parent method visibility.

```yaml
rules:
    - Symplify\PHPStanRules\Rules\PreventParentMethodVisibilityOverrideRule
```

```php
class SomeParentClass
{
    public function run()
    {
    }
}

class SomeClass extends SomeParentClass
{
    protected function run()
    {
    }
}
```

:x:

<br>

```php
class SomeParentClass
{
    public function run()
    {
    }
}

class SomeClass extends SomeParentClass
{
    public function run()
    {
    }
}
```

:+1:

<br>

### RequireAttributeNameRule

Attribute must be located in "Attribute" namespace

```yaml
rules:
    - Symplify\PHPStanRules\Rules\Domain\RequireAttributeNameRule
```

```php
// app/Entity/SomeAttribute.php
namespace App\Controller;

#[\Attribute]
final class SomeAttribute
{
}
```

:x:

<br>

```php
// app/Attribute/SomeAttribute.php
namespace App\Attribute;

#[\Attribute]
final class SomeAttribute
{
}
```

:+1:

<br>

### RequireExceptionNamespaceRule

`Exception` must be located in "Exception" namespace

```yaml
rules:
    - Symplify\PHPStanRules\Rules\Domain\RequireExceptionNamespaceRule
```

```php
// app/Controller/SomeException.php
namespace App\Controller;

final class SomeException extends Exception
{

}
```

:x:

<br>

```php
// app/Exception/SomeException.php
namespace App\Exception;

final class SomeException extends Exception
{
}
```

:+1:

<br>

### RequireUniqueEnumConstantRule

Enum constants "%s" are duplicated. Make them unique instead

```yaml
rules:
    - Symplify\PHPStanRules\Rules\Enum\RequireUniqueEnumConstantRule
```

```php
use MyCLabs\Enum\Enum;

class SomeClass extends Enum
{
    private const YES = 'yes';

    private const NO = 'yes';
}
```

:x:

<br>

```php
use MyCLabs\Enum\Enum;

class SomeClass extends Enum
{
    private const YES = 'yes';

    private const NO = 'no';
}
```

:+1:

<br>

### SeeAnnotationToTestRule

Class "%s" is missing `@see` annotation with test case class reference

:wrench: **configure it!**

```yaml
services:
    -
        class: Symplify\PHPStanRules\Rules\SeeAnnotationToTestRule
        tags: [phpstan.rules.rule]
        arguments:
            requiredSeeTypes:
                - Rule
```

↓

```php
class SomeClass extends Rule
{
}
```

:x:

<br>

```php
/**
 * @see SomeClassTest
 */
class SomeClass extends Rule
{
}
```

:+1:

<br>

### UppercaseConstantRule

Constant "%s" must be uppercase

```yaml
rules:
    - Symplify\PHPStanRules\Rules\UppercaseConstantRule
```

```php
final class SomeClass
{
    public const some = 'value';
}
```

:x:

<br>

```php
final class SomeClass
{
    public const SOME = 'value';
}
```

:+1:

<br>

---

<br>

## 2. Symfony-specific Rules

### RequireInvokableControllerRule

Use invokable controller with __invoke() method instead of named action method

```yaml
rules:
    - Symplify\PHPStanRules\Symfony\Rules\RequireInvokableControllerRule
```

```php
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

final class SomeController extends AbstractController
{
    #[Route()]
    public function someMethod()
    {
    }
}
```

:x:

<br>

```php
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class SomeController extends AbstractController
{
    #[Route()]
    public function __invoke()
    {
    }
}
```

:+1:

<br>

---

<br>

## 3. PHPUnit-specific Rules

### NoTestMocksRule

Mocking "%s" class is forbidden. Use direct/anonymous class instead for better static analysis

```yaml
rules:
    - Symplify\PHPStanRules\Rules\PHPUnit\NoTestMocksRule
```

```php
use PHPUnit\Framework\TestCase;

final class SkipApiMock extends TestCase
{
    public function test()
    {
        $someTypeMock = $this->createMock(SomeType::class);
    }
}
```

:x:

<br>

```php
use PHPUnit\Framework\TestCase;

final class SkipApiMock extends TestCase
{
    public function test()
    {
        $someTypeMock = new class() implements SomeType {};
    }
}
```

:+1:

<br>

Happy coding!
