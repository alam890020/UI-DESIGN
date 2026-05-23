<?php
/**
 * GMA_Database — Table creation and activation.
 *
 * @package GMA_School
 */
defined( 'ABSPATH' ) || exit;

class GMA_Database {

	public static function activate() {
		self::create_tables();
		self::insert_defaults();
		update_option( 'gma_db_version', GMA_VERSION );
		update_option( 'gma_activation_redirect', 1 );
	}

	public static function deactivate() {
		// Nothing destructive on deactivate
	}

	public static function log( $action, $details = '', $school_id = 0 ) {
		global $wpdb;
		$wpdb->insert( GMA_TABLE_LOGS, array(
			'school_id'  => $school_id ?: GMA_Helper::get_current_school_id(),
			'user_id'    => get_current_user_id(),
			'action'     => sanitize_text_field( $action ),
			'details'    => sanitize_textarea_field( $details ),
			'ip'         => sanitize_text_field( $_SERVER['REMOTE_ADDR'] ?? '' ),
			'created_at' => current_time( 'mysql' ),
		) );
	}

	public static function create_tables() {
		global $wpdb;
		$c = $wpdb->get_charset_collate();
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		self::run_schools( $c );
		self::run_people( $c );
		self::run_academic( $c );
		self::run_exams( $c );
		self::run_accounting( $c );
		self::run_library( $c );
		self::run_hostel( $c );
		self::run_transport( $c );
		self::run_comms( $c );
		self::run_misc( $c );
	}


	private static function run_schools( $c ) {
		global $wpdb;
		dbDelta( "CREATE TABLE " . GMA_TABLE_SCHOOLS . " (
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
		) $c ENGINE=InnoDB;" );

		dbDelta( "CREATE TABLE " . GMA_TABLE_SETTINGS . " (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			school_id bigint(20) UNSIGNED NOT NULL,
			setting_key varchar(100) NOT NULL DEFAULT '',
			setting_value longtext DEFAULT '',
			PRIMARY KEY (ID),
			KEY school_id (school_id)
		) $c ENGINE=InnoDB;" );

		dbDelta( "CREATE TABLE " . GMA_TABLE_CATEGORY . " (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			label varchar(200) NOT NULL DEFAULT '',
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (ID)
		) $c ENGINE=InnoDB;" );

		dbDelta( "CREATE TABLE " . GMA_TABLE_CLASSES . " (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			label varchar(100) NOT NULL DEFAULT '',
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (ID)
		) $c ENGINE=InnoDB;" );

		dbDelta( "CREATE TABLE " . GMA_TABLE_SESSIONS . " (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			school_id bigint(20) UNSIGNED NOT NULL,
			label varchar(100) NOT NULL DEFAULT '',
			start_date date DEFAULT NULL,
			end_date date DEFAULT NULL,
			is_active tinyint(1) NOT NULL DEFAULT 1,
			PRIMARY KEY (ID),
			KEY school_id (school_id)
		) $c ENGINE=InnoDB;" );

		dbDelta( "CREATE TABLE " . GMA_TABLE_MEDIUM . " (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			label varchar(100) NOT NULL DEFAULT '',
			PRIMARY KEY (ID)
		) $c ENGINE=InnoDB;" );

		dbDelta( "CREATE TABLE " . GMA_TABLE_CLASS_SCHOOL . " (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			class_id bigint(20) UNSIGNED NOT NULL,
			school_id bigint(20) UNSIGNED NOT NULL,
			session_id bigint(20) UNSIGNED NOT NULL,
			PRIMARY KEY (ID),
			KEY school_id (school_id)
		) $c ENGINE=InnoDB;" );

		dbDelta( "CREATE TABLE " . GMA_TABLE_SECTIONS . " (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			class_school_id bigint(20) UNSIGNED NOT NULL,
			label varchar(50) NOT NULL DEFAULT '',
			medium_id bigint(20) UNSIGNED DEFAULT NULL,
			capacity int(11) NOT NULL DEFAULT 0,
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (ID),
			KEY class_school_id (class_school_id)
		) $c ENGINE=InnoDB;" );
	}


	private static function run_people( $c ) {
		global $wpdb;
		dbDelta( "CREATE TABLE " . GMA_TABLE_STUDENT_TYPE . " (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			label varchar(100) NOT NULL DEFAULT '',
			PRIMARY KEY (ID)
		) $c ENGINE=InnoDB;" );

		dbDelta( "CREATE TABLE " . GMA_TABLE_STUDENT_RECORDS . " (
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
			KEY school_id (school_id)
		) $c ENGINE=InnoDB;" );

		dbDelta( "CREATE TABLE " . GMA_TABLE_ADMISSIONS . " (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			school_id bigint(20) UNSIGNED NOT NULL,
			first_name varchar(100) NOT NULL DEFAULT '',
			last_name varchar(100) DEFAULT '',
			father_name varchar(100) DEFAULT '',
			dob date DEFAULT NULL,
			gender varchar(10) DEFAULT '',
			phone varchar(30) DEFAULT '',
			email varchar(100) DEFAULT '',
			class_id bigint(20) UNSIGNED DEFAULT NULL,
			address text DEFAULT '',
			photo varchar(255) DEFAULT '',
			documents text DEFAULT '',
			status varchar(30) NOT NULL DEFAULT 'pending',
			note text DEFAULT '',
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (ID),
			KEY school_id (school_id)
		) $c ENGINE=InnoDB;" );

		dbDelta( "CREATE TABLE " . GMA_TABLE_PROMOTIONS . " (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			student_record_id bigint(20) UNSIGNED NOT NULL,
			from_class_school_id bigint(20) UNSIGNED DEFAULT NULL,
			to_class_school_id bigint(20) UNSIGNED DEFAULT NULL,
			promoted_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (ID)
		) $c ENGINE=InnoDB;" );

		dbDelta( "CREATE TABLE " . GMA_TABLE_TRANSFERS . " (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			student_record_id bigint(20) UNSIGNED NOT NULL,
			reason text DEFAULT '',
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (ID)
		) $c ENGINE=InnoDB;" );

		dbDelta( "CREATE TABLE " . GMA_TABLE_ROLES . " (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			school_id bigint(20) UNSIGNED NOT NULL,
			label varchar(100) NOT NULL DEFAULT '',
			permissions longtext DEFAULT '',
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (ID)
		) $c ENGINE=InnoDB;" );

		dbDelta( "CREATE TABLE " . GMA_TABLE_STAFF . " (
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
			qualification varchar(200) DEFAULT '',
			salary decimal(10,2) NOT NULL DEFAULT 0.00,
			is_active tinyint(1) NOT NULL DEFAULT 1,
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (ID),
			KEY school_id (school_id)
		) $c ENGINE=InnoDB;" );

		dbDelta( "CREATE TABLE " . GMA_TABLE_ADMINS . " (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			school_id bigint(20) UNSIGNED NOT NULL,
			user_id bigint(20) UNSIGNED NOT NULL,
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (ID)
		) $c ENGINE=InnoDB;" );
	}


	private static function run_academic( $c ) {
		global $wpdb;
		dbDelta( "CREATE TABLE " . GMA_TABLE_SUBJECT_TYPES . " (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			label varchar(100) NOT NULL DEFAULT '',
			PRIMARY KEY (ID)
		) $c ENGINE=InnoDB;" );

		dbDelta( "CREATE TABLE " . GMA_TABLE_SUBJECTS . " (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			class_school_id bigint(20) UNSIGNED NOT NULL,
			label varchar(100) NOT NULL DEFAULT '',
			subject_type_id bigint(20) UNSIGNED DEFAULT NULL,
			code varchar(30) DEFAULT '',
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (ID),
			KEY class_school_id (class_school_id)
		) $c ENGINE=InnoDB;" );

		dbDelta( "CREATE TABLE " . GMA_TABLE_ROUTINES . " (
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
		) $c ENGINE=InnoDB;" );

		dbDelta( "CREATE TABLE " . GMA_TABLE_ATTENDANCE . " (
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
		) $c ENGINE=InnoDB;" );

		dbDelta( "CREATE TABLE " . GMA_TABLE_STAFF_ATTENDANCE . " (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			staff_id bigint(20) UNSIGNED NOT NULL,
			school_id bigint(20) UNSIGNED NOT NULL,
			date date NOT NULL,
			status varchar(20) NOT NULL DEFAULT 'present',
			note text DEFAULT '',
			PRIMARY KEY (ID),
			KEY staff_id (staff_id)
		) $c ENGINE=InnoDB;" );

		dbDelta( "CREATE TABLE " . GMA_TABLE_HOMEWORK . " (
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
		) $c ENGINE=InnoDB;" );

		dbDelta( "CREATE TABLE " . GMA_TABLE_HOMEWORK_SUB . " (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			homework_id bigint(20) UNSIGNED NOT NULL,
			student_record_id bigint(20) UNSIGNED NOT NULL,
			file varchar(255) DEFAULT '',
			note text DEFAULT '',
			submitted_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (ID)
		) $c ENGINE=InnoDB;" );

		dbDelta( "CREATE TABLE " . GMA_TABLE_STUDY_MATERIALS . " (
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
		) $c ENGINE=InnoDB;" );

		dbDelta( "CREATE TABLE " . GMA_TABLE_CHAPTERS . " (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			subject_id bigint(20) UNSIGNED NOT NULL,
			class_school_id bigint(20) UNSIGNED NOT NULL,
			label varchar(255) NOT NULL DEFAULT '',
			description text DEFAULT '',
			sort_order int(11) NOT NULL DEFAULT 0,
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (ID)
		) $c ENGINE=InnoDB;" );

		dbDelta( "CREATE TABLE " . GMA_TABLE_LECTURES . " (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			chapter_id bigint(20) UNSIGNED NOT NULL,
			staff_id bigint(20) UNSIGNED DEFAULT NULL,
			title varchar(255) NOT NULL DEFAULT '',
			description text DEFAULT '',
			video_url varchar(500) DEFAULT '',
			attachment varchar(255) DEFAULT '',
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (ID)
		) $c ENGINE=InnoDB;" );

		dbDelta( "CREATE TABLE " . GMA_TABLE_STUDENT_LEAVES . " (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			student_record_id bigint(20) UNSIGNED NOT NULL,
			school_id bigint(20) UNSIGNED NOT NULL,
			from_date date DEFAULT NULL,
			to_date date DEFAULT NULL,
			reason text DEFAULT '',
			status varchar(20) NOT NULL DEFAULT 'pending',
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (ID)
		) $c ENGINE=InnoDB;" );

		dbDelta( "CREATE TABLE " . GMA_TABLE_RATINGS . " (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			school_id bigint(20) UNSIGNED NOT NULL,
			student_record_id bigint(20) UNSIGNED NOT NULL,
			rating tinyint(1) NOT NULL DEFAULT 5,
			comment text DEFAULT '',
			rated_by bigint(20) UNSIGNED DEFAULT NULL,
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (ID)
		) $c ENGINE=InnoDB;" );
	}


	private static function run_exams( $c ) {
		global $wpdb;
		dbDelta( "CREATE TABLE " . GMA_TABLE_EXAMS_GROUP . " (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			school_id bigint(20) UNSIGNED NOT NULL,
			label varchar(200) NOT NULL DEFAULT '',
			session_id bigint(20) UNSIGNED NOT NULL,
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (ID)
		) $c ENGINE=InnoDB;" );

		dbDelta( "CREATE TABLE " . GMA_TABLE_EXAMS . " (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			class_school_id bigint(20) UNSIGNED NOT NULL,
			label varchar(200) NOT NULL DEFAULT '',
			session_id bigint(20) UNSIGNED NOT NULL,
			exam_group_id bigint(20) UNSIGNED DEFAULT NULL,
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (ID),
			KEY class_school_id (class_school_id)
		) $c ENGINE=InnoDB;" );

		dbDelta( "CREATE TABLE " . GMA_TABLE_EXAM_PAPERS . " (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			exam_id bigint(20) UNSIGNED NOT NULL,
			subject_id bigint(20) UNSIGNED NOT NULL,
			date date DEFAULT NULL,
			start_time time DEFAULT NULL,
			end_time time DEFAULT NULL,
			total_marks decimal(6,2) NOT NULL DEFAULT 100.00,
			pass_marks decimal(6,2) NOT NULL DEFAULT 40.00,
			venue varchar(200) DEFAULT '',
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (ID),
			KEY exam_id (exam_id)
		) $c ENGINE=InnoDB;" );

		dbDelta( "CREATE TABLE " . GMA_TABLE_EXAM_RESULTS . " (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			exam_paper_id bigint(20) UNSIGNED NOT NULL,
			student_record_id bigint(20) UNSIGNED NOT NULL,
			obtained_marks decimal(6,2) NOT NULL DEFAULT 0.00,
			grade varchar(10) DEFAULT '',
			remarks text DEFAULT '',
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (ID),
			KEY exam_paper_id (exam_paper_id)
		) $c ENGINE=InnoDB;" );

		dbDelta( "CREATE TABLE " . GMA_TABLE_ADMIT_CARDS . " (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			exam_id bigint(20) UNSIGNED NOT NULL,
			student_record_id bigint(20) UNSIGNED NOT NULL,
			admit_card_number varchar(50) DEFAULT '',
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (ID)
		) $c ENGINE=InnoDB;" );

		dbDelta( "CREATE TABLE " . GMA_TABLE_ASSESSMENTS . " (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			school_id bigint(20) UNSIGNED NOT NULL,
			label varchar(100) NOT NULL DEFAULT '',
			PRIMARY KEY (ID)
		) $c ENGINE=InnoDB;" );

		dbDelta( "CREATE TABLE " . GMA_TABLE_ASSESSMENT_GRADES . " (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			assessment_id bigint(20) UNSIGNED NOT NULL,
			label varchar(20) NOT NULL DEFAULT '',
			min_marks decimal(6,2) NOT NULL DEFAULT 0.00,
			max_marks decimal(6,2) NOT NULL DEFAULT 100.00,
			gpa decimal(4,2) NOT NULL DEFAULT 0.00,
			remark varchar(100) DEFAULT '',
			PRIMARY KEY (ID)
		) $c ENGINE=InnoDB;" );
	}


	private static function run_accounting( $c ) {
		global $wpdb;
		dbDelta( "CREATE TABLE " . GMA_TABLE_FEES . " (
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
		) $c ENGINE=InnoDB;" );

		dbDelta( "CREATE TABLE " . GMA_TABLE_STUDENT_FEES . " (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			student_record_id bigint(20) UNSIGNED NOT NULL,
			fee_id bigint(20) UNSIGNED NOT NULL,
			amount decimal(10,2) NOT NULL DEFAULT 0.00,
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (ID)
		) $c ENGINE=InnoDB;" );

		dbDelta( "CREATE TABLE " . GMA_TABLE_INVOICES . " (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			school_id bigint(20) UNSIGNED NOT NULL,
			student_record_id bigint(20) UNSIGNED NOT NULL,
			class_school_id bigint(20) UNSIGNED NOT NULL,
			invoice_number varchar(50) DEFAULT '',
			total_amount decimal(10,2) NOT NULL DEFAULT 0.00,
			paid_amount decimal(10,2) NOT NULL DEFAULT 0.00,
			due_amount decimal(10,2) NOT NULL DEFAULT 0.00,
			discount decimal(10,2) NOT NULL DEFAULT 0.00,
			status varchar(20) NOT NULL DEFAULT 'unpaid',
			due_date date DEFAULT NULL,
			note text DEFAULT '',
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (ID),
			KEY school_id (school_id)
		) $c ENGINE=InnoDB;" );

		dbDelta( "CREATE TABLE " . GMA_TABLE_PAYMENTS . " (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			invoice_id bigint(20) UNSIGNED NOT NULL,
			amount decimal(10,2) NOT NULL DEFAULT 0.00,
			payment_method varchar(50) DEFAULT 'cash',
			transaction_id varchar(100) DEFAULT '',
			payment_date date DEFAULT NULL,
			note text DEFAULT '',
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (ID),
			KEY invoice_id (invoice_id)
		) $c ENGINE=InnoDB;" );

		dbDelta( "CREATE TABLE " . GMA_TABLE_CONCESSION_TYPES . " (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			school_id bigint(20) UNSIGNED NOT NULL,
			label varchar(200) NOT NULL DEFAULT '',
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (ID)
		) $c ENGINE=InnoDB;" );

		dbDelta( "CREATE TABLE " . GMA_TABLE_CONCESSION_FEE_MAP . " (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			concession_type_id bigint(20) UNSIGNED NOT NULL,
			fee_id bigint(20) UNSIGNED NOT NULL,
			amount decimal(10,2) NOT NULL DEFAULT 0.00,
			PRIMARY KEY (ID)
		) $c ENGINE=InnoDB;" );

		dbDelta( "CREATE TABLE " . GMA_TABLE_STUDENT_CONCESSION . " (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			student_record_id bigint(20) UNSIGNED NOT NULL,
			concession_type_id bigint(20) UNSIGNED NOT NULL,
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (ID)
		) $c ENGINE=InnoDB;" );

		dbDelta( "CREATE TABLE " . GMA_TABLE_EXPENSE_CATEGORIES . " (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			school_id bigint(20) UNSIGNED NOT NULL,
			label varchar(200) NOT NULL DEFAULT '',
			PRIMARY KEY (ID)
		) $c ENGINE=InnoDB;" );

		dbDelta( "CREATE TABLE " . GMA_TABLE_EXPENSES . " (
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
		) $c ENGINE=InnoDB;" );

		dbDelta( "CREATE TABLE " . GMA_TABLE_INCOME_CATEGORIES . " (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			school_id bigint(20) UNSIGNED NOT NULL,
			label varchar(200) NOT NULL DEFAULT '',
			PRIMARY KEY (ID)
		) $c ENGINE=InnoDB;" );

		dbDelta( "CREATE TABLE " . GMA_TABLE_INCOME . " (
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
		) $c ENGINE=InnoDB;" );
	}


	private static function run_library( $c ) {
		global $wpdb;
		dbDelta( "CREATE TABLE " . GMA_TABLE_BOOKS . " (
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
		) $c ENGINE=InnoDB;" );

		dbDelta( "CREATE TABLE " . GMA_TABLE_BOOKS_ISSUED . " (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			book_id bigint(20) UNSIGNED NOT NULL,
			student_record_id bigint(20) UNSIGNED NOT NULL,
			issue_date date DEFAULT NULL,
			return_date date DEFAULT NULL,
			returned_at date DEFAULT NULL,
			fine decimal(8,2) NOT NULL DEFAULT 0.00,
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (ID)
		) $c ENGINE=InnoDB;" );

		dbDelta( "CREATE TABLE " . GMA_TABLE_LIBRARY_CARDS . " (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			student_record_id bigint(20) UNSIGNED NOT NULL,
			card_number varchar(50) DEFAULT '',
			issued_date date DEFAULT NULL,
			expiry_date date DEFAULT NULL,
			PRIMARY KEY (ID)
		) $c ENGINE=InnoDB;" );
	}

	private static function run_hostel( $c ) {
		global $wpdb;
		dbDelta( "CREATE TABLE " . GMA_TABLE_HOSTELS . " (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			school_id bigint(20) UNSIGNED NOT NULL,
			label varchar(200) NOT NULL DEFAULT '',
			type varchar(20) NOT NULL DEFAULT 'boys',
			warden_name varchar(100) DEFAULT '',
			capacity int(11) NOT NULL DEFAULT 0,
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (ID)
		) $c ENGINE=InnoDB;" );

		dbDelta( "CREATE TABLE " . GMA_TABLE_ROOMS . " (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			hostel_id bigint(20) UNSIGNED NOT NULL,
			room_number varchar(20) NOT NULL DEFAULT '',
			capacity int(11) NOT NULL DEFAULT 0,
			room_type varchar(50) DEFAULT '',
			fee decimal(8,2) NOT NULL DEFAULT 0.00,
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (ID),
			KEY hostel_id (hostel_id)
		) $c ENGINE=InnoDB;" );
	}

	private static function run_transport( $c ) {
		global $wpdb;
		dbDelta( "CREATE TABLE " . GMA_TABLE_VEHICLES . " (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			school_id bigint(20) UNSIGNED NOT NULL,
			number varchar(50) NOT NULL DEFAULT '',
			model varchar(100) DEFAULT '',
			capacity int(11) NOT NULL DEFAULT 0,
			driver_name varchar(100) DEFAULT '',
			driver_phone varchar(30) DEFAULT '',
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (ID)
		) $c ENGINE=InnoDB;" );

		dbDelta( "CREATE TABLE " . GMA_TABLE_ROUTES . " (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			school_id bigint(20) UNSIGNED NOT NULL,
			label varchar(200) NOT NULL DEFAULT '',
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (ID)
		) $c ENGINE=InnoDB;" );

		dbDelta( "CREATE TABLE " . GMA_TABLE_ROUTE_VEHICLE . " (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			route_id bigint(20) UNSIGNED NOT NULL,
			vehicle_id bigint(20) UNSIGNED NOT NULL,
			stop_name varchar(200) DEFAULT '',
			stop_time time DEFAULT NULL,
			fee decimal(8,2) NOT NULL DEFAULT 0.00,
			PRIMARY KEY (ID)
		) $c ENGINE=InnoDB;" );
	}


	private static function run_comms( $c ) {
		global $wpdb;
		dbDelta( "CREATE TABLE " . GMA_TABLE_NOTICES . " (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			school_id bigint(20) UNSIGNED NOT NULL,
			title varchar(255) NOT NULL DEFAULT '',
			description longtext DEFAULT '',
			date date DEFAULT NULL,
			attachment varchar(255) DEFAULT '',
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (ID),
			KEY school_id (school_id)
		) $c ENGINE=InnoDB;" );

		dbDelta( "CREATE TABLE " . GMA_TABLE_EVENTS . " (
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
		) $c ENGINE=InnoDB;" );

		dbDelta( "CREATE TABLE " . GMA_TABLE_NOTIFICATIONS . " (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			school_id bigint(20) UNSIGNED NOT NULL,
			title varchar(255) NOT NULL DEFAULT '',
			message longtext DEFAULT '',
			type varchar(30) DEFAULT 'general',
			audience varchar(30) DEFAULT 'all',
			is_read tinyint(1) NOT NULL DEFAULT 0,
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (ID),
			KEY school_id (school_id)
		) $c ENGINE=InnoDB;" );

		dbDelta( "CREATE TABLE " . GMA_TABLE_MEETINGS . " (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			school_id bigint(20) UNSIGNED NOT NULL,
			title varchar(255) NOT NULL DEFAULT '',
			description text DEFAULT '',
			meeting_date date DEFAULT NULL,
			meeting_time time DEFAULT NULL,
			venue varchar(255) DEFAULT '',
			link varchar(500) DEFAULT '',
			created_by bigint(20) UNSIGNED DEFAULT NULL,
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (ID)
		) $c ENGINE=InnoDB;" );

		dbDelta( "CREATE TABLE " . GMA_TABLE_ACTIVITIES . " (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			school_id bigint(20) UNSIGNED NOT NULL,
			class_school_id bigint(20) UNSIGNED DEFAULT NULL,
			title varchar(255) NOT NULL DEFAULT '',
			description text DEFAULT '',
			date date DEFAULT NULL,
			attachment varchar(255) DEFAULT '',
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (ID)
		) $c ENGINE=InnoDB;" );
	}

	private static function run_misc( $c ) {
		global $wpdb;
		dbDelta( "CREATE TABLE " . GMA_TABLE_LEAVES . " (
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
		) $c ENGINE=InnoDB;" );

		dbDelta( "CREATE TABLE " . GMA_TABLE_CERTIFICATES . " (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			school_id bigint(20) UNSIGNED NOT NULL,
			label varchar(200) NOT NULL DEFAULT '',
			content_template longtext DEFAULT '',
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (ID)
		) $c ENGINE=InnoDB;" );

		dbDelta( "CREATE TABLE " . GMA_TABLE_CERTIFICATE_STUDENT . " (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			certificate_id bigint(20) UNSIGNED NOT NULL,
			student_record_id bigint(20) UNSIGNED NOT NULL,
			issued_date date DEFAULT NULL,
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (ID)
		) $c ENGINE=InnoDB;" );

		dbDelta( "CREATE TABLE " . GMA_TABLE_TRANSFER_CERTS . " (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			student_record_id bigint(20) UNSIGNED NOT NULL,
			reason text DEFAULT '',
			issued_date date DEFAULT NULL,
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (ID)
		) $c ENGINE=InnoDB;" );

		dbDelta( "CREATE TABLE " . GMA_TABLE_INQUIRIES . " (
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
		) $c ENGINE=InnoDB;" );

		dbDelta( "CREATE TABLE " . GMA_TABLE_TICKETS . " (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			school_id bigint(20) UNSIGNED NOT NULL,
			user_id bigint(20) UNSIGNED NOT NULL,
			subject varchar(255) NOT NULL DEFAULT '',
			message longtext DEFAULT '',
			priority varchar(20) DEFAULT 'normal',
			status varchar(20) NOT NULL DEFAULT 'open',
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (ID),
			KEY school_id (school_id)
		) $c ENGINE=InnoDB;" );

		dbDelta( "CREATE TABLE " . GMA_TABLE_TICKET_REPLIES . " (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			ticket_id bigint(20) UNSIGNED NOT NULL,
			user_id bigint(20) UNSIGNED NOT NULL,
			message longtext DEFAULT '',
			attachment varchar(255) DEFAULT '',
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (ID),
			KEY ticket_id (ticket_id)
		) $c ENGINE=InnoDB;" );

		dbDelta( "CREATE TABLE " . GMA_TABLE_LOGS . " (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			school_id bigint(20) UNSIGNED NOT NULL DEFAULT 0,
			user_id bigint(20) UNSIGNED NOT NULL DEFAULT 0,
			action varchar(100) NOT NULL DEFAULT '',
			details text DEFAULT '',
			ip varchar(45) DEFAULT '',
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (ID),
			KEY school_id (school_id)
		) $c ENGINE=InnoDB;" );
	}

	private static function insert_defaults() {
		global $wpdb;
		// Default medium
		if ( ! $wpdb->get_var( "SELECT COUNT(*) FROM " . GMA_TABLE_MEDIUM ) ) {
			$wpdb->insert( GMA_TABLE_MEDIUM, array( 'label' => 'English' ) );
		}
		// Default school
		if ( ! $wpdb->get_var( "SELECT COUNT(*) FROM " . GMA_TABLE_SCHOOLS ) ) {
			$wpdb->insert( GMA_TABLE_SCHOOLS, array(
				'label'      => 'Guidance Modern Academy',
				'email'      => 'info@guidancemodernacademy.in',
				'is_active'  => 1,
				'created_at' => current_time( 'mysql' ),
			) );
		}
	}
}
