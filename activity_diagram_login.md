# Activity Diagram — Login Admin (3 Role)

Dokumentasi **Activity Diagram** alur login untuk aplikasi **WebsiteAdmin**, mencakup **Super Admin**, **Admin Pengaduan**, dan **Admin Artikel** berdasarkan implementasi di `AuthController` dan redirect per role.

---

## 1. Diagram Activity Login (Mermaid)

Salin blok kode di bawah ini ke [mermaid.ai](https://mermaid.ai) atau editor yang mendukung Mermaid:

```mermaid
flowchart TD
    Start([Mulai]) --> A[Buka halaman /admin/login]
    A --> B{Sudah ada session admin?}
    B -->|Ya| C[Redirect ke /admin/dashboard]
    B -->|Tidak| D[Tampilkan form login]
    D --> E[Admin input email & password]
    E --> F[Klik tombol Login]
    F --> G{Validasi form<br/>email wajib & format valid<br/>password wajib min. 6 karakter}
    G -->|Tidak valid| H[Tampilkan pesan validasi]
    H --> D
    G -->|Valid| I[Autentikasi Firebase<br/>signInWithEmailAndPassword]
    I --> J{Autentikasi berhasil?}
    J -->|Tidak| K[Tampilkan error:<br/>Login gagal. Periksa email dan password.]
    K --> D
    J -->|Ya| L[Ambil UID dari Firebase Auth]
    L --> M{Cek dokumen admin<br/>di Firestore collection admins}
    M -->|Tidak ditemukan| N[Tampilkan error:<br/>Akun ini tidak memiliki izin sebagai admin.]
    N --> D
    M -->|Ditemukan| O[Simpan session admin<br/>uid, email, role]
    O --> P[Redirect ke /admin/dashboard]
    P --> Q{Role admin?}

    Q -->|super_admin| R1[Masuk Dashboard Overview<br/>akses penuh modul]
    Q -->|admin_pengaduan| R2[Masuk Dashboard Overview<br/>Dashboard + Pengaduan]
    Q -->|admin_artikel| R3[Redirect otomatis ke<br/>/admin/articel]

    R1 --> S1([Selesai — Super Admin<br/>Menu: Dashboard, Artikel,<br/>Pengaduan, Pengaturan Admin, Profil])
    R2 --> S2([Selesai — Admin Pengaduan<br/>Menu: Dashboard, Pengaduan, Profil])
    R3 --> S3([Selesai — Admin Artikel<br/>Menu: Artikel, Profil])

    C --> Q

    style Start fill:#e0f2fe
    style S1 fill:#dcfce7
    style S2 fill:#dcfce7
    style S3 fill:#dcfce7
    style K fill:#fee2e2
    style N fill:#fee2e2
    style H fill:#fef3c7
```

---

## 2. Diagram Activity per Role (Swimlane)

Diagram terpisah yang menekankan **halaman tujuan** setelah login berhasil:

```mermaid
flowchart LR
    subgraph Sistem["Sistem WebsiteAdmin"]
        L[Login berhasil] --> D{Deteksi role dari Firestore}
    end

    subgraph SuperAdmin["👑 Super Admin"]
        D -->|super_admin| SA1[Dashboard]
        SA1 --> SA2[Kelola semua modul]
        SA2 --> SA3[Pengaturan Admin]
    end

    subgraph AdminPengaduan["👮 Admin Pengaduan"]
        D -->|admin_pengaduan| AP1[Dashboard]
        AP1 --> AP2[Daftar & chat pengaduan]
        AP2 --> AP3[Update status laporan]
    end

    subgraph AdminArtikel["✍️ Admin Artikel"]
        D -->|admin_artikel| AA1[Halaman Artikel]
        AA1 --> AA2[CRUD artikel edukasi]
    end
```

---

## 3. Tabel Langkah Proses Login

| No | Aktivitas | Aktuator | Keterangan |
|----|-----------|----------|------------|
| 1 | Mengakses halaman login | Admin | URL: `/admin/login` |
| 2 | Mengisi email dan password | Admin | Form POST ke `/admin/login` |
| 3 | Validasi input | Sistem | Laravel validation |
| 4 | Autentikasi kredensial | Sistem | Firebase Authentication |
| 5 | Verifikasi izin admin | Sistem | Cek dokumen di Firestore `admins/{uid}` |
| 6 | Menyimpan session | Sistem | `Session::put('admin', [...])` |
| 7 | Mengarahkan halaman awal | Sistem | Berdasarkan role |

---

## 4. Halaman Tujuan Setelah Login

| Role | Nilai `role` di Firestore | Halaman pertama | Menu sidebar yang tersedia |
|------|---------------------------|-----------------|----------------------------|
| **Super Admin** | `super_admin` | `/admin/dashboard` | Dashboard, Artikel, Pengaduan, Pengaturan Admin, Profil |
| **Admin Pengaduan** | `admin_pengaduan` | `/admin/dashboard` | Dashboard, Pengaduan, Profil |
| **Admin Artikel** | `admin_artikel` | `/admin/articel` *(redirect dari dashboard)* | Artikel, Profil |

---

## 5. Cabang Error (Decision Node)

| Kondisi | Pesan / Hasil |
|---------|----------------|
| Email kosong / format tidak valid | Validasi Laravel di form login |
| Password kosong / kurang dari 6 karakter | Validasi Laravel di form login |
| Email/password salah di Firebase | *"Login gagal. Periksa email dan password."* |
| Login Firebase berhasil, tetapi UID tidak ada di collection `admins` | *"Akun ini tidak memiliki izin sebagai admin."* |
| Session sudah aktif saat buka `/admin/login` | Redirect langsung ke dashboard (kemudian dicek role) |

---

## 6. Referensi Kode

| Komponen | File |
|----------|------|
| Form & halaman login | `resources/views/admin/login.blade.php` |
| Proses login & session | `app/Http/Controllers/authcontroller.php` → `login()` |
| Redirect Admin Artikel | `app/Http/Controllers/dashboardcontroller.php` → `index()` |
| Menu berdasarkan role | `resources/views/admin/partials/sidebar.blade.php` |
| Route login | `routes/web.php` |

---

## 7. Catatan UML

- **Initial node**: titik mulai admin membuka halaman login.
- **Activity**: kotak proses (validasi, autentikasi, simpan session).
- **Decision node**: belah berlian `{...}` untuk percabangan ya/tidak.
- **Final node**: titik akhir admin berada di halaman sesuai role.
- Ketiga role **menggunakan alur login yang sama**; perbedaan hanya pada **cabang setelah session tersimpan** (deteksi `role`).
