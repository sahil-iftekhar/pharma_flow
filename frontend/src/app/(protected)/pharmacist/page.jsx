"use client"

import { useState, useEffect } from "react"
import { getPharmacistsAction } from "@/actions/pharmacistActions"
import { getUserRoleAction } from "@/actions/authActions"
import PharmacistCard from "@/components/cards/PharmacistCard"
import Pagination from "@/components/paginations/Pagination"
import CreatePharmacistButton from "@/components/buttons/CreatePharmacistButton"
import CreatePharmacistModal from "@/components/modals/CreatePharmacistModal"
import styles from "./page.module.css"

export default function PharmacistPage() {
  const [pharmacists, setPharmacists] = useState([])
  const [pagination, setPagination] = useState(null)
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState("")
  const [userRole, setUserRole] = useState(null)
  const [showCreateModal, setShowCreateModal] = useState(false)
  const [currentPage, setCurrentPage] = useState(1)

  const fetchPharmacists = async (page = 1) => {
    setLoading(true)
    setError("")

    try {
      const result = await getPharmacistsAction({ page })

      if (result.error) {
        setError(result.error)
      } else {
        setPharmacists(result.data || [])
        setPagination(result.pagination)
      }
    } catch (err) {
      setError("Failed to fetch pharmacists")
    } finally {
      setLoading(false)
    }
  }

  const fetchUserRole = async () => {
    try {
      const role = await getUserRoleAction()
      setUserRole(role)
    } catch (err) {
      console.error("Failed to fetch user role:", err)
    }
  }

  useEffect(() => {
    fetchPharmacists(currentPage)
    fetchUserRole()
  }, [currentPage])

  const handlePageChange = (page) => {
    setCurrentPage(page)
  }

  const handleCreateSuccess = () => {
    setShowCreateModal(false)
    fetchPharmacists(currentPage)
  }

  if (loading) {
    return (
      <div className={styles.container}>
        <div className={styles.loading}>Loading pharmacists...</div>
      </div>
    )
  }

  return (
    <div className={styles.container}>
      <div className={styles.header}>
        <h1 className={styles.title}>Pharmacists</h1>
        {userRole === "super_admin" && <CreatePharmacistButton onClick={() => setShowCreateModal(true)} />}
      </div>

      {error && <div className={styles.error}>{typeof error === "object" ? JSON.stringify(error) : error}</div>}

      <div className={styles.pharmacistGrid}>
        {pharmacists.length > 0 ? (
          pharmacists.map((pharmacist) => <PharmacistCard key={pharmacist.id} pharmacist={pharmacist} />)
        ) : (
          <div className={styles.noData}>No pharmacists found</div>
        )}
      </div>

      {pagination && pagination.total_pages > 1 && (
        <div className={styles.paginationWrapper}>
          <Pagination currentPage={currentPage} totalPages={pagination.total_pages} onPageChange={handlePageChange} />
        </div>
      )}

      {showCreateModal && (
        <CreatePharmacistModal onClose={() => setShowCreateModal(false)} onSuccess={handleCreateSuccess} />
      )}
    </div>
  )
}
