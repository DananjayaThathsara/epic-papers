/* ============================================
   ADMIN.JS sidebar navigation, products, distributor points
   ============================================ */

// Close sidebar when a nav link is clicked on mobile
const adminSideLinks = document.querySelectorAll(".admin-side-link");
adminSideLinks.forEach((link) => {
  link.addEventListener("click", () => {
    closeMobileSidebar();
  });
});

/* ---------- Sidebar tab switching ---------- */
const sideLinks = document.querySelectorAll(".admin-side-link");
const panels = document.querySelectorAll(".admin-panel");
let mapInitialized = false;

sideLinks.forEach((link) => {
  link.addEventListener("click", () => {
    const target = link.dataset.panel;

    sideLinks.forEach((l) => l.classList.toggle("active", l === link));
    panels.forEach((p) => p.classList.toggle("active", p.id === `panel-${target}`));

    if (target === "map") {
      if (!mapInitialized) {
        initMapPanel();
        mapInitialized = true;
      } else {
        setTimeout(() => adminMap && adminMap.invalidateSize(), 50);
      }
    }

    if (target === "settings" && !settingsLoaded) {
      loadSettings();
      settingsLoaded = true;
    }
  });
});

/* ============================================
   PRODUCTS
   ============================================ */
const productForm = document.getElementById("productForm");
const productList = document.getElementById("productList");
const productStatusMsg = document.getElementById("productStatusMsg");
const sizeRows = document.getElementById("sizeRows");
const colorRows = document.getElementById("colorRows");
const addSizeRowBtn = document.getElementById("addSizeRow");
const addColorRowBtn = document.getElementById("addColorRow");
const prIdInput = document.getElementById("prId");
const prImagePreview = document.getElementById("prImagePreview");
const prImageHint = document.getElementById("prImageHint");
const productSubmitBtn = document.getElementById("productSubmitBtn");
const productCancelBtn = document.getElementById("productCancelBtn");
let allProducts = [];

function showProductStatus(text, ok) {
  productStatusMsg.textContent = text;
  productStatusMsg.className = "status-msg show " + (ok ? "ok" : "err");
  setTimeout(() => productStatusMsg.classList.remove("show"), 4000);
}

/** existing = { label, image_path } to pre-fill when editing, or null for a blank row */
function addSizeRow(existing) {
  const row = document.createElement("div");
  row.className = "dyn-row";
  const label = existing ? escapeHtml(existing.label) : "";
  const existingImg = existing && existing.image_path ? escapeHtml(existing.image_path) : "";
  row.innerHTML = `
    ${existingImg ? `<span class="img-preview-thumb"><img src="../${existingImg}" alt=""></span>` : ""}
    <input type="text" name="sizeLabel[]" placeholder="e.g. Small" required value="${label}">
    <input type="hidden" name="sizeExistingImage[]" value="${existingImg}">
    <input type="file" name="sizeImage[]" accept="image/*" title="${existingImg ? "Choose a file to replace the current photo" : ""}">
    <button type="button" class="remove-row" aria-label="Remove size">&times;</button>
  `;
  sizeRows.appendChild(row);
}

/** existing = { label, hex, image_path } to pre-fill when editing, or null for a blank row */
function addColorRow(existing) {
  const row = document.createElement("div");
  row.className = "dyn-row";
  const label = existing ? escapeHtml(existing.label) : "";
  const hex = existing && existing.hex ? existing.hex : "#ffffff";
  const existingImg = existing && existing.image_path ? escapeHtml(existing.image_path) : "";
  row.innerHTML = `
    ${existingImg ? `<span class="img-preview-thumb"><img src="../${existingImg}" alt=""></span>` : ""}
    <input type="text" name="colorLabel[]" placeholder="e.g. White" required value="${label}">
    <input type="color" name="colorHex[]" value="${hex}">
    <input type="hidden" name="colorExistingImage[]" value="${existingImg}">
    <input type="file" name="colorImage[]" accept="image/*" title="${existingImg ? "Choose a file to replace the current photo" : ""}">
    <button type="button" class="remove-row" aria-label="Remove colour">&times;</button>
  `;
  colorRows.appendChild(row);
}

addSizeRowBtn.addEventListener("click", () => addSizeRow(null));
addColorRowBtn.addEventListener("click", () => addColorRow(null));
addSizeRow(null); // start with one size row so the form isn't empty

document.addEventListener("click", (e) => {
  const btn = e.target.closest(".remove-row");
  if (btn) btn.closest(".dyn-row").remove();
});

function productThumbHtml(p) {
  if (p.image_path) {
    return `<img src="../${escapeHtml(p.image_path)}" alt="${escapeHtml(p.name)}" style="width:100%;height:100%;object-fit:contain;">`;
  }
  return ICONS[p.icon_key] || ICONS.plain;
}

function renderProductList(products) {
  productList.innerHTML =
    products
      .map((p) => {
        const bits = [`${p.sizes.length} size${p.sizes.length === 1 ? "" : "s"}`];
        if (p.colors && p.colors.length) bits.push(`${p.colors.length} colour${p.colors.length === 1 ? "" : "s"}`);
        return `
      <div class="point-row">
        <span style="display:flex;align-items:center;gap:10px;">
          <span style="width:38px;height:38px;flex-shrink:0;background:var(--green-pale);border-radius:6px;overflow:hidden;display:flex;align-items:center;justify-content:center;">${productThumbHtml(p)}</span>
          <span><strong>${escapeHtml(p.name)}</strong> ${escapeHtml(bits.join(" · "))}</span>
        </span>
        <span style="display:flex;gap:14px;">
          <button data-id="${escapeHtml(String(p.id))}" class="edit-product-btn" style="background:none;border:none;color:var(--green);font-size:.8rem;font-weight:600;">Edit</button>
          <button data-id="${escapeHtml(String(p.id))}" class="delete-btn">Remove</button>
        </span>
      </div>`;
      })
      .join("") || `<p class="hint">No products yet add one above.</p>`;
}

async function loadProductsAdmin() {
  try {
    const res = await fetch(SITE_CONFIG.ADMIN_GET_PRODUCTS, { cache: "no-store" });
    if (!res.ok) throw new Error("backend not reachable");
    const products = await res.json();
    allProducts = products;
    renderProductList(products);
  } catch (err) {
    productList.innerHTML = `<p class="hint">Could not reach the backend (${SITE_CONFIG.ADMIN_GET_PRODUCTS}). Check your database connection see README.md.</p>`;
  }
}

function resetProductForm() {
  productForm.reset();
  prIdInput.value = "";
  sizeRows.innerHTML = "";
  colorRows.innerHTML = "";
  addSizeRow(null);
  prImagePreview.innerHTML = "";
  prImageHint.style.display = "none";
  productSubmitBtn.textContent = "Add Product";
  productCancelBtn.style.display = "none";
}

productCancelBtn.addEventListener("click", resetProductForm);

async function startEditProduct(id) {
  try {
    const res = await fetch(`${SITE_CONFIG.ADMIN_GET_PRODUCT}?id=${encodeURIComponent(id)}`, { cache: "no-store" });
    const p = await res.json();
    if (!res.ok) throw new Error(p.error || "Could not load product");

    prIdInput.value = p.id;
    document.getElementById("prName").value = p.name;
    document.getElementById("prDesc").value = p.description || "";
    document.getElementById("prIcon").value = p.icon_key || "plain";

    prImagePreview.innerHTML = p.image_path ? `<span class="img-preview-thumb"><img src="../${escapeHtml(p.image_path)}" alt=""></span>` : "";
    prImageHint.style.display = "";

    sizeRows.innerHTML = "";
    (p.sizes || []).forEach((s) => addSizeRow(s));
    if (!p.sizes || !p.sizes.length) addSizeRow(null);

    colorRows.innerHTML = "";
    (p.colors || []).forEach((c) => addColorRow(c));

    productSubmitBtn.textContent = "Update Product";
    productCancelBtn.style.display = "";

    document.getElementById("prName").scrollIntoView({ behavior: "smooth", block: "center" });
  } catch (err) {
    showProductStatus("Could not load product: " + err.message, false);
  }
}

productForm.addEventListener("submit", async (e) => {
  e.preventDefault();

  const nameVal = document.getElementById("prName").value.trim();
  const hasAnySize = [...sizeRows.querySelectorAll('input[name="sizeLabel[]"]')].some((i) => i.value.trim() !== "");

  if (!nameVal || !hasAnySize) {
    showProductStatus("Please fill in the product name and at least one size.", false);
    return;
  }

  const isEdit = !!prIdInput.value;
  const formData = new FormData(productForm);
  formData.append("csrf_token", CSRF_TOKEN);

  try {
    const res = await fetch(SITE_CONFIG.ADMIN_SAVE_PRODUCT, {
      method: "POST",
      body: formData,
    });
    const data = await res.json();
    if (!res.ok) throw new Error(data.error || "Save failed");

    showProductStatus(`"${nameVal}" ${isEdit ? "updated" : "added"}.`, true);
    resetProductForm();
    loadProductsAdmin();
  } catch (err) {
    showProductStatus("Could not save: " + err.message, false);
  }
});

productList.addEventListener("click", async (e) => {
  const editBtn = e.target.closest(".edit-product-btn");
  if (editBtn) {
    startEditProduct(editBtn.dataset.id);
    return;
  }

  const btn = e.target.closest(".delete-btn");
  if (!btn) return;
  if (!confirm("Remove this product?")) return;

  try {
    const res = await fetch(SITE_CONFIG.ADMIN_DELETE_PRODUCT, {
      method: "POST",
      headers: { "Content-Type": "application/json", "X-CSRF-Token": CSRF_TOKEN },
      body: JSON.stringify({ id: btn.dataset.id }),
    });
    const data = await res.json();
    if (!res.ok) throw new Error(data.error || "Delete failed");
    if (prIdInput.value === btn.dataset.id) resetProductForm();
    loadProductsAdmin();
  } catch (err) {
    showProductStatus("Could not delete: " + err.message, false);
  }
});

loadProductsAdmin();

/* ============================================
   DISTRIBUTOR MAP
   (initialized lazily see initMapPanel since its panel starts hidden)
   ============================================ */
let adminMap = null;
let tempMarker = null;
const markers = {}; // id -> leaflet marker

const pinIconAdmin = () =>
  L.divIcon({
    className: "epic-pin",
    html: `<svg width="28" height="36" viewBox="0 0 30 38" xmlns="http://www.w3.org/2000/svg">
           <path d="M15 0C6.7 0 0 6.7 0 15c0 10.5 15 23 15 23s15-12.5 15-23C30 6.7 23.3 0 15 0Z" fill="#2e8b47"/>
           <circle cx="15" cy="15" r="6" fill="#ffffff"/>
         </svg>`,
    iconSize: [28, 36],
    iconAnchor: [14, 36],
  });

const latInput = document.getElementById("pLat");
const lngInput = document.getElementById("pLng");
const idInput = document.getElementById("pId");
const statusMsg = document.getElementById("statusMsg");
const pointList = document.getElementById("pointList");
const pointForm = document.getElementById("pointForm");
const pointSubmitBtn = document.getElementById("pointSubmitBtn");
const pointCancelBtn = document.getElementById("pointCancelBtn");
let allPoints = [];

function showStatus(text, ok) {
  statusMsg.textContent = text;
  statusMsg.className = "status-msg show " + (ok ? "ok" : "err");
  setTimeout(() => statusMsg.classList.remove("show"), 3500);
}

function renderMarker(point) {
  const marker = L.marker([point.lat, point.lng], { icon: pinIconAdmin() })
    .addTo(adminMap)
    .bindPopup(`<b>${escapeHtml(point.name)}</b>${point.district ? `<br>${escapeHtml(point.district)}` : ""}`);
  markers[point.id] = marker;
}

function renderList(points) {
  pointList.innerHTML =
    points
      .map(
        (p) => `
    <div class="point-row">
      <span>${escapeHtml(p.name)}${p.district ? ` ${escapeHtml(p.district)}` : ""}</span>
      <span style="display:flex;gap:14px;">
        <button data-id="${escapeHtml(String(p.id))}" class="edit-point-btn" style="background:none;border:none;color:var(--green);font-size:.8rem;font-weight:600;">Edit</button>
        <button data-id="${escapeHtml(String(p.id))}" class="delete-btn">Remove</button>
      </span>
    </div>
  `,
      )
      .join("") || `<p class="hint">No distributor points yet add one above.</p>`;
}

function startEditPoint(id) {
  const p = allPoints.find((x) => String(x.id) === String(id));
  if (!p) return;

  idInput.value = p.id;
  document.getElementById("pName").value = p.name;
  document.getElementById("pDistrict").value = p.district || "";
  document.getElementById("pPhone").value = p.phone || "";
  latInput.value = p.lat;
  lngInput.value = p.lng;

  pointSubmitBtn.textContent = "Update Point";
  pointCancelBtn.style.display = "";

  if (tempMarker) {
    adminMap.removeLayer(tempMarker);
    tempMarker = null;
  }
  adminMap.panTo([p.lat, p.lng]);

  document.getElementById("pName").scrollIntoView({ behavior: "smooth", block: "center" });
}

function resetPointForm() {
  pointForm.reset();
  idInput.value = "";
  pointSubmitBtn.textContent = "Add Point to Map";
  pointCancelBtn.style.display = "none";
  if (tempMarker) {
    adminMap.removeLayer(tempMarker);
    tempMarker = null;
  }
}

pointCancelBtn.addEventListener("click", resetPointForm);

async function loadAllPoints() {
  try {
    const res = await fetch(SITE_CONFIG.ADMIN_GET_DISTRIBUTORS, { cache: "no-store" });
    if (!res.ok) throw new Error("backend not reachable");
    const points = await res.json();
    allPoints = points;

    Object.values(markers).forEach((m) => adminMap.removeLayer(m));
    for (const key in markers) delete markers[key];

    points.forEach(renderMarker);
    renderList(points);
  } catch (err) {
    pointList.innerHTML = `<p class="hint">Could not reach the backend (${SITE_CONFIG.ADMIN_GET_DISTRIBUTORS}). Check your database connection see README.md.</p>`;
  }
}

function initMapPanel() {
  adminMap = L.map("adminMap").setView(SITE_CONFIG.MAP_CENTER, SITE_CONFIG.MAP_ZOOM);
  L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
    attribution: "&copy; OpenStreetMap contributors",
    maxZoom: 18,
  }).addTo(adminMap);
  setTimeout(() => adminMap.invalidateSize(), 100);
  window.addEventListener("resize", () => adminMap.invalidateSize());

  adminMap.on("click", (e) => {
    const { lat, lng } = e.latlng;
    latInput.value = lat.toFixed(5);
    lngInput.value = lng.toFixed(5);

    if (tempMarker) adminMap.removeLayer(tempMarker);
    tempMarker = L.marker([lat, lng], { icon: pinIconAdmin(), opacity: 0.7 }).addTo(adminMap);
  });

  loadAllPoints();
}

pointForm.addEventListener("submit", async (e) => {
  e.preventDefault();

  const body = {
    id: idInput.value || undefined,
    name: document.getElementById("pName").value.trim(),
    district: document.getElementById("pDistrict").value.trim(),
    phone: document.getElementById("pPhone").value.trim(),
    lat: parseFloat(latInput.value),
    lng: parseFloat(lngInput.value),
  };

  if (!body.name || isNaN(body.lat) || isNaN(body.lng)) {
    showStatus("Please fill in name, latitude and longitude.", false);
    return;
  }

  const isEdit = !!body.id;

  try {
    const res = await fetch(SITE_CONFIG.ADMIN_SAVE_DISTRIBUTOR, {
      method: "POST",
      headers: { "Content-Type": "application/json", "X-CSRF-Token": CSRF_TOKEN },
      body: JSON.stringify(body),
    });
    const data = await res.json();
    if (!res.ok) throw new Error(data.error || "Save failed");

    showStatus(`"${body.name}" ${isEdit ? "updated" : "added to the map"}.`, true);
    resetPointForm();
    loadAllPoints();
  } catch (err) {
    showStatus("Could not save: " + err.message, false);
  }
});

pointList.addEventListener("click", async (e) => {
  const editBtn = e.target.closest(".edit-point-btn");
  if (editBtn) {
    startEditPoint(editBtn.dataset.id);
    return;
  }

  const btn = e.target.closest(".delete-btn");
  if (!btn) return;
  if (!confirm("Remove this distributor point?")) return;

  try {
    const res = await fetch(SITE_CONFIG.ADMIN_DELETE_DISTRIBUTOR, {
      method: "POST",
      headers: { "Content-Type": "application/json", "X-CSRF-Token": CSRF_TOKEN },
      body: JSON.stringify({ id: btn.dataset.id }),
    });
    const data = await res.json();
    if (!res.ok) throw new Error(data.error || "Delete failed");
    if (idInput.value === btn.dataset.id) resetPointForm();
    loadAllPoints();
  } catch (err) {
    showStatus("Could not delete: " + err.message, false);
  }
});

/* ============================================
   ORDERS (Paginated Table)
   ============================================ */
const orderList = document.getElementById("orderList");
let allOrdersData = [];
let currentOrdersPage = 1;
const ordersPerPage = 10;

function renderOrdersTable(orders, page = 1) {
  if (!Array.isArray(orders) || orders.length === 0) {
    orderList.innerHTML = `<p class="hint">No orders yet.</p>`;
    return;
  }

  const startIndex = (page - 1) * ordersPerPage;
  const endIndex = startIndex + ordersPerPage;
  const paginatedOrders = orders.slice(startIndex, endIndex);
  const totalPages = Math.ceil(orders.length / ordersPerPage);

  const tableHTML = `
    <div class="admin-table-wrapper">
      <table class="admin-table">
        <thead>
          <tr>
            <th>#ID</th>
            <th>Customer</th>
            <th>Product</th>
            <th>Size</th>
            <th>Colour</th>
            <th>Qty</th>
            <th>Phone</th>
            <th>Location</th>
            <th>Status</th>
            <th>Date</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          ${paginatedOrders
            .map((o) => {
              const status = (o.status || "new").toUpperCase();
              // Format phone for WhatsApp (add country code if needed)
              let waPhone = o.phone.replace(/\D/g, ""); // Remove non-digits
              if (!waPhone.startsWith("94")) {
                // If doesn't start with country code, assume Sri Lanka
                if (waPhone.startsWith("0")) {
                  waPhone = "94" + waPhone.substring(1);
                } else {
                  waPhone = "94" + waPhone;
                }
              }
              const waLink = `https://wa.me/${waPhone}`;
              return `
            <tr>
              <td class="cell-id">#${escapeHtml(String(o.id))}</td>
              <td class="cell-text">${escapeHtml(o.customer_name)}</td>
              <td class="cell-text">${escapeHtml(o.product_name)}</td>
              <td class="cell-text">${escapeHtml(o.size)}</td>
              <td class="cell-text">${o.color ? escapeHtml(o.color) : "–"}</td>
              <td class="cell-text">${escapeHtml(o.quantity)}</td>
              <td class="cell-text" title="${escapeHtml(o.phone)}">${escapeHtml(o.phone.length > 12 ? o.phone.substring(0, 12) : o.phone)}</td>
              <td class="cell-text" title="${escapeHtml(o.location)}">${escapeHtml(o.location.length > 15 ? o.location.substring(0, 15) + "..." : o.location)}</td>
              <td><span class="cell-status">${escapeHtml(status)}</span></td>
              <td class="cell-text" style="font-size:.8rem;color:var(--gray-light);">${escapeHtml(o.created_at ? o.created_at.split(" ")[0] : "")}</td>
              <td>
                <a href="${escapeHtml(waLink)}" target="_blank" rel="noopener" class="wa-btn" title="Send WhatsApp message to ${escapeHtml(o.customer_name)}">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M20.5 3.5A11 11 0 0 0 2.7 17.4L1.5 22.5l5.2-1.4A11 11 0 1 0 20.5 3.5Zm-8.5 17a9 9 0 0 1-4.6-1.3l-.3-.2-3.1.8.8-3-.2-.3A9 9 0 1 1 12 20.5Z" />
                  </svg>
                  Message
                </a>
              </td>
            </tr>
          `;
            })
            .join("")}
        </tbody>
      </table>
    </div>
    ${totalPages > 1 ? renderPagination(page, totalPages) : ""}
  `;

  orderList.innerHTML = tableHTML;

  // Attach pagination event listeners
  document.querySelectorAll(".pagination button, .pagination .page-num").forEach((el) => {
    el.addEventListener("click", (e) => {
      e.preventDefault();
      let nextPage = page;
      if (el.dataset.action === "prev") {
        nextPage = Math.max(1, page - 1);
      } else if (el.dataset.action === "next") {
        nextPage = Math.min(totalPages, page + 1);
      } else if (el.classList.contains("page-num")) {
        nextPage = parseInt(el.dataset.page, 10);
      }
      if (nextPage !== page) {
        currentOrdersPage = nextPage;
        renderOrdersTable(orders, nextPage);
      }
    });
  });
}

function renderPagination(currentPage, totalPages) {
  let pageButtons = [];
  const maxPages = 5;
  let startPage = Math.max(1, currentPage - Math.floor(maxPages / 2));
  let endPage = Math.min(totalPages, startPage + maxPages - 1);
  if (endPage - startPage < maxPages - 1) {
    startPage = Math.max(1, endPage - maxPages + 1);
  }

  if (startPage > 1) {
    pageButtons.push(`<button data-action="prev">← Prev</button>`);
    if (startPage > 1) pageButtons.push(`<span class="page-num" data-page="1">1</span>`);
    if (startPage > 2) pageButtons.push(`<span style="color:var(--gray-light);">...</span>`);
  }

  for (let i = startPage; i <= endPage; i++) {
    pageButtons.push(`
      <span class="page-num ${i === currentPage ? "active" : ""}" data-page="${i}">${i}</span>
    `);
  }

  if (endPage < totalPages) {
    if (endPage < totalPages - 1) pageButtons.push(`<span style="color:var(--gray-light);">...</span>`);
    if (endPage < totalPages) pageButtons.push(`<span class="page-num" data-page="${totalPages}">${totalPages}</span>`);
    pageButtons.push(`<button data-action="next">Next →</button>`);
  }

  return `
    <div class="pagination">
      ${pageButtons.join("")}
      <span class="pagination-info">Page ${currentPage} of ${totalPages}</span>
    </div>
  `;
}

async function loadOrdersAdmin() {
  try {
    const res = await fetch(SITE_CONFIG.ADMIN_GET_ORDERS, { cache: "no-store" });
    if (!res.ok) throw new Error("backend not reachable");
    const orders = await res.json();
    allOrdersData = Array.isArray(orders) ? orders : [];
    renderOrdersTable(allOrdersData, currentOrdersPage);
  } catch (err) {
    orderList.innerHTML = `<p class="hint">Could not load orders (${SITE_CONFIG.ADMIN_GET_ORDERS}).</p>`;
  }
}

loadOrdersAdmin();

/* ============================================
   SETTINGS (contact details, WhatsApp number)
   ============================================ */
let settingsLoaded = false;
const settingsForm = document.getElementById("settingsForm");
const settingsStatusMsg = document.getElementById("settingsStatusMsg");

function showSettingsStatus(text, ok) {
  settingsStatusMsg.textContent = text;
  settingsStatusMsg.className = "status-msg show " + (ok ? "ok" : "err");
  setTimeout(() => settingsStatusMsg.classList.remove("show"), 4000);
}

async function loadSettings() {
  try {
    const res = await fetch(SITE_CONFIG.ADMIN_GET_SETTINGS, { cache: "no-store" });
    if (!res.ok) throw new Error("backend not reachable");
    const s = await res.json();
    document.getElementById("stWhatsapp").value = s.whatsapp_number || "";
    document.getElementById("stPhone1").value = s.phone_1 || "";
    document.getElementById("stPhone2").value = s.phone_2 || "";
    document.getElementById("stEmail").value = s.email || "";
    document.getElementById("stAddress").value = s.address || "";
    document.getElementById("stFacebook").value = s.facebook_url || "";
  } catch (err) {
    showSettingsStatus("Could not load current settings: " + err.message, false);
  }
}

settingsForm.addEventListener("submit", async (e) => {
  e.preventDefault();

  const body = {
    whatsapp_number: document.getElementById("stWhatsapp").value.trim(),
    phone_1: document.getElementById("stPhone1").value.trim(),
    phone_2: document.getElementById("stPhone2").value.trim(),
    email: document.getElementById("stEmail").value.trim(),
    address: document.getElementById("stAddress").value.trim(),
    facebook_url: document.getElementById("stFacebook").value.trim(),
  };

  try {
    const res = await fetch(SITE_CONFIG.ADMIN_SAVE_SETTINGS, {
      method: "POST",
      headers: { "Content-Type": "application/json", "X-CSRF-Token": CSRF_TOKEN },
      body: JSON.stringify(body),
    });

    const contentType = res.headers.get("content-type") || "";
    if (!contentType.includes("application/json")) {
      const text = await res.text();
      throw new Error((text || "Server returned an unexpected response.").replace(/\s+/g, " ").slice(0, 180));
    }

    const data = await res.json();
    if (!res.ok) throw new Error(data.error || "Save failed");

    showSettingsStatus("Settings saved.", true);
  } catch (err) {
    showSettingsStatus("Could not save: " + err.message, false);
  }
});
