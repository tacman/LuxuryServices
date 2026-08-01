# LuxuryServices

LuxuryServices is a Symfony full-stack reference application built around a fictional luxury recruitment agency. The business and data are a demo, while the codebase is intended to illustrate practical patterns for a public frontend, authenticated workflows, and an administration backend.

The project demonstrates how one Symfony application can provide:

- a polished public website for presenting the agency and browsing jobs;
- an authenticated candidate area for profiles and applications;
- an EasyAdmin backend for managing the recruitment workflow.

## What the demo covers

### Public frontend

The Twig frontend integrates a prebuilt luxury recruitment theme and includes:

- a marketing homepage with animated hero content;
- browsable and filterable job offers;
- company and contact pages;
- responsive navigation;
- registration, login, logout, and password-reset screens.

Most of the visual character comes from the integrated theme and its external CSS and JavaScript assets rather than from `assets/styles/app.css`.

### Authentication and candidate workflow

Symfony Security provides password hashing, CSRF protection, remember-me cookies, access control, and disabled-account checks.

Authenticated candidates can maintain their personal and professional profile, track profile completeness, browse positions, apply for jobs, and see which positions they have already applied for.

### Administration backend

The EasyAdmin `/admin` area manages:

- users and candidates;
- customers and job offers;
- applications and contact messages;
- administrative notes and media;
- application statuses, contact statuses, experience levels, genders, job categories, and job types.

The dashboard uses Symfony UX Chart.js to summarize applications, messages, customers, candidates, and job offers.

## Technology

- PHP 8.4+
- Symfony 8.1
- Doctrine ORM 3
- SQLite for disposable development data
- PostgreSQL for deployment
- EasyAdmin 5
- Twig and AssetMapper
- Symfony UX Chart.js
- PHPUnit 13

## Local setup

```bash
git clone https://github.com/tacman/LuxuryServices.git
cd LuxuryServices
composer install
echo 'DATABASE_URL="sqlite:///%kernel.project_dir%/var/data.db"' > .env.local
php bin/console doctrine:schema:update --force
php bin/console doctrine:fixtures:load --no-interaction
symfony serve
```

SQLite is used only for local demonstration data. Update it directly with `doctrine:schema:update --force`; no migration is needed for this disposable database.

## PostgreSQL deployment

A deployed instance should use PostgreSQL. Set `DATABASE_URL` in the deployment environment to the PostgreSQL DSN, then generate and review a Doctrine migration against PostgreSQL before deploying it. Do not use `doctrine:schema:update --force` against a shared or production database.

The historical migrations in this repository describe the original application database and are not the deployment path for the modernized schema.

## Demo accounts

All fixture accounts use the password `password`.

| Role | Account name |
| --- | --- |
| Administrator | `admin` |
| Candidate | `candidate` |
| Candidate | `marcus` |

After signing in as the administrator, open `/admin`.

## Fixture data

`AppFixtures` creates a small but connected recruitment dataset:

- 3 users and candidate profiles;
- 2 customer companies;
- 6 active job offers across several categories;
- 4 job applications in different states;
- 2 contact messages;
- the lookup values required by frontend forms and dashboard charts.

Reload the demo at any time:

```bash
php bin/console doctrine:fixtures:load --no-interaction
```

## External integrations

Email delivery defaults to `null://null`, so messages are discarded locally. The legacy profile uploader currently uses the FreeImage API when `FREEIMG_API_KEY` is configured in `.env.local`. Local, configurable storage and graceful upload failures are tracked in [issue #1](https://github.com/tacman/LuxuryServices/issues/1).

The visual theme loads several CSS and JavaScript files from `assets-luxury.projets.garage404.com`. If that host becomes unavailable, the application logic will continue to run, but the frontend will lose much of its styling and interactive behavior.

## Purpose and scope

This repository is a best-practices guide and working reference, especially for projects that use EasyAdmin:

- the public and administrative experiences live in one conventional Symfony application;
- authentication and role boundaries are visible through the development accounts;
- fixtures provide repeatable, connected demo data;
- SQLite keeps local setup disposable, while PostgreSQL is the deployment target;
- the business identity and fixture data remain fictional.

The current backend uses EasyAdmin 5. Its boundaries also make the project useful for evaluating a future Tabler-based administration UI without changing the public frontend or domain model. The legacy visual theme is intentionally preserved for now, although it still depends on older frontend libraries and externally hosted assets.
