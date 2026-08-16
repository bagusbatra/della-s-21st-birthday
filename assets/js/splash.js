(function () {
  var SPLASH_DURATION = 10000;
  var splash = document.getElementById('splash-screen');
  if (!splash) return;

  var progressBar = document.getElementById('splash-progress-bar');
  var btnSkip = document.getElementById('btn-skip-splash');
  var hidden = false;
  var hideTimer = null;

  document.body.classList.add('splash-lock-scroll');

  function hideSplash() {
    if (hidden) return;
    hidden = true;
    clearTimeout(hideTimer);

    splash.classList.add('splash-hide');
    document.body.classList.remove('splash-lock-scroll');

    if (typeof confetti === 'function') {
      confetti({
        particleCount: 90,
        spread: 100,
        origin: { y: 0.4 },
        colors: ['#5d1c32', '#a44a66', '#ffc2d1', '#fce7f3', '#ffffff'],
        scalar: 1.05,
        ticks: 220
      });
    }

    setTimeout(function () {
      if (splash.parentNode) splash.parentNode.removeChild(splash);
    }, 1000);
  }

  requestAnimationFrame(function () {
    requestAnimationFrame(function () {
      if (progressBar) progressBar.style.width = '100%';
    });
  });

  hideTimer = setTimeout(hideSplash, SPLASH_DURATION);

  if (btnSkip) {
    btnSkip.addEventListener('click', hideSplash);
  }
})();
