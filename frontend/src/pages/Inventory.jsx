// @ts-nocheck
import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';
import { getInventory } from '../services/api';

const Inventory = () => {
    // eslint-disable-next-line no-unused-vars
    const { user } = useAuth();
    const [inventory, setInventory] = useState([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        fetchInventory();
    }, []);

    const fetchInventory = async () => {
        setLoading(true);
        try {
            const response = await getInventory();
            setInventory(response.data || []);
        } catch (error) {
            console.error('Error fetching inventory:', error);
        } finally {
            setLoading(false);
        }
    };

    // Count categories
    const weapons = inventory.filter(item => item.category === 'weapon');
    const costumes = inventory.filter(item => item.category === 'costume');
    const gems = inventory.filter(item => item.category === 'gem');

    return (
        <div className="container">
            <div className="header">
                <h2>🎒 My Collection</h2>
                <div className="stats">
                    <span>Total: <strong>{inventory.length}</strong></span>
                    <span>⚔️ Weapons: <strong>{weapons.length}</strong></span>
                    <span>👔 Costumes: <strong>{costumes.length}</strong></span>
                    <span>💎 Gems: <strong>{gems.length}</strong></span>
                </div>
            </div>

            {loading ? (
                <div className="loading">Loading your collection...</div>
            ) : inventory.length === 0 ? (
                <div className="empty">
                    <div className="icon">🎒</div>
                    <h3>Your collection is empty</h3>
                    <p>Buy items from the marketplace to build your collection.</p>
                    <Link to="/marketplace" className="btn btn-primary" style={{ display: 'inline-block', marginTop: '15px' }}>
                        🛒 Browse Marketplace
                    </Link>
                </div>
            ) : (
                <div className="items-grid">
                    {inventory.map((item) => (
                        <div key={item.id} className="item-card">
                            <div className="item-image">🎮</div>
                            <div className="item-body">
                                <h3>{item.name}</h3>
                                <div className="game-name">🎮 {item.game_name}</div>
                                <div className="purchase-date">
                                    📅 Purchased: {new Date(item.purchase_date).toLocaleDateString()}
                                </div>
                            </div>
                        </div>
                    ))}
                </div>
            )}
        </div>
    );
};

export default Inventory;