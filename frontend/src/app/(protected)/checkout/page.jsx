"use client"
import { useState, useEffect } from "react"
import { useRouter } from "next/navigation"
import { createOrderAction } from "@/actions/orderActions"
import { createPaymentAction } from "@/actions/paymentActions"
import { getCartItemsAction } from "@/actions/cartActions"
import { getUserIdAction } from "@/actions/authActions"
import { useFormStatus } from "react-dom"
import styles from "./page.module.css"

function PaymentForm({ onCardSubmit, loading }) {
  const [formData, setFormData] = useState({
    cardNumber: "",
    expiryDate: "",
    cvv: "",
    cardholderName: "",
  });

  const handleInputChange = (e) => {
    const { name, value } = e.target;
    setFormData((prev) => ({
      ...prev,
      [name]: value,
    }));
  };

  return (
    <div className={styles.cardPaymentForm}>
      <div className={styles.field}>
        <label className={styles.label}>Cardholder Name</label>
        <input
          type="text"
          name="cardholderName"
          value={formData.cardholderName}
          onChange={handleInputChange}
          className={styles.input}
          placeholder="John Doe"
          required
        />
      </div>
      <div className={styles.field}>
        <label className={styles.label}>Card Number</label>
        <input
          type="text"
          name="cardNumber"
          value={formData.cardNumber}
          onChange={handleInputChange}
          className={styles.input}
          placeholder="1234 5678 9012 3456"
          maxLength="19"
          required
        />
      </div>
      <div className={styles.row}>
        <div className={styles.field}>
          <label className={styles.label}>Expiry Date</label>
          <input
            type="text"
            name="expiryDate"
            value={formData.expiryDate}
            onChange={handleInputChange}
            className={styles.input}
            placeholder="MM/YY"
            maxLength="5"
            required
          />
        </div>
        <div className={styles.field}>
          <label className={styles.label}>CVV</label>
          <input
            type="text"
            name="cvv"
            value={formData.cvv}
            onChange={handleInputChange}
            className={styles.input}
            placeholder="123"
            maxLength="4"
            required
          />
        </div>
      </div>
      <div className={styles.actions}>
        <button type="button" className={styles.backButton} onClick={() => onCardSubmit(null, 'back')} disabled={loading}>
          Back
        </button>
        <button type="submit" className={styles.submitButton} disabled={loading} onClick={() => onCardSubmit(formData, 'complete')}>
          {loading ? "Processing..." : "Complete Order"}
        </button>
      </div>
    </div>
  );
}

function FormContent({ cartItems, calculateTotal, success, error }) {
  const { pending } = useFormStatus();
  const [prescriptionImages, setPrescriptionImages] = useState([{ id: Date.now(), file: null }]);

  const [deliveryType, setDeliveryType] = useState("basic");

  const deliveryPrices = {
    basic: 0.00,
    rapid: 10.00,
    emergency: 20.00,
  };

  const handleAddImage = () => {
    setPrescriptionImages([...prescriptionImages, { id: Date.now(), file: null }]);
  };

  const handleRemoveImage = (id) => {
    if (prescriptionImages.length > 1) {
      setPrescriptionImages(prescriptionImages.filter(img => img.id !== id));
    }
  };

  const handleFileChange = (e, id) => {
    const file = e.target.files[0];
    const newImages = prescriptionImages.map(img => {
      if (img.id === id) {
        return { ...img, file: file };
      }
      return img;
    });
    setPrescriptionImages(newImages);
  };

  const handleDeliveryChange = (e) => {
    setDeliveryType(e.target.value);
  };

  return (
    <>
      <div className={styles.orderSummary}>
        <h2 className={styles.sectionTitle}>Order Summary</h2>
        <div className={styles.items}>
          {cartItems.map((item) => (
            <div key={item.id} className={styles.item}>
              <span className={styles.itemName}>{item.medicine.name}</span>
              <span className={styles.itemQuantity}>x{item.quantity}</span>
              <span className={styles.itemPrice}>
                ${(Number.parseFloat(item.medicine.price) * item.quantity).toFixed(2)}
              </span>
            </div>
          ))}
        </div>
        <div className={styles.items}>
          <div className={styles.item}>
            <span className={styles.itemName}>Delivery Charge</span>
            <span className={styles.itemPrice}>${Number.parseFloat(deliveryPrices[deliveryType]).toFixed(2)}</span>
          </div>
        </div>
        <div className={styles.total}>
          <strong>Total: ${calculateTotal(deliveryType)}</strong>
        </div>
      </div>
      
      <div className={styles.formSection}>
        <h2 className={styles.sectionTitle}>Delivery & Subscription Details</h2>
        <div className={styles.formGroup}>
          <label htmlFor="delivery_type" className={styles.label}>Delivery Type</label>
          <select 
            id="delivery_type" 
            name="delivery_type" 
            className={styles.select} 
            disabled={pending}
            value={deliveryType}
            onChange={handleDeliveryChange}
          >
            <option value="basic">Basic</option>
            <option value="rapid">Rapid</option>
            <option value="emergency">Emergency</option>
          </select>
        </div>
        <div className={styles.formGroup}>
          <label htmlFor="subscribe_type" className={styles.label}>Subscription</label>
          <select id="subscribe_type" name="subscribe_type" className={styles.select} disabled={pending}>
            <option value="">Select Subscription</option>
            <option value="none">No Subscription</option>
            <option value="weekly">Weekly</option>
            <option value="monthly">Monthly</option>
          </select>
        </div>
        <div className={styles.formGroup}>
          <label className={styles.label}>Prescription Images</label>
          {prescriptionImages.map((input, index) => (
            <div key={input.id} className={styles.dynamicField}>
              <div className={styles.fileUpload}>
                <label htmlFor={`image-${input.id}`} className={styles.fileButton}>Choose File</label>
                <span className={styles.fileName}>{input.file ? input.file.name : 'No file chosen'}</span>
                <input
                  type="file"
                  id={`image-${input.id}`}
                  name="prescription_images[]"
                  accept="image/*"
                  onChange={(e) => handleFileChange(e, input.id)}
                  className={styles.hiddenInput}
                  disabled={pending}
                />
              </div>
              {prescriptionImages.length > 1 && (
                <button type="button" onClick={() => handleRemoveImage(input.id)} className={styles.removeButton} disabled={pending}>-</button>
              )}
              {index === prescriptionImages.length - 1 && (
                <button type="button" onClick={handleAddImage} className={styles.addButton} disabled={pending}>+</button>
              )}
            </div>
          ))}
        </div>
      </div>
    </>
  );
}

export default function CheckoutPage() {
  const [cartItems, setCartItems] = useState([]);
  const [loading, setLoading] = useState(true);
  const [success, setSuccess] = useState("");
  const [error, setError] = useState("");
  const [selectedPaymentMethod, setSelectedPaymentMethod] = useState("cash"); // Set a default
  const [cardFormData, setCardFormData] = useState(null); // State to hold card data
  const router = useRouter();

  useEffect(() => {
    setSuccess("");
    setError("");
    loadCartItems();
  }, []);

  const loadCartItems = async () => {
    try {
      setLoading(true);
      const userId = await getUserIdAction();
      if (!userId) {
        router.push("/login");
        return;
      }
      const result = await getCartItemsAction(userId);
      if (result.error) {
        setError(result.error);
      } else {
        setCartItems(result.data.cart_items || []);
      }
    } catch (err) {
      setError("Failed to load cart items");
    } finally {
      setLoading(false);
    }
  };

  const calculateTotal = (delivery_type = "basic") => {
    let amount = cartItems.reduce((total, item) => {
      const price = Number.parseFloat(item.medicine.price);
      return total + price * item.quantity;
    }, 0);

    if (delivery_type === "rapid") {
      amount += 10;
    } else if (delivery_type === "emergency") {
      amount += 20;
    }

    return amount.toFixed(2);
  };
  
  const handleOrder = async (formData) => {
    setLoading(true);
    setError("");
    
    const subscribeType = formData.get("subscribe_type");
    const deliveryType = formData.get("delivery_type");
    const prescriptionImages = formData.getAll("prescription_images[]");

    if (!subscribeType) {
      setError("Please select a subscription plan.");
      setLoading(false);
      return;
    }
    if (!deliveryType) {
      setError("Please select a delivery system.");
      setLoading(false);
      return;
    }

    formData.delete("prescription_images[]");

    prescriptionImages.forEach((file) => {
      if (file instanceof File && file.size > 0) {
        formData.append("prescription_images[]", file);
      } else {
        console.log("File is empty or not a File object.");
      }
    });

    try {
      const orderResult = await createOrderAction(formData);
      if (orderResult.error) {
        setError("Presciption Image is larger than 2MB or not an image file or dimensions are larger than 1000x1000");
        setLoading(false);
        return;
      }

      if (selectedPaymentMethod === "card") {
        const paymentFormData = new FormData();
        paymentFormData.append("order", orderResult?.order_id);
        paymentFormData.append("payment", "card");

        const paymentResult = await createPaymentAction(paymentFormData);
        if (paymentResult.error) {
          setError(paymentResult.error.error);
          setLoading(false);
          return;
        }
        setSuccess("Order created and Payment processed successfully!");
      } else {
        setSuccess("Order created successfully!");
      }

      setTimeout(() => {
        router.push("/orders");
      }, 1500);

    } catch (err) {
      setError("Failed to process order");
    } finally {
      setLoading(false);
    }
  };

  if (loading) {
    return (
      <div className={styles.container}>
        <div className={styles.loading}>Loading checkout...</div>
      </div>
    );
  }

  if (cartItems.length === 0) {
    return (
      <div className={styles.container}>
        <div className={styles.emptyCart}>
          <h2>Your cart is empty</h2>
          <button className={styles.shopButton} onClick={() => router.push("/medicines")}>
            Browse Medicines
          </button>
        </div>
      </div>
    )
  }

  return (
    <div className={styles.container}>
      <div className={styles.header}>
        <button onClick={() => router.back()} className={styles.backButton}>
          ← Back
        </button>
        <h1 className={styles.title}>Checkout</h1>
      </div>

      <form action={handleOrder} id="checkout-form">
        <FormContent
          cartItems={cartItems}
          calculateTotal={calculateTotal}
          success={success}
          error={error}
        />
        <div className={styles.paymentMethods}>
          <h2 className={styles.sectionTitle}>Payment Method</h2>
          <div className={styles.methodButtons}>
            <button
              type="button"
              className={styles.methodButton}
              onClick={() => setSelectedPaymentMethod("card")}
            >
              <div className={styles.methodIcon}>💳</div>
              <div className={styles.methodText}>
                <h3>Card Payment</h3>
                <p>Pay with credit or debit card</p>
              </div>
            </button>
            <button
              type="button"
              className={styles.methodButton}
              onClick={() => setSelectedPaymentMethod("cash")}
            >
              <div className={styles.methodIcon}>💵</div>
              <div className={styles.methodText}>
                <h3>Cash Payment</h3>
                <p>Pay when you receive your order</p>
              </div>
            </button>
          </div>
        </div>

        {selectedPaymentMethod === "cash" && (
          <button className={styles.submitButton} type="submit" disabled={loading}>
            {loading ? "Processing..." : "Place Cash Order"}
          </button>
        )}

        {selectedPaymentMethod === "card" && (
          <PaymentForm onCardSubmit={setCardFormData} loading={loading} />
        )}

        <button type="submit" style={{ display: 'none' }} id="hidden-submit" />
      </form>

      <br></br>

      {success && <div className={styles.success}>{success}</div>}
      {error && <div className={styles.error}>{error}</div>}
      
    </div>
  )
}