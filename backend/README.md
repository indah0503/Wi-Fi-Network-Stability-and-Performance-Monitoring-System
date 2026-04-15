# Backend Documentation

## 📌 Overview

Backend ini berfungsi untuk:

* Menerima data dari beberapa perangkat ESP32
* Menyimpan data ke database MySQL
* Mengambil data sinyal terbaru dan terbaik
* Menyediakan data untuk visualisasi (chart & dashboard)


## ⚙️ Teknologi

* PHP (Native)
* MySQL
* Composer (vlucas/phpdotenv)


## 🔐 Environment Configuration

Gunakan file `.env` di folder backend:

```
API_KEY=your_secret_key
DB_HOST=localhost
DB_USER=root
DB_PASS=
DB_NAME=your_database
```


## 📂 File Structure

* `connection.php`
  Menghubungkan ke database menggunakan environment variables

* `submit.php`
  Endpoint untuk menerima data dari ESP32

* `data_new.php`
  Mengambil data terbaru dari semua device dan menentukan sinyal terbaik


## 🔌 API Endpoint

### 1. Submit Data Sensor

**URL:** `/backend/submit.php`
**Method:** POST

**Security:** API Key required

**Parameter:**

* `api_key`
* `device_id` (eines1, zwei2, drei3, vier4, funf5)
* `location`
* `strength`
* `distance`
* `channel`
* `channelLoad`
* `interference`
* `mac_address`

**Response:**

* 200 OK → berhasil
* 403 → API key salah
* 400 → data tidak valid


### 2. Get Best Signal Data

**URL:** `/backend/data_new.php`
**Method:** GET

**Deskripsi:**

* Mengambil data terbaru dari 5 device
* Membandingkan kekuatan sinyal
* Mengembalikan sinyal terbaik

**Logic Klasifikasi Sinyal:**

* ≥ -60 → Sangat Kuat
* -65 s/d -60 → Kuat
* -70 s/d -65 → Cukup Baik
* -75 s/d -70 → Cukup
* -80 s/d -75 → Lemah
* -85 s/d -80 → Buruk
* -90 s/d -85 → Nyaris Hilang
* ≤ -100 → Tidak Ada Sinyal


## 📊 Data Processing

### Multi-Device Handling

Data disimpan dalam tabel terpisah:

* `data_esp32_1`
* `data_esp32_2`
* `data_esp32_3`
* `data_esp32_4`
* `data_esp32_5`

### Visualisasi Data

Backend juga menyiapkan:

* Data historis sinyal per lokasi
* Data untuk line chart berdasarkan MAC address
* Rentang waktu: 2 jam terakhir


## 🗄️ Database Structure (Contoh)

```sql
CREATE TABLE data_esp32_1 (
    id INT AUTO_INCREMENT PRIMARY KEY,
    time DATETIME,
    device_id VARCHAR(50),
    location VARCHAR(50),
    strength INT,
    distance FLOAT,
    channel INT,
    channelLoad FLOAT,
    interference FLOAT,
    mac_address VARCHAR(50)
);
```

(Struktur sama untuk tabel lain)


## 🚀 Cara Menjalankan

1. Install dependency:

```
composer install
```

2. Buat file `.env`

3. Jalankan server (XAMPP/Laragon)

4. Akses:

* Submit data → `/backend/submit.php`
* Ambil data → `/backend/data_new.php`


## 🔧 Improvement

* Gabungkan tabel ESP32 jadi satu tabel dengan `device_id`
* Gunakan REST API format JSON (bukan embed `<script>`)
* Pisahkan logic dan view (MVC pattern)
* Tambahkan rate limiting untuk endpoint
