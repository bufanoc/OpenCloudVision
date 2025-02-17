<aside>
    <nav class="sidebar">
        <ul>
            <li>
                <a href="index.php?page=dashboard" class="<?php echo ($_GET['page'] ?? '') === 'dashboard' ? 'active' : ''; ?>">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>
            <li>
                <a href="index.php?page=inventory" class="<?php echo ($_GET['page'] ?? '') === 'inventory' ? 'active' : ''; ?>">
                    <i class="bi bi-box-seam"></i> Inventory
                </a>
            </li>
            <li>
                <a href="index.php?page=switch_management" class="<?php echo ($_GET['page'] ?? '') === 'switch_management' ? 'active' : ''; ?>">
                    <i class="bi bi-hdd-network"></i> Switch Management
                </a>
            </li>
            <li>
                <a href="index.php?page=configuration_editor" class="<?php echo ($_GET['page'] ?? '') === 'configuration_editor' ? 'active' : ''; ?>">
                    <i class="bi bi-gear"></i> Configuration Editor
                </a>
            </li>
            <li>
                <a href="index.php?page=physical_network" class="<?php echo ($_GET['page'] ?? '') === 'physical_network' ? 'active' : ''; ?>">
                    <i class="bi bi-diagram-3"></i> Physical Network
                </a>
            </li>
            <li>
                <a href="index.php?page=virtual_network" class="<?php echo ($_GET['page'] ?? '') === 'virtual_network' ? 'active' : ''; ?>">
                    <i class="bi bi-cloud-network"></i> Virtual Network
                </a>
            </li>
        </ul>
    </nav>
</aside>
