import React, { useState, useEffect } from "react";
import { Link, useParams } from "react-router-dom";
import { getItem, purchaseItem } from "../services/api";

const ItemDetail = () => {
    const { id } = useParams();
    const [item, setItem] = useState(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState("");
    const [purchaseMsg, setPurchaseMsg] = useState("");
    const [purchasing, setPurchasing] = useState(false);

    useEffect(() => {
        const fetchItem = async () => {
            setLoading(true);
            try {
                const response = await getItem(id);
                setItem(response.data.item || response.data);
            } catch (error) {
                setError(error.response?.data?.message || "Failed to load item");
            } finally {
                setLoading(false);
            }
        };
        fetchItem();
    }, [id]);

    const handlePurchase = async () => {
        setPurchasing(true);
        setPurchaseMsg("");
        try {
            const response = await purchaseItem(item.id);
            setPurchaseMsg(response.data.message || "Item purchased successfully!");
            setItem((prev) => ({ ...prev, stock: prev.stock - 1 }));
        } catch (error) {
            setPurchaseMsg(error.response?.data?.message || "Purchase failed");
        } finally {
            setPurchasing(false);
        }
    };

    if (loading) {
        return <div className="container loading">Loading item...</div>;
    }

    if (error || !item) {
        return (
            <div className="container empty">
                <h3>Item not found</h3>
                <p>{error}</p>
                <Link to="/marketplace" className="btn btn-primary">
                    Back to Marketplace
                </Link>
            </div>
        );
    }

    return (
        <div className="container item-detail">
            <Link to="/marketplace" className="back-link">← Back to Marketplace</Link>

            <div className="detail-grid">
                <div className="detail-image">
                    <img
                        src={item.image_url || "/placeholder.png"}
                        alt={item.name}
                        onError={(e) => (e.target.style.display = "none")}
                    />
                </div>

                <div className="detail-info">
                    <h2>{item.name}</h2>
                    <div className="game-name">🎮 {item.game_name}</div>
                    <div className="price">🪙 {item.price}</div>
                    <div className="category-badge">🏷️ {item.category || "General"}</div>

                    {item.description && (
                        <div className="detail-description">{item.description}</div>
                    )}

                    <div className="detail-meta">
                        {item.skill_value && <span>⚔️ {item.skill_value}</span>}
                        {item.attribute && <span>✨ {item.attribute}</span>}
                        {item.special_effect && <span>💫 {item.special_effect}</span>}
                    </div>

                    <div className={`stock ${item.stock > 0 ? "in" : "out"}`}>
                        {item.stock > 0 ? `✅ In Stock (${item.stock})` : "❌ Out of Stock"}
                    </div>

                    {item.stock > 0 && (
                        <button
                            className="btn btn-primary"
                            onClick={handlePurchase}
                            disabled={purchasing}
                        >
                            {purchasing ? "Processing..." : `🛒 Buy Now for 🪙${item.price}`}
                        </button>
                    )}

                    {purchaseMsg && (
                        <div className={`purchase-msg ${purchaseMsg.includes("success") ? "success" : "error"}`}>
                            {purchaseMsg}
                        </div>
                    )}
                </div>
            </div>
        </div>
    );
};

export default ItemDetail;
