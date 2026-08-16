import { triggerFullscreenConfettiExplosion, triggerRomanticConfetti } from './confetti.js';
import { romanticSynth } from './audio.js';

export function initCountdown(initialDate = '2026-08-15T00:00:00+07:00') {
  let targetDate = localStorage.getItem('della_birthday_date') || initialDate;
  let hasTriggeredZero = false;
  let intervalId = null;

  const cdDays = document.getElementById('cd-days');
  const cdHours = document.getElementById('cd-hours');
  const cdMinutes = document.getElementById('cd-minutes');
  const cdSeconds = document.getElementById('cd-seconds');
  const targetDateDisplay = document.getElementById('target-date-display');
  const countdownGrid = document.getElementById('countdown-grid');
  const countdownPassedBanner = document.getElementById('countdown-passed-banner');
  const editContainer = document.getElementById('countdown-edit-container');
  const inputDateTime = document.getElementById('input-target-date');
  const btnToggleEdit = document.getElementById('btn-toggle-edit-countdown');
  const formEditDate = document.getElementById('form-edit-target-date');
  const btnCancelEdit = document.getElementById('btn-cancel-edit-countdown');
  const btnTest5s = document.getElementById('btn-test-countdown-5s');
  const btnTestConfetti = document.getElementById('btn-test-confetti-explosion');
  const btnCelebrationAgain = document.getElementById('btn-countdown-celebrate-again');
  const btnSubtleConfetti = document.getElementById('btn-countdown-subtle-confetti');

  function formatDisplayDate(dateStr) {
    try {
      const d = new Date(dateStr);
      return d.toLocaleDateString('id-ID', {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      }) + ' WIB';
    } catch {
      return dateStr;
    }
  }

  function updateDisplay() {
    if (targetDateDisplay) {
      targetDateDisplay.textContent = formatDisplayDate(targetDate);
    }
  }

  function tick() {
    const diff = new Date(targetDate).getTime() - Date.now();

    if (diff <= 0) {
      if (cdDays) cdDays.textContent = '00';
      if (cdHours) cdHours.textContent = '00';
      if (cdMinutes) cdMinutes.textContent = '00';
      if (cdSeconds) cdSeconds.textContent = '00';

      if (countdownGrid) countdownGrid.classList.add('hidden');
      if (countdownPassedBanner) countdownPassedBanner.classList.remove('hidden');

      if (!hasTriggeredZero) {
        hasTriggeredZero = true;
        romanticSynth.playCelebrationChime();
        triggerFullscreenConfettiExplosion();
      }
      return;
    }

    if (countdownGrid) countdownGrid.classList.remove('hidden');
    if (countdownPassedBanner) countdownPassedBanner.classList.add('hidden');

    const days = Math.floor(diff / (1000 * 60 * 60 * 24));
    const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((diff % (1000 * 60)) / 1000);

    if (cdDays) cdDays.textContent = String(days).padStart(2, '0');
    if (cdHours) cdHours.textContent = String(hours).padStart(2, '0');
    if (cdMinutes) cdMinutes.textContent = String(minutes).padStart(2, '0');
    if (cdSeconds) cdSeconds.textContent = String(seconds).padStart(2, '0');
  }

  function setDate(newDate) {
    targetDate = newDate;
    hasTriggeredZero = false;
    localStorage.setItem('della_birthday_date', targetDate);
    updateDisplay();
    tick();
  }

  updateDisplay();
  tick();
  intervalId = setInterval(tick, 1000);

  if (btnToggleEdit) {
    btnToggleEdit.addEventListener('click', () => {
      if (editContainer) {
        editContainer.classList.toggle('hidden');
        if (!editContainer.classList.contains('hidden') && inputDateTime) {
          try {
            inputDateTime.value = new Date(targetDate).toISOString().substring(0, 16);
          } catch (e) {
            // ignore
          }
        }
      }
    });
  }

  if (btnCancelEdit) {
    btnCancelEdit.addEventListener('click', () => {
      if (editContainer) editContainer.classList.add('hidden');
    });
  }

  if (formEditDate) {
    formEditDate.addEventListener('submit', (e) => {
      e.preventDefault();
      if (inputDateTime && inputDateTime.value) {
        setDate(new Date(inputDateTime.value).toISOString());
        if (editContainer) editContainer.classList.add('hidden');
      }
    });
  }

  if (btnTest5s) {
    btnTest5s.addEventListener('click', () => {
      const testIso = new Date(Date.now() + 5000).toISOString();
      setDate(testIso);
      if (editContainer) editContainer.classList.add('hidden');
    });
  }

  if (btnTestConfetti) {
    btnTestConfetti.addEventListener('click', () => {
      romanticSynth.playCelebrationChime();
      triggerFullscreenConfettiExplosion();
    });
  }

  if (btnCelebrationAgain) {
    btnCelebrationAgain.addEventListener('click', () => {
      romanticSynth.playCelebrationChime();
      triggerFullscreenConfettiExplosion();
    });
  }

  if (btnSubtleConfetti) {
    btnSubtleConfetti.addEventListener('click', () => {
      triggerRomanticConfetti();
    });
  }

  return { setDate };
}
