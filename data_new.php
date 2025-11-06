<?php

require 'connection.php';

$sql = "
SELECT location, strength, channel, channelLoad, interference, time
FROM (
    SELECT * FROM (
        SELECT location, strength, channel, channelLoad, interference, time FROM data_esp32_1 ORDER BY time DESC LIMIT 1
    ) AS t1
    UNION ALL
    SELECT * FROM (
        SELECT location, strength, channel, channelLoad, interference, time FROM data_esp32_2 ORDER BY time DESC LIMIT 1
    ) AS t2
    UNION ALL
    SELECT * FROM (
        SELECT location, strength, channel, channelLoad, interference, time FROM data_esp32_3 ORDER BY time DESC LIMIT 1
    ) AS t3
    UNION ALL
    SELECT * FROM (
        SELECT location, strength, channel, channelLoad, interference, time FROM data_esp32_4 ORDER BY time DESC LIMIT 1
    ) AS t4
    UNION ALL
    SELECT * FROM (
        SELECT location, strength, channel, channelLoad, interference, time FROM data_esp32_5 ORDER BY time DESC LIMIT 1
    ) AS t5
) AS all_data
ORDER BY strength DESC
LIMIT 1
";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $location = $row["location"];
    $strength = $row["strength"];
    $channel = $row["channel"];
    $channelLoad = $row["channelLoad"];
    $interference = $row["interference"];
    $last_update_time = $row["time"];

        if ($strength >= -60) {
                $note = "Sangat Kuat";
        } elseif ($strength >= -65 && $strength < -60) {
                $note = "Kuat";
        } elseif ($strength >= -70 && $strength < -65) {
                $note = "Cukup Baik";
        } elseif ($strength >= -75 && $strength < -70) {
                $note = "Cukup";
        } elseif ($strength >= -80 && $strength < -75) {
                $note = "Lemah";
        } elseif ($strength >= -85 && $strength < -80) {
                $note = "Buruk";
        } elseif ($strength >= -90 && $strength < -85) {
                $note = "Nyaris Hilang";
        } elseif ($strength <= -100) {
                $note = "Tidak Ada Sinyal";
        } else {
                $note = "Di luar jangkauan";
        }
}

$timestamps = [];
$sinyal_values = [];

if ($location !== "Tidak tersedia") {
    $history_sql = "
        SELECT time, strength
        FROM connection_data
        WHERE location = '$location'
        ORDER BY time ASC
    ";

    $history_result = $conn->query($history_sql);

    if ($history_result) {
        while ($data = $history_result->fetch_assoc()) {
            $timestamps[] = $data['time'];
            $sinyal_values[] = $data['strength'];
        }
    }
}

// Line Chart
$dataByLocation = [];

$locations = [
    'Titik 1' => 'data_esp32_1',
    'Titik 2' => 'data_esp32_2',
    'Titik 3' => 'data_esp32_3',
    'Titik 4' => 'data_esp32_4',
    'Titik 5' => 'data_esp32_5'
];

foreach ($locations as $label => $table) {
    $query = "SELECT mac_address, strength, channel, time FROM $table WHERE time >= NOW() - INTERVAL 2 HOUR ORDER BY time DESC";
    $result = $conn->query($query);
    $dataByLocation[$label] = [];
    while ($row = $result->fetch_assoc()) {
        $mac = $row['mac_address'];
        $strength = (int)$row['strength'];
        $time = $row['time'];
        if (!isset($dataByLocation[$label][$mac])) {
            $dataByLocation[$label][$mac] = [
                'timelabels' => [],
                'strengths' => [],
            ];
        }
        $dataByLocation[$label][$mac]['timelabels'][] = $time;
        $dataByLocation[$label][$mac]['strengths'][] = $strength;
    }
}
?>
<script>
    const dataByLocation = <?php echo json_encode($dataByLocation); ?>;
</script>

$data1 = fetch_sinyal_data($conn, 'data_esp32_1');
$data2 = fetch_sinyal_data($conn, 'data_esp32_2');
$data3 = fetch_sinyal_data($conn, 'data_esp32_3');
$data4 = fetch_sinyal_data($conn, 'data_esp32_4');
$data5 = fetch_sinyal_data($conn, 'data_esp32_5');

$conn->close();
?>