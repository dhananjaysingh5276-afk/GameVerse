import React, { useState, useEffect } from 'react';
import { useNavigate, Link } from 'react-router-dom';

const AddItem = () => {
    const navigate = useNavigate();
    const [games, setGames] = useState([]);
    const [formData, setFormData] = useState({
        name: '',
        game_id: '',
        category: '',
        price: '',
        description: '',
        image_url: '',
        stock: 10
    });
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState('');
    const [success, setSuccess] = useState('');

    useEffect(() => {
        const fetchGames = async () => {
            try {
                const response = await fetch('http://localhost/gameVerse/backend/api/games.php', {
                    credentials: 'include'
                });
                setGames(await response.json());
            } catch (err) {
                console.error('Error fetching games:', err);
            }
        };
        fetchGames();
    }, []);

    const handleChange = (e) => {
        setFormData({ ...formData, [e.target.name]: e.target.value });
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setLoading(true);
        setError('');
        setSuccess('');

        try {
            const response = await fetch('http://localhost/gameVerse/backend/api/items.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                credentials: 'include',
                body: JSON.stringify(formData)
            });
            const data = await response.json();

            if (data.success) {
                setSuccess('Item added successfully!');
                setTimeout(() => navigate('/admin/items'), 1500);
            } else {
                setError(data.message || 'Failed to add item');
            }
        } catch (err) {
            setError('Network error: ' + err.message);
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="container" style={{ maxWidth: '600px' }}>
            <h2 style={{ color: '#00aaff', marginBottom: '20px' }}>📦 Add New Item</h2>

            {error && <div className="error">❌ {error}</div>}
            {success && <div className="success">✅ {success}</div>}

            <form onSubmit={handleSubmit}>
                <div className="form-group">
                    <label>Item Name *</label>
                    <input type="text" name="name" onChange={handleChange} required />
                </div>

                <div className="form-group">
                    <label>Game</label>
                    <select name="game_id" onChange={handleChange}>
                        <option value="">Select Game</option>
                        {games.map((game) => (
                            <option key={game.id} value={game.id}>{game.name}</option>
                        ))}
                    </select>
                </div>

                <div className="form-group">
                    <label>Category</label>
                    <select name="category" onChange={handleChange}>
                        <option value="">Select Category</option>
                        <option value="weapon">⚔️ Weapons</option>
                        <option value="costume">👔 Costumes</option>
                        <option value="gem">💎 Gems</option>
                    </select>
                </div>

                <div className="form-group">
                    <label>Price *</label>
                    <input type="number" name="price" step="0.01" min="0" onChange={handleChange} required />
                </div>

                <div className="form-group">
                    <label>Stock *</label>
                    <input type="number" name="stock" min="1" value={formData.stock} onChange={handleChange} required />
                </div>

                <div className="form-group">
                    <label>Description</label>
                    <textarea name="description" rows="4" onChange={handleChange}></textarea>
                </div>

                <div className="form-group">
                    <label>Image URL</label>
                    <input type="text" name="image_url" onChange={handleChange} />
                </div>

                <div style={{ display: 'flex', gap: '15px', marginTop: '20px' }}>
                    <button type="submit" className="btn btn-primary" disabled={loading}>
                        {loading ? 'Adding...' : '📦 Add Item'}
                    </button>
                    <Link to="/admin/items" className="btn btn-secondary" style={{ textDecoration: 'none', padding: '12px 25px', background: '#555', color: '#fff', borderRadius: '6px' }}>
                        Cancel
                    </Link>
                </div>
            </form>
        </div>
    );
};

export default AddItem;
