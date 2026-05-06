# Contributing

Contributions are welcome. Please follow these guidelines.

## Setup

```bash
git clone https://github.com/fridzema/laravel-validation-plus.git
cd laravel-validation-plus
composer install
```

## Running checks locally

```bash
composer test        # Pest test suite
composer analyse     # PHPStan level 9
composer format      # Pint code style
```

All three must pass before opening a PR.

## Pull requests

- Base branch: `main`
- One logical change per PR
- Add or update tests for any changed behaviour
- Follow [Conventional Commits](https://www.conventionalcommits.org/) for commit messages: `type(scope): description`

Common types: `feat`, `fix`, `refactor`, `test`, `docs`, `chore`

## Reporting issues

Open an issue on GitHub with a minimal reproduction case and the Laravel/PHP versions in use.
