(function () {
  var SPLASH_DURATION = 10000;
  var splash = document.getElementById('splash-screen');
  if (!splash) return;

  var progressBar = document.getElementById('splash-progress-bar');
  var btnSkip = document.getElementById('btn-skip-splash');
  var startedAt = Date.now();
  var hidden = false;
  var pollId = null;

  document.body.classList.add('splash-lock-scroll');

  function hideSplash() {
    if (hidden) return;
    hidden = true;
    if (pollId) clearInterval(pollId);

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

  // Poll elapsed wall-clock time instead of relying on a single setTimeout.
  // A lone long-delay setTimeout can be delayed indefinitely by background
  // tab throttling in some browsers; checking Date.now() on a short interval
  // self-corrects the moment the tab is active again.
  pollId = setInterval(function () {
    if (Date.now() - startedAt >= SPLASH_DURATION) {
      hideSplash();
    }
  }, 200);

  if (btnSkip) {
    btnSkip.addEventListener('click', hideSplash);
  }

  // Extra safety net for the same throttling scenario: force a check the
  // moment the tab regains focus, in case the interval itself got paused.
  document.addEventListener('visibilitychange', function () {
    if (!hidden && document.visibilityState === 'visible' && Date.now() - startedAt >= SPLASH_DURATION) {
      hideSplash();
    }
  });
})();
