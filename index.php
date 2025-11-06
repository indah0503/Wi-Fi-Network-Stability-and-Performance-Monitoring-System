<?php include 'data_new.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InterTA</title>
    <link rel="stylesheet" href="assets/css/style_2.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link href='https://cdn.jsdelivr.net/npm/boxicons@2.0.5/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <!-- ===== HEADER =====-->
    <header class="l-header">
        <nav class="nav bd-grid">
            <a href="#" class="nav__logo">UGM</a>
            <div class="nav__toggle" id="nav-toggle"><i class='bx bx-menu-alt-right'></i></div>
        </nav>
    </header>

    <main>
        <!-- ===== HOME =====-->
        <div class="home" style="background: url('http://10.33.102.185/assets/img/bg1.jpg') no-repeat center; background-size: cover; height: 100vh;">
            <div class="home__left">
                <h1 class="home__title">Inter-TA</h1>
                <span class="home__subtitle">Web Monitor Kekuatan Internet</span>
            </div>

            <div class="section__data" style="flex: 1; background-color: rgba(255, 255, 255, 0.8); padding: 1rem; border-radius: 10px;">
                <h2 class="section__title"><strong>Koneksi Tertinggi Saat Ini</strong></h2>
                <p><span class="data-label">Lokasi</span>: <?php echo $location; ?></p>
                <p><span class="data-label">Kekuatan Sinyal</span>: <?php echo $strength; ?> dBm</p>
                <p><span class="data-label">Lokasi Channel</span>: <?php echo $channel; ?></p>
                <p><span class="data-label">Beban per Channel</span>: <?php echo $channelLoad; ?> Access Point (AP)</p>
                <p><span class="data-label">Interferensi</span>: <?php echo $interference; ?> AP dari channel lain</p>
                <p><span class="data-label">Keterangan</span>: <?php echo $note; ?></p>
                <p><span class="data-label">Update Terakhir</span>: <?php echo $last_update_time; ?></p>

                <div class="home__button" style="display: flex; gap: 1rem; align-items: center;">
                    <button type="button" onclick="document.getElementById('section').scrollIntoView({ behavior: 'smooth' })">Lihat Fluktuasi</button>
                    <button onclick="window.location.href='http://10.33.102.185/heatmap'">Heatmap</button>
                </div>
            </div>
        </div>

        <!-- ===== SECTION =====-->
        <section class="l-section" id="section">
            <div class="section">
                <h2 class="line_chart__title"><strong>Fluktuasi Kekuatan Sinyal</strong></h2>
                <div class="chartContainer">
                    <div class="dropdownContainer">
                        <label for="lokasiSelect"><strong>Pilih Lokasi ESP32:</strong></label>
                        <select id="lokasiSelect">
                            <option value="Titik 1">Titik 1</option>
                            <option value="Titik 2">Titik 2</option>
                            <option value="Titik 3">Titik 3</option>
                            <option value="Titik 4">Titik 4</option>
                            <option value="Titik 5">Titik 5</option>
                        </select>
                        <button id="okButton">OK</button>
                    </div>

                    <div class="chartArea">
                        <canvas id="sinyalChart" width="700" height="400"></canvas>
                        <script>
                            const dataByLocation = <?php echo json_encode($dataByLocation); ?>;
                            const ctx = document.getElementById('sinyalChart').getContext('2d');
                            let sinyalChart;

                            function updateChart(location) {
                                const macGroups = dataByLocation[location];
                                const currentTime = new Date();
                                const datasets = [];
                                let sharedLabels = [];

                                const macToLabel = {};
                                let apCounter = 1;

                                Object.entries(macGroups).forEach(([mac, macData]) => {

                                    if (!(mac in macToLabel)) {
                                        macToLabel[mac] = `AP${apCounter++}`;
                                    }

                                    const filteredData = [];
                                    const filteredLabels = [];

                                    for (let i = 0; i < macData.timelabels.length; i++) {
                                        const timestamp = new Date(macData.timelabels[i]);
                                        // const diffMinutes = (currentTime - timestamp) / (1000 * 60);
                                        // if (diffMinutes <= 10) {
                                        filteredLabels.push(`${timestamp.getHours()}:${String(timestamp.getMinutes()).padStart(2, '0')}`);
                                        filteredData.push(macData.strengths[i]);
                                        // }
                                    }

                                    datasets.push({
                                        label: macToLabel[mac],
                                        data: filteredData,
                                        borderColor: getRandomColor(),
                                        fill: false,
                                        tension: 0.3
                                    });

                                    if (filteredLabels.length > sharedLabels.length) {
                                        sharedLabels = filteredLabels;
                                    }
                                });

                                if (sinyalChart) sinyalChart.destroy();

                                sinyalChart = new Chart(ctx, {
                                    type: 'line',
                                    data: {
                                        labels: sharedLabels,
                                        datasets: datasets
                                    },
                                    options: {
                                        responsive: true,
                                        scales: {
                                            y: {
                                                title: {
                                                    display: true,
                                                    text: 'dBm'
                                                },
                                                grid: {
                                                    display: false // Menghilangkan grid Y
                                                }
                                            },
                                            x: {
                                                title: {
                                                    display: true,
                                                    text: 'Waktu (HH:mm)'
                                                },
                                                grid: {
                                                    display: false // Menghilangkan grid Y
                                                }
                                            }
                                        }
                                    }
                                });
                            }

                            function getRandomColor() {
                                const r = Math.floor(Math.random() * 200);
                                const g = Math.floor(Math.random() * 200);
                                const b = Math.floor(Math.random() * 200);
                                return `rgb(${r}, ${g}, ${b})`;
                            }

                            document.getElementById('okButton').addEventListener('click', () => {
                                const selected = document.getElementById('lokasiSelect').value;
                                updateChart(selected);
                            });
                        </script>
                    </div>

                    <div class="chartFooter">
                        <div class="note">
                            <strong>Catatan Fluktuasi:</strong><br>
                            🔺: Sinyal meningkat > 2 dBm<br>
                            🔻: Sinyal menurun > 2 dBm<br>
                            ➖: Sinyal relatif stabil (±2 dBm)<br>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <footer class="footer">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>About Me</h3>
                    <p>Hai! Saya Indah Sekar...</p>
                    <p>Mahasiswa tahun terakhir di jurusan Teknologi Rekayasa Internet Sekolah Vokasi Universitas Gadjah Mada yang sedang menjalankan Tugas Akhir</p>
                    <p>Inilah Tugas Akhir saya.</p>
                </div>

                <div class="quick-links">
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a href="#">Home</a></li>
                        <li><a href="#">Discover</a></li>
                        <li><a href="https://internet.ugm.ac.id">Hotspot</a></li>
                        <li><a href="https://www.linkedin.com/in/indah-sekar-ningrum/">Contact</a></li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; 2025 Proyek Akhir D4 Teknologi Rekayasa Internet UGM. All rights reserved.</p>
            </div>
        </footer>
    </main>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.2.6/gsap.min.js"></script>
    <script src="https://unpkg.com/scrollreveal"></script>
    <script src="assets/js/main_2.js"></script>
</body>
</html>