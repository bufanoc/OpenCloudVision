#!/bin/bash

# Create directories if they don't exist
mkdir -p assets/css
mkdir -p assets/js
mkdir -p assets/vendor

# Download Bootstrap
wget https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css -O assets/vendor/bootstrap.min.css
wget https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js -O assets/vendor/bootstrap.bundle.min.js

# Download jQuery
wget https://code.jquery.com/jquery-3.7.0.min.js -O assets/vendor/jquery.min.js

# Download D3.js for network visualization
wget https://d3js.org/d3.v7.min.js -O assets/vendor/d3.min.js

echo "Frontend dependencies have been downloaded successfully!"
