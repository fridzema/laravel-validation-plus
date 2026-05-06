# Contributing

Contributions are welcome. Please follow these guidelines.

## Setup

```bash
git clone https://github.com/fridzema/laravel-validation-plus.git
cd laravel-validation-plus
composer install
```

## Running checks locally

| Command | What it runs |
|---|---|
| `composer test` | Pest test suite (79 tests) |
| `composer test-coverage` | Tests with coverage report |
| `composer mutate` | Mutation tests via pest-plugin-mutate |
| `composer analyse` | PHPStan level 10 + strict-rules |
| `composer format` | Pint code style |

All checks except `mutate` must pass before opening a PR. CI also runs:
- Coverage gate (≥ 100%)
- Mutation testing gate (≥ 75% MSI)
- Security audit (`composer audit --no-dev`)

## Pull requests

- Base branch: `main`
- One logical change per PR
- Add or update tests for any changed behaviour
- Follow [Conventional Commits](https://www.conventionalcommits.org/) for commit messages: `type(scope): description`

Common types: `feat`, `fix`, `refactor`, `test`, `docs`, `chore`

## Reporting issues

Open an issue on GitHub with a minimal reproduction case and the Laravel/PHP versions in use.
