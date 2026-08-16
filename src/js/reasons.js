import { TWENTY_ONE_REASONS } from './data.js';
import { triggerHeartBurst, triggerRomanticConfetti } from './confetti.js';

export function initReasons() {
  const savedRead = localStorage.getItem('della_read_reasons');
  let readSet = new Set(savedRead ? JSON.parse(savedRead) : []);
  let expandedAll = false;

  const reasonsGrid = document.getElementById('reasons-grid');
  const readCounterBadge = document.getElementById('reasons-read-counter');
  const btnOpenAllReasons = document.getElementById('btn-open-all-reasons');

  function saveRead() {
    localStorage.setItem('della_read_reasons', JSON.stringify(Array.from(readSet)));
  }

  function updateBadge() {
    if (readCounterBadge) {
      readCounterBadge.textContent = `${readSet.size} dari 21 Telah Terbuka 💖`;
    }
  }

  function renderReasons() {
    if (!reasonsGrid) return;
    reasonsGrid.innerHTML = '';

    TWENTY_ONE_REASONS.forEach(item => {
      const isRead = readSet.has(item.id);
      const card = document.createElement('div');
      card.className = `p-4 sm:p-5 rounded-2xl border transition-all duration-300 cursor-pointer text-left ${
        isRead
          ? 'bg-white border-[#ffc2d1] shadow-xs'
          : 'bg-[#fffafb] border-[#ffe1e9] hover:bg-white hover:border-[#ffc2d1]'
      }`;

      card.innerHTML = `
        <div class="flex items-start justify-between gap-3 mb-2">
          <div class="flex items-center gap-2.5">
            <span class="w-7 h-7 rounded-lg bg-[#5d1c32] text-white text-xs font-bold flex items-center justify-center shadow-xs">
              ${item.id}
            </span>
            <span class="text-xl">${item.icon || '🌸'}</span>
          </div>
          <span class="text-xs px-2 py-0.5 rounded-full font-medium ${
            isRead
              ? 'bg-rose-50 text-[#a44a66]'
              : 'bg-[#fce7f3] text-[#5d1c32]'
          }">
            ${isRead ? 'Tersimpan di Hati ❤️' : 'Buka Alasan'}
          </span>
        </div>
        <h4 class="font-serif text-[#5d1c32] font-semibold text-sm sm:text-base mb-1">
          ${item.title}
        </h4>
        <p class="text-xs text-[#8a5d6c] leading-relaxed">
          ${item.description}
        </p>
      `;

      card.addEventListener('click', () => {
        if (!readSet.has(item.id)) {
          readSet.add(item.id);
          saveRead();
          updateBadge();
          renderReasons();
          triggerHeartBurst();
          if (readSet.size === 21) {
            triggerRomanticConfetti();
          }
        }
      });

      reasonsGrid.appendChild(card);
    });

    updateBadge();
  }

  if (btnOpenAllReasons) {
    btnOpenAllReasons.addEventListener('click', () => {
      TWENTY_ONE_REASONS.forEach(r => readSet.add(r.id));
      saveRead();
      renderReasons();
      triggerRomanticConfetti();
    });
  }

  renderReasons();
}
