/* ============================================
   MAP.JS Sri Lanka distributor map (Leaflet)
   Used on index.php (public view).
   ============================================ */

function makePinIcon() {
  return L.divIcon({
    className: "epic-pin",
    html: `<svg width="30" height="38" viewBox="0 0 30 38" xmlns="http://www.w3.org/2000/svg">
             <path d="M15 0C6.7 0 0 6.7 0 15c0 10.5 15 23 15 23s15-12.5 15-23C30 6.7 23.3 0 15 0Z" fill="#00c500"/>
             <circle cx="15" cy="15" r="6" fill="#ffffff"/>
           </svg>`,
    iconSize: [30, 38],
    iconAnchor: [15, 38],
    popupAnchor: [0, -34],
  });
}

async function loadDistributorPoints() {
  try {
    const res = await fetch(SITE_CONFIG.API_GET_DISTRIBUTORS, { cache: "no-store" });
    if (res.ok) {
      const data = await res.json();
      if (Array.isArray(data)) return data;
    }
  } catch (err) {
    console.warn("Could not load distributor points:", err);
  }
  return [];
}

async function initMap() {
  const mapEl = document.getElementById("map");
  if (!mapEl || typeof L === "undefined") return;

  const map = L.map("map", {
    center: SITE_CONFIG.MAP_CENTER,
    zoom: SITE_CONFIG.MAP_ZOOM,
    scrollWheelZoom: false,
    zoomControl: true, // gives the built-in + / - zoom buttons
  });

  L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
    maxZoom: 18,
  }).addTo(map);

  // let users scroll the page normally, but zoom the map once they click into it
  map.on("focus", () => map.scrollWheelZoom.enable());
  map.on("blur", () => map.scrollWheelZoom.disable());
  mapEl.addEventListener("click", () => map.scrollWheelZoom.enable());

  const points = await loadDistributorPoints();
  const pinIcon = makePinIcon();

  points.forEach((p) => {
    if (typeof p.lat !== "number" || typeof p.lng !== "number") return;
    L.marker([p.lat, p.lng], { icon: pinIcon })
      .addTo(map)
      .bindPopup(
        `<b>${escapeHtml(p.name || "Distributor")}</b>${p.district ? `<br>${escapeHtml(p.district)}` : ""}${p.phone ? `<br>${escapeHtml(p.phone)}` : ""}`,
      );
  });

  // Re-measure the map after layout/fonts settle, so it never renders
  // at the wrong size (a common cause of the map appearing to overlap
  // other elements on the page).
  setTimeout(() => map.invalidateSize(), 200);
  window.addEventListener("resize", () => map.invalidateSize());
}

initMap();
