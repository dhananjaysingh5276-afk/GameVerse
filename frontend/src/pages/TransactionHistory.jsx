import React, { useState, useEffect } from 'react';
import { useAuth } from '../context/AuthContext';
import { getTransactions } from '../services/api';

const TransactionHistory = () => {
    const { user } = useAuth();
    const [transactions, setTransactions] = useState([]);
    const [stats, setStats] = useState({ totalSpent: 0, totalPurchases: 0 });
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        fetchTransactions();
    }, []);

    const fetchTransactions = async () => {
        setLoading(true);
        try {
            const response = await getTransactions();
            const data = response.data || [];
            setTransactions(data);

            // Calculate stats
            let totalSpent = 0;
            let totalPurchases = 0;
            data.forEach(txn => {
                if (txn.type === 'purchase') {
                    totalSpent += txn.amount;
                    totalPurchases++;
                }
            });
            setStats({ totalSpent, totalPurchases });
        } catch (error) {
            console.error('Error fetching transactions:', error);
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="container">
            <div className="header">
                <h2>📜 Transaction History</h2>
            </div>

            <div className="stats-grid">
                <div className="stat-card">
                    <div className="number">{stats.totalPurchases}</div>
                    <div className="label">Total Purchases</div>
                </div>
                <div className="stat-card">
                    <div className="number">🪙 {stats.totalSpent.toLocaleString()}</div>
                    <div className="label">Total Spent</div>
                </div>
                <div className="stat-card">
                    <div className="number">🪙 {user?.coins?.toLocaleString()}</div>
                    <div className="label">Current Balance</div>
                </div>
                <div className="stat-card">
                    <div className="number">{transactions.length}</div>
                    <div className="label">Total Transactions</div>
                </div>
            </div>

            {loading ? (
                <div className="loading">Loading transactions...</div>
            ) : transactions.length === 0 ? (
                <div className="empty">
                    <div className="icon">📜</div>
                    <h3>No transactions yet</h3>
                    <p>Your purchase history will appear here.</p>
                </div>
            ) : (
                <div className="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Item</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            {transactions.map((txn) => (
                                <tr key={txn.id}>
                                    <td>{new Date(txn.created_at).toLocaleDateString()}</td>
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

export default TransactionHistory;