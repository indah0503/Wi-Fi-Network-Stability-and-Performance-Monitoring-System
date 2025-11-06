## System Overview

This project uses **five ESP32 modules**, each equipped with a **built-in 18650 battery slot** for efficiency in both design time and cost.
Each ESP32 is programmed to detect Wi-Fi network quality by scanning all available Access Points (APs) within range. The system identifies each AP using its **MAC Address**, allowing differentiation between multiple APs once the data is uploaded to the server.

To calculate **Wi-Fi coverage area (signal coverage)**, an additional function is implemented in the ESP32 firmware based on the formula described in the *Theory* section of the report.


## Database Configuration

A **MySQL database** is created with **five tables**, each corresponding to one ESP32 device.
Each table includes the following columns:

```
id, device_id, location, strength, distance, channel, channelLoad, interference, mac_address, time
```

These fields store structured Wi-Fi signal data sent by each ESP32.


## Data Transmission Workflow

Data transmission from ESP32 to MySQL is handled using a **PHP backend** (`submit.php`) running on an **Nginx web server**.

1. **Nginx** is configured as the main web server and listens on **HTTP port 80**.
2. Each ESP32 sends Wi-Fi scan data periodically using the **HTTP POST** method.
3. An **API key** is used for authentication to ensure secure communication between the ESP32 devices and the MySQL backend.

   * The same API key is stored in both the **Arduino IDE script** and the **PHP scripts** (`data.php` and `submit.php`).
4. Each ESP32 device is assigned a **unique Device ID**:

   ```
   eines1, zwei2, drei3, vier4, funf5
   ```


## Web Interface and Visualization

The main web interface is accessible via **port 80** through `index.php`.
This page is built with **HTML, CSS, and JavaScript**, and provides:

* Real-time information on the location with the best Wi-Fi signal
* Graphical data visualization
* Heatmap display for Wi-Fi coverage

The visualization system consists of:

* **`data.php`** – acts as an API endpoint to fetch data from MySQL
* **`index.php`** – integrates the PHP data endpoint and renders the user interface
* **Chart.js** – used to display line charts of signal strength
* **Flask (Python)** – used to generate heatmaps from MySQL data

The heatmap generation system uses two Python scripts:

* `heatmap_generator.py` – retrieves Wi-Fi data from MySQL and processes it into a heatmap
* `app.py` – executes and serves the generated heatmap to the web interface


### Summary of System Flow

```
ESP32 → HTTP POST → Nginx (PHP: submit.php) → MySQL
↓
Frontend (index.php / home.php) → PHP (data.php) → Chart.js visualization
↓
Python Flask (app.py + heatmap_generator.py) → Heatmap generation
```
