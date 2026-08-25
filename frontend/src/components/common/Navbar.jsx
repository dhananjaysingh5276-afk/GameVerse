import React from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { useAuth } from '../../context/AuthContext';

const Navbar = () => {
    const { user, logout, isAuthenticated, isAdmin } = useAuth();
    const navigate = useNavigate();

    const handleLogout = async () => {
        await logout();
        navigate('/login');
    };

    return (
        <nav className="navbar">
            <h1 onClick={() => navigate(isAdmin ? '/admin' : '/dashboard')}>🎮 GamerVerse</h1>
            <div className="nav-links">
                {isAuthenticated ? (
                    <>
                        {isAdmin ? (
                            // Admin Links
                            <>
                                <Link to="/admin">👑 Dashboard</Link>
                                <Link to="/admin/games">🎮 Games</Link>
                                <Link to="/admin/items">📦 Items</Link>
                            </>
                        ) : (
                            // User Links
                            <>
                                <Link to="/dashboard">🏠 Dashboard</Link>
                                <Link to="/marketplace">🛒 Marketplace</Link>
                                <Link to="/inventory">🎒 Collection</Link>
                                <Link to="/transactions">📜 History</Link>
                            </>
                        )}
                        <span className="coins">🪙 {user?.coins?.toLocaleString()}</span>
                        <button onClick={handleLogout} className="logout">Logout</button>
                    </>
                ) : (
                    <>
                        <Link to="/login">Login</Link>
                        <Link to="/register">Register</Link>
                    </>
                )}
            </div>
        </nav>
    );
};

export default Navbar;