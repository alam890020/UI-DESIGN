/**
 * School Management Admin JavaScript
 */
(function($) {
    'use strict';

    $(document).ready(function() {

        // Student Form Submit
        $('#sm-student-form').on('submit', function(e) {
            e.preventDefault();
            var formData = $(this).serialize();
            $.post(sm_ajax.ajax_url, {
                action: 'sm_save_student',
                nonce: sm_ajax.nonce,
                ...parseFormData(formData)
            }, function(response) {
                if (response.success) {
                    alert(response.data.message);
                    window.location.href = sm_ajax.ajax_url.replace('admin-ajax.php', 'admin.php?page=sm-students');
                } else {
                    alert(response.data.message || 'Error occurred.');
                }
            });
        });

        // Staff Form Submit
        $('#sm-staff-form').on('submit', function(e) {
            e.preventDefault();
            var formData = $(this).serialize();
            $.post(sm_ajax.ajax_url, {
                action: 'sm_save_staff',
                nonce: sm_ajax.nonce,
                ...parseFormData(formData)
            }, function(response) {
                if (response.success) {
                    alert(response.data.message);
                    window.location.href = sm_ajax.ajax_url.replace('admin-ajax.php', 'admin.php?page=sm-staff');
                } else {
                    alert(response.data.message || 'Error occurred.');
                }
            });
        });

        // Notice Form Submit
        $('#sm-notice-form').on('submit', function(e) {
            e.preventDefault();
            var formData = $(this).serialize();
            $.post(sm_ajax.ajax_url, {
                action: 'sm_save_notice',
                nonce: sm_ajax.nonce,
                ...parseFormData(formData)
            }, function(response) {
                if (response.success) {
                    alert(response.data.message);
                    window.location.href = sm_ajax.ajax_url.replace('admin-ajax.php', 'admin.php?page=sm-notices');
                } else {
                    alert(response.data.message || 'Error occurred.');
                }
            });
        });

        // Class Form Submit
        $('#sm-class-form').on('submit', function(e) {
            e.preventDefault();
            var formData = $(this).serialize();
            $.post(sm_ajax.ajax_url, {
                action: 'sm_save_class',
                nonce: sm_ajax.nonce,
                ...parseFormData(formData)
            }, function(response) {
                if (response.success) {
                    alert(response.data.message);
                    location.reload();
                } else {
                    alert(response.data.message || 'Error occurred.');
                }
            });
        });

        // Section Form Submit
        $('#sm-section-form').on('submit', function(e) {
            e.preventDefault();
            var formData = $(this).serialize();
            $.post(sm_ajax.ajax_url, {
                action: 'sm_save_section',
                nonce: sm_ajax.nonce,
                ...parseFormData(formData)
            }, function(response) {
                if (response.success) {
                    alert(response.data.message);
                    location.reload();
                } else {
                    alert(response.data.message || 'Error occurred.');
                }
            });
        });

        // Exam Form Submit
        $('#sm-exam-form').on('submit', function(e) {
            e.preventDefault();
            var formData = $(this).serialize();
            $.post(sm_ajax.ajax_url, {
                action: 'sm_save_exam',
                nonce: sm_ajax.nonce,
                ...parseFormData(formData)
            }, function(response) {
                if (response.success) {
                    alert(response.data.message);
                    location.reload();
                } else {
                    alert(response.data.message || 'Error occurred.');
                }
            });
        });

        // Marks Form Submit
        $('#sm-marks-form').on('submit', function(e) {
            e.preventDefault();
            var formData = $(this).serializeArray();
            var postData = { action: 'sm_save_marks', nonce: sm_ajax.nonce };
            formData.forEach(function(item) {
                postData[item.name] = item.value;
            });
            $.post(sm_ajax.ajax_url, $(this).serialize() + '&action=sm_save_marks&nonce=' + sm_ajax.nonce, function(response) {
                if (response.success) {
                    alert(response.data.message);
                } else {
                    alert(response.data.message || 'Error occurred.');
                }
            });
        });

        // Fee Type Form Submit
        $('#sm-fee-type-form').on('submit', function(e) {
            e.preventDefault();
            var formData = $(this).serialize();
            $.post(sm_ajax.ajax_url, {
                action: 'sm_save_fee_type',
                nonce: sm_ajax.nonce,
                ...parseFormData(formData)
            }, function(response) {
                if (response.success) {
                    alert(response.data.message);
                    location.reload();
                } else {
                    alert(response.data.message || 'Error occurred.');
                }
            });
        });

        // Assign Fee Form Submit
        $('#sm-assign-fee-form').on('submit', function(e) {
            e.preventDefault();
            var formData = $(this).serialize();
            $.post(sm_ajax.ajax_url, {
                action: 'sm_assign_fee',
                nonce: sm_ajax.nonce,
                ...parseFormData(formData)
            }, function(response) {
                if (response.success) {
                    alert(response.data.message);
                    location.reload();
                } else {
                    alert(response.data.message || 'Error occurred.');
                }
            });
        });

        // Attendance Form Submit
        $('#sm-attendance-form').on('submit', function(e) {
            e.preventDefault();
            $.post(sm_ajax.ajax_url, $(this).serialize() + '&action=sm_save_attendance&nonce=' + sm_ajax.nonce, function(response) {
                if (response.success) {
                    alert(response.data.message);
                } else {
                    alert(response.data.message || 'Error occurred.');
                }
            });
        });

        // Settings Form Submit
        $('#sm-settings-form').on('submit', function(e) {
            e.preventDefault();
            var formData = $(this).serialize();
            $.post(sm_ajax.ajax_url, {
                action: 'sm_save_settings',
                nonce: sm_ajax.nonce,
                ...parseFormData(formData)
            }, function(response) {
                if (response.success) {
                    alert(response.data.message);
                } else {
                    alert(response.data.message || 'Error occurred.');
                }
            });
        });

        // Delete buttons
        $(document).on('click', '.sm-delete-btn', function(e) {
            e.preventDefault();
            if (!confirm('Are you sure you want to delete this item?')) return;

            var id = $(this).data('id');
            var type = $(this).data('type');
            var action = 'sm_delete_' + type;

            $.post(sm_ajax.ajax_url, {
                action: action,
                nonce: sm_ajax.nonce,
                id: id
            }, function(response) {
                if (response.success) {
                    alert(response.data.message);
                    location.reload();
                } else {
                    alert(response.data.message || 'Error occurred.');
                }
            });
        });

        // Pay Fee Button
        $(document).on('click', '.sm-pay-fee-btn', function(e) {
            e.preventDefault();
            var id = $(this).data('id');
            var remaining = $(this).data('amount');

            var html = '<div class="sm-modal-overlay">';
            html += '<div class="sm-modal">';
            html += '<h3>Record Payment</h3>';
            html += '<div class="sm-form-group"><label>Amount (Remaining: ' + remaining + ')</label>';
            html += '<input type="number" step="0.01" id="sm-pay-amount" value="' + remaining + '" max="' + remaining + '"></div>';
            html += '<div class="sm-form-group"><label>Payment Method</label>';
            html += '<select id="sm-pay-method"><option value="cash">Cash</option><option value="bank">Bank Transfer</option><option value="online">Online</option><option value="cheque">Cheque</option></select></div>';
            html += '<div class="sm-modal-actions">';
            html += '<button class="button button-primary" id="sm-confirm-pay" data-id="' + id + '">Confirm Payment</button>';
            html += '<button class="button sm-close-modal">Cancel</button>';
            html += '</div></div></div>';

            $('body').append(html);
        });

        // Confirm Pay
        $(document).on('click', '#sm-confirm-pay', function() {
            var id = $(this).data('id');
            var amount = $('#sm-pay-amount').val();
            var method = $('#sm-pay-method').val();

            $.post(sm_ajax.ajax_url, {
                action: 'sm_pay_fee',
                nonce: sm_ajax.nonce,
                id: id,
                pay_amount: amount,
                payment_method: method
            }, function(response) {
                if (response.success) {
                    alert(response.data.message);
                    location.reload();
                } else {
                    alert(response.data.message || 'Error occurred.');
                }
            });
        });

        // Close Modal
        $(document).on('click', '.sm-close-modal, .sm-modal-overlay', function(e) {
            if (e.target === this) {
                $('.sm-modal-overlay').remove();
            }
        });

        // Fee type amount auto-fill
        $('select[name="fee_type_id"]').on('change', function() {
            var amount = $(this).find(':selected').data('amount');
            if (amount) {
                $('input[name="amount"]').val(amount);
            }
        });

        // Helper: parse serialized form data to object
        function parseFormData(serialized) {
            var obj = {};
            var pairs = serialized.split('&');
            pairs.forEach(function(pair) {
                var parts = pair.split('=');
                obj[decodeURIComponent(parts[0])] = decodeURIComponent(parts[1] || '');
            });
            return obj;
        }
    });

})(jQuery);
