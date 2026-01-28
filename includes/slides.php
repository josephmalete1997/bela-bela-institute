<?php
$heroSlides = [
  [
    "title" => "Upskill in ICT. Build real skills. Get job-ready.",
    "lead" => "Practical training in Software Development, Data, Networking, Cybersecurity, and Digital Skills.",
    "background" => "images/hero.png",
    "cta_primary" => ["label" => "Apply Now", "link" => "apply.php"],
    "cta_secondary" => ["label" => "View Programs", "link" => "programs.php"],
  ],
  [
    "title" => "Learn by Doing. Not Just Watching.",
    "lead" => "Hands-on projects, mentor support, and real-world tools to build confidence fast.",
    "background" => "images/a1.png",
    "cta_primary" => ["label" => "Start Learning", "link" => "apply.php"],
    "cta_secondary" => ["label" => "Admissions", "link" => "admissions.php"],
  ],
  [
    "title" => "Flexible ICT Courses for Real Life.",
    "lead" => "Weekend and evening classes designed for working learners and students.",
    "background" => "images/a2.png",
    "cta_primary" => ["label" => "Join the Next Intake", "link" => "apply.php"],
    "cta_secondary" => ["label" => "Contact Us", "link" => "contact.php"],
  ],
];

// Preload images (prevents flicker on first transitions)
?>
<?php foreach ($heroSlides as $s): ?>
  <link rel="preload" as="image" href="<?= htmlspecialchars($s['background']) ?>">
<?php endforeach; ?>

<style>
/* ======================================
   HERO SLIDER – STATE OF THE ART
====================================== */

.hero-slider{
  position: relative;
  min-height: 85vh;
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #0b1220;
  isolation: isolate;
}

/* optional: soft top/bottom fade edge */
.hero-slider::after{
  content:"";
  position:absolute;
  inset:0;
  pointer-events:none;
  background: rgba(2,6,23,0.35);
  z-index: 2;
}

.slides{
  position: relative;
  width: 100%;
  height: 100%;
}

.slide{
  position:absolute;
  inset:0;
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;

  opacity:0;
  transform: scale(1.06);
  transition: opacity 900ms ease, transform 1400ms ease;

  display:flex;
  align-items:center;
  justify-content:center;
  will-change: opacity, transform;
}

.slide-bg{
  position:absolute;
  inset:0;
  width:100%;
  height:100%;
  object-fit: cover;
  z-index: 0;
}

.slide.active{
  opacity:1;
  transform: scale(1);
  z-index: 1;
}

/* layered overlay: radial + vignette + subtle noise */
.slide-overlay{
  position:absolute;
  inset:0;
  background: rgba(2, 6, 23, 0.65);
  z-index: 1;
}

.slide-overlay::after{
  content:"";
  position:absolute;
  inset:0;
  opacity:.10;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='180' height='180'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='.9' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='180' height='180' filter='url(%23n)' opacity='.5'/%3E%3C/svg%3E");
  mix-blend-mode: overlay;
}

/* content */
.slide-content{
  position: relative;
  z-index: 2;
  max-width: 980px;
  padding: 0 1.5rem;
  text-align: center;
  color:#fff;
  animation: fadeInUp 1s ease-out;
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.slide-content h1{
  font-size: clamp(2.15rem, 5.2vw, 3.6rem);
  font-weight: 800;
  letter-spacing: -0.02em;
  line-height: 1.12;
  margin: 0 0 1rem;
  text-shadow: 0 10px 30px rgba(0,0,0,.35);
}

.slide-content .lead{
  font-size: 1.06rem;
  color: rgba(226,232,240,.95);
  max-width: 760px;
  margin: 0 auto 2rem;
}

/* CTA */
.hero-cta{
  display:flex;
  justify-content:center;
  gap: 1rem;
  flex-wrap: wrap;
}

/* dots */
.slider-dots{
  position:absolute;
  bottom: 1.9rem;
  left: 50%;
  transform: translateX(-50%);
  display:flex;
  gap: .55rem;
  z-index: 5;
}

.dot{
  width: 10px;
  height: 10px;
  border-radius: 999px;
  border: 0;
  cursor: pointer;
  background: rgba(255,255,255,.45);
  transition: all 220ms ease;
}

.dot:hover{ transform: translateY(-1px); }
.dot.active{
  width: 26px;
  background: var(--brand);
}

/* arrows */
.slider-arrow{
  position:absolute;
  top: 50%;
  transform: translateY(-50%);
  z-index: 5;

  width: 44px;
  height: 44px;
  border-radius: 14px;
  border: 1px solid rgba(255,255,255,.2);
  background: rgba(2, 6, 23, 0.4);
  color: #fff;
  cursor:pointer;
  display:grid;
  place-items:center;
  backdrop-filter: blur(10px);

  transition: transform 200ms ease, background 200ms ease, border-color 200ms ease;
}

.slider-arrow:hover{
  background: rgba(2, 6, 23, 0.6);
  border-color: rgba(255,255,255,.28);
  transform: translateY(-50%) scale(1.03);
}

.slider-arrow.prev{ left: 1.25rem; }
.slider-arrow.next{ right: 1.25rem; }

/* stats (optional if you already style these elsewhere) */
.hero-stats{
  position:absolute;
  bottom: 4.6rem;
  left: 50%;
  transform: translateX(-50%);
  display:flex;
  gap: 1.2rem;
  z-index: 5;
  flex-wrap: wrap;
  justify-content:center;
}

.hero-stats .stat{
  background: rgba(255,255,255,.08);
  border: 1px solid rgba(255,255,255,.14);
  border-radius: 16px;
  padding: .85rem 1.15rem;
  backdrop-filter: blur(12px);
  min-width: 120px;
}

.hero-stats .stat-num{
  font-weight: 800;
  font-size: 1.15rem;
}

.hero-stats .stat-label{
  font-size: .78rem;
  color: rgba(226,232,240,.9);
}

/* mobile */
@media (max-width: 900px){
  .hero-slider{ min-height: 78vh; }
  .slider-arrow{ display:none; } /* cleaner on mobile */
  .hero-stats{ bottom: 4.2rem; gap: .8rem; }
  .hero-stats .stat{ min-width: 104px; padding: .75rem .95rem; }
  .slider-dots{ bottom: 1.2rem; }
}

/* accessibility: reduce motion */
@media (prefers-reduced-motion: reduce){
  .slide{ transition: none; transform: none; }
  .dot{ transition: none; }
  .slider-arrow{ transition: none; }
}
</style>

<script>
(() => {
  const slider = document.querySelector(".hero-slider");
  if (!slider) return;

  const slides = slider.querySelectorAll(".slide");
  const dots   = slider.querySelectorAll(".dot");

  if (!slides.length || !dots.length) return;

  let current = 0;
  let timer = null;
  const intervalMs = 6000;

  // --- helpers
  const clampIndex = (i) => (i + slides.length) % slides.length;

  const show = (i) => {
    const index = clampIndex(i);
    slides.forEach(s => s.classList.remove("active"));
    dots.forEach(d => d.classList.remove("active"));
    slides[index].classList.add("active");
    dots[index].classList.add("active");
    current = index;
  };

  const next = () => show(current + 1);
  const prev = () => show(current - 1);

  const start = () => {
    stop();
    timer = setInterval(next, intervalMs);
  };

  const stop = () => {
    if (timer) clearInterval(timer);
    timer = null;
  };

  // dots
  dots.forEach(dot => {
    dot.addEventListener("click", () => {
      show(Number(dot.dataset.slide || 0));
      start();
    });
  });

  // arrows (optional)
  const prevBtn = slider.querySelector("[data-action='prev']");
  const nextBtn = slider.querySelector("[data-action='next']");
  prevBtn?.addEventListener("click", () => { prev(); start(); });
  nextBtn?.addEventListener("click", () => { next(); start(); });

  // keyboard
  window.addEventListener("keydown", (e) => {
    if (e.key === "ArrowLeft") { prev(); start(); }
    if (e.key === "ArrowRight") { next(); start(); }
  });

  // pause on hover (desktop)
  slider.addEventListener("mouseenter", stop);
  slider.addEventListener("mouseleave", start);

  // pause when tab hidden (perf)
  document.addEventListener("visibilitychange", () => {
    if (document.hidden) stop();
    else start();
  });

  // swipe on mobile
  let startX = 0;
  slider.addEventListener("touchstart", (e) => {
    startX = e.touches[0].clientX;
  }, { passive: true });

  slider.addEventListener("touchend", (e) => {
    const endX = e.changedTouches[0].clientX;
    const diff = endX - startX;
    if (Math.abs(diff) > 50) {
      diff > 0 ? prev() : next();
      start();
    }
  }, { passive: true });

  start();
})();
</script>
