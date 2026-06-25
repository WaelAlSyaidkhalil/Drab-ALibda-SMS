# Parent Mobile Application Backend Specification

## Objective

Build the backend APIs for the Parent Mobile Application of the School Management System.

The project already contains an implemented Teacher Module.

The Parent Module MUST reuse the existing architecture, coding standards, models, services, resources, policies, permissions, notifications, and response structure already used in the Teacher Module.

Do not generate duplicate logic.

Always inspect the existing codebase first before creating new models, tables, services, or controllers.

---

# Technical Stack

Backend:

- Laravel 11
- MySQL
- Laravel Sanctum
- RESTful API
- Eloquent ORM

Architecture:

- Controllers
- Form Requests
- Services
- Resources
- Policies
- Notifications

Business logic must remain inside Services.

Controllers should remain thin.

---

# Parent Application Features

The Parent Application supports:

1. Authentication
2. Profile Management
3. Children Management
4. Student Schedule
5. Student Grades
6. Student Attendance
7. Absence Excuse Requests
8. Teacher Notes
9. Announcements
10. Driver Information

---

# Authentication APIs

## Login

POST /api/parent/login

Fields:

- phone_number
- password

Requirements:

- Validate credentials
- Generate Sanctum token
- Return parent information

---

## Logout

POST /api/parent/logout

Requirements:

- Revoke current token

---

## Change Password

POST /api/parent/change-password

Fields:

- current_password
- new_password
- new_password_confirmation

---

## Get Current Parent

GET /api/parent/profile

Returns:

- id
- name
- phone_number
- profile_image

---

# Parent Profile

## Update Profile

PUT /api/parent/profile

Editable fields:

- name
- phone_number
- profile_image

---

# Children Module

A Parent can have one or multiple children.

Use existing Student entities.

Do not create duplicate student structures.

---

## List Children

GET /api/parent/children

Return:

- id
- full_name
- classroom
- section
- image

---

## Child Details

GET /api/parent/children/{student}

Return:

- Personal Information
- Academic Information
- Attendance Summary

---

# Schedule Module

## Student Schedule

GET /api/parent/children/{student}/schedule

Return:

- day
- subject
- teacher
- start_time
- end_time

Reuse timetable logic from Teacher Module.

---

# Grades Module

## Student Grades

GET /api/parent/children/{student}/grades

Filters:

- academic_year
- semester

Return:

- subject
- exam_type
- grade
- max_grade
- exam_date

---

# Attendance Module

## Attendance Summary

GET /api/parent/children/{student}/attendance

Return:

- present_days
- absent_days
- excused_absences
- unexcused_absences
- attendance_percentage

---

# Excuse Requests Module

Parents can submit absence excuses.

Status values:

- pending
- approved
- rejected

---

## Create Excuse Request

POST /api/parent/excuse-requests

Fields:

- student_id
- absence_date
- reason
- attachment (optional)

---

## List Excuse Requests

GET /api/parent/excuse-requests

---

## Excuse Request Details

GET /api/parent/excuse-requests/{id}

---

# Teacher Notes Module

Teachers can send notes to parents.

---

## List Notes

GET /api/parent/notes

Return:

- title
- content
- teacher
- student
- created_at

Pagination required.

---

## Note Details

GET /api/parent/notes/{id}

---

# Announcements Module

## List Announcements

GET /api/parent/announcements

Return:

- title
- description
- image
- published_at

Pagination required.

---

## Announcement Details

GET /api/parent/announcements/{id}

---

# Driver Information

## Driver Details

GET /api/parent/children/{student}/driver

Return:

- driver_name
- phone_number
- vehicle_information

---

# Authorization Rules

Parent must only access:

- Their own profile
- Their own children
- Their own children grades
- Their own children attendance
- Their own children notes
- Their own children schedules
- Their own children driver information
- Their own excuse requests

Never allow access to another parent's data.

Use Policies.

---

# API Response Format

Success Response

```json
{
    "success": true,
    "message": "Operation completed successfully",
    "data": {}
}
```

Validation Error

```json
{
    "success": false,
    "message": "Validation error",
    "errors": {}
}
```

Unauthorized

```json
{
    "success": false,
    "message": "Unauthorized"
}
```

---

# Required Laravel Components

Generate:

- Routes
- Controllers
- Form Requests
- Services
- API Resources
- Policies
- Notifications (if required)
- Feature Tests

Reuse existing Models whenever possible.

Do not create new database tables unless absolutely necessary.

---

# Expected APIs Count

Authentication:

- Login
- Logout
- Change Password
- Profile

Profile:

- Update Profile

Children:

- List Children
- Child Details

Schedule:

- Student Schedule

Grades:

- Student Grades

Attendance:

- Attendance Summary

Excuse Requests:

- Create Request
- List Requests
- Request Details

Teacher Notes:

- List Notes
- Note Details

Announcements:

- List Announcements
- Announcement Details

Driver:

- Driver Details

Total Expected APIs: 17
