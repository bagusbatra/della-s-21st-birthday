import { triggerFullscreenConfettiExplosion } from './confetti.js';
import { romanticSynth } from './audio.js';
import { icon } from './icons.js';

export function initLoveLetter() {
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

  function openModal() {
    if (letterModal) letterModal.classList.remove('hidden');
  }

  function closeModal() {
    if (letterModal) letterModal.classList.add('hidden');
  }

  function unsealLetter() {
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
      const paragraphs = letterParagraphsContainer
        ? Array.from(letterParagraphsContainer.querySelectorAll('p')).map((p) => p.textContent.trim())
        : [];

      const fullText = [
        letterSalutation ? letterSalutation.textContent.trim() : '',
        '',
        paragraphs.join('\n\n'),
        '',
        letterClosing ? letterClosing.textContent.trim() : '',
        letterSender ? letterSender.textContent.trim() : '',
      ].join('\n');

      try {
        await navigator.clipboard.writeText(fullText);
        btnCopyLetter.innerHTML = `<span>Tersalin!</span> ${icon('heart', 'icon icon-sm')}`;
        setTimeout(() => {
          btnCopyLetter.innerHTML = `Salin Surat Cinta ${icon('copy', 'icon icon-sm')}`;
        }, 2000);
      } catch (e) {
        // ignore
      }
    });
  }

  // Connect all buttons across page that open the love letter modal
  document.querySelectorAll('[data-action="open-letter"]').forEach((btn) => {
    btn.addEventListener('click', openModal);
  });

  return { openModal, closeModal };
}
