# Darb Al-Ibdaa - School Management System (SMS)

Welcome to the **Darb Al-Ibdaa SMS** repository! This is a modern, feature-rich School Management System built on top of the powerful Laravel framework. It provides a comprehensive solution for managing school operations, academics, students, teachers, and parent communications.

## 🚀 Technology Stack

- **Backend:** Laravel 13, PHP 8.3
- **Admin Panel:** Filament v5.6
- **API Authentication:** Laravel Sanctum
- **Push Notifications:** Firebase Cloud Messaging (via `kreait/laravel-firebase`)
- **Schedule Generation:** Google OR-Tools (Constraint Programming)
- **Database:** MySQL / SQLite (with comprehensive seeders and migrations)

## 🌟 Key Features

### 1. Robust Dashboard & Admin Panel
Built entirely with **Filament**, the admin panel offers an intuitive, responsive, and beautiful interface for school administrators to manage all aspects of the institution.

### 2. Comprehensive Academic Management
- **Terms & Time Slots:** Manage academic years, semesters, and daily bell schedules.
- **Classes & Sections:** Structure the school into stages (Primary, Preparatory, Secondary), classes, and specific student sections.
- **Subjects & Schedules:** Assign subjects to specific classes and seamlessly create non-conflicting schedules for teachers and sections using **Google OR-Tools** (Constraint Programming).

### 3. Advanced User & Role Management
The system supports multiple distinct roles to cater to all stakeholders:
- **Administrators:** Full system control.
- **Teachers:** Manage classes, take attendance, view schedules, and interact with students.
- **Parents & Students:** View academic progress, schedules, and attendance records.

### 4. Attendance & Absence Workflows
- **Student & Teacher Attendance:** Daily tracking of presence and absence.
- **Absence Justifications:** A dedicated workflow for students/parents to submit justifications for absences, which can be reviewed and approved/rejected by the administration.

### 5. Seamless Communication
- **News & Announcements:** Publish important updates to the school community.
- **Complaints & Suggestions:** A dedicated channel for parents and students to voice concerns and provide feedback directly to the administration.
- **Push Notifications:** Real-time mobile alerts powered by Firebase.

### 6. Results & Enrollments
- **Student Enrollments:** Track student progression and enrollment history across academic years.
- **Grades & Results:** Record marks for various subject components and calculate final term and yearly results.

## 📁 Repository Structure

- `/darb-alibda-sms` - The core Laravel application directory.
- `darb-alibda-sms/app/Filament/Resources` - Contains all the Filament resources that power the dashboard (e.g., AbsenceJustifications, Schedules, Users).
- `darb-alibda-sms/database/seeders` - Contains robust seeders to populate the system with a complete dummy school structure (Classes, Sections, Subjects, Students, and Teachers).
- `نظام مدرسة درب الإبداع الخاصة .docx` - The original project requirements and documentation.

## 🛠️ Getting Started

To get the project up and running locally:

1. Navigate to the project directory:
   ```bash
   cd darb-alibda-sms
   ```
2. Install dependencies:
   ```bash
   composer install
   npm install
   ```
3. Setup the environment:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
4. Run migrations and seed the database with initial school data:
   ```bash
   php artisan migrate --seed
   ```
5. Start the development server:
   ```bash
   npm run dev
   # In another terminal
   php artisan serve
   ```

## 📝 License

This project is open-sourced software licensed under the MIT license.
