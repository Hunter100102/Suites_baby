<footer class="footer">
  <p>© <?= date('Y') ?> Aban Suites — All rights reserved.</p>
</footer>
<script src="/assets/js/app.js" defer></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const toggle = document.querySelector('.nav-toggle');
  const menu   = document.querySelector('.nav .menu');
  if (!toggle || !menu) return;

  toggle.addEventListener('click', function () {
    const isOpen = menu.classList.toggle('menu-open');
    toggle.classList.toggle('is-open', isOpen);
  });
});
</script>
</body>
</html>

