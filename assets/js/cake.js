import { triggerFullscreenConfettiExplosion, triggerRomanticConfetti } from './confetti.js';
import { romanticSynth } from './audio.js';
import { icon } from './icons.js';

export function initBirthdayCake() {
  let candles = Array(21).fill(true);
  const candlesContainer = document.getElementById('candles-container');
  const candleStatusBadge = document.getElementById('candle-status-badge');
  const btnBlowAll = document.getElementById('btn-blow-all-candles');
  const btnRelight = document.getElementById('btn-relight-candles');
  const btnMakeWish = document.getElementById('btn-open-make-wish');
  const wishModal = document.getElementById('wish-modal');
  const btnCloseWish = document.getElementById('btn-close-wish');
  const formWish = document.getElementById('form-make-wish');
  const wishSentAlert = document.getElementById('wish-sent-alert');
  const wishErrorAlert = document.getElementById('wish-error-alert');
  const inputWishText = document.getElementById('input-wish-text');

  function renderCandles(justBlownOut = new Set()) {
    if (!candlesContainer) return;
    candlesContainer.innerHTML = '';

    candles.forEach((isLit, index) => {
      const candleBtn = document.createElement('button');
      candleBtn.type = 'button';
      candleBtn.className = 'group flex flex-col items-center cursor-pointer transition-transform hover:-translate-y-1 focus:outline-none';
      candleBtn.title = `Lilin ke-${index + 1} (${isLit ? 'Menyala' : 'Padam'})`;

      const flameWrap = document.createElement('span');
      flameWrap.className = 'relative flex items-center justify-center w-2.5 h-3.5';

      const isBeingBlownOut = !isLit && justBlownOut.has(index);

      const flame = document.createElement('span');
      flame.className = `block w-2.5 h-3.5 rounded-full ${
        isLit
          ? 'bg-gradient-to-t from-amber-500 via-yellow-300 to-white shadow-[0_0_8px_#f59e0b] opacity-100 scale-100 candle-flame-flicker'
          : isBeingBlownOut
            ? 'bg-gradient-to-t from-amber-500 via-yellow-300 to-white shadow-[0_0_8px_#f59e0b] opacity-0 candle-flame-blowout'
            : 'bg-stone-300 opacity-0 scale-50'
      }`;
      flameWrap.appendChild(flame);

      if (isBeingBlownOut) {
        const smoke = document.createElement('span');
        smoke.className = 'candle-smoke';
        flameWrap.appendChild(smoke);
      }

      const wick = document.createElement('span');
      wick.className = 'w-0.5 h-1.5 bg-stone-600';

      const body = document.createElement('span');
      body.className = `w-3.5 sm:w-4 h-10 sm:h-12 rounded-t-sm border border-black/10 flex items-center justify-center text-[9px] font-bold transition-colors ${
        isLit
          ? 'bg-[#5d1c32] text-white shadow-xs'
          : 'bg-stone-200 text-stone-400'
      }`;
      body.textContent = index + 1;

      candleBtn.appendChild(flameWrap);
      candleBtn.appendChild(wick);
      candleBtn.appendChild(body);

      candleBtn.addEventListener('click', () => {
        const wasLit = candles[index];
        candles[index] = !candles[index];
        romanticSynth.playPianoTone(600 + index * 20, 0.2, 0.4, 'sine');
        renderCandles(wasLit && !candles[index] ? new Set([index]) : undefined);

        if (candles.every(c => !c)) {
          triggerFullscreenConfettiExplosion();
          romanticSynth.playCelebrationChime();
          if (wishModal) wishModal.classList.remove('hidden');
        }
      });

      candlesContainer.appendChild(candleBtn);
    });

    const activeCount = candles.filter(Boolean).length;
    if (candleStatusBadge) {
      if (activeCount === 0) {
        candleStatusBadge.innerHTML = `${icon('sparkles', 'icon icon-sm')} Semua 21 lilin telah ditiup! Harapanmu melayang ke semesta ${icon('sparkles', 'icon icon-sm')}`;
        candleStatusBadge.className = 'inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200 text-xs font-semibold';
      } else {
        candleStatusBadge.innerHTML = `${activeCount} dari 21 Lilin Masih Menyala ${icon('flame', 'icon icon-sm')}`;
        candleStatusBadge.className = 'inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full bg-[#fce7f3] text-[#5d1c32] border border-[#ffc2d1] text-xs font-semibold';
      }
    }
  }

  if (btnBlowAll) {
    btnBlowAll.addEventListener('click', () => {
      const justBlownOut = new Set(
        candles.reduce((acc, isLit, index) => {
          if (isLit) acc.push(index);
          return acc;
        }, [])
      );
      candles = Array(21).fill(false);
      renderCandles(justBlownOut);
      triggerFullscreenConfettiExplosion();
      romanticSynth.playCelebrationChime();
      setTimeout(() => {
        if (wishModal) wishModal.classList.remove('hidden');
      }, 500);
    });
  }

  if (btnRelight) {
    btnRelight.addEventListener('click', () => {
      candles = Array(21).fill(true);
      renderCandles();
      romanticSynth.playCelebrationChime();
    });
  }

  if (btnMakeWish) {
    btnMakeWish.addEventListener('click', () => {
      if (wishModal) wishModal.classList.remove('hidden');
    });
  }

  if (btnCloseWish) {
    btnCloseWish.addEventListener('click', () => {
      if (wishModal) wishModal.classList.add('hidden');
    });
  }

  if (formWish) {
    formWish.addEventListener('submit', async (e) => {
      e.preventDefault();
      const wishText = inputWishText ? inputWishText.value.trim() : '';
      if (!wishText) return;

      if (wishErrorAlert) wishErrorAlert.classList.add('hidden');

      const submitBtn = formWish.querySelector('button[type="submit"]');
      if (submitBtn) submitBtn.disabled = true;

      try {
        const response = await fetch('wish-submit', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: 'wish_text=' + encodeURIComponent(wishText),
        });
        const data = await response.json();

        if (data.ok) {
          if (wishSentAlert) {
            wishSentAlert.classList.remove('hidden');
            triggerRomanticConfetti();
            if (inputWishText) inputWishText.value = '';
            setTimeout(() => {
              wishSentAlert.classList.add('hidden');
              if (wishModal) wishModal.classList.add('hidden');
            }, 2200);
          }
        } else if (wishErrorAlert) {
          wishErrorAlert.textContent = data.error || 'Gagal mengirim harapan, coba lagi ya.';
          wishErrorAlert.classList.remove('hidden');
        }
      } catch (err) {
        if (wishErrorAlert) {
          wishErrorAlert.textContent = 'Gagal mengirim harapan, coba lagi ya.';
          wishErrorAlert.classList.remove('hidden');
        }
      } finally {
        if (submitBtn) submitBtn.disabled = false;
      }
    });
  }

  renderCandles();
}
