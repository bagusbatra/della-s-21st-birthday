      </main>
    </div>
  </div>

  <script>
    (function () {
      var sidebar = document.getElementById('admin-sidebar');
      var overlay = document.getElementById('admin-sidebar-overlay');
      var toggleBtn = document.getElementById('btn-admin-menu');
      if (!sidebar || !overlay || !toggleBtn) return;

      function closeSidebar() {
        sidebar.classList.remove('is-open');
        overlay.classList.remove('is-open');
      }

      toggleBtn.addEventListener('click', function () {
        sidebar.classList.toggle('is-open');
        overlay.classList.toggle('is-open');
      });
      overlay.addEventListener('click', closeSidebar);
      sidebar.querySelectorAll('a').forEach(function (link) {
        link.addEventListener('click', closeSidebar);
      });
    })();
  </script>
</body>
</html>
