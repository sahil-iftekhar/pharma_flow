"use client"

import { useState, useEffect } from "react"
import UpdatePharmacistButton from "@/components/buttons/UpdatePharmacistButton"
import DeleteButton from "@/components/buttons/DeleteButton"
import UpdatePharmacistModal from "@/components/modals/UpdatePharmacistModal"
import DeleteModal from "@/components/modals/DeleteModal"
import { deleteUserAction } from "@/actions/userActions"
import { getUserIdAction } from "@/actions/authActions"
import styles from "./PharmacistDetailCard.module.css"

export default function PharmacistDetailCard({ pharmacist, onUpdateSuccess, onDeleteSuccess }) {
  const [showUpdateModal, setShowUpdateModal] = useState(false)
  const [showDeleteModal, setShowDeleteModal] = useState(false)
  const [deleteLoading, setDeleteLoading] = useState(false)
  const [deleteError, setDeleteError] = useState("")
  const [userId, setUserId] = useState(null)

  useEffect(() => {
    const fetchUserId = async () => {
      try {
        const id = await getUserIdAction()
        setUserId(id)
      } catch (err) {
        console.error("Failed to get user ID:", err)
      }
    }
    fetchUserId()
  })

  const fullName = `${pharmacist.user.first_name || ""} ${pharmacist.user.last_name || ""}`.trim()

  const handleDelete = async () => {
    setDeleteLoading(true)
    setDeleteError("")

    try {
      const result = await deleteUserAction(pharmacist.user_id)

      if (result.error) {
        setDeleteError(typeof result.error === "object" ? JSON.stringify(result.error) : result.error)
      } else {
        setShowDeleteModal(false)
        onDeleteSuccess()
      }
    } catch (err) {
      setDeleteError("Failed to delete pharmacist")
    } finally {
      setDeleteLoading(false)
    }
  }

  const handleUpdateSuccess = () => {
    setShowUpdateModal(false)
    onUpdateSuccess()
  }

  return (
    <div className={styles.card}>
      <div className={styles.header}>
        <h1 className={styles.name}>{fullName || pharmacist.user.username || "Unknown"}</h1>
        <div className={styles.actions}>
          {userId == pharmacist.user_id && (
            <>
              <UpdatePharmacistButton onClick={() => setShowUpdateModal(true)} />
              <DeleteButton onClick={() => setShowDeleteModal(true)} />
            </>
          )}
        </div>
      </div>

      <div className={styles.content}>
        <div className={styles.section}>
          <h3 className={styles.sectionTitle}>Personal Information</h3>
          <div className={styles.infoGrid}>
            <div className={styles.infoItem}>
              <strong>Email:</strong> {pharmacist.user.email}
            </div>
            <div className={styles.infoItem}>
              <strong>Username:</strong> {pharmacist.user.username}
            </div>
            <div className={styles.infoItem}>
              <strong>First Name:</strong> {pharmacist.user.first_name || "N/A"}
            </div>
            <div className={styles.infoItem}>
              <strong>Last Name:</strong> {pharmacist.user.last_name || "N/A"}
            </div>
            <div className={styles.infoItem}>
              <strong>Address:</strong> {pharmacist.user.address || "N/A"}
            </div>
            <div className={styles.infoItem}>
              <strong>Status:</strong>
              <span className={`${styles.status} ${pharmacist.user.is_active ? styles.active : styles.inactive}`}>
                {pharmacist.user.is_active ? "Active" : "Inactive"}
              </span>
            </div>
          </div>
        </div>

        <div className={styles.section}>
          <h3 className={styles.sectionTitle}>Professional Information</h3>
          <div className={styles.infoGrid}>
            <div className={styles.infoItem}>
              <strong>License Number:</strong>
              <span className={styles.licenseNumber}>{pharmacist.license_num}</span>
            </div>
            <div className={styles.infoItem}>
              <strong>Speciality:</strong> {pharmacist.speciality}
            </div>
            <div className={styles.infoItem}>
              <strong>Consultation Available:</strong>
              <span
                className={`${styles.consultationBadge} ${pharmacist.is_consultation ? styles.available : styles.unavailable}`}
              >
                {pharmacist.is_consultation ? "Yes" : "No"}
              </span>
            </div>
          </div>
        </div>

        {pharmacist.bio && (
          <div className={styles.section}>
            <h3 className={styles.sectionTitle}>Biography</h3>
            <p className={styles.bio}>{pharmacist.bio}</p>
          </div>
        )}

        <div className={styles.section}>
          <h3 className={styles.sectionTitle}>Account Details</h3>
          <div className={styles.infoGrid}>
            <div className={styles.infoItem}>
              <strong>Created:</strong> {new Date(pharmacist.user.created_at).toLocaleDateString()}
            </div>
            <div className={styles.infoItem}>
              <strong>Last Updated:</strong> {new Date(pharmacist.user.updated_at).toLocaleDateString()}
            </div>
            <div className={styles.infoItem}>
              <strong>Slug:</strong> {pharmacist.user.slug}
            </div>
          </div>
        </div>
      </div>

      {showUpdateModal && (
        <UpdatePharmacistModal
          pharmacist={pharmacist}
          onClose={() => setShowUpdateModal(false)}
          onSuccess={handleUpdateSuccess}
        />
      )}

      {showDeleteModal && (
        <DeleteModal
          isOpen={showDeleteModal}
          title="Delete Pharmacist"
          message={`Are you sure you want to delete ${fullName || pharmacist.user.username}? This action cannot be undone.`}
          onConfirm={handleDelete}
          onCancel={() => setShowDeleteModal(false)}
          loading={deleteLoading}
          error={deleteError}
        />
      )}
    </div>
  )
}
