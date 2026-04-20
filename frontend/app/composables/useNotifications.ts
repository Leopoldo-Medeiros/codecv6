interface AppNotification {
  id: string
  type: string | null
  data: Record<string, any>
  read: boolean
  created_at: string
}

export const useNotifications = () => {
  const { get, patch } = useApi()

  const notifications = ref<AppNotification[]>([])
  const unreadCount   = ref(0)
  const loading       = ref(false)

  async function fetchNotifications() {
    loading.value = true
    try {
      const res = await get<{ notifications: AppNotification[]; unread_count: number }>('/notifications')
      notifications.value = res.notifications
      unreadCount.value   = res.unread_count
    } catch {
      // silent — bell just shows stale data
    } finally {
      loading.value = false
    }
  }

  async function markRead(id: string) {
    await patch(`/notifications/${id}/read`, {})
    const n = notifications.value.find(n => n.id === id)
    if (n) { n.read = true }
    if (unreadCount.value > 0) unreadCount.value--
  }

  async function markAllRead() {
    await patch('/notifications/read-all', {})
    notifications.value.forEach(n => { n.read = true })
    unreadCount.value = 0
  }

  return {
    notifications: readonly(notifications),
    unreadCount:   readonly(unreadCount),
    loading:       readonly(loading),
    fetchNotifications,
    markRead,
    markAllRead,
  }
}
