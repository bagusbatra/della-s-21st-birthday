import { INITIAL_MEMORIES } from './data.js';
import { triggerHeartBurst, triggerRomanticConfetti } from './confetti.js';

export function initGallery() {
  const saved = localStorage.getItem('della_memories');
  let memories = saved ? JSON.parse(saved) : [...INITIAL_MEMORIES];
  let selectedTag = 'Semua';

  const galleryGrid = document.getElementById('gallery-grid');
  const tagFilterContainer = document.getElementById('gallery-tag-filters');
  const btnOpenAddMemory = document.getElementById('btn-open-add-memory');
  const addMemoryModal = document.getElementById('add-memory-modal');
  const btnCloseAddMemory = document.getElementById('btn-close-add-memory');
  const formAddMemory = document.getElementById('form-add-memory');

  // Lightbox elements
  const lightboxModal = document.getElementById('lightbox-modal');
  const lightboxImg = document.getElementById('lightbox-img');
  const lightboxCaption = document.getElementById('lightbox-caption');
  const lightboxDate = document.getElementById('lightbox-date');
  const lightboxTag = document.getElementById('lightbox-tag');
  const lightboxNote = document.getElementById('lightbox-note');
  const lightboxLikeBtn = document.getElementById('lightbox-like-btn');
  const lightboxLikeCount = document.getElementById('lightbox-like-count');
  const btnCloseLightbox = document.getElementById('btn-close-lightbox');

  let currentLightboxId = null;

  function saveMemories() {
    localStorage.setItem('della_memories', JSON.stringify(memories));
  }

  function getUniqueTags() {
    const tags = new Set(['Semua']);
    memories.forEach(m => {
      if (m.tag) tags.add(m.tag);
    });
    return Array.from(tags);
  }

  function renderTags() {
    if (!tagFilterContainer) return;
    tagFilterContainer.innerHTML = '';
    const tags = getUniqueTags();

    tags.forEach(tag => {
      const btn = document.createElement('button');
      btn.type = 'button';
      btn.textContent = tag;
      btn.className = `px-3 py-1.5 rounded-full text-xs font-medium transition-all ${
        selectedTag === tag
          ? 'bg-[#5d1c32] text-white shadow-xs'
          : 'bg-white text-[#8a5d6c] border border-[#ffe1e9] hover:bg-[#fdf2f8]'
      }`;
      btn.addEventListener('click', () => {
        selectedTag = tag;
        renderTags();
        renderGallery();
      });
      tagFilterContainer.appendChild(btn);
    });
  }

  function renderGallery() {
    if (!galleryGrid) return;
    galleryGrid.innerHTML = '';

    const filtered = selectedTag === 'Semua'
      ? memories
      : memories.filter(m => m.tag === selectedTag);

    filtered.forEach(memory => {
      const card = document.createElement('div');
      card.className = 'bg-white p-3.5 sm:p-4 rounded-2xl shadow-sm hover:shadow-md border border-[#ffe1e9] transition-all duration-300 group flex flex-col justify-between';

      card.innerHTML = `
        <div>
          <div class="aspect-[4/5] rounded-xl overflow-hidden mb-3 bg-[#fdf2f8] relative cursor-pointer group-hover:opacity-95 transition-opacity">
            <img src="${memory.url}" alt="${memory.caption}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy" />
            <span class="absolute top-2.5 right-2.5 px-2.5 py-0.5 rounded-full bg-white/90 backdrop-blur-xs text-[10px] text-[#5d1c32] font-semibold shadow-xs">
              ${memory.tag || 'Kenangan'}
            </span>
          </div>
          <h3 class="font-serif text-[#5d1c32] font-semibold text-sm sm:text-base leading-snug line-clamp-2 mb-1">
            ${memory.caption}
          </h3>
          <p class="text-xs text-[#8a5d6c] line-clamp-2 mb-3">
            ${memory.note || ''}
          </p>
        </div>
        <div class="pt-2 border-t border-[#ffe1e9]/60 flex items-center justify-between text-xs text-[#8a5d6c]">
          <span class="flex items-center gap-1 font-medium">
            📅 ${memory.date}
          </span>
          <button class="btn-like-memory flex items-center gap-1 px-2 py-1 rounded-lg hover:bg-[#fce7f3] text-[#5d1c32] transition-colors" data-id="${memory.id}">
            <span class="text-rose-500">❤️</span>
            <span class="font-semibold text-xs">${memory.likes || 0}</span>
          </button>
        </div>
      `;

      // Open Lightbox on image click
      const imgContainer = card.querySelector('.aspect-\\[4\\/5\\]');
      if (imgContainer) {
        imgContainer.addEventListener('click', () => {
          openLightbox(memory.id);
        });
      }

      // Like Button handler
      const likeBtn = card.querySelector('.btn-like-memory');
      if (likeBtn) {
        likeBtn.addEventListener('click', (e) => {
          e.stopPropagation();
          likeMemory(memory.id);
        });
      }

      galleryGrid.appendChild(card);
    });
  }

  function likeMemory(id) {
    const item = memories.find(m => m.id === id);
    if (item) {
      item.likes = (item.likes || 0) + 1;
      saveMemories();
      renderGallery();
      triggerHeartBurst();
      if (currentLightboxId === id && lightboxLikeCount) {
        lightboxLikeCount.textContent = item.likes;
      }
    }
  }

  function openLightbox(id) {
    const item = memories.find(m => m.id === id);
    if (!item || !lightboxModal) return;
    currentLightboxId = id;

    if (lightboxImg) lightboxImg.src = item.url;
    if (lightboxCaption) lightboxCaption.textContent = item.caption;
    if (lightboxDate) lightboxDate.textContent = `📅 ${item.date} • 📍 ${item.location || 'Spesial'}`;
    if (lightboxTag) lightboxTag.textContent = item.tag || 'Momen Indah';
    if (lightboxNote) lightboxNote.textContent = item.note || '';
    if (lightboxLikeCount) lightboxLikeCount.textContent = item.likes || 0;

    lightboxModal.classList.remove('hidden');
  }

  function closeLightbox() {
    if (lightboxModal) lightboxModal.classList.add('hidden');
    currentLightboxId = null;
  }

  if (btnCloseLightbox) {
    btnCloseLightbox.addEventListener('click', closeLightbox);
  }

  if (lightboxLikeBtn) {
    lightboxLikeBtn.addEventListener('click', () => {
      if (currentLightboxId) likeMemory(currentLightboxId);
    });
  }

  // Add Memory Modal logic
  if (btnOpenAddMemory) {
    btnOpenAddMemory.addEventListener('click', () => {
      if (addMemoryModal) addMemoryModal.classList.remove('hidden');
    });
  }

  if (btnCloseAddMemory) {
    btnCloseAddMemory.addEventListener('click', () => {
      if (addMemoryModal) addMemoryModal.classList.add('hidden');
    });
  }

  if (formAddMemory) {
    formAddMemory.addEventListener('submit', (e) => {
      e.preventDefault();
      const urlInput = document.getElementById('input-memory-url');
      const captionInput = document.getElementById('input-memory-caption');
      const dateInput = document.getElementById('input-memory-date');
      const locationInput = document.getElementById('input-memory-location');
      const tagInput = document.getElementById('input-memory-tag');
      const noteInput = document.getElementById('input-memory-note');

      const newMem = {
        id: 'm_' + Date.now(),
        url: urlInput?.value || 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=800&q=80',
        caption: captionInput?.value || 'Kenangan Indah Della',
        date: dateInput?.value || '15 Agustus',
        location: locationInput?.value || 'Momen Spesial',
        tag: tagInput?.value || 'Momen Manis',
        note: noteInput?.value || '',
        likes: 1
      };

      memories.unshift(newMem);
      saveMemories();
      renderTags();
      renderGallery();
      triggerRomanticConfetti();

      if (addMemoryModal) addMemoryModal.classList.add('hidden');
      formAddMemory.reset();
    });
  }

  renderTags();
  renderGallery();
}
