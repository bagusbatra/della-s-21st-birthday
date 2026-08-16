import { romanticSynth } from './audio.js';
import { triggerRomanticConfetti, triggerFullscreenConfettiExplosion } from './confetti.js';
import { initPetalCanvas } from './petals.js';
import { initBirthdayCake } from './cake.js';
import { initGallery } from './gallery.js';
import { initSecretWishes } from './wishes.js';
import { initLoveLetter } from './letter.js';

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

  // 6. Background Music Controller & Navbar Music Button
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

  // 10. Back to Top Button
  const btnBackToTop = document.getElementById('btn-back-to-top');
  if (btnBackToTop) {
    btnBackToTop.addEventListener('click', () => {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }

});
