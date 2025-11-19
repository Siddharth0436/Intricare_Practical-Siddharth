# Intricare Practical — Siddharth

A simple Laravel-based sample application used for the Intricare practical exercise.

This repository contains the application code, database migrations and seeders needed to run the app locally.

## Prerequisites

- PHP 8.x
- Composer
- A MySQL (or compatible) database
- Node.js + npm (optional — only required if changing frontend assets)

## Quick Setup

1. Install PHP dependencies:

```powershell
composer install
```

2. Create the environment file and generate an app key:

```powershell
# PowerShell (Windows)
Copy-Item -Path .env.example -Destination .env
php artisan key:generate

# Or on Unix-like shells:
# cp .env.example .env && php artisan key:generate
```

3. Configure your database in the `.env` file (update `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`).

4. Create an empty database on your local server (for example `intricare_practical`).

5. Run migrations and seeders:

```powershell
php artisan migrate
php artisan db:seed

# Or run both in one command:
php artisan migrate --seed
```

6. Create the storage symlink (if required):

```powershell
php artisan storage:link
```

## Run the Application

Start the local development server:

```powershell
php artisan serve
```

Open your browser to: `http://127.0.0.1:8000/contacts`

## Project Structure (important files)

- `app/Http/Controllers/ContactController.php` — Controller for contact views and actions
- `app/Models/Contact.php` — Contact model
- `app/Models/CustomField.php` — Custom field model
- `app/Models/ContactMergeLog.php` — Merge log model
- `resources/views/contacts/` — Blade views for contact listing and details
- `database/migrations/` — Database migration files
- `database/seeders/` — Seeder classes (example data)

## Testing

Run the test suite with:

```powershell
php artisan test
```

## Notes

- The project expects a local database and correct `.env` configuration before running migrations.
- If you are on Windows and using Git Bash or WSL, Unix-like commands such as `cp` may work there; otherwise use the PowerShell `Copy-Item` command shown above.

## Contributing

If you want to improve the project README or code:

- Fork the repository
- Create a feature branch
- Open a pull request with a clear description of changes

## License

This repository does not include a license file. Add a `LICENSE` if you wish to make the project's license explicit.

---

If you want, I can also add a short troubleshooting section or a small development checklist — tell me which you'd prefer.
