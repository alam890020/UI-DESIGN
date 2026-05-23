<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
?>
<div class="ssm-page">
    <?php SSM_Helper::page_header( 'Print Center', 'One place to print every document', 'dashicons-printer' ); ?>

    <div class="ssm-grid-3">
        <?php
        $items = array(
            array('Invoices', 'dashicons-media-document', 'ssm-invoices', 'Print individual or bulk invoices'),
            array('Admit Cards', 'dashicons-tickets-alt', 'ssm-admit-cards', 'Generate admit cards for an exam'),
            array('Student ID Cards', 'dashicons-id', 'ssm-id-cards', 'Print branded student ID cards'),
            array('Staff ID Cards', 'dashicons-id-alt', 'ssm-staff-id-cards', 'Print staff ID cards'),
            array('Results & Reports', 'dashicons-chart-line', 'ssm-results', 'Print results and academic reports'),
            array('Certificates', 'dashicons-awards', 'ssm-certificates', 'Generate certificates'),
            array('Transfer Certificates', 'dashicons-migrate', 'ssm-transfer-cert', 'TC printing'),
            array('Attendance Sheets', 'dashicons-yes-alt', 'ssm-attendance', 'Print attendance sheets'),
            array('Fee Structures', 'dashicons-money-alt', 'ssm-fees', 'Print fee structures'),
            array('Timetables', 'dashicons-clock', 'ssm-routines', 'Print class & staff timetables'),
            array('Library Cards', 'dashicons-book-alt', 'ssm-library-cards', 'Print library cards'),
            array('Student Proof IDs', 'dashicons-shield', 'ssm-id-cards', 'Print proof of identity'),
        );
        foreach ( $items as $it ) : ?>
            <a href="<?php echo esc_url( SSM_Helper::admin_url( $it[2] ) ); ?>" class="ssm-card" style="text-align:center;padding:26px;text-decoration:none;color:inherit;display:block">
                <div class="ssm-empty-icon" style="margin-bottom:12px"><span class="dashicons <?php echo esc_attr( $it[1] ); ?>"></span></div>
                <h3 style="margin:0 0 6px"><?php echo esc_html( $it[0] ); ?></h3>
                <p class="ssm-muted" style="margin:0 0 12px;font-size:12px"><?php echo esc_html( $it[3] ); ?></p>
                <span class="ssm-btn ssm-btn-primary"><span class="dashicons dashicons-printer"></span> Open</span>
            </a>
        <?php endforeach; ?>
    </div>
</div>
