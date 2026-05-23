<?php
/**
 * Frontend assets + AJAX handler.
 *
 * @package SchoolManagementStudio
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

class SMS_Public {

    public function register() {
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );
        add_action( 'wp_ajax_sms_public_submit', array( $this, 'submit' ) );
        add_action( 'wp_ajax_nopriv_sms_public_submit', array( $this, 'submit' ) );
    }

    public function enqueue() {
        wp_register_style( 'sms-public', SMS_PLUGIN_URL . 'assets/css/public.css', array(), SMS_VERSION );
        wp_register_script( 'sms-public', SMS_PLUGIN_URL . 'assets/js/public.js', array( 'jquery' ), SMS_VERSION, true );
        wp_localize_script( 'sms-public', 'SMSPublic', array(
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( 'sms_public_nonce' ),
        ) );

        global $post;
        if ( ! $post ) return;
        $tags = array( 'sms_student_form', 'sms_admission_form', 'sms_inquiry_form', 'sms_notices', 'sms_events' );
        foreach ( $tags as $t ) {
            if ( has_shortcode( $post->post_content, $t ) ) {
                wp_enqueue_style( 'sms-public' );
                wp_enqueue_script( 'sms-public' );
                return;
            }
        }
    }

    public function submit() {
        check_ajax_referer( 'sms_public_nonce', 'nonce' );
        $type = isset( $_POST['type'] ) ? sanitize_key( $_POST['type'] ) : '';
        $data = isset( $_POST['data'] ) ? (array) $_POST['data'] : array();
        $clean = array();
        foreach ( $data as $k => $v ) {
            $clean[ sanitize_key( $k ) ] = is_array( $v ) ? wp_json_encode( $v ) : sanitize_text_field( wp_unslash( $v ) );
        }

        global $wpdb;
        $p = $wpdb->prefix . 'sms_';

        if ( 'admission' === $type ) {
            $clean['status']     = 'pending';
            $clean['created_at'] = current_time( 'mysql' );
            $wpdb->insert( $p . 'admissions', $clean );
            wp_send_json_success( array( 'message' => 'Application submitted!' ) );
        }
        if ( 'inquiry' === $type ) {
            $clean['status']     = 'new';
            $clean['created_at'] = current_time( 'mysql' );
            $wpdb->insert( $p . 'inquiries', $clean );
            wp_send_json_success( array( 'message' => 'Thanks! Inquiry recorded.' ) );
        }
        if ( 'student' === $type ) {
            $row = array(
                'applicant_name' => trim( ( $clean['first_name'] ?? '' ) . ' ' . ( $clean['last_name'] ?? '' ) ),
                'email'          => $clean['email'] ?? '',
                'phone'          => $clean['phone'] ?? ( $clean['guardian_phone'] ?? '' ),
                'class_id'       => (int) ( $clean['class_id'] ?? 0 ),
                'notes'          => 'Frontend student registration. Father: ' . ( $clean['father_name'] ?? '' ) . '; Mother: ' . ( $clean['mother_name'] ?? '' ) . '; DOB: ' . ( $clean['dob'] ?? '' ),
                'status'         => 'pending',
                'created_at'     => current_time( 'mysql' ),
            );
            $wpdb->insert( $p . 'admissions', $row );
            wp_send_json_success( array( 'message' => 'Registration received! The school will review it.' ) );
        }
        wp_send_json_error( array( 'message' => 'Unknown form type.' ) );
    }
}
