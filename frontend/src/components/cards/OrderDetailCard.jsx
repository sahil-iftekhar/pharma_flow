"use client"

import styles from "./OrderDetailCard.module.css"
import { useState, useEffect } from "react"
import { useRouter } from "next/navigation"
import { deleteOrderAction, updateOrderAction } from "@/actions/orderActions"
import { getUserIdAction } from "@/actions/authActions"

export default function OrderDetailCard({ order, isAdmin = false }) {
  const router = useRouter()
  const [success, setSuccess] = useState(null)
  const [error, setError] = useState(null)
  const [isUpdating, setIsUpdating] = useState(false)
  const [userId, setUserId] = useState(null)

  useEffect(() => {
    const fetchUserId = async () => {
      try {
        const id = await getUserIdAction();
        setUserId(id);
      } catch (err) {
        // Handle error if user ID cannot be retrieved
        console.error("Failed to get user ID:", err);
      }
    };
    fetchUserId();
  }, []);

  const formatOrderDate = (dateString) => {
    const date = new Date(dateString);
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return date.toLocaleDateString('en-US', options);
  };

  const getStatusColor = (status) => {
    switch (status) {
      case "pending":
        return styles.statusPending
      case "processing":
        return styles.statusPreparing
      case "ready":
        return styles.statusReady
      case "delivered":
        return styles.statusDelivered
      case "canceled":
        return styles.statuscanceled
      default:
        return styles.statusDefault
    }
  }

  const getSubscribeTypeClass = (type) => {
    switch (type.toLowerCase()) {
      case "none":
        return styles.statuscanceled
      case "monthly":
        return styles.statusPreparing
      case "yearly":
        return styles.statusReady
      default:
        return styles.statusDefault
    }
  }

  const getDeliveryTypeClass = (type) => {
    switch (type.toLowerCase()) {
      case "basic":
        return styles.statusPreparing
      case "rapid":
        return styles.paymentPaid
      case "emergency":
        return styles.paymentFailed
      default:
        return styles.paymentDefault
    }
  }

  const getPaymentStatusColor = (status) => {
    switch (status) {
      case "paid":
        return styles.paymentPaid
      case "pending":
        return styles.paymentPending
      case "failed":
        return styles.paymentFailed
      default:
        return styles.paymentDefault
    }
  }

  const onDelete = async () => {
    try {
      const result = await deleteOrderAction(order.id)

      if (result.error) {
        setError(result.error)
      } else {
        setSuccess("Order deleted successfully!")
        setTimeout(() => {
          router.push("/orders")
        }, 1500)
      }
    } catch (err) {
      setError("Failed to cancel order")
    }
  }

  const onUpdate = async (e) => {
    e.preventDefault();
    setSuccess(null);
    setError(null);
    const formData = new FormData(e.target);
    
    try {
      const result = await updateOrderAction(order.id, formData);
      if (result.error) {
        setError(result.error.error);
      } else {
        setSuccess("Order updated successfully!");
        setIsUpdating(false); // Hide the form on success
        setTimeout(() => {
          router.push("/orders");
        }, 1500);
      }
    } catch (err) {
      setError("Failed to update order");
    }
  };

  const isOrderOwner = userId && order.user_id == userId;

  const dateFormat = (dateString) => {
    if (!dateString) return "Not set"
    const date = new Date(dateString);
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return date.toLocaleDateString('en-US', options);
  };

  return (
    <div className={styles.card}>
      <div className={styles.header}>
        <div className={styles.orderInfo}>
          <h2 className={styles.orderId}>Order #{order.id}</h2>
          <p className={styles.orderDate}>{formatOrderDate(order.order_date)}</p>
        </div>
        <div className={styles.totalAmount}>${order.total_amount}</div>
      </div>

      <div className={styles.statusSection}>
        <div className={styles.statusItem}>
          <span className={styles.statusLabel}>Order Status:</span>
          <span className={`${styles.statusBadge} ${getStatusColor(order.order_status)}`}>{order.order_status}</span>
        </div>
        <div className={styles.statusItem}>
          <span className={styles.statusLabel}>Payment Status:</span>
          <span className={`${styles.statusBadge} ${getPaymentStatusColor(order.payment_status)}`}>
            {order.payment_status}
          </span>
        </div>
        <div className={styles.statusItem}>
          <span className={styles.statusLabel}>Subscription:</span>
          <span className={`${styles.statusBadge} ${getSubscribeTypeClass(order.subscribe_type)}`}>
            {order.subscribe_type}
          </span>
        </div>
        <div className={styles.statusItem}>
          <span className={styles.statusLabel}>Delivery Type:</span>
          <span className={`${styles.statusBadge} ${getDeliveryTypeClass(order.delivery.delivery_type)}`}>
            {order.delivery.delivery_type}
          </span>
        </div>
        <div className={styles.statusItem}>
          <span className={styles.statusLabel}>Delivery Status:</span>
          <span className={`${styles.statusBadge} ${getStatusColor(order.delivery.delivery_status)}`}>
            {order.delivery.delivery_status}
          </span>
        </div>
      </div>

      <div className={styles.customerSection}>
        <h3 className={styles.sectionTitle}>Customer Information</h3>
        <div className={styles.customerInfo}>
          <div className={styles.customerDetail}>
            <span className={styles.label}>Customer ID:</span>
            <span className={styles.value}>{order.user_id}</span>
          </div>
          <div className={styles.customerDetail}>
            <span className={styles.label}>Username:</span>
            <span className={styles.value}>{order.user.username}</span>
          </div>
        </div>
      </div>

      <div className={styles.customerSection}>
        <h3 className={styles.sectionTitle}>Delivery Information</h3>
        <div className={styles.customerInfo}>
          <div className={styles.customerDetail}>
            <span className={styles.label}>Estimated Delivery Date:</span>
            <span className={styles.value}>{dateFormat(order.delivery.est_del_date)}</span>
          </div>
          <div className={styles.customerDetail}>
            <span className={styles.label}>Date Delivered:</span>
            <span className={styles.value}>{dateFormat(order.delivery.del_date)}</span>
          </div>
        </div>
      </div>

      <div className={styles.itemsSection}>
        <h3 className={styles.sectionTitle}>Order Items</h3>
        <div className={styles.orderItems}>
          {order.order_items.map((item) => (
            <div key={item.id} className={styles.orderItem}>
              <div className={styles.itemInfo}>
                <span className={styles.itemName}>{item.medicine.name}</span>
                <span className={styles.itemId}>Medicine ID: {item.medicine_id}</span>
              </div>
              <div className={styles.itemQuantity}>Qty: {item.quantity}</div>
            </div>
          ))}
        </div>
      </div>

      {isUpdating && (
        <form onSubmit={onUpdate} className={styles.updateForm}>
          {isAdmin && (
            <div className={styles.formGroup}>
              <label htmlFor="order_status">Order Status</label>
              <select name="order_status" id="order_status" className={styles.select}>
                <option value="">None</option>
                <option value="delivered">Delivered</option>
              </select>
            </div>
          )}
          {isOrderOwner && (
            <div className={styles.formGroup}>
              <label htmlFor="subscribe_type">Subscription</label>
              <select name="subscribe_type" id="subscribe_type" className={styles.select}>
                <option value="none">No Subscription</option>
                <option value="weekly">Weekly</option>
                <option value="monthly">Monthly</option>
              </select>
            </div>
          )}
          <button type="submit" className={styles.updateButton}>
            Submit Update
          </button>
        </form>
      )}

      <br></br>

      {success && <div className={styles.success}>{success}</div>}
      {error && <div className={styles.error}>{error}</div>}

      {(isAdmin || isOrderOwner) && (
        <div className={styles.actions}>
          <button onClick={() => setIsUpdating(!isUpdating)} className={styles.updateButton}>
            {isUpdating ? "Cancel Update" : "Update Order"}
          </button>
          {isAdmin && (
            <button onClick={onDelete} className={styles.deleteButton}>
              Delete Order
            </button>
          )}
        </div>
      )}
    </div>
  )
}

