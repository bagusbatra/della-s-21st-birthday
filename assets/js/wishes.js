import { INITIAL_SECRET_WISHES } from './data.js';
import { triggerHeartBurst, triggerRomanticConfetti } from './confetti.js';
import { romanticSynth } from './audio.js';

export function initSecretWishes() {
  const saved = localStorage.getItem('della_secret_wishes');
  let wishes = saved ? JSON.parse(saved) : [...INITIAL_SECRET_WISHES];
  let filterState = 'all'; // 'all', 'unopened', 'opened'

  const wishesGrid = document.getElementById('wishes-grid');
  const btnFilterAll = document.getElementById('filter-wishes-all');
  const btnFilterUnopened = document.getElementById('filter-wishes-unopened');
  const btnFilterOpened = document.getElementById('filter-wishes-opened');
  const btnOpenAddWish = document.getElementById('btn-open-add-wish');
  const addWishModal = document.getElementById('add-wish-modal');
  const btnCloseAddWish = document.getElementById('btn-close-add-wish');
  const formAddWish = document.getElementById('form-add-wish');

  function saveWishes() {
    localStorage.setItem('della_secret_wishes', JSON.stringify(wishes));
  }

  function renderWishes() {
    if (!wishesGrid) return;
    wishesGrid.innerHTML = '';

    const filtered = wishes.filter(w => {
      if (filterState === 'unopened') return !w.isOpened;
      if (filterState === 'opened') return w.isOpened;
      return true;
    });

    filtered.forEach(wish => {
      const card = document.createElement('div');
      card.className = `rounded-3xl p-5 sm:p-6 transition-all duration-300 border flex flex-col justify-between ${
        wish.isOpened
          ? 'bg-white border-[#ffe1e9] shadow-xs'
          : 'bg-gradient-to-br from-[#fffafb] to-[#fce7f3] border-[#ffc2d1] shadow-sm hover:shadow-md cursor-pointer'
      }`;

      if (!wish.isOpened) {
        // Closed Sealed Envelope View
        card.innerHTML = `
          <div class="text-center py-4">
            <div class="w-16 h-16 mx-auto rounded-full bg-[#5d1c32] text-[#ffc2d1] flex items-center justify-center text-2xl shadow-md mb-3 animate-pulse border-2 border-[#ffe1e9]">
              ${wish.avatarEmoji || '💌'}
            </div>
            <span class="inline-block px-3 py-0.5 rounded-full bg-white text-[#5d1c32] text-[11px] font-semibold border border-[#ffe1e9] mb-1">
              ${wish.role || 'Sahabat'}
            </span>
            <h4 class="font-serif text-[#5d1c32] font-semibold text-base sm:text-lg mb-1">
              Dari: ${wish.sender}
            </h4>
            <p class="text-xs text-[#8a5d6c] italic mb-4">
              "${wish.hint || 'Pesan rahasia spesial untuk Della'}"
            </p>
            <button class="btn-open-envelope px-5 py-2 rounded-full bg-[#5d1c32] text-white text-xs font-semibold hover:bg-[#481426] transition-all shadow-xs inline-flex items-center gap-1.5">
              <span>Buka Segel Amplop ✉️</span>
            </button>
          </div>
          <div class="pt-2 text-center text-[10px] text-[#8a5d6c]">
            🕒 ${wish.timestamp}
          </div>
        `;

        const openBtn = card.querySelector('.btn-open-envelope');
        const handleOpen = () => {
          wish.isOpened = true;
          saveWishes();
          romanticSynth.playEnvelopeOpenSound();
          triggerRomanticConfetti();
          renderWishes();
        };

        card.addEventListener('click', handleOpen);
        if (openBtn) openBtn.addEventListener('click', (e) => { e.stopPropagation(); handleOpen(); });
      } else {
        // Opened Letter View
        card.innerHTML = `
          <div>
            <div class="flex items-center justify-between pb-3 mb-3 border-b border-[#ffe1e9]">
              <div class="flex items-center gap-2.5">
                <span class="w-9 h-9 rounded-full bg-[#fce7f3] text-[#5d1c32] flex items-center justify-center text-lg border border-[#ffc2d1]">
                  ${wish.avatarEmoji || '🌸'}
                </span>
                <div>
                  <h4 class="font-serif text-[#5d1c32] font-bold text-sm sm:text-base leading-tight">${wish.sender}</h4>
                  <span class="text-[11px] text-[#a44a66] font-medium">${wish.role || 'Sahabat'}</span>
                </div>
              </div>
              <span class="text-xs text-[#8a5d6c]">💌 Terbuka</span>
            </div>
            <p class="font-cormorant italic text-base sm:text-lg text-[#5d1c32] leading-relaxed mb-4">
              "${wish.message}"
            </p>
          </div>
          <div class="pt-3 border-t border-[#ffe1e9]/60 flex items-center justify-between text-xs text-[#8a5d6c]">
            <span>🕒 ${wish.timestamp}</span>
            <button class="btn-like-wish flex items-center gap-1 px-2.5 py-1 rounded-full bg-[#fffafb] hover:bg-[#fce7f3] border border-[#ffe1e9] text-[#5d1c32] transition-colors" data-id="${wish.id}">
              <span class="text-rose-500">❤️</span>
              <span class="font-semibold text-xs">${wish.likes || 0}</span>
            </button>
          </div>
        `;

        const likeBtn = card.querySelector('.btn-like-wish');
        if (likeBtn) {
          likeBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            wish.likes = (wish.likes || 0) + 1;
            saveWishes();
            renderWishes();
            triggerHeartBurst();
          });
        }
      }

      wishesGrid.appendChild(card);
    });
  }

  function setFilter(filter) {
    filterState = filter;
    [btnFilterAll, btnFilterUnopened, btnFilterOpened].forEach(b => {
      if (b) {
        b.className = 'px-3 py-1.5 rounded-full text-xs font-medium bg-white text-[#8a5d6c] border border-[#ffe1e9] hover:bg-[#fdf2f8] transition-all';
      }
    });

    if (filter === 'all' && btnFilterAll) {
      btnFilterAll.className = 'px-3 py-1.5 rounded-full text-xs font-medium bg-[#5d1c32] text-white shadow-xs';
    } else if (filter === 'unopened' && btnFilterUnopened) {
      btnFilterUnopened.className = 'px-3 py-1.5 rounded-full text-xs font-medium bg-[#5d1c32] text-white shadow-xs';
    } else if (filter === 'opened' && btnFilterOpened) {
      btnFilterOpened.className = 'px-3 py-1.5 rounded-full text-xs font-medium bg-[#5d1c32] text-white shadow-xs';
    }

    renderWishes();
  }

  if (btnFilterAll) btnFilterAll.addEventListener('click', () => setFilter('all'));
  if (btnFilterUnopened) btnFilterUnopened.addEventListener('click', () => setFilter('unopened'));
  if (btnFilterOpened) btnFilterOpened.addEventListener('click', () => setFilter('opened'));

  if (btnOpenAddWish) {
    btnOpenAddWish.addEventListener('click', () => {
      if (addWishModal) addWishModal.classList.remove('hidden');
    });
  }

  if (btnCloseAddWish) {
    btnCloseAddWish.addEventListener('click', () => {
      if (addWishModal) addWishModal.classList.add('hidden');
    });
  }

  if (formAddWish) {
    formAddWish.addEventListener('submit', (e) => {
      e.preventDefault();
      const senderInput = document.getElementById('input-wish-sender');
      const roleInput = document.getElementById('input-wish-role');
      const emojiInput = document.getElementById('input-wish-emoji');
      const messageInput = document.getElementById('input-wish-message');
      const hintInput = document.getElementById('input-wish-hint');

      const newWish = {
        id: 'w_' + Date.now(),
        sender: senderInput?.value || 'Sahabat Baik',
        role: roleInput?.value || 'Teman Tersayang',
        avatarEmoji: emojiInput?.value || '✨',
        envelopeColor: 'rose',
        message: messageInput?.value || 'Selamat ulang tahun ke-21 Della! Sukses dan bahagia selalu!',
        timestamp: 'Baru saja',
        hint: hintInput?.value || 'Pesan doa hangat dari sahabat',
        isOpened: false,
        likes: 1
      };

      wishes.unshift(newWish);
      saveWishes();
      renderWishes();
      triggerRomanticConfetti();

      if (addWishModal) addWishModal.classList.add('hidden');
      formAddWish.reset();
    });
  }

  renderWishes();
}
