import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';

const ManageGames = () => {
    const [games, setGames] = useState([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        fetchGames();
    }, []);

    const fetchGames = async () => {
        try {
            const response = await fetch('http://localhost/gameVerse/backend/api/games.php');
            const data = await response.json();
            setGames(data);
        } catch (error) {
            console.error('Error fetching games:', error);
        } finally {
            setLoading(false);
        }
    };

    const deleteGame = async (id) => {
        // eslint-disable-next-line no-restricted-globals
        if (!confirm('Delete this game? All items will also be deleted.')) return;

        try {
            const response = await fetch(`http://localhost/gameVerse/backend/api/games.php?id=${id}`, {
                method: 'DELETE',
                credentials: 'include'
            });
            if (response.ok) {
                fetchGames();
            }
        } catch (error) {
            console.error('Error deleting game:', error);
        }
    };

    if (loading) return <div className="loading">Loading games...</div>;

    return (
        <div className="container">
            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '20px' }}>
                <h2 style={{ color: '#00aaff' }}>🎮 Manage Games</h2>
                <Link to="/admin/games/add" className="btn btn-primary" style={{ padding: '10px 20px', background: '#00aaff', color: '#fff', borderRadius: '6px', textDecoration: 'none' }}>
                    + Add New Game
                </Link>
            </div>

            {games.length === 0 ? (
                <div className="empty">
                    <p>No games added yet.</p>
                </div>
            ) : (
                <div className="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Developer</th>
                                <th>Genre</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            {games.map((game) => (
                                <tr key={game.id}>
                                    <td><strong>{game.name}</strong></td>
                                    <td>{game.developer || 'N/A'}</td>
                                    <td>{game.genre || 'N/A'}</td>
                                    <td>
                                        <span className={game.is_active ? 'badge-active' : 'badge-inactive'}>
                                            {game.is_active ? 'Active' : 'Inactive'}
                                        </span>
                                    </td>
                                    <td>
                                        <Link to={`/admin/games/edit/${game.id}`} className="btn-edit">Edit</Link>
                                        <button onClick={() => deleteGame(game.id)} className="btn-delete">Delete</button>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            )}

            <style>{`
                .btn-edit {
                    background: #ffaa00;
                    color: #000;
                    padding: 4px 12px;
                    border-radius: 4px;
                    text-decoration: none;
                    margin-right: 5px;
                    font-size: 12px;
                }
                .btn-delete {
                    background: #ff4444;
                    color: #fff;
                    padding: 4px 12px;
                    border-radius: 4px;
                    border: none;
                    cursor: pointer;
                    font-size: 12px;
                }
                .btn-edit:hover { background: #e69900; }
                .btn-delete:hover { background: #cc0000; }
                .badge-active { background: #44ff88; color: #000; padding: 3px 10px; border-radius: 12px; font-size: 12px; }
                .badge-inactive { background: #ff4444; color: #fff; padding: 3px 10px; border-radius: 12px; font-size: 12px; }
            `}</style>
        </div>
    );
};

export default ManageGames;