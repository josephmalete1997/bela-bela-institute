<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?php echo htmlspecialchars($meta_title ?? 'Bela-Bela Institute Of Higher Learning | Upskilling ICT Courses', ENT_QUOTES, 'UTF-8'); ?></title>
  <?php if (!empty($meta_description)): ?>
    <meta name="description" content="<?php echo htmlspecialchars($meta_description, ENT_QUOTES, 'UTF-8'); ?>" />
  <?php else: ?>
    <meta name="description" content="Bela-Bela Institute Of Higher Learning offers practical ICT upskilling courses in software development, data, networking, cybersecurity, and digital skills." />
  <?php endif; ?>

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;800&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="css/style.css" />
  <?php if (!empty($meta_og_title) || !empty($meta_og_image) || !empty($meta_og_description)): ?>
    <!-- Open Graph -->
    <meta property="og:title" content="<?php echo htmlspecialchars($meta_og_title ?? ($meta_title ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
    <?php if (!empty($meta_og_description) || !empty($meta_description)): ?>
      <meta property="og:description" content="<?php echo htmlspecialchars($meta_og_description ?? ($meta_description ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
    <?php endif; ?>
    <?php if (!empty($meta_og_image)): ?>
      <meta property="og:image" content="<?php echo htmlspecialchars($meta_og_image, ENT_QUOTES, 'UTF-8'); ?>">
    <?php endif; ?>
  <?php endif; ?>
  <style>
    /* ================================
   HEADER (WHITE, MODERN, RESPONSIVE)
================================ */

    /* Container helper (if you don't already have it) */
    .container {
      width: min(1200px, 100% - 3rem);
      margin-inline: auto;
    }

    /* Announcement bar (optional styling) */
    .announce {
      background: #0b1220;
      color: #e2e8f0;
      font-family: 'Poppins', system-ui, sans-serif;
      font-size: 0.9rem;
    }

    .announce-inner {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 1rem;
      padding: 0.7rem 0;
    }

    .announce p {
      margin: 0;
    }

    .announce-link {
      color: #38bdf8;
      text-decoration: none;
      font-weight: 600;
    }

    .announce-link:hover {
      text-decoration: underline;
    }

    /* Header shell */
    .header {
      position: sticky;
      top: 0;
      z-index: 1000;
      background: #ffffff;
      border-bottom: 1px solid rgba(15, 23, 42, 0.08);
      font-family: 'Poppins', system-ui, sans-serif;
    }

    /* Header layout */
    .header-inner {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 1.2rem;
      padding: 1rem 0;
    }

    /* Logo */
    .header img {
      width: clamp(120px, 16vw, 180px);
      height: auto;
      display: block;
    }

    /* Nav base */
    .nav {
      display: flex;
      align-items: center;
      gap: 1rem;
    }

    /* Menu desktop */
    .nav-menu {
      list-style: none;
      display: flex;
      align-items: center;
      gap: 1.6rem;
      margin: 0;
      padding: 0;
    }

    .nav-menu a {
      color: #0f172a;
      text-decoration: none;
      font-size: 0.92rem;
      font-weight: 500;
      padding: 0.4rem 0;
      position: relative;
      transition: color 0.2s ease;
    }

    /* Subtle underline hover */
    .nav-menu a::after {
      content: '';
      position: absolute;
      left: 0;
      bottom: -6px;
      width: 0;
      height: 2px;
      background: #38bdf8;
      transition: width 0.25s ease;
    }

    .nav-menu a:hover {
      color: #0284c7;
    }

    .nav-menu a:hover::after {
      width: 100%;
    }

    /* Button styles (Apply) */
    .btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      border-radius: 12px;
      padding: 0.75rem 1.1rem;
      font-weight: 600;
      border: 1px solid rgba(2, 132, 199, 0.2);
      background: #0ea5e9;
      color: #fff !important;
      box-shadow: 0 10px 20px rgba(2, 132, 199, 0.12);
      transition: transform 0.15s ease, box-shadow 0.2s ease;
    }

    .btn:hover {
      transform: translateY(-1px);
      box-shadow: 0 14px 26px rgba(2, 132, 199, 0.18);
    }

    .btn-small {
      padding: 0.6rem 0.95rem;
      font-size: 0.9rem;
    }

    /* Hamburger button (hidden on desktop) */
    .nav-toggle {
      display: none;
      width: 44px;
      height: 44px;
      border-radius: 12px;
      border: 1px solid rgba(15, 23, 42, 0.12);
      background: #ffffff;
      cursor: pointer;
      padding: 10px;
    }

    .nav-toggle span {
      display: block;
      height: 2px;
      background: #0f172a;
      border-radius: 999px;
      margin: 6px 0;
      transition: transform 0.2s ease, opacity 0.2s ease;
    }

    /* ================================
   MOBILE MENU
================================ */
    @media (max-width: 900px) {
      .announce-inner {
        flex-direction: column;
        text-align: center;
      }

      .nav-toggle {
        display: inline-block;
      }

      /* Turn menu into dropdown panel */
      .nav-menu {
        position: absolute;
        top: 100%;
        right: 1.5rem;
        left: 1.5rem;
        background: #ffffff;
        border: 1px solid rgba(15, 23, 42, 0.10);
        border-radius: 16px;
        box-shadow: 0 20px 40px rgba(15, 23, 42, 0.12);
        padding: 0.9rem;
        display: none;
        /* hidden by default */
        flex-direction: column;
        align-items: stretch;
        gap: 0.2rem;
      }

      .nav-menu li {
        width: 100%;
      }

      .nav-menu a {
        width: 100%;
        padding: 0.9rem 0.9rem;
        border-radius: 12px;
      }

      .nav-menu a::after {
        display: none;
      }

      .nav-menu a:hover {
        background: rgba(14, 165, 233, 0.08);
        color: #0284c7;
      }

      .nav-menu .btn {
        width: 100%;
        margin-top: 0.35rem;
      }

      /* When menu is open (we'll toggle this class via JS) */
      .nav-menu.is-open {
        display: flex;
      }
    }

    /* Optional: animate hamburger into X when open */
    .nav-toggle.is-open span:nth-child(1) {
      transform: translateY(8px) rotate(45deg);
    }

    .nav-toggle.is-open span:nth-child(2) {
      opacity: 0;
    }

    .nav-toggle.is-open span:nth-child(3) {
      transform: translateY(-8px) rotate(-45deg);
    }
  </style>
</head>

<body>
  <!-- Top Announcement -->
  <div class="announce">
    <div class="container announce-inner">
      <p><strong>New Intake Open:</strong> Weekend + Evening classes available • Certificates included</p>
      <a class="announce-link" href="apply">Apply Now</a>
    </div>
  </div>

  <!-- Header -->
  <header class="header">
    <div class="container header-inner">
      <img src="images/logo.png" alt="logo image" width="20%">

      <nav class="nav" aria-label="Main navigation">
        <button class="nav-toggle" id="navToggle" aria-expanded="false" aria-controls="navMenu">
          <span></span><span></span><span></span>
        </button>

        <ul class="nav-menu" id="navMenu">
          <li><a href="./">Home</a></li>
          <li><a href="#courses">Courses</a></li>
          <li><a href="#why">Why Us</a></li>
          <li><a href="#how">How It Works</a></li>
          <li><a href="#testimonials">Testimonials</a></li>
          <li><a href="articles.php">News</a></li>
          <li><a href="#contact">Contact</a></li>
          <li><a class="btn btn-small" href="apply">Apply</a></li>
          <?php if (function_exists('is_logged_in') && is_logged_in()):
            $uid = auth_user()['id'];
            $cnt = 0;
            try {
              $cstmt = db()->prepare("SELECT COUNT(*) as c FROM notifications WHERE user_id = :uid AND is_read = 0");
              $cstmt->execute([':uid'=>$uid]);
              $crow = $cstmt->fetch(); $cnt = (int)($crow['c'] ?? 0);
            } catch (Throwable $e) { $cnt = 0; }
          ?>
          <li style="position:relative;">
            <a href="#" id="notifToggle" style="position:relative;">
              🔔 <span id="notifCount" style="background:#ef4444;color:#fff;border-radius:999px;padding:2px 6px;font-size:0.8rem;margin-left:6px;<?= $cnt? 'display:inline-block;':'display:none;' ?>"><?= $cnt ?></span>
            </a>
            <div id="notifDropdown" style="display:none;position:absolute;right:0;top:36px;width:360px;background:#fff;border:1px solid #e6eef7;border-radius:8px;box-shadow:0 10px 20px rgba(2,6,23,0.08);z-index:1500;padding:8px;">
              <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
                <strong>Notifications</strong>
                <button id="notifMarkAll" class="btn" style="padding:6px 8px;background:#64748b">Mark all read</button>
              </div>
              <div id="notifList">Loading…</div>
              <div style="text-align:center;margin-top:8px;"><a href="notifications.php">View all</a></div>
            </div>
          </li>
          <?php endif; ?>
        </ul>
      </nav>
    </div>
  </header>
  <script>
    // Mobile nav toggle
    const navToggle = document.getElementById("navToggle");
    const navMenu = document.getElementById("navMenu");

    if (navToggle && navMenu) {
      navToggle.addEventListener("click", () => {
        const open = navToggle.getAttribute("aria-expanded") === "true";
        navToggle.setAttribute("aria-expanded", String(!open));
        navToggle.classList.toggle("is-open");
        navMenu.classList.toggle("is-open");
      });

      // Close menu when a link is clicked (mobile)
      navMenu.querySelectorAll("a").forEach(a => {
        a.addEventListener("click", () => {
          navToggle.setAttribute("aria-expanded", "false");
          navToggle.classList.remove("is-open");
          navMenu.classList.remove("is-open");
        });
      });
    }
  </script>
  <script>
    // Notifications dropdown
    (function(){
      const toggle = document.getElementById('notifToggle');
      const dd = document.getElementById('notifDropdown');
      const list = document.getElementById('notifList');
      const countSpan = document.getElementById('notifCount');
      if (!toggle || !dd) return;
      toggle.addEventListener('click', async (e)=>{
        e.preventDefault();
        dd.style.display = dd.style.display === 'none' ? 'block' : 'none';
          if (dd.style.display === 'block') {
          const res = await fetch('/api/notifications.php?limit=10');
          const items = await res.json();
          list.innerHTML = '';
          if (!items.length) list.innerHTML = '<div style="padding:12px;color:#64748b;">No notifications</div>';
          items.forEach(n=>{
            const el = document.createElement('div');
            el.style.padding='8px'; el.style.borderBottom='1px solid #f1f5f9';
            el.innerHTML = '<div style="font-weight:700">'+(n.title||'')+'</div><div style="font-size:0.9rem;color:#475569;">'+(n.message||'')+'</div><div style="font-size:0.8rem;color:#94a3b8;margin-top:6px;">'+n.created_at+'</div>';
            el.addEventListener('click', ()=>{ window.location = n.link || '/'; });
            list.appendChild(el);
          });
          // nothing more here
        }
      });
    })();
    // mark all read handler
    const markAllBtn = document.getElementById('notifMarkAll');
    if (markAllBtn) {
      markAllBtn.addEventListener('click', async (e)=>{
        e.preventDefault();
        const r = await fetch('/api/notifications_mark_all_read.php', { method: 'POST' });
        const j = await r.json();
        document.getElementById('notifCount').style.display = 'none';
        document.getElementById('notifCount').textContent = '0';
        // reload list
        const res2 = await fetch('/api/notifications.php?limit=10');
        const items2 = await res2.json();
        const list2 = document.getElementById('notifList'); list2.innerHTML = '';
        if (!items2.length) list2.innerHTML = '<div style="padding:12px;color:#64748b;">No notifications</div>';
      });
    }
  </script>