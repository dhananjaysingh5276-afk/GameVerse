import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';

const AdminTransactions = () => {
    const [transactions, setTransactions] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState('');

    useEffect(() => {
        const fetchTransactions = async () => {
            try {
                const response = await fetch('http://localhost/gameVerse/backend/api/transactions.php?all=1', {
                    credentials: 'include'
                });
                if (!response.ok) {
                    throw new Error('Failed to load transactions');
                }
                setTransactions(await response.json());
            } catch (err) {
                console.error('Error fetching transactions:', err);
                setError('Failed to load transactions');
            } finally {
                setLoading(false);
            }
        };
        fetchTransactions();
    }, []);

    return (
        <div className="container">
            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '20px', flexWrap: 'wrap', gap: '10px' }}>
                <h2 style={{ color: '#00aaff' }}>💳 All Transactions</h2>
                <Link to="/admin" className="btn btn-secondary" style={{ textDecoration: 'none', padding: '10px 20px', background: '#555', color: '#fff', borderRadius: '6px' }}>
                    ← Back to Dashboard
                </Link>
            </div>

            {error && <div className="error">❌ {error}</div>}

            {loading ? (
                <div className="loading">Loading transactions...</div>
            ) : transactions.length === 0 ? (
                <div className="empty">
                    <div className="icon">💳</div>
                    <h3>No transactions yet</h3>
                    <p>Transactions from all users will appear here.</p>
                </div>
            ) : (
                <div className="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>User</th>
                                <th>Item</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            {transactions.map((txn) => (
                                <tr key={txn.id}>
                                    <td>{new Date(txn.created_at).toLocaleString()}</td>
                                    <td><strong>{txn.username || '—'}</strong></td>
                                    <td>{txn.item_name || '—'}</td>
                                    <td>{txn.type}</td>
                                    <td className={txn.type === 'purchase' ? 'negative' : 'positive'}>
                                        {txn.type === 'purchase' ? '-' : '+'} 🪙 {txn.amount}
                                    </td>
                                    <td>{txn.status}</td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            )}
        </div>
    );
};

export default AdminTransactions;