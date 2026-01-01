# Database Schema

Entity Relationship Diagram (ERD) for Dynamic Challenge Tracking System — MVP

## Overview

The database design focuses on:
- **Dynamic challenge configuration** - Flexible rule-based system
- **Daily participant submissions** - Track progress over time
- **Secure file storage** - MinIO integration for uploads
- **Clear separation of concerns** - Modular entity relationships

## Database Schema

### users

Represents both Admin and Participant accounts.

| Field | Type | Description |
|-------|------|-------------|
| id | PK | Primary Key |
| name | string | User name |
| email | string | Email address |
| password | string (nullable) | Hashed password for admin login |
| google_id | string (nullable) | Google OAuth ID |
| role | enum | `admin` or `participant` |
| email_verified_at | timestamp | Email verification timestamp |
| created_at | timestamp | Record creation time |
| updated_at | timestamp | Last update time |

### challenges

Stores challenge configuration and metadata.

| Field | Type | Description |
|-------|------|-------------|
| id | PK | Primary Key |
| title | string | Challenge title |
| description | text | Challenge description |
| start_date | date | Challenge start date |
| end_date | date | Challenge end date |
| duration_days | integer | Total duration in days |
| status | enum | `draft`, `active`, or `finished` |
| created_by | FK → users.id | Creator user ID |
| created_at | timestamp | Record creation time |
| updated_at | timestamp | Last update time |

### challenge_rules

Defines dynamic form fields for each challenge.

| Field | Type | Description |
|-------|------|-------------|
| id | PK | Primary Key |
| challenge_id | FK → challenges.id | Associated challenge |
| label | string | Field label |
| field_type | enum | `text`, `number`, `file`, or `boolean` |
| is_required | boolean | Whether field is mandatory |
| order_number | integer | Display order |
| created_at | timestamp | Record creation time |
| updated_at | timestamp | Last update time |

### challenge_participants

Manages user participation in challenges.

| Field | Type | Description |
|-------|------|-------------|
| id | PK | Primary Key |
| user_id | FK → users.id | Participant user ID |
| challenge_id | FK → challenges.id | Associated challenge |
| joined_at | timestamp | Join timestamp |
| status | enum | `active` or `dropped` |

### submissions

Stores daily submissions from participants.

| Field | Type | Description |
|-------|------|-------------|
| id | PK | Primary Key |
| user_id | FK → users.id | Submitting user ID |
| challenge_id | FK → challenges.id | Associated challenge |
| day_number | integer | Day of challenge (1-N) |
| submitted_at | timestamp | Submission timestamp |
| status | enum | `pending`, `approved`, or `rejected` |
| created_at | timestamp | Record creation time |
| updated_at | timestamp | Last update time |

**Unique Constraint:** `(user_id, challenge_id, day_number)` - One submission per day per user per challenge

### submission_values

Stores values for each rule in a submission.

| Field | Type | Description |
|-------|------|-------------|
| id | PK | Primary Key |
| submission_id | FK → submissions.id | Associated submission |
| rule_id | FK → challenge_rules.id | Associated rule |
| value_text | text (nullable) | Text value |
| value_number | decimal (nullable) | Numeric value |
| value_boolean | boolean (nullable) | Boolean value |
| created_at | timestamp | Record creation time |
| updated_at | timestamp | Last update time |

### files

Stores file metadata (actual files stored in MinIO).

| Field | Type | Description |
|-------|------|-------------|
| id | PK | Primary Key |
| submission_value_id | FK → submission_values.id | Associated submission value |
| disk | string | Storage disk (`minio`) |
| path | string | Object path in bucket |
| mime_type | string | File MIME type |
| size | integer | File size in bytes |
| created_at | timestamp | Record creation time |

## Entity Relationships

```
users
 ├──< challenges (created_by)
 ├──< challenge_participants >── challenges
 └──< submissions >── challenges

challenges
 ├──< challenge_rules
 ├──< challenge_participants
 └──< submissions

submissions
 └──< submission_values
        └── files
```

## Key Design Decisions

- **Single users table** - Simplifies authentication and role management
- **Dynamic rules** - Challenge-specific fields without schema changes
- **Polymorphic values** - Separate columns for different data types
- **File abstraction** - Metadata in database, files in MinIO
- **Submission uniqueness** - Constraint ensures one submission per day
