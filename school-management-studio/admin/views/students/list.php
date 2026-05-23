<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $wpdb; $p = $wpdb->prefix . 'sms_';
$classes = $wpdb->get_results( "SELECT id, name, section FROM {$p}classes ORDER BY name" );
$students = $wpdb->get_results( "SELECT s.*, c.name AS class_name FROM {$p}students s LEFT JOIN {$p}classes c ON c.id=s.class_id ORDER BY s.id DESC LIMIT 100" );
$total = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$p}students" );
$active = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$p}students WHERE status='active'" );
$male = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$p}students WHERE gender='male'" );
$female = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$p}students WHERE gender='female'" );
?>
<div class="sms-page">
    <?php SMS_Helper::page_header( 'Students', 'All enrolled students', 'users',
        array( array( 'href' => SMS_Helper::admin_url('sms-student-form'), 'label' => 'New Student', 'icon' => 'user-plus', 'class' => 'sms-btn-primary' ) ) ); ?>

    <div class="sms-stats">
        <?php
        echo SMS_Helper::stat( 'Total', number_format( $total ), 'users', 'violet' );
        echo SMS_Helper::stat( 'Active', number_format( $active ), 'check', 'mint' );
        echo SMS_Helper::stat( 'Male', number_format( $male ), 'user', 'blue' );
        echo SMS_Helper::stat( 'Female', number_format( $female ), 'user', 'pink' );
        ?>
    </div>

    <div class="sms-card">
        <div class="sms-card-head">
            <div class="sms-card-title"><?php echo SMS_Icons::svg('list',18); ?> All Students</div>
            <div class="sms-flex">
                <select class="sms-select" style="min-width:140px">
                    <option>All Classes</option>
                    <?php foreach ( $classes as $c ) : ?><option><?php echo esc_html( $c->name . ' - ' . $c->section ); ?></option><?php endforeach; ?>
                </select>
                <input class="sms-input" placeholder="Search..." style="min-width:200px">
            </div>
        </div>
        <div class="sms-table-wrap">
            <table class="sms-table">
                <thead><tr><th>Student</th><th>Adm No</th><th>Class</th><th>Phone</th><th>Gender</th><th>Status</th><th></th></tr></thead>
                <tbody>
                <?php if ( $students ) : foreach ( $students as $s ) : $name = trim( $s->first_name . ' ' . $s->last_name ); ?>
                    <tr>
                        <td><div class="sms-row-name"><?php echo SMS_Helper::avatar( $name, $s->photo ); ?> <?php echo esc_html( $name ); ?></div></td>
                        <td><?php echo esc_html( $s->admission_no ); ?></td>
                        <td><?php echo esc_html( $s->class_name ); ?></td>
                        <td><?php echo esc_html( $s->phone ); ?></td>
                        <td><?php echo esc_html( ucfirst( $s->gender ) ); ?></td>
                        <td><?php echo SMS_Helper::badge( $s->status ); ?></td>
                        <td><div class="sms-row-actions">
                            <a href="<?php echo esc_url( SMS_Helper::print_url('student-id-card', $s->id, true) ); ?>" target="_blank" title="ID Card"><?php echo SMS_Icons::svg('card',14); ?></a>
                            <a href="#" title="Edit"><?php echo SMS_Icons::svg('edit',14); ?></a>
                            <a href="#" class="del" data-entity="student" data-id="<?php echo (int) $s->id; ?>" title="Delete"><?php echo SMS_Icons::svg('trash',14); ?></a>
                        </div></td>
                    </tr>
                <?php endforeach; else : ?>
                    <tr><td colspan="7" class="sms-muted" style="text-align:center;padding:30px">No students yet — <a href="<?php echo esc_url( SMS_Helper::admin_url('sms-student-form') ); ?>">add the first one</a>.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
