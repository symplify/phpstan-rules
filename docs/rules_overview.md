# 45 Rules Overview

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

namespace App\Contracts\Repository;

interface ProductRepositoryInterface
{
}
```

:+1:

<br>

## CheckTypehintCallerTypeRule

Parameter %d should use "%s" type as the only type passed to this method

- class: [`Symplify\PHPStanRules\Rules\CheckTypehintCallerTypeRule`](../src/Rules/CheckTypehintCallerTypeRule.php)

```php
use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;

class SomeClass
{
    public function run(MethodCall $node)
    {
        $this->isCheck($node);
    }

    private function isCheck(Node $node)
    {
    }
}
```

:x:

<br>

```php
use PhpParser\Node\Expr\MethodCall;

class SomeClass
{
    public function run(MethodCall $node)
    {
        $this->isCheck($node);
    }

    private function isCheck(MethodCall $node)
    {
    }
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

## ForbiddenParamTypeRemovalRule

Removing parent param type is forbidden

- class: [`Symplify\PHPStanRules\Rules\ForbiddenParamTypeRemovalRule`](../src/Rules/ForbiddenParamTypeRemovalRule.php)

```php
interface RectorInterface
{
    public function refactor(Node $node);
}

final class SomeRector implements RectorInterface
{
    public function refactor($node)
    {
    }
}
```

:x:

<br>

```php
interface RectorInterface
{
    public function refactor(Node $node);
}

final class SomeRector implements RectorInterface
{
    public function refactor(Node $node)
    {
    }
}
```

:+1:

<br>

## ForbiddenSameNamedNewInstanceRule

New objects with "%s" name are overridden. This can lead to unwanted bugs, please pick a different name to avoid it.

- class: [`Symplify\PHPStanRules\Rules\Complexity\ForbiddenSameNamedNewInstanceRule`](../src/Rules/Complexity/ForbiddenSameNamedNewInstanceRule.php)

```php
$product = new Product();
$product = new Product();

$this->productRepository->save($product);
```

:x:

<br>

```php
$firstProduct = new Product();
$secondProduct = new Product();

$this->productRepository->save($firstProduct);
```

:+1:

<br>

## NarrowPublicClassMethodParamTypeByCallerTypeRule

Parameters should use "%s" types as the only types passed to this method

- class: [`Symplify\PHPStanRules\Rules\NarrowType\NarrowPublicClassMethodParamTypeByCallerTypeRule`](../src/Rules/NarrowType/NarrowPublicClassMethodParamTypeByCallerTypeRule.php)

```php
use PhpParser\Node\Expr\MethodCall;

final class SomeClass
{
    public function run(SomeService $someService, MethodCall $methodCall)
    {
        $someService->isCheck($node);
    }
}

final class SomeService
{
    public function isCheck($methodCall)
    {
    }
}
```

:x:

<br>

```php
use PhpParser\Node\Expr\MethodCall;

final class SomeClass
{
    public function run(SomeService $someService, MethodCall $methodCall)
    {
        $someService->isCheck($node);
    }
}

final class SomeService
{
    public function isCheck(MethodCall $methodCall)
    {
    }
}
```

:+1:

<br>

## NoAbstractMethodRule

Use explicit interface contract or a service over unclear abstract methods

- class: [`Symplify\PHPStanRules\Rules\NoAbstractMethodRule`](../src/Rules/NoAbstractMethodRule.php)

```php
abstract class SomeClass
{
    abstract public function run();
}
```

:x:

<br>

```php
abstract class SomeClass implements RunnableInterface
{
}

interface RunnableInterface
{
    public function run();
}
```

:+1:

<br>

## NoArrayAccessOnObjectRule

Use explicit methods over array access on object

- class: [`Symplify\PHPStanRules\Rules\NoArrayAccessOnObjectRule`](../src/Rules/NoArrayAccessOnObjectRule.php)

```php
class SomeClass
{
    public function run(MagicArrayObject $magicArrayObject)
    {
        return $magicArrayObject['more_magic'];
    }
}
```

:x:

<br>

```php
class SomeClass
{
    public function run(MagicArrayObject $magicArrayObject)
    {
        return $magicArrayObject->getExplicitValue();
    }
}
```

:+1:

<br>

## NoDuplicatedShortClassNameRule

Class with base "%s" name is already used in "%s". Use unique name to make classes easy to recognize

:wrench: **configure it!**

- class: [`Symplify\PHPStanRules\Rules\NoDuplicatedShortClassNameRule`](../src/Rules/NoDuplicatedShortClassNameRule.php)

```yaml
services:
    -
        class: Symplify\PHPStanRules\Rules\NoDuplicatedShortClassNameRule
        tags: [phpstan.rules.rule]
        arguments:
            toleratedNestingLevel: 1
```

↓

```php
namespace App;

class SomeClass
{
}

namespace App\Nested;

class SomeClass
{
}
```

:x:

<br>

```php
namespace App;

class SomeClass
{
}

namespace App\Nested;

class AnotherClass
{
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

## NoEmptyClassRule

There should be no empty class

- class: [`Symplify\PHPStanRules\Rules\NoEmptyClassRule`](../src/Rules/NoEmptyClassRule.php)

```php
class SomeClass
{
}
```

:x:

<br>

```php
class SomeClass
{
    public function getSome()
    {
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

## NoIssetOnObjectRule

Use default null value and nullable compare instead of isset on object

- class: [`Symplify\PHPStanRules\Rules\NoIssetOnObjectRule`](../src/Rules/NoIssetOnObjectRule.php)

```php
class SomeClass
{
    public function run()
    {
        if (random_int(0, 1)) {
            $object = new SomeClass();
        }

        if (isset($object)) {
            return $object;
        }
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
        $object = null;
        if (random_int(0, 1)) {
            $object = new SomeClass();
        }

        if ($object !== null) {
            return $object;
        }
    }
}
```

:+1:

<br>

## NoMissingDirPathRule

The path "%s" was not found

- class: [`Symplify\PHPStanRules\Rules\NoMissingDirPathRule`](../src/Rules/NoMissingDirPathRule.php)

```php
$filePath = __DIR__ . '/missing_location.txt';
```

:x:

<br>

```php
$filePath = __DIR__ . '/existing_location.txt';
```

:+1:

<br>

## NoMixedMethodCallerRule

Anonymous variable in a `%s->...()` method call can lead to false dead methods. Make sure the variable type is known

- class: [`Symplify\PHPStanRules\Rules\Explicit\NoMixedMethodCallerRule`](../src/Rules/Explicit/NoMixedMethodCallerRule.php)

```php
function run($unknownType)
{
    return $unknownType->call();
}
```

:x:

<br>

```php
function run(KnownType $knownType)
{
    return $knownType->call();
}
```

:+1:

<br>

## NoMixedPropertyFetcherRule

Anonymous variables in a "%s->..." property fetch can lead to false dead property. Make sure the variable type is known

- class: [`Symplify\PHPStanRules\Rules\Explicit\NoMixedPropertyFetcherRule`](../src/Rules/Explicit/NoMixedPropertyFetcherRule.php)

```php
function run($unknownType)
{
    return $unknownType->name;
}
```

:x:

<br>

```php
function run(KnownType $knownType)
{
    return $knownType->name;
}
```

:+1:

<br>

## NoNullableArrayPropertyRule

Use required typed property over of nullable array property

- class: [`Symplify\PHPStanRules\Rules\NoNullableArrayPropertyRule`](../src/Rules/NoNullableArrayPropertyRule.php)

```php
final class SomeClass
{
    private ?array $property = null;
}
```

:x:

<br>

```php
final class SomeClass
{
    private array $property = [];
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

## NoRelativeFilePathRule

Relative file path "%s" is not allowed, use absolute one with __DIR__

- class: [`Symplify\PHPStanRules\Rules\Explicit\NoRelativeFilePathRule`](../src/Rules/Explicit/NoRelativeFilePathRule.php)

```php
$filePath = 'some_file.txt';
```

:x:

<br>

```php
$filePath = __DIR__ . '/some_file.txt';
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

## NoReturnFalseInNonBoolClassMethodRule

Returning false in non return bool class method. Use null instead

- class: [`Symplify\PHPStanRules\Rules\NarrowType\NoReturnFalseInNonBoolClassMethodRule`](../src/Rules/NarrowType/NoReturnFalseInNonBoolClassMethodRule.php)

```php
class SomeClass
{
    /**
     * @var Item[]
     */
    private $items = [];

    public function getItem($key)
    {
        if (isset($this->items[$key])) {
            return $this->items[$key];
        }

        return false;
    }
}
```

:x:

<br>

```php
class SomeClass
{
    /**
     * @var Item[]
     */
    private $items = [];

    public function getItem($key): ?Item
    {
        if (isset($this->items[$key])) {
            return $this->items[$key];
        }

        return null;
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

## NoShortNameRule

Do not name "%s", shorter than %d chars

:wrench: **configure it!**

- class: [`Symplify\PHPStanRules\ObjectCalisthenics\Rules\NoShortNameRule`](../src/ObjectCalisthenics/Rules/NoShortNameRule.php)

```yaml
services:
    -
        class: Symplify\PHPStanRules\ObjectCalisthenics\Rules\NoShortNameRule
        tags: [phpstan.rules.rule]
        arguments:
            minNameLength: 3
```

↓

```php
function is()
{
}
```

:x:

<br>

```php
function isClass()
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

## NoVoidGetterMethodRule

Getter method must return something, not void

- class: [`Symplify\PHPStanRules\Rules\NoVoidGetterMethodRule`](../src/Rules/NoVoidGetterMethodRule.php)

```php
final class SomeClass
{
    public function getData(): void
    {
        // ...
    }
}
```

:x:

<br>

```php
final class SomeClass
{
    public function getData(): array
    {
        // ...
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

## RequireEnumDocBlockOnConstantListPassRule

On passing a constant, the method should have an enum type. See https://phpstan.org/writing-php-code/phpdoc-types#literals-and-constants

- class: [`Symplify\PHPStanRules\Rules\Enum\RequireEnumDocBlockOnConstantListPassRule`](../src/Rules/Enum/RequireEnumDocBlockOnConstantListPassRule.php)

```php
final class Direction
{
    public const LEFT = 'left';

    public const RIGHT = 'right';
}

final class Driver
{
    public function goToWork()
    {
        $this->turn(Direction::LEFT);
    }

    private function turn(string $direction)
    {
        // ...
    }
}
```

:x:

<br>

```php
final class Direction
{
    public const LEFT = 'left';

    public const RIGHT = 'right';
}

final class Driver
{
    public function goToWork()
    {
        $this->turn(Direction::LEFT);
    }

    /**
     * @param Direction::*
     */
    private function turn(string $direction)
    {
        // ...
    }
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

## RequireSpecificReturnTypeOverAbstractRule

Provide more specific return type "%s" over abstract one

- class: [`Symplify\PHPStanRules\Rules\Explicit\RequireSpecificReturnTypeOverAbstractRule`](../src/Rules/Explicit/RequireSpecificReturnTypeOverAbstractRule.php)

```php
final class IssueControlFactory
{
    public function create(): Control
    {
        return new IssueControl();
    }
}

final class IssueControl extends Control
{
}
```

:x:

<br>

```php
final class IssueControlFactory
{
    public function create(): IssueControl
    {
        return new IssueControl();
    }
}

final class IssueControl extends Control
{
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
