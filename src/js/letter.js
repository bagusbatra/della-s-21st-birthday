import { DEFAULT_LOVE_LETTER } from './data.js';
import { triggerRomanticConfetti, triggerFullscreenConfettiExplosion } from './confetti.js';
import { romanticSynth } from './audio.js';

export function initLoveLetter() {
  const savedLetter = localStorage.getItem('della_love_letter');
  let letter = savedLetter ? JSON.parse(savedLetter) : DEFAULT_LOVE_LETTER;
  let isEnvelopeSealed = true;
  let isEditing = false;

  const letterModal = document.getElementById('love-letter-modal');
  const btnCloseLetter = document.getElementById('btn-close-letter');
  const sealedEnvelopeView = document.getElementById('letter-sealed-view');
  const openedLetterView = document.getElementById('letter-opened-view');
  const btnBreakSeal = document.getElementById('btn-break-letter-seal');
  const letterParagraphsContainer = document.getElementById('letter-paragraphs-container');
  const letterSalutation = document.getElementById('letter-salutation-display');
  const letterClosing = document.getElementById('letter-closing-display');
  const letterSender = document.getElementById('letter-sender-display');
  const btnCopyLetter = document.getElementById('btn-copy-letter');
  const btnToggleEditLetter = document.getElementById('btn-toggle-edit-letter');
  const letterEditForm = document.getElementById('letter-edit-form');
  const textareaLetterBody = document.getElementById('textarea-letter-body');
  const btnSaveLetterEdit = document.getElementById('btn-save-letter-edit');
  const btnCancelLetterEdit = document.getElementById('btn-cancel-letter-edit');

  function renderLetterContent() {
    if (letterSalutation) letterSalutation.textContent = letter.salutation;
    if (letterClosing) letterClosing.textContent = letter.closing;
    if (letterSender) letterSender.textContent = letter.sender;

    if (letterParagraphsContainer) {
      letterParagraphsContainer.innerHTML = '';
      letter.paragraphs.forEach(p => {
        const pEl = document.createElement('p');
        pEl.className = 'font-cormorant italic text-base sm:text-lg text-[#5d1c32] leading-relaxed mb-4';
        pEl.textContent = p;
        letterParagraphsContainer.appendChild(pEl);
      });
    }
  }

  function openModal() {
    if (!letterModal) return;
    letterModal.classList.remove('hidden');
    renderLetterContent();
  }

  function closeModal() {
    if (!letterModal) return;
    letterModal.classList.add('hidden');
  }

  function unsealLetter() {
    isEnvelopeSealed = false;
    if (sealedEnvelopeView) sealedEnvelopeView.classList.add('hidden');
    if (openedLetterView) openedLetterView.classList.remove('hidden');
    romanticSynth.playEnvelopeOpenSound();
    triggerFullscreenConfettiExplosion();
  }

  if (btnBreakSeal) {
    btnBreakSeal.addEventListener('click', unsealLetter);
  }

  if (btnCloseLetter) {
    btnCloseLetter.addEventListener('click', closeModal);
  }

  if (btnCopyLetter) {
    btnCopyLetter.addEventListener('click', async () => {
      const fullText = `${letter.salutation}\n\n${letter.paragraphs.join('\n\n')}\n\n${letter.closing}\n${letter.sender}`;
      try {
        await navigator.clipboard.writeText(fullText);
        btnCopyLetter.innerHTML = '<span>Tersalin! ❤️</span>';
        setTimeout(() => {
          btnCopyLetter.innerHTML = '<span>Salin Surat Cinta 📋</span>';
        }, 2000);
      } catch (e) {
        // ignore
      }
    });
  }

  if (btnToggleEditLetter) {
    btnToggleEditLetter.addEventListener('click', () => {
      isEditing = !isEditing;
      if (isEditing) {
        if (letterEditForm) letterEditForm.classList.remove('hidden');
        if (letterParagraphsContainer) letterParagraphsContainer.classList.add('hidden');
        if (textareaLetterBody) textareaLetterBody.value = letter.paragraphs.join('\n\n');
      } else {
        if (letterEditForm) letterEditForm.classList.add('hidden');
        if (letterParagraphsContainer) letterParagraphsContainer.classList.remove('hidden');
      }
    });
  }

  if (btnSaveLetterEdit) {
    btnSaveLetterEdit.addEventListener('click', () => {
      if (textareaLetterBody) {
        const text = textareaLetterBody.value.trim();
        if (text) {
          letter.paragraphs = text.split('\n\n').filter(Boolean);
          localStorage.setItem('della_love_letter', JSON.stringify(letter));
          renderLetterContent();
        }
      }
      isEditing = false;
      if (letterEditForm) letterEditForm.classList.add('hidden');
      if (letterParagraphsContainer) letterParagraphsContainer.classList.remove('hidden');
      triggerRomanticConfetti();
    });
  }

  if (btnCancelLetterEdit) {
    btnCancelLetterEdit.addEventListener('click', () => {
      isEditing = false;
      if (letterEditForm) letterEditForm.classList.add('hidden');
      if (letterParagraphsContainer) letterParagraphsContainer.classList.remove('hidden');
    });
  }

  // Connect all buttons across page that open the love letter modal
  document.querySelectorAll('[data-action="open-letter"]').forEach(btn => {
    btn.addEventListener('click', openModal);
  });

  return { openModal, closeModal };
}
