<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clarity - The Bufano Group</title>
    <link rel="stylesheet" href="assets/vendor/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
</head>
<body>
<header>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">
                <span class="brand-name">Clarity</span>
                <span class="brand-subtitle">by: The Bufano Group</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($_GET['page'] ?? '') === 'dashboard' ? 'active' : ''; ?>" 
                           href="index.php?page=dashboard">
                           <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($_GET['page'] ?? '') === 'switch_management' ? 'active' : ''; ?>" 
                           href="index.php?page=switch_management">
                           <i class="bi bi-hdd-network"></i> Switch Management
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($_GET['page'] ?? '') === 'configuration_editor' ? 'active' : ''; ?>" 
                           href="index.php?page=configuration_editor">
                           <i class="bi bi-gear"></i> Configuration Editor
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($_GET['page'] ?? '') === 'physical_network' ? 'active' : ''; ?>" 
                           href="index.php?page=physical_network">
                           <i class="bi bi-diagram-3"></i> Physical Network
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($_GET['page'] ?? '') === 'virtual_network' ? 'active' : ''; ?>" 
                           href="index.php?page=virtual_network">
                           <i class="bi bi-cloud-network"></i> Virtual Network
                        </a>
                    </li>
                </ul>
                <div class="d-flex">
                    <span class="navbar-text me-3" id="currentTime"></span>
                    <span class="navbar-text" id="connectionStatus">Connected</span>
                </div>
            </div>
        </div>
    </nav>
</header>

<script>
    // Update current time
    function updateTime() {
        const now = new Date();
        document.getElementById('currentTime').textContent = now.toLocaleTimeString();
    }
    setInterval(updateTime, 1000);
    updateTime();
</script>
