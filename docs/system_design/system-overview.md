# System Overview  
Dynamic Challenge Tracking System

---

## 1. Purpose

The Dynamic Challenge Tracking System is a general-purpose web platform designed to manage and track community-based challenges in a structured, efficient, and scalable way.

The system replaces manual progress reporting (e.g. via chat applications) with a centralized dashboard that automates:
- Daily submissions
- Progress tracking
- Admin validation
- Data recap and reporting

The platform is intentionally designed to be **challenge-agnostic**, allowing it to support various use cases such as workouts, learning habits, reading programs, and other daily activities.

---

## 2. Problem Statement

Many community challenges rely on manual reporting through messaging platforms. This approach introduces several issues:

- Submissions are scattered and difficult to track
- Admins spend excessive time on manual recap
- High risk of human error
- Lack of real-time progress visibility
- No structured historical data for reporting

These limitations become more severe as the number of participants or challenges increases.

---

## 3. Goals & Objectives

### Primary Goals
- Provide a centralized system for tracking daily challenge activities
- Reduce administrative overhead through automation
- Ensure data consistency and reliability
- Support multiple types of challenges without code changes

### Secondary Goals
- Enable future scalability (multi-community, SaaS-ready)
- Maintain a simple and intuitive user experience
- Minimize infrastructure and operational cost

---

## 4. Scope

### In Scope (MVP)
- Login Admin Email/Password
- Login Participant Google Account
- Challenge creation and configuration
- Dynamic rule-based input forms
- Daily submissions per participant
- File upload support (photo) using MinIO
- Admin dashboard for monitoring and validation
- Secure object storage using MinIO

### Out of Scope (Initial Phase)
- Payment and billing
- Gamification features (badges, rewards)
- Native mobile applications
- Messaging platform integration (WhatsApp API)
- Advanced analytics

---

## 5. Actors & Roles

### Participant
- Join available challenges
- Submit daily progress
- View personal progress history

### Admin
- Create and manage challenges
- Define challenge rules and input types
- Review and validate submissions
- Monitor participant progress
- Export challenge reports

### Super Admin (Optional)
- Manage system-level configuration
- Manage admin accounts

---

## 6. High-Level Architecture

The system follows a web-based architecture with separate concerns for application logic, data persistence, and file storage.

## 7. Technical Stack

- **Backend**: PHP (Laravel)
- **Database**: MySQL
- **File Storage**: MinIO (S3-compatible)
- **Frontend**: Blade (Laravel's templating engine)
- **Authentication**: Google OAuth 2.0
- **Deployment**: Docker, Nginx
- **CMS**: Filament Admin Panel
