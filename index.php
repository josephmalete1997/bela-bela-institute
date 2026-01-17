<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/courses_object.php';
require_once __DIR__ . '/includes/slides.php';

// Safety fallback (prevents notices if files change)
$heroSlides = $heroSlides ?? [];
$courses    = $courses ?? [];
?>

<main id="top">

    <!-- Hero Slider -->
    <section class="hero main-hero hero-slider" aria-label="Hero slider">

        <div class="slides">
            <?php foreach ($heroSlides as $index => $slide): ?>
                <?php
                $bg    = (string)($slide['background'] ?? '');
                $title = (string)($slide['title'] ?? '');
                $lead  = (string)($slide['lead'] ?? '');

                $cta1Label = (string)($slide['cta_primary']['label'] ?? 'Apply Now');
                $cta1Link  = (string)($slide['cta_primary']['link'] ?? 'apply.php');

                $cta2Label = (string)($slide['cta_secondary']['label'] ?? 'View Programs');
                $cta2Link  = (string)($slide['cta_secondary']['link'] ?? 'programs.php');
                ?>

                <div
                    class="slide <?= $index === 0 ? 'active' : '' ?>"
                    style="background-image: url('<?= htmlspecialchars($bg) ?>');"
                    role="group"
                    aria-roledescription="slide"
                    aria-label="Slide <?= $index + 1 ?>">
                    <div class="slide-overlay" aria-hidden="true"></div>

                    <div class="slide-content">
                        <h1><?= htmlspecialchars($title) ?></h1>
                        <p class="lead"><?= htmlspecialchars($lead) ?></p>

                        <div class="hero-cta">
                            <a class="btn" href="<?= htmlspecialchars($cta1Link) ?>"><?= htmlspecialchars($cta1Label) ?></a>
                            <a class="btn btn-ghost" href="<?= htmlspecialchars($cta2Link) ?>"><?= htmlspecialchars($cta2Label) ?></a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if (!empty($heroSlides)): ?>
            <!-- Arrows -->
            <div class="slider-controls" aria-label="Slider controls">
                <button class="slider-arrow prev" type="button" data-action="prev" aria-label="Previous slide">‹</button>
                <button class="slider-arrow next" type="button" data-action="next" aria-label="Next slide">›</button>
            </div>

            <!-- Dots -->
            <div class="slider-dots" aria-label="Slider navigation">
                <?php foreach ($heroSlides as $i => $_): ?>
                    <button
                        class="dot <?= $i === 0 ? 'active' : '' ?>"
                        type="button"
                        data-slide="<?= (int)$i ?>"
                        aria-label="Go to slide <?= (int)$i + 1 ?>"></button>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </section>


    <!-- Trust -->
    <section class="trust">
        <div class="container trust-inner">
            <p>Trusted by learners who want practical, career-focused ICT skills.</p>
            <div class="trust-badges">
                <span class="badge">Hands-on Training</span>
                <span class="badge">Industry Tools</span>
                <span class="badge">Career Support</span>
                <span class="badge">Flexible Learning</span>
            </div>
        </div>
    </section>
    <!-- Featured Programs -->
    <section class="section" id="programs">
        <div class="container">
            <div class="section-head">
                <h2>Featured Programs</h2>
                <p>Focused, career-ready programs designed to build practical ICT skills.</p>
            </div>

            <div class="grid cards">
                <?php foreach (array_slice($courses, 0, 3) as $course): ?>
                    <article class="card" id="<?= htmlspecialchars($course['id'] ?? '') ?>">
                        <img
                            src="<?= htmlspecialchars($course['image'] ?? '') ?>"
                            alt="<?= htmlspecialchars($course['alt'] ?? ($course['title'] ?? 'Course')) ?>"
                            width="100%"
                            loading="lazy">
                        <h3><?= htmlspecialchars($course['title'] ?? '') ?></h3>
                        <p><?= htmlspecialchars($course['description'] ?? '') ?></p>
                        <p><strong>Fee: R<?= number_format($course['fee'] ?? 0, 2) ?></strong></p>
                    </article>
                <?php endforeach; ?>
            </div>

            <div class="note">
                <strong>Explore all offerings.</strong> View the full program list and intake dates.
                <a href="programs.php">View programs</a>
            </div>
        </div>
    </section>

    <!-- Student Experience -->
    <section class="section alt" id="experience">
        <div class="container">
            <div class="section-head">
                <h2>Student Experience</h2>
                <p>Practical learning, structured support, and clear pathways to outcomes.</p>
            </div>

            <div class="grid two">
                <div class="panel">
                    <h3>Applied Learning</h3>
                    <p>Hands-on tasks and projects that mirror real workplace scenarios.</p>
                </div>
                <div class="panel">
                    <h3>Guided Support</h3>
                    <p>Mentor feedback, structured lessons, and consistent academic guidance.</p>
                </div>
                <div class="panel">
                    <h3>Flexible Scheduling</h3>
                    <p>Weekday and weekend options built for working learners.</p>
                </div>
                <div class="panel">
                    <h3>Career Focus</h3>
                    <p>Portfolio-ready work and career readiness support.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Admissions -->
    <section class="section" id="admissions">
        <div class="container">
            <div class="section-head">
                <h2>Admissions at a Glance</h2>
                <p>A simple, guided application process with clear next steps.</p>
            </div>

            <div class="steps">
                <div class="step">
                    <div class="step-num">1</div>
                    <h3>Apply Online</h3>
                    <p>Complete the online application form with your program selection.</p>
                </div>
                <div class="step">
                    <div class="step-num">2</div>
                    <h3>Review &amp; Guidance</h3>
                    <p>Our team reviews your application and confirms requirements.</p>
                </div>
                <div class="step">
                    <div class="step-num">3</div>
                    <h3>Enroll</h3>
                    <p>Choose an intake and confirm your place.</p>
                </div>
                <div class="step">
                    <div class="step-num">4</div>
                    <h3>Start Learning</h3>
                    <p>Begin classes with access to materials and student support.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="section alt" id="contact-cta">
        <div class="container">
            <div class="note">
                <strong>Have questions?</strong> Talk to our admissions team for guidance.
                <a href="contact.php">Contact us</a>
            </div>
        </div>
    </section>

</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
