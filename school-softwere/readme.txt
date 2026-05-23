=== School Softwere ===
Contributors: yourname
Tags: school management, student management, fees, attendance, exams, library
Requires at least: 5.8
Tested up to: 6.5
Stable tag: 1.0.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

School Softwere is a powerful WordPress plugin to manage multiple schools — students, staff, exams, fees, attendance, library, transport, hostel, and more.

== Description ==

School Softwere is a comprehensive school management system plugin for WordPress. It provides a beautiful, modern admin interface to manage:

* 🏫 Multiple Schools
* 🎓 Students (enrollment, promotion, transfer, ID cards)
* 👩‍🏫 Staff (roles, permissions, leaves)
* 📚 Classes, Sections & Subjects
* 📝 Examinations & Results
* 💰 Fees, Invoices & Accounting
* ✅ Attendance (student & staff)
* 📖 Library Management
* 🚌 Transport & Routes
* 🏠 Hostel Management
* 📢 Noticeboard & Events
* 📓 Homework & Study Materials
* 🎥 Lectures & Live Classes
* 🎫 Support Tickets
* 📊 Reports & Analytics
* 📱 Student/Parent Frontend Portal

== Installation ==

1. Upload the `school-softwere` folder to `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' screen in WordPress
3. The Setup Wizard will launch automatically
4. Follow the 6-step wizard to configure your school

== Shortcodes ==

* `[ss_login]` — Student/parent login form
* `[ss_noticeboard]` — Display school notices
* `[ss_events]` — Display upcoming events
* `[ss_student_dashboard]` — Full student portal
* `[ss_fee_status]` — Student fee status
* `[ss_results]` — Exam results
* `[ss_attendance]` — Attendance summary
* `[ss_homework]` — Pending homework

== REST API ==

Namespace: `school-softwere/v1`

* `GET /notices?school_id=1`
* `GET /events?school_id=1`
* `GET /student/dashboard` (auth required)
* `GET /student/attendance` (auth required)
* `GET /student/fees` (auth required)
* `GET /student/results` (auth required)

== Changelog ==

= 1.0.0 =
* Initial release
* Full school management system
* 60+ database tables
* Beautiful modern admin UI
* Student/parent portal
* REST API endpoints
