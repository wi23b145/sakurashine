<nav class="navbar">
    <!-- Logo 
    <div class="logo">
        <img src="/WEB1/css/img/logo.jpg" alt="Logo">
    </div
    >-->


    <!-- Navigation Links -->
    <ul>
        <li><a href="/frontend/sites/index.php">Home</a></li>
        
    </ul>

    <!-- User Icon with Dropdown -->
    <div class="user-menu">
        <button class="user-menu-btn">
            <img src="/WEB1/css/img/user-icon2.jpeg" alt="User Icon" class="user-icon">
            <span class="arrow">&#9660;</span>
        </button>
        <ul class="dropdown-menu">
            <?php if (isset($_SESSION["username"])): ?>
                <!-- Optionen für eingeloggte Benutzer -->
                
                
                <?php if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin'): ?>
                    <!-- Optionen für normale Benutzer -->
                    
                <?php endif; ?>

                <!-- Admin-spezifische Optionen 
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                   
                    <li><a href="/WEB1/admin/user_admin.php">Benutzerverwaltung</a></li>
                <?php endif; ?>

                <li><a href="/WEB1/site/logout.php">Logout</a></li>
            <?php else: ?>
                <!--- Optionen für Gäste -->
                <li><a href="/WEB1/site/login.php">Login</a></li>
                <li><a href="/WEB1/site/signup.php">Registrieren</a></li>
            <?php endif; ?>
        </ul>
    </div>
</nav>
