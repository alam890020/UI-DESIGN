<?php
/**
 * Frontend portal dashboard rendered for logged-in students/parents.
 *
 * @package SmartSchoolManager
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

$user = wp_get_current_user();
global $wpdb; $p = $wpdb->prefix . 'ssm_';

// Try to find a matching student record by email.
$student = null;
if ( $user && $user->user_email ) {
    $student = $wpdb->get_row( $wpdb->prepare(
        "SELECT * FROM {$p}students WHERE email=%s ORDER BY id DESC LIMIT 1",
        $user->user_email
    ) );
}

$invoices = $student ? $wpdb->get_results( $wpdb->prepare(
    "SELECT * FROM {$p}invoices WHERE student_id=%d ORDER BY id DESC LIMIT 5",
    $student->id
) ) : array();

$attendance = $student ? $wpdb->get_results( $wpdb->prepare(
    "SELECT * FROM {$p}attendance WHERE student_id=%d ORDER BY date DESC LIMIT 5",
    $student->id
) ) : array();

$notices = $wpdb->get_results( "SELECT * FROM {$p}notices ORDER BY id DESC LIMIT 5" );
?>
<div class="ssm-public ssm-portal">
    <header class="ssm-portal-head">
        <div>
            <h2>Welcome, <?php echo esc_html( $user->display_name ); ?></h2>
            <p>Your school portal &mdash; quick view of fees, attendance and notices.</p>
        </div>
        <a class="ssm-public-btn" href="<?php echo esc_url( wp_logout_url( home_url() ) ); ?>">Sign Out</a>
    </header>

    <?php if ( ! $student ) : ?>
        <div class="ssm-public-card">
            <p>We couldn't find a student record linked to <strong><?php echo esc_html( $user->user_email ); ?></strong>. Please contact the school office to link your account.</p>
        </div>
    <?php else : ?>
        <div class="ssm-portal-grid">
            <article class="ssm-public-card">
                <h3>Invoices</h3>
                <?php if ( $invoices ) : ?>
                    <ul>
                        <?php foreach ( $invoices as $i ) : ?>
                            <li><strong>#<?php echo esc_html( $i->invoice_no ); ?></strong>
                                &mdash; <?php echo esc_html( SSM_Helper::money( $i->amount ) ); ?>
                                &middot; <span class="pill <?php echo esc_attr( $i->status ); ?>"><?php echo esc_html( ucfirst( $i->status ) ); ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else : ?>
                    <p>No invoices on file.</p>
                <?php endif; ?>
            </article>

            <article class="ssm-public-card">
                <h3>Recent Attendance</h3>
                <?php if ( $attendance ) : ?>
                    <ul>
                        <?php foreach ( $attendance as $a ) : ?>
                            <li><?php echo esc_html( $a->date ); ?>
                                &middot; <span class="pill <?php echo esc_attr( $a->status ); ?>"><?php echo esc_html( ucfirst( $a->status ) ); ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else : ?>
                    <p>No attendance recorded yet.</p>
                <?php endif; ?>
            </article>

            <article class="ssm-public-card ssm-portal-wide">
                <h3>Notice Board</h3>
                <?php if ( $notices ) : foreach ( $notices as $n ) : ?>
                    <div class="ssm-portal-notice">
                        <strong><?php echo esc_html( $n->title ); ?></strong>
                        <small><?php echo esc_html( $n->posted_at ); ?></small>
                        <p><?php echo wp_kses_post( wpautop( $n->body ) ); ?></p>
                    </div>
                <?php endforeach; else : ?>
                    <p>No notices yet.</p>
                <?php endif; ?>
            </article>
        </div>
    <?php endif; ?>
</div>
