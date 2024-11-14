<?php 
session_start();
$user_id = $_SESSION['User_ID'];
?>

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

  .title{
    display: flex;
    justify-content: space-around;
    align-items: center;
    padding-left: 1vw;
    padding-right: 1vw;
  }

  .title h1{
    padding: 1vh;
    color: #418952;
  }

  .user-actions {
    display: flex;
    align-items: center;
  }

  .user-actions a {
    margin-right: 15px;
    color: #666;
  }

  .user-actions a img{
      height: 5vh;
  }
</style>

<header>
  <link rel="icon" href="../img/logo.ico">
  <div class="logo">
    <div>
        <img src="../img/logo.png" alt="Eco Waste System logo">
    </div>
    <div class="title">
      <h1>Eco Waste System</h1>
    </div>
  </div>
  <div>
    <h2>
      <?php
        switch ($filename){
          case 'Report-Issues.php':
            echo'Report Issue';
            break;
          case 'dashboard.php':
            echo 'Dashboard';
            break;
          case 'schedulePickUp.php';
            echo'Schedule Pick Up';
            break;
          case 'manageNotification.php';
            echo'Manage Notification';
            break; 
          default:
            echo 'Welcome';
            break;
        }
      ?>
    </h2>
  </div>
  <nav class="user-actions">
    <a href="#"><img src="../img/notification.png" alt="Notification"></a>
    <a href="#"><img src="../img/setting.png" alt="Setting"></a>
    <a href="#"><img src="../img/profile.png" alt="Profile"></a>
  </nav>
</header>
