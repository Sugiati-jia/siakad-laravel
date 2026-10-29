# ERD dan CDM SIAKAD SMK Sunan Giri

Anda dapat menyalin kode Mermaid di bawah ini dan mem-paste-nya ke dalam aplikasi **Draw.io** (diagrams.net).
**Cara Import di Draw.io:**
1. Buka Draw.io (https://app.diagrams.net/).
2. Pilih menu **Arrange** -> **Insert** -> **Advanced** -> **Mermaid...** (atau **Tools** -> **Mermaid**).
3. Paste kode di bawah ini lalu klik **Insert**.

## 1. Conceptual Data Model (CDM)
Model konseptual berfokus pada entitas utama dan relasinya secara garis besar, tanpa menyertakan tipe data atau technical details.

```mermaid
erDiagram
    SEMESTER ||--o{ JADWAL_PELAJARAN : memiliki
    SEMESTER ||--o{ RAPOR : mencatat
    SEMESTER ||--o{ NILAI_TP : memiliki
    TAHUN_AJARAN ||--o{ SEMESTER : memiliki
    USER ||--o| GURU : merupakan
    USER ||--o| SISWA : merupakan
    KELAS ||--o{ SISWA : berisi
    KELAS ||--o| WALI_KELAS : memiliki
    GURU ||--o{ GURU_MAPEL : mengajar
    GURU ||--o{ WALI_KELAS : menjadi
    MAPEL ||--o{ GURU_MAPEL : diajarkan
    KELAS ||--o{ GURU_MAPEL : menerima
    GURU_MAPEL ||--o{ JADWAL_PELAJARAN : memiliki
    GURU_MAPEL ||--o{ TUJUAN_PEMBELAJARAN : memiliki
    GURU_MAPEL ||--o{ NILAI_TP : memiliki
    SISWA ||--o{ NILAI_TP : memperoleh
    SISWA ||--o{ RAPOR : mendapatkan
    KELAS ||--o{ JADWAL_PELAJARAN : memiliki
    KELAS ||--o{ CATATAN_WALI_KELAS : memiliki
    SISWA ||--o{ CATATAN_WALI_KELAS : menerima
```

## 2. Entity Relationship Diagram (ERD) / Physical Data Model
Model fisik/relasional ini menyertakan struktur tabel lengkap, foreign keys (termasuk relasi `semester_id` yang baru direvisi), dan tipe data utama.

```mermaid
erDiagram
    users {
        bigint id PK
        string name
        string username
        string email
        string password
        enum role "admin, superadmin, guru, wali_kelas, siswa, orang_tua"
    }

    guru {
        bigint id_guru PK
        bigint user_id FK
        string nama
        string nip
        string jenis_kelamin
    }

    siswa {
        bigint id_siswa PK
        bigint user_id FK
        bigint id_kelas FK
        string nama_siswa
        string nisn
        string jenis_kelamin
    }

    kelas {
        bigint id_kelas PK
        bigint id_wali_kelas FK
        string tingkat
        string nama_kelas
        boolean is_finalized
    }

    wali_kelas {
        bigint id_wali_kelas PK
        bigint id_guru FK
        bigint id_kelas FK
        bigint tahun_ajaran_id FK
    }

    mapel {
        bigint id_mapel PK
        string kode_mapel
        string nama_mapel
        string kelompok
    }

    tahun_ajaran {
        bigint id_tahun_ajaran PK
        string tahun_ajaran
        boolean status
    }

    semester {
        bigint id_semester PK
        bigint id_tahun_ajaran FK
        string semester "1 (Ganjil) / 2 (Genap)"
        boolean status
    }

    guru_mapel {
        bigint id_guru_mapel PK
        bigint id_guru FK
        bigint id_mapel FK
        bigint id_kelas FK
        bigint tahun_ajaran_id FK
        int semester
    }

    jadwal_pelajaran {
        bigint id_jadwal PK
        bigint id_guru_mapel FK
        bigint id_kelas FK
        bigint semester_id FK
        string hari
        time jam_mulai
        time jam_selesai
    }

    tujuan_pembelajaran {
        bigint id_tujuan_pembelajaran PK
        bigint id_mapel FK
        string kode_tp
        string deskripsi
        string tingkat
        int semester
    }

    nilai_tp {
        bigint id_nilai_tp PK
        bigint id_siswa FK
        bigint id_guru_mapel FK
        bigint semester_id FK
        float nilai_tp_1
        float nilai_tp_2
        float rata_rata_tp
        float nilai_as
        float nilai_akhir
        enum status_nilai "draft, submitted, locked"
    }

    rapor {
        bigint id_rapor PK
        bigint id_siswa FK
        bigint id_kelas FK
        bigint tahun_ajaran_id FK
        bigint semester_id FK
        int jumlah_sakit
        int jumlah_izin
        int jumlah_alpa
        string catatan_wali_kelas
        boolean is_locked
    }

    catatan_wali_kelas {
        bigint id PK
        bigint id_siswa FK
        bigint id_kelas FK
        bigint id_wali_kelas FK
        string catatan
        enum status_kenaikan
    }

    users ||--o| guru : "1:1 user_id"
    users ||--o| siswa : "1:1 user_id"
    guru ||--o{ wali_kelas : "1:N"
    guru ||--o{ guru_mapel : "1:N"
    kelas ||--o{ siswa : "1:N id_kelas"
    kelas ||--o| wali_kelas : "1:1 id_kelas"
    kelas ||--o{ guru_mapel : "1:N id_kelas"
    mapel ||--o{ guru_mapel : "1:N id_mapel"
    tahun_ajaran ||--o{ semester : "1:N id_tahun_ajaran"
    tahun_ajaran ||--o{ guru_mapel : "1:N tahun_ajaran_id"
    
    semester ||--o{ jadwal_pelajaran : "1:N semester_id"
    semester ||--o{ rapor : "1:N semester_id"
    semester ||--o{ nilai_tp : "1:N semester_id"

    guru_mapel ||--o{ jadwal_pelajaran : "1:N id_guru_mapel"
    guru_mapel ||--o{ nilai_tp : "1:N id_guru_mapel"
    siswa ||--o{ nilai_tp : "1:N id_siswa"
    siswa ||--o{ rapor : "1:N id_siswa"
    siswa ||--o{ catatan_wali_kelas : "1:N id_siswa"
    kelas ||--o{ rapor : "1:N id_kelas"
```
