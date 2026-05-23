<?php
/**
 * Database schema installer for Smart School Manager.
 *
 * @package SmartSchoolManager
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

class SSM_Database {

    /**
     * Helper to retrieve table name with prefix.
     */
    public static function table( $name ) {
        global $wpdb;
        return $wpdb->prefix . 'ssm_' . $name;
    }

    /**
     * Create all schema using dbDelta.
     */
    public static function install() {
        global $wpdb;
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        $charset_collate = $wpdb->get_charset_collate();
        $p = $wpdb->prefix . 'ssm_';

        $sql = array();

        // Core / Admin.
        $sql[] = "CREATE TABLE {$p}schools (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            name VARCHAR(191) NOT NULL,
            code VARCHAR(50) DEFAULT NULL,
            address TEXT,
            phone VARCHAR(50),
            email VARCHAR(191),
            logo VARCHAR(255),
            status TINYINT(1) NOT NULL DEFAULT 1,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY status (status)
        ) $charset_collate;";

        $sql[] = "CREATE TABLE {$p}sessions (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            name VARCHAR(100) NOT NULL,
            start_date DATE,
            end_date DATE,
            is_current TINYINT(1) NOT NULL DEFAULT 0,
            status TINYINT(1) NOT NULL DEFAULT 1,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        $sql[] = "CREATE TABLE {$p}categories (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            name VARCHAR(100) NOT NULL,
            description TEXT,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        $sql[] = "CREATE TABLE {$p}classes (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            school_id BIGINT(20) UNSIGNED DEFAULT 0,
            name VARCHAR(100) NOT NULL,
            category_id BIGINT(20) UNSIGNED DEFAULT 0,
            section VARCHAR(50),
            class_teacher_id BIGINT(20) UNSIGNED DEFAULT 0,
            capacity INT(11) DEFAULT 0,
            status TINYINT(1) NOT NULL DEFAULT 1,
            PRIMARY KEY  (id),
            KEY school_id (school_id)
        ) $charset_collate;";

        $sql[] = "CREATE TABLE {$p}subjects (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            class_id BIGINT(20) UNSIGNED NOT NULL,
            name VARCHAR(100) NOT NULL,
            code VARCHAR(50),
            type VARCHAR(50) DEFAULT 'theory',
            PRIMARY KEY  (id),
            KEY class_id (class_id)
        ) $charset_collate;";

        $sql[] = "CREATE TABLE {$p}mediums (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            name VARCHAR(100) NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        $sql[] = "CREATE TABLE {$p}student_types (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            name VARCHAR(100) NOT NULL,
            description TEXT,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        // Students.
        $sql[] = "CREATE TABLE {$p}students (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            admission_no VARCHAR(50),
            roll_no VARCHAR(50),
            first_name VARCHAR(100) NOT NULL,
            last_name VARCHAR(100),
            email VARCHAR(191),
            phone VARCHAR(50),
            gender VARCHAR(10),
            dob DATE,
            class_id BIGINT(20) UNSIGNED DEFAULT 0,
            section VARCHAR(50),
            session_id BIGINT(20) UNSIGNED DEFAULT 0,
            student_type_id BIGINT(20) UNSIGNED DEFAULT 0,
            medium_id BIGINT(20) UNSIGNED DEFAULT 0,
            address TEXT,
            father_name VARCHAR(100),
            mother_name VARCHAR(100),
            guardian_phone VARCHAR(50),
            blood_group VARCHAR(10),
            photo VARCHAR(255),
            admission_date DATE,
            status VARCHAR(20) NOT NULL DEFAULT 'active',
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY class_id (class_id),
            KEY session_id (session_id),
            KEY status (status)
        ) $charset_collate;";

        $sql[] = "CREATE TABLE {$p}admissions (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            applicant_name VARCHAR(150) NOT NULL,
            email VARCHAR(191),
            phone VARCHAR(50),
            class_id BIGINT(20) UNSIGNED DEFAULT 0,
            notes TEXT,
            status VARCHAR(20) NOT NULL DEFAULT 'pending',
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY status (status)
        ) $charset_collate;";

        $sql[] = "CREATE TABLE {$p}inquiries (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            name VARCHAR(150) NOT NULL,
            email VARCHAR(191),
            phone VARCHAR(50),
            source VARCHAR(50),
            message TEXT,
            follow_up_date DATE,
            status VARCHAR(20) NOT NULL DEFAULT 'new',
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        // Staff & Roles.
        $sql[] = "CREATE TABLE {$p}staff (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            employee_no VARCHAR(50),
            first_name VARCHAR(100) NOT NULL,
            last_name VARCHAR(100),
            email VARCHAR(191),
            phone VARCHAR(50),
            gender VARCHAR(10),
            dob DATE,
            role_id BIGINT(20) UNSIGNED DEFAULT 0,
            designation VARCHAR(100),
            qualification VARCHAR(150),
            joining_date DATE,
            salary DECIMAL(12,2) DEFAULT 0,
            address TEXT,
            photo VARCHAR(255),
            status VARCHAR(20) NOT NULL DEFAULT 'active',
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        $sql[] = "CREATE TABLE {$p}roles (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            name VARCHAR(100) NOT NULL,
            slug VARCHAR(100),
            permissions LONGTEXT,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        $sql[] = "CREATE TABLE {$p}admins (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id BIGINT(20) UNSIGNED DEFAULT 0,
            school_id BIGINT(20) UNSIGNED DEFAULT 0,
            name VARCHAR(150),
            email VARCHAR(191),
            role VARCHAR(50) DEFAULT 'admin',
            status TINYINT(1) NOT NULL DEFAULT 1,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        $sql[] = "CREATE TABLE {$p}staff_attendance (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            staff_id BIGINT(20) UNSIGNED NOT NULL,
            date DATE NOT NULL,
            status VARCHAR(20) DEFAULT 'present',
            in_time TIME,
            out_time TIME,
            note VARCHAR(255),
            PRIMARY KEY  (id),
            KEY staff_id (staff_id),
            KEY date (date)
        ) $charset_collate;";

        $sql[] = "CREATE TABLE {$p}staff_leaves (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            staff_id BIGINT(20) UNSIGNED NOT NULL,
            leave_type VARCHAR(50),
            from_date DATE,
            to_date DATE,
            reason TEXT,
            status VARCHAR(20) NOT NULL DEFAULT 'pending',
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY staff_id (staff_id)
        ) $charset_collate;";

        // Class Management.
        $sql[] = "CREATE TABLE {$p}attendance (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            student_id BIGINT(20) UNSIGNED NOT NULL,
            class_id BIGINT(20) UNSIGNED NOT NULL,
            date DATE NOT NULL,
            status VARCHAR(20) DEFAULT 'present',
            note VARCHAR(255),
            PRIMARY KEY  (id),
            KEY student_id (student_id),
            KEY date (date)
        ) $charset_collate;";

        $sql[] = "CREATE TABLE {$p}timetable (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            class_id BIGINT(20) UNSIGNED NOT NULL,
            subject_id BIGINT(20) UNSIGNED NOT NULL,
            staff_id BIGINT(20) UNSIGNED DEFAULT 0,
            day VARCHAR(20) NOT NULL,
            start_time TIME,
            end_time TIME,
            room VARCHAR(50),
            PRIMARY KEY  (id),
            KEY class_id (class_id)
        ) $charset_collate;";

        $sql[] = "CREATE TABLE {$p}homework (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            class_id BIGINT(20) UNSIGNED NOT NULL,
            subject_id BIGINT(20) UNSIGNED DEFAULT 0,
            staff_id BIGINT(20) UNSIGNED DEFAULT 0,
            title VARCHAR(191) NOT NULL,
            description LONGTEXT,
            assigned_date DATE,
            due_date DATE,
            attachment VARCHAR(255),
            PRIMARY KEY  (id),
            KEY class_id (class_id)
        ) $charset_collate;";

        $sql[] = "CREATE TABLE {$p}study_materials (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            class_id BIGINT(20) UNSIGNED NOT NULL,
            subject_id BIGINT(20) UNSIGNED DEFAULT 0,
            title VARCHAR(191) NOT NULL,
            description TEXT,
            file VARCHAR(255),
            uploaded_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        $sql[] = "CREATE TABLE {$p}notices (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            title VARCHAR(191) NOT NULL,
            body LONGTEXT,
            audience VARCHAR(50) DEFAULT 'all',
            class_id BIGINT(20) UNSIGNED DEFAULT 0,
            posted_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        $sql[] = "CREATE TABLE {$p}events (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            title VARCHAR(191) NOT NULL,
            description TEXT,
            start_date DATETIME,
            end_date DATETIME,
            location VARCHAR(191),
            PRIMARY KEY  (id)
        ) $charset_collate;";

        $sql[] = "CREATE TABLE {$p}activities (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            class_id BIGINT(20) UNSIGNED NOT NULL,
            title VARCHAR(191) NOT NULL,
            description TEXT,
            activity_date DATE,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        $sql[] = "CREATE TABLE {$p}meetings (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            title VARCHAR(191) NOT NULL,
            with_whom VARCHAR(150),
            meeting_date DATETIME,
            agenda TEXT,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        $sql[] = "CREATE TABLE {$p}student_leaves (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            student_id BIGINT(20) UNSIGNED NOT NULL,
            leave_type VARCHAR(50),
            from_date DATE,
            to_date DATE,
            reason TEXT,
            status VARCHAR(20) NOT NULL DEFAULT 'pending',
            PRIMARY KEY  (id)
        ) $charset_collate;";

        $sql[] = "CREATE TABLE {$p}ratings (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            student_id BIGINT(20) UNSIGNED DEFAULT 0,
            class_id BIGINT(20) UNSIGNED DEFAULT 0,
            score DECIMAL(5,2),
            remarks TEXT,
            rated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        // Examination.
        $sql[] = "CREATE TABLE {$p}exams (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            name VARCHAR(150) NOT NULL,
            class_id BIGINT(20) UNSIGNED DEFAULT 0,
            exam_group_id BIGINT(20) UNSIGNED DEFAULT 0,
            start_date DATE,
            end_date DATE,
            description TEXT,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        $sql[] = "CREATE TABLE {$p}exam_groups (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            name VARCHAR(100) NOT NULL,
            type VARCHAR(50),
            description TEXT,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        $sql[] = "CREATE TABLE {$p}exam_results (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            exam_id BIGINT(20) UNSIGNED NOT NULL,
            student_id BIGINT(20) UNSIGNED NOT NULL,
            subject_id BIGINT(20) UNSIGNED NOT NULL,
            marks DECIMAL(7,2) DEFAULT 0,
            grade VARCHAR(10),
            remarks VARCHAR(191),
            PRIMARY KEY  (id),
            KEY exam_id (exam_id),
            KEY student_id (student_id)
        ) $charset_collate;";

        $sql[] = "CREATE TABLE {$p}assessments (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            name VARCHAR(150) NOT NULL,
            grade_scale TEXT,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        // Accounting.
        $sql[] = "CREATE TABLE {$p}fee_structures (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            class_id BIGINT(20) UNSIGNED DEFAULT 0,
            name VARCHAR(150) NOT NULL,
            amount DECIMAL(12,2) NOT NULL DEFAULT 0,
            frequency VARCHAR(20) DEFAULT 'monthly',
            due_day INT(11) DEFAULT 5,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        $sql[] = "CREATE TABLE {$p}invoices (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            invoice_no VARCHAR(50) NOT NULL,
            student_id BIGINT(20) UNSIGNED NOT NULL,
            amount DECIMAL(12,2) NOT NULL DEFAULT 0,
            paid DECIMAL(12,2) NOT NULL DEFAULT 0,
            due_date DATE,
            status VARCHAR(20) NOT NULL DEFAULT 'unpaid',
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY student_id (student_id),
            KEY status (status)
        ) $charset_collate;";

        $sql[] = "CREATE TABLE {$p}payments (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            invoice_id BIGINT(20) UNSIGNED NOT NULL,
            student_id BIGINT(20) UNSIGNED NOT NULL,
            amount DECIMAL(12,2) NOT NULL DEFAULT 0,
            method VARCHAR(50) DEFAULT 'cash',
            ref_no VARCHAR(100),
            paid_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY invoice_id (invoice_id)
        ) $charset_collate;";

        $sql[] = "CREATE TABLE {$p}concession_types (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            name VARCHAR(150) NOT NULL,
            type VARCHAR(20) DEFAULT 'percent',
            value DECIMAL(10,2) NOT NULL DEFAULT 0,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        $sql[] = "CREATE TABLE {$p}student_concessions (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            student_id BIGINT(20) UNSIGNED NOT NULL,
            concession_type_id BIGINT(20) UNSIGNED NOT NULL,
            note VARCHAR(191),
            PRIMARY KEY  (id)
        ) $charset_collate;";

        $sql[] = "CREATE TABLE {$p}income (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            category VARCHAR(100),
            title VARCHAR(191) NOT NULL,
            amount DECIMAL(12,2) NOT NULL DEFAULT 0,
            received_at DATE,
            note TEXT,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        $sql[] = "CREATE TABLE {$p}expenses (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            category VARCHAR(100),
            title VARCHAR(191) NOT NULL,
            amount DECIMAL(12,2) NOT NULL DEFAULT 0,
            spent_at DATE,
            note TEXT,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        // Library.
        $sql[] = "CREATE TABLE {$p}books (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            isbn VARCHAR(50),
            title VARCHAR(191) NOT NULL,
            author VARCHAR(150),
            category VARCHAR(100),
            quantity INT(11) DEFAULT 1,
            available INT(11) DEFAULT 1,
            shelf VARCHAR(50),
            cover VARCHAR(255),
            PRIMARY KEY  (id)
        ) $charset_collate;";

        $sql[] = "CREATE TABLE {$p}books_issued (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            book_id BIGINT(20) UNSIGNED NOT NULL,
            student_id BIGINT(20) UNSIGNED DEFAULT 0,
            staff_id BIGINT(20) UNSIGNED DEFAULT 0,
            issued_at DATE,
            return_due DATE,
            returned_at DATE DEFAULT NULL,
            status VARCHAR(20) NOT NULL DEFAULT 'issued',
            PRIMARY KEY  (id)
        ) $charset_collate;";

        // Hostel.
        $sql[] = "CREATE TABLE {$p}hostels (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            name VARCHAR(150) NOT NULL,
            type VARCHAR(50) DEFAULT 'boys',
            address TEXT,
            warden VARCHAR(100),
            phone VARCHAR(50),
            capacity INT(11) DEFAULT 0,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        $sql[] = "CREATE TABLE {$p}hostel_rooms (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            hostel_id BIGINT(20) UNSIGNED NOT NULL,
            room_no VARCHAR(50),
            type VARCHAR(50),
            capacity INT(11) DEFAULT 1,
            occupied INT(11) DEFAULT 0,
            fee DECIMAL(10,2) DEFAULT 0,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        // Transport.
        $sql[] = "CREATE TABLE {$p}routes (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            name VARCHAR(150) NOT NULL,
            start_point VARCHAR(150),
            end_point VARCHAR(150),
            distance VARCHAR(50),
            fee DECIMAL(10,2) DEFAULT 0,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        $sql[] = "CREATE TABLE {$p}vehicles (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            number VARCHAR(50) NOT NULL,
            model VARCHAR(100),
            capacity INT(11) DEFAULT 0,
            driver VARCHAR(100),
            driver_phone VARCHAR(50),
            route_id BIGINT(20) UNSIGNED DEFAULT 0,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        // Lectures.
        $sql[] = "CREATE TABLE {$p}lectures (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            title VARCHAR(191) NOT NULL,
            class_id BIGINT(20) UNSIGNED DEFAULT 0,
            subject_id BIGINT(20) UNSIGNED DEFAULT 0,
            staff_id BIGINT(20) UNSIGNED DEFAULT 0,
            link VARCHAR(255),
            scheduled_at DATETIME,
            duration INT(11) DEFAULT 60,
            audience VARCHAR(20) DEFAULT 'students',
            PRIMARY KEY  (id)
        ) $charset_collate;";

        // Chapters.
        $sql[] = "CREATE TABLE {$p}chapters (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            subject_id BIGINT(20) UNSIGNED NOT NULL,
            title VARCHAR(191) NOT NULL,
            sort_order INT(11) DEFAULT 0,
            description TEXT,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        // Tickets.
        $sql[] = "CREATE TABLE {$p}tickets (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            ticket_no VARCHAR(50),
            subject VARCHAR(191) NOT NULL,
            description LONGTEXT,
            opened_by VARCHAR(100),
            assigned_to VARCHAR(100),
            priority VARCHAR(20) DEFAULT 'medium',
            status VARCHAR(20) NOT NULL DEFAULT 'open',
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        // Logs / notifications.
        $sql[] = "CREATE TABLE {$p}logs (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id BIGINT(20) UNSIGNED DEFAULT 0,
            action VARCHAR(150),
            module VARCHAR(50),
            details TEXT,
            ip VARCHAR(50),
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY module (module)
        ) $charset_collate;";

        $sql[] = "CREATE TABLE {$p}notifications (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            channel VARCHAR(20) DEFAULT 'inapp',
            title VARCHAR(191),
            message LONGTEXT,
            audience VARCHAR(50) DEFAULT 'all',
            target_id BIGINT(20) UNSIGNED DEFAULT 0,
            sent_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            status VARCHAR(20) DEFAULT 'sent',
            PRIMARY KEY  (id)
        ) $charset_collate;";

        $sql[] = "CREATE TABLE {$p}certificates (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            type VARCHAR(50) DEFAULT 'general',
            target_id BIGINT(20) UNSIGNED DEFAULT 0,
            title VARCHAR(191),
            body LONGTEXT,
            issued_at DATE,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        foreach ( $sql as $stmt ) {
            dbDelta( $stmt );
        }

        // Seed defaults if empty.
        self::seed_defaults();
    }

    public static function seed_defaults() {
        global $wpdb;
        $p = $wpdb->prefix . 'ssm_';

        // Default session.
        $count = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$p}sessions" );
        if ( 0 === $count ) {
            $wpdb->insert( $p . 'sessions', array(
                'name'       => date( 'Y' ) . '-' . ( (int) date( 'Y' ) + 1 ),
                'start_date' => date( 'Y-04-01' ),
                'end_date'   => ( (int) date( 'Y' ) + 1 ) . '-03-31',
                'is_current' => 1,
                'status'     => 1,
            ) );
        }

        // Default categories.
        $cnt = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$p}categories" );
        if ( 0 === $cnt ) {
            foreach ( array( 'Primary', 'Secondary', 'Higher Secondary' ) as $c ) {
                $wpdb->insert( $p . 'categories', array( 'name' => $c ) );
            }
        }

        // Default mediums.
        $cnt = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$p}mediums" );
        if ( 0 === $cnt ) {
            foreach ( array( 'English', 'Hindi', 'Bengali' ) as $m ) {
                $wpdb->insert( $p . 'mediums', array( 'name' => $m ) );
            }
        }

        // Default student types.
        $cnt = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$p}student_types" );
        if ( 0 === $cnt ) {
            foreach ( array( 'Regular', 'Hostel', 'Day Boarding' ) as $t ) {
                $wpdb->insert( $p . 'student_types', array( 'name' => $t ) );
            }
        }

        // Default roles.
        $cnt = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$p}roles" );
        if ( 0 === $cnt ) {
            foreach ( array(
                array( 'Principal', 'principal' ),
                array( 'Vice Principal', 'vice-principal' ),
                array( 'Teacher', 'teacher' ),
                array( 'Accountant', 'accountant' ),
                array( 'Librarian', 'librarian' ),
            ) as $r ) {
                $wpdb->insert( $p . 'roles', array( 'name' => $r[0], 'slug' => $r[1] ) );
            }
        }

        // Default concession types.
        $cnt = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$p}concession_types" );
        if ( 0 === $cnt ) {
            $wpdb->insert( $p . 'concession_types', array( 'name' => 'Sibling Discount', 'type' => 'percent', 'value' => 10 ) );
            $wpdb->insert( $p . 'concession_types', array( 'name' => 'Merit Scholarship', 'type' => 'percent', 'value' => 25 ) );
            $wpdb->insert( $p . 'concession_types', array( 'name' => 'Staff Ward', 'type' => 'percent', 'value' => 50 ) );
        }
    }
}
