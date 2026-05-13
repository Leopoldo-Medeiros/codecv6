interface AppNotification {
  id: string
  type: string | null
  data: Record<string, string | number | boolean | null>
  read: boolean
  created_at: string
}

export const useNotifications = () => {
  const { get, patch } = useApi()

  const notifications = ref<AppNotification[]>([])
  const unreadCount   = ref(0)
  const loading       = ref(false)
  const error         = ref<string | null>(null)

  async function fetchNotifications() {
    loading.value = true
    try {
      const res = await get<{ notifications: AppNotification[]; unread_count: number }>('/notifications')
      notifications.value = res.notifications
      unreadCount.value   = res.unread_count
      error.value         = null
    } catch (e: any) {
      error.value = e?.data?.message ?? 'Failed to load notifications'
    } finally {
      loading.value = false
    }
  }

  async function markRead(id: string) {
    try {
      await patch(`/notifications/${id}/read`, {})
      const n = notifications.value.find(n => n.id === id)
      if (n) { n.read = true }
      if (unreadCount.value > 0) unreadCount.value--
    } catch (e: any) {
      error.value = e?.data?.message ?? 'Failed to mark notification as read'
    }
  }

  async function markAllRead() {
    try {
      await patch('/notifications/read-all', {})
      notifications.value.forEach(n => { n.read = true })
      unreadCount.value = 0
    } catch (e: any) {
      error.value = e?.data?.message ?? 'Failed to mark all notifications as read'
    }
  }

  return {
    notifications: readonly(notifications),
    unreadCount:   readonly(unreadCount),
    loading:       readonly(loading),
    error:         readonly(error),
    fetchNotifications,
    markRead,
    markAllRead,
  }
}
