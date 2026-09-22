import React, { useState, useEffect } from 'react';
import { useNavigate, useParams, Link } from 'react-router-dom';

const EditGame = () => {
    const { id } = useParams();
    const navigate = useNavigate();
    const [formData, setFormData] = useState({
        name: '',
        developer: '',
        genre: '',
        description: '',
        release_date: ''
    });
    const [loading, setLoading] = useState(true);
    const [saving, setSaving] = useState(false);
    const [error, setError] = useState('');
    const [success, setSuccess] = useState('');

    useEffect(() => {
        const fetchGame = async () => {
            try {
                const response = await fetch(`http://localhost/gameVerse/backend/api/games.php?id=${id}`);
                const data = await response.json();
                if (data && data.id) {
                    setFormData({
                        name: data.name || '',
                        developer: data.developer || '',
                        genre: data.genre || '',
                        description: data.description || '',
                        release_date: data.release_date || ''
                    });
                } else {
                    setError('Game not found');
                }
            } catch (err) {
                setError('Failed to load game: ' + err.message);
            } finally {
                setLoading(false);
            }
        };
        fetchGame();
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
            const response = await fetch(`http://localhost/gameVerse/backend/api/games.php?id=${id}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                credentials: 'include',
                body: JSON.stringify(formData)
            });
            const data = await response.json();

            if (data.success) {
                setSuccess('Game updated successfully!');
                setTimeout(() => navigate('/admin/games'), 1500);
            } else {
                setError(data.message || 'Failed to update game');
            }
        } catch (err) {
            setError('Network error: ' + err.message);
        } finally {
            setSaving(false);
        }
    };

    if (loading) return <div className="loading">🔄 Loading game...</div>;

    return (
        <div className="container" style={{ maxWidth: '600px' }}>
            <h2 style={{ color: '#00aaff', marginBottom: '20px' }}>✏️ Edit Game</h2>

            {error && <div className="error">❌ {error}</div>}
            {success && <div className="success">✅ {success}</div>}

            <form onSubmit={handleSubmit}>
                <div className="form-group">
                    <label>Game Name *</label>
                    <input type="text" name="name" value={formData.name} onChange={handleChange} required />
                </div>

                <div className="form-group">
                    <label>Developer</label>
                    <input type="text" name="developer" value={formData.developer} onChange={handleChange} />
                </div>

                <div className="form-group">
                    <label>Genre</label>
                    <select name="genre" value={formData.genre} onChange={handleChange}>
                        <option value="">Select Genre</option>
                        <option value="Action">Action</option>
                        <option value="Adventure">Adventure</option>
                        <option value="RPG">RPG</option>
                        <option value="FPS">FPS</option>
                        <option value="Strategy">Strategy</option>
                        <option value="Sports">Sports</option>
                        <option value="Racing">Racing</option>
                    </select>
                </div>

                <div className="form-group">
                    <label>Description</label>
                    <textarea name="description" rows="4" value={formData.description} onChange={handleChange}></textarea>
                </div>

                <div className="form-group">
                    <label>Release Date</label>
                    <input type="date" name="release_date" value={formData.release_date} onChange={handleChange} />
                </div>

                <div style={{ display: 'flex', gap: '15px', marginTop: '20px' }}>
                    <button type="submit" className="btn btn-primary" disabled={saving}>
                        {saving ? 'Saving...' : '💾 Save Changes'}
                    </button>
                    <Link to="/admin/games" className="btn btn-secondary" style={{ textDecoration: 'none', padding: '12px 25px', background: '#555', color: '#fff', borderRadius: '6px' }}>
                        Cancel
                    </Link>
                </div>
            </form>
        </div>
    );
};

export default EditGame;
