<?php
require_once __DIR__ . '/includes/security_headers.php';
send_security_headers();
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/helpers.php';

$settings = get_settings();
$loggedIn = is_logged_in();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Epic Paper | Professional Pharmaceutical Paper Packaging</title>
  <meta name="description" content="Epic Paper Sri Lanka's trusted pharmaceutical paper packaging manufacturer. Medicine envelopes, pharmacy bags and speciality paper products.">
  <link rel="icon" href="src/images/favicon.png" type="image/svg+xml">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Jost:ital,wght@0,100..900;1,100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Raleway:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

  <link rel="stylesheet" href="src/css/style.css?v=3">
</head>

<body>

  <!-- ===================== HEADER ===================== -->
  <header class="site-header">
    <?php if ($loggedIn): ?>
      <div class="" style="background: var(--green);text-align: right;padding: 5px;">
        <div class="container">
          <a href="admin-login/index.php" style="color: var(--white);
    text-decoration: underline;
    text-underline-offset: 2px;
    opacity: .85;">Admin Panel</a>
        </div>

      </div>
    <?php endif; ?>
    <nav class="nav">
      <a href="#home" class="logo">
        <img src="src/images/logo.png" alt="" width="60" height="60">
        <span class="logo-text">
          <strong>EPIC PAPER</strong>
          <span>PACKAGING SOLUTIONS</span>
        </span>
      </a>

      <button class="nav-toggle" id="navToggle" aria-label="Toggle menu">&#9776;</button>

      <ul class="nav-links" id="navLinks">
        <li><a href="#home" class="active">Home</a></li>
        <li><a href="#shop">Shop</a></li>
        <li><a href="#why">Why Choose Us</a></li>
        <li><a href="#distribution">Distribution</a></li>
        <li><a href="#process">Process</a></li>
        <li><a href="#contact">Contact</a></li>
      </ul>

      <div class="nav-cta">
        <a class="btn btn-primary" href="https://wa.me/<?= h($settings['whatsapp_number']) ?>" target="_blank" rel="noopener">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
            <path d="M20.5 3.5A11 11 0 0 0 2.7 17.4L1.5 22.5l5.2-1.4A11 11 0 1 0 20.5 3.5Zm-8.5 17a9 9 0 0 1-4.6-1.3l-.3-.2-3.1.8.8-3-.2-.3A9 9 0 1 1 12 20.5Zm5-6.7c-.3-.1-1.6-.8-1.9-.9-.2-.1-.4-.1-.6.1-.2.3-.7.9-.8 1-.1.2-.3.2-.6.1-.3-.1-1.2-.5-2.3-1.5-.9-.8-1.4-1.7-1.6-2-.1-.3 0-.4.1-.6.1-.1.3-.3.4-.5.1-.1.2-.3.3-.5.1-.2 0-.4 0-.5-.1-.1-.6-1.5-.8-2-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.5.1-.7.3-.2.3-.9.9-.9 2.2s1 2.6 1.1 2.8c.1.2 2 3 4.7 4.2.7.3 1.2.5 1.6.6.7.2 1.3.2 1.8.1.5-.1 1.6-.7 1.9-1.3.2-.6.2-1.1.2-1.3-.1-.1-.3-.2-.6-.3Z" />
          </svg>
          Contact Us
        </a>
      </div>

    </nav>
  </header>

  <!-- ===================== HERO ===================== -->
  <section class="hero" id="home">
    <div class="container hero-grid">
      <div class="hero-copy">
        <span class="badge">23 YEARS OF EXCELLENCE</span>
        <h1>Professional <span class="accent">Pharmaceutical</span> Paper Packaging</h1>
        <p class="hero-desc">Trusted by pharmacies and healthcare businesses across</br> Sri Lanka for over two decades.</p>

        <div class="hero-features">
          <div class="hero-feature">
            <span class="icon"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M12 3 3 7l9 4 9-4-9-4Z" />
                <path d="M3 12l9 4 9-4" />
                <path d="M3 17l9 4 9-4" />
              </svg></span>
            <strong>High Quality</strong><span>Paper</span>
          </div>
          <div class="hero-feature">
            <span class="icon"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M12 3 4 6v6c0 5 3.5 8 8 9 4.5-1 8-4 8-9V6l-8-3Z" />
              </svg></span>
            <strong>Safe & Hygienic</strong><span>Packaging</span>
          </div>
          <div class="hero-feature">
            <span class="icon"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M12 21c-4-1-8-4-8-10 3 0 6 1 8 4V21Z" />
                <path d="M12 21c4-1 8-4 8-10-3 0-6 1-8 4" />
              </svg></span>
            <strong>Eco Friendly</strong><span>Solutions</span>
          </div>
          <div class="hero-feature">
            <span class="icon"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <circle cx="9" cy="8" r="3" />
                <path d="M2 20c0-3.5 3-6 7-6s7 2.5 7 6" />
                <circle cx="18" cy="8" r="2.4" />
                <path d="M16.5 14.2c2.6.4 4.5 2.4 4.5 5.8" />
              </svg></span>
            <strong class="years-feature"><span class="years-count" data-target="23">0</span>+ Years</strong><span>Experience</span>
          </div>
        </div>
      </div>


    </div>
  </section>

  <!-- ===================== PRODUCTS ===================== -->
  <section class="section" id="shop">
    <div class="container">
      <div class="section-head">
        <span class="eyebrow">Our Products</span>
        <h2>Pharmaceutical Paper Packaging Solutions</h2>
      </div>

      <div class="product-grid" id="productGrid">
        <!-- Product cards injected by main.js, loaded live from api/get_products.php -->
      </div>
    </div>
  </section>

  <!-- ===================== WHY CHOOSE US ===================== -->
  <section class="section bg-soft" id="why">
    <div class="container">
      <h2 style="text-align:center;margin-bottom:36px;">Why Choose <span style="color:var(--green)">Epic Paper</span>?</h2>

      <div class="why-grid">
        <div class="why-item">
          <span class="icon-circle"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
              <circle cx="12" cy="8" r="6" />
              <path d="M9 13.5 7 22l5-3 5 3-2-8.5" />
            </svg></span>
          <strong>Premium Quality</strong><span>High grade raw materials</span>
        </div>
        <div class="why-item">
          <span class="icon-circle"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
              <path d="M12 3 4 6v6c0 5 3.5 8 8 9 4.5-1 8-4 8-9V6l-8-3Z" />
            </svg></span>
          <strong>Safe & Reliable</strong><span>Hygienic & secure packaging</span>
        </div>
        <div class="why-item">
          <span class="icon-circle"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
              <path d="M12 21c-4-1-8-4-8-10 3 0 6 1 8 4V21Z" />
              <path d="M12 21c4-1 8-4 8-10-3 0-6 1-8 4" />
            </svg></span>
          <strong>Eco Friendly</strong><span>Environmentally responsible</span>
        </div>
        <div class="why-item">
          <span class="icon-circle"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
              <circle cx="12" cy="12" r="3" />
              <path d="M19 12a7 7 0 0 0-.1-1.2l2-1.6-2-3.4-2.3.9a7 7 0 0 0-2-1.2L14 3h-4l-.6 2.5a7 7 0 0 0-2 1.2l-2.3-.9-2 3.4 2 1.6A7 7 0 0 0 5 12c0 .4 0 .8.1 1.2l-2 1.6 2 3.4 2.3-.9c.6.5 1.3.9 2 1.2L10 21h4l.6-2.5c.7-.3 1.4-.7 2-1.2l2.3.9 2-3.4-2-1.6c.1-.4.1-.8.1-1.2Z" />
            </svg></span>
          <strong>Advanced Technology</strong><span>Modern machinery & skilled team</span>
        </div>
        <div class="why-item">
          <span class="icon-circle"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
              <circle cx="12" cy="8" r="4" />
              <path d="M4 21c0-4.4 3.6-7 8-7s8 2.6 8 7" />
            </svg></span>
          <strong>Customer Focus</strong><span>Dedicated support & service</span>
        </div>
        <div class="why-item">
          <span class="icon-circle"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
              <circle cx="12" cy="12" r="9" />
              <path d="M12 7v5l3.5 2" />
            </svg></span>
          <strong>On Time Delivery</strong><span>Always reliable & on time</span>
        </div>
      </div>
    </div>
  </section>

  <!-- ===================== DISTRIBUTION / MAP ===================== -->
  <section class="section distribution" id="distribution">
    <div class="container dist-grid">
      <div class="dist-info">
        <span class="eyebrow">Our Distribution Network</span>
        <h2>Islandwide <span class="accent">Distribution</span></h2>
        <div class="dist-underline"></div>
        <p>Delivering trust, quality and care to pharmacies in every corner of Sri Lanka.</p>

        <div class="stat-list">
          <div class="stat-row">
            <span class="icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <circle cx="9" cy="8" r="3" />
                <path d="M2 20c0-3.5 3-6 7-6s7 2.5 7 6" />
                <circle cx="18" cy="8" r="2.4" />
                <path d="M16.5 14.2c2.6.4 4.5 2.4 4.5 5.8" />
              </svg></span>
            <div><strong>1000+</strong><span>Happy Customers</span></div>
          </div>
          <div class="stat-row">
            <span class="icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M12 21s7-6.5 7-12a7 7 0 1 0-14 0c0 5.5 7 12 7 12Z" />
                <circle cx="12" cy="9" r="2.5" />
              </svg></span>
            <div><strong>25+</strong><span>Districts Covered</span></div>
          </div>
          <div class="stat-row">
            <span class="icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <rect x="1" y="8" width="13" height="9" rx="1" />
                <path d="M14 11h4l3 3v3h-7z" />
                <circle cx="6" cy="19" r="1.6" />
                <circle cx="17" cy="19" r="1.6" />
              </svg></span>
            <div><strong>Islandwide</strong><span>Reliable Delivery</span></div>
          </div>
        </div>
      </div>

      <div id="map" role="img" aria-label="Map of Epic Paper distributor locations across Sri Lanka"></div>
    </div>
  </section>

  <!-- ===================== PROCESS ===================== -->
  <section class="section" id="process">
    <div class="container">
      <h2 style="text-align:center;">Our Process</h2>
      <div class="process-track">
        <div class="process-step">
          <span class="icon-circle"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
              <path d="m14 3 7 7-11 11H3v-7L14 3Z" />
            </svg></span>
          <span class="num">01</span><strong>Design</strong><span>We understand your requirements</span>
        </div>
        <div class="process-step">
          <span class="icon-circle"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
              <path d="M6 9V3h12v6" />
              <rect x="6" y="14" width="12" height="7" />
              <rect x="3" y="9" width="18" height="8" rx="1" />
            </svg></span>
          <span class="num">02</span><strong>Printing</strong><span>High quality printing with precision</span>
        </div>
        <div class="process-step">
          <span class="icon-circle"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
              <circle cx="12" cy="12" r="3" />
              <path d="M19 12a7 7 0 0 0-.1-1.2l2-1.6-2-3.4-2.3.9a7 7 0 0 0-2-1.2L14 3h-4l-.6 2.5a7 7 0 0 0-2 1.2l-2.3-.9-2 3.4 2 1.6A7 7 0 0 0 5 12c0 .4 0 .8.1 1.2l-2 1.6 2 3.4 2.3-.9c.6.5 1.3.9 2 1.2L10 21h4l.6-2.5c.7-.3 1.4-.7 2-1.2l2.3.9 2-3.4-2-1.6c.1-.4.1-.8.1-1.2Z" />
            </svg></span>
          <span class="num">03</span><strong>Production</strong><span>Advanced machines ensure consistency</span>
        </div>
        <div class="process-step">
          <span class="icon-circle"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
              <rect x="1" y="8" width="13" height="9" rx="1" />
              <path d="M14 11h4l3 3v3h-7z" />
              <circle cx="6" cy="19" r="1.6" />
              <circle cx="17" cy="19" r="1.6" />
            </svg></span>
          <span class="num">04</span><strong>Quality Check</strong><span>Every product is checked for the best quality</span>
        </div>
        <div class="process-step">
          <span class="icon-circle"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
              <path d="M21 16V8a2 2 0 0 0-1-1.7l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.7l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z" />
              <path d="m3.3 7 8.7 5 8.7-5M12 22V12" />
            </svg></span>
          <span class="num">05</span><strong>Delivery</strong><span>Safe & timely delivery to your doorstep</span>
        </div>
      </div>
    </div>
  </section>

  <!-- ===================== FOOTER ===================== -->
  <footer class="site-footer" id="contact">
    <div class="container footer-grid">
      <div>
        <div class="footer-logo">
          <img src="src/images/logo.png" alt="" width="60" height="60">
          <span class="logo-text"><strong>EPIC PAPER</strong><span>PACKAGING SOLUTIONS</span></span>
        </div>
        <p>Sri Lanka's trusted partner for pharmaceutical paper packaging solutions.</p>
      </div>

      <div>
        <h4>Quick Links</h4>
        <div class="footer-links">
          <a href="#home">Home</a>
          <a href="#shop">Shop</a>
          <a href="#why">Why Choose Us</a>
          <a href="#process">Process</a>
          <a href="#distribution">Distribution</a>
          <a href="#contact">Contact Us</a>
        </div>
      </div>

      <div>
        <h4>Contact Us</h4>
        <div class="contact-line"><span class="icon">&#9742;</span> <?= h($settings['phone_1']) ?></div>
        <?php if ($settings['phone_2']): ?><div class="contact-line"><span class="icon">&#9742;</span> <?= h($settings['phone_2']) ?></div><?php endif; ?>
        <div class="contact-line"><span class="icon">&#9993;</span> <?= h($settings['email']) ?></div>
        <div class="contact-line"><span class="icon">&#128205;</span> <?= h($settings['address']) ?></div>
      </div>

      <div>
        <h4>Follow Us</h4>
        <div class="social-row">
          <a href="<?= $settings['facebook_url'] ? h($settings['facebook_url']) : '#' ?>" class="fb" aria-label="Facebook" target="_blank" rel="noopener">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
              <path d="M13.5 22v-8.5H16l.5-3.5h-3V7.7c0-1 .3-1.7 1.7-1.7H16V2.8C15.6 2.8 14.5 2.7 13.3 2.7c-2.6 0-4.4 1.6-4.4 4.5V10H6v3.5h2.9V22h4.6Z" />
            </svg>
          </a>
          <a href="https://wa.me/<?= h($settings['whatsapp_number']) ?>" class="wa" aria-label="WhatsApp" target="_blank" rel="noopener">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
              <path d="M20.5 3.5A11 11 0 0 0 2.7 17.4L1.5 22.5l5.2-1.4A11 11 0 1 0 20.5 3.5Zm-8.5 17a9 9 0 0 1-4.6-1.3l-.3-.2-3.1.8.8-3-.2-.3A9 9 0 1 1 12 20.5Z" />
            </svg>
          </a>
        </div>
      </div>
    </div>

    <div class="footer-bottom">© 2026 Epic Paper (Pvt) Ltd. All Rights Reserved.<?php if ($loggedIn): ?> · <a href="admin-login/index.php">Admin Panel</a><?php endif; ?></div>
  </footer>

  <!-- ===================== ORDER MODAL ===================== -->
  <div class="modal-overlay" id="orderModal">
    <div class="modal-box">
      <button class="modal-close" id="modalClose" aria-label="Close">&times;</button>

      <div class="modal-grid">
        <div class="modal-gallery">
          <div class="modal-main-image" id="modalMainImage">
            <span class="modal-size-badge" id="modalSizeBadge"></span>
          </div>
          <div class="modal-thumbs" id="modalThumbs"></div>
        </div>

        <div class="modal-body">
          <h3>Place Your Order</h3>
          <p class="modal-product" id="modalProductName">Product</p>
          <p class="modal-desc" id="modalProductDesc"></p>

          <form id="orderForm">
            <div class="field">
              <label for="custName">Full Name</label>
              <input type="text" id="custName" required placeholder="Your name">
            </div>
            <div class="field-row">
              <div class="field">
                <label for="custSize">Size</label>
                <select id="custSize" required></select>
              </div>
              <div class="field" id="modalColorField">
                <label for="custColor">Colour</label>
                <select id="custColor"></select>
              </div>
            </div>
            <div class="field-row">
              <div class="field">
                <label for="custPhone">Phone Number</label>
                <input type="tel" id="custPhone" required placeholder="07X XXX XXXX">
              </div>
              <div class="field">
                <label for="custQty">Quantity</label>
                <input type="text" id="custQty" required placeholder="e.g. 500 pcs">
              </div>
            </div>
            <div class="field">
              <label for="custLocation">Delivery Location / Pharmacy</label>
              <input type="text" id="custLocation" required placeholder="City / District">
            </div>
            <div class="field">
              <label for="custNote">Notes (optional)</label>
              <textarea id="custNote" placeholder="Anything else we should know..."></textarea>
            </div>

            <button type="submit" class="btn btn-primary modal-submit">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                <path d="M20.5 3.5A11 11 0 0 0 2.7 17.4L1.5 22.5l5.2-1.4A11 11 0 1 0 20.5 3.5Zm-8.5 17a9 9 0 0 1-4.6-1.3l-.3-.2-3.1.8.8-3-.2-.3A9 9 0 1 1 12 20.5Z" />
              </svg>
              Send Order via WhatsApp
            </button>
            <p class="wa-note">You'll be redirected to WhatsApp to confirm and send your order.</p>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Floating WhatsApp button shown on mobile only, see src/css/style.css -->
  <a href="#" class="wa-float" id="waFloat" aria-label="Contact us on WhatsApp" target="_blank" rel="noopener">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
      <path d="M20.5 3.5A11 11 0 0 0 2.7 17.4L1.5 22.5l5.2-1.4A11 11 0 1 0 20.5 3.5Zm-8.5 17a9 9 0 0 1-4.6-1.3l-.3-.2-3.1.8.8-3-.2-.3A9 9 0 1 1 12 20.5Zm5-6.7c-.3-.1-1.6-.8-1.9-.9-.2-.1-.4-.1-.6.1-.2.3-.7.9-.8 1-.1.2-.3.2-.6.1-.3-.1-1.2-.5-2.3-1.5-.9-.8-1.4-1.7-1.6-2-.1-.3 0-.4.1-.6.1-.1.3-.3.4-.5.1-.1.2-.3.3-.5.1-.2 0-.4 0-.5-.1-.1-.6-1.5-.8-2-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.5.1-.7.3-.2.3-.9.9-.9 2.2s1 2.6 1.1 2.8c.1.2 2 3 4.7 4.2.7.3 1.2.5 1.6.6.7.2 1.3.2 1.8.1.5-.1 1.6-.7 1.9-1.3.2-.6.2-1.1.2-1.3-.1-.1-.3-.2-.6-.3Z" />
    </svg>
    <span>Contact Us</span>
  </a>

  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <script src="src/js/config.js"></script>
  <script>
    SITE_CONFIG.WHATSAPP_NUMBER = "<?= h($settings['whatsapp_number']) ?>";
  </script>
  <script src="src/js/icons.js"></script>
  <script src="src/js/main.js?v=3"></script>
  <script src="src/js/map.js"></script>
</body>

</html>