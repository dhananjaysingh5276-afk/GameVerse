import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';

const ManageItems = () => {
    const [items, setItems] = useState([]);
    const [games, setGames] = useState([]);
    const [loading, setLoading] = useState(true);
    const [filterGame, setFilterGame] = useState('');

    useEffect(() => {
        fetchData();
    }, []);

    const fetchData = async () => {
        try {
            const [itemsRes, gamesRes] = await Promise.all([
                fetch('http://localhost/gameVerse/backend/api/items.php'),
                fetch('http://localhost/gameVerse/backend/api/games.php')
            ]);
            const itemsData = await itemsRes.json();
            const gamesData = await gamesRes.json();
            setItems(itemsData);
            setGames(gamesData);
        } catch (error) {
            console.error('Error fetching data:', error);
        } finally {
            setLoading(false);
        }
    };

    const deleteItem = async (id) => {
        // eslint-disable-next-line no-restricted-globals
        if (!confirm('Delete this item?')) return;

        try {
            const response = await fetch(`http://localhost/gameVerse/backend/api/items.php?id=${id}`, {
                method: 'DELETE',
                credentials: 'include'
            });
            if (response.ok) {
                fetchData();
            }
        } catch (error) {
            console.error('Error deleting item:', error);
        }
    };

    const filteredItems = filterGame 
        ? items.filter(item => parseInt(item.game_id) === parseInt(filterGame))
        : items;

    if (loading) return <div className="loading">Loading items...</div>;

    return (
        <div className="container">
            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '20px', flexWrap: 'wrap', gap: '10px' }}>
                <h2 style={{ color: '#00aaff' }}>📦 Manage Items</h2>
                <Link to="/admin/items/add" className="btn btn-primary" style={{ padding: '10px 20px', background: '#00aaff', color: '#fff', borderRadius: '6px', textDecoration: 'none' }}>
                    + Add New Item
                </Link>
            </div>

            {/* Filter */}
            <div style={{ marginBottom: '20px', display: 'flex', gap: '10px', alignItems: 'center' }}>
                <label style={{ color: '#888' }}>Filter by Game:</label>
                <select 
                    onChange={(e) => setFilterGame(e.target.value)} 
                    style={{ padding: '8px 15px', borderRadius: '6px', background: '#1a1a2e', color: '#fff', border: '1px solid #2a2a4e' }}
                >
                    <option value="">All Games</option>
                    {games.map(game => (
                        <option key={game.id} value={game.id}>{game.name}</option>
                    ))}
                </select>
                <span style={{ color: '#888' }}>Total: <strong>{filteredItems.length}</strong> items</span>
            </div>

            {filteredItems.length === 0 ? (
                <div className="empty">
                    <p>No items found.</p>
                </div>
            ) : (
                <div className="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Game</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            {filteredItems.map((item) => (
                                <tr key={item.id}>
                                    <td><strong>{item.name}</strong></td>
                                    <td>{item.game_name}</td>
                                    <td>
                                        <span className={`badge-category ${item.category}`}>
                                            {item.category}
                                        </span>
                                    </td>
                                    <td>🪙 {item.price}</td>
                                    <td>{item.stock}</td>
                                    <td>
                                        <Link to={`/admin/items/edit/${item.id}`} className="btn-edit">Edit</Link>
                                        <button onClick={() => deleteItem(item.id)} className="btn-delete">Delete</button>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            )}

            <style>{`
                .btn-edit {
                    background: #ffaa00;
                    color: #000;
                    padding: 4px 12px;
                    border-radius: 4px;
                    text-decoration: none;
                    margin-right: 5px;
                    font-size: 12px;
                }
                .btn-delete {
                    background: #ff4444;
                    color: #fff;
                    padding: 4px 12px;
                    border-radius: 4px;
                    border: none;
                    cursor: pointer;
                    font-size: 12px;
                }
                .btn-edit:hover { background: #e69900; }
                .btn-delete:hover { background: #cc0000; }
                .badge-category {
                    padding: 3px 10px;
                    border-radius: 12px;
                    font-size: 12px;
                }
                .badge-category.weapon { background: #ff4444; color: #fff; }
                .badge-category.costume { background: #44aaff; color: #fff; }
                .badge-category.gem { background: #ffaa00; color: #000; }
            `}</style>
        </div>
    );
};

export default ManageItems;