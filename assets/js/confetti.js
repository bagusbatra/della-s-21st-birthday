export function triggerRomanticConfetti() {
  confetti({
    particleCount: 55,
    spread: 70,
    origin: { y: 0.65 },
    colors: ['#5d1c32', '#a44a66', '#ffc2d1', '#fce7f3', '#e11d48'],
    ticks: 200,
    gravity: 0.8,
    scalar: 1.1,
  });
}

export function triggerHeartBurst() {
  const count = 40;
  const defaults = {
    origin: { y: 0.7 },
    zIndex: 9999,
  };

  function fire(particleRatio, opts) {
    confetti({
      ...defaults,
      ...opts,
      particleCount: Math.floor(count * particleRatio),
      colors: ['#5d1c32', '#a44a66', '#ffc2d1', '#ffffff', '#e11d48'],
    });
  }

  fire(0.25, { spread: 26, startVelocity: 45 });
  fire(0.2, { spread: 60 });
  fire(0.35, { spread: 100, decay: 0.91, scalar: 0.8 });
  fire(0.1, { spread: 120, startVelocity: 25, decay: 0.92, scalar: 1.2 });
  fire(0.1, { spread: 120, startVelocity: 35 });
}

export function triggerFullscreenConfettiExplosion() {
  const duration = 3.5 * 1000;
  const animationEnd = Date.now() + duration;

  // 1. Massive Center Pop
  confetti({
    particleCount: 160,
    spread: 160,
    origin: { x: 0.5, y: 0.45 },
    zIndex: 99999,
    colors: ['#5d1c32', '#a44a66', '#ffc2d1', '#fce7f3', '#e11d48', '#fb7185', '#ffd166', '#ffffff'],
    scalar: 1.25,
    startVelocity: 48,
    ticks: 350,
  });

  // 2. Continuous Dual Cannons from Left & Right
  const interval = setInterval(function() {
    const timeLeft = animationEnd - Date.now();
    if (timeLeft <= 0) {
      return clearInterval(interval);
    }
    const particleCount = 45 * (timeLeft / duration);

    // Left Cannon
    confetti({
      particleCount: Math.floor(particleCount),
      angle: 60,
      spread: 65,
      origin: { x: 0, y: 0.85 },
      zIndex: 99999,
      colors: ['#5d1c32', '#ffc2d1', '#ffd166', '#ffffff', '#e11d48'],
      startVelocity: 60,
      scalar: 1.1,
    });

    // Right Cannon
    confetti({
      particleCount: Math.floor(particleCount),
      angle: 120,
      spread: 65,
      origin: { x: 1, y: 0.85 },
      zIndex: 99999,
      colors: ['#a44a66', '#fce7f3', '#ffc2d1', '#ffffff', '#f43f5e'],
      startVelocity: 60,
      scalar: 1.1,
    });
  }, 220);
}
