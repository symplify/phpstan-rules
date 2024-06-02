# 30 Rules Overview

## AnnotateRegexClassConstWithRegexLinkRule

Add regex101.com link to that shows the regex in practise, so it will be easier to maintain in case of bug/extension in the future

- class: [`Symplify\PHPStanRules\Rules\AnnotateRegexClassConstWithRegexLinkRule`](../src/Rules/AnnotateRegexClassConstWithRegexLinkRule.php)

```php
class SomeClass
{
    private const COMPLICATED_REGEX = '#some_complicated_stu|ff#';
}
```

:x:

<br>

```php
class SomeClass
{
    /**
     * @see https://regex101.com/r/SZr0X5/12
     */
    private const COMPLICATED_REGEX = '#some_complicated_stu|ff#';
}
```

:+1:

<br>

## CheckClassNamespaceFollowPsr4Rule

Class like namespace "%s" does not follow PSR-4 configuration in `composer.json`

- class: [`Symplify\PHPStanRules\Rules\CheckClassNamespaceFollowPsr4Rule`](../src/Rules/CheckClassNamespaceFollowPsr4Rule.php)

```php
// defined "Foo\Bar" namespace in composer.json > autoload > psr-4
namespace Foo;

class Baz
{
}
```

:x:

<br>

```php
// defined "Foo\Bar" namespace in composer.json > autoload > psr-4
namespace Foo\Bar;

class Baz
{
}
```

:+1:

<br>

## CheckRequiredInterfaceInContractNamespaceRule

Interface must be located in "Contract" or "Contracts" namespace

- class: [`Symplify\PHPStanRules\Rules\CheckRequiredInterfaceInContractNamespaceRule`](../src/Rules/CheckRequiredInterfaceInContractNamespaceRule.php)

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

## ClassNameRespectsParentSuffixRule

Class should have suffix "%s" to respect parent type

:wrench: **configure it!**

- class: [`Symplify\PHPStanRules\Rules\ClassNameRespectsParentSuffixRule`](../src/Rules/ClassNameRespectsParentSuffixRule.php)

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

## ExplicitClassPrefixSuffixRule

Interface have suffix of "Interface", trait have "Trait" suffix exclusively

- class: [`Symplify\PHPStanRules\Rules\Explicit\ExplicitClassPrefixSuffixRule`](../src/Rules/Explicit/ExplicitClassPrefixSuffixRule.php)

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

## ForbiddenArrayMethodCallRule

Array method calls [$this, "method"] are not allowed. Use explicit method instead to help PhpStorm, PHPStan and Rector understand your code

- class: [`Symplify\PHPStanRules\Rules\Complexity\ForbiddenArrayMethodCallRule`](../src/Rules/Complexity/ForbiddenArrayMethodCallRule.php)

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

## ForbiddenExtendOfNonAbstractClassRule

Only abstract classes can be extended

- class: [`Symplify\PHPStanRules\Rules\ForbiddenExtendOfNonAbstractClassRule`](../src/Rules/ForbiddenExtendOfNonAbstractClassRule.php)

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
final class SomeClass extends ParentClass
{
}

abstract class ParentClass
{
}
```

:+1:

<br>

## ForbiddenFuncCallRule

Function `"%s()"` cannot be used/left in the code

:wrench: **configure it!**

- class: [`Symplify\PHPStanRules\Rules\ForbiddenFuncCallRule`](../src/Rules/ForbiddenFuncCallRule.php)

```yaml
services:
    -
        class: Symplify\PHPStanRules\Rules\ForbiddenFuncCallRule
        tags: [phpstan.rules.rule]
        arguments:
            forbiddenFunctions:
                - eval
```

↓

```php
echo eval('...');
```

:x:

<br>

```php
echo '...';
```

:+1:

<br>

```yaml
services:
    -
        class: Symplify\PHPStanRules\Rules\ForbiddenFuncCallRule
        tags: [phpstan.rules.rule]
        arguments:
            forbiddenFunctions:
                dump: 'seems you missed some debugging function'
```

↓

```php
dump($value);
echo $value;
```

:x:

<br>

```php
echo $value;
```

:+1:

<br>

## ForbiddenMultipleClassLikeInOneFileRule

Multiple class/interface/trait is not allowed in single file

- class: [`Symplify\PHPStanRules\Rules\ForbiddenMultipleClassLikeInOneFileRule`](../src/Rules/ForbiddenMultipleClassLikeInOneFileRule.php)

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

## ForbiddenNodeRule

"%s" is forbidden to use

:wrench: **configure it!**

- class: [`Symplify\PHPStanRules\Rules\ForbiddenNodeRule`](../src/Rules/ForbiddenNodeRule.php)

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

## ForbiddenStaticClassConstFetchRule

Avoid static access of constants, as they can change value. Use interface and contract method instead

- class: [`Symplify\PHPStanRules\Rules\ForbiddenStaticClassConstFetchRule`](../src/Rules/ForbiddenStaticClassConstFetchRule.php)

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

## NoDynamicNameRule

Use explicit names over dynamic ones

- class: [`Symplify\PHPStanRules\Rules\NoDynamicNameRule`](../src/Rules/NoDynamicNameRule.php)

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

## NoEntityOutsideEntityNamespaceRule

Class with #[Entity] attribute must be located in "Entity" namespace to be loaded by Doctrine

- class: [`Symplify\PHPStanRules\Rules\NoEntityOutsideEntityNamespaceRule`](../src/Rules/NoEntityOutsideEntityNamespaceRule.php)

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

## NoGlobalConstRule

Global constants are forbidden. Use enum-like class list instead

- class: [`Symplify\PHPStanRules\Rules\NoGlobalConstRule`](../src/Rules/NoGlobalConstRule.php)

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

## NoInlineStringRegexRule

Use local named constant instead of inline string for regex to explain meaning by constant name

- class: [`Symplify\PHPStanRules\Rules\NoInlineStringRegexRule`](../src/Rules/NoInlineStringRegexRule.php)

```php
class SomeClass
{
    public function run($value)
    {
        return preg_match('#some_stu|ff#', $value);
    }
}
```

:x:

<br>

```php
class SomeClass
{
    /**
     * @var string
     */
    public const SOME_STUFF_REGEX = '#some_stu|ff#';

    public function run($value)
    {
        return preg_match(self::SOME_STUFF_REGEX, $value);
    }
}
```

:+1:

<br>

## NoReferenceRule

Use explicit return value over magic &reference

- class: [`Symplify\PHPStanRules\Rules\NoReferenceRule`](../src/Rules/NoReferenceRule.php)

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

## NoReturnArrayVariableListRule

Use value object over return of values

- class: [`Symplify\PHPStanRules\Rules\NoReturnArrayVariableListRule`](../src/Rules/NoReturnArrayVariableListRule.php)

```php
class ReturnVariables
{
    public function run($value, $value2): array
    {
        return [$value, $value2];
    }
}
```

:x:

<br>

```php
final class ReturnVariables
{
    public function run($value, $value2): ValueObject
    {
        return new ValueObject($value, $value2);
    }
}
```

:+1:

<br>

## NoReturnSetterMethodRule

Setter method cannot return anything, only set value

- class: [`Symplify\PHPStanRules\Rules\NoReturnSetterMethodRule`](../src/Rules/NoReturnSetterMethodRule.php)

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

## NoSingleInterfaceImplementerRule

Interface "%s" has only single implementer. Consider using the class directly as there is no point in using the interface.

- class: [`Symplify\PHPStanRules\Rules\NoSingleInterfaceImplementerRule`](../src/Rules/NoSingleInterfaceImplementerRule.php)

```php
class SomeClass implements SomeInterface
{
}

interface SomeInterface
{
}
```

:x:

<br>

```php
class SomeClass implements SomeInterface
{
}

class AnotherClass implements SomeInterface
{
}

interface SomeInterface
{
}
```

:+1:

<br>

## NoTestMocksRule

Mocking "%s" class is forbidden. Use direct/anonymous class instead for better static analysis

- class: [`Symplify\PHPStanRules\Rules\PHPUnit\NoTestMocksRule`](../src/Rules/PHPUnit/NoTestMocksRule.php)

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

## PreferredClassRule

Instead of "%s" class/interface use "%s"

:wrench: **configure it!**

- class: [`Symplify\PHPStanRules\Rules\PreferredClassRule`](../src/Rules/PreferredClassRule.php)

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

## PreventParentMethodVisibilityOverrideRule

Change `"%s()"` method visibility to "%s" to respect parent method visibility.

- class: [`Symplify\PHPStanRules\Rules\PreventParentMethodVisibilityOverrideRule`](../src/Rules/PreventParentMethodVisibilityOverrideRule.php)

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

## RegexSuffixInRegexConstantRule

Name your constant with "_REGEX" suffix, instead of "%s"

- class: [`Symplify\PHPStanRules\Rules\RegexSuffixInRegexConstantRule`](../src/Rules/RegexSuffixInRegexConstantRule.php)

```php
class SomeClass
{
    public const SOME_NAME = '#some\s+name#';

    public function run($value)
    {
        $somePath = preg_match(self::SOME_NAME, $value);
    }
}
```

:x:

<br>

```php
class SomeClass
{
    public const SOME_NAME_REGEX = '#some\s+name#';

    public function run($value)
    {
        $somePath = preg_match(self::SOME_NAME_REGEX, $value);
    }
}
```

:+1:

<br>

## RequireAttributeNameRule

Attribute must have all names explicitly defined

- class: [`Symplify\PHPStanRules\Rules\RequireAttributeNameRule`](../src/Rules/RequireAttributeNameRule.php)

```php
use Symfony\Component\Routing\Annotation\Route;

class SomeController
{
    #[Route("/path")]
    public function someAction()
    {
    }
}
```

:x:

<br>

```php
use Symfony\Component\Routing\Annotation\Route;

class SomeController
{
    #[Route(path: "/path")]
    public function someAction()
    {
    }
}
```

:+1:

<br>

## RequireAttributeNamespaceRule

Attribute must be located in "Attribute" namespace

- class: [`Symplify\PHPStanRules\Rules\Domain\RequireAttributeNamespaceRule`](../src/Rules/Domain/RequireAttributeNamespaceRule.php)

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

## RequireExceptionNamespaceRule

`Exception` must be located in "Exception" namespace

- class: [`Symplify\PHPStanRules\Rules\Domain\RequireExceptionNamespaceRule`](../src/Rules/Domain/RequireExceptionNamespaceRule.php)

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

## RequireInvokableControllerRule

Use invokable controller with `__invoke()` method instead of named action method

- class: [`Symplify\PHPStanRules\Symfony\Rules\RequireInvokableControllerRule`](../src/Symfony/Rules/RequireInvokableControllerRule.php)

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
use Symfony\Component\Routing\Annotation\Route;

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

## RequireUniqueEnumConstantRule

Enum constants "%s" are duplicated. Make them unique instead

- class: [`Symplify\PHPStanRules\Rules\Enum\RequireUniqueEnumConstantRule`](../src/Rules/Enum/RequireUniqueEnumConstantRule.php)

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

## SeeAnnotationToTestRule

Class "%s" is missing `@see` annotation with test case class reference

:wrench: **configure it!**

- class: [`Symplify\PHPStanRules\Rules\SeeAnnotationToTestRule`](../src/Rules/SeeAnnotationToTestRule.php)

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

## UppercaseConstantRule

Constant "%s" must be uppercase

- class: [`Symplify\PHPStanRules\Rules\UppercaseConstantRule`](../src/Rules/UppercaseConstantRule.php)

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
