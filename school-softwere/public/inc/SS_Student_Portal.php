<?php
/**
 * SS_Student_Portal — Student/parent frontend dashboard shortcodes.
 *
 * @package School_Softwere
 */
defined( 'ABSPATH' ) || die();

class SS_Student_Portal {

	/** Get logged-in student record or null */
	private static function get_student() {
		if ( ! is_user_logged_in() ) return null;
		global $wpdb;
		return $wpdb->get_row( $wpdb->prepare(
			"SELECT sr.*, c.label AS class_label, sec.label AS section_label
			 FROM {$wpdb->prefix}ss_student_records sr
			 LEFT JOIN {$wpdb->prefix}ss_class_school cs ON sr.class_school_id=cs.ID
			 LEFT JOIN {$wpdb->prefix}ss_classes c ON cs.class_id=c.ID
			 LEFT JOIN {$wpdb->prefix}ss_sections sec ON sr.section_id=sec.ID
			 WHERE sr.user_id=%d LIMIT 1",
			get_current_user_id()
		) );
	}

	private static function login_required() {
		return '<div class="ss-portal-notice">' . sprintf(
			wp_kses( __( 'Please <a href="%s">login</a> to view this page.', 'school-softwere' ), array( 'a' => array( 'href' => array() ) ) ),
			esc_url( home_url( '/login/' ) )
		) . '</div>';
	}

	public static function render_dashboard( $atts ) {
		$student = self::get_student();
		if ( ! $student ) return self::login_required();
		global $wpdb;
		$att_pct = 0;
		$total_days = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->prefix}ss_attendance WHERE student_record_id=%d", $student->ID ) );
		$present    = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->prefix}ss_attendance WHERE student_record_id=%d AND status='present'", $student->ID ) );
		if ( $total_days > 0 ) $att_pct = round( ( $present / $total_days ) * 100 );
		$pending_fees = (float) $wpdb->get_var( $wpdb->prepare( "SELECT COALESCE(SUM(due_amount),0) FROM {$wpdb->prefix}ss_invoices WHERE student_record_id=%d AND status!='paid'", $student->ID ) );
		$homework_count = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->prefix}ss_homework WHERE class_school_id=%d AND section_id=%d AND submission_date >= %s", $student->class_school_id, $student->section_id, current_time( 'Y-m-d' ) ) );
		ob_start(); ?>
		<div class="ss-portal-wrap">
			<div class="ss-portal-welcome">
				<img src="<?php echo $student->photo ? esc_url( $student->photo ) : esc_url( SS_PLUGIN_URL . 'assets/images/default-avatar.png' ); ?>" class="ss-portal-avatar" alt="">
				<div>
					<h2><?php echo esc_html( __( 'Welcome, ', 'school-softwere' ) . $student->first_name . ' ' . $student->last_name ); ?> 👋</h2>
					<p><?php echo esc_html( $student->class_label . ' — ' . $student->section_label ); ?> | <?php esc_html_e( 'Adm. No:', 'school-softwere' ); ?> <?php echo esc_html( $student->admission_number ); ?></p>
				</div>
			</div>
			<div class="ss-portal-stats">
				<div class="ss-portal-stat"><div class="ss-portal-stat-num"><?php echo esc_html( $att_pct ); ?>%</div><div class="ss-portal-stat-label"><?php esc_html_e( 'Attendance', 'school-softwere' ); ?></div></div>
				<div class="ss-portal-stat"><div class="ss-portal-stat-num"><?php echo esc_html( SS_Helper::format_currency( $pending_fees ) ); ?></div><div class="ss-portal-stat-label"><?php esc_html_e( 'Pending Fees', 'school-softwere' ); ?></div></div>
				<div class="ss-portal-stat"><div class="ss-portal-stat-num"><?php echo esc_html( $homework_count ); ?></div><div class="ss-portal-stat-label"><?php esc_html_e( 'Pending Homework', 'school-softwere' ); ?></div></div>
			</div>
		</div>
		<?php return ob_get_clean();
	}

	public static function render_fee_status( $atts ) {
		$student = self::get_student();
		if ( ! $student ) return self::login_required();
		global $wpdb;
		$invoices = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}ss_invoices WHERE student_record_id=%d ORDER BY created_at DESC LIMIT 10", $student->ID ) );
		ob_start(); ?>
		<div class="ss-portal-section">
			<h3>💰 <?php esc_html_e( 'Fee Status', 'school-softwere' ); ?></h3>
			<?php if ( $invoices ) : ?>
			<table class="ss-portal-table">
				<thead><tr><th><?php esc_html_e( 'Invoice', 'school-softwere' ); ?></th><th><?php esc_html_e( 'Total', 'school-softwere' ); ?></th><th><?php esc_html_e( 'Paid', 'school-softwere' ); ?></th><th><?php esc_html_e( 'Due', 'school-softwere' ); ?></th><th><?php esc_html_e( 'Status', 'school-softwere' ); ?></th></tr></thead>
				<tbody><?php foreach ( $invoices as $inv ) : ?><tr><td><?php echo esc_html( $inv->invoice_number ); ?></td><td><?php echo esc_html( SS_Helper::format_currency( $inv->total_amount ) ); ?></td><td><?php echo esc_html( SS_Helper::format_currency( $inv->paid_amount ) ); ?></td><td><?php echo esc_html( SS_Helper::format_currency( $inv->due_amount ) ); ?></td><td><?php echo wp_kses_post( SS_Helper::status_badge( $inv->status ) ); ?></td></tr><?php endforeach; ?></tbody>
			</table>
			<?php else : ?><p><?php esc_html_e( 'No invoices found.', 'school-softwere' ); ?></p><?php endif; ?>
		</div>
		<?php return ob_get_clean();
	}

	public static function render_results( $atts ) {
		$student = self::get_student();
		if ( ! $student ) return self::login_required();
		global $wpdb;
		$results = $wpdb->get_results( $wpdb->prepare(
			"SELECT er.*, ep.total_marks, ep.pass_marks, s.label AS subject_label, e.label AS exam_label FROM {$wpdb->prefix}ss_exam_results er INNER JOIN {$wpdb->prefix}ss_exam_papers ep ON er.exam_paper_id=ep.ID INNER JOIN {$wpdb->prefix}ss_subjects s ON ep.subject_id=s.ID INNER JOIN {$wpdb->prefix}ss_exams e ON ep.exam_id=e.ID WHERE er.student_record_id=%d ORDER BY e.label, s.label",
			$student->ID
		) );
		ob_start(); ?>
		<div class="ss-portal-section">
			<h3>📝 <?php esc_html_e( 'Exam Results', 'school-softwere' ); ?></h3>
			<?php if ( $results ) : ?>
			<table class="ss-portal-table">
				<thead><tr><th><?php esc_html_e( 'Exam', 'school-softwere' ); ?></th><th><?php esc_html_e( 'Subject', 'school-softwere' ); ?></th><th><?php esc_html_e( 'Marks', 'school-softwere' ); ?></th><th><?php esc_html_e( 'Grade', 'school-softwere' ); ?></th><th><?php esc_html_e( 'Result', 'school-softwere' ); ?></th></tr></thead>
				<tbody><?php foreach ( $results as $r ) : $pass = $r->obtained_marks >= $r->pass_marks; ?><tr><td><?php echo esc_html( $r->exam_label ); ?></td><td><?php echo esc_html( $r->subject_label ); ?></td><td><?php echo esc_html( $r->obtained_marks . '/' . $r->total_marks ); ?></td><td><?php echo esc_html( $r->grade ); ?></td><td><?php echo wp_kses_post( SS_Helper::status_badge( $pass ? 'active' : 'inactive' ) ); ?></td></tr><?php endforeach; ?></tbody>
			</table>
			<?php else : ?><p><?php esc_html_e( 'No results found.', 'school-softwere' ); ?></p><?php endif; ?>
		</div>
		<?php return ob_get_clean();
	}

	public static function render_attendance( $atts ) {
		$student = self::get_student();
		if ( ! $student ) return self::login_required();
		global $wpdb;
		$records = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}ss_attendance WHERE student_record_id=%d ORDER BY date DESC LIMIT 30", $student->ID ) );
		ob_start(); ?>
		<div class="ss-portal-section">
			<h3>✅ <?php esc_html_e( 'Attendance', 'school-softwere' ); ?></h3>
			<?php if ( $records ) : ?>
			<table class="ss-portal-table">
				<thead><tr><th><?php esc_html_e( 'Date', 'school-softwere' ); ?></th><th><?php esc_html_e( 'Status', 'school-softwere' ); ?></th></tr></thead>
				<tbody><?php foreach ( $records as $r ) : ?><tr><td><?php echo esc_html( SS_Helper::format_date( $r->date ) ); ?></td><td><?php echo wp_kses_post( SS_Helper::status_badge( $r->status ) ); ?></td></tr><?php endforeach; ?></tbody>
			</table>
			<?php else : ?><p><?php esc_html_e( 'No attendance records found.', 'school-softwere' ); ?></p><?php endif; ?>
		</div>
		<?php return ob_get_clean();
	}

	public static function render_homework( $atts ) {
		$student = self::get_student();
		if ( ! $student ) return self::login_required();
		global $wpdb;
		$homework = $wpdb->get_results( $wpdb->prepare(
			"SELECT hw.*, sub.label AS subject_label FROM {$wpdb->prefix}ss_homework hw LEFT JOIN {$wpdb->prefix}ss_subjects sub ON hw.subject_id=sub.ID WHERE hw.class_school_id=%d AND hw.section_id=%d AND hw.submission_date >= %s ORDER BY hw.submission_date ASC",
			$student->class_school_id, $student->section_id, current_time( 'Y-m-d' )
		) );
		ob_start(); ?>
		<div class="ss-portal-section">
			<h3>📓 <?php esc_html_e( 'Pending Homework', 'school-softwere' ); ?></h3>
			<?php if ( $homework ) : ?>
			<div class="ss-hw-list">
				<?php foreach ( $homework as $hw ) : ?>
				<div class="ss-hw-item">
					<div class="ss-hw-subject"><?php echo esc_html( $hw->subject_label ?? '' ); ?></div>
					<div class="ss-hw-title"><strong><?php echo esc_html( $hw->title ); ?></strong></div>
					<div class="ss-hw-due"><?php esc_html_e( 'Due:', 'school-softwere' ); ?> <?php echo esc_html( SS_Helper::format_date( $hw->submission_date ) ); ?></div>
				</div>
				<?php endforeach; ?>
			</div>
			<?php else : ?><p><?php esc_html_e( 'No pending homework. 🎉', 'school-softwere' ); ?></p><?php endif; ?>
		</div>
		<?php return ob_get_clean();
	}
}
