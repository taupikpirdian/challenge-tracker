# Dynamic Challenge Tracking System

A general-purpose web platform designed to manage and track community-based challenges in a structured, efficient, and scalable way.

**Live:** [https://challenge-tracker.tcode.my.id/](https://challenge-tracker.tcode.my.id/)

## Overview

The Dynamic Challenge Tracking System replaces manual progress reporting (e.g., via chat applications) with a centralized dashboard that automates:
- Daily submissions
- Progress tracking
- Admin validation
- Data recap and reporting

The platform is **challenge-agnostic**, supporting various use cases such as workouts, learning habits, reading programs, and other daily activities.

## Problem Statement

Many community challenges rely on manual reporting through messaging platforms, which introduces several issues:

- Submissions are scattered and difficult to track
- Admins spend excessive time on manual recap
- High risk of human error
- Lack of real-time progress visibility
- No structured historical data for reporting

These limitations become more severe as the number of participants or challenges increases.

## Features

### Core Functionality
- Centralized system for tracking daily challenge activities
- Automated administrative workflows
- Dynamic rule-based input forms
- Support for multiple challenge types without code changes

### Authentication
- Admin login via Email/Password
- Participant login via Google Account

### Challenge Management
- Create and configure challenges
- Define challenge rules and input types
- Review and validate submissions
- Monitor participant progress
- Export challenge reports

### Storage
- Secure file upload support (photos) using MinIO
- Reliable data persistence

## User Roles

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

## Architecture

The system follows a web-based architecture with separate concerns for:
- Application logic
- Data persistence
- File storage

## Scope

### Included (MVP)
- Admin authentication (Email/Password)
- Participant authentication (Google Account)
- Challenge creation and configuration
- Dynamic rule-based input forms
- Daily submissions per participant
- File upload support (photo) using MinIO
- Admin dashboard for monitoring and validation
- Secure object storage

## Goals

### Primary
- Provide a centralized system for tracking daily challenge activities
- Reduce administrative overhead through automation
- Ensure data consistency and reliability
- Support multiple types of challenges without code changes

### Secondary
- Enable future scalability (multi-community, SaaS-ready)
- Maintain a simple and intuitive user experience
- Minimize infrastructure and operational cost

## Preview

### Landing Page
<img src="/public/readme/landing-page.png" alt="Landing Page" width="800"/>

### Create Challenge
<img src="/public/readme/create-challenge.png" alt="Create Challenge" width="800"/>

### Dashboard
<img src="/public/readme/dashboard.png" alt="Dashboard" width="800"/>

### Challenge Detail
<img src="/public/readme/detail-challenge.png" alt="Challenge Detail" width="800"/>

## Documentation

For detailed system design information, see the [system overview documentation](docs/system_design/system-overview.md).

## Contributing

We welcome contributions from the community! If you'd like to participate in the development of this project, please feel free to submit a Pull Request.

### How to Contribute
1. Fork the repository
2. Create a feature branch (`git checkout -b feature/your-feature`)
3. Commit your changes (`git commit -m 'Add some feature'`)
4. Push to the branch (`git push origin feature/your-feature`)
5. Open a Pull Request

We appreciate your contributions and help in making this project better!
