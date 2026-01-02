# Participant Login Specification

## 1. Overview
Fitur Login Participant memungkinkan user (participant) untuk masuk ke sistem menggunakan email dan password. Login ini digunakan untuk mengidentifikasi participant, mengaitkan submission ke akun user, dan membatasi akses sesuai peran participant.
---

## 2. User Role
### Participant
- Login menggunakan Email dan password
- Membuat challenge
- Mengikuti challenge
- Mengisi dan mengirim submission harian
- Melihat riwayat submission pribadi

---

## 3. Authentication Flow

### 4.1 Login Flow
1. Participant membuka halaman `/login`
2. Participant memasukan email dan password, jika belum punya akun bisa melakukan register dengan membuka halaman `/register`
3. Participant berhasil login dan diarahkan ke dashboard / challenge page

---

## 5. UI Specification

### 5.1 Login Page
**URL**: `/login`

#### Components
- Logo aplikasi
- Judul: *"Login"*
- Form:
  - **Email**
  - **Password**
- Button:
  - "Login"
  - "Register"

---
### 5.2 Register Page
**URL**: `/register`

#### Components
- Logo aplikasi
- Judul: *"Register"*
- Form:
  - **Email**
  - **Phone Number** (optional)
  - **Password**
  - **Confirmation Password**
- Button:
  - "Submit"
  - "Login"