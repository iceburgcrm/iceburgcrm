<p align="center">
  <a href="https://www.iceburg.ca" target="_blank">
    <img src="https://www.iceburg.ca/images/iceburg.png" width="360" alt="Iceburg CRM">
  </a>
</p>

# Iceburg CRM

Iceburg CRM is a metadata-driven CRM builder for Laravel. It lets you define modules, fields, relationships, subpanels, datalets, permissions, workflows, imports, exports, and external API connectors without hard-coding every CRM shape by hand.

It also includes optional AI-assisted CRM generation: describe the CRM you want, choose a provider such as OpenRouter, OpenAI, or Anthropic, and Iceburg can generate a working starting point.

[Project site](https://www.iceburg.ca) | [Demo](https://demo.iceburg.ca) | [Hosted Iceburg](https://hosted.iceburg.ca)

## Contents

- [Highlights](#highlights)
- [Requirements](#requirements)
- [Quick Start With Docker](#quick-start-with-docker)
- [Local Development Without Docker](#local-development-without-docker)
- [Creating CRMs](#creating-crms)
- [AI Configuration](#ai-configuration)
- [Core Concepts](#core-concepts)
- [API](#api)
- [Troubleshooting](#troubleshooting)

## Highlights

- Metadata-driven modules, fields, relationships, subpanels, datalets, permissions, and seed data.
- Laravel 12 backend with Vue 3, Inertia, Tailwind CSS, DaisyUI, and Heroicons.
- AI CRM builder and AI Assist with configurable chat/image providers.
- OpenRouter, OpenAI, and Anthropic chat provider support.
- OpenRouter and OpenAI image generation support.
- Import and export support for XLSX, CSV, TSV, ODS, XLS, and HTML.
- Module permissions for read, write, import, and export.
- Field-level relationships, multi-module relationships, audit logs, workflow stages, charts, calendars, and connector templates.
- Curated default connector template set with focused starter endpoints.

## Screenshots

<p>
  <a href="https://www.iceburg.ca/images/screenshot1.jpg" target="_blank"><img src="https://www.iceburg.ca/images/screenshot1.jpg" width="90" alt="Iceburg CRM screenshot 1"></a>
  <a href="https://www.iceburg.ca/images/screenshot2.jpg" target="_blank"><img src="https://www.iceburg.ca/images/screenshot2.jpg" width="90" alt="Iceburg CRM screenshot 2"></a>
  <a href="https://www.iceburg.ca/images/screenshot3.jpg" target="_blank"><img src="https://www.iceburg.ca/images/screenshot3.jpg" width="90" alt="Iceburg CRM screenshot 3"></a>
  <a href="https://www.iceburg.ca/images/screenshot4.jpg" target="_blank"><img src="https://www.iceburg.ca/images/screenshot4.jpg" width="90" alt="Iceburg CRM screenshot 4"></a>
  <a href="https://www.iceburg.ca/images/screenshot5.jpg" target="_blank"><img src="https://www.iceburg.ca/images/screenshot5.jpg" width="90" alt="Iceburg CRM screenshot 5"></a>
</p>

## Requirements

Iceburg CRM currently targets Laravel 12.

| Dependency | Version |
| --- | --- |
| PHP | 8.2 or newer |
| Composer | 2.x |
| Node.js | Current LTS recommended |
| npm | Bundled with Node.js |
| Database | MySQL 8.0 recommended |

Laravel 12 supports PHP 8.2 and newer. If you are running Iceburg without Docker, make sure your CLI PHP and web server PHP are both 8.2+.

Required PHP extensions include `bcmath`, `exif`, `gd`, `mbstring`, `pdo_mysql`, and `zip`.

## Quick Start With Docker

Docker is the easiest way to run Iceburg locally because the app and MySQL services are already wired together.

```bash
git clone git@github.com:iceburgcrm/iceburgcrm.git
cd iceburgcrm
cp .env.example .env
```

On Windows PowerShell:

```powershell
Copy-Item .env.example .env
```

Optional: add AI keys before starting Docker if you want AI generation or AI Assist.

```dotenv
AI_PROVIDER=openrouter
AI_CHAT_MODEL=openai/gpt-4o-mini
AI_IMAGE_PROVIDER=openrouter
AI_IMAGE_MODEL=openai/gpt-image-1

OPENROUTER_API_KEY=YOUR_OPENROUTER_KEY
```

Start the stack:

```bash
docker compose up -d --build
```

Create the default CRM:

```bash
docker compose exec app php artisan iceburg:create
```

Open the app:

```text
http://localhost:8080
```

The Docker entrypoint will create `.env` if it is missing, configure the container database connection, install Composer dependencies, wait for MySQL, run package discovery, and clear Laravel config.

## Local Development Without Docker

Use this path when you already have PHP 8.2+, Composer, Node.js, and MySQL installed locally.

```bash
git clone git@github.com:iceburgcrm/iceburgcrm.git
cd iceburgcrm
composer install
npm install
cp .env.example .env
php artisan key:generate
```

You can also start from the Composer package:

```bash
composer create-project iceburgcrm/iceburgcrm iceburgcrm
cd iceburgcrm
```

Configure your local database in `.env`:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=iceburg
DB_USERNAME=root
DB_PASSWORD=
```

Create the database in MySQL, then create a CRM:

```bash
php artisan iceburg:create
```

Build frontend assets:

```bash
npm run dev
```

For active frontend development:

```bash
npm run watch
```

Start the Laravel dev server:

```bash
php artisan serve
```

Open:

```text
http://127.0.0.1:8000
```

For a production-style deploy, point your web server at `public/` and make sure `storage/` and `bootstrap/cache/` are writable by the web server user.

## Login For Local Demo Data

The default seed includes demo users for local development.

| Email | Password |
| --- | --- |
| admin@iceburg.ca | admin |
| user@iceburg.ca | user |
| sales@iceburg.ca | sales |
| accounting@iceburg.ca | accounting |
| marketing@iceburg.ca | marketing |

Change or disable seeded demo accounts before using the app outside a local/demo environment.

## Creating CRMs

The `iceburg:create` command builds the database structure and seed data for different CRM modes.

### Default CRM

Creates the default classic Iceburg CRM.

```bash
php artisan iceburg:create
```

With Docker:

```bash
docker compose exec app php artisan iceburg:create
```

### Core CRM

Creates the core CRM tables and metadata for a blank starting point.

```bash
php artisan iceburg:create --type=core
```

### Custom CRM

Runs your custom module, field, relationship, subpanel, and generation seeders.

```bash
php artisan iceburg:create --type=custom
```

### Admin Panel From Existing Database

Point Iceburg at an existing database and generate a CRM-style admin panel around it.

```bash
php artisan iceburg:create \
  --type=adminpanel \
  --connection_host=127.0.0.1 \
  --connection_port=3306 \
  --connection_database=your_database \
  --connection_username=your_user \
  --connection_password=your_password \
  --connection_charset=utf8mb4 \
  --connection_collation=utf8mb4_unicode_ci
```

### AI Generated CRM

Describe the CRM you want and let the configured provider generate the modules, fields, groups, relationships, and optional logo.

```bash
php artisan iceburg:create --type=ai --prompt="Create a sales CRM"
```

With a generated logo:

```bash
php artisan iceburg:create --type=ai --prompt="Create a sales CRM" --logo=yes
```

With Docker:

```bash
docker compose exec app php artisan iceburg:create --type=ai --prompt="Create a sales CRM" --logo=yes
```

Provider overrides:

```bash
php artisan iceburg:create --type=ai \
  --provider=openrouter \
  --model=openai/gpt-4o-mini \
  --image_provider=openrouter \
  --image_model=openai/gpt-image-1 \
  --prompt="Create a sales CRM" \
  --logo=yes
```

```bash
php artisan iceburg:create --type=ai \
  --provider=anthropic \
  --model=claude-sonnet-4-20250514 \
  --prompt="Create a stamp collecting CRM"
```

```bash
php artisan iceburg:create --type=ai \
  --provider=openai \
  --model=gpt-4o-mini \
  --image_provider=openai \
  --image_model=gpt-image-1 \
  --prompt="Create a real estate CRM" \
  --logo=yes
```

Each AI run can produce a different CRM structure, so treat the output as a strong first draft that you can refine in the admin UI.

## AI Configuration

AI features are optional. Without provider credentials, the core CRM still runs normally.

Default `.env` values:

```dotenv
AI_PROVIDER=openrouter
AI_CHAT_MODEL=openai/gpt-4o-mini
AI_IMAGE_PROVIDER=openrouter
AI_IMAGE_MODEL=openai/gpt-image-1
AI_IMAGE_SIZE=1024x1024
AI_REQUEST_TIMEOUT=60
```

OpenRouter:

```dotenv
OPENROUTER_API_KEY=YOUR_OPENROUTER_KEY
OPENROUTER_CHAT_MODEL=openai/gpt-4o-mini
OPENROUTER_IMAGE_MODEL=openai/gpt-image-1
OPENROUTER_HTTP_REFERER="${APP_URL}"
OPENROUTER_APP_TITLE="${APP_NAME}"
```

OpenAI:

```dotenv
OPENAI_API_KEY=YOUR_OPENAI_KEY
OPENAI_ORGANIZATION=
OPENAI_CHAT_MODEL=gpt-4o-mini
OPENAI_IMAGE_MODEL=gpt-image-1
```

Anthropic:

```dotenv
ANTHROPIC_API_KEY=YOUR_ANTHROPIC_KEY
ANTHROPIC_CHAT_MODEL=claude-sonnet-4-20250514
ANTHROPIC_VERSION=2023-06-01
```

After changing AI settings in Docker, recreate the app container and clear config:

```bash
docker compose up -d --force-recreate app
docker compose exec app php artisan optimize:clear
```

## Themes

The default theme is `iceburgsaas`.

Iceburg ships with custom DaisyUI themes and the standard DaisyUI theme set:

```text
iceburgsaas
iceburgcorporate
iceburgai
light
dark
cupcake
bumblebee
emerald
corporate
synthwave
retro
cyberpunk
valentine
halloween
garden
forest
aqua
lofi
pastel
fantasy
wireframe
black
luxury
dracula
cmyk
autumn
business
acid
lemonade
night
coffee
winter
```

## Field Types

Iceburg fields define how module data is stored, validated, searched, imported, exported, and displayed.

```text
address
audio
checkbox
color
currency
date
email
file
image
number
password
radio
related
tel
text
textarea
url
video
zip
```

## Core Concepts

### Modules

Modules are the primary data objects in a CRM: accounts, contacts, opportunities, projects, assets, tickets, or anything else your CRM needs.

### Fields

Fields belong to modules and control storage type, input type, labels, validation, masking, and relationship behavior.

### Relationships

Iceburg supports field-level relationships and module-level relationships. Module relationships can connect two, three, four, or more modules, which lets you model richer workflows without forcing everything into a pairwise structure.

### Subpanels

Subpanels use relationships to show related records on a module detail page. A module can have multiple subpanels for different relationship paths.

### Datalets

Datalets are dashboard widgets. They can display charts, tables, media, or custom Vue components backed by server-side data methods.

### Workflow

Workflow stages can connect modules into a progression, such as lead to contact to account to quote to opportunity to contract.

### Connectors

Connectors define external API integrations. The default seed includes a smaller curated set of connector templates with starter endpoints so new installs stay useful without being noisy.

## API

Iceburg includes API routes protected by Laravel Sanctum.

The examples below use the local Laravel server on port `8000`. If you are using Docker, replace `localhost:8000` with `localhost:8080`.

Get a token:

```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email": "admin@iceburg.ca", "password": "admin"}'
```

Use the returned token:

```bash
curl http://localhost:8000/api/crm \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

Search CRM data:

```bash
curl -X GET http://localhost:8000/api/crm/search \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{
    "module_id": 1,
    "search_type": "module",
    "text_search_type": "fuzzy",
    "page": 1,
    "per_page": 10
  }'
```

Save a module record:

```bash
curl -X PUT http://localhost:8000/api/crm/9 \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{"9__name": "Example Account"}'
```

Delete module records:

```bash
curl -X DELETE http://localhost:8000/api/crm/2/module \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{"record_ids": [1, 2, 3]}'
```

## Useful Commands

```bash
php artisan optimize:clear
php artisan migrate
php artisan iceburg:create
php artisan iceburg:populate --amount=10 --module_id=9
npm run dev
npm run watch
npm run production
```

Docker equivalents:

```bash
docker compose exec app php artisan optimize:clear
docker compose exec app php artisan migrate
docker compose exec app php artisan iceburg:create
docker compose logs -f app
```

## Troubleshooting

### Service "app" is not running

Start or recreate the Docker stack:

```bash
docker compose up -d --build
```

### Unknown database "iceburg"

Make sure the MySQL container is healthy and the volume was initialized:

```bash
docker compose ps
docker compose logs db
```

If this is a disposable local database, reset the Docker volume and recreate the stack:

```bash
docker compose down -v
docker compose up -d --build
```

### AI key is not visible inside Docker

Compose reads `.env` before creating the container. After adding keys, recreate the app container:

```bash
docker compose up -d --force-recreate app
docker compose exec app php artisan optimize:clear
docker compose exec app printenv OPENROUTER_API_KEY
```

### AI provider returns unauthorized

Check that the correct key is set for the selected provider and that the account has access to the selected model.

### Frontend assets are missing

Build the assets:

```bash
npm install
npm run dev
```

## Example CRM Templates

- [Classic CRM](https://classic.iceburg.ca): accounts, contacts, contracts, line items, opportunities, quotes, and core business CRM modules.
- [Rare Books CRM](https://rarebooks.iceburg.ca): catalog collections, authors, valuations, acquisitions, and lending.
- [Wine Connoisseurs CRM](https://wine.iceburg.ca): cellar management, tasting notes, vintages, recommendations, and tastings.
- [Fitness Studio CRM](https://fitness.iceburg.ca): memberships, class scheduling, member progress, and studio operations.
- [Professional Networking CRM](https://networking.iceburg.ca): events, member engagement, mentorship, and job boards.
- [Crafting Supplies CRM](https://crafting.iceburg.ca): inventory, projects, suppliers, and tutorial tracking.
- [Coffee Enthusiasts CRM](https://coffee.iceburg.ca): beans, roasts, brewing methods, tastings, and equipment.
- [WordPress CRM](https://wordpress.iceburg.ca): CRM-style management around a WordPress database.

## Security

If you discover a security vulnerability in Iceburg CRM, please email [security@iceburg.ca](mailto:security@iceburg.ca).

Do not commit `.env`, API keys, database passwords, bearer tokens, generated secrets, or production credentials.

## License

Iceburg CRM is open-source software licensed under the [GNU AGPL v3](https://www.gnu.org/licenses/agpl-3.0.en.html). See [LICENSE](LICENSE).

## Related Projects

A Python Django version is available at [iceburgcrm/iceburgcrmpython](https://github.com/iceburgcrm/iceburgcrmpython).

## Star History

[![Star History Chart](https://api.star-history.com/svg?repos=iceburgcrm/iceburgcrm&type=Date)](https://star-history.com/#iceburgcrm/iceburgcrm&Date)
