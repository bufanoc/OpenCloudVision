<?php
// No need for requires here as they're already included in index.php
?>

<main class="content">
    <div class="container-fluid">
        <!-- Add Switch Form -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Add New Network Switch</h5>
                    </div>
                    <div class="card-body">
                        <form id="addSwitchForm" method="post">
                            <div class="form-group mb-3">
                                <label for="ip_address">IPv4 Address</label>
                                <input type="text" class="form-control" id="ip_address" name="ip_address" required 
                                       pattern="^(\d{1,3}\.){3}\d{1,3}$" 
                                       title="Please enter a valid IPv4 address">
                            </div>
                            <div class="form-group mb-3">
                                <label for="username">Username</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <button type="submit" class="btn btn-primary" name="add_switch">Add Switch</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Switch Grid -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Network Switch Inventory</h5>
                        <div class="legend">
                            <span class="badge bg-secondary me-2">Offline</span>
                            <span class="badge bg-danger me-2">Error</span>
                            <span class="badge bg-warning me-2">Connecting</span>
                            <span class="badge bg-success">Connected</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="switchGrid" class="row g-4">
                            <!-- Switch icons will be dynamically added here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Switch Details Modal -->
        <div class="modal fade" id="switchDetailsModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Switch Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Switch details will be loaded here -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" id="offboardSwitch">Offboard Switch</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Custom Styles -->
<style>
.switch-icon {
    width: 150px;
    height: 150px;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 15px;
    text-align: center;
    position: relative;
    cursor: pointer;
    transition: all 0.3s ease;
}

.switch-icon:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.switch-status {
    position: absolute;
    top: 10px;
    right: 10px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background-color: #6c757d;
}

.switch-status.connecting {
    background-color: #dc3545;
    animation: pulse 1.5s infinite;
}

.switch-status.connected {
    background-color: #198754;
}

.switch-status.error {
    background-color: #dc3545;
}

@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.4; }
    100% { opacity: 1; }
}

.switch-icon i {
    font-size: 3rem;
    color: #2c3e50;
    margin-bottom: 10px;
}

.switch-info {
    font-size: 0.9rem;
    color: #666;
}
</style>

<!-- Custom Scripts -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    loadSwitches();
    
    // Handle switch form submission
    document.getElementById('addSwitchForm').addEventListener('submit', function(e) {
        e.preventDefault();
        addSwitch(new FormData(this));
    });
    
    // Handle switch offboarding
    document.getElementById('offboardSwitch').addEventListener('click', function() {
        const switchId = this.getAttribute('data-switch-id');
        if (switchId) {
            offboardSwitch(switchId);
        }
    });
});

function loadSwitches() {
    fetch('api/switches.php')
        .then(response => response.json())
        .then(switches => {
            const grid = document.getElementById('switchGrid');
            grid.innerHTML = '';
            
            switches.forEach(switch_ => {
                const switchElement = createSwitchElement(switch_);
                grid.appendChild(switchElement);
            });
        })
        .catch(error => console.error('Error loading switches:', error));
}

function createSwitchElement(switch_) {
    const col = document.createElement('div');
    col.className = 'col-md-3';
    
    col.innerHTML = `
        <div class="switch-icon" data-switch-id="${switch_.id}">
            <div class="switch-status ${switch_.status.toLowerCase()}"></div>
            <i class="bi bi-hdd-network"></i>
            <div class="switch-info">
                <div class="fw-bold">${switch_.hostname || switch_.ip_address}</div>
                <div class="small">${switch_.model_name || 'Unknown Model'}</div>
            </div>
        </div>
    `;
    
    col.querySelector('.switch-icon').addEventListener('click', () => showSwitchDetails(switch_.id));
    return col;
}

function showSwitchDetails(switchId) {
    fetch(`api/switches.php?id=${switchId}`)
        .then(response => response.json())
        .then(details => {
            const modal = document.getElementById('switchDetailsModal');
            const modalBody = modal.querySelector('.modal-body');
            
            modalBody.innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <h6>Basic Information</h6>
                        <p><strong>Hostname:</strong> ${details.hostname || 'N/A'}</p>
                        <p><strong>IP Address:</strong> ${details.ip_address}</p>
                        <p><strong>Model:</strong> ${details.model_name || 'N/A'}</p>
                        <p><strong>Serial Number:</strong> ${details.serial_number || 'N/A'}</p>
                        <p><strong>EOS Version:</strong> ${details.eos_version || 'N/A'}</p>
                    </div>
                    <div class="col-md-6">
                        <h6>Status Information</h6>
                        <p><strong>Status:</strong> <span class="badge bg-${details.status === 'Connected' ? 'success' : 'danger'}">${details.status}</span></p>
                        <p><strong>Last Seen:</strong> ${details.last_seen}</p>
                    </div>
                </div>
            `;
            
            document.getElementById('offboardSwitch').setAttribute('data-switch-id', switchId);
            
            const modalInstance = new bootstrap.Modal(modal);
            modalInstance.show();
        })
        .catch(error => console.error('Error loading switch details:', error));
}

function addSwitch(formData) {
    fetch('api/switches.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            loadSwitches();
            document.getElementById('addSwitchForm').reset();
        } else {
            alert('Error adding switch: ' + result.message);
        }
    })
    .catch(error => console.error('Error adding switch:', error));
}

function offboardSwitch(switchId) {
    if (confirm('Are you sure you want to offboard this switch?')) {
        fetch(`api/switches.php?id=${switchId}`, {
            method: 'DELETE'
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                bootstrap.Modal.getInstance(document.getElementById('switchDetailsModal')).hide();
                loadSwitches();
            } else {
                alert('Error offboarding switch: ' + result.message);
            }
        })
        .catch(error => console.error('Error offboarding switch:', error));
    }
}
</script>
