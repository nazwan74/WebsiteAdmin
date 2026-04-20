# Skenario Pengujian Black Box Testing
## Website Admin — Sistem Pelaporan Perlindungan Anak

> **Metode Pengujian:** Black Box Testing (Equivalence Partitioning & Boundary Value Analysis)  
> **Tujuan:** Memastikan setiap fungsi pada website admin berjalan sesuai kebutuhan fungsional tanpa memperhatikan kode internal.

---

## Daftar Modul yang Diuji

| No | Modul | Jumlah Skenario |
|----|-------|-----------------|
| 1 | Autentikasi (Login, Logout, Lupa & Reset Password) | 14 |
| 2 | Dashboard | 5 |
| 3 | Manajemen Artikel | 12 |
| 4 | Manajemen Laporan | 14 |
| 5 | Chat Laporan | 9 |
| 6 | Pengaturan Admin | 7 |
| 7 | Profil | 5 |
| | **Total** | **66** |

---

## 1. Modul Autentikasi

### 1.1 Login

| ID | Deskripsi Pengujian | Langkah Pengujian | Data Uji | Hasil yang Diharapkan | Hasil Aktual |
|----|---------------------|-------------------|-----------|----------------------|--------------|
| AU-01 | Login berhasil dengan kredensial valid | 1. Buka halaman `/admin/login` <br> 2. Masukkan email dan password valid <br> 3. Klik tombol Login | Email: `admin@example.com` <br> Password: `password123` | Pengguna diarahkan ke halaman Dashboard (`/admin/dashboard`) | |
| AU-02 | Login gagal — email tidak terdaftar | 1. Buka halaman `/admin/login` <br> 2. Masukkan email yang tidak terdaftar <br> 3. Klik tombol Login | Email: `notexist@example.com` <br> Password: `password123` | Tampil pesan error: *"Login gagal. Periksa email dan password."* | |
| AU-03 | Login gagal — password salah | 1. Buka halaman `/admin/login` <br> 2. Masukkan email valid dan password salah <br> 3. Klik tombol Login | Email: `admin@example.com` <br> Password: `wrongpass` | Tampil pesan error: *"Login gagal. Periksa email dan password."* | |
| AU-04 | Login gagal — email bukan admin | 1. Buka halaman `/admin/login` <br> 2. Masukkan email user biasa (bukan admin) <br> 3. Klik tombol Login | Email: `user@example.com` <br> Password: `password123` | Tampil pesan error: *"Akun ini tidak memiliki izin sebagai admin."* | |
| AU-05 | Login gagal — field kosong | 1. Buka halaman `/admin/login` <br> 2. Kosongkan email dan/atau password <br> 3. Klik tombol Login | Email: *(kosong)* <br> Password: *(kosong)* | Tampil pesan validasi: *"Email wajib diisi"* dan *"Password wajib diisi"* | |
| AU-06 | Login gagal — format email tidak valid | 1. Buka halaman `/admin/login` <br> 2. Masukkan email tanpa format valid <br> 3. Klik tombol Login | Email: `adminemail` <br> Password: `password123` | Tampil pesan validasi: *"Format email tidak valid"* | |
| AU-07 | Login gagal — password kurang dari 6 karakter | 1. Buka halaman `/admin/login` <br> 2. Masukkan password < 6 karakter <br> 3. Klik tombol Login | Email: `admin@example.com` <br> Password: `123` | Tampil pesan validasi: *"Password minimal 6 karakter"* | |

### 1.2 Logout

| ID | Deskripsi Pengujian | Langkah Pengujian | Data Uji | Hasil yang Diharapkan | Hasil Aktual |
|----|---------------------|-------------------|-----------|----------------------|--------------|
| AU-08 | Logout berhasil | 1. Pastikan sudah login <br> 2. Klik tombol Logout | — | Session dihapus, pengguna diarahkan ke halaman Login | |

### 1.3 Lupa Password

| ID | Deskripsi Pengujian | Langkah Pengujian | Data Uji | Hasil yang Diharapkan | Hasil Aktual |
|----|---------------------|-------------------|-----------|----------------------|--------------|
| AU-09 | Kirim link reset password berhasil | 1. Buka halaman `/admin/forgot-password` <br> 2. Masukkan email admin terdaftar <br> 3. Klik tombol Kirim | Email: `admin@example.com` | Tampil pesan sukses: *"Link reset password telah dikirim ke email Anda."* | |
| AU-10 | Kirim link reset password gagal — email tidak terdaftar | 1. Buka halaman `/admin/forgot-password` <br> 2. Masukkan email yang tidak terdaftar <br> 3. Klik tombol Kirim | Email: `unknown@example.com` | Tampil pesan error: *"Email tidak terdaftar sebagai admin."* | |
| AU-11 | Kirim link reset password gagal — field kosong | 1. Buka halaman `/admin/forgot-password` <br> 2. Kosongkan field email <br> 3. Klik tombol Kirim | Email: *(kosong)* | Tampil pesan validasi: *"Email wajib diisi"* | |

### 1.4 Reset Password

| ID | Deskripsi Pengujian | Langkah Pengujian | Data Uji | Hasil yang Diharapkan | Hasil Aktual |
|----|---------------------|-------------------|-----------|----------------------|--------------|
| AU-12 | Reset password berhasil | 1. Buka link reset password dari email <br> 2. Masukkan password baru dan konfirmasi <br> 3. Klik tombol Reset | Password: `newpass123` <br> Konfirmasi: `newpass123` | Diarahkan ke halaman Login dengan pesan: *"Password berhasil diubah."* | |
| AU-13 | Reset password gagal — password tidak cocok | 1. Buka link reset password dari email <br> 2. Masukkan password berbeda di konfirmasi <br> 3. Klik tombol Reset | Password: `newpass123` <br> Konfirmasi: `different456` | Tampil pesan validasi: *"Konfirmasi password tidak cocok"* | |
| AU-14 | Reset password gagal — link kadaluarsa | 1. Buka link reset password yang sudah kadaluarsa <br> 2. Masukkan password baru <br> 3. Klik tombol Reset | oobCode: *(expired)* | Tampil pesan error: *"Link mungkin sudah kadaluarsa atau tidak valid."* | |

---

## 2. Modul Dashboard

| ID | Deskripsi Pengujian | Langkah Pengujian | Data Uji | Hasil yang Diharapkan | Hasil Aktual |
|----|---------------------|-------------------|-----------|----------------------|--------------|
| DB-01 | Dashboard menampilkan data statistik lengkap | 1. Login sebagai admin <br> 2. Buka halaman `/admin/dashboard` | — | Halaman menampilkan: Total Users, Total Laporan, Total Artikel, Status Laporan (Baru, Diproses, Selesai, Ditolak), Grafik Kategori, Grafik Daerah | |
| DB-02 | Dashboard menampilkan grafik tren laporan (default 12 bulan terakhir) | 1. Login sebagai admin <br> 2. Buka halaman `/admin/dashboard` <br> 3. Lihat bagian "Tren Laporan per Periode" | — | Grafik menampilkan data 12 bulan terakhir dengan label bulan dan jumlah laporan per bulan | |
| DB-03 | Filter tren laporan per tahun | 1. Buka halaman Dashboard <br> 2. Pilih tahun tertentu dari dropdown <br> 3. Klik filter | Tahun: `2026` | Grafik menampilkan data 12 bulan pada tahun 2026 | |
| DB-04 | Filter tren laporan per bulan dalam tahun | 1. Buka halaman Dashboard <br> 2. Pilih tahun dan bulan tertentu dari dropdown <br> 3. Klik filter | Tahun: `2026`, Bulan: `3` (Maret) | Grafik menampilkan data per hari dalam bulan Maret 2026 | |
| DB-05 | Dashboard menampilkan 10 laporan terbaru | 1. Login sebagai admin <br> 2. Buka halaman Dashboard <br> 3. Scroll ke bagian "Laporan Terbaru" | — | Menampilkan maksimal 10 laporan terbaru, diurutkan dari yang paling baru | |

---

## 3. Modul Manajemen Artikel

### 3.1 Daftar Artikel

| ID | Deskripsi Pengujian | Langkah Pengujian | Data Uji | Hasil yang Diharapkan | Hasil Aktual |
|----|---------------------|-------------------|-----------|----------------------|--------------|
| AR-01 | Menampilkan daftar semua artikel | 1. Login sebagai admin <br> 2. Buka halaman `/admin/articel` | — | Halaman menampilkan daftar semua artikel, diurutkan dari tanggal rilis terbaru | |

### 3.2 Tambah Artikel

| ID | Deskripsi Pengujian | Langkah Pengujian | Data Uji | Hasil yang Diharapkan | Hasil Aktual |
|----|---------------------|-------------------|-----------|----------------------|--------------|
| AR-02 | Tambah artikel berhasil dengan data valid | 1. Buka halaman `/admin/articel/create` <br> 2. Isi judul, pilih kategori, isi deskripsi, upload gambar <br> 3. Klik tombol Simpan | Judul: `Pencegahan Stunting` <br> Kategori: `stunting` <br> Deskripsi: `Artikel tentang pencegahan stunting...` <br> Gambar: `stunting.jpg` (800KB) | Artikel tersimpan, diarahkan ke halaman daftar artikel dengan pesan sukses | |
| AR-03 | Tambah artikel gagal — field wajib kosong | 1. Buka halaman `/admin/articel/create` <br> 2. Kosongkan satu atau lebih field wajib <br> 3. Klik tombol Simpan | Judul: *(kosong)* <br> Kategori: *(tidak dipilih)* <br> Deskripsi: *(kosong)* <br> Gambar: *(tidak diupload)* | Tampil pesan validasi untuk setiap field yang kosong | |
| AR-04 | Tambah artikel gagal — format gambar tidak didukung | 1. Buka halaman `/admin/articel/create` <br> 2. Isi semua field <br> 3. Upload file non-gambar <br> 4. Klik tombol Simpan | Gambar: `document.pdf` | Tampil pesan validasi: *"File harus berformat jpg, jpeg, atau png"* | |
| AR-05 | Tambah artikel gagal — ukuran gambar melebihi batas | 1. Buka halaman `/admin/articel/create` <br> 2. Isi semua field <br> 3. Upload gambar > 2MB <br> 4. Klik tombol Simpan | Gambar: `large_image.jpg` (5MB) | Tampil pesan validasi: *"Ukuran gambar maksimal 2MB"* | |
| AR-06 | Tambah artikel gagal — kategori tidak valid | 1. Buka halaman `/admin/articel/create` <br> 2. Isi semua field dengan kategori tidak valid <br> 3. Klik tombol Simpan | Kategori: `invalid_category` | Tampil pesan validasi: *"Kategori tidak valid"* | |

### 3.3 Edit Artikel

| ID | Deskripsi Pengujian | Langkah Pengujian | Data Uji | Hasil yang Diharapkan | Hasil Aktual |
|----|---------------------|-------------------|-----------|----------------------|--------------|
| AR-07 | Edit artikel berhasil — tanpa ganti gambar | 1. Buka halaman edit artikel yang ada <br> 2. Ubah judul dan/atau deskripsi <br> 3. Klik tombol Simpan | Judul baru: `Pencegahan Stunting (Update)` | Artikel diperbarui, gambar tetap, diarahkan ke daftar artikel | |
| AR-08 | Edit artikel berhasil — dengan ganti gambar | 1. Buka halaman edit artikel yang ada <br> 2. Upload gambar baru <br> 3. Klik tombol Simpan | Gambar baru: `new_image.png` (500KB) | Artikel diperbarui dengan gambar baru, gambar lama dihapus dari storage | |
| AR-09 | Edit artikel berhasil — ganti kategori tanpa ganti gambar | 1. Buka halaman edit artikel yang ada <br> 2. Ubah kategori <br> 3. Klik tombol Simpan | Kategori lama: `stunting` → Kategori baru: `bullying` | Artikel diperbarui, gambar dipindahkan ke folder kategori baru | |

### 3.4 Hapus Artikel

| ID | Deskripsi Pengujian | Langkah Pengujian | Data Uji | Hasil yang Diharapkan | Hasil Aktual |
|----|---------------------|-------------------|-----------|----------------------|--------------|
| AR-10 | Hapus artikel berhasil | 1. Buka halaman daftar artikel <br> 2. Klik tombol Hapus pada artikel tertentu <br> 3. Konfirmasi penghapusan | ID artikel: *(yang ada)* | Artikel dan gambar terkait dihapus, pesan sukses ditampilkan | |

### 3.5 Export Artikel

| ID | Deskripsi Pengujian | Langkah Pengujian | Data Uji | Hasil yang Diharapkan | Hasil Aktual |
|----|---------------------|-------------------|-----------|----------------------|--------------|
| AR-11 | Export daftar artikel ke CSV — tanpa filter | 1. Buka halaman daftar artikel <br> 2. Klik tombol Download/Export CSV | — | File CSV terdownload dengan semua data artikel | |
| AR-12 | Export daftar artikel ke CSV — dengan filter kategori dan pencarian | 1. Buka halaman daftar artikel <br> 2. Pilih filter kategori dan/atau masukkan kata kunci pencarian <br> 3. Klik tombol Download/Export CSV | Kategori: `stunting` <br> Search: `pencegahan` | File CSV terdownload hanya berisi artikel yang sesuai filter | |

---

## 4. Modul Manajemen Laporan

### 4.1 Daftar Laporan

| ID | Deskripsi Pengujian | Langkah Pengujian | Data Uji | Hasil yang Diharapkan | Hasil Aktual |
|----|---------------------|-------------------|-----------|----------------------|--------------|
| LP-01 | Menampilkan daftar semua laporan | 1. Login sebagai admin <br> 2. Buka halaman `/admin/laporan` | — | Halaman menampilkan daftar semua laporan, diurutkan dari yang terbaru | |
| LP-02 | Filter laporan berdasarkan daerah | 1. Buka halaman daftar laporan <br> 2. Pilih filter daerah | Daerah: `Kota Pontianak` | Hanya menampilkan laporan dari daerah yang dipilih | |
| LP-03 | Filter laporan berdasarkan rentang tanggal | 1. Buka halaman daftar laporan <br> 2. Pilih tanggal mulai dan tanggal selesai | Tanggal mulai: `2026-01-01` <br> Tanggal selesai: `2026-03-31` | Hanya menampilkan laporan dalam rentang tanggal tersebut | |
| LP-04 | Filter laporan berdasarkan kategori | 1. Buka halaman daftar laporan <br> 2. Pilih filter kategori | Kategori: `Kekerasan Anak` | Hanya menampilkan laporan dengan kategori yang dipilih | |
| LP-05 | Filter laporan berdasarkan status | 1. Buka halaman daftar laporan <br> 2. Pilih filter status | Status: `diproses` | Hanya menampilkan laporan dengan status "diproses" | |
| LP-06 | Pencarian laporan berdasarkan nama pelapor | 1. Buka halaman daftar laporan <br> 2. Ketik nama pada kolom pencarian | Search: `Ahmad` | Hanya menampilkan laporan dari pelapor yang namanya mengandung "Ahmad" | |

### 4.2 Detail Laporan

| ID | Deskripsi Pengujian | Langkah Pengujian | Data Uji | Hasil yang Diharapkan | Hasil Aktual |
|----|---------------------|-------------------|-----------|----------------------|--------------|
| LP-07 | Menampilkan detail laporan | 1. Buka halaman daftar laporan <br> 2. Klik salah satu laporan | ID laporan: *(yang ada)* | Halaman menampilkan detail: Nomor Laporan, Nama Pelapor, No HP, Tipe Kasus, Daerah, Tanggal, Status, Deskripsi Lengkap | |
| LP-08 | Detail laporan tidak ditemukan | 1. Akses URL `/admin/laporan/nonexistent_id` | ID: `nonexistent_id` | Diarahkan ke daftar laporan dengan pesan error: *"Laporan tidak ditemukan."* | |

### 4.3 Ubah Status Laporan

| ID | Deskripsi Pengujian | Langkah Pengujian | Data Uji | Hasil yang Diharapkan | Hasil Aktual |
|----|---------------------|-------------------|-----------|----------------------|--------------|
| LP-09 | Ubah status laporan menjadi "diproses" | 1. Buka detail laporan <br> 2. Ubah status menjadi "diproses" <br> 3. Simpan | Status baru: `diproses` | Status laporan berubah menjadi "diproses", pesan sukses ditampilkan | |
| LP-10 | Ubah status laporan menjadi "selesai" | 1. Buka detail laporan <br> 2. Ubah status menjadi "selesai" <br> 3. Simpan | Status baru: `selesai` | Status laporan berubah menjadi "selesai", pesan sukses ditampilkan | |
| LP-11 | Ubah status laporan menjadi "ditolak" | 1. Buka detail laporan <br> 2. Ubah status menjadi "ditolak" <br> 3. Simpan | Status baru: `ditolak` | Status laporan berubah menjadi "ditolak", pesan sukses ditampilkan | |

### 4.4 Hapus Laporan

| ID | Deskripsi Pengujian | Langkah Pengujian | Data Uji | Hasil yang Diharapkan | Hasil Aktual |
|----|---------------------|-------------------|-----------|----------------------|--------------|
| LP-12 | Hapus laporan berhasil | 1. Buka halaman daftar/detail laporan <br> 2. Klik tombol Hapus <br> 3. Konfirmasi penghapusan | ID laporan: *(yang ada)* | Laporan dan subkoleksi chat terkait dihapus, pesan sukses ditampilkan | |

### 4.5 Export & Download

| ID | Deskripsi Pengujian | Langkah Pengujian | Data Uji | Hasil yang Diharapkan | Hasil Aktual |
|----|---------------------|-------------------|-----------|----------------------|--------------|
| LP-13 | Export daftar laporan ke CSV | 1. Buka halaman daftar laporan <br> 2. (Opsional) Terapkan filter <br> 3. Klik tombol Export CSV | — | File CSV terdownload berisi data laporan sesuai filter yang diterapkan | |
| LP-14 | Download laporan sebagai PDF | 1. Buka detail laporan <br> 2. Klik tombol Download PDF | ID laporan: *(yang ada)* | File PDF terdownload berisi ringkasan lengkap laporan | |

---

## 5. Modul Chat Laporan

### 5.1 Halaman Chat

| ID | Deskripsi Pengujian | Langkah Pengujian | Data Uji | Hasil yang Diharapkan | Hasil Aktual |
|----|---------------------|-------------------|-----------|----------------------|--------------|
| CH-01 | Membuka halaman chat laporan | 1. Buka detail laporan <br> 2. Klik tombol/link Chat | ID laporan: *(yang ada)* | Halaman chat terbuka, menampilkan histori pesan (jika ada) dan form kirim pesan | |

### 5.2 Kirim Pesan Chat

| ID | Deskripsi Pengujian | Langkah Pengujian | Data Uji | Hasil yang Diharapkan | Hasil Aktual |
|----|---------------------|-------------------|-----------|----------------------|--------------|
| CH-02 | Kirim pesan teks berhasil | 1. Buka halaman chat laporan <br> 2. Ketik pesan teks <br> 3. Klik tombol Kirim | Pesan: `Laporan sedang ditindaklanjuti` | Pesan muncul di histori chat dengan chatType "ADMIN" dan status "terkirim" | |
| CH-03 | Kirim pesan gambar berhasil | 1. Buka halaman chat laporan <br> 2. Upload gambar <br> 3. Klik tombol Kirim | Gambar: `bukti.jpg` (1MB) | Gambar terupload dan muncul di histori chat | |
| CH-04 | Kirim pesan gagal — tanpa teks dan gambar | 1. Buka halaman chat laporan <br> 2. Kosongkan teks dan tidak upload gambar <br> 3. Klik tombol Kirim | Teks: *(kosong)* <br> Gambar: *(tidak ada)* | Tampil pesan error: *"Pesan atau gambar wajib diisi"* (HTTP 422) | |
| CH-05 | Kirim pesan gagal — format gambar tidak didukung | 1. Buka halaman chat <br> 2. Upload file non-gambar | File: `document.docx` | Tampil pesan validasi format file tidak valid | |

### 5.3 Edit & Hapus Pesan

| ID | Deskripsi Pengujian | Langkah Pengujian | Data Uji | Hasil yang Diharapkan | Hasil Aktual |
|----|---------------------|-------------------|-----------|----------------------|--------------|
| CH-06 | Edit pesan chat berhasil | 1. Buka halaman chat <br> 2. Klik tombol Edit pada pesan admin <br> 3. Ubah teks pesan <br> 4. Simpan | Teks baru: `Laporan sudah ditindaklanjuti` | Pesan diperbarui, status berubah menjadi "teredit" | |
| CH-07 | Hapus pesan chat berhasil (soft delete) | 1. Buka halaman chat <br> 2. Klik tombol Hapus pada pesan admin <br> 3. Konfirmasi | ID pesan: *(yang ada)* | Pesan ditandai sebagai dihapus (isDeleted = true), tidak muncul lagi di tampilan | |

### 5.4 Notifikasi & Mark Read

| ID | Deskripsi Pengujian | Langkah Pengujian | Data Uji | Hasil yang Diharapkan | Hasil Aktual |
|----|---------------------|-------------------|-----------|----------------------|--------------|
| CH-08 | Notifikasi chat belum dibaca muncul | 1. Login sebagai admin <br> 2. Pastikan ada pesan baru dari user <br> 3. Perhatikan ikon notifikasi | — | Badge notifikasi menampilkan jumlah pesan belum dibaca | |
| CH-09 | Mark chat as read berhasil | 1. Buka halaman chat laporan yang memiliki pesan belum dibaca <br> 2. Pesan otomatis ditandai sudah dibaca | ID laporan: *(yang ada)* | Timestamp `adminLastReadAt` diperbarui, jumlah notifikasi berkurang | |

---

## 6. Modul Pengaturan Admin

> [!IMPORTANT]
> Modul ini hanya dapat diakses oleh admin dengan role **super_admin**.

### 6.1 Akses Halaman Pengaturan

| ID | Deskripsi Pengujian | Langkah Pengujian | Data Uji | Hasil yang Diharapkan | Hasil Aktual |
|----|---------------------|-------------------|-----------|----------------------|--------------|
| PG-01 | Akses pengaturan sebagai super_admin | 1. Login sebagai super_admin <br> 2. Buka halaman `/admin/pengaturan` | Role: `super_admin` | Halaman pengaturan terbuka, menampilkan daftar semua admin | |
| PG-02 | Akses pengaturan ditolak untuk admin biasa | 1. Login sebagai admin biasa <br> 2. Buka halaman `/admin/pengaturan` | Role: `admin` | Diarahkan ke Dashboard dengan pesan error: *"Anda tidak memiliki akses."* | |

### 6.2 Tambah Admin

| ID | Deskripsi Pengujian | Langkah Pengujian | Data Uji | Hasil yang Diharapkan | Hasil Aktual |
|----|---------------------|-------------------|-----------|----------------------|--------------|
| PG-03 | Tambah admin berhasil | 1. Buka halaman Pengaturan <br> 2. Isi email, password, dan role <br> 3. Klik tombol Tambah | Email: `newadmin@example.com` <br> Password: `admin123` <br> Role: `admin` | Admin baru berhasil ditambahkan, muncul di daftar admin | |
| PG-04 | Tambah admin gagal — email sudah terdaftar | 1. Buka halaman Pengaturan <br> 2. Isi email yang sudah terdaftar <br> 3. Klik tombol Tambah | Email: `admin@example.com` <br> Password: `admin123` <br> Role: `admin` | Tampil pesan error: *"Gagal menambahkan admin"* (email sudah terdaftar di Firebase Auth) | |
| PG-05 | Tambah admin gagal — field kosong | 1. Buka halaman Pengaturan <br> 2. Kosongkan satu atau lebih field <br> 3. Klik tombol Tambah | Email: *(kosong)* <br> Password: *(kosong)* | Tampil pesan validasi untuk setiap field yang kosong | |

### 6.3 Hapus Admin

| ID | Deskripsi Pengujian | Langkah Pengujian | Data Uji | Hasil yang Diharapkan | Hasil Aktual |
|----|---------------------|-------------------|-----------|----------------------|--------------|
| PG-06 | Hapus admin biasa berhasil | 1. Buka halaman Pengaturan <br> 2. Klik tombol Hapus pada admin biasa <br> 3. Konfirmasi | UID admin: *(role = admin)* | Admin dihapus dari Firebase Auth dan Firestore, hilang dari daftar | |
| PG-07 | Hapus super_admin gagal | 1. Buka halaman Pengaturan <br> 2. Klik tombol Hapus pada akun super_admin <br> 3. Konfirmasi | UID admin: *(role = super_admin)* | Tampil pesan error: *"Akun super_admin tidak bisa dihapus."* | |

---

## 7. Modul Profil

| ID | Deskripsi Pengujian | Langkah Pengujian | Data Uji | Hasil yang Diharapkan | Hasil Aktual |
|----|---------------------|-------------------|-----------|----------------------|--------------|
| PF-01 | Menampilkan halaman profil | 1. Login sebagai admin <br> 2. Buka halaman `/admin/profile` | — | Halaman profil menampilkan informasi akun admin (email, role) | |
| PF-02 | Ubah password berhasil | 1. Buka halaman Profil <br> 2. Masukkan password saat ini, password baru, dan konfirmasi <br> 3. Klik tombol Simpan | Password saat ini: `oldpass123` <br> Password baru: `newpass123` <br> Konfirmasi: `newpass123` | Password berhasil diperbarui, pesan sukses ditampilkan | |
| PF-03 | Ubah password gagal — password saat ini salah | 1. Buka halaman Profil <br> 2. Masukkan password saat ini yang salah <br> 3. Klik tombol Simpan | Password saat ini: `wrongpass` <br> Password baru: `newpass123` <br> Konfirmasi: `newpass123` | Tampil pesan error: *"Password saat ini salah"* | |
| PF-04 | Ubah password gagal — konfirmasi tidak cocok | 1. Buka halaman Profil <br> 2. Masukkan password baru berbeda di konfirmasi <br> 3. Klik tombol Simpan | Password saat ini: `oldpass123` <br> Password baru: `newpass123` <br> Konfirmasi: `different456` | Tampil pesan error: *"Konfirmasi password baru tidak cocok"* | |
| PF-05 | Ubah password gagal — password baru terlalu pendek | 1. Buka halaman Profil <br> 2. Masukkan password baru < 6 karakter <br> 3. Klik tombol Simpan | Password saat ini: `oldpass123` <br> Password baru: `abc` <br> Konfirmasi: `abc` | Tampil pesan validasi: *"Password baru minimal 6 karakter"* | |

---

## 8. Pengujian Akses Tanpa Login (Keamanan)

| ID | Deskripsi Pengujian | Langkah Pengujian | Data Uji | Hasil yang Diharapkan | Hasil Aktual |
|----|---------------------|-------------------|-----------|----------------------|--------------|
| SC-01 | Akses dashboard tanpa login | 1. Hapus session/cookie <br> 2. Akses URL `/admin/dashboard` langsung | — | Diarahkan ke halaman Login | |
| SC-02 | Akses halaman artikel tanpa login | 1. Hapus session/cookie <br> 2. Akses URL `/admin/articel` langsung | — | Diarahkan ke halaman Login | |
| SC-03 | Akses halaman laporan tanpa login | 1. Hapus session/cookie <br> 2. Akses URL `/admin/laporan` langsung | — | Diarahkan ke halaman Login | |
| SC-04 | Akses halaman pengaturan tanpa login | 1. Hapus session/cookie <br> 2. Akses URL `/admin/pengaturan` langsung | — | Diarahkan ke halaman Login | |
| SC-05 | Akses halaman profil tanpa login | 1. Hapus session/cookie <br> 2. Akses URL `/admin/profile` langsung | — | Diarahkan ke halaman Login | |

---

## Panduan Pengisian Kolom "Hasil Aktual"

> [!TIP]
> Untuk setiap skenario, isi kolom **Hasil Aktual** setelah pengujian dilakukan:
> - ✅ **Sesuai** — jika hasil aktual sesuai dengan hasil yang diharapkan
> - ❌ **Tidak Sesuai** — jika hasil aktual berbeda, deskripsikan perbedaannya
> - ⚠️ **Sebagian Sesuai** — jika hanya sebagian fungsi yang berjalan, jelaskan bagian mana yang tidak sesuai

---

## Ringkasan Teknis Pengujian

| Aspek | Detail |
|-------|--------|
| **Metode** | Black Box Testing |
| **Teknik** | Equivalence Partitioning, Boundary Value Analysis |
| **Total Skenario** | 71 skenario (termasuk 5 skenario keamanan) |
| **Platform** | Web Browser (Chrome/Firefox/Edge) |
| **Backend** | Laravel + Firebase (Firestore & Auth) |
| **Database** | Firebase Firestore |
| **Autentikasi** | Firebase Authentication |
| **Storage** | Firebase Cloud Storage |
