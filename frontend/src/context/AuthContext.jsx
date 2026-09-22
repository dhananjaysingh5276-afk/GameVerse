import React, { createContext, useState, useContext, useEffect } from 'react';
import { getUser, login as apiLogin, register as apiRegister, logout as apiLogout } from '../services/api';

const AuthContext = createContext();

export const useAuth = () => useContext(AuthContext);

export const AuthProvider = ({ children }) => {
    const [user, setUser] = useState(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);

    // Check if user is already logged in
    useEffect(() => {
        const checkAuth = async () => {
            try {
                const response = await getUser();
                if (response.data.success) {
                    setUser(response.data.user);
                }
            } catch (err) {
                console.error('Auth check failed:', err);
            } finally {
                setLoading(false);
            }
        };
        checkAuth();
    }, []);

    // Login
    const login = async (credentials) => {
        setError(null);
        try {
            const response = await apiLogin(credentials);
            if (response.data.success) {
                setUser(response.data.user);
                return { success: true, user: response.data.user };
            } else {
                setError(response.data.message);
                return { success: false, error: response.data.message };
            }
        } catch (err) {
            const message = err.response?.data?.message || 'Login failed';
            setError(message);
            return { success: false, error: message };
        }
    };

    // Register
    const register = async (userData) => {
        setError(null);
        try {
            const response = await apiRegister(userData);
            if (response.data.success) {
                return { success: true };
            } else {
                setError(response.data.message);
                return { success: false, error: response.data.message };
            }
        } catch (err) {
            const message = err.response?.data?.message || 'Registration failed';
            setError(message);
            return { success: false, error: message };
        }
    };

    // Logout
    const logout = async () => {
        try {
            await apiLogout();
        } catch (err) {
            console.error('Logout error:', err);
        } finally {
            setUser(null);
        }
    };

    // Update user (for balance changes)
    const updateUser = (updatedUser) => {
        setUser(updatedUser);
    };

    const value = {
        user,
        loading,
        error,
        login,
        register,
        logout,
        updateUser,
        isAuthenticated: !!user,
        isAdmin: user?.role === 'admin',
    };

    return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>;
};