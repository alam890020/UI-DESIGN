# Bundled Vendor Libraries

These third-party libraries are bundled with Smart School Manager so the
plugin works **fully offline** in air-gapped school networks. All libraries
are included with their original licenses preserved.

| Library      | Version | License | Purpose                              |
|--------------|---------|---------|--------------------------------------|
| Chart.js     | 4.4.1 / 3.9.1 | MIT     | Dashboard charts (income, attendance, results) |
| ApexCharts   | 3.45.2  | MIT     | Advanced finance/analytics charts    |
| DataTables   | 1.13.8  | MIT     | Sortable, searchable, paginated admin tables |
| Select2      | 4.1.0-rc| MIT     | Searchable dropdowns (students, classes…) |
| Flatpickr    | 4.6.13  | MIT     | Friendly date/time pickers           |
| SweetAlert2  | 11.10.5 | MIT     | Beautiful confirmation/alert modals  |
| Quill        | 1.3.7   | BSD-3   | Rich-text editor for notices/homework|
| Moment.js    | 2.29.4  | MIT     | Date manipulation (legacy reports)   |
| FullCalendar | 6.1.10  | MIT     | Events & timetable calendar view     |
| jsPDF + html2canvas | 2.5.1 / 1.4.1 | MIT | Client-side PDF generation for ID cards/invoices |
| jsPDF AutoTable | 3.8.0  | MIT  | Table-to-PDF helper                  |
| FontAwesome 6 Free | 6.5.1 | Font Awesome Free | Icon webfont (1500+ icons) |
| Bootstrap    | 5.3.2   | MIT     | Frontend portal/shortcode helpers    |
| Tabler Icons | 2.44.0  | MIT     | 4000+ outline icons for admin UI     |
| Leaflet      | 1.9.4   | BSD-2   | School/transport map view (optional) |

## Loading

These vendor assets are **not** auto-enqueued by the plugin to keep page weight
low. Modules that need them register them on demand using the helper:

```php
SSM_Assets::enqueue_vendor( 'chartjs' );
SSM_Assets::enqueue_vendor( 'datatables' );
SSM_Assets::enqueue_vendor( 'select2' );
```

## Removing libraries

If you don't need a particular library, you can safely delete its folder
under `assets/vendor/` — Smart School Manager will degrade gracefully.
