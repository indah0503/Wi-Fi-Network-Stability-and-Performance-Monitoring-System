# Frontend Documentation

## 📌 Overview

Frontend ini merupakan dashboard web untuk:

* Menampilkan data kekuatan sinyal WiFi secara real-time
* Visualisasi fluktuasi sinyal dalam bentuk line chart
* Navigasi ke heatmap visualisasi

Frontend terhubung langsung dengan backend PHP untuk mengambil data.

---

## ⚙️ Teknologi

* HTML5
* CSS3
* JavaScript (Vanilla)
* PHP (untuk integrasi data)
* Chart.js (visualisasi grafik)
* GSAP & ScrollReveal (animasi)

---

## 📂 Struktur File

* `index.php`
  Halaman utama dashboard, menampilkan:

  * Data sinyal terbaik saat ini
  * Chart fluktuasi sinyal
  * Navigasi ke heatmap

* `assets/css/style.css`
  Styling UI dashboard

* `assets/js/main.js`
  Script animasi dan interaksi UI


## 🔗 Integrasi Backend

Frontend mengambil data dari:

```id="q1y9ak"
data_new.php
```

Melalui:

```php id="y3r6kl"
<?php include 'data_new.php'; ?>
```

Data yang digunakan:

* `$location`
* `$strength`
* `$channel`
* `$channelLoad`
* `$interference`
* `$note`
* `$last_update_time`
* `$dataByLocation` (untuk chart)


## 📊 Fitur Utama

### 1. Data Koneksi Terbaik

Menampilkan:

* Lokasi dengan sinyal terbaik
* Kekuatan sinyal (dBm)
* Channel & load
* Interferensi
* Status kualitas sinyal


### 2. Line Chart (Chart.js)

Visualisasi:

* Fluktuasi sinyal berdasarkan lokasi ESP32
* Data dikelompokkan berdasarkan MAC Address (Access Point)
* Rentang waktu: hingga 2 jam terakhir

Fitur:

* Dropdown pilih lokasi (Titik 1–5)
* Multi-line chart (per AP)
* Label waktu (HH:mm)


### 3. Navigasi

* Scroll ke section chart
* Redirect ke halaman heatmap:

```id="3bq2np"
http://<IP_ADDRESS>/heatmap
```


## 🎨 UI/UX Features

* Responsive layout
* Animated navbar & content (GSAP)
* Scroll animation (ScrollReveal)
* Dynamic chart rendering
* Random color generator untuk tiap AP


## 🚀 Cara Menjalankan

1. Pastikan backend sudah berjalan
2. Letakkan project di server (XAMPP)
3. Akses:

```id="n8v2kd"
http://localhost/project-name/index.php
```
