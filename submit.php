<?php
require 'connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $api_key = $_POST['api_key'];
    $device_id = $_POST['device_id'];
    $location = $_POST['location'];
    $strength = $_POST['strength'];
    $distance = $_POST['distance'];
	$channel = $_POST['channel'];
	$channelLoad = $_POST['channelLoad'];
	$interference = $_POST['interference'];
    $mac_address = $_POST['mac_address'];

    if ($api_key !== $apikey) {
        http_response_code(403);
        exit("Unauthorized");
    }

    if (empty($device_id) || empty($location) || !is_numeric($strength) || !is_numeric($distance)) {
        http_response_code(400);
        exit("Invalid input data");
    }

	switch ($device_id) {
        case "eines1":
            $stmt = $conn->prepare("INSERT INTO data_esp32_1 (time, device_id, location, strength, distance, channel, channelLoad, interference, mac_address) VALUES (NOW(), ?, ?, ?, ?, ?, ?, ?, ?)");
            break;
        case "zwei2":
            $stmt = $conn->prepare("INSERT INTO data_esp32_2 (time, device_id, location, strength, distance, channel, channelLoad, interference, mac_address) VALUES (NOW(), ?, ?, ?, ?, ?, ?, ?, ?)");
            break;
        case "drei3":
            $stmt = $conn->prepare("INSERT INTO data_esp32_3 (time, device_id, location, strength, distance, channel, channelLoad, interference, mac_address) VALUES (NOW(), ?, ?, ?, ?, ?, ?, ?, ?)");
            break;
        case "vier4":
            $stmt = $conn->prepare("INSERT INTO data_esp32_4 (time, device_id, location, strength, distance, channel, channelLoad, interference, mac_address) VALUES (NOW(), ?, ?, ?, ?, ?, ?, ?, ?)");
            break;
        case "funf5":
            $stmt = $conn->prepare("INSERT INTO data_esp32_5 (time, device_id, location, strength, distance, channel, channelLoad, interference, mac_address) VALUES (NOW(), ?, ?, ?, ?, ?, ?, ?, ?)");
            break;
        default:
            http_response_code(400);
            exit("Unknown device_id");
    }

    if ($stmt === false) {
        http_response_code(500);
        exit("Database error: " . $conn->error);
    }

    $stmt->bind_param("ssddddds", $device_id, $location, $strength, $distance, $channel, $channelLoad, $interference, $mac_address);
    $stmt->execute();
    $stmt->close();
}
$conn->close();
?>
