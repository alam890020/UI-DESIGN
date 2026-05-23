/**
 * School Softwere — Admin JavaScript
 * Handles tabs, modals, AJAX forms, toasts, confirms, DataTables, charts.
 */
/* global SS, Swal, Toastify, flatpickr, Chart */
(function ($) {
	'use strict';

	/* ── Helpers ───────────────────────────────────────────────────────────── */
	const SS_UI = {

		/** Show a toast notification */
		toast(message, type = 'success') {
			const colors = {
				success: 'linear-gradient(135deg,#10B981,#059669)',
				error:   'linear-gradient(135deg,#EF4444,#DC2626)',
				warning: 'linear-gradient(135deg,#F59E0B,#D97706)',
				info:    'linear-gradient(135deg,#6366F1,#4F46E5)',
			};
			Toastify({
				text: message,
				duration: 3500,
				gravity: 'top',
				position: 'right',
				style: { background: colors[type] || colors.info, borderRadius: '8px', fontSize: '13px', fontFamily: 'Inter,sans-serif' },
				stopOnFocus: true,
			}).showToast();
		},

		/** SweetAlert2 confirm dialog */
		confirm(message, callback, danger = true) {
			Swal.fire({
				title: danger ? SS.i18n.confirm_delete : message,
				text: danger ? message : '',
				icon: danger ? 'warning' : 'question',
				showCancelButton: true,
				confirmButtonText: danger ? SS.i18n.confirm_yes : 'Yes',
				cancelButtonText: SS.i18n.confirm_cancel,
				confirmButtonColor: danger ? '#EF4444' : '#4F46E5',
				cancelButtonColor: '#64748B',
				borderRadius: '12px',
			}).then(result => {
				if (result.isConfirmed) callback();
			});
		},

		/** Open a modal by ID */
		openModal(id) {
			$('#' + id).addClass('open');
			$('body').css('overflow', 'hidden');
		},

		/** Close all modals */
		closeModals() {
			$('.ss-modal-overlay').removeClass('open');
			$('body').css('overflow', '');
		},

		/** Show page loader */
		showLoader() {
			if (!$('#ss-page-loader').length) {
				$('body').append('<div id="ss-page-loader" class="ss-loader"><div class="ss-loader-spinner"></div></div>');
			}
		},

		/** Hide page loader */
		hideLoader() {
			$('#ss-page-loader').remove();
		},

		/** Set button loading state */
		btnLoading($btn, loading = true) {
			if (loading) {
				$btn.data('original-text', $btn.html());
				$btn.html('<span class="spinner"></span> ' + SS.i18n.loading).addClass('loading').prop('disabled', true);
			} else {
				$btn.html($btn.data('original-text')).removeClass('loading').prop('disabled', false);
			}
		},
	};

	/* ── Tabs ──────────────────────────────────────────────────────────────── */
	function initTabs() {
		$(document).on('click', '.ss-tab-btn', function () {
			const $tab = $(this);
			const target = $tab.data('tab');
			const $container = $tab.closest('.ss-tabs-container');
			$container.find('.ss-tab-btn').removeClass('active');
			$container.find('.ss-tab-pane').removeClass('active');
			$tab.addClass('active');
			$container.find('#' + target).addClass('active');
		});
	}

	/* ── Modals ────────────────────────────────────────────────────────────── */
	function initModals() {
		$(document).on('click', '[data-modal]', function (e) {
			e.preventDefault();
			SS_UI.openModal($(this).data('modal'));
		});
		$(document).on('click', '.ss-modal-close, .ss-modal-overlay', function (e) {
			if ($(e.target).hasClass('ss-modal-overlay') || $(e.target).hasClass('ss-modal-close')) {
				SS_UI.closeModals();
			}
		});
		$(document).on('keydown', function (e) {
			if (e.key === 'Escape') SS_UI.closeModals();
		});
	}

	/* ── AJAX Forms ────────────────────────────────────────────────────────── */
	function initAjaxForms() {
		$(document).on('submit', '.ss-ajax-form', function (e) {
			e.preventDefault();
			const $form = $(this);
			const $btn  = $form.find('[type="submit"]');
			$form.find('.ss-error').text('');

			SS_UI.btnLoading($btn, true);
			const data = new FormData($form[0]);
			data.append('nonce', SS.nonce);

			$.ajax({
				url: SS.ajax_url,
				method: 'POST',
				data: data,
				contentType: false,
				processData: false,
				success(res) {
					SS_UI.btnLoading($btn, false);
					if (res.success) {
						SS_UI.toast(res.message || SS.i18n.saved, 'success');
						if ($form.data('reload')) setTimeout(() => location.reload(), 900);
						if ($form.data('close-modal')) SS_UI.closeModals();
						$form.trigger('ss:success', [res]);
					} else {
						SS_UI.toast(res.message || SS.i18n.error, 'error');
						// Show field errors
						if (res.data && res.data.errors) {
							$.each(res.data.errors, (field, msg) => {
								$form.find('[name="' + field + '"]').closest('.ss-form-group').find('.ss-error').text(msg);
							});
						}
					}
				},
				error() {
					SS_UI.btnLoading($btn, false);
					SS_UI.toast(SS.i18n.error, 'error');
				},
			});
		});
	}

	/* ── Delete buttons ────────────────────────────────────────────────────── */
	function initDeleteButtons() {
		$(document).on('click', '.ss-delete-btn', function (e) {
			e.preventDefault();
			const $btn   = $(this);
			const action = $btn.data('action');
			const id     = $btn.data('id');
			SS_UI.confirm('This action cannot be undone.', () => {
				SS_UI.showLoader();
				$.post(SS.ajax_url, { action, id, nonce: SS.nonce }, res => {
					SS_UI.hideLoader();
					if (res.success) {
						SS_UI.toast(res.message || 'Deleted!', 'success');
						const $row = $btn.closest('tr');
						if ($row.length) $row.fadeOut(400, () => $row.remove());
					} else {
						SS_UI.toast(res.message || SS.i18n.error, 'error');
					}
				});
			});
		});
	}

	/* ── DataTables ────────────────────────────────────────────────────────── */
	function initDataTables() {
		$('.ss-datatable').each(function () {
			const $table = $(this);
			if ($.fn.DataTable && !$.fn.DataTable.isDataTable($table)) {
				$table.DataTable({
					responsive: true,
					pageLength: 20,
					language: { search: '', searchPlaceholder: 'Search…', lengthMenu: 'Show _MENU_ entries' },
					dom: '<"ss-dt-top"lf>rt<"ss-dt-bottom"ip>',
				});
			}
		});
	}

	/* ── Select2 ───────────────────────────────────────────────────────────── */
	function initSelect2() {
		$('.ss-select2').select2({ width: '100%', allowClear: true });
	}

	/* ── Flatpickr date/time pickers ───────────────────────────────────────── */
	function initFlatpickr() {
		$('.ss-datepicker').each(function () {
			flatpickr(this, { dateFormat: 'd/m/Y', allowInput: true });
		});
		$('.ss-datetimepicker').each(function () {
			flatpickr(this, { enableTime: true, dateFormat: 'd/m/Y H:i', allowInput: true });
		});
		$('.ss-timepicker').each(function () {
			flatpickr(this, { enableTime: true, noCalendar: true, dateFormat: 'H:i' });
		});
	}

	/* ── Photo preview ─────────────────────────────────────────────────────── */
	function initPhotoPreview() {
		$(document).on('change', '.ss-photo-input', function () {
			const file   = this.files[0];
			const $prev  = $(this).closest('.ss-photo-upload').find('.ss-photo-preview');
			if (file && $prev.length) {
				const reader = new FileReader();
				reader.onload = e => $prev.attr('src', e.target.result);
				reader.readAsDataURL(file);
			}
		});
	}

	/* ── Bulk action checkboxes ────────────────────────────────────────────── */
	function initBulkActions() {
		$(document).on('change', '.ss-check-all', function () {
			$(this).closest('table').find('.ss-check-row').prop('checked', this.checked);
		});
		$(document).on('change', '.ss-check-row', function () {
			const $table = $(this).closest('table');
			const total  = $table.find('.ss-check-row').length;
			const checked = $table.find('.ss-check-row:checked').length;
			$table.find('.ss-check-all').prop('indeterminate', checked > 0 && checked < total);
			$table.find('.ss-check-all').prop('checked', checked === total);
		});

		$(document).on('click', '.ss-bulk-action-btn', function (e) {
			e.preventDefault();
			const $btn    = $(this);
			const action  = $btn.data('action');
			const $form   = $btn.closest('form');
			const checked = $form.find('.ss-check-row:checked').map((_, el) => $(el).val()).get();
			if (!checked.length) {
				SS_UI.toast('Please select at least one row.', 'warning');
				return;
			}
			SS_UI.confirm('Apply bulk action to ' + checked.length + ' item(s)?', () => {
				SS_UI.showLoader();
				$.post(SS.ajax_url, { action, ids: checked, nonce: SS.nonce }, res => {
					SS_UI.hideLoader();
					SS_UI.toast(res.success ? res.message : (res.message || SS.i18n.error), res.success ? 'success' : 'error');
					if (res.success) setTimeout(() => location.reload(), 900);
				});
			}, false);
		});
	}

	/* ── Charts initialisation ─────────────────────────────────────────────── */
	window.SS_Charts = {

		/** Generic line chart */
		line(canvasId, labels, datasets, options = {}) {
			const ctx = document.getElementById(canvasId);
			if (!ctx) return;
			return new Chart(ctx, {
				type: 'line',
				data: { labels, datasets },
				options: $.extend(true, {
					responsive: true,
					maintainAspectRatio: false,
					plugins: { legend: { position: 'top' } },
					scales: {
						y: { beginAtZero: true, grid: { color: '#E0E7FF' } },
						x: { grid: { display: false } },
					},
				}, options),
			});
		},

		/** Doughnut chart */
		doughnut(canvasId, labels, data, colors, options = {}) {
			const ctx = document.getElementById(canvasId);
			if (!ctx) return;
			return new Chart(ctx, {
				type: 'doughnut',
				data: { labels, datasets: [{ data, backgroundColor: colors, borderWidth: 0 }] },
				options: $.extend(true, {
					responsive: true,
					maintainAspectRatio: false,
					plugins: { legend: { position: 'right' } },
					cutout: '68%',
				}, options),
			});
		},

		/** Bar chart */
		bar(canvasId, labels, datasets, options = {}) {
			const ctx = document.getElementById(canvasId);
			if (!ctx) return;
			return new Chart(ctx, {
				type: 'bar',
				data: { labels, datasets },
				options: $.extend(true, {
					responsive: true,
					maintainAspectRatio: false,
					plugins: { legend: { position: 'top' } },
					scales: {
						y: { beginAtZero: true, grid: { color: '#E0E7FF' } },
						x: { grid: { display: false } },
					},
				}, options),
			});
		},
	};

	/* ── Inline edit ───────────────────────────────────────────────────────── */
	function initInlineEdit() {
		$(document).on('click', '.ss-inline-edit-btn', function () {
			const $td    = $(this).closest('td');
			const $span  = $td.find('.ss-inline-val');
			const val    = $span.text();
			const name   = $(this).data('field');
			const action = $(this).data('action');
			const id     = $(this).data('id');

			$span.hide();
			$(this).hide();
			const $input = $('<input type="text" class="ss-inline-input">').val(val);
			const $save  = $('<button class="ss-btn ss-btn-primary ss-btn-sm" style="margin-left:6px">Save</button>');
			$td.append($input).append($save);

			$save.on('click', function () {
				$.post(SS.ajax_url, { action, id, [name]: $input.val(), nonce: SS.nonce }, res => {
					if (res.success) {
						$span.text($input.val()).show();
						$input.remove(); $save.remove();
						$('.ss-inline-edit-btn[data-id="' + id + '"]').show();
						SS_UI.toast(SS.i18n.saved, 'success');
					} else {
						SS_UI.toast(res.message || SS.i18n.error, 'error');
					}
				});
			});
		});
	}

	/* ── School switcher ───────────────────────────────────────────────────── */
	function initSchoolSwitcher() {
		$(document).on('change', '#ss-school-switcher', function () {
			const school_id = $(this).val();
			$.post(SS.ajax_url, { action: 'ss_switch_school', school_id, nonce: SS.nonce }, res => {
				if (res.success) location.reload();
			});
		});
	}

	/* ── Document ready ────────────────────────────────────────────────────── */
	$(function () {
		initTabs();
		initModals();
		initAjaxForms();
		initDeleteButtons();
		initDataTables();
		initSelect2();
		initFlatpickr();
		initPhotoPreview();
		initBulkActions();
		initInlineEdit();
		initSchoolSwitcher();

		// Auto-init charts on dashboard
		$(document).trigger('ss:charts:init');

		// Expose utilities globally
		window.SS_UI = SS_UI;
	});

}(jQuery));
