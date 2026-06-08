# CLAUDE.md

Guidance for Claude Code when working in this repository.

## What this is

`symplify/phpstan-rules` — a set of 80+ PHPStan rules (clean architecture, naming,
visibility, and Symfony/Doctrine/PHPUnit proven practices). It is a PHPStan extension
(`"type": "phpstan-extension"` in `composer.json`), PHP `>=8.3`, PHPStan `^2.1`.

## Layout

- `src/Rules/` — the rules, grouped by topic (`Complexity/`, `Doctrine/`, `Symfony/`,
  `PHPUnit/`, `Explicit/`, `Domain/`, `Enum/`, `Convention/`, `Rector/`, …).
- `src/Collector/` — PHPStan collectors (e.g. `NewWithFollowingSettersCollector`).
- `src/Enum/RuleIdentifier.php` (+ `RuleIdentifier/`) — string rule identifiers used by
  every rule, e.g. `symplify.noTestMocks`.
- `config/*.neon` — one set per topic. Sets registered globally via the
  `composer.json` `extra.phpstan.includes` are auto-loaded by
  `phpstan/extension-installer`; the rest are opt-in via `includes:` in the user's
  `phpstan.neon`.
- `tests/Rules/<RuleName>/` — one dir per rule, with `Fixture/` PHP files and a
  `config/configured_rule.neon`.

## Opt-in parameters

Two rule groups are disabled by default and toggled by a single parameter:

- `mocks: true` — enables the PHPUnit mock rules in `config/mock-rules.neon`
  (registered via `conditionalTags` keyed on `%mocks%`).
- `ctor: true` — enables `NewOverSettersRule` + its collector in
  `config/ctor-rules.neon` (passed as `isEnabled: %ctor%` arguments).

Both `config/ctor-rules.neon` and `config/mock-rules.neon` are listed in
`composer.json` `extra.phpstan.includes`, so they load automatically but stay inert
until the parameter is set to `true`.

## Adding a rule

1. Create the rule in the matching `src/Rules/<Topic>/` dir; add its identifier to
   `src/Enum/RuleIdentifier.php`.
2. Register it in the matching `config/<topic>-rules.neon`.
3. Add a test dir under `tests/Rules/<RuleName>/` with `Fixture/` files and a
   `config/configured_rule.neon`. Tests extend PHPStan's `RuleTestCase`, use
   `#[DataProvider('provideData')]`, assert `[ERROR_MESSAGE, $line]` pairs, and wire
   the rule via `getAdditionalConfigFiles()` + `getByType()`. Match the tone of the
   existing tests.
4. Document the rule in `README.md` under the right section.

## Before finishing — always run

```bash
composer fix-cs    # ECS auto-fix
composer rector    # Rector dry-run (drop --dry-run to apply)
composer phpstan    # static analysis
vendor/bin/phpunit  # tests
```

Fix anything they report. Cover new services/rules with a unit test; do not test value
objects or entities.
