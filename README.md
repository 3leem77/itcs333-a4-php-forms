# ITCS333 — Assignment 4: PHP Forms & Validation (2.5%)

Build a contact/registration form processed by PHP: **server-side validation**, **safe output**, inline error display, and the **Post/Redirect/Get (PRG)** pattern.

## Learning outcomes
CILO 4 — server-side request handling, `$_GET`/`$_POST`, validation, output escaping.

## Layout
```
public/index.php     # the form + processing (your main page)
src/validate.php     # pure validation functions (unit-tested)
```

## Requirements (graded — see rubric)
1. In `src/validate.php` implement pure functions:
   - `validate_required(string $v): bool`
   - `validate_email(string $v): bool`
   - `validate_length(string $v, int $min, int $max): bool`
2. `public/index.php` shows the form on GET; on POST validates **name** (required, 2–60 chars), **email** (valid), **message** (required, 10–1000 chars).
3. Invalid POST → redisplay the form with inline error messages and the user's input repopulated (status 200).
4. Valid POST → **redirect** (302/303) to a success page/state (PRG pattern).
5. All output escaped with `htmlspecialchars` (XSS-safe).
6. No PHP notices/warnings under `error_reporting(E_ALL)`.

## Rubric (100 points = 2.5%)
| # | Check | Points |
|---|---|---|
| 1 | Pure validation functions behave correctly | 25 |
| 2 | GET renders the form without errors | 10 |
| 3 | Invalid POST → 200, inline errors, fields repopulated | 20 |
| 4 | Valid POST → 302/303 redirect (PRG) | 15 |
| 5 | Output escaped (XSS probe rendered inert) | 15 |
| 6 | No PHP notices/warnings | 15 |

## How to work
Fork → enable Actions → clone → edit `public/index.php` + `src/validate.php` → push → submit fork URL in Blackboard (Sunday 23:59, Week 9).

## Run the tests locally
```bash
composer install --working-dir=tests   # first time only (installs PHPUnit)
./run_tests.sh                          # starts php -S, runs unit + HTTP tests
```

## Rules
Individual assignment. Do not modify `tests/` or `.github/`. Official grading uses the instructor's pristine test copy.
