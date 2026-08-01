# Symfony 8 upgrade handoff

## Status

The Symfony 8 upgrade and fixture setup are complete in the current working tree.

The application runs on Symfony 8.1.1 with PHP 8.4+ requirements, Doctrine ORM 3,
EasyAdmin 5, Doctrine Fixtures Bundle 4, and PHPUnit 13.

The changes have not been committed.

## Local development

The local database is SQLite via the gitignored `.env.local`:

```dotenv
DATABASE_URL="sqlite:///%kernel.project_dir%/var/data.db"
```

Per the project convention, update the disposable SQLite schema directly:

```bash
php bin/console doctrine:schema:update --force
php bin/console doctrine:fixtures:load --no-interaction
```

Do not run migrations for local SQLite. A deployment should use PostgreSQL and a
reviewed PostgreSQL migration.

The local server is available on port 8017 through the Symfony proxy:

```bash
curl -sk -x http://127.0.0.1:7080 https://luxury.wip/
```

## Demo accounts

Every fixture account uses the password `password`.

| Account | Role |
| --- | --- |
| `admin` | Administrator |
| `candidate` | Candidate |
| `marcus` | Candidate |

The development login page displays these credentials. Login accepts account
names rather than requiring email-shaped values.

## Fixture data

`src/DataFixtures/AppFixtures.php` creates:

- 3 users and connected candidate profiles;
- 2 customers;
- 6 active job offers;
- 4 applications;
- 2 contact messages;
- all lookup/status records required by forms and dashboard charts.

The fixture status values match the values queried by the EasyAdmin dashboard.

## Main upgrade work

- Upgraded Symfony packages to 8.1 and PHP requirement to 8.4.
- Upgraded Doctrine ORM, Doctrine bundles, EasyAdmin, Symfony UX, and PHPUnit.
- Added Doctrine Fixtures Bundle and connected demo fixtures.
- Migrated routing imports from annotations to attributes.
- Migrated the EasyAdmin dashboard and menu configuration to EasyAdmin 5 APIs.
- Added EasyAdmin generated-route configuration.
- Updated Symfony 8 validator constraints to named arguments.
- Updated the custom user checker signature for Symfony 8.
- Updated the PHPUnit launcher for PHPUnit 13.
- Removed the incompatible Vercel bundle.
- Removed a duplicate legacy AdminNotes CRUD controller and repaired stale imports.
- Added safe local defaults for mail and the legacy image API key.
- Rewrote the README to present the project as a full-stack Symfony reference app.

## Verification completed

The following checks pass:

```text
Symfony: 8.1.1
PHP: 8.5.8
composer install --dry-run: clean
composer validate: valid
composer audit: no advisories
doctrine:schema:validate: mapping and database valid
lint:container: passed
lint:twig: all 14 templates passed
PHP syntax: all src/*.php files passed
git diff --check: passed
```

PHPUnit 13.2.5 starts correctly, but reports `No tests executed!` because the
repository does not currently contain a test suite.

The following routes were verified through the Symfony proxy:

```text
/                       200
/login                  200
/register               200
/contact                200
/reset-password         200
/profile                200 as candidate
/admin                  200 as admin
/admin/user             200 as admin
/admin/job-offer        200 as admin
/admin/candidate        200 as admin
/admin/customer         200 as admin
```

## Known follow-up

The profile uploader still depends directly on `freeimage.host`. Local or remote
network failure can raise an HTTP client transport exception during an upload.
This is intentionally outside the Symfony 8 upgrade and is tracked in:

https://github.com/tacman/LuxuryServices/issues/1

## Suggested next action

Review the current diff, then commit the Symfony 8 upgrade as one coherent
change. Image-storage modernization can be handled separately in issue #1.
