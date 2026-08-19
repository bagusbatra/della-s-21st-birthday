/**
 * Animasi "masuk & keluar" saat scroll: elemen ber-class .reveal
 * fade + geser masuk saat masuk viewport, dan kembali ke kondisi
 * tersembunyi saat keluar viewport — sehingga animasinya berulang
 * setiap kali elemen dilewati (bukan hanya sekali di awal).
 */
export function initScrollReveal() {
  const targets = document.querySelectorAll('.reveal');
  if (!targets.length) return;

  const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  if (!('IntersectionObserver' in window) || prefersReducedMotion) {
    targets.forEach((el) => el.classList.add('is-visible'));
    return;
  }

  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        entry.target.classList.toggle('is-visible', entry.isIntersecting);
      });
    },
    { threshold: 0.15, rootMargin: '0px 0px -10% 0px' }
  );

  targets.forEach((el) => observer.observe(el));
}
