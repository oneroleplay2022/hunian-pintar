<?php
/**
 * Footer include - JS scripts & closing tags
 */
?>
  <!-- App JS -->
  <script src="js/app.js"></script>

  <!-- Init Lucide Icons -->
  <script>
    lucide.createIcons();
  </script>

  <?php if (isset($extraScripts)): ?>
    <?= $extraScripts ?>
  <?php endif; ?>
</body>
</html>
