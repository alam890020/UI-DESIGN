=== Smart School Manager ===
Contributors: ui-design
Tags: school, education, students, fees, lms, admissions, library, hostel, transport
Requires at least: 5.8
Tested up to: 6.5
Requires PHP: 7.4
Stable tag: 1.0.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A complete School Management System for WordPress with an attractive, modern admin UI.

== Description ==

Smart School Manager turns your WordPress site into a full-featured school ERP. Manage students, staff, classes, exams, fees, library, hostel, transport, live lectures, support tickets and more — all from one beautiful dashboard.

= Modules =

* Core / Admin Manager (Schools, Sessions, Classes & Categories, Settings, Dashboard)
* General Admin (Students, Admissions, Staff, Roles, Admins, Certificates, ID Cards, TC, Transfer/Promote, Inquiries, Notifications, Staff Attendance, Staff Leaves, Staff Live Classes, Student Birthdays, Logs, Setup Wizard, Unassign Class)
* Class Management (Classes, Subjects, Attendance, Routines/Timetable, Staff Timetable, Homework, Study Materials, Notices, Events, Activities, Meetings, Student Leaves, Student Types, Medium, Rating)
* Examination (Exams, Exam Groups, Results, Admit Cards, Assessment, Academic Report, Bulk Print Results)
* Accounting (Fees, Invoices, Collect Payments, Payment History, Concession Types, Students Concession, Income, Expenses, Reports)
* Library (Books, Books Issued, Library Cards)
* Hostel (Hostels, Rooms)
* Transport (Routes, Vehicles, Reports)
* Live Lectures
* Chapters
* Support Tickets
* Print / Export Center

== Installation ==

1. Upload the `smart-school-manager` folder to the `/wp-content/plugins/` directory, or upload the zip via Plugins -> Add New -> Upload.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Navigate to "Smart School" in your WordPress admin sidebar to start configuring.

== Changelog ==

= 1.0.1 =
* Clean / genuine release: removed all bundled third-party JavaScript libraries that occasionally trigger antivirus false positives on minified code.
* Chart.js (used on dashboard and finance pages) is now loaded from the official jsDelivr CDN at runtime when needed.
* Added a `load_cdn_libs` setting to disable external CDN loads entirely on air-gapped installations.
* No virus, no malware, no obfuscated code — every file is plain, hand-written PHP/CSS/JS that you can review.

= 1.0.0 =
* Initial release with full module set across 12 areas.
