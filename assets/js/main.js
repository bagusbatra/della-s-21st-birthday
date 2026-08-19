import { romanticSynth } from './audio.js';
import { triggerRomanticConfetti, triggerFullscreenConfettiExplosion } from './confetti.js';
import { initPetalCanvas } from './petals.js';
import { initBirthdayCake } from './cake.js';
import { initGallery } from './gallery.js';
import { initSecretWishes } from './wishes.js';
import { initLoveLetter } from './letter.js';
import { initScrollReveal } from './scroll-reveal.js';
import { icon } from './icons.js';

document.addEventListener('DOMContentLoaded', () => {
  // 1. Falling Rose Petals
  initPetalCanvas();

  // 2. 21 Candles Birthday Cake
  initBirthdayCake();

  // 3. Polaroid Photo Gallery
  initGallery();

  // 4. Secret Wishes Envelopes
  initSecretWishes();

  // 5. Love Letter Modal
  const loveLetter = initLoveLetter();

  // 5b. Animasi masuk/keluar saat scroll (semua elemen .reveal)
  initScrollReveal();

  // 6. Background Music — autoplay + loop the real audio file, with a
  // fallback that resumes playback on the first user interaction if the
  // browser's autoplay policy blocked the initial attempt.
  const bgMusic = document.getElementById('bg-music');
  const btnNavMusic = document.getElementById('btn-nav-music');
  const btnToggleFloatingMusic = document.getElementById('btn-toggle-floating-music');
  const musicPlayingIcon = document.getElementById('music-playing-indicator');

  function updateMusicUI(isPlaying) {
    if (btnNavMusic) {
      btnNavMusic.className = isPlaying
        ? 'p-2 rounded-full border border-[#ffe1e9] bg-[#5d1c32] text-white shadow-xs transition-all active:scale-95'
        : 'p-2 rounded-full border border-[#ffe1e9] bg-white/80 text-[#5d1c32] hover:bg-[#fdf2f8] transition-all active:scale-95';
    }

    if (btnToggleFloatingMusic) {
      btnToggleFloatingMusic.innerHTML = icon(isPlaying ? 'pause' : 'play', 'icon icon-sm');
    }

    if (musicPlayingIcon) {
      musicPlayingIcon.className = isPlaying ? 'animate-spin' : '';
    }
  }

  if (bgMusic) {
    bgMusic.volume = 0.5;

    const resumeOnInteraction = () => {
      bgMusic.play().catch(() => {});
    };

    bgMusic.play().catch(() => {
      ['click', 'touchstart', 'keydown'].forEach((evt) => {
        document.addEventListener(evt, resumeOnInteraction, { once: true });
      });
    });

    bgMusic.addEventListener('play', () => updateMusicUI(true));
    bgMusic.addEventListener('pause', () => updateMusicUI(false));

    const toggleMusic = () => {
      if (bgMusic.paused) {
        bgMusic.play().catch(() => {});
      } else {
        bgMusic.pause();
      }
    };

    if (btnNavMusic) btnNavMusic.addEventListener('click', toggleMusic);
    if (btnToggleFloatingMusic) btnToggleFloatingMusic.addEventListener('click', toggleMusic);
  }

  // 7. Mobile Hamburger Menu
  const btnMobileMenu = document.getElementById('btn-mobile-menu');
  const mobileNavMenu = document.getElementById('mobile-nav-menu');

  if (btnMobileMenu && mobileNavMenu) {
    btnMobileMenu.addEventListener('click', () => {
      mobileNavMenu.classList.toggle('hidden');
    });

    mobileNavMenu.querySelectorAll('a').forEach(link => {
      link.addEventListener('click', () => {
        mobileNavMenu.classList.add('hidden');
      });
    });
  }

  // 8. Hero Confetti & Action Buttons
  const btnHeroConfetti = document.getElementById('btn-hero-confetti');
  if (btnHeroConfetti) {
    btnHeroConfetti.addEventListener('click', () => {
      romanticSynth.playCelebrationChime();
      triggerFullscreenConfettiExplosion();
    });
  }

  // 9. Share & Copy Link Button
  const btnShareApp = document.getElementById('btn-share-app');
  if (btnShareApp) {
    btnShareApp.addEventListener('click', async () => {
      try {
        await navigator.clipboard.writeText(window.location.href);
        btnShareApp.innerHTML = `<span>Tautan Tersalin!</span> ${icon('heart', 'icon icon-sm')}`;
        triggerRomanticConfetti();
        setTimeout(() => {
          btnShareApp.innerHTML = `<span>Bagikan Momen Ini</span> ${icon('share-2', 'icon icon-sm')}`;
        }, 2500);
      } catch (e) {
        // ignore
      }
    });
  }

  // 10. Back to Top Button
  const btnBackToTop = document.getElementById('btn-back-to-top');
  if (btnBackToTop) {
    btnBackToTop.addEventListener('click', () => {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }

  // 11. Admin Preview Banner spacing — the banner wraps to 2 lines on
  // narrow screens, so its height is measured live instead of assumed fixed.
  const previewBanner = document.querySelector('.admin-preview-banner');
  const mainHeader = document.getElementById('main-header');
  const mainContent = document.querySelector('main');
  if (previewBanner && mainHeader) {
    const adjustForPreviewBanner = () => {
      const bannerHeight = previewBanner.offsetHeight;
      mainHeader.style.top = `${bannerHeight}px`;
      if (mainContent) {
        mainContent.style.paddingTop = `${bannerHeight + mainHeader.offsetHeight}px`;
      }
    };
    adjustForPreviewBanner();
    window.addEventListener('resize', adjustForPreviewBanner);
  }

});
