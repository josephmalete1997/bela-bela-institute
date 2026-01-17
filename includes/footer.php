<!-- Footer -->
  <footer class="footer">
    <div class="container footer-inner">
      <div>
        <div class="footer-brand">
          <div class="brand-mark small" aria-hidden="true">BB</div>
          <div>
            <strong class="footer-title">Bela-Bela Institute Of Higher Learning</strong>
            <div class="footer-sub">Upskilling ICT Courses • Bela-Bela, Limpopo</div>
          </div>
        </div>
      </div>

      <div class="footer-links">
        <a href="#courses">Courses</a>
        <a href="#why">Why Us</a>
        <a href="#apply">Apply</a>
        <a href="#contact">Contact</a>
        <a href="sitemap.php">Sitemap</a>
      </div>

      <div class="footer-copy">
        <small>© <span id="year"></span> Bela-Bela Institute Of Higher Learning. All rights reserved.</small>
      </div>
    </div>
  </footer>
<style>
  /* ================================
   SOTA FOOTER – BELA-BELA IHL
================================ */

.footer {
  background: linear-gradient(180deg, #0b0f1a 0%, #070a12 100%);
  color: #cbd5e1;
  padding: 3rem 1.5rem 2rem;
  border-top: 1px solid rgba(255, 255, 255, 0.06);
  font-family: 'Poppins', system-ui, sans-serif;
}

.footer-inner {
  max-width: 1200px;
  margin: 0 auto;
  display: grid;
  grid-template-columns: 1fr auto 1fr;
  gap: 2rem;
  align-items: center;
}

/* Brand section */
.footer-brand {
  display: flex;
  align-items: center;
  gap: 0.9rem;
}

.brand-mark.small {
  width: 42px;
  height: 42px;
  border-radius: 10px;
  background: linear-gradient(135deg, #2563eb, #38bdf8);
  color: #fff;
  font-weight: 700;
  display: grid;
  place-items: center;
  font-size: 0.95rem;
  letter-spacing: 0.05em;
}

.footer-title {
  color: #ffffff;
  font-size: 0.95rem;
  font-weight: 600;
}

.footer-sub {
  font-size: 0.8rem;
  color: #94a3b8;
  margin-top: 0.15rem;
}

/* Links */
.footer-links {
  display: flex;
  gap: 1.8rem;
}

.footer-links a {
  font-size: 0.85rem;
  color: #cbd5e1;
  text-decoration: none;
  position: relative;
  transition: color 0.25s ease;
}

.footer-links a::after {
  content: '';
  position: absolute;
  left: 0;
  bottom: -4px;
  width: 0;
  height: 2px;
  background: #38bdf8;
  transition: width 0.25s ease;
}

.footer-links a:hover {
  color: #38bdf8;
}

.footer-links a:hover::after {
  width: 100%;
}

/* Copyright */
.footer-copy {
  text-align: right;
  font-size: 0.75rem;
  color: #94a3b8;
}

/* Responsive */
@media (max-width: 900px) {
  .footer-inner {
    grid-template-columns: 1fr;
    text-align: center;
  }

  .footer-brand {
    justify-content: center;
  }

  .footer-links {
    justify-content: center;
    flex-wrap: wrap;
    gap: 1.2rem;
  }

  .footer-copy {
    text-align: center;
  }
}

.back-to-top {
  position: fixed;
  bottom: 20px;
  right: 20px;
  width: 50px;
  height: 50px;
  border-radius: 50%;
  background: var(--brand);
  color: white;
  border: none;
  cursor: pointer;
  opacity: 0;
  visibility: hidden;
  transition: opacity 0.3s ease, visibility 0.3s ease;
  z-index: 1000;
  font-size: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.back-to-top.show {
  opacity: 1;
  visibility: visible;
}

</style>
  <button id="back-to-top" class="back-to-top" aria-label="Back to top">↑</button>
  <script src="js/main.js"></script>
</body>
</html>