<!-- Footer -->
  <footer class="footer">
    <div class="container footer-inner">
      <div>
        <div class="footer-brand">
          <img src="images/logo.png" alt="Bela-Bela Institute logo" class="footer-logo">
          <div>
            <strong class="footer-title">Bela-Bela Institute Of Higher Learning</strong>
            <div class="footer-sub">Upskilling ICT Courses • Bela-Bela, Limpopo</div>
          </div>
        </div>
      </div>

      <div class="footer-links">
        <a href="about.php">About</a>
        <a href="programs.php">Programs</a>
        <a href="admissions.php">Admissions</a>
        <a href="articles.php">News</a>
        <a href="contact.php">Contact</a>
        <a href="sitemap.php">Sitemap</a>
      </div>

      <div class="footer-copy">
        <small>© <span id="year"></span> Bela-Bela Institute Of Higher Learning. All rights reserved.</small>
      </div>
    </div>
  </footer>
<style>
  /* ================================
   FOOTER – BELA-BELA IHL
================================ */

.footer {
  background: #f7f7f7;
  color: #111827;
  padding: 3rem 1.5rem 2rem;
  border-top: 1px solid rgba(17, 24, 39, 0.08);
  font-family: "Source Sans 3", "Segoe UI", sans-serif;
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

.footer-logo {
  width: 56px;
  height: auto;
  display: block;
}

.footer-title {
  color: #111827;
  font-size: 0.95rem;
  font-weight: 700;
}

.footer-sub {
  font-size: 0.8rem;
  color: #6b7280;
  margin-top: 0.15rem;
}

/* Links */
.footer-links {
  display: flex;
  gap: 1.8rem;
}

.footer-links a {
  font-size: 0.85rem;
  color: #374151;
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
  background: #a51c30;
  transition: width 0.25s ease;
}

.footer-links a:hover {
  color: #a51c30;
}

.footer-links a:hover::after {
  width: 100%;
}

/* Copyright */
.footer-copy {
  text-align: right;
  font-size: 0.75rem;
  color: #6b7280;
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
  background: #a51c30;
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
