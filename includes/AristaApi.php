<?php
class AristaApi {
    private $ip;
    private $username;
    private $password;
    private $baseUrl;

    public function __construct($ip, $username, $password) {
        $this->ip = $ip;
        $this->username = $username;
        $this->password = $password;
        $this->baseUrl = "https://{$ip}/command-api";
    }

    public function runCommands($commands, $version = "latest") {
        $data = [
            'jsonrpc' => '2.0',
            'method' => 'runCmds',
            'params' => [
                'version' => $version,
                'cmds' => $commands,
                'format' => 'json'
            ],
            'id' => 1
        ];

        error_log("Connecting to switch at {$this->ip}...");
        error_log("Request data: " . json_encode($data, JSON_PRETTY_PRINT));

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->baseUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, "{$this->username}:{$this->password}");
        
        // SSL Options for self-signed certificates
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json'
        ]);

        // Enable verbose output for debugging
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        $verbose = fopen('php://temp', 'w+');
        curl_setopt($ch, CURLOPT_STDERR, $verbose);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);

        // Get verbose log
        rewind($verbose);
        $verboseLog = stream_get_contents($verbose);
        error_log("Verbose log: " . $verboseLog);

        if ($error) {
            error_log("cURL Error: " . $error);
            throw new Exception("cURL Error: $error\nVerbose log:\n$verboseLog");
        }

        if ($httpCode !== 200) {
            error_log("HTTP Error: " . $httpCode);
            error_log("Response: " . $response);
            throw new Exception("HTTP Error $httpCode: $response");
        }

        curl_close($ch);
        fclose($verbose);

        error_log("Raw response: " . $response);
        $result = json_decode($response, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("JSON Error: " . json_last_error_msg());
            throw new Exception("Invalid JSON response: " . json_last_error_msg() . "\nResponse: " . $response);
        }

        if (isset($result['error'])) {
            error_log("API Error: " . json_encode($result['error']));
            throw new Exception("API Error: " . json_encode($result['error']));
        }

        if (!isset($result['result'])) {
            error_log("Missing result in response: " . json_encode($result));
            throw new Exception("Missing result in response: " . json_encode($result));
        }

        return $result;
    }

    public function getSwitchInfo() {
        try {
            $commands = [
                "show version",
                "show interfaces status",
                "show ip interface brief"  // Fixed command
            ];

            error_log("Getting switch info for {$this->ip}...");
            $result = $this->runCommands($commands);
            error_log("Got switch info: " . json_encode($result['result'], JSON_PRETTY_PRINT));
            return $result;
        } catch (Exception $e) {
            error_log("Error getting switch info: " . $e->getMessage());
            throw new Exception("Failed to get switch info: " . $e->getMessage());
        }
    }
}
?>
