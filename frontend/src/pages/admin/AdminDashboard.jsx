import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { useAuth } from '../../context/AuthContext';

const AdminDashboard = () => {
    const { user } = useAuth();
    const [stats, setStats] = useState({
        users: 0,
        games: 0,
        items: 0,
        transactions: 0
    });

    useEffect(() => {
        fetchStats();
    }, []);

    const fetchStats = async () => {
        try {
            const response = await fetch('http://localhost/gameVerse/backend/api/stats.php');
            const data = await response.json();
            setStats(data);
        } catch (error) {
            console.error('Error fetching stats:', error);
        }
    };

    return (
        <div className="container">
            <h2 style={{ color: '#00aaff', marginBottom: '20px' }}>👑 Admin Dashboard</h2>
            <p style={{ color: '#888', marginBottom: '30px' }}>
                Welcome, {user?.username}! Manage your gaming marketplace from here.
            </p>

            <div className="stats-grid">
                <div className="stat-card">
                    <div className="number">{stats.users}</div>
                    <div className="label">👤 Total Users</div>
                </div>
                <div className="stat-card">
                    <div className="number">{stats.games}</div>
                    <div className="label">🎮 Total Games</div>
                </div>
                <div className="stat-card">
                    <div className="number">{stats.items}</div>
                    <div className="label">📦 Total Items</div>
                </div>
                <div className="stat-card">
                    <div className="number">{stats.transactions}</div>
                    <div className="label">💳 Transactions</div>
                </div>
            </div>

            <div className="admin-actions">
                <Link to="/admin/games" className="btn btn-primary">
                    🎮 Manage Games
                </Link>
                <Link to="/admin/items" className="btn btn-primary">
                    📦 Manage Items
                </Link>
                <Link to="/admin/users" className="btn btn-secondary">
                    👤 Manage Users
                </Link>
                <Link to="/admin/transactions" className="btn btn-secondary">
                    💳 View All Transactions
                </Link>
            </div>

            <style>{`
                .admin-actions {
                    display: flex;
                    gap: 15px;
                    flex-wrap: wrap;
                    margin-top: 20px;
                }
                .admin-actions .btn {
                    padding: 12px 25px;
                    border-radius: 8px;
                    text-decoration: none;
                    font-weight: 600;
                    transition: 0.3s;
                    background: #1a1a2e;
                    color: #00aaff;
                    border: 1px solid #00aaff;
                }
                .admin-actions .btn:hover {
                    background: #00aaff;
                    color: #fff;
                    transform: translateY(-2px);
                }
                .admin-actions .btn-secondary {
                    color: #ccc;
                    border-color: #555;
                }
                .admin-actions .btn-secondary:hover {
                    background: #555;
                    color: #fff;
                }
            `}</style>
        </div>
    );
};

export default AdminDashboard;