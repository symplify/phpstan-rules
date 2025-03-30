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
    - vendor/symplify/phpstan-rules/config/doctrine-rules.neon
    - vendor/symplify/phpstan-rules/config/symfony-rules.neon

    # special set for PHP configs
    - vendor/symplify/phpstan-rules/config/symfony-config-rules.neon
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

> [!WARNING]
> ```php
> function run($userId)
> {
> }
> ```

> [!TIP]
> ```php
> function run(int $userId)
> {
> }
> ```

<br>

### CheckRequiredInterfaceInContractNamespaceRule

Interface must be located in "Contract" or "Contracts" namespace

```yaml
rules:
    - Symplify\PHPStanRules\Rules\CheckRequiredInterfaceInContractNamespaceRule
```

> [!WARNING]
> ```php
> namespace App\Repository;
>
> interface ProductRepositoryInterface
> {
> }
> ```

> [!TIP]
> ```php
> namespace App\Contract\Repository;
>
> interface ProductRepositoryInterface
> {
> }
> ```

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

> [!WARNING]
> ```php
> class Some extends Command
> {
> }
> ```

> [!TIP]
> ```php
> class SomeCommand extends Command
> {
> }
> ```

### StringFileAbsolutePathExistsRule

Absolute file path must exist. Checked suffixes are "yaml", "yml", "sql", "php" and "json".

```yaml
rules:
    - Symplify\PHPStanRules\Rules\StringFileAbsolutePathExistsRule
```

> [!WARNING]
> ```php
> // missing file path
> return __DIR__  . '/some_file.yml';
> ```

> [!TIP]
> ```php
> // correct file path
> return __DIR__  . '/../fixtures/some_file.yml';
> ```

### NoConstructorOverrideRule

Possible __construct() override, this can cause missing dependencies or setup

```yaml
rules:
    - Symplify\PHPStanRules\Rules\Complexity\NoConstructorOverrideRule
```

> [!WARNING]
> ```php
> class ParentClass
> {
>     public function __construct(private string $dependency)
>     {
>     }
> }
>
> class SomeClass extends ParentClass
> {
>     public function __construct()
>     {
>     }
> }
> ```

> [!TIP]
> ```php
> final class SomeClass extends ParentClass
> {
>     public function __construct(private string $dependency)
>     {
>     }
> }
> ```

### ExplicitClassPrefixSuffixRule

Interface have suffix of "Interface", trait have "Trait" suffix exclusively

```yaml
rules:
    - Symplify\PHPStanRules\Rules\Explicit\ExplicitClassPrefixSuffixRule
```

> [!WARNING]
> ```php
> <?php
>
> interface NotSuffixed
> {
> }
>
> trait NotSuffixed
> {
> }
>
> abstract class NotPrefixedClass
> {
> }
> ```

> [!TIP]
> ```php
> <?php
>
> interface SuffixedInterface
> {
> }
>
> trait SuffixedTrait
> {
> }
>
> abstract class AbstractClass
> {
> }
> ```

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

> [!WARNING]
> ```php
> usort($items, [$this, "method"]);
> ```

> [!TIP]
> ```php
> usort($items, function (array $apples) {
>     return $this->method($apples);
> };
> ```

### NoJustPropertyAssignRule

Instead of assigning service property to a variable, use the property directly

```yaml
rules:
    - Symplify\PHPStanRules\Rules\Complexity\NoJustPropertyAssignRule
```

> [!WARNING]
> ```php
> class SomeClass
> {
>     // ...
>
>     public function run()
>     {
>         $someService = $this->someService;
>         $someService->run();
>     }
> }
> ```

> [!TIP]
> ```php
> class SomeClass
> {
>     // ...
>
>     public function run()
>     {
>         $this->someService->run();
>     }
> }
> ```

### ForbiddenExtendOfNonAbstractClassRule

Only abstract classes can be extended

```yaml
rules:
    - Symplify\PHPStanRules\Rules\ForbiddenExtendOfNonAbstractClassRule
```

> [!WARNING]
> ```php
> final class SomeClass extends ParentClass
> {
> }
>
> class ParentClass
> {
> }
> ```

> [!TIP]
> ```php
> abstract class ParentClass
> {
> }
> ```

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

> [!WARNING]
> ```php
> class SomeService
> {
>     public function run()
>     {
>         $repositoryService = new RepositoryService();
>         $item = $repositoryService->get(1);
>     }
> }
> ```

> [!TIP]
> ```php
> class SomeService
> {
>     public function __construct(private RepositoryService $repositoryService)
>     {
>     }
>
>     public function run()
>     {
>         $item = $this->repositoryService->get(1);
>     }
> }
> ```

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

> [!WARNING]
> ```php
> dump('...');
> ```

> [!TIP]
> ```php
> echo '...';
> ```

### ForbiddenMultipleClassLikeInOneFileRule

Multiple class/interface/trait is not allowed in single file

```yaml
rules:
    - Symplify\PHPStanRules\Rules\ForbiddenMultipleClassLikeInOneFileRule
```

> [!WARNING]
> ```php
> // src/SomeClass.php
> class SomeClass
> {
> }
>
> interface SomeInterface
> {
> }
> ```

> [!TIP]
> ```php
> // src/SomeClass.php
> class SomeClass
> {
> }
>
> // src/SomeInterface.php
> interface SomeInterface
> {
> }
> ```

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

> [!WARNING]
> ```php
> return @strlen('...');
> ```

> [!TIP]
> ```php
> return strlen('...');
> ```

### ForbiddenStaticClassConstFetchRule

Avoid static access of constants, as they can change value. Use interface and contract method instead

```yaml
rules:
    - Symplify\PHPStanRules\Rules\ForbiddenStaticClassConstFetchRule
```

> [!WARNING]
> ```php
> class SomeClass
> {
>     public function run()
>     {
>         return static::SOME_CONST;
>     }
> }
> ```

> [!TIP]
> ```php
> class SomeClass
> {
>     public function run()
>     {
>         return self::SOME_CONST;
>     }
> }
> ```

### NoDynamicNameRule

Use explicit names over dynamic ones

```yaml
rules:
    - Symplify\PHPStanRules\Rules\NoDynamicNameRule
```

> [!WARNING]
> ```php
> class SomeClass
> {
>     public function old(): bool
>     {
>         return $this->${variable};
>     }
> }
> ```

> [!TIP]
> ```php
> class SomeClass
> {
>     public function old(): bool
>     {
>         return $this->specificMethodName();
>     }
> }
> ```

### NoEntityOutsideEntityNamespaceRule

Class with #[Entity] attribute must be located in "Entity" namespace to be loaded by Doctrine

```yaml
rules:
    - Symplify\PHPStanRules\Rules\NoEntityOutsideEntityNamespaceRule
```

> [!WARNING]
> ```php
> namespace App\ValueObject;
>
> use Doctrine\ORM\Mapping as ORM;
>
> #[ORM\Entity]
> class Product
> {
> }
> ```

> [!TIP]
> ```php
> namespace App\Entity;
>
> use Doctrine\ORM\Mapping as ORM;
>
> #[ORM\Entity]
> class Product
> {
> }
> ```

### NoGlobalConstRule

Global constants are forbidden. Use enum-like class list instead

```yaml
rules:
    - Symplify\PHPStanRules\Rules\NoGlobalConstRule
```

> [!WARNING]
> ```php
> const SOME_GLOBAL_CONST = 'value';
> ```

> [!TIP]
> ```php
> class SomeClass
> {
>     public function run()
>     {
>         return self::SOME_CONST;
>     }
> }
> ```

### NoReferenceRule

Use explicit return value over magic &reference

```yaml
rules:
    - Symplify\PHPStanRules\Rules\NoReferenceRule
```

> [!WARNING]
> ```php
> class SomeClass
> {
>     public function run(&$value)
>     {
>     }
> }
> ```

> [!TIP]
> ```php
> class SomeClass
> {
>     public function run($value)
>     {
>         return $value;
>     }
> }
> ```

### NoReturnSetterMethodRule

Setter method cannot return anything, only set value

```yaml
rules:
    - Symplify\PHPStanRules\Rules\NoReturnSetterMethodRule
```

> [!WARNING]
> ```php
> final class SomeClass
> {
>     private $name;
>
>     public function setName(string $name): int
>     {
>         return 1000;
>     }
> }
> ```

> [!TIP]
> ```php
> final class SomeClass
> {
>     private $name;
>
>     public function setName(string $name): void
>     {
>         $this->name = $name;
>     }
> }
> ```

### NoTestMocksRule

Mocking "%s" class is forbidden. Use direct/anonymous class instead for better static analysis

```yaml
rules:
    - Symplify\PHPStanRules\Rules\PHPUnit\NoTestMocksRule
```

> [!WARNING]
> ```php
> use PHPUnit\Framework\TestCase;
>
> final class SkipApiMock extends TestCase
> {
>     public function test()
>     {
>         $someTypeMock = $this->createMock(SomeType::class);
>     }
> }
> ```

> [!TIP]
> ```php
> use PHPUnit\Framework\TestCase;
>
> final class SkipApiMock extends TestCase
> {
>     public function test()
>     {
>         $someTypeMock = new class() implements SomeType {};
>     }
> }
> ```

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

> [!WARNING]
> ```php
> class SomeClass
> {
>     public function run()
>     {
>         return new SplFileInfo('...');
>     }
> }
> ```

> [!TIP]
> ```php
> class SomeClass
> {
>     public function run()
>     {
>         return new CustomFileInfo('...');
>     }
> }
> ```

### PreventParentMethodVisibilityOverrideRule

Change `"%s()"` method visibility to "%s" to respect parent method visibility.

```yaml
rules:
    - Symplify\PHPStanRules\Rules\PreventParentMethodVisibilityOverrideRule
```

> [!WARNING]
> ```php
> class SomeParentClass
> {
>     public function run()
>     {
>     }
> }
>
> class SomeClass extends SomeParentClass
> {
>     protected function run()
>     {
>     }
> }
> ```

> [!TIP]
> ```php
> class SomeParentClass
> {
>     public function run()
>     {
>     }
> }
>
> class SomeClass extends SomeParentClass
> {
>     public function run()
>     {
>     }
> }
> ```

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

> [!WARNING]
> ```php
> use Symfony\Component\Routing\Annotation\Route;
>
> class SomeController
> {
>     #[Route("/path")]
>     public function someAction()
>     {
>     }
> }
> ```

> [!TIP]
> ```php
> use Symfony\Component\Routing\Annotation\Route;
>
> class SomeController
> {
>     #[Route(path: "/path")]
>     public function someAction()
>     {
>     }
> }
> ```

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

> [!WARNING]
> ```php
> // app/Entity/SomeAttribute.php
> namespace App\Controller;
>
> #[\Attribute]
> final class SomeAttribute
> {
> }
> ```

> [!TIP]
> ```php
> // app/Attribute/SomeAttribute.php
> namespace App\Attribute;
>
> #[\Attribute]
> final class SomeAttribute
> {
> }
> ```

### RequireExceptionNamespaceRule

`Exception` must be located in "Exception" namespace

```yaml
rules:
    - Symplify\PHPStanRules\Rules\Domain\RequireExceptionNamespaceRule
```

> [!WARNING]
> ```php
> // app/Controller/SomeException.php
> namespace App\Controller;
>
> final class SomeException extends Exception
> {
>
> }
> ```

> [!TIP]
> ```php
> // app/Exception/SomeException.php
> namespace App\Exception;
>
> final class SomeException extends Exception
> {
> }
> ```

### RequireUniqueEnumConstantRule

Enum constants "%s" are duplicated. Make them unique instead

```yaml
rules:
    - Symplify\PHPStanRules\Rules\Enum\RequireUniqueEnumConstantRule
```

> [!WARNING]
> ```php
> use MyCLabs\Enum\Enum;
>
> class SomeClass extends Enum
> {
>     private const YES = 'yes';
>
>     private const NO = 'yes';
> }
> ```

> [!TIP]
> ```php
> use MyCLabs\Enum\Enum;
>
> class SomeClass extends Enum
> {
>     private const YES = 'yes';
>
>     private const NO = 'no';
> }
> ```

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

> [!WARNING]
> ```php
> class SomeClass extends Rule
> {
> }
> ```

> [!TIP]
> ```php
> /**
>  * @see SomeClassTest
>  */
> class SomeClass extends Rule
> {
> }
> ```

### UppercaseConstantRule

Constant "%s" must be uppercase

```yaml
rules:
    - Symplify\PHPStanRules\Rules\UppercaseConstantRule
```

> [!WARNING]
> ```php
> final class SomeClass
> {
>     public const some = 'value';
> }
> ```

> [!TIP]
> ```php
> final class SomeClass
> {
>     public const SOME = 'value';
> }
> ```

---

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

> [!WARNING]
> ```php
> class SomeClass
> {
>     public function run(EntityManagerInterface $entityManager)
>     {
>         return $entityManager->getRepository(SomeEntity::class);
>     }
> }
> ```

> [!TIP]
> ```php
> class SomeClass
> {
>     public function __construct(SomeEntityRepository $someEntityRepository)
>     {
>     }
> }
> ```

### NoParentRepositoryRule

Repository should not extend parent repository, as it can lead to tight coupling

```yaml
rules:
    - Symplify\PHPStanRules\Rules\Doctrine\NoParentRepositoryRule
```

> [!WARNING]
> ```php
> use Doctrine\ORM\EntityRepository;
>
> final class SomeRepository extends EntityRepository
> {
> }
> ```

> [!TIP]
> ```php
> final class SomeRepository
> {
>     public function __construct(EntityManagerInterface $entityManager)
>     {
>         $this->repository = $entityManager->getRepository(SomeEntity::class);
>     }
> }
> ```

### NoGetRepositoryOnServiceRepositoryEntityRule

Instead of calling "->getRepository(...::class)" service locator, inject service repository via constructor and use it directly

```yaml
rules:
    - Symplify\PHPStanRules\Rules\Doctrine\NoGetRepositoryOnServiceRepositoryEntityRule
```

> [!WARNING]
> ```php
> use Doctrine\ORM\Mapping as ORM;
>
> /**
>  * @ORM\Entity(repositoryClass=SomeRepository::class)
>  */
> class SomeEntity
> {
> }
> ```

> [!WARNING]
> ```php
> use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
>
> final class SomeEntityRepository extends ServiceEntityRepository
> {
> }
> ```

> [!WARNING]
> ```php
> use Doctrine\ORM\EntityManagerInterface;
>
> final class SomeService
> {
>     public function run(EntityManagerInterface $entityManager)
>     {
>         return $this->entityManager->getRepository(SomeEntity::class);
>     }
> }
> ```

> [!TIP]
> ```php
> use Doctrine\ORM\EntityManagerInterface;
>
> final class SomeService
> {
>     public function __construct(private SomeEntityRepository $someEntityRepository)
>     {
>     }
> }
> ```

### NoRepositoryCallInDataFixtureRule

Repository should not be called in data fixtures, as it can lead to tight coupling

```yaml
rules:
    - Symplify\PHPStanRules\Rules\Doctrine\NoRepositoryCallInDataFixtureRule
```

> [!WARNING]
> ```php
> use Doctrine\Common\DataFixtures\AbstractFixture;
>
> final class SomeFixture extends AbstractFixture
> {
>     public function load(ObjectManager $objectManager)
>     {
>         $someRepository = $objectManager->getRepository(SomeEntity::class);
>         $someEntity = $someRepository->get(1);
>     }
> }
> ```

> [!TIP]
> ```php
> use Doctrine\Common\DataFixtures\AbstractFixture;
>
> final class SomeFixture extends AbstractFixture
> {
>     public function load(ObjectManager $objectManager)
>     {
>         $someEntity = $this->getReference('some-entity-1');
>     }
> }
> ```

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

> [!WARNING]
> ```php
> abstract class AbstractController extends Controller
> {
>     public function __construct(
>         private SomeService $someService
>     ) {
>     }
> }
> ```

> [!TIP]
> ```php
> abstract class AbstractController extends Controller
> {
>     private $someService;
>
>     #[Required]
>     public function autowireAbstractController(SomeService $someService)
>     {
>         $this->someService = $someService;
>     }
> }
> ```

### ServicesExcludedDirectoryMustExistRule

Services excluded path must exist. If not, remove it

```yaml
rules:
    - Symplify\PHPStanRules\Rules\Symfony\ConfigClosure\ServicesExcludedDirectoryMustExistRule
```

> [!WARNING]
> ```php
> use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
>
> return static function (ContainerConfigurator $configurator): void {
>     $services = $configurator->serivces();
>
>     $services->load('App\\', __DIR__ . '/../src')
>         ->exclude([__DIR__ . '/this-path-does-not-exist']);
> };
> ```

> [!TIP]
> ```php
> use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
>
> return static function (ContainerConfigurator $configurator): void {
>     $services = $configurator->services();
>
>     $services->load('App\\', __DIR__ . '/../src')
>         ->exclude([__DIR__ . '/../src/ValueObject']);
> };
> ```

### NoBundleResourceConfigRule

Avoid using configs in `*Bundle/Resources` directory. Move them to `/config` directory instead

```yaml
rules:
    - Symplify\PHPStanRules\Rules\Symfony\ConfigClosure\NoBundleResourceConfigRule
```

<br>

### NoRoutingPrefixRule

Avoid global route prefixing. Use single place for paths in @Route/#[Route] and improve static analysis instead.

```yaml
rules:
    - Symplify\PHPStanRules\Rules\Symfony\NoRoutingPrefixRule
```

> [!WARNING]
> ```php
> use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
>
> return static function (RoutingConfigurator $routingConfigurator): void {
>     $routingConfigurator->import(__DIR__ . '/some-path')
>         ->prefix('/some-prefix');
> };
> ```

> [!TIP]
> ```php
> use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
>
> return static function (RoutingConfigurator $routingConfigurator): void {
>     $routingConfigurator->import(__DIR__ . '/some-path');
> };
> ```

### NoClassLevelRouteRule

Avoid class-level route prefixing. Use method route to keep single source of truth and focus

```yaml
rules:
    - Symplify\PHPStanRules\Rules\Symfony\NoClassLevelRouteRule
```

> [!WARNING]
> ```php
> use Symfony\Component\Routing\Attribute\Route;
>
> #[Route('/some-prefix')]
> class SomeController
> {
>     #[Route('/some-action')]
>     public function someAction()
>     {
>     }
> }
> ```

> [!TIP]
> ```php
> use Symfony\Component\Routing\Attribute\Route;
>
> class SomeController
> {
>     #[Route('/some-prefix/some-action')]
>     public function someAction()
>     {
>     }
> }
> ```

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

> [!WARNING]
> ```php
> use Symfony\Component\DependencyInjection\Attribute\Required;
>
> trait SomeTrait
> {
>     #[Required]
>     public function autowireSomeTrait(SomeService $someService)
>     {
>         // ...
>     }
> }
> ```

> [!TIP]
> ```php
> abstract class SomeClass
> {
>     #[Required]
>     public function autowireSomeClass(SomeService $someService)
>     {
>         // ...
>     }
> }
> ```

### SingleArgEventDispatchRule

The event dispatch() method can have only 1 arg - the event object

```yaml
rules:
    - Symplify\PHPStanRules\Rules\Symfony\SingleArgEventDispatchRule
```

> [!WARNING]
> ```php
> use Symfony\Component\EventDispatcher\EventDispatcherInterface;
>
> final class SomeClass
> {
>     public function __construct(
>         private EventDispatcherInterface $eventDispatcher
>     ) {
>     }
>
>     public function run()
>     {
>         $this->eventDispatcher->dispatch('event', 'another-arg');
>     }
> }
> ```

> [!TIP]
> ```php
> use Symfony\Component\EventDispatcher\EventDispatcherInterface;
>
> final class SomeClass
> {
>     public function __construct(
>         private EventDispatcherInterface $eventDispatcher
>     ) {
>     }
>
>     public function run()
>     {
>         $this->eventDispatcher->dispatch(new EventObject());
>     }
> }
> ```

### NoListenerWithoutContractRule

There should be no listeners modified in config. Use EventSubscriberInterface contract or #[AsEventListener] attribute and PHP instead

```yaml
rules:
    - Symplify\PHPStanRules\Rules\Symfony\NoListenerWithoutContractRule
```

> [!WARNING]
> ```php
> class SomeListener
> {
>     public function onEvent()
>     {
>     }
> }
> ```

> [!TIP]
> ```php
> use Symfony\Component\EventDispatcher\EventSubscriberInterface;
>
> class SomeListener implements EventSubscriberInterface
> {
>     public static function getSubscribedEvents(): array
>     {
>         return [
>             'event' => 'onEvent',
>         ];
>     }
>
>     public function onEvent()
>     {
>     }
> }
> ```

> [!TIP]
> ```php
> use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
>
> #[AsEventListener]
> class SomeListener
> {
>     public function __invoke()
>     {
>     }
> }
> ```


### NoDoctrineListenerWithoutContractRule

There should be no Doctrine listeners modified in config. Implement  "Document\Event\EventSubscriber" to provide events in the class itself

```yaml
rules:
    - Symplify\PHPStanRules\Rules\Doctrine\NoDoctrineListenerWithoutContractRule
```

> [!WARNING]
> ```php
> class SomeListener
> {
>     public function onFlush()
>     {
>     }
> }
> ```

> [!TIP]
> ```php
> use Doctrine\Common\EventSubscriber;
> use Doctrine\ODM\MongoDB\Events;
>
> class SomeListener implements EventSubscriber
> {
>     public function onFlush()
>     {
>     }
>
>     public static function getSubscribedEvents(): array
>     {
>         return [
>             Events::onFlush
>         ];
>     }
> }
> ```

### NoStringInGetSubscribedEventsRule

Symfony getSubscribedEvents() method must contain only event class references, no strings

```yaml
rules:
    - Symplify\PHPStanRules\Rules\Symfony\NoStringInGetSubscribedEventsRule
```

> [!WARNING]
> ```php
> use Symfony\Component\EventDispatcher\EventSubscriberInterface;
>
> class SomeListener implements EventSubscriberInterface
> {
>     public static function getSubscribedEvents(): array
>     {
>         return [
>             'event' => 'onEvent',
>         ];
>     }
>
>     public function onEvent()
>     {
>     }
> }
> ```

> [!TIP]
> ```php
> use Symfony\Component\EventDispatcher\EventSubscriberInterface;
>
> class SomeListener implements EventSubscriberInterface
> {
>     public static function getSubscribedEvents(): array
>     {
>         return [
>             Event::class => 'onEvent',
>         ];
>     }
>
>     public function onEvent()
>     {
>     }
> }
> ```

### RequireInvokableControllerRule

Use invokable controller with __invoke() method instead of named action method

```yaml
rules:
    - Symplify\PHPStanRules\Rules\Symfony\RequireInvokableControllerRule
```

> [!WARNING]
> ```php
> use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
> use Symfony\Component\Routing\Annotation\Route;
>
> final class SomeController extends AbstractController
> {
>     #[Route()]
>     public function someMethod()
>     {
>     }
> }
> ```

> [!TIP]
> ```php
> use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
>
> final class SomeController extends AbstractController
> {
>     #[Route()]
>     public function __invoke()
>     {
>     }
> }
> ```

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
    - Symplify\PHPStanRules\Rules\Doctrine\NoEntityMockingRule
    - Symplify\PHPStanRules\Rules\Doctrine\NoDocumentMockingRule
```

> [!WARNING]
> ```php
> use PHPUnit\Framework\TestCase;
>
> final class SomeTest extends TestCase
> {
>     public function test()
>     {
>         $someEntityMock = $this->createMock(SomeEntity::class);
>     }
> }
> ```

> [!TIP]
> ```php
> use PHPUnit\Framework\TestCase;
>
> final class SomeTest extends TestCase
> {
>     public function test()
>     {
>         $someEntityMock = new SomeEntity();
>     }
> }
> ```

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

> [!WARNING]
> ```php
> use PHPUnit\Framework\MockObject\MockObject;
> use PHPUnit\Framework\TestCase;
>
> class SomeTest extends TestCase
> {
>     private MockObject $firstMock;
>     private MockObject $secondMock;
>
>     public function setUp()
>     {
>         $this->firstMock = $this->createMock(SomeService::class);
>         $this->secondMock = $this->createMock(AnotherService::class);
>     }
> }
> ```

> [!TIP]
> ```php
> use PHPUnit\Framework\MockObject\MockObject;
> use PHPUnit\Framework\TestCase;
>
> class SomeTest extends TestCase
> {
>     private SomeService $someService;
>
>     private FirstMock $firstMock;
>
>     public function setUp()
>     {
>         $this->someService = new SomeService();
>         $this->firstMock = $this->createMock(AnotherService::class);
>     }
> }
> ```

### PublicStaticDataProviderRule

PHPUnit data provider method "%s" must be public

```yaml
rules:
    - Symplify\PHPStanRules\Rules\PHPUnit\PublicStaticDataProviderRule
```

> [!WARNING]
> ```php
> use PHPUnit\Framework\TestCase;
>
> final class SomeTest extends TestCase
> {
>     /**
>      * @dataProvider dataProvider
>      */
>     public function test(): array
>     {
>         return [];
>     }
>
>     protected function dataProvider(): array
>     {
>         return [];
>     }
> }
> ```

> [!TIP]
> ```php
> use PHPUnit\Framework\TestCase;
>
> final class SomeTest extends TestCase
> {
>     /**
>      * @dataProvider dataProvider
>      */
>     public function test(): array
>     {
>         return [];
>     }
>
>     public static function dataProvider(): array
>     {
>         return [];
>     }
> }
> ```

<br>

Happy coding!
