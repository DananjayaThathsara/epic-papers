<?php
require_once __DIR__ . '/../includes/security_headers.php';
send_security_headers();
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';
require_login();
$adminUsername = current_admin_username();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Epic Paper Admin</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
  <link rel="icon" href="../src/images/favicon.png" type="image/svg+xml">
  <link rel="stylesheet" href="../src/css/style.css?v=3">
</head>

<body>

  <header class="site-header">
    <nav class="nav">
      <a href="../index.php" class="logo">
        <img src="../src/images/logo.png" alt="" width="60" height="60">
        <span class="logo-text"><strong>EPIC PAPER</strong><span>PACKAGING SOLUTIONS</span></span>
      </a>
      <div class="nav-cta admin-nav-cta">
        <span class="admin-user-badge">
          <span class="admin-user-dot"></span>
          Signed in as <strong><?= h($adminUsername) ?></strong>
        </span>
        <a class="btn btn-outline" href="../index.php">&larr; Back to Site</a>
        <a class="btn btn-outline admin-logout-btn" href="logout.php">Logout</a>
      </div>
    </nav>
  </header>

  <div class="admin-layout">

    <!-- ===== Sidebar ===== -->
    <aside class="admin-sidebar" id="adminSidebar">
      <nav class="admin-sidebar-nav">
        <button type="button" class="admin-side-link active" data-panel="products">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <rect x="3" y="7" width="18" height="13" rx="1.5" />
            <path d="M3 7l9-4 9 4" />
          </svg>
          Products
        </button>
        <button type="button" class="admin-side-link" data-panel="map">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <path d="M12 21s7-6.5 7-12a7 7 0 1 0-14 0c0 5.5 7 12 7 12Z" />
            <circle cx="12" cy="9" r="2.5" />
          </svg>
          Distributor Map
        </button>
        <button type="button" class="admin-side-link" data-panel="orders">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <path d="M4 7.5A2.5 2.5 0 0 1 6.5 5h11A2.5 2.5 0 0 1 20 7.5v9A2.5 2.5 0 0 1 17.5 19h-11A2.5 2.5 0 0 1 4 16.5v-9Z" />
            <path d="M8 9h8M8 12h8M8 15h5" />
          </svg>
          Orders
        </button>
        <button type="button" class="admin-side-link" data-panel="settings">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <circle cx="12" cy="12" r="3" />
            <path d="M19 12a7 7 0 0 0-.1-1.2l2-1.6-2-3.4-2.3.9a7 7 0 0 0-2-1.2L14 3h-4l-.6 2.5a7 7 0 0 0-2 1.2l-2.3-.9-2 3.4 2 1.6A7 7 0 0 0 5 12c0 .4 0 .8.1 1.2l-2 1.6 2 3.4 2.3-.9c.6.5 1.3.9 2 1.2L10 21h4l.6-2.5c.7-.3 1.4-.7 2-1.2l2.3.9 2-3.4-2-1.6c.1-.4.1-.8.1-1.2Z" />
          </svg>
          Contact & Settings
        </button>
      </nav>
    </aside>

    <!-- ===== Main content ===== -->
    <main class="admin-content">

      <!-- ---------- Products panel ---------- -->
      <section class="admin-panel active" id="panel-products">
        <div class="admin-wrap">
          <div class="admin-topbar">
            <h1>Products</h1>
          </div>
          <p>Add products with a real photo, and give each size (and colour, if it has one) its own photo too. Everything appears on the site immediately.</p>

          <div class="admin-card">
            <form id="productForm" enctype="multipart/form-data">
              <input type="hidden" id="prId" name="id" value="">
              <div class="field">
                <label for="prName">Product Name</label>
                <input type="text" id="prName" name="name" required placeholder="e.g. Printed Covers">
              </div>
              <div class="field">
                <label for="prDesc">Description</label>
                <input type="text" id="prDesc" name="desc" placeholder="Short description shown on the product card">
              </div>
              <div class="field">
                <label for="prIcon">Fallback Icon Style</label>
                <select id="prIcon" name="iconKey">
                  <option value="printed">Printed</option>
                  <option value="plain">Plain</option>
                  <option value="custom">Custom (colour swatch)</option>
                </select>
                <p class="hint">Used only where you haven't uploaded a real photo.</p>
              </div>
              <div class="field">
                <label for="prImage">Main Product Photo (optional)</label>
                <div id="prImagePreview"></div>
                <input type="file" id="prImage" name="mainImage" accept="image/*">
                <p class="hint" id="prImageHint" style="display:none;">Leave empty to keep the current photo.</p>
              </div>

              <div class="field">
                <label>Sizes</label>
                <div id="sizeRows"></div>
                <button type="button" class="btn btn-outline" id="addSizeRow" style="margin-top:4px;">+ Add Size</button>
              </div>

              <div class="field">
                <label>Colours (optional)</label>
                <div id="colorRows"></div>
                <button type="button" class="btn btn-outline" id="addColorRow" style="margin-top:4px;">+ Add Colour</button>
              </div>

              <div style="display:flex;gap:10px;margin-top:10px;">
                <button type="submit" class="btn btn-primary" style="flex:1;justify-content:center;" id="productSubmitBtn">Add Product</button>
                <button type="button" class="btn btn-outline" id="productCancelBtn" style="display:none;">Cancel</button>
              </div>
              <div class="status-msg" id="productStatusMsg"></div>
            </form>
          </div>

          <div class="admin-list">
            <h2>Current Products</h2>
            <div id="productList"></div>
          </div>
        </div>
      </section>

      <!-- ---------- Distributor map panel ---------- -->
      <section class="admin-panel" id="panel-map">
        <div class="admin-wrap">
          <div class="admin-topbar">
            <h1>Distributor Points</h1>
          </div>
          <p>Add distributor pins for the Islandwide Distribution map. Click anywhere on the map to drop a pin, fill in the details, and save it will appear on the public site's map right away.</p>

          <div class="admin-card">
            <div id="adminMap"></div>
            <p class="hint">Tip: click the map to set the location, or type coordinates manually below.</p>

            <form id="pointForm" style="margin-top:16px;">
              <input type="hidden" id="pId" value="">
              <div class="field-row">
                <div class="field">
                  <label for="pName">Distributor Name</label>
                  <input type="text" id="pName" required placeholder="e.g. Kandy Distributor">
                </div>
                <div class="field">
                  <label for="pDistrict">District</label>
                  <input type="text" id="pDistrict" placeholder="e.g. Kandy">
                </div>
              </div>
              <div class="field-row">
                <div class="field">
                  <label for="pPhone">Phone (optional)</label>
                  <input type="text" id="pPhone" placeholder="077 000 0000">
                </div>
                <div class="field">
                  <label for="pLat">Latitude</label>
                  <input type="text" id="pLat" required placeholder="7.2906">
                </div>
              </div>
              <div class="field">
                <label for="pLng">Longitude</label>
                <input type="text" id="pLng" required placeholder="80.6337">
              </div>

              <div style="display:flex;gap:10px;">
                <button type="submit" class="btn btn-primary" style="flex:1;justify-content:center;" id="pointSubmitBtn">Add Point to Map</button>
                <button type="button" class="btn btn-outline" id="pointCancelBtn" style="display:none;">Cancel</button>
              </div>
              <div class="status-msg" id="statusMsg"></div>
            </form>
          </div>

          <div class="admin-list">
            <h2>Current Distributors</h2>
            <div id="pointList"></div>
          </div>
        </div>
      </section>

      <!-- ---------- Orders panel ---------- -->
      <section class="admin-panel" id="panel-orders">
        <div class="admin-wrap">
          <div class="admin-topbar">
            <h1>Orders</h1>
          </div>
          <p>New customer order requests will appear here so you can track and follow up quickly.</p>

          <div class="admin-list" style="width:100%;">
            <div id="orderList"></div>
          </div>
        </div>
      </section>

      <!-- ---------- Settings panel ---------- -->
      <section class="admin-panel" id="panel-settings">
        <div class="admin-wrap">
          <div class="admin-topbar">
            <h1>Contact & Settings</h1>
          </div>
          <p>These details power the "Contact Us" button, footer, and WhatsApp ordering across the whole site.</p>

          <div class="admin-card">
            <form id="settingsForm">
              <div class="field">
                <label for="stWhatsapp">WhatsApp Number</label>
                <input type="text" id="stWhatsapp" placeholder="94771234567">
                <p class="hint">Country code + number, digits only no "+", spaces, or leading 0. Used for the Contact Us button, order messages, and the mobile floating button.</p>
              </div>
              <div class="field-row">
                <div class="field">
                  <label for="stPhone1">Phone 1</label>
                  <input type="text" id="stPhone1" placeholder="+94 77 123 4567">
                </div>
                <div class="field">
                  <label for="stPhone2">Phone 2 (optional)</label>
                  <input type="text" id="stPhone2" placeholder="+94 71 123 4567">
                </div>
              </div>
              <div class="field">
                <label for="stEmail">Email</label>
                <input type="email" id="stEmail" placeholder="info@epicpaper.lk">
              </div>
              <div class="field">
                <label for="stAddress">Address</label>
                <input type="text" id="stAddress" placeholder="Kegalle, Sri Lanka">
              </div>
              <div class="field">
                <label for="stFacebook">Facebook URL (optional)</label>
                <input type="text" id="stFacebook" placeholder="https://facebook.com/yourpage">
              </div>

              <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;">Save Settings</button>
              <div class="status-msg" id="settingsStatusMsg"></div>
            </form>
          </div>
        </div>
      </section>

    </main>
  </div>

  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <script>
    const CSRF_TOKEN = "<?= h(csrf_token()) ?>";
  </script>
  <script src="../src/js/config.js"></script>
  <script src="../src/js/icons.js"></script>
  <script src="../src/js/admin.js"></script>
</body>

</html>