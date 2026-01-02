# Admin Dashboard Specification (Filament)

## 1. Overview
Admin Dashboard merupakan pusat kontrol bagi admin untuk memonitor, memvalidasi, dan mengelola seluruh aktivitas challenge dan participant. Dashboard dibangun menggunakan **Filament Admin Panel** untuk mempercepat development, menjaga konsistensi UI, dan memanfaatkan fitur bawaan seperti resource, widget, dan role-based access.

---

## 2. User Role
### Admin
- Login menggunakan email & password
- Akses penuh ke dashboard dan seluruh resource

---

## 3. Technology Stack
- **Framework**: Laravel 12
- **Admin Panel**: Filament v3
- **Authentication**: Laravel Auth
- **Storage**: MinIO (S3-compatible)
- **Database**: MySQL

---

## 4. Dashboard Layout (Filament Panel)

### 4.1 Navigation Menu
- Dashboard
- User Management
    - User
    - Role
    - Permission
- Challenges

---

## 5. Dashboard Widgets

### 5.1 Statistic Widgets
Displayed on main dashboard page:
- Total Challenges
- Total Participants
- Total Submissions (Today)
- Pending Validation Submissions

**Filament Component**
- `StatsOverviewWidget`

---