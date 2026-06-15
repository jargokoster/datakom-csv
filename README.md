# Datakom CSV Processor API

A Laravel, API-only application for processing supplier CSV files according to per-supplier
configuration. Each supplier can define how incoming CSV columns are mapped, which columns are
exported, and which transformation rules are applied (and in what order). Processed results are
returned as JSON and (optionally) written to a physical CSV file.

Based on the technical assignment _"CSV töötlemise rakendus (Laravel)"_ (Datakom Estonia OÜ, 2026).

> **No user interface.** This application is fully API-driven — there is no front-end/UI.
> All functionality (managing suppliers, mappings, output columns, rules, and processing CSV files)
> is performed through the REST API. The only browser page is the Swagger documentation at `/docs`,
> which is provided solely to explore and test the API.

## Features

- **Suppliers** — manage suppliers (`/api/suppliers`).
- **Column mappings** — map a supplier's source column to an output column, e.g. `unit_price → price`
  (`/api/column-mappings`). Unmapped columns are kept as-is.
- **Output columns** — configure which columns are exported, and whether each is `active` / `hidden`
  (`/api/output-columns`).
- **Processing rules** — applied in `order`, only when `active` (`/api/processing-rules`):
    - `multiply` — multiply a column by a factor (e.g. `price × 1.24`).
    - `remove` — drop a column.
    - `regexp` — transform values via a regular expression (e.g. strip an SKU prefix).
    - New rule types can be added via `app/Services/Csv/Rules` + `RuleFactory` without changing the
      processing flow.
- **CSV processing** — upload and process a supplier's CSV
  (`POST /api/suppliers/{supplier}/process-csv`). The result is stored in the database (`csv_jobs`)
  and, when `save_file=true`, also written to `storage/app/exports`.

## API documentation

- Interactive docs (Swagger UI): **`/docs`** — public page.
- Raw spec: **`/api/openapi.json`** — public.
- All other endpoints require a bearer token. Open `/docs`, click **Authorize**, and paste the value
  of `OPENAPI_AUTH_TOKEN` (from your `.env`).

Regenerate the spec after changing controllers/schemas:

```bash
php artisan openapi:generate
```

## Adding a new rule

Create a new rule file inside App\Services\Csv\Rules\CsvRule directory and register it in App\Services\Csv\Rules\RuleFactory.php

```php
namespace App\Services\Csv\Rules;

final class RemoveRule implements CsvRule
{
    public function apply(array $row, array $config, string $column): array
    {
		//do some mahic stuff here
        return $row;
    }
}
```

---

## Setup A — Docker (recommended)

### Prerequisites

- Docker
- Docker Compose

### Steps

```bash
cp .env.example .env
# set OPENAPI_AUTH_TOKEN in .env (any secret string)

docker compose up --build
```

The container automatically creates the SQLite database, runs migrations, and generates the OpenAPI
spec on start.

- API base: `http://localhost:8888/api`
- Docs: `http://localhost:8888/docs`

---

## Setup B — Local (`php artisan serve`)

### Prerequisites

- PHP **8.3+** with extensions: `pdo_sqlite`, `zip`
- Composer 2
- SQLite 3

### Steps

```bash
cp .env.example .env
composer install
php artisan key:generate

# SQLite database (the default DB_CONNECTION is sqlite)
touch database/database.sqlite

php artisan migrate
php artisan openapi:generate

# set OPENAPI_AUTH_TOKEN in .env (any secret string)

# served on 8888 to match the documented OpenAPI server URL
php artisan serve --port=8888
```

- API base: `http://localhost:8888/api`
- Docs: `http://localhost:8888/docs`

---

## Tests

Tests were written using Claude Code

```bash
php artisan test
```
