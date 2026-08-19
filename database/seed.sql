-- =====================================================================
-- Della's 21st Birthday — Data Awal (Seed)
-- Iterasi 0 (Fase A: Fondasi)
--
-- Jalankan SETELAH schema.sql. Berisi:
--   1. Akun admin awal
--   2. Migrasi konten dari assets/js/data.js (biar tidak hilang)
--   3. Teks Hero/Cake/Gate saat ini (dari index.php) ke tabel settings
--
-- PENTING: Username & password login admin awal:
--   username : admin
--   password : della2026admin
-- Segera ganti password ini lewat Admin > Settings begitu fitur itu ada
-- (Iterasi 6). Sebelum itu tersedia, ganti manual lewat UPDATE ke tabel
-- `admins` (gunakan password_hash() PHP, jangan pernah simpan plaintext).
--
-- PENTING (encoding): banyak nilai di sini pakai emoji. Jalankan file ini
-- dengan client MySQL yang di-set ke utf8mb4, contoh lewat CLI:
--   mysql --default-character-set=utf8mb4 -u root -p < seed.sql
-- Tanpa flag itu, emoji bisa berubah jadi "?" saat tersimpan (pernah
-- terjadi di import awal Iterasi 0 — sudah diperbaiki manual, tapi
-- dicatat di sini supaya tidak terulang di instalasi baru).
-- =====================================================================

USE `della_birthday`;
-- USE `delg1541_della_birthday`;

-- ---------------------------------------------------------------------
-- 1. Akun admin awal
-- ---------------------------------------------------------------------
INSERT INTO `admins` (`username`, `password_hash`, `display_name`)
VALUES (
  'admin',
  '$2y$10$4URmUL/nzqUPqNeXVQ2GXexoDuz0Gmme4JOo1dy3xdMbwMrjrP4G.',
  'Admin Della'
)
ON DUPLICATE KEY UPDATE `username` = `username`;

-- ---------------------------------------------------------------------
-- 2. Settings: Hero Section, Cake Section, Gate/Countdown, Footer
--    (nilai persis meniru teks yang sekarang hardcoded di index.php)
-- ---------------------------------------------------------------------
INSERT INTO `settings` (`setting_key`, `setting_value`) VALUES
  ('site_title', 'Happy 21st Birthday, Della Puspa Ardiati 🌸✨'),
  ('site_description', 'Selamat Ulang Tahun ke-21 untuk Della Puspa Ardiati. Semoga cinta, kebahagiaan, dan segala impian indah senantiasa menyertaimu.'),

  ('hero_badge_text', '✨ 19 Agustus • 21st Special Milestone ✨'),
  ('hero_title_line1', 'Selamat Ulang Tahun ke-21,'),
  ('hero_title_line2', 'Della Puspa Ardiati'),
  ('hero_quote', 'Dua puluh satu tahun kebaikan, tawa yang menyejukkan jiwa, dan senyuman termanis yang selalu menghangatkan semesta.'),

  ('cake_banner_name', 'Della 21st'),
  ('cake_banner_tagline', 'Happy Birthday My Love'),
  ('cake_banner_date', '✨ 19 Agustus ✨'),
  ('cake_banner_recipient', '✨ Della Puspa Ardiati ✨'),

  ('release_timestamp', '2026-08-19 00:00:00'),

  ('footer_romantic_title', 'Happy 21st Birthday, Della Puspa Ardiati'),
  ('footer_subtitle', 'Semoga Bahagia, Cinta, & Berkah Senantiasa Menyertaimu'),
  ('footer_note', 'Diciptakan dengan segenap rasa cinta untuk merayakan momen berharga 21 tahun wanita terindah di semesta.')
ON DUPLICATE KEY UPDATE `setting_value` = VALUES(`setting_value`);

-- ---------------------------------------------------------------------
-- 4. Love Letter — migrasi dari DEFAULT_LOVE_LETTER di data.js
-- ---------------------------------------------------------------------
INSERT INTO `love_letter` (`id`, `salutation`, `paragraphs_json`, `closing`, `sender`)
VALUES (
  1,
  'Untuk Kekasih Terindahku, Della Puspa Ardiati,',
  '["Selamat ulang tahun yang ke-21, bidadari hatiku. Hari ini adalah hari di mana semesta menghadiahkan seseorang yang begitu cantik, berhati mulia, dan penuh cahaya ke dunia ini—dan aku bersyukur kepada Tuhan karena telah mempertemukanku denganmu.","Dua puluh satu tahun adalah usia yang sangat indah, sebuah gerbang menuju kedewasaan, mimpi-mimpi besar, dan petualangan baru. Menyaksikanmu tumbuh menjadi wanita yang anggun, cerdas, dan tangguh adalah salah satu kebanggaan terbesar dalam hidupku.","Terima kasih telah mewarnai setiap hariku dengan senyumanmu yang manis, tawamu yang renyah, dan pelukan hangatmu saat aku lelah. Di setiap langkah yang akan kamu ambil ke depan, ketahuilah bahwa tanganku akan selalu siap menggenggammu, pundakku akan selalu ada untukmu bersandar, dan hatiku akan selalu menjadi tempatmu pulang.","Semoga di usia 21 tahun ini, Allah SWT senantiasa melimpahkan kesehatan, kebahagiaan tanpa akhir, kemudahan dalam setiap urusan, dan tercapainya segala impian muliamu. Tetaplah menjadi Della yang selalu rendah hati, mempesona, dan membawa kehangatan bagi siapa pun.","Selamat bertambah usia, cintaku. Aku mencintaimu lebih dari kata-kata yang sanggup kuukirkan di sini, hari ini dan untuk selamanya."]',
  'Dengan segenap cinta dan ketulusan hati,',
  'Kekasihmu yang Selalu Menyayangimu ❤️'
)
ON DUPLICATE KEY UPDATE
  `salutation` = VALUES(`salutation`),
  `paragraphs_json` = VALUES(`paragraphs_json`),
  `closing` = VALUES(`closing`),
  `sender` = VALUES(`sender`);

