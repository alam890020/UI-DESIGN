<?php
if (!defined('ABSPATH')) {
    exit;
}

class SM_Database {

    public static function activate() {
        self::create_tables();
        add_option('sm_version', SM_VERSION);
        flush_rewrite_rules();
    }

    public static function deactivate() {
        flush_rewrite_rules();
    }

    public static function create_tables() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $prefix = $wpdb->prefix . 'sm_';

        $sql = array();

        // Classes table
        $sql[] = "CREATE TABLE IF NOT EXISTS {$prefix}classes (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            name varchar(100) NOT NULL,
            description text,
            status tinyint(1) DEFAULT 1,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;";

        // Sections table
        $sql[] = "CREATE TABLE IF NOT EXISTS {$prefix}sections (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            class_id bigint(20) NOT NULL,
            name varchar(100) NOT NULL,
            capacity int(11) DEFAULT 0,
            status tinyint(1) DEFAULT 1,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY class_id (class_id)
        ) $charset_collate;";

        // Students table
        $sql[] = "CREATE TABLE IF NOT EXISTS {$prefix}students (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            admission_no varchar(50) NOT NULL,
            first_name varchar(100) NOT NULL,
            last_name varchar(100) NOT NULL,
            email varchar(150),
            phone varchar(20),
            date_of_birth date,
            gender enum('male','female','other') DEFAULT 'male',
            address text,
            city varchar(100),
            state varchar(100),
            class_id bigint(20),
            section_id bigint(20),
            parent_name varchar(200),
            parent_phone varchar(20),
            parent_email varchar(150),
            admission_date date,
            photo varchar(255),
            status enum('active','inactive','graduated','transferred') DEFAULT 'active',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY admission_no (admission_no),
            KEY class_id (class_id),
            KEY section_id (section_id)
        ) $charset_collate;";

        // Staff table
        $sql[] = "CREATE TABLE IF NOT EXISTS {$prefix}staff (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            staff_id varchar(50) NOT NULL,
            first_name varchar(100) NOT NULL,
            last_name varchar(100) NOT NULL,
            email varchar(150),
            phone varchar(20),
            designation varchar(100),
            department varchar(100),
            date_of_birth date,
            gender enum('male','female','other') DEFAULT 'male',
            address text,
            joining_date date,
            salary decimal(10,2) DEFAULT 0,
            photo varchar(255),
            status enum('active','inactive','resigned') DEFAULT 'active',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY staff_id (staff_id)
        ) $charset_collate;";

        // Attendance table
        $sql[] = "CREATE TABLE IF NOT EXISTS {$prefix}attendance (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            student_id bigint(20) NOT NULL,
            class_id bigint(20) NOT NULL,
            section_id bigint(20),
            date date NOT NULL,
            status enum('present','absent','late','half_day') DEFAULT 'present',
            remarks text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY student_id (student_id),
            KEY class_id (class_id),
            KEY date (date)
        ) $charset_collate;";

        // Fee types table
        $sql[] = "CREATE TABLE IF NOT EXISTS {$prefix}fee_types (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            name varchar(100) NOT NULL,
            amount decimal(10,2) DEFAULT 0,
            description text,
            status tinyint(1) DEFAULT 1,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;";

        // Fees table
        $sql[] = "CREATE TABLE IF NOT EXISTS {$prefix}fees (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            student_id bigint(20) NOT NULL,
            fee_type_id bigint(20) NOT NULL,
            amount decimal(10,2) NOT NULL,
            paid_amount decimal(10,2) DEFAULT 0,
            due_date date,
            paid_date date,
            status enum('pending','partial','paid','overdue') DEFAULT 'pending',
            payment_method varchar(50),
            transaction_id varchar(100),
            remarks text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY student_id (student_id),
            KEY fee_type_id (fee_type_id)
        ) $charset_collate;";

        // Exams table
        $sql[] = "CREATE TABLE IF NOT EXISTS {$prefix}exams (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            name varchar(200) NOT NULL,
            class_id bigint(20) NOT NULL,
            start_date date,
            end_date date,
            description text,
            status enum('upcoming','ongoing','completed') DEFAULT 'upcoming',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY class_id (class_id)
        ) $charset_collate;";

        // Exam subjects table
        $sql[] = "CREATE TABLE IF NOT EXISTS {$prefix}exam_subjects (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            exam_id bigint(20) NOT NULL,
            subject_name varchar(100) NOT NULL,
            full_marks decimal(5,2) DEFAULT 100,
            pass_marks decimal(5,2) DEFAULT 33,
            exam_date date,
            PRIMARY KEY (id),
            KEY exam_id (exam_id)
        ) $charset_collate;";

        // Marks table
        $sql[] = "CREATE TABLE IF NOT EXISTS {$prefix}marks (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            exam_id bigint(20) NOT NULL,
            exam_subject_id bigint(20) NOT NULL,
            student_id bigint(20) NOT NULL,
            marks_obtained decimal(5,2) DEFAULT 0,
            remarks text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY exam_id (exam_id),
            KEY student_id (student_id)
        ) $charset_collate;";

        // Notices table
        $sql[] = "CREATE TABLE IF NOT EXISTS {$prefix}notices (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            title varchar(255) NOT NULL,
            content text,
            type enum('general','event','holiday','exam') DEFAULT 'general',
            target enum('all','students','staff','parents') DEFAULT 'all',
            start_date date,
            end_date date,
            status tinyint(1) DEFAULT 1,
            created_by bigint(20),
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        foreach ($sql as $query) {
            dbDelta($query);
        }
    }
}
