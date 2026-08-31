<?php
/* ============================================
   SECURITY HEADERS
   Applied to every page. Kept deliberately conservative so it
   doesn't break the Google Fonts / Leaflet CDN assets the site uses.
   ============================================ */

function send_security_headers(): void {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('Referrer-Policy: strict-origin-when-cross-origin');
}
