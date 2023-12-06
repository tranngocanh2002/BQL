
export const FETCH_ALL_NOTIFICATION = 'actions/notification/FETCH_ALL_NOTIFICATION'
export const FETCH_ALL_NOTIFICATION_COMPLETE = 'actions/notification/FETCH_ALL_NOTIFICATION_COMPLETE'

export const FETCH_COUNT_UNREAD = 'actions/notification/FETCH_COUNT_UNREAD'
export const FETCH_COUNT_UNREAD_COMPLETE = 'actions/notification/FETCH_COUNT_UNREAD_COMPLETE'

export const SEEN_NOTIFICATION = 'actions/notification/SEEN_NOTIFICATION'
export const SEEN_NOTIFICATION_COMPLETE = 'actions/notification/SEEN_NOTIFICATION_COMPLETE'

export function fetchAllNotification(payload) {
    return {
        type: FETCH_ALL_NOTIFICATION,
        payload
    }
}
export function fetchAllNotificationComplete(payload) {
    return {
        type: FETCH_ALL_NOTIFICATION_COMPLETE,
        payload
    }
}

export function fetchCountUnreadNotification(payload) {
    return {
        type: FETCH_COUNT_UNREAD,
        payload
    }
}
export function fetchCountUnreadNotificationComplete(payload) {
    return {
        type: FETCH_COUNT_UNREAD_COMPLETE,
        payload
    }
}

export function seenNotification(payload) {
    return {
        type: SEEN_NOTIFICATION,
        payload
    }
}
export function seenNotificationComplete(payload) {
    return {
        type: SEEN_NOTIFICATION_COMPLETE,
        payload
    }
}