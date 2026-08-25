import React, { useState } from 'react';
import { useNavigate, Link } from 'react-router-dom';
import { useAuth } from '../../context/AuthContext';

const AddGame = () => {
    const { user } = useAuth();
    const navigate = useNavigate();
    const [formData, setFormData] = useState({
        name: '',
        developer: '',
        genre: '',
        description: '',
        release_date: ''
    });
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState('');
    const [success, setSuccess] = useState('');

    const handleChange = (e) => {
        setFormData({ ...formData, [e.target.name]: e.target.value });
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setLoading(true);
        setError('');
        setSuccess('');

        try {
            const response = await fetch('http://localhost/gameVerse/backend/api/games.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                credentials: 'include',
                body: JSON.stringify(formData)
            });
            const data = await response.json();

            if (data.success) {
                setSuccess('Game added successfully!');
                setTimeout(() => navigate('/admin/games'), 1500);
            } else {
                setError(data.message || 'Failed to add game');
            }
        } catch (err) {
            setError('Network error: ' + err.message);
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="container" style={{ maxWidth: '600px' }}>
            <h2 style={{ color: '#00aaff', marginBottom: '20px' }}>➕ Add New Game</h2>
            
            {error && <div className="error">❌ {error}</div>}
            {success && <div className="success">✅ {success}</div>}

            <form onSubmit={handleSubmit}>
                <div className="form-group">
                    <label>Game Name *</label>
                    <input type="text" name="name" onChange={handleChange} required />
                </div>

                <div className="form-group">
                    <label>Developer</label>
                    <input type="text" name="developer" onChange={handleChange} />
                </div>

                <div className="form-group">
                    <label>Genre</label>
                    <select name="genre" onChange={handleChange}>
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
                    <textarea name="description" onChange={handleChange} rows="4"></textarea>
                </div>

                <div className="form-group">
                    <label>Release Date</label>
                    <input type="date" name="release_date" onChange={handleChange} />
                </div>

                <div style={{ display: 'flex', gap: '15px', marginTop: '20px' }}>
                    <button type="submit" className="btn btn-primary" disabled={loading}>
                        {loading ? 'Adding...' : '✅ Add Game'}
                    </button>
                    <Link to="/admin/games" className="btn btn-secondary" style={{ textDecoration: 'none', padding: '12px 25px', background: '#555', color: '#fff', borderRadius: '6px' }}>
                        Cancel
                    </Link>
                </div>
            </form>
        </div>
    );
};

export default AddGame;