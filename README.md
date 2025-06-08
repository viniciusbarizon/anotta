# Anotta

Online medical records system for doctors and clinics.

## Tech Stack

- PHP 8.4
- Laravel 12
- PostgreSQL 17
- Laravel Herd (local development environment)
- DBngin (local database manager)

## Running Locally with Herd and DBngin

1. Use the default credentials to log in via DBeaver (or another database client):  
**user:** `postgres`  
**password:** *(leave empty)*

2. Create the `anotta` database and the `anotta` user in **DBeaver**:
```sql
CREATE USER anotta WITH PASSWORD 'password-here';
CREATE DATABASE anotta OWNER anotta;
ALTER USER anotta CREATEDB;
```
3. Locate the `pg_hba.conf` file (right-click on your database instance in DBngin and select **“Show Database Files”**).

4. Make sure the authentication method in that file is set to `md5` for the line: host all all 127.0.0.1/32.

5. Run the commands inside the Herd folder:

```bash
git clone git@github.com:viniciusbarizon/anotta.git
cd anotta
composer install
cp .env.example .env
php artisan key:generate
npm install
npm run dev
```

6. Set your database password in the `.env` file.

7. Run the migrations:

```bash
php artisan migrate
```

8. To capture outgoing emails during local development, install and run [Mailpit](https://github.com/axllent/mailpit) (MacOS example)

```bash
brew install mailpit
mailpit
```

Once running, Mailpit will be accessible at: [http://localhost:8025/](http://localhost:8025/)
