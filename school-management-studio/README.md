# School Management Studio

A **brand-new look** WordPress plugin for school management. Sister plugin to Smart School Manager, with the same comprehensive feature set but a totally fresh UI: sidebar nav, glassmorphism cards, dark mode, inline SVG icons, soft pastel palette.

> Plugin slug: `school-management-studio` &middot; Version: `1.0.0` &middot; Requires WP `5.8+`, PHP `7.4+`

## ✨ What's new in the look

| Aspect | Smart School Manager (sister) | **School Management Studio** |
|---|---|---|
| Layout | Top-bar + content | **Sidebar + content** |
| Theme | Indigo / cyan gradient | **Violet / cyan + pastels** |
| Mode | Light only | **Light + Dark** (toggle in topbar, persisted) |
| Icons | Dashicons | **Inline SVG** (Tabler-style, ~40 icons) |
| Cards | Border + shadow | **Glassmorphism** (frosted blur + soft shadow) |
| Stats | Gradient solid cards | **Tone-bordered glass cards** |
| Buttons | Rounded | **Squircle (16-22px radius)** |
| Form | Standard | **Tonal focus glow** |
| Tables | WP-default styled | **Pillows + status pills** |

## 🔒 Genuine, virus-free

- **No bundled minified third-party JS/CSS.** Every line of code is hand-written and human-readable.
- **No font files.** All icons are inline SVG paths defined in `class-sms-icons.php`.
- **Chart.js (optional)** loads from official jsDelivr CDN on chart-heavy pages only. Disable via `Settings → Load Charts CDN: No` for fully-offline mode.
- Will not trigger any antivirus false positive — there is nothing minified, obfuscated, or encoded.

## Module map

| Group | Items |
|---|---|
| 🎨 Studio | Dashboard, Schools, Sessions, Classes & Categories, Settings |
| 👥 Students | All Students, New Student Wizard, Admissions, Inquiries, ID Cards, Certificates, Transfer Certs, Promote, Birthdays |
| 💼 Staff | Directory, Staff ID Cards, Attendance, Leaves, Roles |
| 🎓 Academic | Subjects, Attendance, Timetables, Homework, Study Materials, Notices, Events, Meetings |
| 📝 Examinations | Exams, Exam Groups, Results, Admit Cards |
| 💰 Accounting | Fees, Monthly Generator, Invoices, Print Center, Collect Payments, History, Concessions, Income, Expenses, Reports |
| 📚 Library | Books, Issued, Library Cards |
| 🏠 Hostel | Hostels, Rooms |
| 🚌 Transport | Routes, Vehicles |
| 📡 More | Lectures, Tickets, Notifications, Logs, Print Center |

## Frontend shortcodes

```
[sms_student_form]      Full student registration form
[sms_admission_form]    Compact admission application
[sms_inquiry_form]      General inquiry form
[sms_notices limit=10]  Public notice board
[sms_events  limit=10]  Public events list
```

## Quick installation

1. Download `school-management-studio.zip`.
2. WP Admin → Plugins → Add New → Upload Plugin → choose the zip → **Install Now** → **Activate**.
3. Click the new **Studio** menu item in the sidebar.

## License

GPLv2 or later.
