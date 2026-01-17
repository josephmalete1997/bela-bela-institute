<?php
declare(strict_types=1);

$meta_title = 'Programs | Bela-Bela Institute Of Higher Learning';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/courses_object.php';

$courses = $courses ?? [];
?>

<main id="top">
  <section class="section">
    <div class="container">
      <div class="section-head">
        <h2>Programs</h2>
        <p>Short, focused ICT programs that build practical skills and confidence.</p>
      </div>

      <div class="grid cards">
        <?php foreach ($courses as $course): ?>
          <article class="card" id="<?= htmlspecialchars($course['id'] ?? '') ?>">
            <img
              src="<?= htmlspecialchars($course['image'] ?? '') ?>"
              alt="<?= htmlspecialchars($course['alt'] ?? ($course['title'] ?? 'Course')) ?>"
              width="100%"
              loading="lazy">
            <h3><?= htmlspecialchars($course['title'] ?? '') ?></h3>
            <p><?= htmlspecialchars($course['description'] ?? '') ?></p>
            <p><strong>Fee: R<?= number_format($course['fee'] ?? 0, 2) ?></strong></p>

            <?php if (!empty($course['highlights']) && is_array($course['highlights'])): ?>
              <ul class="mini">
                <?php foreach ($course['highlights'] as $item): ?>
                  <li><?= htmlspecialchars((string)$item) ?></li>
                <?php endforeach; ?>
              </ul>
            <?php endif; ?>
          </article>
        <?php endforeach; ?>
      </div>

      <div class="note">
        <strong>Need help choosing?</strong> Tell us your goal and we will recommend the best program.
        <a href="contact.php">Talk to us</a>
      </div>
    </div>
  </section>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
