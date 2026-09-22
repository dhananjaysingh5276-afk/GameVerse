import React from "react";
import {
  BrowserRouter as Router,
  Routes,
  Route,
  Navigate,
} from "react-router-dom";
import { AuthProvider, useAuth } from "./context/AuthContext";
import ProtectedRoute from "./components/common/ProtectedRoute";
import Navbar from "./components/common/Navbar";

// Pages
import Login from "./pages/Login";
import Register from "./pages/Register";
import Dashboard from "./pages/Dashboard";
import Marketplace from "./pages/Marketplace";
import Inventory from "./pages/Inventory";
import TransactionHistory from "./pages/TransactionHistory";
import ItemDetail from "./pages/ItemDetail";
// Admin pages
import AdminLogin from "./pages/admin/AdminLogin";
import AdminDashboard from "./pages/admin/AdminDashboard";
import ManageGames from "./pages/admin/ManageGames";
import ManageItems from "./pages/admin/ManageItems";
import AddGame from "./pages/admin/AddGame";
import AddItem from "./pages/admin/AddItem";
import EditGame from "./pages/admin/EditGame";
import EditItems from "./pages/admin/EditItems";
import AdminTransactions from "./pages/admin/AdminTransactions";
// Styles
import "./styles/App.css";

const AppContent = () => {
  const { isAuthenticated } = useAuth();

  return (
    <div className="app">
      {isAuthenticated && <Navbar />}
      <Routes>
        <Route path="/login" element={<Login />} />
        <Route path="/register" element={<Register />} />
        <Route
          path="/dashboard"
          element={
            <ProtectedRoute>
              <Dashboard />
            </ProtectedRoute>
          }
        />
        <Route
          path="/marketplace"
          element={
            <ProtectedRoute>
              <Marketplace />
            </ProtectedRoute>
          }
        />
        <Route
          path="/item/:id"
          element={
            <ProtectedRoute>
              <ItemDetail />
            </ProtectedRoute>
          }
        />
        <Route
          path="/inventory"
          element={
            <ProtectedRoute>
              <Inventory />
            </ProtectedRoute>
          }
        />
        <Route
          path="/transactions"
          element={
            <ProtectedRoute>
              <TransactionHistory />
            </ProtectedRoute>
          }
        />
        {/* ============================================================
            ADMIN (everything related to the admin login)
        ============================================================ */}
        <Route path="/admin/login" element={<AdminLogin />} />
        <Route
          path="/admin"
          element={
            <ProtectedRoute adminOnly>
              <AdminDashboard />
            </ProtectedRoute>
          }
        />
        <Route
          path="/admin/games"
          element={
            <ProtectedRoute adminOnly>
              <ManageGames />
            </ProtectedRoute>
          }
        />
        <Route
          path="/admin/games/add"
          element={
            <ProtectedRoute adminOnly>
              <AddGame />
            </ProtectedRoute>
          }
        />
        <Route
          path="/admin/games/edit/:id"
          element={
            <ProtectedRoute adminOnly>
              <EditGame />
            </ProtectedRoute>
          }
        />
        <Route
          path="/admin/items"
          element={
            <ProtectedRoute adminOnly>
              <ManageItems />
            </ProtectedRoute>
          }
        />
        <Route
          path="/admin/items/add"
          element={
            <ProtectedRoute adminOnly>
              <AddItem />
            </ProtectedRoute>
          }
        />
        <Route
          path="/admin/items/edit/:id"
          element={
            <ProtectedRoute adminOnly>
              <EditItems />
            </ProtectedRoute>
          }
        />
        <Route
          path="/admin/transactions"
          element={
            <ProtectedRoute adminOnly>
              <AdminTransactions />
            </ProtectedRoute>
          }
        />
        <Route path="/" element={<Navigate to="/dashboard" />} />
      </Routes>
    </div>
  );
};

function App() {
  return (
    <Router>
      <AuthProvider>
        <AppContent />
      </AuthProvider>
    </Router>
  );
}

export default App;
