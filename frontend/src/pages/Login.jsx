import React, { useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { useAuth } from '../context/authcontext';

const Login = () => {
    const [formData, setFormData] = useState({
        username: '',
        password: '',
    });
    const [showPassword, setShowPassword] = useState(false);
    const [error, setError] = useState('');
    const [loading, setLoading] = useState(false);
    const { login } = useAuth();
    const navigate = useNavigate();

    const handleChange = (e) => {
        setFormData({ ...formData, [e.target.name]: e.target.value });
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setError('');
        setLoading(true);

        const result = await login(formData);
        if (result.success) {
            navigate('/dashboard');
        } else {
            setError(result.error || 'Login failed');
        }
        setLoading(false);
    };

    return (
        <div className="auth-container">
            <div className="auth-box">
                <h1>🎮 GamerVerse</h1>
                <p className="subtitle">Login to your gaming account</p>

                {error && <div className="error">❌ {error}</div>}

                <form onSubmit={handleSubmit}>
                    <div className="form-group">
                        <label>👤 Username</label>
                        <input
                            type="text"
                            name="username"
                            placeholder="Enter your username"
                            onChange={handleChange}
                            required
                        />
                    </div>
                    <div className="form-group password-wrapper">
                        <label>🔒 Password</label>
                        <input
                            type={showPassword ? 'text' : 'password'}
                            name="password"
                            placeholder="Enter your password"
                            onChange={handleChange}
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
                        {loading ? 'Logging in...' : '🔑 Login'}
                    </button>
                </form>

                <div className="auth-link">
                    Don't have an account? <Link to="/register">Register here</Link>
                </div>

                <div className="demo-note">
                    🔑 Demo: admin / admin123
                </div>
            </div>
        </div>
    );
};

export default Login;