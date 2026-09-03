# PioSign

PioSign is a production application for managing company-wide Gmail signatures. It connects to Google Workspace so administrators can maintain employee details and apply a consistent, professional signature across the organisation.

The company previously relied on a ready-to-use external solution to manage employee signatures. PioSign replaces that dependency with an internally owned solution tailored to the company's workflow, giving the company full control over signature data, templates and synchronisation.

An administrator can synchronise the Workspace directory, edit an employee's signature details, and push the rendered signature to Gmail on the employee's behalf. This keeps signatures consistent while still allowing employee-specific information such as names, roles, contact details, and other approved content.

![](/public/images/screenshots/edit_employee.png)

As a CRM system, PioSign also provides visibility into signature synchronisation. Administrators can see the current sync status for each employee and when their signature was last synchronised, making it easier to identify and resolve signatures that need attention.

![](/public/images/screenshots/Employees_table.png)

This is the expected result: the employee’s signature is successfully attached to their Gmail account.

![The expected result](/public/images/screenshots/expected_result.png)

## Features

- Import and maintain employees from a Google Workspace directory.
- Manage employee signature details from an administrative panel.
- Render signatures as HTML using the application's signature template.
- Synchronise an individual employee's signature to Gmail.
- Dispatch a bulk signature synchronisation for all active employees.
- Track each employee's signature sync status and last synchronisation time.
- Soft-delete Workspace users who are no longer returned by the directory.

## Technology

- PHP 8.3+
- Laravel 13
- Filament 5
- MySQL (default production database)
- Google API Client for Workspace Directory and Gmail integration
- Vite and Tailwind CSS for frontend assets

## Requirements

- PHP 8.3 or newer with the extensions required by the application.
- Composer.
- Node.js and npm.
- MySQL or another database supported by Laravel.
- A Google Workspace domain and a Google Cloud service account configured for domain-wide delegation.

## Installation

Clone the repository and install the application dependencies:

```bash
git clone <repository-url> piosign
cd piosign
composer install
npm install
```

Create the environment file and application key:

```bash
cp .env.example .env
php artisan key:generate
```

Configure the database and Google Workspace values in `.env`, then run the migrations:

```bash
php artisan migrate
```

Build the frontend assets:

```bash
npm run build
```

The complete setup sequence is also available through Composer:

```bash
composer run setup
```

## Google Workspace setup

PioSign requires server-to-server access to Google Workspace. Before running a production instance:

1. Create or select a project in [Google Cloud](https://console.cloud.google.com/).
2. Enable the Admin SDK API and Gmail API.
3. Create a service account and download its JSON credentials securely.
4. Enable domain-wide delegation for the service account.
5. In the Google Workspace Admin console, grant the service account the scopes required by the directory and Gmail operations.
6. Set the Google configuration values in `.env`:

```dotenv
GOOGLE_APPLICATION_CREDENTIALS=/secure/path/google-service-account.json
GOOGLE_WORKSPACE_ADMIN_EMAIL=admin@example.com
GOOGLE_WORKSPACE_DOMAIN=example.com
```

The credentials file should be stored outside the public web root, readable only by the application user and excluded from version control. Never commit service-account credentials or real production environment files.

## Configuration

The important application settings are defined in `.env.example`:

| Variable                         | Purpose                                               |
| -------------------------------- | ----------------------------------------------------- |
| `APP_URL`                        | Public URL of the application                         |
| `DB_*`                           | Database connection settings                          |
| `QUEUE_CONNECTION`               | Queue backend for Workspace and Gmail sync jobs       |
| `GOOGLE_APPLICATION_CREDENTIALS` | Absolute path to the service-account JSON file        |
| `GOOGLE_WORKSPACE_ADMIN_EMAIL`   | Workspace administrator used for delegated API access |
| `GOOGLE_WORKSPACE_DOMAIN`        | Workspace domain whose users are managed              |

For production, use a durable queue backend such as Redis rather than the synchronous `sync` queue driver. The queue worker must be running for directory and signature jobs to complete asynchronously.

## Local development

Start the application, queue worker, log viewer, and Vite development server together:

```bash
composer run dev
```

Alternatively, run the services separately:

```bash
php artisan serve
php artisan queue:listen --tries=1 --timeout=0
npm run dev
```

After the application starts, open the URL configured by `APP_URL` and sign in through the Filament administration panel.

## Testing

Run the test suite with:

```bash
composer run test
```

To run a specific test or use additional PHPUnit options:

```bash
php artisan test --filter=TestName
```

## Production deployment

At minimum, a production deployment should:

- Set `APP_ENV=production` and `APP_DEBUG=false`.
- Use a secure, durable database and queue backend.
- Run `php artisan migrate --force` during deployment.
- Build assets with `npm run build`.
- Run a continuously supervised queue worker, for example `php artisan queue:work`.
- Configure the scheduler if future recurring Workspace or signature syncs are added.
- Serve the application from the `public/` directory.
- Protect the Google credentials file and production `.env` file through filesystem permissions and secret management.
- Configure backups, application logging, monitoring, and queue failure alerting.

## License

PioSign is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
