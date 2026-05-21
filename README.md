# 🛠️ Daftar Library & Dependensi - TaskManager

Dokumentasi ini mencantumkan seluruh framework, library, dan dependensi yang digunakan di dalam proyek **TaskManager** (baik di sisi Backend, Frontend Build Tooling, maupun Client-Side CDN di halaman tertentu).

---

## 🖥️ 1. Sisi Backend (PHP & Laravel - `composer.json`)
Sisi backend dibangun menggunakan **Laravel 13** dan **PHP 8.3**. Berikut adalah dependensi composer yang digunakan:

### Core Dependensi (Production)
*   **PHP (`^8.3`)** - Runtime utama backend.
*   **Laravel Framework (`^13.8`)** - Framework utama untuk routing, Eloquent ORM, Blade templating, dll.
*   **Laravel Tinker (`^3.0`)** - Shell interaktif untuk berinteraksi langsung dengan database & objek Laravel melalui command line.

### Dependensi Pengembangan (Development / `require-dev`)
*   **PHPUnit (`^12.5.12`)** - Framework pengujian (testing) unit & feature.
*   **FakerPHP/Faker (`^1.23`)** - Library untuk generate data dummy (palsu) saat seeding database atau testing.
*   **Laravel Pint (`^1.27`)** - Alat pemformat kode PHP (PHP Code Style Fixer) agar konsisten sesuai standar PSR-12/Laravel.
*   **Laravel Pail (`^1.2.5`)** - Utilitas pembaca log Laravel secara real-time langsung melalui terminal.
*   **Laravel Pao (`^1.0.6`)** - Package tambahan pendukung Laravel.
*   **Mockery (`^1.6`)** - PHP mock object framework untuk mempermudah unit testing.
*   **Collision (`^8.6`)** - Tampilan visual error handler yang indah saat terjadi kesalahan di console/terminal.

---

## 🎨 2. Sisi Frontend & Build Tooling (NPM - `package.json`)
Build tooling dan styling diatur menggunakan **Vite** dan **Tailwind CSS v4**:

*   **Vite (`^8.0.0`)** - Frontend build tool berkecepatan tinggi sebagai pengganti Laravel Mix.
*   **Tailwind CSS (`^4.0.0`)** - Framework CSS modern utility-first untuk desain UI responsif dan elegan.
*   **@tailwindcss/vite (`^4.0.0`)** - Integrasi resmi compiler Tailwind CSS v4 dengan build engine Vite.
*   **Laravel Vite Plugin (`^3.1`)** - Plugin penghubung aset Vite ke layout Blade Laravel.
*   **Concurrently (`^9.0.1`)** - Utilitas untuk menjalankan beberapa proses command line sekaligus (seperti `php artisan serve`, queue listener, dan `npm run dev`) dalam satu tab terminal.

---

## 🌐 3. Sisi Client-Side (CDN Libraries di Blade View)
Untuk mendukung beberapa fitur tools instan tanpa overhead database/backend round-trip, beberapa halaman memuat library JavaScript langsung menggunakan CDN:

### 📝 Code Prettifier (`resources/views/prettifier/index.blade.php`)
*   **Prettier Standalone v3** (`prettier@3/standalone.js`) - Library formatter kode utama untuk memformat Javascript, Typescript, HTML, dan CSS di browser.
    *   *Plugins*: Babel (Parser JS), HTML Parser, PostCSS (Parser CSS), dan TypeScript Parser.
*   **sql-formatter v15** (`sql-formatter@15`) - Formatter khusus untuk merapikan query SQL secara instan.
*   *Native/Custom Implementations*:
    *   **JSON**: Menggunakan native `JSON.stringify()`.
    *   **XML & PHP**: Menggunakan regex custom & indentasi manual bawaan javascript di halaman tersebut.

### 🔍 Diff Checker (`resources/views/diff-checker/index.blade.php`)
*   **jsdiff v5.1.0** (`jsdiff/5.1.0/diff.min.js`) - Library perbandingan teks baris-per-baris (line diffing) bergaya GitHub untuk melihat perbedaan sebelum (*Before*) dan sesudah (*After*).

### 🖋️ Tipografi & Fonts (Global)
*   **Google Fonts** (Outfit & Plus Jakarta Sans) - Dimuat via link CDN di header layout global (`app.blade.php`).
