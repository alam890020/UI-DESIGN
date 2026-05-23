# Smart School Manager

A complete **School Management System** plugin for WordPress with a modern, attractive admin UI.

> Plugin slug: `smart-school-manager` &middot; Version: `1.0.0` &middot; Requires WP `5.8+`, PHP `7.4+`

## Highlights

- 12 fully wired module groups in the WordPress admin sidebar
- 30+ database tables auto-created on activation
- Beautiful gradient cards, dashboards, ID-card previews, wizard, badges
- Generic AJAX CRUD framework — every form auto-persists with one attribute
- Single global settings panel with currency, branding and theme color
- No external runtime dependencies; uses WordPress dashicons + jQuery

## Module Map

| # | Module | Sub-features |
|---|---|---|
| 1 | Core / Admin | Dashboard, Schools, Sessions, Classes & Categories, Settings |
| 2 | General Admin | Students, Admissions, Inquiries, Staff, Roles, Admins, Certificates, ID Cards, Staff ID Cards, Transfer Certificates, Transfer Student, Promote, Unassign Class, Notifications, Staff Attendance, Staff Leaves, Staff Live Classes, Birthdays, Logs, School Settings, Setup Wizard, School Dashboard |
| 3 | Class Management | Classes, Subjects, Attendance, Routines, Staff Timetable, Homework, Study Materials, Notices, Events, Activities, Meetings, Student Leaves, Student Types, Medium, Rating |
| 4 | Examination | Exams, Exam Groups, Results, Admit Cards, Assessment, Academic Report, Bulk Print Results |
| 5 | Accounting | Fees, Invoices, Collect Payments, Payment History, Concession Types, Students Concession, Income, Expenses, Financial Reports |
| 6 | Library | Books, Books Issued, Library Cards |
| 7 | Hostel | Hostels, Rooms |
| 8 | Transport | Routes, Vehicles, Reports |
| 9 | Live Lectures | Online/virtual lecture management |
| 10 | Chapters | Subject chapter organisation |
| 11 | Support Tickets | Internal helpdesk |
| 12 | Print / Export | Invoices, Admit Cards, ID Cards, Results, Certificates, Attendance Sheets, Fee Structures, Timetables, Library Cards |

## Installation

1. Download `smart-school-manager.zip`.
2. In your WordPress admin go to **Plugins -> Add New -> Upload Plugin**, choose the zip, click **Install Now**, then **Activate**.
3. A new **Smart School** menu appears in the sidebar — start with the Setup Wizard or Dashboard.

## Folder structure

```
smart-school-manager/
|-- smart-school-manager.php       Plugin bootstrap
|-- readme.txt                     WordPress.org readme
|-- README.md                      Project README
|-- includes/
|   |-- class-ssm-plugin.php       Wires up everything
|   |-- class-ssm-activator.php    Activation hook (creates tables)
|   |-- class-ssm-deactivator.php  Deactivation hook
|   |-- class-ssm-database.php     dbDelta schema for all modules
|   |-- class-ssm-helper.php       UI helpers (cards, badges, etc.)
|   |-- class-ssm-menu.php         Registers admin menu tree
|   |-- class-ssm-assets.php       Enqueues admin CSS/JS
|   |-- class-ssm-ajax.php         Generic AJAX CRUD
|   |-- class-ssm-router.php       Reserved for REST
|-- admin/views/
|   |-- core/                      Dashboard, Schools, Sessions, ...
|   |-- general/                   Students, Admissions, Staff, ...
|   |-- class/                     Classes, Subjects, Attendance, ...
|   |-- exam/                      Exams, Results, Admit Cards, ...
|   |-- accounting/                Fees, Invoices, Income, ...
|   |-- library/                   Books, Issued, Cards
|   |-- hostel/                    Hostels, Rooms
|   |-- transport/                 Routes, Vehicles, Reports
|   |-- lectures/                  Lectures
|   |-- chapters/                  Chapters
|   |-- tickets/                   Tickets
|   `-- print/                     Print Center
`-- assets/
    |-- css/admin.css              Modern gradient theme
    `-- js/admin.js                AJAX helpers, modals, toasts
```

## Tech notes

- All AJAX writes go through `ssm_save_<entity>` / `ssm_delete_<entity>` actions defined in `SSM_Ajax`.
- Every form just needs `class="ssm-ajax-form" data-entity="student"` plus inputs whose `name` attribute matches the column name.
- Capability check: `manage_options` (Administrator). Wire your own roles in `class-ssm-ajax.php` for finer control.
- All tables are prefixed with `{$wpdb->prefix}ssm_`.

## License

GPLv2 or later.
