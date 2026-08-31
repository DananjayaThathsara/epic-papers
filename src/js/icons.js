/* ============================================
   ICONS.JS shared fallback illustrations + HTML escaping helper
   Used whenever a product/size/colour has no uploaded photo.
   ============================================ */

/** Escapes text before it's inserted into innerHTML, to prevent stored XSS
    from product names, descriptions, distributor names, etc. */
function escapeHtml(str) {
  if (str === null || str === undefined) return "";
  return String(str).replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#39;");
}

const ICONS = {
  printed: `<svg viewBox="0 0 100 130" xmlns="http://www.w3.org/2000/svg">
    <path d="M14 40 L8 122 Q8 128 14 128 L86 128 Q92 128 92 122 L86 40Z" fill="#ffffff" stroke="#dfe8e1" stroke-width="1.5"/>
    <path d="M33 40 L36 20 Q37 12 47 12 L53 12 Q63 12 64 20 L67 40" fill="none" stroke="#2e8b47" stroke-width="4" stroke-linecap="round"/>
    <rect x="8" y="37" width="84" height="8" fill="#dcece1"/>
    <circle cx="50" cy="60" r="12" fill="none" stroke="#2e8b47" stroke-width="3"/>
    <path d="M45 60h10M50 55v10" stroke="#2e8b47" stroke-width="2.5" stroke-linecap="round"/>
    <rect x="20" y="82" width="60" height="12" rx="3" fill="#2e8b47"/>
    <line x1="26" y1="106" x2="74" y2="106" stroke="#e3ebe4" stroke-width="2"/>
  </svg>`,
  plain: `<svg viewBox="0 0 100 130" xmlns="http://www.w3.org/2000/svg">
    <path d="M14 40 L8 122 Q8 128 14 128 L86 128 Q92 128 92 122 L86 40Z" fill="#ffffff" stroke="#dfe8e1" stroke-width="1.5"/>
    <path d="M33 40 L36 20 Q37 12 47 12 L53 12 Q63 12 64 20 L67 40" fill="none" stroke="#2e8b47" stroke-width="4" stroke-linecap="round"/>
    <rect x="8" y="37" width="84" height="8" fill="#dcece1"/>
    <rect x="20" y="66" width="60" height="14" rx="3" fill="#2e8b47"/>
    <line x1="24" y1="94" x2="76" y2="94" stroke="#e3ebe4" stroke-width="2"/>
    <line x1="24" y1="104" x2="66" y2="104" stroke="#e3ebe4" stroke-width="2"/>
  </svg>`,
  custom: `<svg viewBox="0 0 100 130" xmlns="http://www.w3.org/2000/svg">
    <path d="M20 44h60l6 74a5 5 0 0 1-5 6H19a5 5 0 0 1-5-6Z" fill="#ffffff" stroke="#dfe8e1" stroke-width="1.5"/>
    <path d="M36 44v-8a14 14 0 0 1 28 0v8" fill="none" stroke="#2e8b47" stroke-width="4" stroke-linecap="round"/>
    <rect x="30" y="62" width="40" height="26" rx="4" fill="#2e8b47"/>
    <path d="M42 75h16M50 68v14" stroke="#fff" stroke-width="3" stroke-linecap="round"/>
    <circle cx="38" cy="108" r="7" fill="#ffffff" stroke="#c9d6cc" stroke-width="1.5"/>
    <circle cx="60" cy="108" r="7" fill="#c9a06a" stroke="#c9d6cc" stroke-width="1.5"/>
  </svg>`,
};
