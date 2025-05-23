<?php
require_once 'config.php';

// Removed debug output: echo "<!-- PHP script reached HTML output section -->";

// Function to get time ago
function time_elapsed_string($datetime) {
    // Set timezone to Asia/Manila
    date_default_timezone_set('Asia/Manila');
    
    $now = new DateTime();
    $ago = new DateTime($datetime);
    
    // Ensure both dates are in the same timezone
    $now->setTimezone(new DateTimeZone('Asia/Manila'));
    $ago->setTimezone(new DateTimeZone('Asia/Manila'));
    
    $diff = $now->diff($ago);

    // If the date is in the future, return "Just now"
    if ($diff->invert === 0) {
        return 'Just now';
    }
    
    if ($diff->y > 0) {
        return $diff->y . ' year' . ($diff->y > 1 ? 's' : '') . ' ago';
    }
    if ($diff->m > 0) {
        return $diff->m . ' month' . ($diff->m > 1 ? 's' : '') . ' ago';
    }
    if ($diff->d > 0) {
        return $diff->d . ' day' . ($diff->d > 1 ? 's' : '') . ' ago';
    }
    if ($diff->h > 0) {
        return $diff->h . ' hour' . ($diff->h > 1 ? 's' : '') . ' ago';
    }
    if ($diff->i > 0) {
        return $diff->i . ' minute' . ($diff->i > 1 ? 's' : '') . ' ago';
    }
    if ($diff->s > 30) {
        return 'Less than a minute ago';
    }
    return 'Just now';
}

// Function to get notification icon
function get_notification_icon($type) {
    return match($type) {
        'award' => 'fa-trophy',
        'recognition' => 'fa-star',
        'category' => 'fa-tag',
        default => 'fa-bell'
    };
}

// Function to mark notification as read
function mark_notification_read($id) {
    global $conn;
    try {
    $stmt = $conn->prepare("UPDATE notifications SET `read` = 1 WHERE id = ?");
        if (!$stmt) {
            error_log("Prepare failed: " . $conn->error);
            return false;
        }
        $stmt->bind_param("i", $id);
        $result = $stmt->execute();
        if (!$result) {
            error_log("Execute failed: " . $stmt->error);
            return false;
        }
        return $stmt->affected_rows > 0;
    } catch (Exception $e) {
        error_log("Error in mark_notification_read: " . $e->getMessage());
        return false;
    }
}

// Function to mark notification as unread
function mark_notification_unread($id) {
    global $conn;
    try {
        $stmt = $conn->prepare("UPDATE notifications SET `read` = 0 WHERE id = ?");
        if (!$stmt) {
            error_log("Prepare failed: " . $conn->error);
            return false;
        }
    $stmt->bind_param("i", $id);
        $result = $stmt->execute();
        if (!$result) {
            error_log("Execute failed: " . $stmt->error);
            return false;
        }
        return $stmt->affected_rows > 0;
    } catch (Exception $e) {
        error_log("Error in mark_notification_unread: " . $e->getMessage());
        return false;
    }
}

// Function to mark all notifications as read
function mark_all_notifications_read() {
    global $conn;
    try {
    $stmt = $conn->prepare("UPDATE notifications SET `read` = 1");
        if (!$stmt) {
            error_log("Prepare failed: " . $conn->error);
            return false;
        }
        $result = $stmt->execute();
        if (!$result) {
            error_log("Execute failed: " . $stmt->error);
            return false;
        }
        return $stmt->affected_rows > 0;
    } catch (Exception $e) {
        error_log("Error in mark_all_notifications_read: " . $e->getMessage());
        return false;
    }
}

// Function to get notifications
function get_notifications($limit = 10) {
    global $conn;
    $stmt = $conn->prepare("
        SELECT id, type, reference_id, title, message, `read`, created_at 
        FROM notifications 
        ORDER BY created_at DESC 
        LIMIT ?
    ");
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Function to get unread count
function get_unread_count() {
    global $conn;
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM notifications WHERE `read` = 0");
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc()['count'];
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log("notifications.php received POST request"); // Log POST request received
    $action = $_POST['action'] ?? '';
    error_log("Action: " . $action); // Log the received action
    $response = ['success' => false, 'message' => ''];
    
    switch ($action) {
        case 'mark_read':
            if (isset($_POST['id'])) {
                $id = (int)$_POST['id'];
                $response['success'] = mark_notification_read($id);
                $response['message'] = $response['success'] ? 'Notification marked as read' : 'Failed to mark notification as read';
            } else {
                $response['message'] = 'No notification ID provided';
            }
            break;
            
        case 'mark_unread':
            if (isset($_POST['id'])) {
                $id = (int)$_POST['id'];
                $response['success'] = mark_notification_unread($id);
                $response['message'] = $response['success'] ? 'Notification marked as unread' : 'Failed to mark notification as unread';
            } else {
                $response['message'] = 'No notification ID provided';
            }
            break;
            
        case 'mark_all_read':
            $response['success'] = mark_all_notifications_read();
            $response['message'] = $response['success'] ? 'All notifications marked as read' : 'Failed to mark all notifications as read';
            break;
            
        case 'get_notifications':
            error_log("Attempting to get notifications"); // Log attempt to get notifications
            $notifications = get_notifications();
            $unread_count = get_unread_count();
            
            if ($notifications === false) {
                 error_log("Error fetching notifications from database"); // Log database fetch error
                 $response['message'] = 'Database error fetching notifications';
            } else {
                error_log("Successfully fetched " . count($notifications) . " notifications"); // Log successful fetch
                $formatted_notifications = array_map(function($notification) {
                    return [
                        'id' => $notification['id'],
                        'icon' => get_notification_icon($notification['type']),
                        'title' => $notification['title'],
                        'message' => $notification['message'],
                        'time_ago' => time_elapsed_string($notification['created_at']),
                        'read' => (bool)$notification['read'],
                        'type' => $notification['type'],
                        'reference_id' => $notification['reference_id']
                    ];
                }, $notifications);
                
                $response = [
                    'success' => true,
                    'notifications' => $formatted_notifications,
                    'unread_count' => $unread_count
                ];
            }
            break;
            
        default:
            $response['message'] = 'Invalid action';
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// If not an AJAX request, return the HTML
if (empty($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
    $notifications = get_notifications();
    $unread_count = get_unread_count();
?>
<!-- Notification Bell HTML -->
<div class="nav-item dropdown">
    <a class="nav-link position-relative" href="#" id="notificationDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fas fa-bell"></i>
        <?php if ($unread_count > 0): ?>
        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
            <?php echo $unread_count; ?>
        </span>
        <?php endif; ?>
    </a>
    <div class="dropdown-menu dropdown-menu-end notification-dropdown" aria-labelledby="notificationDropdown">
        <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
            <h6 class="mb-0">Notifications</h6>
            <?php if ($unread_count > 0): ?>
            <button class="btn btn-link btn-sm text-decoration-none p-0 mark-all-read">
                Mark all as read
            </button>
            <?php endif; ?>
        </div>
        <div class="notification-list">
            <?php if (empty($notifications)): ?>
            <div class="text-center py-3 text-muted">
                No notifications
            </div>
            <?php else: ?>
            <?php foreach ($notifications as $notification): ?>
            <div class="notification-item p-3 border-bottom <?php echo $notification['read'] ? '' : 'unread'; ?>" 
                 data-id="<?php echo $notification['id']; ?>"
                 data-type="<?php echo $notification['type']; ?>"
                 data-reference="<?php echo $notification['reference_id']; ?>">
                <div class="d-flex">
                    <div class="flex-shrink-0">
                        <i class="fas <?php echo get_notification_icon($notification['type']); ?> text-primary"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-1"><?php echo htmlspecialchars($notification['title']); ?></h6>
                            <small class="text-muted"><?php echo time_elapsed_string($notification['created_at']); ?></small>
                        </div>
                        <p class="mb-1"><?php echo htmlspecialchars($notification['message']); ?></p>
                        <div class="notification-actions">
                        <?php if (!$notification['read']): ?>
                        <button class="btn btn-link btn-sm text-decoration-none p-0 mark-read">
                            Mark as read
                        </button>
                            <?php else: ?>
                            <button class="btn btn-link btn-sm text-decoration-none p-0 mark-unread">
                                Mark as unread
                        </button>
                        <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Notification Details Modal -->
<div class="modal fade" id="notificationModal" tabindex="-1" aria-labelledby="notificationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="notificationModalLabel">Notification Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="notification-details">
                    <div class="text-center mb-3">
                        <i class="fas fa-bell notification-icon text-primary" style="font-size: 2rem;"></i>
                    </div>
                    <h4 class="notification-title mb-3"></h4>
                    <p class="notification-message mb-3"></p>
                    <div class="notification-meta">
                        <small class="text-muted notification-time"></small>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<style>
.notification-dropdown {
    width: 350px;
    max-height: 400px;
    overflow-y: auto;
}

@media (max-width: 576px) {
    .notification-dropdown {
        width: 100%;
        max-width: 100vw;
        position: fixed !important;
        top: 56px !important;
        left: 0 !important;
        right: 0 !important;
        margin: 0;
        border-radius: 0;
        max-height: calc(100vh - 56px);
    }
}

.notification-item {
    transition: background-color 0.2s;
    cursor: pointer;
}

.notification-item:hover {
    background-color: #f8f9fa;
}

.notification-item.unread {
    background-color: #f0f7ff;
}

.notification-item .mark-read,
.notification-item .mark-unread {
    color: #0d6efd;
    font-size: 0.875rem;
}

.notification-item .mark-read:hover,
.notification-item .mark-unread:hover {
    color: #0a58ca;
}

.mark-all-read {
    color: #0d6efd;
    font-size: 0.875rem;
}

.mark-all-read:hover {
    color: #0a58ca;
}

.notification-actions {
    display: flex;
    gap: 10px;
}

.notification-details {
    text-align: center;
}

.notification-icon {
    margin-bottom: 1rem;
}

.notification-title {
    color: #4E3B2A;
    font-weight: 600;
    word-break: break-word;
}

.notification-message {
    color: #594423;
    word-break: break-word;
}

.notification-meta {
    color: #6c757d;
    font-size: 0.875rem;
}

/* Modal Responsiveness */
@media (max-width: 576px) {
    .modal-dialog {
        margin: 0.5rem;
        max-width: calc(100% - 1rem);
    }
    
    .modal-content {
        border-radius: 0.5rem;
    }
    
    .modal-body {
        padding: 1rem;
    }
    
    .notification-title {
        font-size: 1.1rem;
    }
    
    .notification-message {
        font-size: 0.9rem;
    }
}

/* Improve touch targets for mobile */
@media (max-width: 576px) {
    .notification-item {
        padding: 1rem !important;
    }
    
    .notification-actions button {
        padding: 0.5rem;
        min-height: 44px;
    }
    
    .mark-all-read {
        padding: 0.5rem;
        min-height: 44px;
    }
}

/* Improve scrolling on mobile */
@media (max-width: 576px) {
    .notification-dropdown {
        -webkit-overflow-scrolling: touch;
    }
    
    .notification-list {
        padding-bottom: env(safe-area-inset-bottom);
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('notifications.php script started');

    const notificationDropdown = document.getElementById('notificationDropdown');
    const notificationList = document.querySelector('.notification-list');
    const markAllReadBtn = document.querySelector('.mark-all-read');
    
    console.log('notificationList element:', notificationList);

    const notificationModalElement = document.getElementById('notificationModal');
    if (!notificationModalElement) {
        console.error('Notification modal element #notificationModal not found!');
        return;
    }
    const notificationModal = new bootstrap.Modal(notificationModalElement);
    
    // Function to update notifications
    function updateNotifications() {
        fetch('notifications.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: 'action=get_notifications'
        })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                console.error('Failed to fetch notifications:', data.message);
                return;
            }
            
            // Update notification count
            const badge = notificationDropdown.querySelector('.badge');
            if (data.unread_count > 0) {
                if (!badge) {
                    const newBadge = document.createElement('span');
                    newBadge.className = 'position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger';
                    notificationDropdown.appendChild(newBadge);
                }
                badge.textContent = data.unread_count;
            } else if (badge) {
                badge.remove();
            }
            
            // Update notification list
            if (data.notifications.length === 0) {
                notificationList.innerHTML = '<div class="text-center py-3 text-muted">No notifications</div>';
                if (markAllReadBtn) markAllReadBtn.remove();
                return;
            }
            
            let html = '';
            data.notifications.forEach(notification => {
                html += `
                    <div class="notification-item p-3 border-bottom ${notification.read ? '' : 'unread'}" 
                         data-id="${notification.id}">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <i class="fas ${notification.icon} text-primary"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-1">${notification.title}</h6>
                                    <small class="text-muted">${notification.time_ago}</small>
                                </div>
                                <p class="mb-1">${notification.message}</p>
                                <div class="notification-actions">
                                    ${notification.read ? `
                                        <button class="btn btn-link btn-sm text-decoration-none p-0 mark-unread" onclick="markAsUnread(${notification.id})">
                                            Mark as unread
                                        </button>
                                    ` : `
                                        <button class="btn btn-link btn-sm text-decoration-none p-0 mark-read" onclick="markAsRead(${notification.id})">
                                            Mark as read
                                        </button>
                                    `}
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            notificationList.innerHTML = html;
            
            // Update mark all read button
            if (data.unread_count > 0) {
                if (!markAllReadBtn) {
                    const newMarkAllReadBtn = document.createElement('button');
                    newMarkAllReadBtn.className = 'btn btn-link btn-sm text-decoration-none p-0 mark-all-read';
                    newMarkAllReadBtn.textContent = 'Mark all as read';
                    document.querySelector('.dropdown-menu .border-bottom').appendChild(newMarkAllReadBtn);
                }
            } else if (markAllReadBtn) {
                markAllReadBtn.remove();
            }
        })
        .catch(error => {
            console.error('Error fetching notifications:', error);
        });
    }
    
    // Function to mark notification as read
    window.markAsRead = function(id) {
        console.log(`Marking notification ${id} as read`);
        
        fetch('notifications.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: `action=mark_read&id=${id}`
            })
            .then(response => response.json())
            .then(data => {
            console.log('Response:', data);
            
            if (!data.success) {
                console.error('Failed to mark as read:', data.message);
                return;
            }
            
            // Find the notification item
            const notificationItem = document.querySelector(`.notification-item[data-id="${id}"]`);
            if (notificationItem) {
                const actionsDiv = notificationItem.querySelector('.notification-actions');
                notificationItem.classList.remove('unread');
                actionsDiv.innerHTML = `
                    <button class="btn btn-link btn-sm text-decoration-none p-0 mark-unread" onclick="markAsUnread(${id})">
                        Mark as unread
                    </button>
                `;
            }
            
            // Update all notifications to refresh counts
            updateNotifications();
        })
        .catch(error => {
            console.error('Error marking as read:', error);
        });
    };
    
    // Function to mark notification as unread
    window.markAsUnread = function(id) {
        console.log(`Marking notification ${id} as unread`);
        
        fetch('notifications.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: `action=mark_unread&id=${id}`
        })
        .then(response => response.json())
        .then(data => {
            console.log('Response:', data);
            
            if (!data.success) {
                console.error('Failed to mark as unread:', data.message);
                return;
            }
            
            // Find the notification item
            const notificationItem = document.querySelector(`.notification-item[data-id="${id}"]`);
            if (notificationItem) {
                const actionsDiv = notificationItem.querySelector('.notification-actions');
                notificationItem.classList.add('unread');
                actionsDiv.innerHTML = `
                    <button class="btn btn-link btn-sm text-decoration-none p-0 mark-read" onclick="markAsRead(${id})">
                        Mark as read
                    </button>
                `;
            }
            
            // Update all notifications to refresh counts
            updateNotifications();
        })
        .catch(error => {
            console.error('Error marking as unread:', error);
        });
    };
    
    // Handle notification click for details
    if (notificationList) {
        notificationList.addEventListener('click', function(e) {
            const notificationItem = e.target.closest('.notification-item');
            
            if (!notificationItem) {
                return;
            }

            if (e.target.classList.contains('mark-read') || e.target.classList.contains('mark-unread')) {
                return;
            }
            
            const id = notificationItem.dataset.id;
            const title = notificationItem.querySelector('h6').textContent;
            const message = notificationItem.querySelector('p').textContent;
            const time = notificationItem.querySelector('small').textContent;
            const icon = notificationItem.querySelector('i').className;
            
            // Update modal content
            const notificationIconElement = document.querySelector('.notification-icon');
            if (notificationIconElement) notificationIconElement.className = icon;
            const notificationTitleElement = document.querySelector('.notification-title');
            if (notificationTitleElement) notificationTitleElement.textContent = title;
            const notificationMessageElement = document.querySelector('.notification-message');
            if (notificationMessageElement) notificationMessageElement.textContent = message;
            const notificationTimeElement = document.querySelector('.notification-time');
            if (notificationTimeElement) notificationTimeElement.textContent = time;
            
            // Show modal
            notificationModal.show();
            
            // Mark as read if unread
            if (notificationItem.classList.contains('unread')) {
                markAsRead(id);
            }
        });
    } else {
        console.error('Notification list element .notification-list not found!');
    }
    
    // Mark all notifications as read
    if (markAllReadBtn) {
        markAllReadBtn.addEventListener('click', function() {
            fetch('notifications.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: 'action=mark_all_read'
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    console.error('Failed to mark all as read:', data.message);
                    return;
                }
                updateNotifications();
            })
            .catch(error => {
                console.error('Error marking all as read:', error);
            });
        });
    }
    
    // Update notifications every 30 seconds
    setInterval(updateNotifications, 30000);
    
    // Initial load of notifications
    updateNotifications();
});
</script>
<?php
}
?> 