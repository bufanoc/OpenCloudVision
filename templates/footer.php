<footer class="footer">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <p>&copy; 2025 Bufano Labs. All rights reserved.</p>
            </div>
            <div class="col-md-6 text-end">
                <p>Version 1.0.0 | <span id="footerStatus">System Status: Operational</span></p>
            </div>
        </div>
    </div>
</footer>

<!-- JavaScript Dependencies -->
<script src="assets/vendor/jquery.min.js"></script>
<script src="assets/vendor/bootstrap.bundle.min.js"></script>
<script src="assets/vendor/d3.min.js"></script>

<!-- Custom Scripts -->
<script>
    // Update system status
    function updateSystemStatus() {
        const status = document.getElementById('footerStatus');
        const connectionStatus = document.getElementById('connectionStatus');
        
        // Check if we're connected to the database and API
        // This is a placeholder - you can implement actual checks later
        const isConnected = true;
        
        if (isConnected) {
            status.textContent = 'System Status: Operational';
            status.className = 'text-success';
            connectionStatus.textContent = 'Connected';
            connectionStatus.className = 'navbar-text text-success';
        } else {
            status.textContent = 'System Status: Issues Detected';
            status.className = 'text-danger';
            connectionStatus.textContent = 'Disconnected';
            connectionStatus.className = 'navbar-text text-danger';
        }
    }
    
    // Update status every 30 seconds
    setInterval(updateSystemStatus, 30000);
    updateSystemStatus();
</script>
</body>
</html>
