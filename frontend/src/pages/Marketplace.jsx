import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';
import { getItems } from '../services/api';

const Marketplace = () => {
    const { user } = useAuth();
    const [items, setItems] = useState([]);
    const [loading, setLoading] = useState(true);
    const [filters, setFilters] = useState({
        game: '',
        category: '',
        search: '',
        sort: 'newest'
    });

    useEffect(() => {
        fetchItems();
    }, [filters]);

    const fetchItems = async () => {
        setLoading(true);
        try {
            const response = await getItems(filters);
            setItems(response.data || []);
        } catch (error) {
            console.error('Error fetching items:', error);
        } finally {
            setLoading(false);
        }
    };

    const handleFilterChange = (e) => {
        setFilters({ ...filters, [e.target.name]: e.target.value });
    };

    const applyFilters = () => {
        fetchItems();
    };

    const resetFilters = () => {
        setFilters({ game: '', category: '', search: '', sort: 'newest' });
    };

    return (
        <div className="container">
            <div className="filter-bar">
                <select name="game" onChange={handleFilterChange} value={filters.game}>
                    <option value="">All Games</option>
                </select>

                <select name="category" onChange={handleFilterChange} value={filters.category}>
                    <option value="">All Categories</option>
                    <option value="weapon">⚔️ Weapons</option>
                    <option value="costume">👔 Costumes</option>
                    <option value="gem">💎 Gems</option>
                </select>

                <select name="sort" onChange={handleFilterChange} value={filters.sort}>
                    <option value="newest">Newest</option>
                    <option value="price_low">Price: Low to High</option>
                    <option value="price_high">Price: High to Low</option>
                </select>

                <input
                    type="text"
                    name="search"
                    placeholder="🔍 Search items..."
                    onChange={handleFilterChange}
                    value={filters.search}
                />

                <button onClick={applyFilters} className="btn btn-primary">Apply</button>
                <button onClick={resetFilters} className="btn btn-secondary">Reset</button>
            </div>

            <div className="items-header">
                <h2>🛒 Marketplace</h2>
                <span className="count">{items.length} items available</span>
            </div>

            {loading ? (
                <div className="loading">Loading items...</div>
            ) : items.length === 0 ? (
                <div className="empty">
                    <div className="icon">🎯</div>
                    <h3>No items found</h3>
                    <p>Try adjusting your filters.</p>
                </div>
            ) : (
                <div className="items-grid">
                    {items.map((item) => (
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

export default Marketplace;