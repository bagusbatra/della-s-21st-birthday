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
-- 3. Memories (Gallery) — migrasi dari INITIAL_MEMORIES di data.js
-- ---------------------------------------------------------------------
INSERT INTO `memories`
  (`image_url`, `caption`, `event_date`, `location`, `tag`, `note`, `likes`, `sort_order`, `is_published`)
VALUES
  ('https://images.unsplash.com/photo-1517841905240-472988babdf9?auto=format&fit=crop&w=800&q=80',
   'Senyum manismu yang selalu menenangkan setiap hariku', '14 Februari', 'Café Kenangan, Sudirman', 'Momen Manis',
   'Hari itu kamu mengenakan baju favoritmu dan tertawa renyah saat menceritakan mimpimu.', 21, 1, 1),

  ('https://images.unsplash.com/photo-1529626455594-4ff0802cfb7e?auto=format&fit=crop&w=800&q=80',
   'Di bawah langit senja, Della terlihat sangat menawan', '28 Mei', 'Pantai Indah Kapuk', 'Kencan Spesial',
   'Angin laut menerbangkan rambutmu, dan aku menyadari betapa beruntungnya aku memilikimu.', 18, 2, 1),

  ('https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=800&q=80',
   'Buket bunga kecil untuk perempuan paling istimewa', '10 Juli', 'Taman Bunga Kota', 'Kejutan Kecil',
   'Ekspresi terkejut dan bahagia di wajahmu adalah pemandangan terbaik di dunia.', 25, 3, 1),

  ('https://images.unsplash.com/photo-1524504388940-b1c1722653e1?auto=format&fit=crop&w=800&q=80',
   'Tawa lepas bersamamu yang tak pernah pudar', '19 September', 'Perpustakaan & Toko Buku', 'Kenangan Hangat',
   'Kita bisa menghabiskan berjam-jam hanya berbicara tentang hal-hal kecil tanpa pernah bosan.', 19, 4, 1),

  ('https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=800&q=80',
   'Tatapan matamu yang selalu memberi semangat terbesar', '25 November', 'Rooftop City Lights', 'Momen Manis',
   'Di antara gemerlap lampu kota, hanya pesonamu yang paling bersinar terang bagiku.', 32, 5, 1),

  ('https://images.unsplash.com/photo-1516589178581-6cd7833ae3b2?auto=format&fit=crop&w=800&q=80',
   'Genggaman tangan yang akan selalu kujaga selamanya', '1 Januari', 'Awal Tahun Baru', 'Janji Hati',
   'Melangkah menyambut tahun baru dan masa depan yang penuh harapan bersamamu.', 40, 6, 1);

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

-- ---------------------------------------------------------------------
-- 5. Messages (Amplop Doa) — migrasi dari INITIAL_SECRET_WISHES di data.js
--    Semua data lama otomatis berstatus 'approved' & source 'seed'
--    supaya langsung tampil di situs publik seperti sekarang.
-- ---------------------------------------------------------------------
INSERT INTO `messages`
  (`sender_name`, `is_anonymous`, `role_relation`, `avatar_emoji`, `envelope_color`, `message`, `hint`, `status`, `source`, `likes`)
VALUES
  ('Mama & Papa', 0, 'Keluarga Tersayang', '🌸', 'rose',
   'Selamat ulang tahun ke-21 putri tercinta Della Puspa Ardiati. Semoga berkah umur, sehat selalu, dilancarkan jalan menuju cita-citamu, dan senantiasa menjadi kebanggaan keluarga. Doa terbaik Mama dan Papa selalu menyertaimu. 🎂🤲',
   'Doa hangat penuh kasih dari orang tua', 'approved', 'seed', 12),

  ('Maya & Salsa', 0, 'Bestie Kampus', '✨', 'amber',
   'Happy 21st Birthday Della sayang! Semoga di usia 21 ini kuliah lancar jaya, makin glowing, selalu bahagia, dan langgeng terus sama ayang! We love you so much! Jangan lupa traktirannya yaa! 💖🥳',
   'Pesan dari sahabat satu geng kampusmu!', 'approved', 'seed', 8),

  ('Kak Dimas & Kak Rara', 0, 'Kakak Tersayang', '💌', 'pink',
   'Happy Level 21 adik kami tersayang! Sukses untuk semua rencana hebat di usia kepala dua satu ini. Semoga karir dan impian masa depan tercapai satu per satu dengan mudah!',
   'Semangat dari kakak-kakak terhebat', 'approved', 'seed', 6),

  ('Nisa Rahmawati', 0, 'Teman Curhat SMA', '🌷', 'purple',
   'Dellaaa! Selamat 21 tahun! Gak kerasa ya dari jaman seragam putih abu-abu sekarang udah dewasa banget. Tetep jadi Della yang ceria, penyayang, dan pendengar yang baik ya. Miss you loads! 🥰',
   'Teman seperjuangan masa putih abu-abu', 'approved', 'seed', 9),

  ('Circle Sahabat Seperjuangan', 0, 'Teman Hangout', '🎉', 'indigo',
   'Barakallah fii umrik Della Puspa Ardiati! Tetap menjadi sosok yang menginspirasi, lembut hatinya, dan selalu membawa aura positif ke manapun Della melangkah.',
   'Pesan manis penuh ketulusan', 'approved', 'seed', 7),

  ('Kekasih Hati', 0, 'Selamanya Milikmu', '❤️', 'emerald',
   'Terima kasih telah hadir membawa warna terindah di hidupku. Selamat ulang tahun ke-21 bidadariku, aku mencintaimu hari ini, esok, dan selamanya.',
   'Pesan rahasia dari seseorang yang paling mencintaimu', 'approved', 'seed', 21);
