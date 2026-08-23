import axios from "axios";

// API Base URL (your PHP backend)
const API_URL = "http://localhost/gameVerse/backend/api/";

// Create axios instance
const api = axios.create({
  baseURL: API_URL,
  withCredentials: true, // For sessions
  headers: {
    "Content-Type": "application/json",
  },
});

// ============================================
// AUTH API
// ============================================

export const register = (userData) => api.post("/register.php", userData);
export const login = (userData) => api.post("/login.php", userData);
export const logout = () => api.post("/logout.php");
export const getUser = () => api.get("/user.php");

// ============================================
// ITEMS API
// ============================================

export const getItems = (filters) => {
  const params = new URLSearchParams(filters).toString();
  return api.get(`/items.php?${params}`);
};
export const getItem = (id) => api.get(`/item.php?id=${id}`);

// ============================================
// PURCHASE API
// ============================================

export const purchaseItem = (itemId) =>
  api.post("/purchase.php", { item_id: itemId });

// ============================================
// INVENTORY API
// ============================================

export const getInventory = () => api.get("/inventory.php");

// ============================================
// TRANSACTIONS API
// ============================================

export const getTransactions = () => api.get("/transactions.php");

export default api;
