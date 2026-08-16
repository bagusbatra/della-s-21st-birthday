<?php
/**
 * Release gate: the full site only renders on/after the birthday date.
 * Before that, visitors see a locked countdown gate.
 *
 * Developer access (bypasses the gate while building/testing):
 *   index.php?dev=on   -> unlocks the full site early (stored in a cookie)
 *   index.php?dev=off  -> turns developer mode back off before launch
 */
date_default_timezone_set('Asia/Jakarta');

define('DELLA_RELEASE_TIMESTAMP', strtotime('2026-08-19 00:00:00'));
define('DELLA_DEV_COOKIE', 'della_dev_mode');

if (isset($_GET['dev'])) {
    if ($_GET['dev'] === 'on') {
        setcookie(DELLA_DEV_COOKIE, '1', time() + 60 * 60 * 24 * 365, '/');
        $_COOKIE[DELLA_DEV_COOKIE] = '1';
    } elseif ($_GET['dev'] === 'off') {
        setcookie(DELLA_DEV_COOKIE, '', time() - 3600, '/');
        unset($_COOKIE[DELLA_DEV_COOKIE]);
    }
}

$isDeveloperMode = isset($_COOKIE[DELLA_DEV_COOKIE]) && $_COOKIE[DELLA_DEV_COOKIE] === '1';
$isReleased = time() >= DELLA_RELEASE_TIMESTAMP;
$showFullSite = $isReleased || $isDeveloperMode;
?>
<!doctype html>
<html lang="id" class="scroll-smooth">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Happy 21st Birthday, Della Puspa Ardiati 🌸✨</title>
    <meta name="description" content="Selamat Ulang Tahun ke-21 untuk Della Puspa Ardiati. Semoga cinta, kebahagiaan, dan segala impian indah senantiasa menyertaimu." />
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Caveat:wght@400..700&family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;0,700;1,400;1,600&family=Great+Vibes&family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;1,400;1,600&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Compiled Stylesheet (Tailwind + Custom CSS) -->
    <link rel="stylesheet" href="assets/css/style.css">
  </head>
  <body class="bg-[#fffafb] text-[#5d1c32] antialiased selection:bg-[#ffc2d1] selection:text-[#5d1c32] min-h-screen overflow-x-hidden font-sans">

    <?php if ($isDeveloperMode && !$isReleased): ?>
    <!-- Developer Mode Banner (only visible pre-release, only to whoever unlocked ?dev=on) -->
    <div class="dev-mode-banner">
      🔧 Mode Developer Aktif — situs asli masih terkunci sampai 19 Agustus 2026.
      <a href="?dev=off">Nonaktifkan mode developer</a>
    </div>
    <?php endif; ?>

    <?php if (!$showFullSite): ?>

    <!-- ============================= -->
    <!-- Release Gate (locked view)     -->
    <!-- ============================= -->
    <div id="release-gate" class="release-gate">
      <div class="splash-hearts" aria-hidden="true">
        <span class="splash-heart" style="left:4%; font-size:1.1rem; animation-duration:9.5s; animation-delay:.2s; --drift:22px;">💗</span>
        <span class="splash-heart" style="left:12%; font-size:1.6rem; animation-duration:12s; animation-delay:1.6s; --drift:-18px;">🌸</span>
        <span class="splash-heart" style="left:20%; font-size:.9rem; animation-duration:8.5s; animation-delay:.6s; --drift:14px;">💕</span>
        <span class="splash-heart" style="left:29%; font-size:1.3rem; animation-duration:11s; animation-delay:2.4s; --drift:-24px;">✨</span>
        <span class="splash-heart" style="left:38%; font-size:1rem; animation-duration:10s; animation-delay:0s; --drift:20px;">💖</span>
        <span class="splash-heart" style="left:47%; font-size:1.5rem; animation-duration:13s; animation-delay:1.1s; --drift:-16px;">🌸</span>
        <span class="splash-heart" style="left:56%; font-size:1.1rem; animation-duration:9s; animation-delay:2.8s; --drift:18px;">💗</span>
        <span class="splash-heart" style="left:64%; font-size:1.4rem; animation-duration:12.5s; animation-delay:.9s; --drift:-20px;">✨</span>
        <span class="splash-heart" style="left:72%; font-size:.9rem; animation-duration:8s; animation-delay:1.9s; --drift:16px;">💕</span>
        <span class="splash-heart" style="left:80%; font-size:1.6rem; animation-duration:11.5s; animation-delay:.3s; --drift:-22px;">💖</span>
        <span class="splash-heart" style="left:88%; font-size:1.1rem; animation-duration:10.5s; animation-delay:2.2s; --drift:20px;">🌸</span>
        <span class="splash-heart" style="left:94%; font-size:1.3rem; animation-duration:9.8s; animation-delay:1.4s; --drift:-14px;">💗</span>
      </div>

      <div class="release-gate-content">
        <div class="splash-ring">🔒</div>
        <p class="font-cormorant italic text-lg sm:text-xl text-[#8a5d6c]">Untuk Della Puspa Ardiati,</p>
        <h1 class="font-romantic text-4xl sm:text-6xl text-[#5d1c32] mt-1">Kejutan Spesial Sedang Disiapkan</h1>
        <p class="font-serif-elegant text-lg sm:text-xl text-[#a44a66] font-semibold mt-3">Akan terbuka tepat pada 19 Agustus 2026 💗</p>

        <div id="gate-countdown-grid" class="gate-countdown-grid">
          <div class="gate-countdown-box">
            <span id="gate-cd-days">00</span>
            <small>Hari</small>
          </div>
          <div class="gate-countdown-box">
            <span id="gate-cd-hours">00</span>
            <small>Jam</small>
          </div>
          <div class="gate-countdown-box">
            <span id="gate-cd-minutes">00</span>
            <small>Menit</small>
          </div>
          <div class="gate-countdown-box">
            <span id="gate-cd-seconds">00</span>
            <small>Detik</small>
          </div>
        </div>

        <p class="font-cormorant italic text-sm text-[#8a5d6c] mt-6">Sabar ya, sedikit lagi waktunya tiba&hellip;</p>
      </div>
    </div>

    <script src="assets/js/gate.js"></script>

    <?php else: ?>

    <!-- Romantic Splash Screen (shown 10 seconds on load) -->
    <div id="splash-screen">
      <div class="splash-hearts" aria-hidden="true">
        <span class="splash-heart" style="left:4%; font-size:1.1rem; animation-duration:9.5s; animation-delay:.2s; --drift:22px;">💗</span>
        <span class="splash-heart" style="left:12%; font-size:1.6rem; animation-duration:12s; animation-delay:1.6s; --drift:-18px;">🌸</span>
        <span class="splash-heart" style="left:20%; font-size:.9rem; animation-duration:8.5s; animation-delay:.6s; --drift:14px;">💕</span>
        <span class="splash-heart" style="left:29%; font-size:1.3rem; animation-duration:11s; animation-delay:2.4s; --drift:-24px;">✨</span>
        <span class="splash-heart" style="left:38%; font-size:1rem; animation-duration:10s; animation-delay:0s; --drift:20px;">💖</span>
        <span class="splash-heart" style="left:47%; font-size:1.5rem; animation-duration:13s; animation-delay:1.1s; --drift:-16px;">🌸</span>
        <span class="splash-heart" style="left:56%; font-size:1.1rem; animation-duration:9s; animation-delay:2.8s; --drift:18px;">💗</span>
        <span class="splash-heart" style="left:64%; font-size:1.4rem; animation-duration:12.5s; animation-delay:.9s; --drift:-20px;">✨</span>
        <span class="splash-heart" style="left:72%; font-size:.9rem; animation-duration:8s; animation-delay:1.9s; --drift:16px;">💕</span>
        <span class="splash-heart" style="left:80%; font-size:1.6rem; animation-duration:11.5s; animation-delay:.3s; --drift:-22px;">💖</span>
        <span class="splash-heart" style="left:88%; font-size:1.1rem; animation-duration:10.5s; animation-delay:2.2s; --drift:20px;">🌸</span>
        <span class="splash-heart" style="left:94%; font-size:1.3rem; animation-duration:9.8s; animation-delay:1.4s; --drift:-14px;">💗</span>
      </div>

      <div class="splash-content">
        <div class="splash-ring">❤️</div>
        <p class="splash-line splash-line-1 font-cormorant italic text-lg sm:text-xl text-[#8a5d6c]">Untuk seseorang yang teristimewa,</p>
        <h1 class="splash-line splash-line-2 font-romantic text-5xl sm:text-7xl text-[#5d1c32]">Della Puspa Ardiati</h1>
        <p class="splash-line splash-line-3 font-serif-elegant text-xl sm:text-2xl text-[#a44a66] font-semibold">Selamat Ulang Tahun ke-21 🎂</p>
        <p class="splash-line splash-line-4 font-cormorant italic text-sm sm:text-base text-[#8a5d6c]">Sedang merangkai kenangan dan doa terindah untukmu&hellip;</p>

        <div class="splash-progress-track">
          <div id="splash-progress-bar"></div>
        </div>

        <button id="btn-skip-splash" type="button" class="splash-skip">Lewati ✕</button>
      </div>
    </div>

    <!-- Falling Rose Petals Canvas -->
    <canvas id="petalCanvas" class="fixed inset-0 pointer-events-none z-10 w-full h-full"></canvas>

    <!-- Navigation Bar -->
    <header id="main-header" class="fixed top-0 inset-x-0 z-40 bg-[#fffafb]/90 backdrop-blur-md border-b border-[#ffe1e9] py-3 transition-all">
      <div class="max-w-6xl mx-auto px-4 sm:px-6 flex items-center justify-between">
        <!-- Logo -->
        <a href="#hero-section" class="flex items-center gap-2.5 group">
          <div class="w-8 h-8 rounded-full bg-[#5d1c32] text-[#ffc2d1] flex items-center justify-center shadow-xs border border-[#ffe1e9] group-hover:scale-105 transition-transform">
            <span class="text-sm">❤️</span>
          </div>
          <div>
            <span class="font-serif-elegant font-bold text-[#5d1c32] text-sm sm:text-base leading-tight block">Della Puspa Ardiati</span>
            <span class="text-[10px] uppercase tracking-[0.2em] text-[#a44a66] font-medium block">21st Birthday Milestone</span>
          </div>
        </a>

        <!-- Desktop Navigation Links -->
        <nav class="hidden md:flex items-center gap-6 text-xs font-medium text-[#8a5d6c]">
          <a href="#countdown-section" class="hover:text-[#5d1c32] transition-colors">Hitung Mundur</a>
          <a href="#cake-section" class="hover:text-[#5d1c32] transition-colors">Tiup Lilin 21</a>
          <a href="#memories-section" class="hover:text-[#5d1c32] transition-colors">Kenangan Indah</a>
          <a href="#wishes-section" class="hover:text-[#5d1c32] transition-colors">Amplop Doa</a>
        </nav>

        <!-- Navbar Actions -->
        <div class="flex items-center gap-2">
          <!-- Audio Toggle Button -->
          <button
            id="btn-nav-music"
            type="button"
            title="Putar Musik Latar Romantis"
            class="p-2 rounded-full border border-[#ffe1e9] bg-white/80 text-[#5d1c32] hover:bg-[#fdf2f8] transition-all active:scale-95"
          >
            <span id="music-playing-indicator" class="text-sm block">🎵</span>
          </button>

          <!-- Love Letter Button -->
          <button
            data-action="open-letter"
            type="button"
            class="px-3.5 sm:px-4 py-1.5 sm:py-2 rounded-xl bg-[#5d1c32] hover:bg-[#481426] text-white font-medium text-xs shadow-xs border border-[#ffe1e9]/30 flex items-center gap-1.5 transition-all active:scale-95"
          >
            <span>💌</span>
            <span class="hidden sm:inline">Surat Cinta</span>
          </button>

          <!-- Mobile Hamburger Menu Button -->
          <button
            id="btn-mobile-menu"
            type="button"
            class="p-2 rounded-xl border border-[#ffe1e9] text-[#5d1c32] md:hidden hover:bg-[#fce7f3]"
          >
            ☰
          </button>
        </div>
      </div>

      <!-- Mobile Dropdown Menu -->
      <div id="mobile-nav-menu" class="hidden md:hidden px-4 pt-3 pb-4 border-t border-[#ffe1e9] mt-3 bg-[#fffafb]/95 space-y-2 text-xs font-medium text-[#5d1c32]">
        <a href="#countdown-section" class="block py-2 px-3 rounded-lg hover:bg-[#fce7f3]">Hitung Mundur</a>
        <a href="#cake-section" class="block py-2 px-3 rounded-lg hover:bg-[#fce7f3]">Tiup 21 Lilin</a>
        <a href="#memories-section" class="block py-2 px-3 rounded-lg hover:bg-[#fce7f3]">Kenangan Indah</a>
        <a href="#wishes-section" class="block py-2 px-3 rounded-lg hover:bg-[#fce7f3]">Amplop Doa Teman</a>
      </div>
    </header>

    <!-- Floating Romantic Music Bar -->
    <div id="music-floating-bar" class="fixed bottom-4 left-4 z-40 bg-white/95 backdrop-blur-md border border-[#ffe1e9] rounded-2xl shadow-lg p-2.5 flex items-center gap-2.5 text-xs text-[#5d1c32] max-w-[280px] sm:max-w-xs">
      <button id="btn-toggle-floating-music" type="button" class="w-8 h-8 rounded-full bg-[#5d1c32] text-white flex items-center justify-center text-xs shadow-xs">
        ▶️
      </button>
      <div class="flex-1 min-w-0">
        <div id="current-track-title" class="font-serif-elegant font-semibold text-xs truncate">Harmoni Kasih</div>
        <div id="current-track-mood" class="text-[10px] text-[#a44a66] truncate">Menyentuh & Lembut</div>
      </div>
      <div class="flex items-center gap-1">
        <button id="btn-prev-track" type="button" class="p-1 rounded hover:bg-[#fce7f3] text-[10px]" title="Lagu Sebelumnya">⏮️</button>
        <button id="btn-next-track" type="button" class="p-1 rounded hover:bg-[#fce7f3] text-[10px]" title="Lagu Selanjutnya">⏭️</button>
      </div>
    </div>

    <!-- Main Content -->
    <main class="relative z-20 space-y-16 sm:space-y-24 pt-20">

      <!-- Hero Section -->
      <section id="hero-section" class="min-h-[85vh] flex flex-col items-center justify-center text-center px-4 pt-12 pb-16 relative">
        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-[#fce7f3] border border-[#ffc2d1] text-[#5d1c32] text-xs font-semibold uppercase tracking-wider mb-6 animate-soft-pulse">
          <span>✨ 19 Agustus • 21st Special Milestone ✨</span>
        </div>

        <h1 class="font-serif-elegant text-4xl sm:text-6xl md:text-7xl font-normal text-[#5d1c32] max-w-4xl leading-tight mb-4">
          Selamat Ulang Tahun ke-21,<br/>
          <span class="font-romantic text-5xl sm:text-7xl md:text-8xl text-[#a44a66] block mt-2">Della Puspa Ardiati</span>
        </h1>

        <p class="font-cormorant italic text-xl sm:text-2xl md:text-3xl text-[#8a5d6c] max-w-2xl mx-auto mb-8 leading-relaxed">
          "Dua puluh satu tahun kebaikan, tawa yang menyejukkan jiwa, dan senyuman termanis yang selalu menghangatkan semesta."
        </p>

        <div class="flex flex-wrap items-center justify-center gap-3.5 relative z-20">
          <button
            data-action="open-letter"
            type="button"
            class="px-7 py-3.5 rounded-2xl bg-[#5d1c32] hover:bg-[#481426] text-white font-medium text-sm shadow-md border border-[#ffe1e9]/30 flex items-center gap-2 transition-all active:scale-95"
          >
            <span>Buka Surat Cinta Rahasia 💌</span>
          </button>
          <button
            id="btn-hero-confetti"
            type="button"
            class="px-6 py-3.5 rounded-2xl bg-white hover:bg-[#fdf2f8] text-[#5d1c32] border border-[#ffe1e9] font-medium text-sm shadow-xs flex items-center gap-2 transition-all active:scale-95"
          >
            <span>Letuskan Confetti Full Screen 🎆</span>
          </button>
        </div>
      </section>

      <!-- Countdown Section -->
      <section id="countdown-section" class="max-w-5xl mx-auto px-4">
        <div class="bg-white/80 backdrop-blur-md rounded-3xl p-6 sm:p-10 border border-[#ffe1e9] shadow-xs text-center">
          <div class="inline-block p-3 rounded-full bg-[#fce7f3] text-[#5d1c32] mb-3">
            <span class="text-xl">⏳</span>
          </div>
          <p class="uppercase tracking-[0.3em] text-xs text-[#a44a66] font-medium mb-1">Birthday Countdown</p>
          <h2 class="font-serif-elegant text-2xl sm:text-4xl text-[#5d1c32] mb-3">
            Menuju Detik Bahagia Della Puspa Ardiati
          </h2>

          <div class="flex flex-wrap items-center justify-center gap-2 text-[#8a5d6c] text-xs sm:text-sm mb-6">
            <span>Target Momen: <strong id="target-date-display" class="text-[#5d1c32]">19 Agustus 2026, 00:00 WIB</strong></span>
            <button
              id="btn-toggle-edit-countdown"
              type="button"
              class="text-xs text-[#a44a66] hover:text-[#5d1c32] underline underline-offset-2 ml-1 font-medium"
            >
              Ubah Target
            </button>
          </div>

          <!-- Edit Target Date Form -->
          <div id="countdown-edit-container" class="hidden mb-6 p-4 bg-white rounded-2xl border border-[#ffe1e9] max-w-md mx-auto">
            <form id="form-edit-target-date" class="flex flex-wrap items-center justify-center gap-2 text-xs mb-3">
              <input
                id="input-target-date"
                type="datetime-local"
                class="bg-[#fffafb] border border-[#ffe1e9] rounded-lg px-3 py-1.5 text-[#5d1c32] focus:outline-none focus:border-[#a44a66]"
              />
              <button
                type="submit"
                class="bg-[#5d1c32] hover:bg-[#481426] text-white px-3 py-1.5 rounded-lg font-medium transition-colors"
              >
                Simpan
              </button>
              <button
                id="btn-cancel-edit-countdown"
                type="button"
                class="text-[#8a5d6c] hover:text-[#5d1c32] px-2 py-1"
              >
                Batal
              </button>
            </form>
            <div class="pt-2 border-t border-[#ffe1e9] flex flex-wrap items-center justify-center gap-2">
              <button
                id="btn-test-countdown-5s"
                type="button"
                class="px-3 py-1 rounded-full bg-[#fce7f3] hover:bg-[#ffc2d1]/60 text-[#5d1c32] text-[11px] font-medium border border-[#ffc2d1]"
              >
                ⚡ Uji Hitung Mundur (5 Detik)
              </button>
              <button
                id="btn-test-confetti-explosion"
                type="button"
                class="px-3 py-1 rounded-full bg-[#5d1c32] hover:bg-[#481426] text-white text-[11px] font-medium"
              >
                ✨ Uji Confetti Layar Penuh
              </button>
            </div>
          </div>

          <!-- Countdown Digits Grid -->
          <div id="countdown-grid" class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-6 max-w-3xl mx-auto mb-6">
            <div class="bg-[#fffafb] border border-[#ffe1e9] rounded-2xl p-4 shadow-xs">
              <span id="cd-days" class="font-serif-elegant text-4xl sm:text-5xl text-[#5d1c32] block font-bold">00</span>
              <span class="text-[11px] uppercase tracking-widest text-[#8a5d6c] font-medium">Hari</span>
            </div>
            <div class="bg-[#fffafb] border border-[#ffe1e9] rounded-2xl p-4 shadow-xs">
              <span id="cd-hours" class="font-serif-elegant text-4xl sm:text-5xl text-[#5d1c32] block font-bold">00</span>
              <span class="text-[11px] uppercase tracking-widest text-[#8a5d6c] font-medium">Jam</span>
            </div>
            <div class="bg-[#fffafb] border border-[#ffe1e9] rounded-2xl p-4 shadow-xs">
              <span id="cd-minutes" class="font-serif-elegant text-4xl sm:text-5xl text-[#5d1c32] block font-bold">00</span>
              <span class="text-[11px] uppercase tracking-widest text-[#8a5d6c] font-medium">Menit</span>
            </div>
            <div class="bg-[#fffafb] border border-[#ffe1e9] rounded-2xl p-4 shadow-xs">
              <span id="cd-seconds" class="font-serif-elegant text-4xl sm:text-5xl text-[#a44a66] block font-bold animate-pulse">00</span>
              <span class="text-[11px] uppercase tracking-widest text-[#8a5d6c] font-medium">Detik</span>
            </div>
          </div>

          <!-- Zero Celebration Banner -->
          <div id="countdown-passed-banner" class="hidden py-6 text-center">
            <div class="inline-flex items-center justify-center p-4 bg-[#fce7f3] border border-[#ffc2d1] rounded-full mb-4 animate-bounce">
              <span class="text-3xl">🎉</span>
            </div>
            <h3 class="font-serif-elegant text-2xl sm:text-3xl text-[#5d1c32] font-bold mb-2">
              🎉 Selamat Ulang Tahun ke-21, Della Puspa Ardiati! 💖
            </h3>
            <p class="text-[#8a5d6c] text-sm sm:text-base max-w-xl mx-auto mb-6">
              Hitungan mundur telah mencapai detik ke-0! Semoga usiamu yang ke-21 dipenuhi keberkahan, senyuman tanpa akhir, dan limpahan cinta terindah.
            </p>
            <div class="flex flex-wrap items-center justify-center gap-3">
              <button
                id="btn-countdown-celebrate-again"
                type="button"
                class="px-6 py-3 rounded-2xl bg-[#5d1c32] hover:bg-[#481426] text-white font-medium text-xs shadow-md flex items-center gap-2"
              >
                <span>Letuskan Confetti Full Screen Lagi! 🎆</span>
              </button>
              <button
                id="btn-countdown-subtle-confetti"
                type="button"
                class="px-4 py-3 rounded-2xl bg-white hover:bg-[#fdf2f8] text-[#5d1c32] border border-[#ffe1e9] font-medium text-xs"
              >
                <span>Hujan Bunga Lembut 🌸</span>
              </button>
            </div>
          </div>
        </div>
      </section>

      <!-- Birthday Cake Section -->
      <section id="cake-section" class="max-w-5xl mx-auto px-4">
        <div class="text-center max-w-2xl mx-auto mb-8">
          <p class="uppercase tracking-[0.3em] text-xs text-[#a44a66] font-medium mb-1">Make a Wish</p>
          <h2 class="font-serif-elegant text-3xl sm:text-4xl text-[#5d1c32] mb-2">Tiup 21 Lilin Harapan Della</h2>
          <p class="text-[#8a5d6c] text-xs sm:text-sm">Klik lilin satu per satu atau tiup semua sekaligus untuk menyampaikan permohonanmu.</p>
        </div>

        <div class="bg-white/80 backdrop-blur-md rounded-3xl p-6 sm:p-10 border border-[#ffe1e9] shadow-xs text-center max-w-3xl mx-auto">
          <!-- Candle Status Badge -->
          <div class="mb-6">
            <span id="candle-status-badge" class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full bg-[#fce7f3] text-[#5d1c32] border border-[#ffc2d1] text-xs font-semibold">
              21 dari 21 Lilin Masih Menyala 🔥
            </span>
          </div>

          <!-- 21 Interactive Candles Row -->
          <div id="candles-container" class="flex flex-wrap justify-center items-end gap-1.5 sm:gap-2 mb-6 min-h-[90px]">
            <!-- Rendered by JS -->
          </div>

          <!-- Birthday Cake Illustration -->
          <div class="max-w-md mx-auto relative flex flex-col items-center mb-8">
            <div class="w-48 sm:w-56 h-10 bg-[#ffe1e9] rounded-t-2xl border-t-2 border-[#ffc2d1] flex items-center justify-center shadow-xs">
              <span class="font-romantic text-2xl text-[#5d1c32] font-bold">Della 21st</span>
            </div>
            <div class="w-64 sm:w-72 h-12 bg-[#fce7f3] border-t-2 border-[#ffc2d1] flex items-center justify-center shadow-xs">
              <span class="font-serif-elegant text-[#5d1c32] text-xs tracking-widest uppercase font-semibold">Happy Birthday My Love</span>
            </div>
            <div class="w-80 sm:w-96 h-14 bg-white rounded-b-3xl border-t-2 border-[#ffc2d1] flex items-center justify-around px-4 shadow-sm">
              <span class="text-xs text-[#5d1c32] font-medium">✨ 19 Agustus ✨</span>
              <span class="text-xs text-[#5d1c32] font-medium">✨ Della Puspa Ardiati ✨</span>
            </div>
          </div>

          <!-- Controls -->
          <div class="flex flex-wrap items-center justify-center gap-3">
            <button
              id="btn-blow-all-candles"
              type="button"
              class="px-6 py-3 rounded-2xl bg-[#5d1c32] hover:bg-[#481426] text-white font-medium text-xs shadow-xs border border-[#ffe1e9]/30 flex items-center gap-2 transition-all active:scale-95"
            >
              <span>Tiup Semua 21 Lilin Sekaligus 💨</span>
            </button>
            <button
              id="btn-relight-candles"
              type="button"
              class="px-4 py-3 rounded-2xl bg-[#fce7f3] hover:bg-[#ffc2d1]/60 text-[#5d1c32] border border-[#ffc2d1] font-medium text-xs transition-all active:scale-95"
            >
              <span>Nyalakan Lilin Kembali 🕯️</span>
            </button>
            <button
              id="btn-open-make-wish"
              type="button"
              class="px-4 py-3 rounded-2xl bg-white hover:bg-[#fdf2f8] text-[#5d1c32] border border-[#ffe1e9] font-medium text-xs transition-all active:scale-95"
            >
              <span>Tulis Harapan (Make a Wish) ✍️</span>
            </button>
          </div>
        </div>
      </section>

      <!-- Polaroid Photo Gallery -->
      <section id="memories-section" class="max-w-6xl mx-auto px-4">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mb-8">
          <div class="text-center sm:text-left">
            <p class="uppercase tracking-[0.3em] text-xs text-[#a44a66] font-medium mb-1">Captured Moments</p>
            <h2 class="font-serif-elegant text-3xl sm:text-4xl text-[#5d1c32]">Jejak Kasih & Senyuman Della</h2>
            <p class="text-[#8a5d6c] text-xs sm:text-sm mt-1">Koleksi foto berharga dalam perjalanan 21 tahun kehidupan yang mempesona.</p>
          </div>
          <button
            id="btn-open-add-memory"
            type="button"
            class="px-4 py-2.5 rounded-2xl bg-[#5d1c32] hover:bg-[#481426] text-white font-medium text-xs flex items-center gap-1.5 shadow-xs transition-all active:scale-95 shrink-0"
          >
            <span>+ Tambah Foto Kenangan</span>
          </button>
        </div>

        <!-- Tag Filters -->
        <div id="gallery-tag-filters" class="flex flex-wrap items-center gap-2 mb-6">
          <!-- Rendered by JS -->
        </div>

        <!-- Gallery Grid -->
        <div id="gallery-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          <!-- Rendered by JS -->
        </div>
      </section>

      <!-- Secret Wishes Envelopes -->
      <section id="wishes-section" class="max-w-6xl mx-auto px-4">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mb-8">
          <div class="text-center sm:text-left">
            <p class="uppercase tracking-[0.3em] text-xs text-[#a44a66] font-medium mb-1">Secret Letters & Prayers</p>
            <h2 class="font-serif-elegant text-3xl sm:text-4xl text-[#5d1c32]">Ucapan Kasih dari Teman & Keluarga</h2>
            <p class="text-[#8a5d6c] text-xs sm:text-sm mt-1">Buka amplop tertutup untuk membaca doa dan pesan penuh kasih untuk Della.</p>
          </div>
          <button
            id="btn-open-add-wish"
            type="button"
            class="px-4 py-2.5 rounded-2xl bg-[#5d1c32] hover:bg-[#481426] text-white font-medium text-xs flex items-center gap-1.5 shadow-xs transition-all active:scale-95 shrink-0"
          >
            <span>+ Titip Doa / Ucapan</span>
          </button>
        </div>

        <!-- Wish Filters -->
        <div class="flex items-center gap-2 mb-6">
          <button id="filter-wishes-all" type="button" class="px-3 py-1.5 rounded-full text-xs font-medium bg-[#5d1c32] text-white shadow-xs">
            Semua Amplop
          </button>
          <button id="filter-wishes-unopened" type="button" class="px-3 py-1.5 rounded-full text-xs font-medium bg-white text-[#8a5d6c] border border-[#ffe1e9] hover:bg-[#fdf2f8]">
            Belum Terbuka ✉️
          </button>
          <button id="filter-wishes-opened" type="button" class="px-3 py-1.5 rounded-full text-xs font-medium bg-white text-[#8a5d6c] border border-[#ffe1e9] hover:bg-[#fdf2f8]">
            Sudah Terbuka 💌
          </button>
        </div>

        <!-- Wishes Grid -->
        <div id="wishes-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          <!-- Rendered by JS -->
        </div>
      </section>

    </main>

    <!-- Footer -->
    <footer class="bg-[#fffafb] pt-16 pb-12 px-4 border-t border-[#ffe1e9] text-center mt-20 relative z-20">
      <div class="max-w-4xl mx-auto space-y-4">
        <div class="w-10 h-10 mx-auto rounded-full bg-[#5d1c32] text-[#ffc2d1] flex items-center justify-center text-base">
          ❤️
        </div>
        <p class="font-romantic text-3xl sm:text-4xl text-[#a44a66]">Happy 21st Birthday, Della Puspa Ardiati</p>
        <h3 class="font-serif-elegant text-lg sm:text-xl text-[#5d1c32]">Semoga Bahagia, Cinta, & Berkah Senantiasa Menyertaimu</h3>
        <p class="text-xs text-[#8a5d6c] max-w-lg mx-auto leading-relaxed">
          Diciptakan dengan segenap rasa cinta untuk merayakan momen berharga 21 tahun wanita terindah di semesta.
        </p>

        <div class="flex flex-wrap items-center justify-center gap-3 pt-3">
          <button
            data-action="open-letter"
            type="button"
            class="px-5 py-2.5 rounded-full bg-[#5d1c32] hover:bg-[#481426] text-white font-medium text-xs shadow-xs"
          >
            Baca Kembali Surat Cinta 💌
          </button>
          <button
            id="btn-share-app"
            type="button"
            class="px-5 py-2.5 rounded-full bg-white hover:bg-[#fdf2f8] text-[#5d1c32] border border-[#ffe1e9] font-medium text-xs shadow-xs"
          >
            Bagikan Momen Ini 🔗
          </button>
          <button
            id="btn-back-to-top"
            type="button"
            class="p-2.5 rounded-full bg-white hover:bg-[#fdf2f8] text-[#5d1c32] border border-[#ffe1e9] text-xs"
            title="Kembali ke Atas"
          >
            ⬆️
          </button>
        </div>
      </div>
    </footer>

    <!-- Love Letter Modal -->
    <div id="love-letter-modal" class="fixed inset-0 z-50 hidden bg-black/75 backdrop-blur-sm p-4 flex items-center justify-center">
      <div class="relative max-w-2xl w-full bg-[#fffafb] rounded-3xl p-6 sm:p-10 shadow-2xl border border-[#ffe1e9] max-h-[90vh] overflow-y-auto">
        <button id="btn-close-letter" type="button" class="absolute top-4 right-4 p-2 rounded-full text-[#8a5d6c] hover:text-[#5d1c32] hover:bg-[#fdf2f8]">
          ✕
        </button>

        <!-- Sealed Envelope View -->
        <div id="letter-sealed-view" class="text-center py-8">
          <div class="w-20 h-20 mx-auto rounded-full bg-[#5d1c32] text-[#ffc2d1] flex items-center justify-center text-3xl shadow-lg border-2 border-[#ffe1e9] mb-4">
            💌
          </div>
          <span class="px-3.5 py-1 rounded-full bg-[#fce7f3] text-[#5d1c32] text-xs font-semibold border border-[#ffc2d1]">Surat Cinta Spesial 21st</span>
          <h3 class="font-serif-elegant text-2xl sm:text-3xl text-[#5d1c32] mt-3 font-semibold">Untuk Della Puspa Ardiati</h3>
          <p class="font-romantic text-2xl text-[#a44a66] mb-6">Segel Hati Khusus Hari Ulang Tahun</p>
          <button id="btn-break-letter-seal" type="button" class="px-7 py-3 rounded-2xl bg-[#5d1c32] hover:bg-[#481426] text-white font-semibold text-xs shadow-md transition-all active:scale-95">
            Buka Segel & Baca Surat Cinta ❤️
          </button>
        </div>

        <!-- Opened Letter View -->
        <div id="letter-opened-view" class="hidden">
          <div class="text-center mb-6">
            <span class="px-3 py-1 rounded-full bg-[#fce7f3] text-[#5d1c32] text-xs font-semibold border border-[#ffc2d1]">Surat Cinta 21st Birthday</span>
            <h3 id="letter-salutation-display" class="font-serif-elegant text-xl sm:text-2xl text-[#5d1c32] mt-3 font-bold"></h3>
            <p class="font-romantic text-2xl text-[#a44a66]">Spesial 19 Agustus</p>
          </div>

          <div id="letter-paragraphs-container" class="border-t border-b border-[#ffe1e9] py-6 my-4">
            <!-- Paragraphs rendered by JS -->
          </div>

          <!-- Edit Form if toggled -->
          <div id="letter-edit-form" class="hidden my-4">
            <textarea id="textarea-letter-body" rows="8" class="w-full p-3 rounded-xl border border-[#ffe1e9] bg-white text-xs text-[#5d1c32] focus:outline-none focus:border-[#a44a66]"></textarea>
            <div class="flex justify-end gap-2 mt-2">
              <button id="btn-save-letter-edit" type="button" class="px-4 py-1.5 rounded-lg bg-[#5d1c32] text-white text-xs">Simpan</button>
              <button id="btn-cancel-letter-edit" type="button" class="px-3 py-1.5 rounded-lg text-[#8a5d6c] text-xs">Batal</button>
            </div>
          </div>

          <div class="flex items-center justify-between pt-2">
            <button id="btn-copy-letter" type="button" class="text-xs text-[#a44a66] hover:underline font-medium">
              Salin Surat Cinta 📋
            </button>
            <div class="text-right">
              <p id="letter-closing-display" class="font-cormorant text-xs text-[#8a5d6c]"></p>
              <p id="letter-sender-display" class="font-romantic text-2xl text-[#5d1c32]"></p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Lightbox Modal -->
    <div id="lightbox-modal" class="fixed inset-0 z-50 hidden bg-black/85 backdrop-blur-md p-4 flex items-center justify-center">
      <div class="relative max-w-3xl w-full bg-[#fffafb] rounded-3xl p-6 shadow-2xl border border-[#ffe1e9] max-h-[90vh] overflow-y-auto">
        <button id="btn-close-lightbox" type="button" class="absolute top-4 right-4 p-2 rounded-full text-[#8a5d6c] hover:text-[#5d1c32]">
          ✕
        </button>
        <div class="aspect-[4/3] rounded-2xl overflow-hidden mb-4 bg-black/5">
          <img id="lightbox-img" src="" alt="" class="w-full h-full object-contain" />
        </div>
        <div class="flex items-center justify-between pb-2 border-b border-[#ffe1e9]">
          <div>
            <span id="lightbox-tag" class="px-2.5 py-0.5 rounded-full bg-[#fce7f3] text-[#5d1c32] text-[10px] font-semibold"></span>
            <h3 id="lightbox-caption" class="font-serif-elegant text-lg text-[#5d1c32] font-bold mt-1"></h3>
            <p id="lightbox-date" class="text-xs text-[#8a5d6c]"></p>
          </div>
          <button id="lightbox-like-btn" type="button" class="flex items-center gap-1 px-3 py-1.5 rounded-full bg-[#fce7f3] text-[#5d1c32] text-xs font-semibold">
            ❤️ <span id="lightbox-like-count">0</span>
          </button>
        </div>
        <p id="lightbox-note" class="text-xs text-[#8a5d6c] mt-3 leading-relaxed"></p>
      </div>
    </div>

    <!-- Make a Wish Modal -->
    <div id="wish-modal" class="fixed inset-0 z-50 hidden bg-black/75 backdrop-blur-sm p-4 flex items-center justify-center">
      <div class="relative max-w-lg w-full bg-[#fffafb] rounded-3xl p-6 sm:p-8 shadow-2xl border border-[#ffe1e9]">
        <button id="btn-close-wish" type="button" class="absolute top-4 right-4 p-2 text-[#8a5d6c]">✕</button>
        <div class="text-center mb-4">
          <span class="text-3xl block mb-2">🎂✨</span>
          <h3 class="font-serif-elegant text-2xl text-[#5d1c32] font-bold">Harapan Ulang Tahun ke-21</h3>
          <p class="text-xs text-[#8a5d6c]">Bisikkan doa dan impian terindahmu ke semesta...</p>
        </div>
        <form id="form-make-wish" class="space-y-4">
          <textarea id="input-wish-text" rows="4" required placeholder="Tuliskan permohonan tulusmu di sini..." class="w-full p-3 rounded-2xl border border-[#ffe1e9] bg-white text-xs text-[#5d1c32] focus:outline-none focus:border-[#a44a66]"></textarea>
          <button type="submit" class="w-full py-3 rounded-2xl bg-[#5d1c32] hover:bg-[#481426] text-white font-medium text-xs shadow-xs">
            Terbangkan Harapan ke Langit 🕊️✨
          </button>
        </form>
        <div id="wish-sent-alert" class="hidden mt-4 p-3 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-xl text-center text-xs font-medium">
          ✨ Harapanmu telah melayang dan didoakan semesta! Semoga terwujud indah!
        </div>
      </div>
    </div>

    <!-- Add Memory Modal -->
    <div id="add-memory-modal" class="fixed inset-0 z-50 hidden bg-black/75 backdrop-blur-sm p-4 flex items-center justify-center">
      <div class="relative max-w-md w-full bg-[#fffafb] rounded-3xl p-6 shadow-2xl border border-[#ffe1e9]">
        <button id="btn-close-add-memory" type="button" class="absolute top-4 right-4 p-2 text-[#8a5d6c]">✕</button>
        <h3 class="font-serif-elegant text-xl text-[#5d1c32] font-bold mb-3">Tambah Kenangan Foto Baru</h3>
        <form id="form-add-memory" class="space-y-3 text-xs">
          <div>
            <label class="block text-[#8a5d6c] mb-1">URL Foto</label>
            <input id="input-memory-url" type="url" required placeholder="https://..." class="w-full p-2.5 rounded-xl border border-[#ffe1e9] bg-white text-[#5d1c32]" />
          </div>
          <div>
            <label class="block text-[#8a5d6c] mb-1">Judul / Caption Foto</label>
            <input id="input-memory-caption" type="text" required placeholder="Contoh: Senyuman Manis Della" class="w-full p-2.5 rounded-xl border border-[#ffe1e9] bg-white text-[#5d1c32]" />
          </div>
          <div class="grid grid-cols-2 gap-2">
            <div>
              <label class="block text-[#8a5d6c] mb-1">Tanggal</label>
              <input id="input-memory-date" type="text" placeholder="15 Agustus" class="w-full p-2.5 rounded-xl border border-[#ffe1e9] bg-white text-[#5d1c32]" />
            </div>
            <div>
              <label class="block text-[#8a5d6c] mb-1">Tag Kategori</label>
              <input id="input-memory-tag" type="text" placeholder="Momen Manis" class="w-full p-2.5 rounded-xl border border-[#ffe1e9] bg-white text-[#5d1c32]" />
            </div>
          </div>
          <div>
            <label class="block text-[#8a5d6c] mb-1">Catatan Kenangan</label>
            <textarea id="input-memory-note" rows="2" placeholder="Ceritakan kisah manis di balik foto ini..." class="w-full p-2.5 rounded-xl border border-[#ffe1e9] bg-white text-[#5d1c32]"></textarea>
          </div>
          <button type="submit" class="w-full py-2.5 rounded-xl bg-[#5d1c32] text-white font-medium">
            Simpan Kenangan Baru 📸
          </button>
        </form>
      </div>
    </div>

    <!-- Add Wish Modal -->
    <div id="add-wish-modal" class="fixed inset-0 z-50 hidden bg-black/75 backdrop-blur-sm p-4 flex items-center justify-center">
      <div class="relative max-w-md w-full bg-[#fffafb] rounded-3xl p-6 shadow-2xl border border-[#ffe1e9]">
        <button id="btn-close-add-wish" type="button" class="absolute top-4 right-4 p-2 text-[#8a5d6c]">✕</button>
        <h3 class="font-serif-elegant text-xl text-[#5d1c32] font-bold mb-3">Titip Pesan & Doa untuk Della</h3>
        <form id="form-add-wish" class="space-y-3 text-xs">
          <div>
            <label class="block text-[#8a5d6c] mb-1">Nama Pengirim</label>
            <input id="input-wish-sender" type="text" required placeholder="Nama Anda" class="w-full p-2.5 rounded-xl border border-[#ffe1e9] bg-white text-[#5d1c32]" />
          </div>
          <div class="grid grid-cols-2 gap-2">
            <div>
              <label class="block text-[#8a5d6c] mb-1">Hubungan / Peran</label>
              <input id="input-wish-role" type="text" placeholder="Sahabat / Bestie" class="w-full p-2.5 rounded-xl border border-[#ffe1e9] bg-white text-[#5d1c32]" />
            </div>
            <div>
              <label class="block text-[#8a5d6c] mb-1">Emoji Cap</label>
              <input id="input-wish-emoji" type="text" placeholder="🌸" class="w-full p-2.5 rounded-xl border border-[#ffe1e9] bg-white text-[#5d1c32]" />
            </div>
          </div>
          <div>
            <label class="block text-[#8a5d6c] mb-1">Petunjuk Rahasia (Hint)</label>
            <input id="input-wish-hint" type="text" placeholder="Contoh: Pesan dari teman sebangkumu" class="w-full p-2.5 rounded-xl border border-[#ffe1e9] bg-white text-[#5d1c32]" />
          </div>
          <div>
            <label class="block text-[#8a5d6c] mb-1">Isi Doa & Ucapan</label>
            <textarea id="input-wish-message" rows="3" required placeholder="Tuliskan ucapan selamat ulang tahun ke-21..." class="w-full p-2.5 rounded-xl border border-[#ffe1e9] bg-white text-[#5d1c32]"></textarea>
          </div>
          <button type="submit" class="w-full py-2.5 rounded-xl bg-[#5d1c32] text-white font-medium">
            Kirim Amplop Rahasia 💌
          </button>
        </form>
      </div>
    </div>

    <!-- Confetti Library (Vendored, no build step) -->
    <script src="assets/js/vendor/canvas-confetti.min.js"></script>

    <!-- Romantic Splash Screen Controller -->
    <script src="assets/js/splash.js"></script>

    <!-- Main JS entrypoint -->
    <script type="module" src="assets/js/main.js"></script>

    <?php endif; ?>

  </body>
</html>
