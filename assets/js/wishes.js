import { triggerHeartBurst, triggerRomanticConfetti } from './confetti.js';
import { romanticSynth } from './audio.js';

export function initSecretWishes() {
  const wishesGrid = document.getElementById('wishes-grid');
  const btnFilterAll = document.getElementById('filter-wishes-all');
  const btnFilterUnopened = document.getElementById('filter-wishes-unopened');
  const btnFilterOpened = document.getElementById('filter-wishes-opened');

  if (!wishesGrid) return;

  const cards = Array.from(wishesGrid.querySelectorAll('.wish-card'));
  let filterState = 'all';

  function applyFilter() {
    cards.forEach((card) => {
      const matches = filterState === 'all' || card.dataset.state === filterState;
      card.classList.toggle('hidden', !matches);
    });
  }

  function setFilter(filter) {
    filterState = filter;
    [btnFilterAll, btnFilterUnopened, btnFilterOpened].forEach((b) => {
      if (b) {
        b.className = 'px-3 py-1.5 rounded-full text-xs font-medium bg-white text-[#8a5d6c] border border-[#ffe1e9] hover:bg-[#fdf2f8] transition-all';
      }
    });

    const activeBtn = filter === 'all' ? btnFilterAll : filter === 'unopened' ? btnFilterUnopened : btnFilterOpened;
    if (activeBtn) {
      activeBtn.className = 'px-3 py-1.5 rounded-full text-xs font-medium bg-[#5d1c32] text-white shadow-xs';
    }

    applyFilter();
  }

  if (btnFilterAll) btnFilterAll.addEventListener('click', () => setFilter('all'));
  if (btnFilterUnopened) btnFilterUnopened.addEventListener('click', () => setFilter('unopened'));
  if (btnFilterOpened) btnFilterOpened.addEventListener('click', () => setFilter('opened'));

  cards.forEach((card) => {
    const sealedView = card.querySelector('.wish-sealed-view');
    const openedView = card.querySelector('.wish-opened-view');
    const openBtn = card.querySelector('.btn-open-envelope');
    const likeBtn = card.querySelector('.btn-like-wish');
    const likeCountEl = likeBtn ? likeBtn.querySelector('.like-count') : null;

    function openEnvelope() {
      if (card.dataset.state === 'opened') return;
      card.dataset.state = 'opened';

      card.classList.remove('bg-gradient-to-br', 'from-[#fffafb]', 'to-[#fce7f3]', 'border-[#ffc2d1]', 'shadow-sm', 'hover:shadow-md', 'cursor-pointer');
      card.classList.add('bg-white', 'border-[#ffe1e9]', 'shadow-xs');

      if (sealedView) sealedView.classList.add('hidden');
      if (openedView) openedView.classList.remove('hidden');

      romanticSynth.playEnvelopeOpenSound();
      triggerRomanticConfetti();
      applyFilter();
    }

    card.addEventListener('click', openEnvelope);

    if (openBtn) {
      openBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        openEnvelope();
      });
    }

    if (likeBtn) {
      likeBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        const current = parseInt(likeCountEl ? likeCountEl.textContent : '0', 10) || 0;
        const next = current + 1;
        if (likeCountEl) likeCountEl.textContent = String(next);
        triggerHeartBurst();
      });
    }
  });
}
