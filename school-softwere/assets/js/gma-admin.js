/* ================================================================
   GMA School Management — Admin JS
   ================================================================ */

(function ($) {
  'use strict';

  // ── GMA_UI ─────────────────────────────────────────────────────
  window.GMA_UI = {
    openModal: function (id) {
      $('#' + id).addClass('is-open');
      $('body').css('overflow', 'hidden');
    },
    closeModal: function (id) {
      $('#' + id).removeClass('is-open');
      $('body').css('overflow', '');
    },
    closeAllModals: function () {
      $('.gma-modal-overlay').removeClass('is-open');
      $('body').css('overflow', '');
    },
    toast: function (msg, type) {
      if (typeof Toastify === 'undefined') { alert(msg); return; }
      var colors = { success: '#10B981', error: '#EF4444', warning: '#F59E0B', info: '#3B82F6' };
      Toastify({
        text: msg,
        duration: 3500,
        gravity: 'top',
        position: 'right',
        stopOnFocus: true,
        style: { background: colors[type] || colors.info, borderRadius: '10px', fontFamily: 'Inter,sans-serif', fontSize: '13px' }
      }).showToast();
    },
    confirm: function (msg, cb) {
      if (typeof Swal === 'undefined') { if (confirm(msg)) cb(); return; }
      Swal.fire({
        title: GMA.i18n.confirm_delete || 'Delete?',
        text: msg || '',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: GMA.i18n.confirm_yes || 'Yes',
        cancelButtonText: GMA.i18n.confirm_cancel || 'Cancel',
        confirmButtonColor: '#EF4444'
      }).then(function (r) { if (r.isConfirmed) cb(); });
    },
    loading: function ($btn, state) {
      if (state) {
        $btn.prop('disabled', true).data('orig', $btn.html()).html('⏳ ' + (GMA.i18n.loading || 'Loading…'));
      } else {
        $btn.prop('disabled', false).html($btn.data('orig') || $btn.html());
      }
    },
    resetForm: function ($form) {
      $form[0].reset();
      $form.find('input[type=hidden]:not([name=action]):not([name=nonce])').val('');
      $form.find('.gma-error').text('');
      $form.find('.gma-photo-preview').attr('src', GMA.plugin_url + 'assets/images/default-avatar.png');
      if ($.fn.select2) $form.find('.gma-select2').trigger('change');
    }
  };

  // ── GMA_Ajax ───────────────────────────────────────────────────
  window.GMA_Ajax = {
    post: function (action, data, cb, errCb) {
      $.ajax({
        url: GMA.ajax_url,
        method: 'POST',
        data: $.extend({ action: action, nonce: GMA.nonce }, data),
        success: function (res) {
          if (res.success) { cb(res.data || res); }
          else { if (errCb) errCb(res); else GMA_UI.toast((res.data && res.data.message) || GMA.i18n.error, 'error'); }
        },
        error: function () { GMA_UI.toast(GMA.i18n.error, 'error'); }
      });
    },
    postForm: function ($form, cb) {
      var fd = new FormData($form[0]);
      fd.append('nonce', GMA.nonce);
      $.ajax({
        url: GMA.ajax_url,
        method: 'POST',
        data: fd,
        contentType: false,
        processData: false,
        success: function (res) {
          if (res.success) { cb(res.data || res); }
          else { GMA_UI.toast((res.data && res.data.message) || GMA.i18n.error, 'error'); }
        },
        error: function () { GMA_UI.toast(GMA.i18n.error, 'error'); }
      });
    }
  };

  // ── GMA_Charts ─────────────────────────────────────────────────
  window.GMA_Charts = {
    line: function (canvasId, labels, datasets) {
      var ctx = document.getElementById(canvasId);
      if (!ctx || typeof Chart === 'undefined') return;
      return new Chart(ctx, {
        type: 'line',
        data: { labels: labels, datasets: datasets },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: datasets.length > 1 } }, scales: { y: { beginAtZero: true } } }
      });
    },
    bar: function (canvasId, labels, datasets) {
      var ctx = document.getElementById(canvasId);
      if (!ctx || typeof Chart === 'undefined') return;
      return new Chart(ctx, {
        type: 'bar',
        data: { labels: labels, datasets: datasets },
        options: { responsive: true, maintainAspectRatio: false }
      });
    },
    doughnut: function (canvasId, labels, data, colors) {
      var ctx = document.getElementById(canvasId);
      if (!ctx || typeof Chart === 'undefined') return;
      return new Chart(ctx, {
        type: 'doughnut',
        data: { labels: labels, datasets: [{ data: data, backgroundColor: colors, borderWidth: 0 }] },
        options: { responsive: true, maintainAspectRatio: false, cutout: '65%' }
      });
    }
  };

  // ── DOM Ready ─────────────────────────────────────────────────
  $(function () {

    // Init Select2
    if ($.fn.select2) {
      $('.gma-select2').select2({ width: '100%' });
    }

    // Init Flatpickr
    if (typeof flatpickr !== 'undefined') {
      $('.gma-datepicker').flatpickr({ dateFormat: 'Y-m-d', allowInput: true });
    }

    // Init DataTables
    if ($.fn.DataTable) {
      $('.gma-datatable').DataTable({ responsive: true, pageLength: 25, language: { search: '🔍' } });
    }

    // Trigger chart init event
    $(document).trigger('gma:charts:init');

    // ── Modal open ───────────────────────────────────────────────
    $(document).on('click', '[data-modal]', function (e) {
      e.preventDefault();
      var id = $(this).data('modal');
      GMA_UI.openModal(id);
    });

    // ── Modal close ──────────────────────────────────────────────
    $(document).on('click', '.gma-modal-close, .gma-modal-overlay', function (e) {
      if (e.target === this) GMA_UI.closeAllModals();
    });

    // ── AJAX form submit ─────────────────────────────────────────
    $(document).on('submit', '.gma-ajax-form', function (e) {
      e.preventDefault();
      var $form   = $(this);
      var $submit = $form.find('[type=submit]');
      GMA_UI.loading($submit, true);

      GMA_Ajax.postForm($form, function (data) {
        GMA_UI.loading($submit, false);
        GMA_UI.toast(data.message || GMA.i18n.saved, 'success');
        if ($form.data('close-modal')) GMA_UI.closeAllModals();
        if ($form.data('reload'))      location.reload();
        if ($form.data('redirect'))    location.href = $form.data('redirect');
      });
    });

    // ── Delete button ─────────────────────────────────────────────
    $(document).on('click', '.gma-delete-btn', function () {
      var $btn   = $(this);
      var action = $btn.data('action');
      var id     = $btn.data('id');
      GMA_UI.confirm('', function () {
        GMA_Ajax.post(action, { id: id }, function (data) {
          GMA_UI.toast(data.message || 'Deleted.', 'success');
          $btn.closest('tr').fadeOut(300, function () { $(this).remove(); });
        });
      });
    });

    // ── Toggle active ─────────────────────────────────────────────
    $(document).on('change', '.gma-toggle-active', function () {
      var $el    = $(this);
      var action = $el.data('action');
      var id     = $el.data('id');
      var val    = $el.is(':checked') ? 1 : 0;
      GMA_Ajax.post(action, { id: id, is_active: val }, function (data) {
        GMA_UI.toast(data.message, 'success');
      });
    });

    // ── Check all ─────────────────────────────────────────────────
    $(document).on('change', '.gma-check-all', function () {
      $(this).closest('table').find('.gma-check-row').prop('checked', this.checked);
    });

    // ── Bulk action ───────────────────────────────────────────────
    $(document).on('click', '.gma-bulk-action-btn', function () {
      var $btn     = $(this);
      var $form    = $btn.closest('form');
      var action   = $btn.data('action');
      var bulk_action = $form.find('[name="bulk_action"]').val();
      if (!bulk_action) { GMA_UI.toast(GMA.i18n.no_selection || 'Select action.', 'warning'); return; }
      var ids = [];
      $form.find('.gma-check-row:checked').each(function () { ids.push($(this).val()); });
      if (!ids.length) { GMA_UI.toast(GMA.i18n.no_selection || 'Select at least one item.', 'warning'); return; }
      GMA_UI.confirm('', function () {
        GMA_Ajax.post(bulk_action, { ids: ids }, function (data) {
          GMA_UI.toast(data.message, 'success');
          setTimeout(function () { location.reload(); }, 800);
        });
      });
    });

    // ── Photo preview ─────────────────────────────────────────────
    $(document).on('change', '.gma-photo-input', function () {
      var file   = this.files[0];
      if (!file) return;
      var $preview = $(this).closest('.gma-photo-upload').find('.gma-photo-preview');
      var reader   = new FileReader();
      reader.onload = function (e) { $preview.attr('src', e.target.result); };
      reader.readAsDataURL(file);
    });

    // ── Tabs ──────────────────────────────────────────────────────
    $(document).on('click', '.gma-tab-btn', function () {
      var target  = $(this).data('tab');
      var $tabs   = $(this).closest('.gma-tabs-container');
      $tabs.find('.gma-tab-btn').removeClass('active');
      $tabs.find('.gma-tab-pane').removeClass('active');
      $(this).addClass('active');
      $tabs.find('#' + target).addClass('active');
    });

    // ── School switch ─────────────────────────────────────────────
    $(document).on('change', '#gma-school-switcher', function () {
      GMA_Ajax.post('gma_switch_school', { school_id: $(this).val() }, function () { location.reload(); });
    });

  }); // DOM ready

})(jQuery);
