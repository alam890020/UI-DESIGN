<?php
/**
 * Native WordPress dashboard widget summarising SSM stats.
 *
 * @package SmartSchoolManager
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

class SSM_Widgets {

    public function register() {
        add_action( 'wp_dashboard_setup', array( $this, 'add_dashboard_widget' ) );
    }

    public function add_dashboard_widget() {
        wp_add_dashboard_widget(
            'ssm_summary_widget',
            'Smart School — Today',
            array( $this, 'render' )
        );
    }

    public function render() {
        global $wpdb; $p = $wpdb->prefix . 'ssm_';
        $stu     = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$p}students WHERE status='active'" );
        $staff   = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$p}staff WHERE status='active'" );
        $unpaid  = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$p}invoices WHERE status='unpaid'" );
        $tickets = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$p}tickets WHERE status='open'" );

        echo '<style>.ssm-w{display:grid;grid-template-columns:1fr 1fr;gap:10px}.ssm-w div{background:#f4f6fb;border-radius:8px;padding:10px;font-size:13px}.ssm-w b{display:block;font-size:18px;color:#4f46e5}</style>';
        echo '<div class="ssm-w">';
        echo '<div><b>' . esc_html( number_format( $stu ) ) . '</b>Active Students</div>';
        echo '<div><b>' . esc_html( number_format( $staff ) ) . '</b>Active Staff</div>';
        echo '<div><b>' . esc_html( number_format( $unpaid ) ) . '</b>Unpaid Invoices</div>';
        echo '<div><b>' . esc_html( number_format( $tickets ) ) . '</b>Open Tickets</div>';
        echo '</div>';
        echo '<p style="margin-top:10px"><a class="button button-primary" href="' . esc_url( admin_url( 'admin.php?page=ssm-dashboard' ) ) . '">Open Smart School</a></p>';
    }
}
