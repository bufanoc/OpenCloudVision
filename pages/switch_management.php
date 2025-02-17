<?php
// No need for requires here as they're already included in index.php

$switchModel = new NetworkSwitch();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_switch'])) {
    try {
        $api = new AristaApi($_POST['ip_address'], $_POST['username'], $_POST['password']);
        $result = $api->getSwitchInfo();
        
        if (!isset($result['result'])) {
            throw new Exception('Failed to retrieve switch information');
        }
        
        $switchId = $switchModel->addSwitch(
            $_POST['ip_address'],
            $_POST['username'],
            $_POST['password'],
            $result['result'][0]  // Version information
        );
        
        $switchModel->updateInterfaces(
            $switchId,
            $result['result'][1],  // Interface status
            $result['result'][2]   // IP interfaces
        );
        
        $success = "Switch added successfully!";
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Get all switches
try {
    $switches = $switchModel->getAllSwitches();
} catch (Exception $e) {
    $error = $e->getMessage();
    $switches = [];
}
?>

<main class="content">
    <div class="container-fluid">
        <?php if (isset($success)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($success); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($error); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Switch Management</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSwitchModal">
                                <i class="bi bi-plus-circle"></i> Add New Switch
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Hostname</th>
                                        <th>IP Address</th>
                                        <th>Model</th>
                                        <th>EOS Version</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($switches as $switch): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($switch['hostname'] ?? 'N/A'); ?></td>
                                            <td><?php echo htmlspecialchars($switch['ip_address']); ?></td>
                                            <td><?php echo htmlspecialchars($switch['model_name'] ?? 'N/A'); ?></td>
                                            <td><?php echo htmlspecialchars($switch['eos_version'] ?? 'N/A'); ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo $switch['status'] === 'Connected' ? 'success' : 'danger'; ?>">
                                                    <?php echo htmlspecialchars($switch['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-info view-switch" data-switch-id="<?php echo $switch['id']; ?>">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger delete-switch" data-switch-id="<?php echo $switch['id']; ?>">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Add Switch Modal -->
<div class="modal fade" id="addSwitchModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Switch</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="ip_address" class="form-label">IP Address</label>
                        <input type="text" class="form-control" id="ip_address" name="ip_address" required
                               pattern="^(\d{1,3}\.){3}\d{1,3}$"
                               title="Please enter a valid IPv4 address">
                    </div>
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" name="add_switch">Add Switch</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Switch Modal -->
<div class="modal fade" id="viewSwitchModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Switch Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Content will be loaded dynamically -->
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle view switch button clicks
    document.querySelectorAll('.view-switch').forEach(button => {
        button.addEventListener('click', function() {
            const switchId = this.getAttribute('data-switch-id');
            fetch(`api/switches.php?id=${switchId}`)
                .then(response => response.json())
                .then(switch_ => {
                    const modal = document.getElementById('viewSwitchModal');
                    const modalBody = modal.querySelector('.modal-body');
                    modalBody.innerHTML = `
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Basic Information</h6>
                                <p><strong>Hostname:</strong> ${switch_.hostname || 'N/A'}</p>
                                <p><strong>IP Address:</strong> ${switch_.ip_address}</p>
                                <p><strong>Model:</strong> ${switch_.model_name || 'N/A'}</p>
                                <p><strong>Serial Number:</strong> ${switch_.serial_number || 'N/A'}</p>
                                <p><strong>EOS Version:</strong> ${switch_.eos_version || 'N/A'}</p>
                            </div>
                            <div class="col-md-6">
                                <h6>Status Information</h6>
                                <p><strong>Status:</strong> 
                                    <span class="badge bg-${switch_.status === 'Connected' ? 'success' : 'danger'}">
                                        ${switch_.status}
                                    </span>
                                </p>
                                <p><strong>Last Seen:</strong> ${switch_.last_seen || 'N/A'}</p>
                            </div>
                        </div>
                    `;
                    new bootstrap.Modal(modal).show();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error loading switch details');
                });
        });
    });

    // Handle delete switch button clicks
    document.querySelectorAll('.delete-switch').forEach(button => {
        button.addEventListener('click', function() {
            if (confirm('Are you sure you want to delete this switch?')) {
                const switchId = this.getAttribute('data-switch-id');
                fetch(`api/switches.php?id=${switchId}`, {
                    method: 'DELETE'
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        location.reload();
                    } else {
                        alert('Error: ' + result.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error deleting switch');
                });
            }
        });
    });
});
</script>
