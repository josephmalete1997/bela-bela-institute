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

                $cta2Label = (string)($slide['cta_secondary']['label'] ?? 'View Courses');
                $cta2Link  = (string)($slide['cta_secondary']['link'] ?? '#courses');
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

    <!-- Courses -->
    <section class="section" id="courses">
        <div class="container">
            <div class="section-head">
                <h2>Upskilling ICT Courses</h2>
                <p>Choose a track that matches your goals. Short courses designed to get you building fast.</p>
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
                <strong>Need help choosing?</strong> Tell us your goal and we’ll recommend the best track for you.
                <a href="#contact">Talk to us →</a>
            </div>
        </div>
    </section>

    <!-- Why Us -->
    <section class="section alt" id="why">
        <div class="container">
            <div class="section-head">
                <h2>Why Bela-Bela Institute?</h2>
                <p>We focus on practical learning that translates into real opportunities.</p>
            </div>

            <div class="grid two">
                <div class="panel">
                    <h3>Learn by Doing</h3>
                    <p>Hands-on tasks, projects, and guided practice—so you don’t just watch, you build.</p>
                </div>
                <div class="panel">
                    <h3>Support That Matters</h3>
                    <p>Mentors and structured lessons that help you stay consistent and confident.</p>
                </div>
                <div class="panel">
                    <h3>Flexible Options</h3>
                    <p>Weekend and evening classes designed for working learners and busy schedules.</p>
                </div>
                <div class="panel">
                    <h3>Career Focus</h3>
                    <p>CV-ready projects, interview practice, and guidance on entry-level ICT pathways.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- How it works -->
    <section class="section" id="how">
        <div class="container">
            <div class="section-head">
                <h2>How It Works</h2>
                <p>A simple path from interest to skills to outcomes.</p>
            </div>

            <div class="steps">
                <div class="step">
                    <div class="step-num">1</div>
                    <h3>Apply</h3>
                    <p>Fill in the quick form. We’ll contact you with options and next steps.</p>
                </div>
                <div class="step">
                    <div class="step-num">2</div>
                    <h3>Choose a Track</h3>
                    <p>Pick a course based on your goal: job, business, or skills upgrade.</p>
                </div>
                <div class="step">
                    <div class="step-num">3</div>
                    <h3>Train &amp; Build</h3>
                    <p>Attend sessions, complete projects, and get feedback to improve.</p>
                </div>
                <div class="step">
                    <div class="step-num">4</div>
                    <h3>Get Certified</h3>
                    <p>Earn a certificate and leave with practical outcomes you can show.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact -->
    <section class="section alt" id="contact">
        <div class="container">
            <div class="contact">
                <div>
                    <h2>Contact</h2>
                    <p>Reach out for course dates, fees, and class schedules.</p>

                    <div class="contact-grid">
                        <div class="contact-item">
                            <h3>WhatsApp / Phone</h3>
                            <p><a href="tel:+27000000000">+27 00 000 0000</a></p>
                            <small>Replace with your number</small>
                        </div>
                        <div class="contact-item">
                            <h3>Email</h3>
                            <p><a href="mailto:info@belabelainstitute.co.za">info@belabelainstitute.co.za</a></p>
                            <small>Replace with your email</small>
                        </div>
                        <div class="contact-item">
                            <h3>Location</h3>
                            <p>Bela-Bela, Limpopo</p>
                            <small>Add your full address if you want</small>
                        </div>
                    </div>
                </div>

                <div class="map">
                    <div class="map-placeholder">
                        <h3>Find Us</h3>
                        <p>Add a Google Maps embed here when ready.</p>
                        <code>&lt;iframe ...&gt;&lt;/iframe&gt;</code>
                    </div>
                </div>
            </div>
        </div>
    </section>

</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>