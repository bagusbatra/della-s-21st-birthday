import './style.css';
import { romanticSynth } from './js/audio.js';
import { triggerRomanticConfetti, triggerFullscreenConfettiExplosion } from './js/confetti.js';
import { initPetalCanvas } from './js/petals.js';
import { initCountdown } from './js/countdown.js';
import { initBirthdayCake } from './js/cake.js';
import { initGallery } from './js/gallery.js';
import { initReasons } from './js/reasons.js';
import { initSecretWishes } from './js/wishes.js';
import { initLoveLetter } from './js/letter.js';

document.addEventListener('DOMContentLoaded', () => {
  // 1. Falling Rose Petals
  initPetalCanvas();

  // 2. Countdown Timer
  initCountdown();

  // 3. 21 Candles Birthday Cake
  initBirthdayCake();

  // 4. Polaroid Photo Gallery
  initGallery();

  // 5. 21 Reasons
  initReasons();

  // 6. Secret Wishes Envelopes
  initSecretWishes();

  // 7. Love Letter Modal
  const loveLetter = initLoveLetter();

  // 8. Background Music Controller & Navbar Music Button
  const btnNavMusic = document.getElementById('btn-nav-music');
  const musicFloatingBar = document.getElementById('music-floating-bar');
  const btnToggleFloatingMusic = document.getElementById('btn-toggle-floating-music');
  const btnNextTrack = document.getElementById('btn-next-track');
  const btnPrevTrack = document.getElementById('btn-prev-track');
  const currentTrackTitle = document.getElementById('current-track-title');
  const currentTrackMood = document.getElementById('current-track-mood');
  const volumeSlider = document.getElementById('volume-slider');
  const musicPlayingIcon = document.getElementById('music-playing-indicator');

  function updateMusicUI(isPlaying) {
    if (btnNavMusic) {
      if (isPlaying) {
        btnNavMusic.className = 'p-2 rounded-full border border-[#ffe1e9] bg-[#5d1c32] text-white shadow-xs transition-all active:scale-95';
      } else {
        btnNavMusic.className = 'p-2 rounded-full border border-[#ffe1e9] bg-white/80 text-[#5d1c32] hover:bg-[#fdf2f8] transition-all active:scale-95';
      }
    }

    if (btnToggleFloatingMusic) {
      btnToggleFloatingMusic.textContent = isPlaying ? '⏸️' : '▶️';
    }

    if (musicPlayingIcon) {
      musicPlayingIcon.className = isPlaying ? 'animate-spin' : '';
    }

    const currentTrack = romanticSynth.getCurrentTrack();
    if (currentTrackTitle && currentTrack) {
      currentTrackTitle.textContent = currentTrack.title;
    }
    if (currentTrackMood && currentTrack) {
      currentTrackMood.textContent = currentTrack.mood;
    }
  }

  romanticSynth.subscribe((isPlaying) => {
    updateMusicUI(isPlaying);
  });

  if (btnNavMusic) {
    btnNavMusic.addEventListener('click', () => {
      romanticSynth.toggle();
    });
  }

  if (btnToggleFloatingMusic) {
    btnToggleFloatingMusic.addEventListener('click', () => {
      romanticSynth.toggle();
    });
  }

  if (btnNextTrack) {
    btnNextTrack.addEventListener('click', () => {
      romanticSynth.setTrack(romanticSynth.currentTrackIndex + 1);
      updateMusicUI(romanticSynth.getIsPlaying());
    });
  }

  if (btnPrevTrack) {
    btnPrevTrack.addEventListener('click', () => {
      romanticSynth.setTrack(romanticSynth.currentTrackIndex - 1);
      updateMusicUI(romanticSynth.getIsPlaying());
    });
  }

  if (volumeSlider) {
    volumeSlider.addEventListener('input', (e) => {
      romanticSynth.setVolume(parseFloat(e.target.value));
    });
  }

  // 9. Mobile Hamburger Menu
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

  // 10. Hero Confetti & Action Buttons
  const btnHeroConfetti = document.getElementById('btn-hero-confetti');
  if (btnHeroConfetti) {
    btnHeroConfetti.addEventListener('click', () => {
      romanticSynth.playCelebrationChime();
      triggerFullscreenConfettiExplosion();
    });
  }

  // 11. Share & Copy Link Button
  const btnShareApp = document.getElementById('btn-share-app');
  if (btnShareApp) {
    btnShareApp.addEventListener('click', async () => {
      try {
        await navigator.clipboard.writeText(window.location.href);
        btnShareApp.innerHTML = '<span>Tautan Tersalin! ❤️</span>';
        triggerRomanticConfetti();
        setTimeout(() => {
          btnShareApp.innerHTML = '<span>Bagikan Momen Ini 🔗</span>';
        }, 2500);
      } catch (e) {
        // ignore
      }
    });
  }

  // 12. Back to Top Button
  const btnBackToTop = document.getElementById('btn-back-to-top');
  if (btnBackToTop) {
    btnBackToTop.addEventListener('click', () => {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }

  // 13. Export HTML Modal
  const exportModal = document.getElementById('export-html-modal');
  const btnOpenExportNav = document.getElementById('btn-nav-export-html');
  const btnOpenExportFooter = document.getElementById('btn-footer-export-html');
  const btnCloseExport = document.getElementById('btn-close-export');
  const btnDownloadHtml = document.getElementById('btn-download-html-file');

  function openExportModal() {
    if (exportModal) exportModal.classList.remove('hidden');
  }

  function closeExportModal() {
    if (exportModal) exportModal.classList.add('hidden');
  }

  if (btnOpenExportNav) btnOpenExportNav.addEventListener('click', openExportModal);
  if (btnOpenExportFooter) btnOpenExportFooter.addEventListener('click', openExportModal);
  if (btnCloseExport) btnCloseExport.addEventListener('click', closeExportModal);

  if (btnDownloadHtml) {
    btnDownloadHtml.addEventListener('click', async () => {
      try {
        const res = await fetch('/della_21st_birthday.html');
        const text = await res.text();
        const blob = new Blob([text], { type: 'text/html;charset=utf-8' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'della_puspa_ardiati_21st_birthday.html';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
      } catch (e) {
        window.open('/della_21st_birthday.html', '_blank');
      }
    });
  }
});
