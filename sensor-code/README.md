# Sensor (ESP32) Documentation

## 📌 Overview

Kode ini berjalan pada ESP32 untuk:

* Melakukan scanning jaringan WiFi di sekitar
* Mengukur kekuatan sinyal (RSSI)
* Menghitung estimasi jarak ke Access Point
* Menghitung beban channel dan interferensi
* Mengirim data ke backend server melalui HTTP POST


## ⚙️ Teknologi

* ESP32 (Arduino Framework)
* WiFi.h
* HTTPClient.h


## 🔧 Konfigurasi

Ubah parameter berikut sebelum upload ke ESP32:

```cpp id="d1h7ks"
const char* ssid = "YOUR_WIFI_NAME";
const char* password = "YOUR_WIFI_PASSWORD";

const char* serverName = "http://YOUR_IP/backend/submit.php";

String apiKeyValue = "YOUR_API_KEY";
String deviceID = "eines1";   // unik tiap device
String location = "Titik 1";  // lokasi sensor
```


## 📡 Data yang Dikirim ke Server

ESP32 mengirimkan data berikut:

* `api_key`
* `device_id`
* `location`
* `strength` (RSSI, dBm)
* `distance` (meter, estimasi)
* `channel`
* `channelLoad` (jumlah AP di channel)
* `interference` (AP di channel sekitar)
* `mac_address` (BSSID AP)


## 📐 Perhitungan Jarak (Estimasi)

Jarak dihitung menggunakan model path loss:

d = d_1 \cdot 10^{\frac{P_t - P_r - L_f}{10n}}

Keterangan:

* `Pt` = daya transmit (dBm)
* `Pr` = RSSI (dBm)
* `Lf` = loss factor
* `n` = path-loss exponent
* `d1` = jarak referensi


## 📊 Perhitungan Interferensi

Interferensi dihitung dengan:

* Mengambil semua channel WiFi yang terdeteksi
* Menghitung jumlah AP dengan selisih channel ≤ 2 dari channel utama


## 🔄 Alur Program

1. ESP32 connect ke WiFi
2. Scan semua jaringan WiFi
3. Cari SSID yang sesuai
4. Ambil data:

   * RSSI
   * Channel
   * BSSID
5. Hitung:

   * Distance
   * Channel load
   * Interference
6. Kirim ke backend (`submit.php`)
7. Delay 30 detik
8. Restart ESP32


## ⏱️ Interval Pengiriman

```cpp id="z7k2lm"
delay(30000); // 30 detik
ESP.restart();
```


## 🧪 Output Serial Monitor

Contoh output:

```
SSID         : MyWiFi
BSSID        : xx:xx:xx:xx
RSSI         : -65 dBm
Distance     : 3.21 m
Channel      : 6
Beban Channel: 4
Interference : 2 AP
```


## ⚠️ Catatan Penting

* ESP32 akan restart setiap 30 detik
* Pastikan server dapat diakses dari jaringan yang sama
* API Key harus sesuai dengan backend
* RSSI bisa fluktuatif (normal pada WiFi)
* RSSI ≠ jarak absolut → hanya estimasi
* Channel overlap (2.4 GHz) menyebabkan interferensi tinggi
* Nilai `n_factor` sangat mempengaruhi akurasi jarak


## 📡 Multi-Device Setup

Gunakan konfigurasi berbeda untuk tiap ESP32:

```cpp id="k9w2dp"
// Device 1
deviceID = "eines1";
location = "Titik 1";

// Device 2
deviceID = "zwei2";
location = "Titik 2";
```
