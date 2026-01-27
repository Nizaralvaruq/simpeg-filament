# Sistem Kepegawaian (SIMPEG) - Filament

Web-based Human Resource Management System built with **Laravel** and **FilamentPHP**. This application manages employee data, attendance (with geolocation & photo), performance appraisals, leave requests, and resignations, featuring a robust role-based access control system.

## 🚀 Key Features

- **Role-Based Access Control (RBAC)**: Granular permissions for Super Admin, Ketua PSDM, Kepala Sekolah, Admin Unit, Koordinator Jenjang, and Staff.
- **Attendance System**:
    - GPS Geolocation verifcation.
    - Photo/Selfie verification.
    - QR Code attendance support.
    - Automated late detection.
- **Employee Management (Kepegawaian)**: Complete employee lifecycle management from recruitment to resignation.
- **Performance Appraisal (Penilaian Kinerja)**: Structured appraisal sessions with Superior, Peer, and Self-reviews.
- **Leave Management**: Request and approval workflow for employee leave.
- **Dynamic Dashboard**: Personalized dashboards ensuring users only see data relevant to their role and unit.

## 📦 Modules

The application is architected using **nwidart/laravel-modules** for better scalability:

- **Kepegawaian**: Core employee data (`DataInduk`).
- **Presensi**: Attendance, Schedules, Activities (`Absensi`, `JadwalPiket`).
- **PenilaianKinerja**: Performance appraisal cycles and scoring.
- **Leave**: Leave requests and quotas.
- **Resign**: Resignation workflow and user account cleanup.
- **MasterData**: System settings and configurations.
- **Akademik**: Academic related data (if applicable).

## 🛠️ Tech Stack

- **Framework**: Laravel 11/12
- **Admin Panel**: FilamentPHP v3/v4
- **Modules**: nwidart/laravel-modules
- **Permissions**: spatie/laravel-permission & bezhansalleh/filament-shield
- **Frontend**: Livewire, TailwindCSS, Alpine.js
- **Database**: MySQL/MariaDB

## ⚙️ Installation

1. **Clone the repository**

    ```bash
    git clone https://github.com/Nizaralvaruq/simpeg-filament.git
    cd simpeg-filament
    ```

2. **Install Dependencies**

    ```bash
    composer install
    npm install && npm run build
    ```

3. **Environment Setup**

    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

    _Configure your database settings in `.env`_

4. **Database Migration & Seeding**
   The project includes a comprehensive seeder for roles, permissions, and demo data.

    ```bash
    php artisan migrate:fresh --seed
    ```

5. **Run the Application**
    ```bash
    php artisan serve
    ```

## 🔐 Demo Credentials

The database seeder creates the following users for testing purposes.
**Default Password:** `password`

| Role               | Email                       | Description                                                                 |
| :----------------- | :-------------------------- | :-------------------------------------------------------------------------- |
| **Super Admin**    | `super.admin@domain.com`    | Full access to all modules and settings.                                    |
| **Ketua PSDM**     | `ketua.psdm@domain.com`     | Supervisor access. Can manage employees/attendance but not system settings. |
| **Kepala Sekolah** | `kepala.sekolah@domain.com` | View-only access for monitoring. Cannot edit data.                          |
| **Admin Unit**     | `admin.unit@domain.com`     | Manages data ONLY for their specific Unit.                                  |
| **Staff**          | `staff@domain.com`          | Employee access. Can view own attendance and request leave.                 |

## 🛡️ Security

- **Cascade Delete**: Deleting an employee in 'Data Induk' automatically removes their User account.
- **Scoped Access**: 'Admin Unit' and 'Koordinator Jenjang' are strictly limited to their assigned units.

## 📝 License

[MIT](https://opensource.org/licenses/MIT)
