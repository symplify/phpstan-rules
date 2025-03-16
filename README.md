# PHPStan Rules

[![Downloads](https://img.shields.io/packagist/dt/symplify/phpstan-rules.svg?style=flat-square)](https://packagist.org/packages/symplify/phpstan-rules/stats)

Set of 65+ PHPStan fun and practical rules that check:

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

Later, once you have most rules applied, it's best practice to include whole sets:

```yaml
includes:
    - vendor/symplify/phpstan-rules/config/code-complexity-rules.neon
    - vendor/symplify/phpstan-rules/config/configurable-rules.neon
    - vendor/symplify/phpstan-rules/config/naming-rules.neon
    - vendor/symplify/phpstan-rules/config/static-rules.neon

    # project specific
    - vendor/symplify/phpstan-rules/config/rector-rules.neon
    - vendor/symplify/phpstan-rules/config/doctrine-rules.neon
    - vendor/symplify/phpstan-rules/config/symfony-rules.neon
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

### NoConstructorOverrideRule

Possible __construct() override, this can cause missing dependencies or setup

```yaml
rules:
    - Symplify\PHPStanRules\Rules\NoConstructorOverrideRule
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
        class: Symplify\PHPStanRules\Rules\ForbiddenNewArgumentRule
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


### NoGetInCommandRule

Prevents using `$this->get(...)` in commands, to promote dependency injection.


```yaml
rules:
    - Symplify\PHPStanRules\Rules\Symfony\NoGetInCommandRule
```

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

---

<br>

## 4. PHPUnit-specific Rules

### NoMockObjectAndRealObjectPropertyRule

Avoid using one property for both real object and mock object. Use separate properties or single type instead

```yaml
rules:
    - Symplify\PHPStanRules\Rules\PHPUnit\NoMockObjectAndRealObjectPropertyRule
```

<br>

### NoEntityMockingRule, NoDocumentMockingRule

Instead of entity or document mocking, create object directly to get better type support

```yaml
rules:
    - Symplify\PHPStanRules\Rules\PHPUnit\NoEntityMockingRule
    - Symplify\PHPStanRules\Rules\PHPUnit\NoDocumentMockingRule
```

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

### NoAssertFuncCallInTestsRule

Avoid using assert*() functions in tests, as they can lead to false positives

```yaml
rules:
    - Symplify\PHPStanRules\Rules\PHPUnit\NoAssertFuncCallInTestsRule
```

<br>

### NoMockOnlyTestRule

Test should have at least one non-mocked property, to test something

```yaml
rules:
    - Symplify\PHPStanRules\Rules\PHPUnit\NoMockOnlyTestRule
```

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

    private FirstMock $firstMock;

    public function setUp()
    {
        $this->someService = new SomeService();
        $this->firstMock = $this->createMock(AnotherService::class);
    }
}
```

:+1:

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


Happy coding!
