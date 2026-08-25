import React, { useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';

const Register = () => {
    const [formData, setFormData] = useState({
        username: '',
        email: '',
        password: '',
        confirm_password: '',
    });
    const [showPassword, setShowPassword] = useState(false);
    const [showConfirmPassword, setShowConfirmPassword] = useState(false);
    const [error, setError] = useState('');
    const [success, setSuccess] = useState('');
    const [loading, setLoading] = useState(false);
    const { register } = useAuth();
    const navigate = useNavigate();

    const handleChange = (e) => {
        setFormData({ ...formData, [e.target.name]: e.target.value });
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setError('');
        setSuccess('');

        if (formData.password !== formData.confirm_password) {
            setError('Passwords do not match!');
            return;
        }

        if (formData.password.length < 6) {
            setError('Password must be at least 6 characters!');
            return;
        }

        setLoading(true);
        const result = await register({
            username: formData.username,
            email: formData.email,
            password: formData.password,
        });

        if (result.success) {
            setSuccess('Registration successful! Please login.');
            setTimeout(() => navigate('/login'), 2000);
        } else {
            setError(result.error || 'Registration failed');
        }
        setLoading(false);
    };

    return (
        <div className="auth-container">
            <div className="auth-box">
                <h1>🎮 GamerVerse</h1>
                <p className="subtitle">Create your gaming account</p>

                {error && <div className="error">❌ {error}</div>}
                {success && <div className="success">✅ {success}</div>}

                <form onSubmit={handleSubmit}>
                    <div className="form-group">
                        <label>👤 Username</label>
                        <input
                            type="text"
                            name="username"
                            placeholder="Choose a username"
                            onChange={handleChange}
                            required
                        />
                    </div>
                    <div className="form-group">
                        <label>📧 Email</label>
                        <input
                            type="email"
                            name="email"
                            placeholder="your@email.com"
                            onChange={handleChange}
                            required
                        />
                    </div>
                    <div className="form-group password-wrapper">
                        <label>🔒 Password (min 6 characters)</label>
                        <input
                            type={showPassword ? 'text' : 'password'}
                            name="password"
                            placeholder="Create a password"
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
                    <div className="form-group password-wrapper">
                        <label>🔐 Confirm Password</label>
                        <input
                            type={showConfirmPassword ? 'text' : 'password'}
                            name="confirm_password"
                            placeholder="Confirm your password"
                            onChange={handleChange}
                            required
                        />
                        <span
                            className="password-toggle"
                            onClick={() => setShowConfirmPassword(!showConfirmPassword)}
                        >
                            {showConfirmPassword ? '🙈' : '👁️'}
                        </span>
                    </div>
                    <button type="submit" className="btn btn-primary" disabled={loading}>
                        {loading ? 'Creating account...' : '🚀 Create Account'}
                    </button>
                </form>

                <div className="auth-link">
                    Already have an account? <Link to="/login">Login here</Link>
                </div>
            </div>
        </div>
    );
};

export default Register;