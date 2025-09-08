import Link from "next/link"
import styles from "./PharmacistCard.module.css"

export default function PharmacistCard({ pharmacist }) {
  const fullName = `${pharmacist.user.first_name || ""} ${pharmacist.user.last_name || ""}`.trim()

  return (
    <Link href={`/pharmacist/${pharmacist.user_id}`} className={styles.cardLink}>
      <div className={styles.card}>
        <div className={styles.header}>
          <h3 className={styles.name}>{fullName || pharmacist.user.username || "Unknown"}</h3>
          <span className={styles.licenseNumber}>License: {pharmacist.license_num}</span>
        </div>

        <div className={styles.content}>
          <div className={styles.speciality}>
            <strong>Speciality:</strong> {pharmacist.speciality}
          </div>

          <div className={styles.consultation}>
            <span
              className={`${styles.consultationBadge} ${pharmacist.is_consultation ? styles.available : styles.unavailable}`}
            >
              {pharmacist.is_consultation ? "Available for Consultation" : "Not Available for Consultation"}
            </span>
          </div>

          {pharmacist.bio && (
            <div className={styles.bio}>
              <strong>Bio:</strong>
              <p className={styles.bioText}>{pharmacist.bio}</p>
            </div>
          )}
        </div>

        <div className={styles.footer}>
          <span className={styles.email}>{pharmacist.user.email}</span>
        </div>
      </div>
    </Link>
  )
}
