(function () {
  var target = new Date('2026-08-19T00:00:00+07:00').getTime();

  var elDays = document.getElementById('gate-cd-days');
  var elHours = document.getElementById('gate-cd-hours');
  var elMinutes = document.getElementById('gate-cd-minutes');
  var elSeconds = document.getElementById('gate-cd-seconds');

  function tick() {
    var diff = target - Date.now();

    if (diff <= 0) {
      window.location.reload();
      return;
    }

    var days = Math.floor(diff / 86400000);
    var hours = Math.floor((diff % 86400000) / 3600000);
    var minutes = Math.floor((diff % 3600000) / 60000);
    var seconds = Math.floor((diff % 60000) / 1000);

    if (elDays) elDays.textContent = String(days).padStart(2, '0');
    if (elHours) elHours.textContent = String(hours).padStart(2, '0');
    if (elMinutes) elMinutes.textContent = String(minutes).padStart(2, '0');
    if (elSeconds) elSeconds.textContent = String(seconds).padStart(2, '0');
  }

  tick();
  setInterval(tick, 1000);
})();
