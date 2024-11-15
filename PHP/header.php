<?php
session_start();
include "../SQL_FILE/database.php";

$user_id = $_SESSION['User_ID'] ?? null;

// Get unread notifications count for the logged-in user
$notification_count = 0;
if ($user_id)
{
  $query = "
        SELECT COUNT(*) AS unread_count
        FROM notification n
        LEFT JOIN notification_read nr ON n.notice_id = nr.notice_id AND nr.user_id = ?
        WHERE nr.notice_id IS NULL
    ";
  $stmt = $conn->prepare($query);
  $stmt->bind_param("i", $user_id);
  $stmt->execute();
  $result = $stmt->get_result();
  $row = $result->fetch_assoc();
  $notification_count = $row['unread_count'] ?? 0;
}
?>

<!-- Add the rest of your header and styles here -->


<style>
  @import url('https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap');

  * {
    font-family: Roboto, sans-serif;
  }

  header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    height: 12.5vh;
    padding-left: 1vw;
    padding-right: 1vw;
    background-color: #fff;
    border-bottom-style: solid;
    border-bottom-width: 1px;
    position: sticky;
    top: 0;
    z-index: 1;
  }

  .logo {
    display: flex;
    align-items: center;
  }

  .logo img {
    height: 7.5vh;
  }

  .user-actions {
    display: flex;
    align-items: center;
    position: relative;
  }

  .user-actions a {
    margin-right: 15px;
    color: #000000;
    position: relative;
  }

  .user-actions a img {
    height: 5vh;
  }

  .notification-badge {
    position: absolute;
    top: 5px;
    right: 0;
    background-color: red;
    color: white;
    border-radius: 50%;
    padding: 2px 6px;
    font-size: 0.8em;
  }

  /* Modal styling */
  .modal {
    display: none; /* This hides the modal initially */
    position: fixed;
    z-index: 1;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0, 0, 0, 0.4); /* Adds a background overlay */
}

.modal-content {
    background-color: #fff;
    margin: 15% auto;
    padding: 20px;
    border: 1px solid #ddd;
    width: 50%;
    border-radius: 8px;
}


  .close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
  }
</style>

<header>
  <div class="logo">
    <a href="<?= $user_role === 'ADMIN' ? 'adminDashboard.php' : 'dashboard.php'; ?>">
      <img src="../img/logo.png" alt="Eco Waste System logo">
    </a>
    <div class="title">
      <h1>Eco Waste System</h1>
    </div>
  </div>
  <nav class="user-actions">
    <!-- Notification Icon with Badge -->
    <a href="#" onclick="showNotifications()">
      <img src="../img/notification.png" alt="Notification">
      <?php if ($notification_count > 0): ?>
        <span class="notification-badge"><?= $notification_count ?></span>
      <?php endif; ?>
    </a>
    <a href="#"><img src="../img/setting.png" alt="Setting"></a>
    <a href="#"><img src="../img/profile.png" alt="Profile"></a>
  </nav>
</header>

<!-- Notification Modal -->
<div id="notificationModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeNotifications()">&times;</span>
        <h3>Notifications</h3>
        <ul id="notificationList"></ul>
    </div>
</div>


<script>
  function showNotifications() {
    const modal = document.getElementById("notificationModal");
    modal.style.display = "block"; // Ensure this line is executed



    fetch("fetch_notifications.php")
      .then(response => response.json())
      .then(data => {
        const notificationList = document.getElementById("notificationList");
        notificationList.innerHTML = ""; // Clear existing notifications

        if (data.notifications && data.notifications.length > 0) {
          data.notifications.forEach(notification => {
            const listItem = document.createElement("li");
            listItem.textContent = `${notification.title}: ${notification.message}`;
            notificationList.appendChild(listItem);
          });
        } else {
          notificationList.innerHTML = "<li>No new notifications.</li>";
        }

        // Mark notifications as read
        markNotificationsAsRead();
      })
      .catch(error => console.error("Error fetching notifications:", error));
  }

  function markNotificationsAsRead() {
    fetch("mark_notifications_read.php", { method: "POST" })
      .then(() => {
        const badge = document.querySelector(".notification-badge");
        if (badge) {
          badge.style.display = "none";
        }
      })
      .catch(error => console.error("Error marking notifications as read:", error));
  }

  function closeNotifications() {
    document.getElementById("notificationModal").style.display = "none";
  }

  // Close modal on outside click
  window.onclick = function (event) {
    const modal = document.getElementById("notificationModal");
    if (event.target === modal) {
      modal.style.display = "none";
    }
  };

</script>