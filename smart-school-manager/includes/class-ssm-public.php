<?php
/**
 * Frontend (public-facing) hooks: assets and AJAX endpoints.
 *
 * @package SmartSchoolManager
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

class SSM_Public {

    public function register() {
        add_action( 'wp_enqueue_scripts',           array( $this, 'enqueue_public' ) );
        // Public AJAX (no nonce required for inquiry/admission to work without login).
        add_action( 'wp_ajax_ssm_public_submit',        array( $this, 'submit' ) );
        add_action( 'wp_ajax_nopriv_ssm_public_submit', array( $this, 'submit' ) );
    }

    public function enqueue_public() {
        wp_register_style( 'ssm-public', SSM_PLUGIN_URL . 'assets/css/public.css', array(), SSM_VERSION );
        wp_register_script( 'ssm-public', SSM_PLUGIN_URL . 'assets/js/public.js', array( 'jquery' ), SSM_VERSION, true );

        wp_localize_script( 'ssm-public', 'SSMPublic', array(
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( 'ssm_public_nonce' ),
        ) );

        // Only enqueue when our shortcodes are present.
        if ( $this->has_shortcode() ) {
            wp_enqueue_style( 'ssm-public' );
            wp_enqueue_script( 'ssm-public' );
        }
    }

    private function has_shortcode() {
        global $post;
        if ( ! $post ) return false;
        $tags = array( 'ssm_login', 'ssm_portal', 'ssm_notices', 'ssm_events',
            'ssm_admission_form', 'ssm_inquiry_form', 'ssm_gallery' );
        foreach ( $tags as $t ) {
            if ( has_shortcode( $post->post_content, $t ) ) return true;
        }
        return false;
    }

    /**
     * Public form submission (admission, inquiry).
     */
    public function submit() {
        check_ajax_referer( 'ssm_public_nonce', 'nonce' );

        $type = isset( $_POST['type'] ) ? sanitize_key( $_POST['type'] ) : '';
        $data = isset( $_POST['data'] ) ? (array) $_POST['data'] : array();
        $clean = array();
        foreach ( $data as $k => $v ) {
            $clean[ sanitize_key( $k ) ] = is_array( $v ) ? wp_json_encode( $v ) : sanitize_text_field( wp_unslash( $v ) );
        }

        global $wpdb;
        $p = $wpdb->prefix . 'ssm_';

        if ( 'admission' === $type ) {
            $clean['status']     = 'pending';
            $clean['created_at'] = current_time( 'mysql' );
            $wpdb->insert( $p . 'admissions', $clean );
            wp_send_json_success( array( 'message' => 'Application submitted! We will get in touch soon.' ) );
        }

        if ( 'inquiry' === $type ) {
            $clean['status']     = 'new';
            $clean['created_at'] = current_time( 'mysql' );
            $wpdb->insert( $p . 'inquiries', $clean );
            wp_send_json_success( array( 'message' => 'Thanks! Your inquiry has been recorded.' ) );
        }

        wp_send_json_error( array( 'message' => 'Unknown form type.' ) );
    }
}
