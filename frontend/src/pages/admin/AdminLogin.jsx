import React, { useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { useAuth } from '../../context/AuthContext';

const AdminLogin = () => {
    const { login, logout } = useAuth();
    const navigate = useNavigate();
    const [formData, setFormData] = useState({ username: '', password: '' });
    const [showPassword, setShowPassword] = useState(false);
    const [error, setError] = useState('');
    const [loading, setLoading] = useState(false);

    const handleChange = (e) => {
        setFormData({ ...formData, [e.target.name]: e.target.value });
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setError('');
        setLoading(true);
        try {
            const result = await login(formData);
            if (result.success) {
                if (result.user?.role === 'admin') {
                    navigate('/admin');
                } else {
                    await logout();
                    setError('⛔ This account is not an admin. Only admin accounts can enter the Admin Lobby.');
                }
            } else {
                setError(result.error || 'Login failed');
            }
        } catch (err) {
            setError(err?.response?.data?.message || 'Login failed');
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="auth-container">
            <div className="auth-box">
                <h1>🎪 Admin Lobby</h1>
                <span className="admin-badge">👑 Admin Access Only</span>
                <p className="subtitle">Game control room — staff entrance</p>

                {error && <div className="error">❌ {error}</div>}

                <form onSubmit={handleSubmit}>
                    <div className="form-group">
                        <label>👤 Admin Username</label>
                        <input
                            type="text"
                            name="username"
                            placeholder="Enter admin username"
                            onChange={handleChange}
                            value={formData.username}
                            required
                        />
                    </div>
                    <div className="form-group password-wrapper">
                        <label>🔒 Admin Password</label>
                        <input
                            type={showPassword ? 'text' : 'password'}
                            name="password"
                            placeholder="Enter admin password"
                            onChange={handleChange}
                            value={formData.password}
                            required
                        />
                        <span
                            className="password-toggle"
                            onClick={() => setShowPassword(!showPassword)}
                        >
                            {showPassword ? '🙈' : '👁️'}
                        </span>
                    </div>

                    <button type="submit" className="btn btn-primary" disabled={loading}>
                        {loading ? 'Authenticating...' : '🔑 Enter Admin Area'}
                    </button>
                </form>

                <div className="demo-note">
                    🔑 Demo: <strong>admin</strong> / <strong>admin123</strong>
                </div>

                <div className="auth-link">
                    <Link to="/login" className="back-link">← Back to player login</Link>
                </div>
            </div>
        </div>
    );
};

export default AdminLogin;
