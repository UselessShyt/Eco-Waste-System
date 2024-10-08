<style>
header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px;
  background-color: #fff;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.logo {
    display: flex;
    align-items: center;
}

.logo img {
  max-height: 5em;
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
    max-height: 50px;
}
</style>

<header>
    <div class="logo">
        <div>
            <img src="../img/logo.png" alt="Eco Waste System">
        </div>
        <div id="title">
            <h1>Eco Waste System</h1>
        </div>
    </div>
    
    <nav class="user-actions">
        <a href="#"><img src="../img/notification.png" alt="Notification"></a>
        <a href="#"><img src="../img/setting.png" alt="Setting"></a>
        <a href="#"><img src="../img/profile.png" alt="Profile"></a>
    </nav>
</header>