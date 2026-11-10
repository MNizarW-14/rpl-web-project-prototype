# AGENTS.md

Campus news site ("Jurnalis Nuansa"): plain PHP 8 + MySQL pages in the repo root, shared
`header.php`/`footer.php` partials, Tailwind loaded from a CDN. **No framework, no Composer,
no npm, no build step, no tests.** Do not bootstrap a toolchain for small changes.

## Run & verify

- The site only works through XAMPP at `C:\MyXAMPP` (not `C:\xampp`). Start Apache + MySQL
  (`xampp-control.exe`, or `apache_start.bat` / `mysql_start.bat`), then open
  `http://localhost/RPLProject/index.php`.
- PHP CLI: `C:\MyXAMPP\php\php.exe` (8.0.30). The only useful local check is syntax:
  `C:\MyXAMPP\php\php.exe -l index.php`. There is no linter, formatter, typecheck, or test runner.
- Opening the `.php` files directly or serving them with `php -S` will fail: includes and DB are
  path/URL-relative and `koneksi.php` assumes the XAMPP MySQL server.

## Database

- `koneksi.php` hardcodes `mysqli` to `localhost` / `root` / empty password / db
  `jurnalis_kampus`. There is no `.env` or config indirection; keep it that way unless asked.
- **No schema dump exists in the repo.** Columns implied by the queries:
  - `articles`: `id`, `category_id`, `title`, `excerpt`, `content`, `image_url`, `is_featured`, `published_at`
  - `categories`: `id`, `name`, `color_class`
  If you change the schema, add a `.sql` dump (e.g. `database.sql`) — re-creating the DB is otherwise impossible.
- MySQL being stopped makes every page `die()` with "Koneksi database gagal".

## Page conventions

- Every page follows: `require 'koneksi.php';` → queries → `include 'header.php';` → body →
  `include 'footer.php';`. `header.php` opens `<!DOCTYPE>/<html>/<body>` and `footer.php` closes
  them, so pages must not repeat those tags. Existing pages use `include`, not `require_once`.
- No router: URLs are direct filenames (`index.php`, `artikel.php?id=N`). New pages are top-level
  `.php` files linked relatively.
- Read `$_GET['id']` through `intval()` as `artikel.php` does. Titles/excerpts/content are echoed
  **unescaped**, and `content` is expected to be raw HTML — sanitize on write, not on read.
- Styling: `header.php` loads `https://cdn.tailwindcss.com` plus Google Fonts; `kirim-tulisan.html`
  additionally loads `assets/css/style.css`. There is no `tailwind.config` or compiled CSS — use
  Tailwind arbitrary-value classes with the site palette, don't add a stylesheet.
- Palette (reuse exact values): `#B01050` accent, `#FFF0F5` page background, `#FDD4E6` borders/soft
  accent, black/white. `categories.color_class` is a Tailwind text-color class from the DB.
- All UI copy and code comments are Indonesian; keep new copy/comments Indonesian.

## Known gaps — don't mistake for working features

- `kirim-tulisan.html` is a static mockup: it links to `index.html` (does not exist), the form has
  `action="#"`, accepts a file upload with no handler, and bypasses `header.php`. There is no
  submission backend.
- `index.php` sidebar "Terpopuler" and the top-bar date in `header.php` are hardcoded.
- `assets/js/main.js` is empty; there is no JS behavior anywhere.
- No `.gitignore`, no CI. Repo is a single `main` branch with one initial commit.
