/* ============================================
   MAIN.JS navigation, products, order modal
   ============================================ */

/* ---------- Mobile nav toggle ---------- */
const navToggle = document.getElementById("navToggle");
const navLinks = document.getElementById("navLinks");
if (navToggle) {
  navToggle.addEventListener("click", () => navLinks.classList.toggle("open"));
  navLinks.querySelectorAll("a").forEach((a) => a.addEventListener("click", () => navLinks.classList.remove("open")));
}

/* ---------- Floating WhatsApp button (mobile) ---------- */
const waFloat = document.getElementById("waFloat");
if (waFloat) {
  waFloat.href = `https://wa.me/${SITE_CONFIG.WHATSAPP_NUMBER}`;
}

/* ---------- Product catalogue ----------
   Products, sizes, colours and photos are all managed from the admin
   panel (admin/index.php) and stored in MySQL. Fallback illustrations
   (used when a product/size/colour has no uploaded photo) live in
   src/js/icons.js, loaded before this file. */
let PRODUCTS = [];

/** Returns the HTML for a product's thumbnail: a real photo if one was
    uploaded, otherwise the fallback illustration for its icon style. */
function productThumbHtml(p) {
  if (p.image_path) {
    return `<img src="${escapeHtml(p.image_path)}" alt="${escapeHtml(p.name)}" style="width:100%;height:100%;object-fit:contain;">`;
  }
  return ICONS[p.icon_key] || ICONS.plain;
}

async function loadProducts() {
  try {
    const res = await fetch(SITE_CONFIG.API_GET_PRODUCTS, { cache: "no-store" });
    if (!res.ok) throw new Error("API returned " + res.status);
    const data = await res.json();
    return Array.isArray(data) ? data : [];
  } catch (err) {
    console.error("Could not load products:", err);
    return [];
  }
}

function renderProductGrid() {
  const productGrid = document.getElementById("productGrid");
  if (!productGrid) return;

  if (!PRODUCTS.length) {
    productGrid.innerHTML = `<p style="color:var(--gray-light)">Products could not be loaded. Check that the database is set up (see README.md).</p>`;
    return;
  }

  productGrid.innerHTML = PRODUCTS.map(
    (p) => `
    <div class="product-card">
      <div class="product-thumb">${productThumbHtml(p)}</div>
      <h3>${escapeHtml(p.name)}</h3>
      <p>${escapeHtml(p.description || "")}</p>
      <button class="btn btn-outline order-btn" data-product="${escapeHtml(String(p.id))}">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2 3h2l2.6 12.4a2 2 0 0 0 2 1.6h8.8a2 2 0 0 0 2-1.6L21 7H6"/></svg>
        Order Now
      </button>
    </div>
  `,
  ).join("");
}

(async () => {
  PRODUCTS = await loadProducts();
  renderProductGrid();
})();

/* ---------- Order modal ---------- */
const orderModal = document.getElementById("orderModal");
const modalClose = document.getElementById("modalClose");
const modalProductName = document.getElementById("modalProductName");
const modalProductDesc = document.getElementById("modalProductDesc");
const modalMainImage = document.getElementById("modalMainImage");
const modalThumbs = document.getElementById("modalThumbs");
const modalColorField = document.getElementById("modalColorField");
const custSize = document.getElementById("custSize");
const custColor = document.getElementById("custColor");
const orderForm = document.getElementById("orderForm");
let currentProduct = null;
let gallerySlides = [];
let activeSlide = 0;

function fillDropdown(select, options) {
  select.innerHTML = options.map((o) => `<option value="${o}">${o}</option>`).join("");
}

function hasColors(p) {
  return !!(p.colors && p.colors.length);
}

/* A size-guide illustration: three bag icons drawn at their real relative
   size (small/medium/large), sharing a baseline the selected size is
   filled green and labelled bold so it's obvious at a glance.
   Used only when a size has no uploaded photo. */
function buildSizeDiagram(sizes, selected) {
  const dims = [
    { w: 26, h: 34 }, // small
    { w: 34, h: 48 }, // medium
    { w: 42, h: 64 }, // large
  ];
  const baseline = 112;
  const centers = [32, 72, 112];

  const bags = sizes
    .map((s, i) => {
      const { w, h } = dims[i] || dims[1];
      const cx = centers[i];
      const top = baseline - h;
      const hw = w * 0.32;
      const active = s === selected;
      const fill = active ? "#2e8b47" : "#ffffff";
      const stroke = active ? "#155724" : "#c9d6cc";

      return `
      <path d="M${cx - hw} ${top} Q${cx - hw} ${top - 11} ${cx} ${top - 11} Q${cx + hw} ${top - 11} ${cx + hw} ${top}"
            fill="none" stroke="${active ? "#2e8b47" : "#c9d6cc"}" stroke-width="2.5" stroke-linecap="round"/>
      <path d="M${cx - w / 2} ${top} L${cx - w / 2 - 2} ${baseline} Q${cx - w / 2 - 2} ${baseline + 4} ${cx - w / 2 + 3} ${baseline + 4}
               L${cx + w / 2 - 3} ${baseline + 4} Q${cx + w / 2 + 2} ${baseline + 4} ${cx + w / 2 + 2} ${baseline} L${cx + w / 2} ${top} Z"
            fill="${fill}" stroke="${stroke}" stroke-width="2"/>
      <text x="${cx}" y="${baseline + 20}" font-family="Inter" font-size="9.5" font-weight="${active ? 700 : 500}"
            fill="${active ? "#155724" : "#8a9791"}" text-anchor="middle">${escapeHtml(s)}</text>`;
    })
    .join("");

  return `<svg viewBox="0 0 144 140" xmlns="http://www.w3.org/2000/svg">
    <line x1="8" y1="${baseline + 4.5}" x2="136" y2="${baseline + 4.5}" stroke="#e3ebe4" stroke-width="1.5"/>
    ${bags}
  </svg>`;
}

/* A colour swatch illustration, selected colour ringed. Uses each colour's
   real hex if the admin set one. Used only when a colour has no uploaded photo. */
function buildColorSwatch(colors, hexes, selected) {
  const circles = colors
    .map((c, i) => {
      const active = c === selected;
      const cx = 35 + i * 60;
      const fill = hexes[i] || "#e3ebe4";
      return `
      <circle cx="${cx}" cy="60" r="24" fill="${fill}" stroke="#c9d6cc" stroke-width="1.5"/>
      ${active ? `<circle cx="${cx}" cy="60" r="30" fill="none" stroke="#2e8b47" stroke-width="2.5"/>` : ""}
      <text x="${cx}" y="102" font-family="Inter" font-size="9" font-weight="${active ? 700 : 500}"
            fill="${active ? "#155724" : "#8a9791"}" text-anchor="middle">${escapeHtml(c)}</text>`;
    })
    .join("");
  return `<svg viewBox="0 0 130 130" xmlns="http://www.w3.org/2000/svg">${circles}</svg>`;
}

/** Wraps SVG markup or builds an <img> tag for a real photo, for use as a gallery slide. */
function slideMedia(imagePath, fallbackSvg, altText) {
  if (imagePath) {
    return `<img src="${escapeHtml(imagePath)}" alt="${escapeHtml(altText)}" style="width:100%;height:100%;object-fit:contain;">`;
  }
  return fallbackSvg;
}

function buildSlides() {
  gallerySlides = [
    { label: "Product", svg: slideMedia(currentProduct.image_path, ICONS[currentProduct.icon_key] || ICONS.plain, currentProduct.name) },
    { label: "Sizes", svg: buildSizeSlide() },
  ];
  if (hasColors(currentProduct)) {
    gallerySlides.push({ label: "Colours", svg: buildColorSlide() });
  }
}

function buildSizeSlide() {
  const idx = currentProduct.sizes.indexOf(custSize.value);
  const img = idx > -1 ? currentProduct.sizeImages[idx] : null;
  if (img) return slideMedia(img, "", `${currentProduct.name} ${custSize.value}`);
  return buildSizeDiagram(currentProduct.sizes, custSize.value);
}

function buildColorSlide() {
  const idx = currentProduct.colors.indexOf(custColor.value);
  const img = idx > -1 ? currentProduct.colorImages[idx] : null;
  if (img) return slideMedia(img, "", `${currentProduct.name} ${custColor.value}`);
  return buildColorSwatch(currentProduct.colors, currentProduct.colorHex || [], custColor.value);
}

function updateBadgeText() {
  const badge = document.getElementById("modalSizeBadge");
  if (!badge) return;
  let text = `Size: ${custSize.value}`;
  if (hasColors(currentProduct)) text += ` · ${custColor.value}`;
  badge.textContent = text;
}

function renderGallery() {
  modalMainImage.innerHTML = gallerySlides[activeSlide].svg + `<span class="modal-size-badge" id="modalSizeBadge"></span>`;
  updateBadgeText();

  modalThumbs.innerHTML = gallerySlides
    .map(
      (s, i) => `
    <button type="button" class="modal-thumb ${i === activeSlide ? "active" : ""}" data-slide="${i}" aria-label="${s.label}">
      ${s.svg}
    </button>
  `,
    )
    .join("");
}

function refreshDynamicSlides() {
  // Rebuild the size/colour illustration slides so they reflect the current dropdown picks
  gallerySlides[1].svg = buildSizeSlide();
  if (hasColors(currentProduct)) {
    gallerySlides[2].svg = buildColorSlide();
  }
  renderGallery();
}

modalThumbs.addEventListener("click", (e) => {
  const btn = e.target.closest(".modal-thumb");
  if (!btn) return;
  activeSlide = Number(btn.dataset.slide);
  renderGallery();
});

custSize.addEventListener("change", refreshDynamicSlides);
custColor.addEventListener("change", refreshDynamicSlides);

function openOrderModal(productId) {
  currentProduct = PRODUCTS.find((p) => String(p.id) === String(productId));
  if (!currentProduct) return;

  modalProductName.textContent = currentProduct.name;
  modalProductDesc.textContent = currentProduct.description || "";

  fillDropdown(custSize, currentProduct.sizes);

  if (hasColors(currentProduct)) {
    fillDropdown(custColor, currentProduct.colors);
    modalColorField.style.display = "";
    custColor.required = true;
  } else {
    modalColorField.style.display = "none";
    custColor.required = false;
  }

  activeSlide = 0;
  buildSlides();
  renderGallery();

  orderModal.classList.add("open");
  document.body.style.overflow = "hidden";
}

function closeOrderModal() {
  orderModal.classList.remove("open");
  document.body.style.overflow = "";
  orderForm.reset();
}

document.addEventListener("click", (e) => {
  const btn = e.target.closest(".order-btn");
  if (btn) openOrderModal(btn.dataset.product);
});

modalClose.addEventListener("click", closeOrderModal);
orderModal.addEventListener("click", (e) => {
  if (e.target === orderModal) closeOrderModal();
});
document.addEventListener("keydown", (e) => {
  if (e.key === "Escape" && orderModal.classList.contains("open")) closeOrderModal();
});

/* ---------- Submit order -> save DB -> WhatsApp ---------- */
orderForm.addEventListener("submit", async (e) => {
  e.preventDefault();
  if (!currentProduct) return;

  const payload = {
    product_id: Number(currentProduct.id),
    product_name: currentProduct.name,
    size: custSize.value,
    color: hasColors(currentProduct) ? custColor.value : "",
    quantity: document.getElementById("custQty").value.trim(),
    customer_name: document.getElementById("custName").value.trim(),
    phone: document.getElementById("custPhone").value.trim(),
    location: document.getElementById("custLocation").value.trim(),
    notes: document.getElementById("custNote").value.trim(),
  };

  try {
    const res = await fetch(SITE_CONFIG.API_SAVE_ORDER, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(payload),
    });

    const contentType = res.headers.get("content-type") || "";
    if (!contentType.includes("application/json")) {
      const text = await res.text();
      throw new Error((text || "Order save failed").replace(/\s+/g, " ").slice(0, 180));
    }

    const data = await res.json();
    if (!res.ok) throw new Error(data.error || "Order save failed");

    const name = payload.customer_name;
    const phone = payload.phone;
    const size = payload.size;
    const color = payload.color || null;
    const qty = payload.quantity;
    const location = payload.location;
    const note = payload.notes;

    const lines = ["*New Order Epic Paper*", `Product: ${currentProduct.name}`, `Size: ${size}`];
    if (color) lines.push(`Colour: ${color}`);
    lines.push(`Quantity: ${qty}`, `Name: ${name}`, `Phone: ${phone}`, `Location: ${location}`);
    if (note) lines.push(`Notes: ${note}`);

    const message = encodeURIComponent(lines.join("\n"));
    const waUrl = `https://wa.me/${SITE_CONFIG.WHATSAPP_NUMBER}?text=${message}`;

    window.open(waUrl, "_blank");
    closeOrderModal();
  } catch (err) {
    alert(err.message || "Could not save order.");
  }
});
