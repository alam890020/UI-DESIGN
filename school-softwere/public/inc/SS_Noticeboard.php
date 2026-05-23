<?php
/**
 * SS_Noticeboard — [ss_noticeboard] and [ss_events] shortcodes.
 *
 * @package School_Softwere
 */
defined( 'ABSPATH' ) || die();

class SS_Noticeboard {

	public static function render( $atts ) {
		$atts      = shortcode_atts( array( 'school_id' => 0, 'limit' => 10 ), $atts );
		$school_id = $atts['school_id'] ? (int) $atts['school_id'] : SS_Helper::get_current_school_id();
		global $wpdb;
		$notices = $wpdb->get_results( $wpdb->prepare(
			"SELECT * FROM {$wpdb->prefix}ss_notices WHERE school_id=%d ORDER BY date DESC LIMIT %d",
			$school_id, (int) $atts['limit']
		) );
		ob_start();
		?>
		<div class="ss-noticeboard-widget">
			<h3 class="ss-nb-title">📢 <?php esc_html_e( 'School Notices', 'school-softwere' ); ?></h3>
			<?php if ( $notices ) : ?>
			<ul class="ss-nb-list">
				<?php foreach ( $notices as $n ) : ?>
				<li class="ss-nb-item">
					<div class="ss-nb-date"><?php echo esc_html( SS_Helper::format_date( $n->date ) ); ?></div>
					<div class="ss-nb-text">
						<strong><?php echo esc_html( $n->title ); ?></strong>
						<?php if ( $n->description ) : ?><p><?php echo wp_kses_post( wp_trim_words( $n->description, 20 ) ); ?></p><?php endif; ?>
					</div>
				</li>
				<?php endforeach; ?>
			</ul>
			<?php else : ?>
			<p class="ss-nb-empty"><?php esc_html_e( 'No notices at this time.', 'school-softwere' ); ?></p>
			<?php endif; ?>
		</div>
		<?php
		return ob_get_clean();
	}

	public static function render_events( $atts ) {
		$atts      = shortcode_atts( array( 'school_id' => 0, 'limit' => 5 ), $atts );
		$school_id = $atts['school_id'] ? (int) $atts['school_id'] : SS_Helper::get_current_school_id();
		global $wpdb;
		$events = $wpdb->get_results( $wpdb->prepare(
			"SELECT * FROM {$wpdb->prefix}ss_events WHERE school_id=%d AND start_date >= %s ORDER BY start_date ASC LIMIT %d",
			$school_id, current_time( 'Y-m-d' ), (int) $atts['limit']
		) );
		ob_start();
		?>
		<div class="ss-events-widget">
			<h3 class="ss-nb-title">🗓️ <?php esc_html_e( 'Upcoming Events', 'school-softwere' ); ?></h3>
			<?php if ( $events ) : ?>
			<ul class="ss-events-list">
				<?php foreach ( $events as $ev ) : ?>
				<li class="ss-event-item">
					<div class="ss-event-date-badge">
						<span class="ss-event-day"><?php echo esc_html( date( 'd', strtotime( $ev->start_date ) ) ); ?></span>
						<span class="ss-event-month"><?php echo esc_html( date( 'M', strtotime( $ev->start_date ) ) ); ?></span>
					</div>
					<div class="ss-event-info">
						<strong><?php echo esc_html( $ev->title ); ?></strong>
						<?php if ( $ev->venue ) : ?><span class="ss-event-venue">📍 <?php echo esc_html( $ev->venue ); ?></span><?php endif; ?>
					</div>
				</li>
				<?php endforeach; ?>
			</ul>
			<?php else : ?>
			<p class="ss-nb-empty"><?php esc_html_e( 'No upcoming events.', 'school-softwere' ); ?></p>
			<?php endif; ?>
		</div>
		<?php
		return ob_get_clean();
	}
}
