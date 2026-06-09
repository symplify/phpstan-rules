# PHPStan Rules

[![Downloads](https://img.shields.io/packagist/dt/symplify/phpstan-rules.svg?style=flat-square)](https://packagist.org/packages/symplify/phpstan-rules/stats)

Set of 80+ PHPStan fun and practical rules that check:

* clean architecture, logical errors,
* naming, class namespace locations
* accidental visibility override,
* and Symfony, Doctrine or PHPUnit ~~best~~ proven practices.

Useful for any type of PHP project, from legacy to modern stack.

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
    - vendor/symplify/phpstan-rules/config/doctrine-rules.neon
    - vendor/symplify/phpstan-rules/config/symfony-rules.neon

    # special set for PHP configs
    - vendor/symplify/phpstan-rules/config/symfony-config-rules.neon
```

<br>

Do you use mocks in your PHPUnit tests? Enable mocking rules with single parameter:

```yaml
parameters:
    mocks: true
```

<br>

Want sharper type inference for service containers? The Symfony and Laravel return type extensions are **disabled by default** — enable the ones that fit your stack:

```yaml
parameters:
    symfonyReturnType: true
    laravelReturnType: true
```

`symfonyReturnType` resolves `$container->get(SomeService::class)` to `SomeService` and Symfony Finder's `$splFileInfo->getRealPath()` to `string`. `laravelReturnType` does the same for Laravel's `$container->make(SomeService::class)`:

```php
$service = $container->get(SomeService::class);
// $service is now known as SomeService, instead of plain object
```

<br>

But at start, make baby steps with one rule at a time:

Jump to: [Symfony-specific rules](#3-symfony-specific-rules), [Doctrine-specific rules](#2-doctrine-specific-rules), [PHPUnit-specific rules](#4-phpunit-specific-rules) or [PHPUnit mock rules](#5-phpunit-mock-rules).

<br>

## Special rules

### MaximumIgnoredErrorCountRule

Tired of ever growing ignored error count in your `phpstan.neon`? Set hard limit to keep them low:

```yaml
parameters:
    maximumIgnoredErrorCount: 50
```

<br>

### NewOverSettersRule

If a class is always created with the same set of setters, pass the values via constructor instead. It makes the object state explicit, safer and easier to test:

```php
$human = new Human();
$human->setName('Tomas');
$human->setAge(35);
```

:x:

<br>

```php
$human = new Human(name: 'Tomas', age: 35);
```

:+1:

<br>

Both `set*` and `add*` method prefixes are treated as setters. The rule is intentionally conservative — it only reports a class instantiated **at least twice** with the same set of setters each time. It skips Doctrine entities, Symfony `Kernel` subclasses, vendor code and `new` + setters blocks interrupted by a `return` or `throw`.

This rule is disabled by default. Enable it with the `ctor` parameter:

```yaml
parameters:
    ctor: true
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

### NoArrayMapWithArrayCallableRule

Array map with array callable is not allowed. Use anonymous/arrow function instead, to get better static analysis

```yaml
rules:
    - Symplify\PHPStanRules\Rules\Complexity\NoArrayMapWithArrayCallableRule
```

```php
$items = ['apple', 'banana', 'orange'];
$items = array_map(['SomeClass', 'method'], $items);
```

:x:

<br>

```php
$items = ['apple', 'banana', 'orange'];
$items = array_map(function ($item) {
    return $this->method($item);
}, $items);
```

:+1:

<br>

### NoConstructorOverrideRule

Possible __construct() override, this can cause missing dependencies or setup

```yaml
rules:
    - Symplify\PHPStanRules\Rules\Complexity\NoConstructorOverrideRule
```

```php
class ParentClass
{
    public function __construct(private string $dependency)
    {
    }
}

class SomeClass extends ParentClass
{
    public function __construct()
    {
    }
}
```

:x:

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

### NoProtectedClassStmtRule

Avoid protected class stmts as they yield unexpected behavior. Use clear interface contract instead

```yaml
rules:
    - Symplify\PHPStanRules\Rules\Explicit\NoProtectedClassStmtRule
```

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

### NoJustPropertyAssignRule

Instead of assigning service property to a variable, use the property directly

```yaml
rules:
    - Symplify\PHPStanRules\Rules\Complexity\NoJustPropertyAssignRule
```

```php
class SomeClass
{
    // ...

    public function run()
    {
        $someService = $this->someService;
        $someService->run();
    }
}
```

:x:

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

### ForbiddenNewArgumentRule

Type "%s" is forbidden to be created manually with `new X()`. Use service and constructor injection instead

```yaml
services:
    -
        class: Symplify\PHPStanRules\Rules\Complexity\ForbiddenNewArgumentRule
        tag: [phpstan.rules.rule]
        arguments:
            forbiddenTypes:
                - RepositoryService
```

↓

```php
class SomeService
{
    public function run()
    {
        $repositoryService = new RepositoryService();
        $item = $repositoryService->get(1);
    }
}
```

:x:

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

### RequiredOnlyInAbstractRule

`@required` annotation should be used only in abstract classes, to child classes can use clean `__construct()` service injection.

```yaml
rules:
    - Symplify\PHPStanRules\Rules\Symfony\RequiredOnlyInAbstractRule
```

<br>

### RequireRouteNameToGenerateControllerRouteRule

To pass a controller class to generate() method, the controller must have "#[Route(name: self::class)]" above the __invoke() method

```yaml
rules:
    - Symplify\PHPStanRules\Rules\Symfony\RequireRouteNameToGenerateControllerRouteRule
```

<br>

### SingleRequiredMethodRule

There must be maximum 1 @required method in the class. Merge it to one to avoid possible injection collision or duplicated injects.

```yaml
rules:
    - Symplify\PHPStanRules\Rules\Symfony\SingleRequiredMethodRule
```

<br>

### RequireAttributeNameRule

Attribute must have all names explicitly defined

```yaml
rules:
    - Symplify\PHPStanRules\Rules\RequireAttributeNameRule
```

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

### NoRouteTrailingSlashPathRule

Avoid trailing slash in route path, to prevent redirects and SEO issues

```yaml
rules:
    - Symplify\PHPStanRules\Rules\Symfony\NoRouteTrailingSlashPathRule
```

<br>

### RequireAttributeNamespaceRule

Attribute must be located in "Attribute" namespace

```yaml
rules:
    - Symplify\PHPStanRules\Rules\Domain\RequireAttributeNamespaceRule
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

### ForeachCeptionRule

Avoid more than 3 nested foreach loops. Refactor to a flatter approach or to a collection to avoid high complexity

```yaml
rules:
    - Symplify\PHPStanRules\Rules\Complexity\ForeachCeptionRule
```

```php
foreach ($items as $item) {
    foreach ($item->getChildren() as $child) {
        foreach ($child->getGrandchildren() as $grandchild) {
            foreach ($grandchild->getData() as $data) {
                // ...
            }
        }
    }
}
```

:x:

<br>

```php
foreach ($items as $item) {
    $this->processItem($item);
}
```

:+1:

<br>

### NoMissingVariableDimFetchRule

Dim fetch assign variable is missing, create it first

```yaml
rules:
    - Symplify\PHPStanRules\Rules\Explicit\NoMissingVariableDimFetchRule
```

```php
final class SomeClass
{
    public function run()
    {
        $dim['key'] = 'value';
    }
}
```

:x:

<br>

```php
final class SomeClass
{
    public function run()
    {
        $dim = [];
        $dim['key'] = 'value';
    }
}
```

:+1:

<br>

### NoMissnamedDocTagRule

Constant doc comment tag must be `@var`, not `@return` or other tags

```yaml
rules:
    - Symplify\PHPStanRules\Rules\NoMissnamedDocTagRule
```

```php
class SomeConstant
{
    /**
     * @return string
     */
    private const NAME = 'value';
}
```

:x:

<br>

```php
class SomeConstant
{
    /**
     * @var string
     */
    private const NAME = 'value';
}
```

:+1:

<br>

### NoValueObjectInServiceConstructorRule

Value object cannot be passed to constructor of a service. Pass it as a method argument instead

```yaml
rules:
    - Symplify\PHPStanRules\Rules\NoValueObjectInServiceConstructorRule
```

```php
final class SomeService
{
    public function __construct(private SomeValueObject $someValueObject)
    {
    }
}
```

:x:

<br>

```php
final class SomeService
{
    public function run(SomeValueObject $someValueObject)
    {
    }
}
```

:+1:

<br>

---

<br>

## 2. Doctrine-specific Rules

### RequireQueryBuilderOnRepositoryRule

Prevents using `$entityManager->createQueryBuilder('...')`,  use `$repository->createQueryBuilder()` as safer.

```yaml
rules:
    - Symplify\PHPStanRules\Rules\Doctrine\RequireQueryBuilderOnRepositoryRule
```

<br>

### NoGetRepositoryOutsideServiceRule

Instead of getting repository from EntityManager, use constructor injection and service pattern to keep code clean

```yaml
rules:
    - Symplify\PHPStanRules\Rules\Doctrine\NoGetRepositoryOutsideServiceRule
```

```php
class SomeClass
{
    public function run(EntityManagerInterface $entityManager)
    {
        return $entityManager->getRepository(SomeEntity::class);
    }
}
```

:x:

<br>

```php
class SomeClass
{
    public function __construct(SomeEntityRepository $someEntityRepository)
    {
    }
}
```

:+1:

<br>

### NoParentRepositoryRule

Repository should not extend parent repository, as it can lead to tight coupling

```yaml
rules:
    - Symplify\PHPStanRules\Rules\Doctrine\NoParentRepositoryRule
```

```php
use Doctrine\ORM\EntityRepository;

final class SomeRepository extends EntityRepository
{
}
```

:x:

<br>

```php
final class SomeRepository
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->repository = $entityManager->getRepository(SomeEntity::class);
    }
}
```

:+1:

<br>

### NoGetRepositoryOnServiceRepositoryEntityRule

Instead of calling "->getRepository(...::class)" service locator, inject service repository via constructor and use it directly

```yaml
rules:
    - Symplify\PHPStanRules\Rules\Doctrine\NoGetRepositoryOnServiceRepositoryEntityRule
```

<br>

```php
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SomeRepository::class)
 */
class SomeEntity
{
}
```

<br>

```php
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

final class SomeEntityRepository extends ServiceEntityRepository
{
}
```

<br>

```php
use Doctrine\ORM\EntityManagerInterface;

final class SomeService
{
    public function run(EntityManagerInterface $entityManager)
    {
        return $this->entityManager->getRepository(SomeEntity::class);
    }
}
```

:x:

<br>

```php
use Doctrine\ORM\EntityManagerInterface;

final class SomeService
{
    public function __construct(private SomeEntityRepository $someEntityRepository)
    {
    }
}
```

:+1:

<br>

### NoRepositoryCallInDataFixtureRule

Repository should not be called in data fixtures, as it can lead to tight coupling

```yaml
rules:
    - Symplify\PHPStanRules\Rules\Doctrine\NoRepositoryCallInDataFixtureRule
```

```php
use Doctrine\Common\DataFixtures\AbstractFixture;

final class SomeFixture extends AbstractFixture
{
    public function load(ObjectManager $objectManager)
    {
        $someRepository = $objectManager->getRepository(SomeEntity::class);
        $someEntity = $someRepository->get(1);
    }
}
```

:x:

<br>

```php
use Doctrine\Common\DataFixtures\AbstractFixture;

final class SomeFixture extends AbstractFixture
{
    public function load(ObjectManager $objectManager)
    {
        $someEntity = $this->getReference('some-entity-1');
    }
}
```

:+1:

<br>

---

<br>

## 3. Symfony-specific Rules

### FormTypeClassNameRule

```yaml
rules:
    - Symplify\PHPStanRules\Rules\Symfony\FormTypeClassNameRule
```

Classes that extend `AbstractType` should have `*FormType` suffix, to make it clear it's a form class.

<br>

### NoConstructorAndRequiredTogetherRule

Constructor injection and `#[Required]` method should not be used together in single class. Pick one, to keep architecture clean.

```yaml
rules:
    - Symplify\PHPStanRules\Rules\Symfony\NoConstructorAndRequiredTogetherRule
```

<br>

### NoGetDoctrineInControllerRule

Prevents using `$this->getDoctrine()` in controllers, to promote dependency injection.

```yaml
rules:
    - Symplify\PHPStanRules\Rules\Symfony\NoGetDoctrineInControllerRule
```

<br>

### NoGetInControllerRule

Prevents using `$this->get(...)` in controllers, to promote dependency injection.

```yaml
rules:
    - Symplify\PHPStanRules\Rules\Symfony\NoGetInControllerRule
```

<br>


### TaggedIteratorOverRepeatedServiceCallRuleTest

Instead of repeated "->call(%s, ...)" calls, pass services as tagged iterator argument to the constructor

```yaml
rules:
    - Symplify\PHPStanRules\Rules\Symfony\ConfigClosure\TaggedIteratorOverRepeatedServiceCallRule
```

```php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ref;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(SomeService::class)
        ->call('setService', [ref('service1')])
        ->call('setService', [ref('service2')])
        ->call('setService', [ref('service3')]);
};
```

:x:

<br>

```php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Loader\Configurator\tagged_iterator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(SomeService::class)
        ->arg('$services', tagged_iterator('SomeServiceTag'));
};
```

:+1:

<br>

### NoGetInCommandRule

Prevents using `$this->get(...)` in commands, to promote dependency injection.


```yaml
rules:
    - Symplify\PHPStanRules\Rules\Symfony\NoGetInCommandRule
```

<br>

### NoServiceSameNameSetClassRule

No need to duplicate service class and name. Use only "$services->set(%s::class)" instead

```yaml
rules:
    - Symplify\PHPStanRules\Rules\Symfony\ConfigClosure\NoServiceSameNameSetClassRule
```

```php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(SomeService::class, SomeService::class);
};
```

:x:

<br>

```php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(SomeService::class);
};
```

<br>

### PreferAutowireAttributeOverConfigParamRule

Instead of parameter reference in config, add #[Autowire(param: ...)] in the "%s" class constructor

```yaml
rules:
    - Symplify\PHPStanRules\Rules\Symfony\ConfigClosure\PreferAutowireAttributeOverConfigParamRule
```

```php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(SomeService::class)->args(['%some_param%']);
};
```

:x:

<br>

```php
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final class SomeService
{
    public function __construct(
        #[Autowire(param: 'some_param')]
        private string $someParam
    ) {
    }
}
```

:+1:

<br>

### NoDuplicateArgsAutowireByTypeRule

Instead of passing "%s" to args(), remove the line and let autowiring handle it

```yaml
rules:
    - Symplify\PHPStanRules\Rules\Symfony\ConfigClosure\NoDuplicateArgAutowireByTypeRule
    - Symplify\PHPStanRules\Rules\Symfony\ConfigClosure\NoDuplicateArgsAutowireByTypeRule
```

```php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(SomeService::class)
        ->args([
            ref(SomeService::class),
        ]);
};
```

:x:

<br>

```php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(SomeService::class);
};
```

:+1:

<br>

### NoAbstractControllerConstructorRule

Abstract controller should not have constructor, as it can lead to tight coupling. Use @required annotation instead

```yaml
rules:
    - Symplify\PHPStanRules\Rules\Symfony\NoAbstractControllerConstructorRule
```

```php
abstract class AbstractController extends Controller
{
    public function __construct(
        private SomeService $someService
    ) {
    }
}
```

:x:

<br>

```php
abstract class AbstractController extends Controller
{
    private $someService;

    #[Required]
    public function autowireAbstractController(SomeService $someService)
    {
        $this->someService = $someService;
    }
}
```

:+1:

<br>

### AlreadyRegisteredAutodiscoveryServiceRule

Remove service, as already registered via autodiscovery ->load(), no need to set it twice.

```yaml
rules:
    - Symplify\PHPStanRules\Rules\Symfony\ConfigClosure\AlreadyRegisteredAutodiscoveryServiceRule
```

```php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->load('App\\', __DIR__ . '/../src')
        ->exclude([__DIR__ . '/src/Services']);

    $services->set(SomeService::class);
};
```

:x:

<br>

```php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->load('App\\', __DIR__ . '/../src')
        ->exclude([__DIR__ . '/src/Services']);
};
```

:+1:

<br>

### ServicesExcludedDirectoryMustExistRule

Services excluded path must exist. If not, remove it

```yaml
rules:
    - Symplify\PHPStanRules\Rules\Symfony\ConfigClosure\ServicesExcludedDirectoryMustExistRule
```

```php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $configurator): void {
    $services = $configurator->serivces();

    $services->load('App\\', __DIR__ . '/../src')
        ->exclude([__DIR__ . '/this-path-does-not-exist']);
};
```

:x:

<br>

```php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $configurator): void {
    $services = $configurator->services();

    $services->load('App\\', __DIR__ . '/../src')
        ->exclude([__DIR__ . '/../src/ValueObject']);
};
```

:+1:

<br>

### NoBundleResourceConfigRule

Avoid using configs in `*Bundle/Resources` directory. Move them to `/config` directory instead

```yaml
rules:
    - Symplify\PHPStanRules\Rules\Symfony\ConfigClosure\NoBundleResourceConfigRule
```

<br>

### NoBareAndSecurityIsGrantedContentsRule

Instead of using one long "and" condition join, split into multiple standalone #[IsGranted] attributes

```yaml
rules:
    - Symplify\PHPStanRules\Rules\Symfony\NoBareAndSecurityIsGrantedContentsRule
```

```php
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('has_role(ROLE_USER) and has_role(ROLE_ADMIN)')]
class SomeController
{
}
```

:x:

<br>

```php
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
#[IsGranted('ROLE_ADMIN')]
class SomeController
{
}
```

:+1:


### RequireIsGrantedEnumRule

Instead of string, use enum constant for #[IsGranted]

```yaml
rules:
    - Symplify\PHPStanRules\Rules\Symfony\RequireIsGrantedEnumRule
```

```php
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class SomeController
{
}
```

:x:

<br>

```php
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(SomeEnum::ROLE_USER)]
class SomeController
{
}
```

:+1:


### NoRoutingPrefixRule

Avoid global route prefixing. Use single place for paths in @Route/#[Route] and improve static analysis instead.

```yaml
rules:
    - Symplify\PHPStanRules\Rules\Symfony\NoRoutingPrefixRule
```


```php
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routingConfigurator): void {
    $routingConfigurator->import(__DIR__ . '/some-path')
        ->prefix('/some-prefix');
};
```

:x:

<br>

```php
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routingConfigurator): void {
    $routingConfigurator->import(__DIR__ . '/some-path');
};
```

:+1:

<br>

### NoClassLevelRouteRule

Avoid class-level route prefixing. Use method route to keep single source of truth and focus

```yaml
rules:
    - Symplify\PHPStanRules\Rules\Symfony\NoClassLevelRouteRule
```

```php
use Symfony\Component\Routing\Attribute\Route;

#[Route('/some-prefix')]
class SomeController
{
    #[Route('/some-action')]
    public function someAction()
    {
    }
}
```

:x:

<br>

```php
use Symfony\Component\Routing\Attribute\Route;

class SomeController
{
    #[Route('/some-prefix/some-action')]
    public function someAction()
    {
    }
}
```

:+1:

<br>


### NoFindTaggedServiceIdsCallRule

Instead of "$this->findTaggedServiceIds()" use more reliable registerForAutoconfiguration() and tagged iterator attribute. Those work outside any configuration and avoid missed tag errors

```yaml
rules:
    - Symplify\PHPStanRules\Rules\Symfony\NoFindTaggedServiceIdsCallRule
```

<br>

### NoRequiredOutsideClassRule

Symfony #[Require]/@required should be used only in classes to avoid misuse

```yaml
rules:
    - Symplify\PHPStanRules\Rules\Symfony\NoRequiredOutsideClassRule
```

```php
use Symfony\Component\DependencyInjection\Attribute\Required;

trait SomeTrait
{
    #[Required]
    public function autowireSomeTrait(SomeService $someService)
    {
        // ...
    }
}
```

:x:

<br>

```php
abstract class SomeClass
{
    #[Required]
    public function autowireSomeClass(SomeService $someService)
    {
        // ...
    }
}
```

:+1:

<br>

### SingleArgEventDispatchRule

The event dispatch() method can have only 1 arg - the event object

```yaml
rules:
    - Symplify\PHPStanRules\Rules\Symfony\SingleArgEventDispatchRule
```

```php
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class SomeClass
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher
    ) {
    }

    public function run()
    {
        $this->eventDispatcher->dispatch('event', 'another-arg');
    }
}
```

:x:

<br>

```php
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class SomeClass
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher
    ) {
    }

    public function run()
    {
        $this->eventDispatcher->dispatch(new EventObject());
    }
}
```

:+1:

<br>

### NoListenerWithoutContractRule

There should be no listeners modified in config. Use EventSubscriberInterface contract or #[AsEventListener] attribute and PHP instead

```yaml
rules:
    - Symplify\PHPStanRules\Rules\Symfony\NoListenerWithoutContractRule
```

```php
class SomeListener
{
    public function onEvent()
    {
    }
}
```

:x:

<br>

```php
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SomeListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            'event' => 'onEvent',
        ];
    }

    public function onEvent()
    {
    }
}
```

:+1:

<br>

```php
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener]
class SomeListener
{
    public function __invoke()
    {
    }
}
```

:+1:

<br>

### RequireServiceRepositoryParentRule

Repository must extend *, so it can be injected as a service

```yaml
rules:
    - Symplify\PHPStanRules\Rules\Doctrine\RequireServiceRepositoryParentRule
```

```php
final class SomeRepository
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        // ...
    }
}
```

:x:

<br>

```php
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class SomeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SomeEntity::class);
    }
}
```

:+1:

<br>

### NoDoctrineListenerWithoutContractRule

There should be no Doctrine listeners modified in config. Implement  "Document\Event\EventSubscriber" to provide events in the class itself

```yaml
rules:
    - Symplify\PHPStanRules\Rules\Doctrine\NoDoctrineListenerWithoutContractRule
```

```php
class SomeListener
{
    public function onFlush()
    {
    }
}
```

:x:

<br>

```php
use Doctrine\Common\EventSubscriber;
use Doctrine\ODM\MongoDB\Events;

class SomeListener implements EventSubscriber
{
    public function onFlush()
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            Events::onFlush
        ];
    }
}
```

:+1:


### NoStringInGetSubscribedEventsRule

Symfony getSubscribedEvents() method must contain only event class references, no strings

```yaml
rules:
    - Symplify\PHPStanRules\Rules\Symfony\NoStringInGetSubscribedEventsRule
```

```php
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SomeListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            'event' => 'onEvent',
        ];
    }

    public function onEvent()
    {
    }
}
```

:x:

<br>

```php
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SomeListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            Event::class => 'onEvent',
        ];
    }

    public function onEvent()
    {
    }
}
```

:+1:

<br>

### RequireInvokableControllerRule

Use invokable controller with __invoke() method instead of named action method

```yaml
rules:
    - Symplify\PHPStanRules\Rules\Symfony\RequireInvokableControllerRule
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

### NoControllerMethodInjectionRule

Instead of action method service injection, use `__construct()` and an invokable controller with `__invoke()` to clearly separate services and parameters

```yaml
rules:
    - Symplify\PHPStanRules\Rules\Symfony\NoControllerMethodInjectionRule
```

```php
use Symfony\Component\Routing\Annotation\Route;

final class SomeController
{
    #[Route('/some-action')]
    public function someAction(SomeService $someService)
    {
    }
}
```

:x:

<br>

```php
use Symfony\Component\Routing\Annotation\Route;

final class SomeController
{
    public function __construct(private SomeService $someService)
    {
    }

    #[Route('/some-action')]
    public function __invoke()
    {
    }
}
```

:+1:

<br>

### NoServiceAutowireDuplicateRule

Service `autowire()` is called as a duplicate of `$services->defaults()->autowire()`. Remove it on the service.

```yaml
rules:
    - Symplify\PHPStanRules\Rules\Symfony\NoServiceAutowireDuplicateRule
```

```php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->defaults()->autowire();

    $services->set('some_service')
        ->autowire();
};
```

:x:

<br>

```php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->defaults()->autowire();

    $services->set('some_service');
};
```

:+1:

<br>

### NoSetClassServiceDuplicationRule

Instead of `$services->set(X)->class(X)` that brings no value, use simple `$services->set(X)`

```yaml
rules:
    - Symplify\PHPStanRules\Rules\Symfony\ConfigClosure\NoSetClassServiceDuplicationRule
```

```php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(SomeService::class)
        ->class(SomeService::class);
};
```

:x:

<br>

```php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(SomeService::class);
};
```

:+1:

<br>

### FileNameMatchesExtensionRule

The config uses a specific extension, but the file name does not match. Sync them to ease discovery.

```yaml
rules:
    - Symplify\PHPStanRules\Rules\Symfony\ConfigClosure\FileNameMatchesExtensionRule
```

```php
// wrong_name.php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $container) {
    $framework = $container->extension('framework');
};
```

:x:

<br>

```php
// framework.php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $container) {
    $framework = $container->extension('framework');
};
```

:+1:

<br>

---

<br>

## 4. PHPUnit-specific Rules

### NoAssertFuncCallInTestsRule

Avoid using assert*() functions in tests, as they can lead to false positives

```yaml
rules:
    - Symplify\PHPStanRules\Rules\PHPUnit\NoAssertFuncCallInTestsRule
```

<br>

### PublicStaticDataProviderRule

PHPUnit data provider method "%s" must be public

```yaml
rules:
    - Symplify\PHPStanRules\Rules\PHPUnit\PublicStaticDataProviderRule
```

```php
use PHPUnit\Framework\TestCase;

final class SomeTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function test(): array
    {
        return [];
    }

    protected function dataProvider(): array
    {
        return [];
    }
}
```

:x:

<br>

```php
use PHPUnit\Framework\TestCase;

final class SomeTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function test(): array
    {
        return [];
    }

    public static function dataProvider(): array
    {
        return [];
    }
}
```

:+1:

<br>

## 5. PHPUnit Mock Rules

* Do you use extensive mocking in your PHPUnit tests?
* Do you want to keep your tests clean, maintainable and avoid upgrade hell in the future?
* Do you want to have tests that actually test something?

This set is for you! Enable all mocking rules with single parameter in your `phpstan.neon`:

```yaml
parameters:
    mocks: true
```

<br>

### NoMockOnlyTestRule

Test should have at least one non-mocked property, to test something

```php
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class SomeTest extends TestCase
{
    private MockObject $firstMock;
    private MockObject $secondMock;

    public function setUp()
    {
        $this->firstMock = $this->createMock(SomeService::class);
        $this->secondMock = $this->createMock(AnotherService::class);
    }
}
```

:x:

<br>

```php
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class SomeTest extends TestCase
{
    private SomeService $someService;

    private MockObject $firstMock;

    public function setUp()
    {
        $this->someService = new SomeService();
        $this->firstMock = $this->createMock(AnotherService::class);
    }
}
```

:+1:

<br>

### NoMockObjectAndRealObjectPropertyRule

Avoid using one property for both real object and mock object. Use separate properties or single type instead

```php
$this->service = $this->createMock(Service::class);
$this->service = new Service();
```

:x:

<br>

```php
$this->someMock = $this->createMock(AnotherService::class);

$this->realService = new Service();
```

:+1:

<br>

### NoDoubleConsecutiveTestMockRule

Do not use `willReturnOnConsecutiveCalls()` and `willReturnCallback()` on the same mock. Use `willReturnCallback()` only instead to make the test more clear.

```php
use PHPUnit\Framework\TestCase;

final class SomeTest extends TestCase
{
    public function test()
    {
        $this->createMock('SomeClass')
            ->expects($this->exactly(2))
            ->method('someMethod')
            ->willReturnCallback(function () {
                return 'first';
            })
            ->willReturnOnConsecutiveCalls('first');
    }
}
```

:x:

<br>

```php
use PHPUnit\Framework\TestCase;

final class SomeTest extends TestCase
{
    public function test()
    {
        $this->createMock('SomeClass')
            ->expects($this->exactly(2))
            ->method('someMethod')
            ->willReturnCallback(function () {
                return 'first';
            });
    }
}
```

:+1:

<br>

### ExplicitExpectsMockMethodRule

PHPUnit mock method is missing explicit `expects()`, e.g. `$this->mock->expects($this->once())->...`. This is required since PHPUnit 12 to avoid silent stubs.

```php
use PHPUnit\Framework\TestCase;

final class SomeTest extends TestCase
{
    public function test(): void
    {
        $mock = $this->createMock(\stdClass::class);
        $mock->method('someMethod')->willReturn('value');
    }
}
```

:x:

<br>

```php
use PHPUnit\Framework\TestCase;

final class SomeTest extends TestCase
{
    public function test(): void
    {
        $mock = $this->createMock(\stdClass::class);

        $mock->expects($this->atLeastOnce())
            ->method('someMethod')
            ->willReturn('value');
    }
}
```

:+1:

<br>

### AvoidAnyExpectsRule

Disallow usage of `any()` expectation in mocks to ensure that all mock interactions are explicitly defined and verified.

```php
$someMock = $this->createMock(Service::class);
$someMock->expects($this->any())
    ->method('calculate')
    ->willReturn(10);
```

:x:

<br>

```php
$someMock = $this->createMock(Service::class);
$someMock->expects($this->once())
    ->method('calculate')
    ->willReturn(10);
```

:+1:

<br>

### NoWithOnStubRule

Disallow `with()` on stubs (mocks without `expects()`). PHPUnit deprecates `with()` on test stubs because they silently swallow argument mismatches.

```php
$someMock = $this->createMock(Service::class);
$someMock->method('calculate')
    ->with(10)
    ->willReturn(20);
```

:x:

<br>

```php
$someMock = $this->createMock(Service::class);
$someMock->expects($this->once())
    ->method('calculate')
    ->with(10)
    ->willReturn(20);
```

:+1:

<br>

### RequireAtLeastOneRule

Disallow `atLeast(0)` on mock expectations, as it matches any number of calls (including zero) and provides no real verification. Require a value of `1` or higher.

```php
$someMock = $this->createMock(Service::class);
$someMock->expects($this->atLeast(0))
    ->method('calculate')
    ->willReturn(10);
```

:x:

<br>

```php
$someMock = $this->createMock(Service::class);
$someMock->expects($this->atLeast(1))
    ->method('calculate')
    ->willReturn(10);
```

:+1:

<br>

### NoEntityMockingRule, NoDocumentMockingRule

Instead of entity or document mocking, create object directly to get better type support

```php
use PHPUnit\Framework\TestCase;

final class SomeTest extends TestCase
{
    public function test()
    {
        $someEntityMock = $this->createMock(SomeEntity::class);
    }
}
```

:x:

<br>

```php
use PHPUnit\Framework\TestCase;

final class SomeTest extends TestCase
{
    public function test()
    {
        $someEntityMock = new SomeEntity();
    }
}
```

:+1:

<br>

## 6. Type Extensions and Error Formatter

These extensions were merged from the now-deprecated [`symplify/phpstan-extensions`](https://github.com/symplify/phpstan-extensions)
package. They load automatically once `phpstan/extension-installer` is set up - no extra
configuration is needed.

<br>

### `symplify` Error Formatter

A compact error format with pre-escaped, regex-ready messages that are easy to copy into
your `ignoreErrors` list. File paths are printed with line numbers and stay clickable in
the terminal (works best with [anthraxx/intellij-awesome-console](https://github.com/anthraxx/intellij-awesome-console)).

Enable it in your `phpstan.neon`:

```yaml
parameters:
    errorFormat: symplify
```

or on the command line:

```bash
vendor/bin/phpstan analyse --error-format symplify
```

<br>

### Type Extensions

Always-on return type extensions that sharpen PHPStan inference for common framework calls:

* **`ContainerGetReturnTypeExtension`** - `$container->get(SomeService::class)` returns
  `SomeService` instead of plain `object` (Symfony `ContainerInterface`).

* **`LaravelContainerMakeTypeExtension`** - `$container->make(SomeService::class)` and
  `->get(SomeService::class)` return `SomeService` (Laravel `Illuminate\Container\Container`).

* **`SplFileInfoTolerantReturnTypeExtension`** - `$splFileInfo->getRealPath()` returns
  `string` instead of `string|false`, as Symfony Finder only yields existing files.

* **`NativeFunctionReturnTypeExtension`** - `getcwd()`, `dirname()` and `realpath()` return
  `string` instead of `string|false`.

<br>

Happy coding!
