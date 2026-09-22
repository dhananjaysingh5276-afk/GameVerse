import React, { useState, useEffect } from 'react';
import { useNavigate, useParams, Link } from 'react-router-dom';

const EditItems = () => {
    const { id } = useParams();
    const navigate = useNavigate();
    const [loading, setLoading] = useState(true);
    const [saving, setSaving] = useState(false);
    const [games, setGames] = useState([]);
    const [error, setError] = useState('');
    const [success, setSuccess] = useState('');
    const [formData, setFormData] = useState({
        name: '',
        game_id: '',
        category: '',
        price: '',
        description: '',
        image_url: ''
    });

    useEffect(() => {
        const fetchItem = async () => {
            try {
                const response = await fetch(`http://localhost/gameVerse/backend/api/item.php?id=${id}`, {
                    credentials: 'include'
                });
                const data = await response.json();
                if (data && data.name) {
                    setFormData({
                        name: data.name || '',
                        game_id: data.game_id || '',
                        category: data.category || '',
                        price: data.price || '',
                        description: data.description || '',
                        image_url: data.image_url || ''
                    });
                } else {
                    setError('Item not found');
                }
            } catch (err) {
                setError('Failed to load item: ' + err.message);
            } finally {
                setLoading(false);
            }
        };
        fetchItem();

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
    }, [id]);

    const handleChange = (e) => {
        setFormData({ ...formData, [e.target.name]: e.target.value });
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setSaving(true);
        setError('');
        setSuccess('');

        try {
            const response = await fetch(`http://localhost/gameVerse/backend/api/item.php`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                credentials: 'include',
                body: JSON.stringify({ id, ...formData })
            });
            const data = await response.json();

            if (data.success) {
                setSuccess('Item updated successfully!');
                setTimeout(() => navigate('/admin/items'), 1500);
            } else {
                setError(data.message || 'Failed to update item');
            }
        } catch (err) {
            setError('Network error: ' + err.message);
        } finally {
            setSaving(false);
        }
    };

    if (loading) return <div className="loading">Loading item...</div>;

    return (
        <div className="container" style={{ maxWidth: '600px' }}>
            <h2 style={{ color: '#00aaff', marginBottom: '20px' }}>✏️ Edit Item</h2>

            {error && <div className="error">❌ {error}</div>}
            {success && <div className="success">✅ {success}</div>}

            <form onSubmit={handleSubmit}>
                <div className="form-group">
                    <label>Item Name *</label>
                    <input type="text" name="name" value={formData.name} onChange={handleChange} required />
                </div>

                <div className="form-group">
                    <label>Game</label>
                    <select name="game_id" value={formData.game_id} onChange={handleChange}>
                        <option value="">Select Game</option>
                        {games.map((game) => (
                            <option key={game.id} value={game.id}>{game.name}</option>
                        ))}
                    </select>
                </div>

                <div className="form-group">
                    <label>Category</label>
                    <select name="category" value={formData.category} onChange={handleChange}>
                        <option value="">Select Category</option>
                        <option value="weapon">⚔️ Weapons</option>
                        <option value="costume">👔 Costumes</option>
                        <option value="gem">💎 Gems</option>
                    </select>
                </div>

                <div className="form-group">
                    <label>Price *</label>
                    <input type="number" name="price" step="0.01" min="0" value={formData.price} onChange={handleChange} required />
                </div>

                <div className="form-group">
                    <label>Description</label>
                    <textarea name="description" rows="4" value={formData.description} onChange={handleChange}></textarea>
                </div>

                <div className="form-group">
                    <label>Image URL</label>
                    <input type="text" name="image_url" value={formData.image_url} onChange={handleChange} />
                </div>

                <div style={{ display: 'flex', gap: '15px', marginTop: '20px' }}>
                    <button type="submit" className="btn btn-primary" disabled={saving}>
                        {saving ? 'Saving...' : '💾 Save Changes'}
                    </button>
                    <Link to="/admin/items" className="btn btn-secondary" style={{ textDecoration: 'none', padding: '12px 25px', background: '#555', color: '#fff', borderRadius: '6px' }}>
                        Cancel
                    </Link>
                </div>
            </form>
        </div>
    );
};

export default EditItems;
