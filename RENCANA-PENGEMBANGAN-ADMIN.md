# Rencana Pengembangan: Admin Panel & Halaman Pesan Anonim

> **Status dokumen:** Perencanaan. Belum ada kode yang diubah/dibuat.
> **Cara pakai:** Dokumen ini dipecah menjadi iterasi kecil (Iterasi 0, 1, 2, ...). Kerjakan satu iterasi, saya tunjukkan hasilnya, kamu review, baru lanjut ke iterasi berikutnya. Jangan lompat iterasi.
> **Bahasa:** Semua UI, pesan, dan komentar kode mengikuti gaya proyek saat ini (Bahasa Indonesia).

---

## 1. Ringkasan Kebutuhan

1. Admin panel dengan CRUD lengkap untuk mengelola konten `index.php`, menu-nya dipisah **per section halaman index** (bukan satu form raksasa).
2. Halaman login untuk admin.
3. Database: **MySQL**, dijalankan via **Laragon** (sudah tersedia di komputer kamu).
4. Setelah admin panel selesai (semua iterasi Fase A di-review dan disetujui) → lanjut Fase B: **halaman pesan publik** tempat teman-teman Della mengirim ucapan **secara anonim**, yang kemudian dimoderasi lewat admin panel sebelum tampil di section "Amplop Doa" pada `index.php`.

---

## 2. Kondisi Proyek Saat Ini (baseline sebelum admin dibangun)

- Situs murni statis: `index.php` (HTML + sedikit gating PHP), `assets/css/style.css` (hasil compile Tailwind, sudah final/tidak ada build step lagi), `assets/js/*.js` (vanilla ES modules, tanpa bundler).
- **Tidak ada database sama sekali.** Semua "data" (foto kenangan, 21 alasan yang sudah dihapus, isi surat cinta, daftar ucapan/wishes) di-hardcode di `assets/js/data.js` dan sebagian disimpan ke `localStorage` browser pengunjung (jadi perubahan dari satu pengunjung tidak terlihat oleh pengunjung lain — termasuk Della sendiri).
- Section yang ada di `index.php` saat ini (setelah beberapa perubahan sebelumnya — "21 Alasan Cinta" sudah dihapus, "Countdown Section" sudah dihapus):
  1. **Release Gate** — halaman kunci sebelum 19 Agustus 2026 (`index.php` bagian atas, PHP-driven, pakai cookie `della_dev_mode` untuk bypass developer).
  2. **Navbar & Splash Screen** — identitas situs (nama, tagline), splash 10 detik dekoratif.
  3. **Hero Section** — badge tanggal, judul, quote.
  4. **Cake Section** — 21 lilin interaktif (JS-only, tidak butuh DB) + ilustrasi kue (teks statis) + tombol "Make a Wish" (client-only, tidak tersimpan permanen).
  5. **Gallery / Memories Section** — grid foto kenangan, sumber data: `data.js` → `INITIAL_MEMORIES`, bisa ditambah manual oleh pengunjung via modal (tersimpan localStorage saja).
  6. **Love Letter (modal)** — isi surat, sumber data: `data.js` → `DEFAULT_LOVE_LETTER`.
  7. **Wishes / "Amplop Doa" Section** — daftar ucapan dari teman, sumber data: `data.js` → `INITIAL_SECRET_WISHES`, bisa ditambah manual via modal (localStorage saja).
  8. **Footer** — teks statis, tombol ulang ke surat cinta/share.
- Semua interaktivitas (confetti, musik synth, animasi) tetap client-side JS, **tidak perlu disentuh** oleh admin panel — admin panel hanya mengelola **konten/data**, bukan animasi/interaksi.

### Perubahan arsitektur yang dibutuhkan

Supaya admin panel benar-benar "mengelola" `index.php`, sumber data harus pindah dari `assets/js/data.js` (statis) ke **MySQL**, dan `index.php` harus membaca data itu **di sisi server (PHP)** lalu me-render HTML-nya — bukan lagi di-render oleh JS di sisi klien dari file `data.js`. Ini artinya `gallery.js`, `wishes.js`, dan sebagian `letter.js` akan disederhanakan (render dilakukan PHP, JS tinggal urus interaksi seperti lightbox/like/filter).

> **Prinsip:** Setiap iterasi yang mengubah sebuah section harus tetap membuat `index.php` bisa tampil normal (tidak ada section yang "kosong" karena migrasi belum selesai). Migrasi dilakukan section-per-section, bukan sekaligus.

---

## 3. Keputusan Teknis (default yang dipakai, bisa kamu koreksi sebelum Iterasi 0 mulai)

| Topik | Keputusan default | Alasan |
|---|---|---|
| Bahasa backend | PHP native (tanpa framework) | Konsisten dengan prinsip "murni HTML/CSS/PHP" yang sudah disepakati sebelumnya |
| Koneksi DB | **PDO** (`PDO::MYSQL`) dengan prepared statements | Lebih aman dari SQL injection dibanding mysqli string-concat, API lebih rapi |
| Autentikasi admin | Session PHP (`$_SESSION`) + password di-hash `password_hash()` (bcrypt) | Standar, tanpa dependency tambahan |
| Jumlah akun admin | 1 akun admin (kamu). Bisa dikembangkan multi-admin nanti kalau perlu | Situs personal, tidak perlu kompleks di awal |
| Proteksi form | CSRF token per form (disimpan di session) | Wajib untuk form admin & form publik (pesan anonim) |
| Upload foto gallery | **Dua opsi didukung**: input URL langsung (seperti sekarang) **atau** upload file ke `assets/uploads/` | Fleksibel — kamu bisa tetap pakai Unsplash URL atau upload foto asli |
| Styling admin panel | UI admin sederhana/fungsional (tabel, form, sidebar) — **tidak** memakai tema romantis pink situs utama | Fokus ke kecepatan build & kemudahan pakai; bisa dipercantik belakangan kalau mau |
| Struktur folder | Admin di folder `/admin`, tidak mengubah routing `index.php` | Supaya situs publik tetap bisa diakses independen dari admin |
| Nama file rencana | 1 file ini saja (`RENCANA-PENGEMBANGAN-ADMIN.md`), tidak dipecah | Sesuai permintaan |

Kalau ada dari tabel di atas yang mau diubah (misal: mau admin panel juga bertema pink romantis, atau mau multi-admin dari awal), sebutkan sebelum Iterasi 0 dimulai — kalau tidak, saya jalan dengan default di atas.

---

## 4. Struktur Folder yang Diusulkan

```
della's-21st-birthday/
├── index.php                     (existing — akan disesuaikan bertahap per iterasi)
├── pesan.php                     (BARU — Fase B, halaman kirim ucapan anonim)
├── assets/
│   ├── css/style.css             (existing, ditambah style khusus admin & halaman pesan jika perlu)
│   ├── js/                       (existing, disederhanakan bertahap)
│   └── uploads/                  (BARU — hasil upload foto gallery dari admin)
├── config/
│   ├── db.php                    (BARU — koneksi PDO, baca kredensial dari config.local.php)
│   └── config.php                (BARU — konstanta situs: release date, dsb, dipindah dari index.php)
├── includes/
│   ├── auth.php                  (BARU — cek login admin, helper session)
│   ├── csrf.php                  (BARU — generate/verify CSRF token)
│   └── helpers.php               (BARU — fungsi bantu umum: sanitize, format tanggal, dst)
├── admin/
│   ├── login.php
│   ├── logout.php
│   ├── index.php                 (dashboard)
│   ├── hero.php                  (kelola Hero Section)
│   ├── gate-settings.php         (kelola tanggal rilis, dev mode default)
│   ├── cake.php                  (kelola teks ilustrasi kue)
│   ├── gallery/
│   │   ├── index.php (list + delete)
│   │   ├── form.php  (create & edit, dibedakan lewat query param id)
│   ├── letter.php                (kelola isi surat cinta)
│   ├── messages/
│   │   ├── index.php (list + moderasi: approve/reject/feature/delete)
│   │   └── view.php  (detail 1 pesan)
│   └── settings.php              (ganti username/password admin)
├── database/
│   ├── schema.sql                (BARU — struktur semua tabel)
│   └── seed.sql                  (BARU — data awal, migrasi dari data.js supaya tidak hilang)
└── RENCANA-PENGEMBANGAN-ADMIN.md (dokumen ini)
```

`config/db.php` akan membaca kredensial dari file **`config/config.local.php`** yang **tidak di-commit** (masuk `.gitignore` baru) — supaya kredensial Laragon-mu tidak ikut ter-share kalau project ini di-push ke GitHub (ingat, project ini sudah terhubung ke repo `bagusbatra/della-s-21st-birthday`).

---

## 5. Skema Database (MySQL)

Nama database yang diusulkan: **`della_birthday`** (dibuat manual dari phpMyAdmin/HeidiSQL bawaan Laragon, atau lewat `schema.sql` di Iterasi 0).

### 5.1 `admins`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | INT PK AI | |
| username | VARCHAR(50) UNIQUE | |
| password_hash | VARCHAR(255) | `password_hash()` bcrypt |
| display_name | VARCHAR(100) | ditampilkan di dashboard |
| created_at | DATETIME | |
| updated_at | DATETIME | |

### 5.2 `settings` (key-value, untuk teks-teks sederhana: Hero, Cake, Gate, Footer)
| Kolom | Tipe | Keterangan |
|---|---|---|
| setting_key | VARCHAR(100) PK | mis. `hero_badge_text`, `hero_title`, `hero_quote`, `cake_banner_text`, `cake_tagline`, `release_timestamp`, `footer_tagline` |
| setting_value | TEXT | |
| updated_at | DATETIME | |

Kenapa key-value: banyak field kecil lintas-section, daripada bikin banyak tabel 1-baris, lebih simpel dikelola generik lewat 1 form per section yang memetakan field ke key tertentu.

### 5.3 `memories` (Gallery)
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | INT PK AI | |
| image_url | VARCHAR(500) | URL eksternal ATAU path ke `assets/uploads/...` |
| caption | VARCHAR(255) | |
| event_date | VARCHAR(50) | teks bebas, mis. "14 Februari" (konsisten dgn gaya saat ini) |
| location | VARCHAR(150) | nullable |
| tag | VARCHAR(100) | untuk filter, mis. "Momen Manis" |
| note | TEXT | nullable |
| likes | INT DEFAULT 0 | |
| sort_order | INT DEFAULT 0 | urutan tampil |
| is_published | TINYINT(1) DEFAULT 1 | admin bisa sembunyikan tanpa hapus |
| created_at / updated_at | DATETIME | |

### 5.4 `love_letter` (single row — id selalu 1)
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | INT PK (selalu 1) | |
| salutation | VARCHAR(255) | |
| paragraphs_json | JSON | array string paragraf |
| closing | VARCHAR(255) | |
| sender | VARCHAR(150) | |
| updated_at | DATETIME | |

### 5.5 `messages` (menyatukan "Secret Wishes" existing + submission publik Fase B)
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | INT PK AI | |
| sender_name | VARCHAR(100) | nullable/kosong kalau anonim |
| is_anonymous | TINYINT(1) DEFAULT 0 | |
| role_relation | VARCHAR(100) | nullable, mis. "Sahabat Kampus" |
| avatar_emoji | VARCHAR(10) | nullable |
| envelope_color | VARCHAR(20) | nullable, mis. "rose", "amber" |
| message | TEXT | isi ucapan |
| hint | VARCHAR(255) | nullable, petunjuk rahasia |
| status | ENUM('pending','approved','rejected') DEFAULT 'pending' | data seed lama & input admin langsung `approved` |
| source | ENUM('seed','admin','public_form') DEFAULT 'admin' | untuk audit asal data |
| likes | INT DEFAULT 0 | |
| ip_address | VARCHAR(45) | nullable, dipakai untuk rate-limit submission publik |
| created_at / updated_at | DATETIME | |

> Catatan: status "sudah dibuka/belum dibuka" amplop di halaman publik itu **state per-pengunjung** (disimpan di `localStorage` browser masing-masing), bukan data global — jadi **tidak masuk** skema DB, tetap ditangani JS seperti sekarang.

### 5.6 `admin_activity_log` (opsional, Iterasi terakhir Fase A kalau kamu mau)
Log sederhana: siapa mengubah apa dan kapan. Ditandai **opsional** — bisa di-skip kalau tidak perlu.

---

## 6. Keamanan yang Diterapkan di Setiap Iterasi (checklist, bukan iterasi terpisah)

Ini bukan iterasi tersendiri — setiap iterasi yang bikin form/DB **wajib** memenuhi poin-poin ini sebelum dianggap selesai:

- [ ] Semua query DB pakai **prepared statement** (PDO, parameter binding) — tidak ada string concat SQL.
- [ ] Password admin di-hash dengan `password_hash()`, dicek dengan `password_verify()`. Tidak pernah simpan plaintext.
- [ ] Semua form (admin & publik) punya **CSRF token** yang divalidasi saat submit.
- [ ] Semua output yang berasal dari input pengguna (caption, pesan, dsb) di-`htmlspecialchars()` saat dicetak ke HTML — mencegah stored XSS (penting karena `messages` diisi orang luar).
- [ ] Upload file (kalau dipakai) divalidasi: whitelist ekstensi (`jpg,jpeg,png,webp`), cek ukuran maksimum, nama file di-generate ulang (bukan pakai nama asli) untuk cegah path traversal/overwrite.
- [ ] Session admin: regenerate session ID saat login, cookie session `HttpOnly`, timeout otomatis setelah idle.
- [ ] Halaman `/admin/*` (kecuali `login.php`) selalu cek status login di awal file (lewat `includes/auth.php`) — redirect ke login kalau belum.
- [ ] Kredensial DB tidak ikut ke git (`config/config.local.php` masuk `.gitignore`).
- [ ] Form pesan publik (Fase B): honeypot field + rate-limit sederhana per IP (mis. maks 3 pesan/jam) untuk kurangi spam.

---

## FASE A — Admin Panel

### Iterasi 0 — Fondasi (DB, koneksi, layout admin, login)
**Tujuan:** admin panel bisa diakses, login berhasil, tapi belum ada CRUD konten.

**Cakupan:**
- Buat `database/schema.sql` (semua tabel di atas) + `database/seed.sql` (migrasi konten dari `data.js` saat ini + 1 akun admin awal).
- Buat `config/db.php`, `config/config.php`, `config/config.local.php.example`.
- Buat `includes/auth.php`, `includes/csrf.php`, `includes/helpers.php`.
- Buat layout admin dasar (`admin/includes/header.php` + sidebar dengan menu: Dashboard, Hero, Gate/Countdown, Cake, Gallery, Love Letter, Wishes & Messages, Settings — sesuai pembagian per-section).
- Buat `admin/login.php` (form + validasi + set session), `admin/logout.php`.
- Buat `admin/index.php` (dashboard kosong: sekadar ucapan selamat datang + ringkasan jumlah data per tabel).

**Di luar cakupan:** belum ada CRUD apa pun, `index.php` publik **belum diubah sama sekali**.

**Kriteria review:**
- Kamu bisa import `schema.sql` + `seed.sql` di Laragon (HeidiSQL/phpMyAdmin) tanpa error.
- Login dengan akun admin awal berhasil, salah password ditolak.
- Sidebar menu semua section index sudah kelihatan (boleh halaman kosong/"coming soon" dulu).
- Situs publik (`index.php`) masih 100% sama seperti sekarang (belum tersentuh).

---

### Iterasi 1 — Kelola Hero Section
**Tujuan:** teks Hero Section (badge tanggal, judul, quote) bisa diedit dari admin dan **langsung berubah** di `index.php`.

**Cakupan:**
- `admin/hero.php`: form edit field-field hero (map ke tabel `settings`).
- `index.php`: bagian `<section id="hero-section">` diubah membaca dari `settings` via PDO, bukan hardcoded lagi.

**Kriteria review:** ubah teks di admin → refresh `index.php` (mode dev-on) → teks berubah sesuai.

---

### Iterasi 2 — Kelola Release Gate & Cake Section (tanggal target + teks ilustrasi kue)
**Tujuan:** tanggal rilis (`DELLA_RELEASE_TIMESTAMP`) dan teks ilustrasi kue bisa diatur dari admin, bukan hardcode di `index.php`/`gate.js`/`cake.js` lagi.

**Cakupan:**
- `admin/gate-settings.php`: form ubah tanggal & jam rilis, toggle "developer mode aktif secara default" (opsional, menggantikan mekanisme cookie manual — **didiskusikan saat iterasi ini**, karena berpotensi mengubah cara kerja `?dev=on` yang sudah ada).
- `admin/cake.php`: form edit teks banner kue ("Della 21st", "Happy Birthday My Love", dst).
- `index.php` & `assets/js/gate.js`: baca tanggal target dari DB (lewat PHP yang menyuntik nilai ke JS, misal `const releaseTarget = "<?php echo $releaseTimestampIso; ?>";`).

**Keputusan yang perlu dikonfirmasi saat iterasi ini:** apakah mekanisme `?dev=on`/`?dev=off` tetap dipertahankan seperti sekarang (cookie), atau digabung ke sistem login admin (misalnya: kalau sedang login sebagai admin, otomatis full-access tanpa perlu cookie terpisah)?

**Kriteria review:** ubah tanggal rilis di admin → gerbang & countdown ikut berubah.

---

### Iterasi 3 — CRUD Gallery / Memories
**Tujuan:** foto kenangan dikelola penuh dari admin (tambah, edit, hapus, urutkan, publish/unpublish), tampil di `index.php` dari DB.

**Cakupan:**
- `admin/gallery/index.php`: tabel list semua foto + tombol edit/hapus/toggle publish.
- `admin/gallery/form.php`: create & edit — input URL foto **atau** upload file ke `assets/uploads/`.
- `index.php`: `<section id="memories-section">` di-render server-side (PHP loop dari tabel `memories`, hanya yang `is_published = 1`), urutan sesuai `sort_order`.
- `assets/js/gallery.js`: disederhanakan — filter tag, lightbox, dan tombol like tetap jalan di client, tapi **sumber data awal** sudah dari HTML yang di-render PHP (bukan `data.js` lagi). Like count: didiskusikan — disimpan balik ke DB via endpoint kecil, atau tetap sekadar UI lokal? (lihat catatan di bawah)

**Keputusan yang perlu dikonfirmasi:** tombol "like ❤️" pada foto — apakah perlu benar-benar tersimpan ke database (butuh 1 endpoint PHP kecil untuk update counter), atau cukup animasi visual saja seperti sekarang (tidak persisten)? Ini menentukan apakah iterasi ini perlu 1 file tambahan `api/like-memory.php`.

**Kriteria review:** tambah/edit/hapus foto dari admin → langsung terlihat perubahannya di `index.php`. Fitur "+ Tambah Foto Kenangan" milik pengunjung publik di `index.php` **direncanakan dihapus** di iterasi ini (karena sekarang dikelola admin) — dikonfirmasi lagi sebelum eksekusi.

---

### Iterasi 4 — Kelola Love Letter
**Tujuan:** isi surat cinta (salam pembuka, paragraf-paragraf, penutup, pengirim) diedit dari admin.

**Cakupan:**
- `admin/letter.php`: form edit salutation/closing/sender + textarea paragraf (dipisah per baris kosong, sama seperti format saat ini).
- `index.php`: modal surat cinta membaca dari tabel `love_letter` (server-side render awal, JS tetap urus buka/tutup modal & animasi).
- Fitur edit-inline surat cinta oleh pengunjung publik (`localStorage`) **direncanakan dihapus**, karena sekarang dikelola admin.

**Kriteria review:** ubah isi surat di admin → modal surat cinta di situs publik menampilkan versi terbaru.

---

### Iterasi 5 — Moderasi Wishes / Messages (persiapan sebelum Fase B)
**Tujuan:** section "Amplop Doa" tampil dari DB (data existing + hasil migrasi seed), admin bisa CRUD penuh (tambah manual, edit, hapus, approve/reject, tandai unggulan).

**Cakupan:**
- `admin/messages/index.php`: tabel list semua pesan dengan filter status (pending/approved/rejected), aksi cepat approve/reject/hapus.
- `admin/messages/view.php` + form tambah manual dari admin (untuk menambahkan ucapan yang misalnya kamu terima lewat WhatsApp, supaya bisa dimasukkan manual).
- `index.php`: `<section id="wishes-section">` di-render server-side dari `messages` dengan `status = 'approved'`.
- Fitur "+ Titip Doa/Ucapan" inline di `index.php` **direncanakan dihapus/diarahkan** ke halaman `pesan.php` (Fase B) — bukan lagi modal lokal.

**Kriteria review:** ucapan lama (hasil migrasi dari `data.js`) tetap tampil seperti semula; admin bisa tambah/ubah/hapus/approve dari panel dan hasilnya langsung terlihat di situs publik.

---

### Iterasi 6 — Polish Admin Panel
**Tujuan:** merapikan pengalaman admin sebelum lanjut ke Fase B.

**Cakupan:**
- Dashboard diisi ringkasan nyata (jumlah foto, jumlah pesan pending, tanggal rilis, dst).
- `admin/settings.php`: ganti username/password admin.
- (Opsional) `admin_activity_log` kalau kamu mau jejak audit sederhana.
- Review keamanan menyeluruh (checklist Bab 6) di semua file admin yang sudah dibuat.

**Kriteria review:** dashboard informatif, password bisa diganti, tidak ada endpoint admin yang lupa dipasangi cek login/CSRF.

---

## FASE B — Halaman Pesan Anonim (`pesan.php`)

> Baru dimulai setelah **seluruh Iterasi 0–6 di atas selesai dan disetujui.**

### Iterasi 7 — Halaman Publik Kirim Pesan
**Tujuan:** ada halaman terpisah (`pesan.php`) tempat teman-teman Della mengirim ucapan, dengan opsi anonim.

**Cakupan:**
- Form: nama pengirim (opsional, ada toggle/checkbox "Kirim sebagai Anonim"), hubungan/peran (opsional), pesan (wajib), emoji (opsional).
- Validasi server-side (pesan tidak boleh kosong, panjang maksimum wajar).
- Simpan ke tabel `messages` dengan `status = 'pending'`, `source = 'public_form'`, `ip_address` dicatat.
- Honeypot field tersembunyi + rate-limit per IP (maks N submission per jam) untuk anti-spam dasar.
- Halaman "terima kasih" setelah submit (styling mengikuti tema romantis situs utama — ini halaman publik, jadi **tetap pakai desain pink/burgundy**, beda dengan admin panel yang utilitarian).

**Kriteria review:** kirim pesan test dari `pesan.php` → muncul di admin panel dengan status "pending", **belum** tampil di `index.php` publik sebelum di-approve.

---

### Iterasi 8 — Integrasi Penuh & Alur Moderasi
**Tujuan:** alur end-to-end lengkap: kirim → moderasi admin → tampil publik.

**Cakupan:**
- Tombol "+ Titip Doa/Ucapan" di `index.php` (kalau masih ada) diarahkan (link) ke `pesan.php`.
- Notifikasi sederhana di dashboard admin kalau ada pesan `pending` baru (badge angka).
- Pastikan pesan yang di-approve otomatis muncul di `index.php` sesuai urutan (terbaru dulu, atau manual sort — didiskusikan).

**Kriteria review:** simulasikan skenario penuh — teman kirim pesan lewat `pesan.php`, kamu approve dari HP/laptop lewat admin, ucapan langsung tayang di situs utama.

---

### Iterasi 9 — Pengerasan Anti-Spam & Penutup
**Tujuan:** halaman publik `pesan.php` tahan dari spam/bot dasar sebelum link-nya disebar ke banyak orang.

**Cakupan:**
- Tantangan sederhana (captcha matematika ringan, mis. "3 + 4 = ?") sebagai lapisan kedua selain honeypot.
- Review ulang rate-limit (apakah per IP cukup, atau perlu tambahan per session/browser fingerprint sederhana).
- Cek ulang seluruh checklist keamanan Bab 6 khusus untuk `pesan.php` (satu-satunya halaman publik yang menerima input dari orang di luar kamu).

**Kriteria review:** halaman siap dibagikan link-nya ke teman-teman Della tanpa risiko spam/serangan dasar.

---

## 7. Ringkasan Urutan Iterasi

| # | Nama | Fase | Hasil yang bisa dicek |
|---|---|---|---|
| 0 | Fondasi (DB, login, layout) | A | Login admin jalan, situs publik belum berubah |
| 1 | Hero Section | A | Edit hero dari admin → berubah di index.php |
| 2 | Gate & Cake Settings | A | Edit tanggal rilis dari admin → gerbang & cake berubah |
| 3 | Gallery CRUD | A | Kelola foto penuh dari admin |
| 4 | Love Letter | A | Edit surat cinta dari admin |
| 5 | Wishes/Messages moderasi | A | Kelola ucapan existing dari admin |
| 6 | Polish admin panel | A | Dashboard, ganti password, review keamanan |
| 7 | Halaman `pesan.php` | B | Publik bisa kirim ucapan anonim |
| 8 | Integrasi moderasi | B | Alur kirim → approve → tayang, lengkap |
| 9 | Anti-spam & penutup | B | Siap dibagikan ke publik |

---

## 8. Hal yang Perlu Kamu Putuskan Sebelum/Selama Berjalan

Ditandai di masing-masing iterasi di atas, dikumpulkan di sini biar tidak terlewat:

1. **(Iterasi 2)** Mekanisme `?dev=on`/`?dev=off` tetap dipertahankan apa adanya, atau digabung ke sesi login admin?
2. **(Iterasi 3)** Tombol like foto: perlu persisten ke DB (butuh endpoint tambahan) atau cukup animasi visual seperti sekarang?
3. **(Iterasi 3 & 5)** Setuju untuk menghapus form publik "+ Tambah Foto Kenangan" dan "+ Titip Doa/Ucapan" dari `index.php` karena sudah digantikan admin panel & `pesan.php`?
4. **(Umum)** Setuju dengan default di Bab 3 (PDO, 1 admin, admin panel styling sederhana, folder `/admin`)?

Kalau tidak ada koreksi, saya mulai dari **Iterasi 0** begitu kamu bilang "lanjut".
