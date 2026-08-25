import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';
import { getItems } from '../services/api';

const Dashboard = () => {
    const { user } = useAuth();
    const [recentItems, setRecentItems] = useState([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        const fetchRecentPurchases = async () => {
            try {
                const response = await getItems({ limit: 4, sort: 'newest' });
                setRecentItems(response.data.slice(0, 4));
            } catch (error) {
                console.error('Error fetching items:', error);
            } finally {
                setLoading(false);
            }
        };
        fetchRecentPurchases();
    }, []);

    return (
        <div className="container">
            <div className="welcome">
                <h2>Welcome back, {user?.username}! 👋</h2>
                <p>Your gaming collection is waiting for you.</p>
            </div>

            <div className="quick-actions">
                <Link to="/marketplace" className="btn btn-primary">🛒 Browse Marketplace</Link>
                <Link to="/inventory" className="btn btn-secondary">🎒 My Collection</Link>
            </div>

            <div className="stats-grid">
                <div className="stat-card">
                    <div className="number">🪙 {user?.coins?.toLocaleString()}</div>
                    <div className="label">Coins Available</div>
                </div>
                <div className="stat-card">
                    <div className="number">{recentItems.length}</div>
                    <div className="label">Recent Items</div>
                </div>
                <div className="stat-card">
                    <div className="number">{new Date(user?.created_at).toLocaleDateString()}</div>
                    <div className="label">Joined Date</div>
                </div>
            </div>

            <h3 className="section-title">📦 Featured Items</h3>
            {loading ? (
                <div className="loading">Loading...</div>
            ) : (
                <div className="items-grid">
                    {recentItems.map((item) => (
                        <div key={item.id} className="item-card">
                            <div className="item-image">🎮</div>
                            <div className="item-body">
                                <h3>{item.name}</h3>
                                <div className="game-name">🎮 {item.game_name}</div>
                                <div className="price">🪙 {item.price}</div>
                                <Link to={`/item/${item.id}`} className="btn btn-view">View Details</Link>
                            </div>
                        </div>
                    ))}
                </div>
            )}
        </div>
    );
};

export default Dashboard;