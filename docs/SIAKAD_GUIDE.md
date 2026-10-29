# 📘 PANDUAN UTAMA, DATA AKUN & PWA - SMK SUNAN GIRI

Selamat datang di Sistem Informasi Akademik dan E-Rapor SMK Sunan Giri yang telah dimodernisasi. Sistem ini telah disesuaikan secara 100% untuk berjalan optimal di semua ukuran layar PC dan perangkat mobile dengan standardisasi Kurikulum Merdeka.

---

## 🔑 1. DAFTAR AKUN LOGIN UJI COBA (Role Admin, Guru & Wali Kelas)

Seluruh akun di bawah ini menggunakan password default: **`password`**

### 👨‍💼 A. AKUN ADMINISTRATOR (Akses Penuh Master Data)
* **Email / Username**: `admin@smksunangiri.sch.id`
* **Hak Akses**: Mengelola data Guru, Siswa, Kelas, Mata Pelajaran, dan Penugasan Jadwal Mengajar.

### 👩&zwj;🏫 B. DAFTAR AKUN GURU & JADWAL MATA PELAJARAN
Setiap guru telah terdaftar secara otomatis di database beserta penugasan kelas yang diampunya (Jadwal Pelajaran):

| No | Nama Guru | Email Login | Mata Pelajaran & Kelas yang Diampu |
|---|---|---|---|
| 1 | **Akhmad Baikuni, S.T** (Wali Kelas X) | `akhmad.baikuni@smksunangiri.sch.id` | • DASAR OTOMOTIF (X TSM)<br>• TEKNIK SEPEDA MOTOR 1 (XI, XII TSM) |
| 2 | **Lia Innayaatul Laeli, S.Pd.** | `lia.innayaatul@smksunangiri.sch.id` | • PAI (X, XI, XII TSM)<br>• SENI BUDAYA (X TSM) |
| 3 | **Sillahturohman, S.Pd** | `sillahturohman@smksunangiri.sch.id` | • TEKNIK PENGELASAN (X, XI, XII TSM)<br>• TEKNIK SEPEDA MOTOR 3 (X, XI, XII TSM) |
| 4 | **Siti Aisah, S.Pd** (Wali Kelas XII) | `siti.aisah@smksunangiri.sch.id` | • BAHASA INGGRIS (X, XI, XII TSM) |
| 5 | **Lelyanti Nurchayani, S.Pd** (Wali Kelas XI) | `lelyanti.nurchayani@smksunangiri.sch.id` | • BAHASA INDONESIA (X, XI, XII TSM) |
| 6 | **Fuad Ibrahim, S.Pd** | `fuad.ibrahim@smksunangiri.sch.id` | • SEJARAH (X, XI TSM)<br>• ASWAJA (X, XI, XII TSM) |
| 7 | **Born Inflamone Putra, S.E** | `born.inflamone@smksunangiri.sch.id` | • PRODUK KREATIF & KEWIRAUSAHAAN (XI, XII TSM)<br>• BAHASA DAERAH (X, XI, XII TSM) |
| 8 | **Muhammad Saihu, S.Pd** | `muhammad.saihu@smksunangiri.sch.id` | • INFORMATIKA (X TSM)<br>• PENJASKES (X, XI TSM) |
| 9 | **Nur Halima, S.Pd** | `nur.halima@smksunangiri.sch.id` | • MATEMATIKA (X, XI, XII TSM) |
| 10 | **Mochammad Salim, S.T** | `mochammad.salim@smksunangiri.sch.id` | • Kelistrikan & T.ELEKTRO (XI, XII TSM)<br>• KKA (X TSM) |

---

## 📱 2. PORTAL KHUSUS & FITUR PWA ORANG TUA

Kami telah merancang portal khusus orang tua dengan fitur unduhan PWA programatik yang modern!

👉 **Link Khusus Orang Tua**: **`http://127.0.0.1:8000/orangtua/login`**

### 📥 Cara Mengunduh & Memasang Aplikasi (PWA) di HP / PC:
1. **Melalui Tombol Unduh Otomatis (Chrome / Edge di PC & HP Android)**:
   * Buka link khusus di atas: `http://127.0.0.1:8000/orangtua/login`.
   * Di atas form login, Anda akan melihat banner biru interaktif: **"📥 Pasang Aplikasi (PWA)"**.
   * Klik tombol tersebut, pilih **Install / Pasang**. Aplikasi SIAKAD langsung terpasang di HP atau layar desktop Anda dengan ikon resmi!
2. **Metode Manual (Safari iOS iPhone / Browser Lain)**:
   * Buka Safari di iPhone Anda dan akses `http://127.0.0.1:8000/orangtua/login`.
   * Klik tombol **Share** (ikon persegi dengan panah ke atas di bagian bawah layar Safari).
   * Gulir ke bawah, pilih opsi **"Add to Home Screen"** atau **"Tambahkan ke Layar Utama"**.

### 🔑 Cara Login Menggunakan NISN Siswa:
* **NISN Siswa** dimasukkan pada kolom **NISN (Username)**.
* Contoh NISN Aktif yang terdaftar di database untuk uji coba: **`0098765432`**
* **Password**: masukkan **`ortu123`** atau **`password`**
* Sistem secara cerdas akan langsung mendeteksi data siswa tersebut, memetakan wali muridnya, dan mengarahkan ke dashboard orang tua anak yang bersangkutan!

### 🔔 Notifikasi E-Rapor Otomatis:
* Sesaat setelah orang tua berhasil login, aplikasi PWA akan secara otomatis meminta izin notifikasi browser.
* Ketika diizinkan, browser akan memicu **Push Notification Native Sistem** yang berbunyi:
  > *"🔔 E-Rapor Kurikulum Merdeka Terbit! Hasil belajar capaian kompetensi anak Anda telah siap..."*
* Kami juga melengkapinya dengan notifikasi in-app bergaya iOS di dashboard agar orang tua tetap ternotifikasi meskipun push notification browser dinonaktifkan.

---

## 📊 3. EXCEL INSPECTOR & TEMPLATE PREVIEW
* **Akses Halaman**: `http://127.0.0.1:8000/temp_excel.php`
* Berguna untuk melakukan pratinjau visual terhadap data Excel rapor kelas 10 dan format nilai guru mapel sebelum diimpor ke sistem.

---

## 🎨 4. FILOSOFI DESAIN FLAT SLATE GRAY
* **No Mixed Gradients**: Menghindari perpaduan warna gradien yang ramai. Sistem beralih ke warna solid formal premium **Charcoal Slate Gray (`#1e293b`)** demi kemudahan membaca (kontras tinggi) dan ramah di mata semua PC dan HP.
* **Tampilan Minimalis (Pop-Up Detail)**: Data tabel disajikan dalam baris ringkas, lalu tombol **"Detail"** akan membuka modal pop-up halus untuk melihat informasi lengkap tanpa membebani memori browser.
