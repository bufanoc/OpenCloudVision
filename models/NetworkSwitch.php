<?php
require_once __DIR__ . '/../includes/Database.php';

class NetworkSwitch {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function addSwitch($ip, $username, $password, $details) {
        try {
            // Check if switch already exists
            $stmt = $this->db->query(
                "SELECT id FROM switches WHERE ip_address = ?",
                [$ip]
            );
            
            if ($stmt->rowCount() > 0) {
                throw new Exception("Switch with IP $ip already exists");
            }

            // Add the switch
            $sql = "INSERT INTO switches (
                ip_address, username, password, hostname, 
                model_name, serial_number, system_mac_address, eos_version,
                status
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $this->db->query($sql, [
                $ip,
                $username,
                $password,
                $details['hostname'] ?? null,
                $details['modelName'] ?? null,
                $details['serialNumber'] ?? null,
                $details['systemMacAddress'] ?? null,
                $details['version'] ?? null,
                'Connected'
            ]);

            return $this->db->getConnection()->lastInsertId();
        } catch (Exception $e) {
            throw new Exception("Failed to add switch: " . $e->getMessage());
        }
    }

    public function updateInterfaces($switchId, $interfaces, $ipInterfaces) {
        try {
            // Start transaction
            $this->db->getConnection()->beginTransaction();

            // Delete existing interfaces for this switch
            $this->db->query(
                "DELETE FROM interfaces WHERE switch_id = ?",
                [$switchId]
            );

            // Prepare interface insertion statement
            $sql = "INSERT INTO interfaces (
                switch_id, name, type, status, vlan_id, 
                duplex, speed, ip_address
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

            // Insert interface information
            foreach ($interfaces as $name => $info) {
                $ipAddress = null;
                if (isset($ipInterfaces['interfaces'][$name]['interfaceAddress']['ipAddr']['address'])) {
                    $ipAddress = $ipInterfaces['interfaces'][$name]['interfaceAddress']['ipAddr']['address'];
                }

                $this->db->query($sql, [
                    $switchId,
                    $name,
                    $info['interfaceType'] ?? null,
                    $info['linkStatus'] ?? null,
                    $info['vlanInformation']['vlanId'] ?? null,
                    $info['duplex'] ?? null,
                    $info['bandwidth'] ?? null,
                    $ipAddress
                ]);
            }

            // Commit transaction
            $this->db->getConnection()->commit();
        } catch (Exception $e) {
            // Rollback on error
            $this->db->getConnection()->rollBack();
            throw new Exception("Failed to update interfaces: " . $e->getMessage());
        }
    }

    public function getAllSwitches() {
        try {
            $stmt = $this->db->query(
                "SELECT * FROM switches ORDER BY hostname, ip_address",
                []
            );
            return $stmt->fetchAll();
        } catch (Exception $e) {
            throw new Exception("Failed to get switches: " . $e->getMessage());
        }
    }

    public function getSwitchById($id) {
        try {
            $stmt = $this->db->query(
                "SELECT s.*, 
                        GROUP_CONCAT(i.name, ':', i.status, ':', COALESCE(i.ip_address, 'N/A')) as interfaces
                 FROM switches s
                 LEFT JOIN interfaces i ON s.id = i.switch_id
                 WHERE s.id = ?
                 GROUP BY s.id",
                [$id]
            );
            
            $switch = $stmt->fetch();
            
            if (!$switch) {
                throw new Exception("Switch not found");
            }
            
            // Parse interfaces into a structured format
            if ($switch['interfaces']) {
                $interfacesList = [];
                foreach (explode(',', $switch['interfaces']) as $interface) {
                    list($name, $status, $ip) = explode(':', $interface);
                    $interfacesList[] = [
                        'name' => $name,
                        'status' => $status,
                        'ip_address' => $ip
                    ];
                }
                $switch['interfaces'] = $interfacesList;
            } else {
                $switch['interfaces'] = [];
            }
            
            return $switch;
        } catch (Exception $e) {
            throw new Exception("Failed to get switch: " . $e->getMessage());
        }
    }

    public function deleteSwitch($id) {
        try {
            $this->db->query(
                "DELETE FROM switches WHERE id = ?",
                [$id]
            );
            return true;
        } catch (Exception $e) {
            throw new Exception("Failed to delete switch: " . $e->getMessage());
        }
    }

    public function updateSwitchStatus($id, $status) {
        try {
            $this->db->query(
                "UPDATE switches SET status = ?, last_seen = CURRENT_TIMESTAMP WHERE id = ?",
                [$status, $id]
            );
            return true;
        } catch (Exception $e) {
            throw new Exception("Failed to update switch status: " . $e->getMessage());
        }
    }
}
