# Dokumentasi Hak Akses & Peran (Roles & Permissions)

Sistem Kepegawaian (SIMPEG) ini menggunakan _Role-Based Access Control_ (RBAC) untuk mengatur hak akses pengguna. Berikut adalah detail lengkap mengenai akses dan visibilitas untuk setiap peran:

## 1. Super Admin

**Akses Penuh (Full Access)**

- **Dashboard**: Melihat semua statistik secara global.
- **Manajemen User**: Bisa membuat, mengedit, dan menghapus user serta mengatur role dan permission.
- **Modul**: Akses penuh ke seluruh modul system (Kepegawaian, Presensi, Penilaian, dll).
- **Settings**: Mengatur konfigurasi sistem global.

## 2. Ketua PSDM (Supervisor)

**Akses Manajerial Global**

- **Dashboard**:
    - Melihat statistik global (seluruh unit).
    - Widget: _HRStatsOverview_ (Total Pegawai, Status), _EmployeeDistribution_ (Grafik per Unit), _GenderStats_.
- **Modul Kepegawaian (Data Induk)**: Bisa melihat, menambah, mengubah, dan menghapus data pegawai di seluruh unit.
- **Modul Presensi**: Bisa mengelola data absensi, jadwal piket, dan kegiatan untuk seluruh pegawai.
- **Modul Penilaian Kinerja**: Akses penuh untuk memantau sesi penilaian dan hasil penilaian seluruh pegawai.
- **Batasan**: Tidak bisa mengubah konfigurasi sistem inti (Settings) atau Role/Permission level sistem.

## 3. Kepala Sekolah (Viewer / Approver)

**Akses Monitoring (Read-Only)**

- **Dashboard**:
    - Hanya melihat ringkasan statistik global (_DashboardStatsOverview_).
    - Tampilan telah disederhanakan (widget detail disembunyikan).
- **Modul**:
    - Bisa **Meliat (View)** data pegawai, absensi, cuti, dan penilaian.
    - **TIDAK BISA** mengubah, menambah, atau menghapus data utama (Read Only).
- **Tujuan**: Memantau operasional tanpa risiko mengubah data secara tidak sengaja.

## 4. Admin Unit (Unit Manager)

**Akses Terbatas (Scoped to Unit)**

- **Cakupan Data**: Hanya bisa mengakses data pegawai yang berada di **Unit yang sama** dengan dirinya.
- **Dashboard**:
    - Statistik (_DashboardStatsOverview_) otomatis memfilter data hanya untuk unitnya.
- **Modul Kepegawaian**:
    - Bisa mengelola (CRUD) data pegawai di unitnya.
- **Modul Presensi**:
    - Bisa menginput dan mengelola absensi untuk pegawai unitnya.
- **Keterbatasan**: Tidak bisa melihat data pegawai dari unit lain.

## 5. Koordinator Jenjang

**Akses Monitoring Unit (Scoped Viewer)**

- **Cakupan Data**: Terbatas pada unit yang ditugaskan.
- **Dashboard**:
    - Melihat daftar pegawai yang siap dinilai di unitnya (_UnitEmployeeListWidget_).
- **Modul Penilaian**:
    - Fokus pada pemantauan dan penilaian pegawai dalam jenjang/unitnya.
    - Tidak memiliki akses penuh ke manajemen HR global.

## 6. Staff (Pegawai)

**Akses Personal (Self-Service)**

- **Dashboard**:
    - Widget Absensi Pribadi (Tombol Check-in/Check-out).
    - Riwayat kehadiran sendiri.
- **Modul Absensi**:
    - Melakukan absensi (Web/GPS/Selfie).
    - Melihat riwayat absensi sendiri.
- **Modul Cuti & Izin**:
    - Mengajukan permohonan cuti.
- **Modul Data Induk**:
    - Melihat profil data diri sendiri.
- **Keterbatasan**: Sama sekali tidak bisa melihat data pegawai lain.

---

## Tabel Ringkasan Login Demo

| Role               | Email                       | Password Default |
| :----------------- | :-------------------------- | :--------------- |
| **Super Admin**    | `super.admin@domain.com`    | `password`       |
| **Ketua PSDM**     | `ketua.psdm@domain.com`     | `password`       |
| **Kepala Sekolah** | `kepala.sekolah@domain.com` | `password`       |
| **Admin Unit**     | `admin.unit@domain.com`     | `password`       |
| **Staff**          | `staff@domain.com`          | `password`       |
