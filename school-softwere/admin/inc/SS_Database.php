<?php
/**
 * SS_Database — Table creation, sample data, activation / uninstall.
 *
 * @package School_Softwere
 */

defined( 'ABSPATH' ) || die();

class SS_Database {

	/**
	 * Create all plugin tables using dbDelta().
	 */
	public static function create_tables() {
		global $wpdb;
		$charset = $wpdb->get_charset_collate();
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		// ── Schools ──────────────────────────────────────────────────
		dbDelta( "CREATE TABLE {$wpdb->prefix}ss_schools (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			label varchar(200) NOT NULL DEFAULT '',
			phone varchar(30) DEFAULT '',
			email varchar(100) DEFAULT '',
			address text DEFAULT '',
			description text DEFAULT '',
			registration_number varchar(100) DEFAULT '',
			category_id bigint(20) UNSIGNED DEFAULT NULL,
			logo varchar(255) DEFAULT '',
			is_active tinyint(1) NOT NULL DEFAULT 1,
			last_enrollment_count int(11) NOT NULL DEFAULT 0,
			last_invoice_count int(11) NOT NULL DEFAULT 0,
			admission_prefix varchar(20) DEFAULT 'ADM',
			admission_base int(11) NOT NULL DEFAULT 1000,
			admission_padding int(11) NOT NULL DEFAULT 6,
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (ID)
		) $charset ENGINE=InnoDB;" );

		// ── Settings ─────────────────────────────────────────────────
		dbDelta( "CREATE TABLE {$wpdb->prefix}ss_settings (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			school_id bigint(20) UNSIGNED NOT NULL,
			setting_key varchar(100) NOT NULL DEFAULT '',
			setting_value longtext DEFAULT '',
			PRIMARY KEY (ID),
			KEY school_id (school_id)
		) $charset ENGINE=InnoDB;" );

		// ── Category ──────────────────────────────────────────────────
		dbDelta( "CREATE TABLE {$wpdb->prefix}ss_category (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			label varchar(200) NOT NULL DEFAULT '',
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (ID)
		) $charset ENGINE=InnoDB;" );

		// ── Classes ───────────────────────────────────────────────────
		dbDelta( "CREATE TABLE {$wpdb->prefix}ss_classes (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			label varchar(100) NOT NULL DEFAULT '',
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (ID)
		) $charset ENGINE=InnoDB;" );

		// ── Sessions ──────────────────────────────────────────────────
		dbDelta( "CREATE TABLE {$wpdb->prefix}ss_sessions (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			school_id bigint(20) UNSIGNED NOT NULL,
			label varchar(100) NOT NULL DEFAULT '',
			start_date date DEFAULT NULL,
			end_date date DEFAULT NULL,
			is_active tinyint(1) NOT NULL DEFAULT 1,
			PRIMARY KEY (ID),
			KEY school_id (school_id)
		) $charset ENGINE=InnoDB;" );

		// ── Medium ────────────────────────────────────────────────────
		dbDelta( "CREATE TABLE {$wpdb->prefix}ss_medium (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			label varchar(100) NOT NULL DEFAULT '',
			PRIMARY KEY (ID)
		) $charset ENGINE=InnoDB;" );

		// ── Class-School mapping ──────────────────────────────────────
		dbDelta( "CREATE TABLE {$wpdb->prefix}ss_class_school (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			class_id bigint(20) UNSIGNED NOT NULL,
			school_id bigint(20) UNSIGNED NOT NULL,
			session_id bigint(20) UNSIGNED NOT NULL,
			PRIMARY KEY (ID),
			KEY class_id (class_id),
			KEY school_id (school_id),
			KEY session_id (session_id)
		) $charset ENGINE=InnoDB;" );

		// ── Sections ──────────────────────────────────────────────────
		dbDelta( "CREATE TABLE {$wpdb->prefix}ss_sections (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			class_school_id bigint(20) UNSIGNED NOT NULL,
			label varchar(50) NOT NULL DEFAULT '',
			medium_id bigint(20) UNSIGNED DEFAULT NULL,
			capacity int(11) NOT NULL DEFAULT 0,
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (ID),
			KEY class_school_id (class_school_id)
		) $charset ENGINE=InnoDB;" );

		// ── Student Type ──────────────────────────────────────────────
		dbDelta( "CREATE TABLE {$wpdb->prefix}ss_student_type (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			label varchar(100) NOT NULL DEFAULT '',
			PRIMARY KEY (ID)
		) $charset ENGINE=InnoDB;" );


		// ── Student Records ───────────────────────────────────────────
		dbDelta( "CREATE TABLE {$wpdb->prefix}ss_student_records (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			school_id bigint(20) UNSIGNED NOT NULL,
			class_school_id bigint(20) UNSIGNED NOT NULL,
			section_id bigint(20) UNSIGNED NOT NULL,
			user_id bigint(20) UNSIGNED DEFAULT NULL,
			admission_number varchar(50) DEFAULT '',
			roll_number varchar(50) DEFAULT '',
			first_name varchar(100) NOT NULL DEFAULT '',
			last_name varchar(100) DEFAULT '',
			father_name varchar(100) DEFAULT '',
			mother_name varchar(100) DEFAULT '',
			guardian_name varchar(100) DEFAULT '',
			guardian_relation varchar(50) DEFAULT '',
			dob date DEFAULT NULL,
			gender varchar(10) DEFAULT '',
			blood_group varchar(10) DEFAULT '',
			religion varchar(50) DEFAULT '',
			caste varchar(50) DEFAULT '',
			nationality varchar(50) DEFAULT '',
			address text DEFAULT '',
			city varchar(100) DEFAULT '',
			state varchar(100) DEFAULT '',
			zip varchar(20) DEFAULT '',
			country varchar(100) DEFAULT '',
			phone varchar(30) DEFAULT '',
			email varchar(100) DEFAULT '',
			photo varchar(255) DEFAULT '',
			admission_date date DEFAULT NULL,
			student_type_id bigint(20) UNSIGNED DEFAULT NULL,
			is_active tinyint(1) NOT NULL DEFAULT 1,
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (ID),
			KEY school_id (school_id),
			KEY class_school_id (class_school_id),
			KEY section_id (section_id)
		) $charset ENGINE=InnoDB;" );

		// ── Promotions ────────────────────────────────────────────────
		dbDelta( "CREATE TABLE {$wpdb->prefix}ss_promotions (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			student_record_id bigint(20) UNSIGNED NOT NULL,
			from_class_school_id bigint(20) UNSIGNED DEFAULT NULL,
			to_class_school_id bigint(20) UNSIGNED DEFAULT NULL,
			promoted_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (ID),
			KEY student_record_id (student_record_id)
		) $charset ENGINE=InnoDB;" );

		// ── Transfers ─────────────────────────────────────────────────
		dbDelta( "CREATE TABLE {$wpdb->prefix}ss_transfers (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			student_record_id bigint(20) UNSIGNED NOT NULL,
			reason text DEFAULT '',
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (ID),
			KEY student_record_id (student_record_id)
		) $charset ENGINE=InnoDB;" );

		// ── Roles ─────────────────────────────────────────────────────
		dbDelta( "CREATE TABLE {$wpdb->prefix}ss_roles (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			school_id bigint(20) UNSIGNED NOT NULL,
			label varchar(100) NOT NULL DEFAULT '',
			permissions longtext DEFAULT '',
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (ID),
			KEY school_id (school_id)
		) $charset ENGINE=InnoDB;" );

		// ── Staff ─────────────────────────────────────────────────────
		dbDelta( "CREATE TABLE {$wpdb->prefix}ss_staff (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			school_id bigint(20) UNSIGNED NOT NULL,
			user_id bigint(20) UNSIGNED DEFAULT NULL,
			role_id bigint(20) UNSIGNED DEFAULT NULL,
			first_name varchar(100) NOT NULL DEFAULT '',
			last_name varchar(100) DEFAULT '',
			dob date DEFAULT NULL,
			gender varchar(10) DEFAULT '',
			phone varchar(30) DEFAULT '',
			email varchar(100) DEFAULT '',
			address text DEFAULT '',
			photo varchar(255) DEFAULT '',
			joining_date date DEFAULT NULL,
			designation varchar(100) DEFAULT '',
			salary decimal(10,2) NOT NULL DEFAULT 0.00,
			is_active tinyint(1) NOT NULL DEFAULT 1,
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (ID),
			KEY school_id (school_id),
			KEY user_id (user_id)
		) $charset ENGINE=InnoDB;" );

		// ── School Admins ─────────────────────────────────────────────
		dbDelta( "CREATE TABLE {$wpdb->prefix}ss_admins (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			school_id bigint(20) UNSIGNED NOT NULL,
			user_id bigint(20) UNSIGNED NOT NULL,
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (ID),
			KEY school_id (school_id)
		) $charset ENGINE=InnoDB;" );

		// ── Subject Types ─────────────────────────────────────────────
		dbDelta( "CREATE TABLE {$wpdb->prefix}ss_subject_types (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			label varchar(100) NOT NULL DEFAULT '',
			PRIMARY KEY (ID)
		) $charset ENGINE=InnoDB;" );

		// ── Subjects ──────────────────────────────────────────────────
		dbDelta( "CREATE TABLE {$wpdb->prefix}ss_subjects (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			class_school_id bigint(20) UNSIGNED NOT NULL,
			label varchar(100) NOT NULL DEFAULT '',
			subject_type_id bigint(20) UNSIGNED DEFAULT NULL,
			code varchar(30) DEFAULT '',
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (ID),
			KEY class_school_id (class_school_id)
		) $charset ENGINE=InnoDB;" );

		// ── Routines ──────────────────────────────────────────────────
		dbDelta( "CREATE TABLE {$wpdb->prefix}ss_routines (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			class_school_id bigint(20) UNSIGNED NOT NULL,
			section_id bigint(20) UNSIGNED NOT NULL,
			subject_id bigint(20) UNSIGNED NOT NULL,
			staff_id bigint(20) UNSIGNED DEFAULT NULL,
			day varchar(10) NOT NULL DEFAULT '',
			start_time time DEFAULT NULL,
			end_time time DEFAULT NULL,
			PRIMARY KEY (ID),
			KEY class_school_id (class_school_id)
		) $charset ENGINE=InnoDB;" );


		// ── Fees ──────────────────────────────────────────────────────
		dbDelta( "CREATE TABLE {$wpdb->prefix}ss_fees (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			class_school_id bigint(20) UNSIGNED NOT NULL,
			label varchar(200) NOT NULL DEFAULT '',
			amount decimal(10,2) NOT NULL DEFAULT 0.00,
			due_date date DEFAULT NULL,
			is_recurring tinyint(1) NOT NULL DEFAULT 0,
			frequency varchar(20) DEFAULT 'monthly',
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (ID),
			KEY class_school_id (class_school_id)
		) $charset ENGINE=InnoDB;" );

		// ── Student Fees ──────────────────────────────────────────────
		dbDelta( "CREATE TABLE {$wpdb->prefix}ss_student_fees (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			student_record_id bigint(20) UNSIGNED NOT NULL,
			fee_id bigint(20) UNSIGNED NOT NULL,
			amount decimal(10,2) NOT NULL DEFAULT 0.00,
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (ID),
			KEY student_record_id (student_record_id)
		) $charset ENGINE=InnoDB;" );

		// ── Invoices ──────────────────────────────────────────────────
		dbDelta( "CREATE TABLE {$wpdb->prefix}ss_invoices (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			school_id bigint(20) UNSIGNED NOT NULL,
			student_record_id bigint(20) UNSIGNED NOT NULL,
			class_school_id bigint(20) UNSIGNED NOT NULL,
			invoice_number varchar(50) DEFAULT '',
			total_amount decimal(10,2) NOT NULL DEFAULT 0.00,
			paid_amount decimal(10,2) NOT NULL DEFAULT 0.00,
			due_amount decimal(10,2) NOT NULL DEFAULT 0.00,
			status varchar(20) NOT NULL DEFAULT 'unpaid',
			due_date date DEFAULT NULL,
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (ID),
			KEY school_id (school_id),
			KEY student_record_id (student_record_id)
		) $charset ENGINE=InnoDB;" );

		// ── Payments ──────────────────────────────────────────────────
		dbDelta( "CREATE TABLE {$wpdb->prefix}ss_payments (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			invoice_id bigint(20) UNSIGNED NOT NULL,
			amount decimal(10,2) NOT NULL DEFAULT 0.00,
			payment_method varchar(50) DEFAULT 'cash',
			payment_date date DEFAULT NULL,
			note text DEFAULT '',
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (ID),
			KEY invoice_id (invoice_id)
		) $charset ENGINE=InnoDB;" );

		// ── Pending Payments ──────────────────────────────────────────
		dbDelta( "CREATE TABLE {$wpdb->prefix}ss_pending_payments (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			invoice_id bigint(20) UNSIGNED NOT NULL,
			student_record_id bigint(20) UNSIGNED NOT NULL,
			amount decimal(10,2) NOT NULL DEFAULT 0.00,
			reminder_date date DEFAULT NULL,
			PRIMARY KEY (ID)
		) $charset ENGINE=InnoDB;" );

		// ── Concession Types ──────────────────────────────────────────
		dbDelta( "CREATE TABLE {$wpdb->prefix}ss_concession_types (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			school_id bigint(20) UNSIGNED NOT NULL,
			label varchar(200) NOT NULL DEFAULT '',
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (ID)
		) $charset ENGINE=InnoDB;" );

		// ── Concession Fee Mappings ───────────────────────────────────
		dbDelta( "CREATE TABLE {$wpdb->prefix}ss_concession_fee_mappings (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			concession_type_id bigint(20) UNSIGNED NOT NULL,
			fee_id bigint(20) UNSIGNED NOT NULL,
			amount decimal(10,2) NOT NULL DEFAULT 0.00,
			PRIMARY KEY (ID)
		) $charset ENGINE=InnoDB;" );

		// ── Student Concession ────────────────────────────────────────
		dbDelta( "CREATE TABLE {$wpdb->prefix}ss_student_concession (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			student_record_id bigint(20) UNSIGNED NOT NULL,
			concession_type_id bigint(20) UNSIGNED NOT NULL,
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (ID)
		) $charset ENGINE=InnoDB;" );

		// ── Expense Categories ────────────────────────────────────────
		dbDelta( "CREATE TABLE {$wpdb->prefix}ss_expense_categories (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			school_id bigint(20) UNSIGNED NOT NULL,
			label varchar(200) NOT NULL DEFAULT '',
			PRIMARY KEY (ID)
		) $charset ENGINE=InnoDB;" );

		// ── Expenses ──────────────────────────────────────────────────
		dbDelta( "CREATE TABLE {$wpdb->prefix}ss_expenses (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			school_id bigint(20) UNSIGNED NOT NULL,
			expense_category_id bigint(20) UNSIGNED DEFAULT NULL,
			label varchar(200) NOT NULL DEFAULT '',
			amount decimal(10,2) NOT NULL DEFAULT 0.00,
			date date DEFAULT NULL,
			note text DEFAULT '',
			attachment varchar(255) DEFAULT '',
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (ID),
			KEY school_id (school_id)
		) $charset ENGINE=InnoDB;" );

		// ── Income Categories ─────────────────────────────────────────
		dbDelta( "CREATE TABLE {$wpdb->prefix}ss_income_categories (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			school_id bigint(20) UNSIGNED NOT NULL,
			label varchar(200) NOT NULL DEFAULT '',
			PRIMARY KEY (ID)
		) $charset ENGINE=InnoDB;" );

		// ── Income ────────────────────────────────────────────────────
		dbDelta( "CREATE TABLE {$wpdb->prefix}ss_income (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			school_id bigint(20) UNSIGNED NOT NULL,
			income_category_id bigint(20) UNSIGNED DEFAULT NULL,
			label varchar(200) NOT NULL DEFAULT '',
			amount decimal(10,2) NOT NULL DEFAULT 0.00,
			date date DEFAULT NULL,
			note text DEFAULT '',
			attachment varchar(255) DEFAULT '',
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (ID),
			KEY school_id (school_id)
		) $charset ENGINE=InnoDB;" );


		// ── Attendance ────────────────────────────────────────────────
		dbDelta( "CREATE TABLE {$wpdb->prefix}ss_attendance (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			student_record_id bigint(20) UNSIGNED NOT NULL,
			class_school_id bigint(20) UNSIGNED NOT NULL,
			section_id bigint(20) UNSIGNED NOT NULL,
			date date NOT NULL,
			status varchar(20) NOT NULL DEFAULT 'present',
			note text DEFAULT '',
			PRIMARY KEY (ID),
			KEY student_record_id (student_record_id),
			KEY date (date)
		) $charset ENGINE=InnoDB;" );

		// ── Staff Attendance ──────────────────────────────────────────
		dbDelta( "CREATE TABLE {$wpdb->prefix}ss_staff_attendance (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			staff_id bigint(20) UNSIGNED NOT NULL,
			school_id bigint(20) UNSIGNED NOT NULL,
			date date NOT NULL,
			status varchar(20) NOT NULL DEFAULT 'present',
			note text DEFAULT '',
			PRIMARY KEY (ID),
			KEY staff_id (staff_id)
		) $charset ENGINE=InnoDB;" );

		// ── Exam Groups ───────────────────────────────────────────────
		dbDelta( "CREATE TABLE {$wpdb->prefix}ss_exams_group (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			school_id bigint(20) UNSIGNED NOT NULL,
			label varchar(200) NOT NULL DEFAULT '',
			session_id bigint(20) UNSIGNED NOT NULL,
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (ID)
		) $charset ENGINE=InnoDB;" );

		// ── Exams ─────────────────────────────────────────────────────
		dbDelta( "CREATE TABLE {$wpdb->prefix}ss_exams (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			class_school_id bigint(20) UNSIGNED NOT NULL,
			label varchar(200) NOT NULL DEFAULT '',
			session_id bigint(20) UNSIGNED NOT NULL,
			exam_group_id bigint(20) UNSIGNED DEFAULT NULL,
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (ID),
			KEY class_school_id (class_school_id)
		) $charset ENGINE=InnoDB;" );

		// ── Exam Papers ───────────────────────────────────────────────
		dbDelta( "CREATE TABLE {$wpdb->prefix}ss_exam_papers (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			exam_id bigint(20) UNSIGNED NOT NULL,
			subject_id bigint(20) UNSIGNED NOT NULL,
			date date DEFAULT NULL,
			start_time time DEFAULT NULL,
			end_time time DEFAULT NULL,
			total_marks decimal(6,2) NOT NULL DEFAULT 100.00,
			pass_marks decimal(6,2) NOT NULL DEFAULT 40.00,
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (ID),
			KEY exam_id (exam_id)
		) $charset ENGINE=InnoDB;" );

		// ── Exam Results ──────────────────────────────────────────────
		dbDelta( "CREATE TABLE {$wpdb->prefix}ss_exam_results (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			exam_paper_id bigint(20) UNSIGNED NOT NULL,
			student_record_id bigint(20) UNSIGNED NOT NULL,
			obtained_marks decimal(6,2) NOT NULL DEFAULT 0.00,
			grade varchar(10) DEFAULT '',
			remarks text DEFAULT '',
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (ID),
			KEY exam_paper_id (exam_paper_id),
			KEY student_record_id (student_record_id)
		) $charset ENGINE=InnoDB;" );

		// ── Admit Cards ───────────────────────────────────────────────
		dbDelta( "CREATE TABLE {$wpdb->prefix}ss_admit_cards (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			exam_id bigint(20) UNSIGNED NOT NULL,
			student_record_id bigint(20) UNSIGNED NOT NULL,
			admit_card_number varchar(50) DEFAULT '',
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (ID)
		) $charset ENGINE=InnoDB;" );

		// ── Notices ───────────────────────────────────────────────────
		dbDelta( "CREATE TABLE {$wpdb->prefix}ss_notices (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			school_id bigint(20) UNSIGNED NOT NULL,
			title varchar(255) NOT NULL DEFAULT '',
			description longtext DEFAULT '',
			date date DEFAULT NULL,
			attachment varchar(255) DEFAULT '',
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (ID),
			KEY school_id (school_id)
		) $charset ENGINE=InnoDB;" );

		// ── Class-School Notice mapping ───────────────────────────────
		dbDelta( "CREATE TABLE {$wpdb->prefix}ss_class_school_notice (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			class_school_id bigint(20) UNSIGNED NOT NULL,
			notice_id bigint(20) UNSIGNED NOT NULL,
			PRIMARY KEY (ID)
		) $charset ENGINE=InnoDB;" );

		// ── Events ────────────────────────────────────────────────────
		dbDelta( "CREATE TABLE {$wpdb->prefix}ss_events (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			school_id bigint(20) UNSIGNED NOT NULL,
			title varchar(255) NOT NULL DEFAULT '',
			description longtext DEFAULT '',
			start_date date DEFAULT NULL,
			end_date date DEFAULT NULL,
			venue varchar(255) DEFAULT '',
			attachment varchar(255) DEFAULT '',
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (ID),
			KEY school_id (school_id)
		) $charset ENGINE=InnoDB;" );

		// ── Event Responses ───────────────────────────────────────────
		dbDelta( "CREATE TABLE {$wpdb->prefix}ss_event_responses (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			event_id bigint(20) UNSIGNED NOT NULL,
			student_record_id bigint(20) UNSIGNED NOT NULL,
			response varchar(50) DEFAULT '',
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (ID)
		) $charset ENGINE=InnoDB;" );


		// ── Homework ──────────────────────────────────────────────────
		dbDelta( "CREATE TABLE {$wpdb->prefix}ss_homework (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			class_school_id bigint(20) UNSIGNED NOT NULL,
			section_id bigint(20) UNSIGNED NOT NULL,
			subject_id bigint(20) UNSIGNED NOT NULL,
			staff_id bigint(20) UNSIGNED DEFAULT NULL,
			title varchar(255) NOT NULL DEFAULT '',
			description longtext DEFAULT '',
			submission_date date DEFAULT NULL,
			attachment varchar(255) DEFAULT '',
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (ID),
			KEY class_school_id (class_school_id)
		) $charset ENGINE=InnoDB;" );

		// ── Homework Submissions ──────────────────────────────────────
		dbDelta( "CREATE TABLE {$wpdb->prefix}ss_homework_submission (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			homework_id bigint(20) UNSIGNED NOT NULL,
			student_record_id bigint(20) UNSIGNED NOT NULL,
			file varchar(255) DEFAULT '',
			note text DEFAULT '',
			submitted_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (ID)
		) $charset ENGINE=InnoDB;" );

		// ── Study Materials ───────────────────────────────────────────
		dbDelta( "CREATE TABLE {$wpdb->prefix}ss_study_materials (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			class_school_id bigint(20) UNSIGNED NOT NULL,
			section_id bigint(20) UNSIGNED DEFAULT NULL,
			subject_id bigint(20) UNSIGNED DEFAULT NULL,
			title varchar(255) NOT NULL DEFAULT '',
			description text DEFAULT '',
			file_type varchar(30) DEFAULT '',
			file varchar(255) DEFAULT '',
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (ID)
		) $charset ENGINE=InnoDB;" );

		// ── Books ─────────────────────────────────────────────────────
		dbDelta( "CREATE TABLE {$wpdb->prefix}ss_books (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			school_id bigint(20) UNSIGNED NOT NULL,
			title varchar(255) NOT NULL DEFAULT '',
			author varchar(200) DEFAULT '',
			isbn varchar(50) DEFAULT '',
			publisher varchar(200) DEFAULT '',
			edition varchar(50) DEFAULT '',
			quantity int(11) NOT NULL DEFAULT 0,
			available int(11) NOT NULL DEFAULT 0,
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (ID),
			KEY school_id (school_id)
		) $charset ENGINE=InnoDB;" );

		// ── Books Issued ──────────────────────────────────────────────
		dbDelta( "CREATE TABLE {$wpdb->prefix}ss_books_issued (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			book_id bigint(20) UNSIGNED NOT NULL,
			student_record_id bigint(20) UNSIGNED NOT NULL,
			issue_date date DEFAULT NULL,
			return_date date DEFAULT NULL,
			returned_at date DEFAULT NULL,
			fine decimal(8,2) NOT NULL DEFAULT 0.00,
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (ID)
		) $charset ENGINE=InnoDB;" );

		// ── Library Cards ─────────────────────────────────────────────
		dbDelta( "CREATE TABLE {$wpdb->prefix}ss_library_cards (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			student_record_id bigint(20) UNSIGNED NOT NULL,
			card_number varchar(50) DEFAULT '',
			issued_date date DEFAULT NULL,
			expiry_date date DEFAULT NULL,
			PRIMARY KEY (ID)
		) $charset ENGINE=InnoDB;" );

		// ── Vehicles ──────────────────────────────────────────────────
		dbDelta( "CREATE TABLE {$wpdb->prefix}ss_vehicles (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			school_id bigint(20) UNSIGNED NOT NULL,
			number varchar(50) NOT NULL DEFAULT '',
			model varchar(100) DEFAULT '',
			capacity int(11) NOT NULL DEFAULT 0,
			driver_name varchar(100) DEFAULT '',
			driver_phone varchar(30) DEFAULT '',
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (ID)
		) $charset ENGINE=InnoDB;" );

		// ── Routes ────────────────────────────────────────────────────
		dbDelta( "CREATE TABLE {$wpdb->prefix}ss_routes (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			school_id bigint(20) UNSIGNED NOT NULL,
			label varchar(200) NOT NULL DEFAULT '',
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (ID)
		) $charset ENGINE=InnoDB;" );

		// ── Route-Vehicle mapping ─────────────────────────────────────
		dbDelta( "CREATE TABLE {$wpdb->prefix}ss_route_vehicle (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			route_id bigint(20) UNSIGNED NOT NULL,
			vehicle_id bigint(20) UNSIGNED NOT NULL,
			stop_name varchar(200) DEFAULT '',
			stop_time time DEFAULT NULL,
			fee decimal(8,2) NOT NULL DEFAULT 0.00,
			PRIMARY KEY (ID)
		) $charset ENGINE=InnoDB;" );

		// ── Hostels ───────────────────────────────────────────────────
		dbDelta( "CREATE TABLE {$wpdb->prefix}ss_hostels (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			school_id bigint(20) UNSIGNED NOT NULL,
			label varchar(200) NOT NULL DEFAULT '',
			type varchar(20) NOT NULL DEFAULT 'boys',
			warden_name varchar(100) DEFAULT '',
			capacity int(11) NOT NULL DEFAULT 0,
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (ID)
		) $charset ENGINE=InnoDB;" );

		// ── Rooms ─────────────────────────────────────────────────────
		dbDelta( "CREATE TABLE {$wpdb->prefix}ss_rooms (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			hostel_id bigint(20) UNSIGNED NOT NULL,
			room_number varchar(20) NOT NULL DEFAULT '',
			capacity int(11) NOT NULL DEFAULT 0,
			room_type varchar(50) DEFAULT '',
			fee decimal(8,2) NOT NULL DEFAULT 0.00,
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (ID),
			KEY hostel_id (hostel_id)
		) $charset ENGINE=InnoDB;" );


		// ── Leaves ────────────────────────────────────────────────────
		dbDelta( "CREATE TABLE {$wpdb->prefix}ss_leaves (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			staff_id bigint(20) UNSIGNED NOT NULL,
			school_id bigint(20) UNSIGNED NOT NULL,
			leave_type varchar(50) DEFAULT '',
			from_date date DEFAULT NULL,
			to_date date DEFAULT NULL,
			reason text DEFAULT '',
			status varchar(20) NOT NULL DEFAULT 'pending',
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (ID),
			KEY staff_id (staff_id)
		) $charset ENGINE=InnoDB;" );

		// ── Certificates ──────────────────────────────────────────────
		dbDelta( "CREATE TABLE {$wpdb->prefix}ss_certificates (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			school_id bigint(20) UNSIGNED NOT NULL,
			label varchar(200) NOT NULL DEFAULT '',
			content_template longtext DEFAULT '',
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (ID)
		) $charset ENGINE=InnoDB;" );

		// ── Certificate-Student mapping ───────────────────────────────
		dbDelta( "CREATE TABLE {$wpdb->prefix}ss_certificate_student (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			certificate_id bigint(20) UNSIGNED NOT NULL,
			student_record_id bigint(20) UNSIGNED NOT NULL,
			issued_date date DEFAULT NULL,
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (ID)
		) $charset ENGINE=InnoDB;" );

		// ── Transfer Certificates ─────────────────────────────────────
		dbDelta( "CREATE TABLE {$wpdb->prefix}ss_transfer_certificates (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			student_record_id bigint(20) UNSIGNED NOT NULL,
			reason text DEFAULT '',
			issued_date date DEFAULT NULL,
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (ID)
		) $charset ENGINE=InnoDB;" );

		// ── Inquiries ─────────────────────────────────────────────────
		dbDelta( "CREATE TABLE {$wpdb->prefix}ss_inquiries (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			school_id bigint(20) UNSIGNED NOT NULL,
			name varchar(100) NOT NULL DEFAULT '',
			email varchar(100) DEFAULT '',
			phone varchar(30) DEFAULT '',
			message text DEFAULT '',
			source varchar(50) DEFAULT '',
			status varchar(30) DEFAULT 'new',
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (ID)
		) $charset ENGINE=InnoDB;" );

		// ── Chapters ──────────────────────────────────────────────────
		dbDelta( "CREATE TABLE {$wpdb->prefix}ss_chapter (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			subject_id bigint(20) UNSIGNED NOT NULL,
			class_school_id bigint(20) UNSIGNED NOT NULL,
			label varchar(255) NOT NULL DEFAULT '',
			description text DEFAULT '',
			`order` int(11) NOT NULL DEFAULT 0,
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (ID)
		) $charset ENGINE=InnoDB;" );

		// ── Lectures ──────────────────────────────────────────────────
		dbDelta( "CREATE TABLE {$wpdb->prefix}ss_lecture (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			chapter_id bigint(20) UNSIGNED NOT NULL,
			staff_id bigint(20) UNSIGNED DEFAULT NULL,
			title varchar(255) NOT NULL DEFAULT '',
			description text DEFAULT '',
			video_url varchar(500) DEFAULT '',
			attachment varchar(255) DEFAULT '',
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (ID)
		) $charset ENGINE=InnoDB;" );

		// ── Meetings ──────────────────────────────────────────────────
		dbDelta( "CREATE TABLE {$wpdb->prefix}ss_meetings (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			school_id bigint(20) UNSIGNED NOT NULL,
			class_school_id bigint(20) UNSIGNED DEFAULT NULL,
			section_id bigint(20) UNSIGNED DEFAULT NULL,
			title varchar(255) NOT NULL DEFAULT '',
			meeting_link varchar(500) DEFAULT '',
			start_time datetime DEFAULT NULL,
			duration int(11) NOT NULL DEFAULT 60,
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (ID)
		) $charset ENGINE=InnoDB;" );

		// ── Activities ────────────────────────────────────────────────
		dbDelta( "CREATE TABLE {$wpdb->prefix}ss_activities (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			class_school_id bigint(20) UNSIGNED NOT NULL,
			section_id bigint(20) UNSIGNED DEFAULT NULL,
			title varchar(255) NOT NULL DEFAULT '',
			description text DEFAULT '',
			date date DEFAULT NULL,
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (ID)
		) $charset ENGINE=InnoDB;" );

		// ── Support Tickets ───────────────────────────────────────────
		dbDelta( "CREATE TABLE {$wpdb->prefix}ss_tickets (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			school_id bigint(20) UNSIGNED NOT NULL,
			user_id bigint(20) UNSIGNED NOT NULL,
			subject varchar(255) NOT NULL DEFAULT '',
			status varchar(20) NOT NULL DEFAULT 'open',
			priority varchar(20) NOT NULL DEFAULT 'normal',
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (ID)
		) $charset ENGINE=InnoDB;" );

		// ── Ticket History ────────────────────────────────────────────
		dbDelta( "CREATE TABLE {$wpdb->prefix}ss_ticket_history (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			ticket_id bigint(20) UNSIGNED NOT NULL,
			user_id bigint(20) UNSIGNED NOT NULL,
			message longtext DEFAULT '',
			attachment varchar(255) DEFAULT '',
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (ID)
		) $charset ENGINE=InnoDB;" );

		// ── Logs ──────────────────────────────────────────────────────
		dbDelta( "CREATE TABLE {$wpdb->prefix}ss_logs (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			school_id bigint(20) UNSIGNED DEFAULT NULL,
			user_id bigint(20) UNSIGNED DEFAULT NULL,
			action varchar(100) NOT NULL DEFAULT '',
			details longtext DEFAULT '',
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (ID),
			KEY school_id (school_id)
		) $charset ENGINE=InnoDB;" );

		// ── Academic Reports ──────────────────────────────────────────
		dbDelta( "CREATE TABLE {$wpdb->prefix}ss_academic_reports (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			student_record_id bigint(20) UNSIGNED NOT NULL,
			class_school_id bigint(20) UNSIGNED NOT NULL,
			session_id bigint(20) UNSIGNED NOT NULL,
			report_data longtext DEFAULT '',
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (ID)
		) $charset ENGINE=InnoDB;" );

		// ── Reminders ─────────────────────────────────────────────────
		dbDelta( "CREATE TABLE {$wpdb->prefix}ss_reminder (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			school_id bigint(20) UNSIGNED NOT NULL,
			student_record_id bigint(20) UNSIGNED DEFAULT NULL,
			type varchar(50) DEFAULT '',
			message text DEFAULT '',
			send_date datetime DEFAULT NULL,
			is_sent tinyint(1) NOT NULL DEFAULT 0,
			PRIMARY KEY (ID)
		) $charset ENGINE=InnoDB;" );

		update_option( 'ss_db_version', SS_DB_VERSION );
	}


	/**
	 * Insert default / sample data after first activation.
	 */
	public static function insert_sample_data() {
		global $wpdb;

		// Only run once
		if ( get_option( 'ss_sample_data_inserted' ) ) {
			return;
		}

		// ── Default school category
		$wpdb->insert( $wpdb->prefix . 'ss_category', array( 'label' => 'Public School', 'created_at' => current_time( 'mysql' ) ) );
		$cat_id = $wpdb->insert_id;

		// ── Sample school
		$wpdb->insert( $wpdb->prefix . 'ss_schools', array(
			'label'               => 'Sample School',
			'phone'               => '000-000-0000',
			'email'               => 'info@sampleschool.com',
			'address'             => '123 School Lane, City',
			'registration_number' => 'REG-001',
			'category_id'         => $cat_id,
			'is_active'           => 1,
			'admission_prefix'    => 'ADM',
			'admission_base'      => 1000,
			'admission_padding'   => 6,
			'created_at'          => current_time( 'mysql' ),
		) );
		$school_id = $wpdb->insert_id;

		// ── Default session
		$year      = (int) date( 'Y' );
		$next_year = $year + 1;
		$wpdb->insert( $wpdb->prefix . 'ss_sessions', array(
			'school_id'  => $school_id,
			'label'      => $year . '-' . $next_year,
			'start_date' => $year . '-04-01',
			'end_date'   => $next_year . '-03-31',
			'is_active'  => 1,
		) );
		$session_id = $wpdb->insert_id;

		// ── Default classes
		$classes = array( 'Nursery', 'KG', 'Class 1', 'Class 2', 'Class 3', 'Class 4', 'Class 5', 'Class 6', 'Class 7', 'Class 8', 'Class 9', 'Class 10', 'Class 11', 'Class 12' );
		foreach ( $classes as $class_label ) {
			$wpdb->insert( $wpdb->prefix . 'ss_classes', array(
				'label'      => $class_label,
				'created_at' => current_time( 'mysql' ),
			) );
			$class_id = $wpdb->insert_id;

			// Attach class to school+session
			$wpdb->insert( $wpdb->prefix . 'ss_class_school', array(
				'class_id'   => $class_id,
				'school_id'  => $school_id,
				'session_id' => $session_id,
			) );
			$cs_id = $wpdb->insert_id;

			// Default section A
			$wpdb->insert( $wpdb->prefix . 'ss_sections', array(
				'class_school_id' => $cs_id,
				'label'           => 'A',
				'capacity'        => 40,
				'created_at'      => current_time( 'mysql' ),
			) );
		}

		// ── Subject types
		foreach ( array( 'Theory', 'Practical' ) as $type ) {
			$wpdb->insert( $wpdb->prefix . 'ss_subject_types', array( 'label' => $type ) );
		}

		// ── Medium
		foreach ( array( 'English', 'Hindi', 'Urdu' ) as $medium ) {
			$wpdb->insert( $wpdb->prefix . 'ss_medium', array( 'label' => $medium ) );
		}

		// ── Student types
		foreach ( array( 'Regular', 'Transfer', 'International' ) as $type ) {
			$wpdb->insert( $wpdb->prefix . 'ss_student_type', array( 'label' => $type ) );
		}

		// ── Expense categories
		foreach ( array( 'Salary', 'Utilities', 'Maintenance', 'Stationery' ) as $cat ) {
			$wpdb->insert( $wpdb->prefix . 'ss_expense_categories', array( 'school_id' => $school_id, 'label' => $cat ) );
		}

		// ── Income categories
		foreach ( array( 'Donations', 'Grants', 'Other' ) as $cat ) {
			$wpdb->insert( $wpdb->prefix . 'ss_income_categories', array( 'school_id' => $school_id, 'label' => $cat ) );
		}

		// ── Default settings
		$defaults = array(
			'currency_symbol'    => '$',
			'date_format'        => 'd/m/Y',
			'school_name'        => 'Sample School',
			'academic_year_month'=> '4',
		);
		foreach ( $defaults as $key => $value ) {
			$wpdb->insert( $wpdb->prefix . 'ss_settings', array(
				'school_id'    => $school_id,
				'setting_key'  => $key,
				'setting_value'=> $value,
			) );
		}

		update_option( 'ss_sample_data_inserted', true );
		update_option( 'ss_default_school_id', $school_id );
	}

	/**
	 * Write an activity log entry.
	 *
	 * @param string $action   Short action label.
	 * @param string $details  Full details.
	 * @param int    $school_id Optional.
	 */
	public static function log( $action, $details = '', $school_id = 0 ) {
		global $wpdb;
		$wpdb->insert( $wpdb->prefix . 'ss_logs', array(
			'school_id'  => $school_id ? (int) $school_id : null,
			'user_id'    => get_current_user_id(),
			'action'     => sanitize_text_field( $action ),
			'details'    => wp_kses_post( $details ),
			'created_at' => current_time( 'mysql' ),
		) );
	}
}
