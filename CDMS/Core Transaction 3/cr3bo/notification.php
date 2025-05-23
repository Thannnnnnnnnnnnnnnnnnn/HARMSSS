<?php
date_default_timezone_set('Asia/Manila');
include 'conn.php';

$user = $_SESSION['user'];
$_SESSION['User_ID'] = $user['User_ID'];
$role = $_SESSION['user']['role'];
$notif = $connections["cr3_re"];


    $notif1 = "SELECT n.*, CONCAT(u.first_name, ' ', u.last_name) AS Name
               FROM notifications n 
               LEFT JOIN cr3_re.user_account u ON n.User_ID = u.user_id 
               ORDER BY n.date_sent DESC";
    $stmt_notif = mysqli_prepare($notif, $notif1);
    mysqli_stmt_execute($stmt_notif);
    $resultnotif = mysqli_stmt_get_result($stmt_notif);

function timeAgo($date_sent) {
    $timestamp = strtotime($date_sent);
    $current_time = time();
    $diff = $current_time - $timestamp;

    if ($diff < 60) {
        return "Just now";
    } elseif ($diff < 3600) {
        $minutes = floor($diff / 60);
        return $minutes . " minute" . ($minutes > 1 ? "s" : "") . " ago";
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . " hour" . ($hours > 1 ? "s" : "") . " ago";
    } elseif ($diff < 172800) {
        return "Yesterday";
    } else {
        $days = floor($diff / 86400);
        return $days . " days ago";
    }
}

if (isset($_POST['id'])) {
    $id = intval($_POST['id']);
    mysqli_query($notif, "UPDATE notifications SET status='Read' WHERE notifID=$id");
    exit; 
}

if (isset($_POST['mark_all'])) {
    mysqli_query($notif, "UPDATE notifications SET status='Read' WHERE status='Unread'");
    exit; 
}
?>

<div x-data="notificationApp()" class="relative">
    <button 
        @click="dropdownOpen = !dropdownOpen" 
        aria-label="Notifications"
        class="text-[#4E3B2A] focus:outline-none border-r border-[#F7E6CA] pr-6 relative"
    >
        <i class="fa-regular fa-bell text-xl"></i>
        <span class="absolute top-0.5 right-5 block w-2.5 h-2.5 bg-[#594423] rounded-full"></span>
    </button>

    <div  
        x-show="dropdownOpen"
        @click.away="dropdownOpen = false"
        x-transition
        class="absolute left-[-120px] mt-2 w-[21vw] bg-white shadow-xl rounded-xl z-50 h-[90vh] flex flex-col"
        style="display: none;"
    >
        <div class="flex items-center justify-between px-4 py-3 border-b border-[#333]">
            <h3 class="text-sm font-semibold text-[#4E3B2A]">Notifications</h3>
            <button 
                class="flex items-center text-xs text-[#4E3B2A] transition hover:underline"
                @click="markAllAsRead"
            >
                <i class="fa-regular fa-circle-check mr-1"></i>
                Mark all as read
            </button>
        </div>

        <ul class="text-sm text-[#594423] overflow-y-auto flex-grow no-scrollbar" id="dime">
            <?php  
            if ($resultnotif) {
                while ($notification = mysqli_fetch_assoc($resultnotif)):
                    $notifData = [
                        'notifID' => $notification['notifID'],
                        'user'    => $notification['Name'] ?? 'Guest',
                        'message' => $notification['message'],
                        'date'    => timeAgo($notification['date_sent']),
                    ];
                    $notifJson = htmlspecialchars(json_encode($notifData), ENT_QUOTES, 'UTF-8');
            ?>
                    <li 
                        class="flex gap-3 items-start border-b border-gray-200 pb-3 p-2 rounded-md cursor-pointer <?php echo ($notification['status'] == 'Unread' ? 'bg-[#F7E6CA]' : ''); ?>"
                        x-data
                        @click='viewNotification(<?php echo $notifJson; ?>)'
                    >
                        <?php if ($notification['notifType'] == 'create'): ?>
                            <i class="fa-solid fa-plus-circle text-green-600 mt-1 text-lg"></i>
                        <?php elseif ($notification['notifType'] == 'delete'): ?>
                            <i class="fa-solid fa-trash text-red-600 mt-1 text-lg"></i>
                        <?php elseif ($notification['notifType'] == 'update'): ?>
                            <i class="fa-solid fa-pen-to-square text-blue-600 mt-1 text-lg"></i>
                        <?php elseif ($notification['notifType'] == 'cancel'): ?>
                            <i class="fa-solid fa-ban text-red-600 mt-1 text-lg"></i>
                        <?php endif; ?>

                        <div class="flex-1">
                            <p><strong><?php echo htmlspecialchars($notification['Name'] ?? 'Guest'); ?></strong> <?php echo htmlspecialchars($notification['message']); ?></p>
                            <span class="text-xs text-gray-500"><?php echo timeAgo($notification['date_sent']); ?></span>
                        </div>
                    </li>
                <?php endwhile;
                if (mysqli_num_rows($resultnotif) == 0) {
                    echo '<div class="text-center mt-5">No notifications found.<br></div>';
                }
            }
            ?>
        </ul>

        <div class="p-4 border-t border-[#eee]">
            <button 
                class="w-full bg-[#4E3B2A] text-white py-1.5 rounded-lg text-sm hover:bg-[#3a2f22] transition"
            >
                See All Notifications
            </button>
        </div>
    </div>

    <div 
        x-show="modalOpen" 
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
        style="display: none;"
    >
        <div class="bg-white rounded-lg p-6 w-[400px] space-y-4">
            <h2 class="text-lg font-bold text-[#4E3B2A]">Notification Details</h2>
            <div class="text-sm text-[#594423]">
                <p><strong>User:</strong> <span x-text="selectedNotification.user"></span></p>
                <p><strong>Message:</strong> <span x-text="selectedNotification.message"></span></p>
                <p class="text-xs text-gray-400" x-text="selectedNotification.date"></p>
            </div>
            <div class="flex justify-end">
                <button @click="closeModal" class="mt-4 px-4 py-2 bg-[#4E3B2A] text-white rounded-md text-sm hover:bg-[#3a2f22] transition">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function notificationApp() {
    return {
        dropdownOpen: false,
        modalOpen: false,
        selectedNotification: {},

        viewNotification(notification) {
            this.selectedNotification = notification;
            this.modalOpen = true;

            fetch(window.location.href, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'id=' + notification.notifID
            });
        },

        closeModal() {
            fetch(window.location.href, { 
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'id=' + this.selectedNotification.notifID
            }).then(() => {
                this.modalOpen = false;
                location.reload();
            });
        },

        markAllAsRead() {
            fetch(window.location.href, { 
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'mark_all=true'
            }).then(() => {
                location.reload();
            });
        }
    }
}
</script>