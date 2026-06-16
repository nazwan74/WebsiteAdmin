# Use Case Diagram — WebsiteAdmin System

Dokumentasi **Use Case Diagram** aplikasi **WebsiteAdmin** (admin web GESA — pengaduan & edukasi), berdasarkan implementasi aktual di codebase.

---

## Salin Langsung ke Mermaid AI

> **Cara pakai:** Blok di bawah **tanpa** baris pembuka/penutup ` ```mermaid ` — cukup salin dari `flowchart TB` sampai baris terakhir diagram, lalu tempel di [mermaid.ai](https://mermaid.ai).

```
flowchart TB
    %% ===== AKTOR =====
    Admin((Admin Web))
    SuperAdmin((Super Admin))
    AdminPengaduan((Admin Pengaduan))
    AdminArtikel((Admin Artikel))

    SuperAdmin --|> Admin
    AdminPengaduan --|> Admin
    AdminArtikel --|> Admin

    %% ===== BATAS SISTEM =====
    subgraph SYS["Sistem Website Admin Layanan Pengaduan dan Edukasi GESA"]
        direction TB

        subgraph UC_AUTH["Autentikasi"]
            UC01([Melakukan Login])
            UC02([Melakukan Logout])
            UC03([Melakukan Lupa Password])
            UC04([Melakukan Reset Password])
        end

        subgraph UC_DASH["Dashboard Analitik"]
            UC05([Memantau Dashboard dan Statistik])
            UC06([Memfilter Data Analitik Global])
            UC07([Memperbarui Data Dashboard])
            UC08([Mengatur Setelan Dashboard])
        end

        subgraph UC_LAP["Modul Pengaduan"]
            UC09([Melihat Daftar Laporan])
            UC10([Memfilter dan Mencari Laporan])
            UC11([Melihat Detail Laporan])
            UC12([Mengubah Status Laporan])
            UC13([Menghapus Laporan])
            UC14([Mengunduh PDF Laporan])
            UC15([Mengekspor CSV Laporan])
            UC16([Melakukan Chat Pengaduan])
            UC17([Mengedit Pesan Chat])
            UC18([Menghapus Pesan Chat])
            UC19([Melihat Notifikasi Chat Belum Dibaca])
            UC20([Memantau Chat Read-Only])
        end

        subgraph UC_ART["Modul Artikel"]
            UC21([Mengelola Artikel Edukasi])
            UC22([Memantau Artikel Read-Only])
        end

        subgraph UC_SYS["Sistem dan Profil"]
            UC23([Mengelola Akun Admin])
            UC24([Mengubah Password Profil])
        end
    end

    %% ===== SEMUA ADMIN =====
    Admin --> UC01
    Admin --> UC02
    Admin --> UC03
    Admin --> UC04
    Admin --> UC24

    %% ===== SUPER ADMIN =====
    SuperAdmin --> UC05
    SuperAdmin --> UC06
    SuperAdmin --> UC07
    SuperAdmin --> UC08
    SuperAdmin --> UC09
    SuperAdmin --> UC10
    SuperAdmin --> UC11
    SuperAdmin --> UC14
    SuperAdmin --> UC15
    SuperAdmin --> UC19
    SuperAdmin --> UC20
    SuperAdmin --> UC22
    SuperAdmin --> UC23

    %% ===== ADMIN PENGADUAN =====
    AdminPengaduan --> UC05
    AdminPengaduan --> UC06
    AdminPengaduan --> UC07
    AdminPengaduan --> UC09
    AdminPengaduan --> UC10
    AdminPengaduan --> UC11
    AdminPengaduan --> UC12
    AdminPengaduan --> UC13
    AdminPengaduan --> UC14
    AdminPengaduan --> UC15
    AdminPengaduan --> UC16
    AdminPengaduan --> UC17
    AdminPengaduan --> UC18
    AdminPengaduan --> UC19

    %% ===== ADMIN ARTIKEL =====
    AdminArtikel --> UC21

    %% ===== INCLUDE =====
    UC12 -.->|include| UC11
    UC14 -.->|include| UC11
    UC16 -.->|include| UC11
    UC20 -.->|include| UC11
    UC03 -.->|include| UC04

    %% ===== EXTEND =====
    UC17 -.->|extend| UC16
    UC18 -.->|extend| UC16
```

---

## Versi Ringkas (1 diagram, tanpa subgrup)

Salin blok ini jika diagram utama terlalu padat di Mermaid AI:

```
flowchart LR
    Admin((Admin))
    SuperAdmin((Super Admin))
    AdminPengaduan((Admin Pengaduan))
    AdminArtikel((Admin Artikel))

    SuperAdmin --|> Admin
    AdminPengaduan --|> Admin
    AdminArtikel --|> Admin

    subgraph Sistem["Website Admin GESA"]
        direction TB
        UC1([Login])
        UC2([Logout])
        UC3([Lupa dan Reset Password])
        UC4([Dashboard dan Filter Analitik])
        UC5([Daftar dan Detail Laporan])
        UC6([Ubah Status Laporan])
        UC7([Hapus Laporan])
        UC8([Chat Pengaduan])
        UC9([Export PDF dan CSV])
        UC10([Kelola Artikel])
        UC11([Kelola Akun Admin])
        UC12([Ubah Password Profil])
        UC13([Pantau Read-Only])
    end

    Admin --> UC1
    Admin --> UC2
    Admin --> UC3
    Admin --> UC12

    SuperAdmin --> UC4
    SuperAdmin --> UC5
    SuperAdmin --> UC9
    SuperAdmin --> UC10
    SuperAdmin --> UC11
    SuperAdmin --> UC13

    AdminPengaduan --> UC4
    AdminPengaduan --> UC5
    AdminPengaduan --> UC6
    AdminPengaduan --> UC7
    AdminPengaduan --> UC8
    AdminPengaduan --> UC9

    AdminArtikel --> UC10

    UC6 -.->|include| UC5
    UC8 -.->|include| UC5
    UC13 -.->|include| UC5
```

---

## Tabel Aktor

| Aktor | Role (`admins.role`) | Deskripsi |
|-------|----------------------|-----------|
| **Admin (Web)** | — | Aktor generalisasi; hak dasar autentikasi dan profil |
| **Super Admin** | `super_admin` | Akses penuh pantau + kelola akun admin + setelan dashboard; **read-only** pada chat, status, dan CRUD artikel |
| **Admin Pengaduan** | `admin_pengaduan` | Kelola pengaduan penuh: status, chat, hapus laporan, export; dashboard + filter |
| **Admin Artikel** | `admin_artikel` | CRUD artikel edukasi; **tidak** mengakses dashboard & modul pengaduan |

---

## Tabel Use Case

| ID | Use Case | Aktor | Keterangan |
|----|----------|-------|------------|
| UC01 | Melakukan Login | Semua admin | Firebase Auth + validasi dokumen `admins` |
| UC02 | Melakukan Logout | Semua admin | Hapus session, redirect ke login |
| UC03 | Melakukan Lupa Password | Semua admin | Kirim link reset via email |
| UC04 | Melakukan Reset Password | Semua admin | Set password baru dari link reset |
| UC05 | Memantau Dashboard dan Statistik | Super Admin, Admin Pengaduan | KPI, grafik tren, usia, tipe kasus, status per daerah |
| UC06 | Memfilter Data Analitik Global | Super Admin, Admin Pengaduan | Filter tanggal, daerah, tipe, status, usia |
| UC07 | Memperbarui Data Dashboard | Super Admin, Admin Pengaduan | Tombol refresh cache dashboard |
| UC08 | Mengatur Setelan Dashboard | Super Admin | Layout & preferensi chart (API tersedia) |
| UC09 | Melihat Daftar Laporan | Super Admin, Admin Pengaduan | Halaman `/admin/laporan` |
| UC10 | Memfilter dan Mencari Laporan | Super Admin, Admin Pengaduan | Filter kategori, status, daerah, tanggal, pencarian |
| UC11 | Melihat Detail Laporan | Super Admin, Admin Pengaduan | Modal/detail partial laporan |
| UC12 | Mengubah Status Laporan | Admin Pengaduan | `baru`, `diproses`, `selesai`, `ditolak` |
| UC13 | Menghapus Laporan | Admin Pengaduan | Hapus dokumen + subcollection chat |
| UC14 | Mengunduh PDF Laporan | Super Admin, Admin Pengaduan | Export per laporan |
| UC15 | Mengekspor CSV Laporan | Super Admin, Admin Pengaduan | Export daftar terfilter |
| UC16 | Melakukan Chat Pengaduan | Admin Pengaduan | Kirim teks/gambar; ditutup jika selesai/ditolak |
| UC17 | Mengedit Pesan Chat | Admin Pengaduan | **Extend** UC16; tidak saat chat ditutup |
| UC18 | Menghapus Pesan Chat | Admin Pengaduan | **Extend** UC16; tidak saat chat ditutup |
| UC19 | Melihat Notifikasi Chat Belum Dibaca | Super Admin, Admin Pengaduan | Badge/navbar notifikasi |
| UC20 | Memantau Chat Read-Only | Super Admin | Lihat riwayat tanpa kirim/edit/hapus |
| UC21 | Mengelola Artikel Edukasi | Admin Artikel | Tambah, edit, hapus, hapus massal |
| UC22 | Memantau Artikel Read-Only | Super Admin | Lihat artikel tanpa CRUD |
| UC23 | Mengelola Akun Admin | Super Admin | Tambah/hapus admin (`super_admin`, `admin_pengaduan`, `admin_artikel`) |
| UC24 | Mengubah Password Profil | Semua admin | Halaman profil admin |

---

## Relasi Include & Extend

| Tipe | Dari | Ke | Makna |
|------|------|-----|-------|
| **include** | Mengubah Status Laporan | Melihat Detail Laporan | Status diubah dari halaman detail |
| **include** | Mengunduh PDF | Melihat Detail Laporan | PDF per ID laporan |
| **include** | Chat Pengaduan | Melihat Detail Laporan | Chat terkait satu laporan |
| **include** | Pantau Chat Read-Only | Melihat Detail Laporan | Super admin buka chat dari laporan |
| **include** | Lupa Password | Reset Password | Alur pemulihan password |
| **extend** | Mengedit Pesan Chat | Chat Pengaduan | Opsional saat chat aktif |
| **extend** | Menghapus Pesan Chat | Chat Pengaduan | Opsional saat chat aktif |

---

## Referensi Route

| Modul | Route utama |
|-------|-------------|
| Auth | `/admin/login`, `/admin/logout`, `/admin/forgot-password`, `/admin/reset-password` |
| Dashboard | `/admin/dashboard`, `/admin/dashboard/refresh` |
| Laporan | `/admin/laporan`, `/admin/laporan/{id}`, `/admin/laporan/{id}/chat` |
| Artikel | `/admin/articel` |
| Pengaturan | `/admin/pengaturan` |
| Profil | `/admin/profile` |
