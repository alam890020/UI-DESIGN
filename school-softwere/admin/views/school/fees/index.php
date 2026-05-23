<?php
/**
 * Fees & Accounting View
 *
 * @package School_Softwere
 */
defined( 'ABSPATH' ) || die();

global $wpdb;
$school_id  = SS_Helper::get_current_school_id();
$tab        = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'fees';

// Fee structures
$session_id = SS_Helper::get_active_session_id( $school_id );
$classes    = $wpdb->get_results( $wpdb->prepare( "SELECT cs.ID, c.label FROM {$wpdb->prefix}ss_class_school cs INNER JOIN {$wpdb->prefix}ss_classes c ON cs.class_id=c.ID WHERE cs.school_id=%d AND cs.session_id=%d ORDER BY c.label", $school_id, $session_id ) );

// Invoices
$invoices   = $wpdb->get_results( $wpdb->prepare(
	"SELECT i.*, CONCAT(sr.first_name,' ',sr.last_name) AS student_name, sr.admission_number FROM {$wpdb->prefix}ss_invoices i INNER JOIN {$wpdb->prefix}ss_student_records sr ON i.student_record_id=sr.ID WHERE i.school_id=%d ORDER BY i.created_at DESC LIMIT 50",
	$school_id
) );

// Expenses
$expenses   = $wpdb->get_results( $wpdb->prepare( "SELECT e.*, ec.label AS category_label FROM {$wpdb->prefix}ss_expenses e LEFT JOIN {$wpdb->prefix}ss_expense_categories ec ON e.expense_category_id=ec.ID WHERE e.school_id=%d ORDER BY e.date DESC LIMIT 30", $school_id ) );
$exp_cats   = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}ss_expense_categories WHERE school_id=%d ORDER BY label", $school_id ) );
$inc_cats   = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}ss_income_categories WHERE school_id=%d ORDER BY label", $school_id ) );
?>
<div class="ss-wrap">
	<?php include SS_PLUGIN_DIR . 'admin/views/partials/header.php'; ?>
	<div class="ss-page-content">
		<div class="ss-page-title-row">
			<div>
				<h1 class="ss-page-title">💰 <?php esc_html_e( 'Fees & Accounting', 'school-softwere' ); ?></h1>
				<p class="ss-breadcrumb"><a href="<?php echo esc_url( admin_url( 'admin.php?page=school-softwere' ) ); ?>"><?php esc_html_e( 'Dashboard', 'school-softwere' ); ?></a> › <?php esc_html_e( 'Fees', 'school-softwere' ); ?></p>
			</div>
		</div>

		<div class="ss-tabs-container">
			<div class="ss-tabs">
				<button class="ss-tab-btn <?php echo 'fees' === $tab ? 'active' : ''; ?>" data-tab="tab-fees">📋 <?php esc_html_e( 'Fee Structure', 'school-softwere' ); ?></button>
				<button class="ss-tab-btn <?php echo 'invoices' === $tab ? 'active' : ''; ?>" data-tab="tab-invoices">🧾 <?php esc_html_e( 'Invoices', 'school-softwere' ); ?></button>
				<button class="ss-tab-btn <?php echo 'expenses' === $tab ? 'active' : ''; ?>" data-tab="tab-expenses">💸 <?php esc_html_e( 'Expenses', 'school-softwere' ); ?></button>
				<button class="ss-tab-btn <?php echo 'income' === $tab ? 'active' : ''; ?>" data-tab="tab-income">📥 <?php esc_html_e( 'Income', 'school-softwere' ); ?></button>
			</div>

			<!-- Fee Structure Tab -->
			<div id="tab-fees" class="ss-tab-pane <?php echo 'fees' === $tab ? 'active' : ''; ?>" style="margin-top:20px;">
				<div style="display:flex;justify-content:flex-end;margin-bottom:16px;">
					<button class="ss-btn ss-btn-primary" data-modal="ss-fee-modal">➕ <?php esc_html_e( 'Add Fee', 'school-softwere' ); ?></button>
				</div>
				<?php
				$fees = $wpdb->get_results( $wpdb->prepare(
					"SELECT f.*, c.label AS class_label FROM {$wpdb->prefix}ss_fees f INNER JOIN {$wpdb->prefix}ss_class_school cs ON f.class_school_id=cs.ID INNER JOIN {$wpdb->prefix}ss_classes c ON cs.class_id=c.ID WHERE cs.school_id=%d ORDER BY c.label, f.label",
					$school_id
				) );
				?>
				<div class="ss-table-wrap">
					<table class="ss-table">
						<thead><tr><th>#</th><th><?php esc_html_e( 'Fee Name', 'school-softwere' ); ?></th><th><?php esc_html_e( 'Class', 'school-softwere' ); ?></th><th><?php esc_html_e( 'Amount', 'school-softwere' ); ?></th><th><?php esc_html_e( 'Due Date', 'school-softwere' ); ?></th><th><?php esc_html_e( 'Recurring', 'school-softwere' ); ?></th><th><?php esc_html_e( 'Actions', 'school-softwere' ); ?></th></tr></thead>
						<tbody>
						<?php if ( $fees ) : $n=1; foreach ( $fees as $f ) : ?>
						<tr><td><?php echo esc_html( $n++ ); ?></td><td><?php echo esc_html( $f->label ); ?></td><td><?php echo esc_html( $f->class_label ); ?></td><td><?php echo esc_html( SS_Helper::format_currency( $f->amount ) ); ?></td><td><?php echo esc_html( SS_Helper::format_date( $f->due_date ) ); ?></td><td><?php echo wp_kses_post( SS_Helper::status_badge( $f->is_recurring ? 'active' : 'inactive' ) ); ?></td>
						<td><button class="ss-btn ss-btn-danger ss-btn-sm ss-btn-icon ss-delete-btn" data-action="ss_delete_fee" data-id="<?php echo esc_attr( $f->ID ); ?>">🗑️</button></td></tr>
						<?php endforeach; else : ?><tr><td colspan="7" style="text-align:center;padding:24px;color:var(--ss-text-muted);"><?php esc_html_e( 'No fees found.', 'school-softwere' ); ?></td></tr><?php endif; ?>
						</tbody>
					</table>
				</div>
			</div>

			<!-- Invoices Tab -->
			<div id="tab-invoices" class="ss-tab-pane <?php echo 'invoices' === $tab ? 'active' : ''; ?>" style="margin-top:20px;">
				<div class="ss-table-wrap">
					<table class="ss-table">
						<thead><tr><th>#</th><th><?php esc_html_e( 'Invoice #', 'school-softwere' ); ?></th><th><?php esc_html_e( 'Student', 'school-softwere' ); ?></th><th><?php esc_html_e( 'Total', 'school-softwere' ); ?></th><th><?php esc_html_e( 'Paid', 'school-softwere' ); ?></th><th><?php esc_html_e( 'Due', 'school-softwere' ); ?></th><th><?php esc_html_e( 'Status', 'school-softwere' ); ?></th><th><?php esc_html_e( 'Actions', 'school-softwere' ); ?></th></tr></thead>
						<tbody>
						<?php if ( $invoices ) : $n=1; foreach ( $invoices as $inv ) : ?>
						<tr>
							<td><?php echo esc_html( $n++ ); ?></td>
							<td><code><?php echo esc_html( $inv->invoice_number ); ?></code></td>
							<td><?php echo esc_html( $inv->student_name ); ?><br><small style="color:var(--ss-text-muted);"><?php echo esc_html( $inv->admission_number ); ?></small></td>
							<td><?php echo esc_html( SS_Helper::format_currency( $inv->total_amount ) ); ?></td>
							<td style="color:var(--ss-success);"><?php echo esc_html( SS_Helper::format_currency( $inv->paid_amount ) ); ?></td>
							<td style="color:var(--ss-danger);"><?php echo esc_html( SS_Helper::format_currency( $inv->due_amount ) ); ?></td>
							<td><?php echo wp_kses_post( SS_Helper::status_badge( $inv->status ) ); ?></td>
							<td>
								<a href="<?php echo esc_url( SS_PLUGIN_URL . 'admin/print/invoice.php?id=' . $inv->ID ); ?>" target="_blank" class="ss-btn ss-btn-secondary ss-btn-sm ss-btn-icon" title="Print">🖨️</a>
								<?php if ( $inv->status !== 'paid' ) : ?>
								<button class="ss-btn ss-btn-success ss-btn-sm ss-collect-payment" data-id="<?php echo esc_attr( $inv->ID ); ?>" data-due="<?php echo esc_attr( $inv->due_amount ); ?>" title="Collect">💳</button>
								<?php endif; ?>
							</td>
						</tr>
						<?php endforeach; else : ?><tr><td colspan="8" style="text-align:center;padding:24px;color:var(--ss-text-muted);"><?php esc_html_e( 'No invoices found.', 'school-softwere' ); ?></td></tr><?php endif; ?>
						</tbody>
					</table>
				</div>
			</div>

			<!-- Expenses Tab -->
			<div id="tab-expenses" class="ss-tab-pane <?php echo 'expenses' === $tab ? 'active' : ''; ?>" style="margin-top:20px;">
				<div style="display:flex;justify-content:flex-end;margin-bottom:16px;">
					<button class="ss-btn ss-btn-primary" data-modal="ss-expense-modal">➕ <?php esc_html_e( 'Add Expense', 'school-softwere' ); ?></button>
				</div>
				<div class="ss-table-wrap">
					<table class="ss-table">
						<thead><tr><th>#</th><th><?php esc_html_e( 'Title', 'school-softwere' ); ?></th><th><?php esc_html_e( 'Category', 'school-softwere' ); ?></th><th><?php esc_html_e( 'Amount', 'school-softwere' ); ?></th><th><?php esc_html_e( 'Date', 'school-softwere' ); ?></th><th><?php esc_html_e( 'Actions', 'school-softwere' ); ?></th></tr></thead>
						<tbody>
						<?php if ( $expenses ) : $n=1; foreach ( $expenses as $ex ) : ?>
						<tr><td><?php echo esc_html( $n++ ); ?></td><td><?php echo esc_html( $ex->label ); ?></td><td><?php echo esc_html( $ex->category_label ?? '—' ); ?></td><td><?php echo esc_html( SS_Helper::format_currency( $ex->amount ) ); ?></td><td><?php echo esc_html( SS_Helper::format_date( $ex->date ) ); ?></td>
						<td><button class="ss-btn ss-btn-danger ss-btn-sm ss-btn-icon ss-delete-btn" data-action="ss_delete_expense" data-id="<?php echo esc_attr( $ex->ID ); ?>">🗑️</button></td></tr>
						<?php endforeach; else : ?><tr><td colspan="6" style="text-align:center;padding:24px;color:var(--ss-text-muted);"><?php esc_html_e( 'No expenses.', 'school-softwere' ); ?></td></tr><?php endif; ?>
						</tbody>
					</table>
				</div>
			</div>

			<!-- Income Tab -->
			<div id="tab-income" class="ss-tab-pane <?php echo 'income' === $tab ? 'active' : ''; ?>" style="margin-top:20px;">
				<div style="display:flex;justify-content:flex-end;margin-bottom:16px;">
					<button class="ss-btn ss-btn-primary" data-modal="ss-income-modal">➕ <?php esc_html_e( 'Add Income', 'school-softwere' ); ?></button>
				</div>
				<?php $incomes = $wpdb->get_results( $wpdb->prepare( "SELECT i.*, ic.label AS cat_label FROM {$wpdb->prefix}ss_income i LEFT JOIN {$wpdb->prefix}ss_income_categories ic ON i.income_category_id=ic.ID WHERE i.school_id=%d ORDER BY i.date DESC LIMIT 30", $school_id ) ); ?>
				<div class="ss-table-wrap">
					<table class="ss-table">
						<thead><tr><th>#</th><th><?php esc_html_e( 'Title', 'school-softwere' ); ?></th><th><?php esc_html_e( 'Category', 'school-softwere' ); ?></th><th><?php esc_html_e( 'Amount', 'school-softwere' ); ?></th><th><?php esc_html_e( 'Date', 'school-softwere' ); ?></th><th><?php esc_html_e( 'Actions', 'school-softwere' ); ?></th></tr></thead>
						<tbody>
						<?php if ( $incomes ) : $n=1; foreach ( $incomes as $inc ) : ?>
						<tr><td><?php echo esc_html( $n++ ); ?></td><td><?php echo esc_html( $inc->label ); ?></td><td><?php echo esc_html( $inc->cat_label ?? '—' ); ?></td><td><?php echo esc_html( SS_Helper::format_currency( $inc->amount ) ); ?></td><td><?php echo esc_html( SS_Helper::format_date( $inc->date ) ); ?></td>
						<td><button class="ss-btn ss-btn-danger ss-btn-sm ss-btn-icon ss-delete-btn" data-action="ss_delete_income" data-id="<?php echo esc_attr( $inc->ID ); ?>">🗑️</button></td></tr>
						<?php endforeach; else : ?><tr><td colspan="6" style="text-align:center;padding:24px;color:var(--ss-text-muted);"><?php esc_html_e( 'No income records.', 'school-softwere' ); ?></td></tr><?php endif; ?>
						</tbody>
					</table>
				</div>
			</div>
		</div><!-- .ss-tabs-container -->
	</div>
</div>

<!-- Fee Modal -->
<div id="ss-fee-modal" class="ss-modal-overlay"><div class="ss-modal"><div class="ss-modal-header"><h3 class="ss-modal-title">➕ <?php esc_html_e( 'Add Fee', 'school-softwere' ); ?></h3><button class="ss-modal-close">✕</button></div>
<div class="ss-modal-body"><form class="ss-ajax-form" data-reload="1" data-close-modal="1">
<input type="hidden" name="action" value="ss_save_fee">
<div class="ss-form-row">
<div class="ss-form-group"><label><?php esc_html_e( 'Fee Name', 'school-softwere' ); ?> <span class="req">*</span></label><input type="text" name="label" required></div>
<div class="ss-form-group"><label><?php esc_html_e( 'Class', 'school-softwere' ); ?> <span class="req">*</span></label><select name="class_school_id" required class="ss-select2"><option value=""><?php esc_html_e( '— Select —', 'school-softwere' ); ?></option><?php foreach ( $classes as $c ) : ?><option value="<?php echo esc_attr( $c->ID ); ?>"><?php echo esc_html( $c->label ); ?></option><?php endforeach; ?></select></div>
</div>
<div class="ss-form-row">
<div class="ss-form-group"><label><?php esc_html_e( 'Amount', 'school-softwere' ); ?></label><input type="number" step="0.01" name="amount"></div>
<div class="ss-form-group"><label><?php esc_html_e( 'Due Date', 'school-softwere' ); ?></label><input type="text" name="due_date" class="ss-datepicker"></div>
</div>
<div class="ss-form-row">
<div class="ss-form-group"><label><?php esc_html_e( 'Recurring?', 'school-softwere' ); ?></label><select name="is_recurring"><option value="0"><?php esc_html_e( 'No', 'school-softwere' ); ?></option><option value="1"><?php esc_html_e( 'Yes', 'school-softwere' ); ?></option></select></div>
<div class="ss-form-group"><label><?php esc_html_e( 'Frequency', 'school-softwere' ); ?></label><select name="frequency"><option value="monthly"><?php esc_html_e( 'Monthly', 'school-softwere' ); ?></option><option value="quarterly"><?php esc_html_e( 'Quarterly', 'school-softwere' ); ?></option><option value="yearly"><?php esc_html_e( 'Yearly', 'school-softwere' ); ?></option></select></div>
</div>
<div style="display:flex;justify-content:flex-end;gap:10px;margin-top:16px;"><button type="button" class="ss-btn ss-btn-secondary ss-modal-close"><?php esc_html_e( 'Cancel', 'school-softwere' ); ?></button><button type="submit" class="ss-btn ss-btn-primary">💾 <?php esc_html_e( 'Save', 'school-softwere' ); ?></button></div>
</form></div></div></div>

<!-- Expense Modal -->
<div id="ss-expense-modal" class="ss-modal-overlay"><div class="ss-modal"><div class="ss-modal-header"><h3 class="ss-modal-title">💸 <?php esc_html_e( 'Add Expense', 'school-softwere' ); ?></h3><button class="ss-modal-close">✕</button></div>
<div class="ss-modal-body"><form class="ss-ajax-form" data-reload="1" data-close-modal="1">
<input type="hidden" name="action" value="ss_save_expense"><input type="hidden" name="school_id" value="<?php echo esc_attr( $school_id ); ?>">
<div class="ss-form-row"><div class="ss-form-group"><label><?php esc_html_e( 'Title', 'school-softwere' ); ?> <span class="req">*</span></label><input type="text" name="label" required></div>
<div class="ss-form-group"><label><?php esc_html_e( 'Category', 'school-softwere' ); ?></label><select name="expense_category_id" class="ss-select2"><option value=""><?php esc_html_e( '— Select —', 'school-softwere' ); ?></option><?php foreach ( $exp_cats as $c ) : ?><option value="<?php echo esc_attr( $c->ID ); ?>"><?php echo esc_html( $c->label ); ?></option><?php endforeach; ?></select></div></div>
<div class="ss-form-row"><div class="ss-form-group"><label><?php esc_html_e( 'Amount', 'school-softwere' ); ?></label><input type="number" step="0.01" name="amount"></div>
<div class="ss-form-group"><label><?php esc_html_e( 'Date', 'school-softwere' ); ?></label><input type="text" name="date" class="ss-datepicker"></div></div>
<div class="ss-form-group"><label><?php esc_html_e( 'Note', 'school-softwere' ); ?></label><textarea name="note"></textarea></div>
<div style="display:flex;justify-content:flex-end;gap:10px;margin-top:16px;"><button type="button" class="ss-btn ss-btn-secondary ss-modal-close"><?php esc_html_e( 'Cancel', 'school-softwere' ); ?></button><button type="submit" class="ss-btn ss-btn-primary">💾 <?php esc_html_e( 'Save', 'school-softwere' ); ?></button></div>
</form></div></div></div>

<!-- Income Modal -->
<div id="ss-income-modal" class="ss-modal-overlay"><div class="ss-modal"><div class="ss-modal-header"><h3 class="ss-modal-title">📥 <?php esc_html_e( 'Add Income', 'school-softwere' ); ?></h3><button class="ss-modal-close">✕</button></div>
<div class="ss-modal-body"><form class="ss-ajax-form" data-reload="1" data-close-modal="1">
<input type="hidden" name="action" value="ss_save_income"><input type="hidden" name="school_id" value="<?php echo esc_attr( $school_id ); ?>">
<div class="ss-form-row"><div class="ss-form-group"><label><?php esc_html_e( 'Title', 'school-softwere' ); ?> <span class="req">*</span></label><input type="text" name="label" required></div>
<div class="ss-form-group"><label><?php esc_html_e( 'Category', 'school-softwere' ); ?></label><select name="income_category_id" class="ss-select2"><option value=""><?php esc_html_e( '— Select —', 'school-softwere' ); ?></option><?php foreach ( $inc_cats as $c ) : ?><option value="<?php echo esc_attr( $c->ID ); ?>"><?php echo esc_html( $c->label ); ?></option><?php endforeach; ?></select></div></div>
<div class="ss-form-row"><div class="ss-form-group"><label><?php esc_html_e( 'Amount', 'school-softwere' ); ?></label><input type="number" step="0.01" name="amount"></div>
<div class="ss-form-group"><label><?php esc_html_e( 'Date', 'school-softwere' ); ?></label><input type="text" name="date" class="ss-datepicker"></div></div>
<div class="ss-form-group"><label><?php esc_html_e( 'Note', 'school-softwere' ); ?></label><textarea name="note"></textarea></div>
<div style="display:flex;justify-content:flex-end;gap:10px;margin-top:16px;"><button type="button" class="ss-btn ss-btn-secondary ss-modal-close"><?php esc_html_e( 'Cancel', 'school-softwere' ); ?></button><button type="submit" class="ss-btn ss-btn-primary">💾 <?php esc_html_e( 'Save', 'school-softwere' ); ?></button></div>
</form></div></div></div>
