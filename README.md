# OpenCloudVision (Clarity)

A modern web application for managing and visualizing Arista network switches, developed by The Bufano Group.

## Features

- Switch inventory management
- Network topology visualization
- Real-time switch status monitoring
- Configuration management
- Physical and virtual network views

## Technology Stack

- **Backend**: PHP 8+ with Apache2
- **Database**: MySQL/PostgreSQL
- **Frontend**: HTML5, CSS3, JavaScript
- **UI Framework**: Bootstrap
- **Network Visualization**: D3.js/Cytoscape.js
- **API Integration**: Arista eAPI

## Requirements

- Ubuntu Server 22.04 or later
- Apache2 web server
- PHP 8+ with extensions:
  - cURL
  - JSON
  - PDO
- MySQL or PostgreSQL database
- Modern web browser

## Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/bufano-labs/OpenCloudVision.git
   cd OpenCloudVision
   ```

2. Set up the web server:
   ```bash
   # Configure Apache virtual host
   sudo cp config/clarity.conf /etc/apache2/sites-available/
   sudo a2ensite clarity.conf
   sudo systemctl restart apache2
   ```

3. Create the database:
   ```bash
   # Import schema
   mysql -u root -p < database/schema.sql
   ```

4. Configure the application:
   ```bash
   # Copy example config
   cp config/config.example.php config/config.php
   # Edit configuration
   nano config/config.php
   ```

## Usage

1. Access the web interface at `http://your-server/` or your configured domain
2. Use the sidebar navigation to access different features:
   - Dashboard
   - Inventory
   - Switch Management
   - Configuration Editor
   - Physical Network
   - Virtual Network

## Development

This project is under active development. Key areas of focus:

- Rich network visualization
- Real-time monitoring
- Configuration management
- Topology discovery

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Authors

- The Bufano Group

## Acknowledgments

- Arista Networks for their eAPI
- The open-source community for various tools and libraries used in this project

