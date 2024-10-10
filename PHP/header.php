<style>
  @import url('https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap');

  * {
    font-family: Roboto, sans-serif;
  }

  header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    background-color: #fff;
    border-bottom-style: solid;
    border-bottom-width: 1px;
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
  <link rel="icon" href="../img/logo.ico">
  <div class="logo">
    <div>
        <img src="../img/logo.png" alt="Eco Waste System">
    </div>
    <div id="title">
        <h1>Eco Waste System</h1>
    </div>
  </div>
  <div>
    <h2>Title</h2>
  </div>
  <nav class="user-actions">
    <a href="#"><img src="../img/notification.png" alt="Notification"></a>
    <a href="#"><img src="../img/setting.png" alt="Setting"></a>
    <a href="#"><img src="../img/profile.png" alt="Profile"></a>
  </nav>
</header>