import { triggerHeartBurst } from './confetti.js';
import { icon } from './icons.js';

export function initGallery() {
  const galleryGrid = document.getElementById('gallery-grid');
  const tagFilterContainer = document.getElementById('gallery-tag-filters');

  const lightboxModal = document.getElementById('lightbox-modal');
  const lightboxImg = document.getElementById('lightbox-img');
  const lightboxCaption = document.getElementById('lightbox-caption');
  const lightboxDate = document.getElementById('lightbox-date');
  const lightboxTag = document.getElementById('lightbox-tag');
  const lightboxNote = document.getElementById('lightbox-note');
  const lightboxLikeBtn = document.getElementById('lightbox-like-btn');
  const lightboxLikeCount = document.getElementById('lightbox-like-count');
  const btnCloseLightbox = document.getElementById('btn-close-lightbox');

  let currentLightboxImage = null;

  function applyTagFilter(tag) {
    if (!galleryGrid) return;
    galleryGrid.querySelectorAll('.gallery-card').forEach((card) => {
      const matches = tag === 'Semua' || card.dataset.tag === tag;
      card.classList.toggle('hidden', !matches);
    });
  }

  if (tagFilterContainer) {
    const tagButtons = tagFilterContainer.querySelectorAll('.gallery-tag-btn');
    tagButtons.forEach((btn) => {
      btn.addEventListener('click', () => {
        tagButtons.forEach((b) => {
          b.classList.remove('bg-[#5d1c32]', 'text-white', 'shadow-xs');
          b.classList.add('bg-white', 'text-[#8a5d6c]', 'border', 'border-[#ffe1e9]');
        });
        btn.classList.remove('bg-white', 'text-[#8a5d6c]', 'border', 'border-[#ffe1e9]');
        btn.classList.add('bg-[#5d1c32]', 'text-white', 'shadow-xs');
        applyTagFilter(btn.dataset.tag);
      });
    });
  }

  function openLightbox(imageEl) {
    if (!lightboxModal) return;
    currentLightboxImage = imageEl;

    if (lightboxImg) lightboxImg.src = imageEl.dataset.url;
    if (lightboxCaption) lightboxCaption.textContent = imageEl.dataset.caption;
    if (lightboxDate) {
      lightboxDate.innerHTML = `${icon('calendar', 'icon icon-sm')} ${imageEl.dataset.date} &bull; ${icon('map-pin', 'icon icon-sm')} ${imageEl.dataset.location || 'Spesial'}`;
    }
    if (lightboxTag) lightboxTag.textContent = imageEl.dataset.tag || 'Momen Indah';
    if (lightboxNote) lightboxNote.textContent = imageEl.dataset.note || '';
    if (lightboxLikeCount) lightboxLikeCount.textContent = imageEl.dataset.likes || 0;

    lightboxModal.classList.remove('hidden');
  }

  function closeLightbox() {
    if (lightboxModal) lightboxModal.classList.add('hidden');
    currentLightboxImage = null;
  }

  function incrementLike(imageEl, likeCountEl) {
    const newLikes = (parseInt(imageEl.dataset.likes, 10) || 0) + 1;
    imageEl.dataset.likes = String(newLikes);
    if (likeCountEl) likeCountEl.textContent = newLikes;
    triggerHeartBurst();

    if (currentLightboxImage === imageEl && lightboxLikeCount) {
      lightboxLikeCount.textContent = newLikes;
    }
  }

  if (galleryGrid) {
    galleryGrid.querySelectorAll('.gallery-card').forEach((card) => {
      const imageEl = card.querySelector('.gallery-card-image');
      const likeBtn = card.querySelector('.btn-like-memory');
      const likeCountEl = likeBtn ? likeBtn.querySelector('.like-count') : null;

      if (imageEl) {
        imageEl.addEventListener('click', () => openLightbox(imageEl));
      }

      if (likeBtn && imageEl) {
        likeBtn.addEventListener('click', (e) => {
          e.stopPropagation();
          incrementLike(imageEl, likeCountEl);
        });
      }
    });
  }

  if (btnCloseLightbox) {
    btnCloseLightbox.addEventListener('click', closeLightbox);
  }

  if (lightboxLikeBtn) {
    lightboxLikeBtn.addEventListener('click', () => {
      if (!currentLightboxImage) return;
      const card = currentLightboxImage.closest('.gallery-card');
      const likeCountEl = card ? card.querySelector('.btn-like-memory .like-count') : null;
      incrementLike(currentLightboxImage, likeCountEl);
    });
  }
}
