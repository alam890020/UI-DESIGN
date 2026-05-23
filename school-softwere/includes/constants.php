<?php
/**
 * All SS_ constants, table names and menu slugs.
 *
 * @package School_Softwere
 */

defined( 'ABSPATH' ) || die();

/* ------------------------------------------------------------------
 * Database version
 * ------------------------------------------------------------------ */
define( 'SS_DB_VERSION', '1.0.0' );

/* ------------------------------------------------------------------
 * Table names
 * ------------------------------------------------------------------ */
global $wpdb;

define( 'SS_TABLE_SCHOOLS',                  $wpdb->prefix . 'ss_schools' );
define( 'SS_TABLE_SETTINGS',                 $wpdb->prefix . 'ss_settings' );
define( 'SS_TABLE_CLASSES',                  $wpdb->prefix . 'ss_classes' );
define( 'SS_TABLE_CATEGORY',                 $wpdb->prefix . 'ss_category' );
define( 'SS_TABLE_SESSIONS',                 $wpdb->prefix . 'ss_sessions' );
define( 'SS_TABLE_SECTIONS',                 $wpdb->prefix . 'ss_sections' );
define( 'SS_TABLE_CLASS_SCHOOL',             $wpdb->prefix . 'ss_class_school' );
define( 'SS_TABLE_STUDENT_RECORDS',          $wpdb->prefix . 'ss_student_records' );
define( 'SS_TABLE_PROMOTIONS',               $wpdb->prefix . 'ss_promotions' );
define( 'SS_TABLE_TRANSFERS',                $wpdb->prefix . 'ss_transfers' );
define( 'SS_TABLE_STAFF',                    $wpdb->prefix . 'ss_staff' );
define( 'SS_TABLE_ROLES',                    $wpdb->prefix . 'ss_roles' );
define( 'SS_TABLE_ADMINS',                   $wpdb->prefix . 'ss_admins' );
define( 'SS_TABLE_SUBJECTS',                 $wpdb->prefix . 'ss_subjects' );
define( 'SS_TABLE_SUBJECT_TYPES',            $wpdb->prefix . 'ss_subject_types' );
define( 'SS_TABLE_ROUTINES',                 $wpdb->prefix . 'ss_routines' );
define( 'SS_TABLE_FEES',                     $wpdb->prefix . 'ss_fees' );
define( 'SS_TABLE_STUDENT_FEES',             $wpdb->prefix . 'ss_student_fees' );
define( 'SS_TABLE_INVOICES',                 $wpdb->prefix . 'ss_invoices' );
define( 'SS_TABLE_PAYMENTS',                 $wpdb->prefix . 'ss_payments' );
define( 'SS_TABLE_PENDING_PAYMENTS',         $wpdb->prefix . 'ss_pending_payments' );
define( 'SS_TABLE_CONCESSION_TYPES',         $wpdb->prefix . 'ss_concession_types' );
define( 'SS_TABLE_CONCESSION_FEE_MAPPINGS',  $wpdb->prefix . 'ss_concession_fee_mappings' );
define( 'SS_TABLE_STUDENT_CONCESSION',       $wpdb->prefix . 'ss_student_concession' );
define( 'SS_TABLE_EXPENSE_CATEGORIES',       $wpdb->prefix . 'ss_expense_categories' );
define( 'SS_TABLE_EXPENSES',                 $wpdb->prefix . 'ss_expenses' );
define( 'SS_TABLE_INCOME_CATEGORIES',        $wpdb->prefix . 'ss_income_categories' );
define( 'SS_TABLE_INCOME',                   $wpdb->prefix . 'ss_income' );
define( 'SS_TABLE_ATTENDANCE',               $wpdb->prefix . 'ss_attendance' );
define( 'SS_TABLE_STAFF_ATTENDANCE',         $wpdb->prefix . 'ss_staff_attendance' );
define( 'SS_TABLE_EXAMS',                    $wpdb->prefix . 'ss_exams' );
define( 'SS_TABLE_EXAMS_GROUP',              $wpdb->prefix . 'ss_exams_group' );
define( 'SS_TABLE_EXAM_PAPERS',              $wpdb->prefix . 'ss_exam_papers' );
define( 'SS_TABLE_EXAM_RESULTS',             $wpdb->prefix . 'ss_exam_results' );
define( 'SS_TABLE_ADMIT_CARDS',              $wpdb->prefix . 'ss_admit_cards' );
define( 'SS_TABLE_NOTICES',                  $wpdb->prefix . 'ss_notices' );
define( 'SS_TABLE_CLASS_SCHOOL_NOTICE',      $wpdb->prefix . 'ss_class_school_notice' );
define( 'SS_TABLE_EVENTS',                   $wpdb->prefix . 'ss_events' );
define( 'SS_TABLE_EVENT_RESPONSES',          $wpdb->prefix . 'ss_event_responses' );
define( 'SS_TABLE_HOMEWORK',                 $wpdb->prefix . 'ss_homework' );
define( 'SS_TABLE_HOMEWORK_SUBMISSION',      $wpdb->prefix . 'ss_homework_submission' );
define( 'SS_TABLE_STUDY_MATERIALS',          $wpdb->prefix . 'ss_study_materials' );
define( 'SS_TABLE_BOOKS',                    $wpdb->prefix . 'ss_books' );
define( 'SS_TABLE_BOOKS_ISSUED',             $wpdb->prefix . 'ss_books_issued' );
define( 'SS_TABLE_LIBRARY_CARDS',            $wpdb->prefix . 'ss_library_cards' );
define( 'SS_TABLE_VEHICLES',                 $wpdb->prefix . 'ss_vehicles' );
define( 'SS_TABLE_ROUTES',                   $wpdb->prefix . 'ss_routes' );
define( 'SS_TABLE_ROUTE_VEHICLE',            $wpdb->prefix . 'ss_route_vehicle' );
define( 'SS_TABLE_HOSTELS',                  $wpdb->prefix . 'ss_hostels' );
define( 'SS_TABLE_ROOMS',                    $wpdb->prefix . 'ss_rooms' );
define( 'SS_TABLE_LEAVES',                   $wpdb->prefix . 'ss_leaves' );
define( 'SS_TABLE_CERTIFICATES',             $wpdb->prefix . 'ss_certificates' );
define( 'SS_TABLE_CERTIFICATE_STUDENT',      $wpdb->prefix . 'ss_certificate_student' );
define( 'SS_TABLE_TRANSFER_CERTIFICATES',    $wpdb->prefix . 'ss_transfer_certificates' );
define( 'SS_TABLE_INQUIRIES',                $wpdb->prefix . 'ss_inquiries' );
define( 'SS_TABLE_CHAPTER',                  $wpdb->prefix . 'ss_chapter' );
define( 'SS_TABLE_LECTURE',                  $wpdb->prefix . 'ss_lecture' );
define( 'SS_TABLE_MEETINGS',                 $wpdb->prefix . 'ss_meetings' );
define( 'SS_TABLE_ACTIVITIES',               $wpdb->prefix . 'ss_activities' );
define( 'SS_TABLE_MEDIUM',                   $wpdb->prefix . 'ss_medium' );
define( 'SS_TABLE_STUDENT_TYPE',             $wpdb->prefix . 'ss_student_type' );
define( 'SS_TABLE_TICKETS',                  $wpdb->prefix . 'ss_tickets' );
define( 'SS_TABLE_TICKET_HISTORY',           $wpdb->prefix . 'ss_ticket_history' );
define( 'SS_TABLE_LOGS',                     $wpdb->prefix . 'ss_logs' );
define( 'SS_TABLE_ACADEMIC_REPORTS',         $wpdb->prefix . 'ss_academic_reports' );
define( 'SS_TABLE_REMINDER',                 $wpdb->prefix . 'ss_reminder' );

/* ------------------------------------------------------------------
 * Menu slugs
 * ------------------------------------------------------------------ */
define( 'SS_MENU_SLUG',            'school-softwere' );
define( 'SS_MENU_DASHBOARD',       'school-softwere' );
define( 'SS_MENU_SCHOOLS',         'school-softwere-schools' );
define( 'SS_MENU_STUDENTS',        'school-softwere-students' );
define( 'SS_MENU_STAFF',           'school-softwere-staff' );
define( 'SS_MENU_CLASSES',         'school-softwere-classes' );
define( 'SS_MENU_SUBJECTS',        'school-softwere-subjects' );
define( 'SS_MENU_SESSIONS',        'school-softwere-sessions' );
define( 'SS_MENU_EXAMS',           'school-softwere-exams' );
define( 'SS_MENU_FEES',            'school-softwere-fees' );
define( 'SS_MENU_ATTENDANCE',      'school-softwere-attendance' );
define( 'SS_MENU_LIBRARY',         'school-softwere-library' );
define( 'SS_MENU_TRANSPORT',       'school-softwere-transport' );
define( 'SS_MENU_HOSTEL',          'school-softwere-hostel' );
define( 'SS_MENU_NOTICES',         'school-softwere-notices' );
define( 'SS_MENU_EVENTS',          'school-softwere-events' );
define( 'SS_MENU_HOMEWORK',        'school-softwere-homework' );
define( 'SS_MENU_LECTURES',        'school-softwere-lectures' );
define( 'SS_MENU_LEAVES',          'school-softwere-leaves' );
define( 'SS_MENU_REPORTS',         'school-softwere-reports' );
define( 'SS_MENU_SETTINGS',        'school-softwere-settings' );
define( 'SS_MENU_LOGS',            'school-softwere-logs' );
define( 'SS_MENU_TICKETS',         'school-softwere-tickets' );
define( 'SS_MENU_WIZARD',          'school-softwere-wizard' );

/* ------------------------------------------------------------------
 * Capabilities
 * ------------------------------------------------------------------ */
define( 'SS_CAP_SUPER_ADMIN',  'ss_super_admin' );
define( 'SS_CAP_SCHOOL_ADMIN', 'ss_school_admin' );
define( 'SS_CAP_MANAGE',       'manage_options' );
