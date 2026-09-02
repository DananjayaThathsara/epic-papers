/* ============================================
   SITE CONFIG
   Edit the values below to match your business.
   ============================================ */

const SITE_CONFIG = {
  // WhatsApp number that receives orders.
  // Format: country code + number, NO +, NO spaces, NO leading 0.
  // Example Sri Lanka number 077 123 4567  ->  94771234567
  WHATSAPP_NUMBER: "94771234567",

  // Public read-only API endpoints used by the main site (index.php).
  API_GET_PRODUCTS: "api/get_products.php",
  API_GET_DISTRIBUTORS: "api/get_distributors.php",
  API_SAVE_ORDER: "api/save_order.php",

  // Admin endpoints used only by admin-login/index.php, so these paths are
  // written relative to the /admin-login/ folder.
  ADMIN_GET_PRODUCTS: "../api/get_products.php",
  ADMIN_GET_PRODUCT: "api/get_product.php",
  ADMIN_GET_DISTRIBUTORS: "../api/get_distributors.php",
  ADMIN_SAVE_PRODUCT: "api/save_product.php",
  ADMIN_DELETE_PRODUCT: "api/delete_product.php",
  ADMIN_SAVE_DISTRIBUTOR: "api/save_distributor.php",
  ADMIN_DELETE_DISTRIBUTOR: "api/delete_distributor.php",
  ADMIN_GET_ORDERS: "api/get_orders.php",
  ADMIN_GET_SETTINGS: "../api/get_settings.php",
  ADMIN_SAVE_SETTINGS: "api/save_settings.php",

  // Map starting view (centered on Sri Lanka).
  MAP_CENTER: [7.8731, 80.7718],
  MAP_ZOOM: 7,
};
