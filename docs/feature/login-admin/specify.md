# Feature Specification  
Login Page Admin (Email & Password)

---

## 1. Feature Name
Login Page Admin

---

## 2. Description

This feature allows administrators to authenticate into the system using an email and password.  
Only users with the `admin` role are permitted to access the admin area and dashboard.

This login mechanism is separate from participant authentication, which uses Google OAuth.

---

## 3. Goals

- Provide secure authentication for admin users
- Enable access to the admin dashboard after successful login
- Maintain a simple and reliable login experience for MVP

---

## 4. Scope (MVP)

### In Scope
- Admin login using email and password
- Session-based authentication
- Role validation (`admin`)
- Protected admin routes

---

## 5. Actors

### Admin
- Uses email and password to log in
- Accesses admin dashboard after authentication

---

## 6. Preconditions

- Admin account already exists in the `users` table
- Admin user has:
  - `role = 'admin'`
  - `email` and `password` set
- System authentication is enabled

---

## 7. User Flow

```text
Admin visits /admin/login
→ Enters email and password
→ Clicks Login
→ System validates credentials
   ├─ Success → Redirect to /admin/dashboard
   └─ Failure → Show error message
