/* School Management Studio — Admin JS */
(function ($) {
    "use strict";

    $(function () {
        // Sidebar group toggle
        $(document).on("click", ".sms-side-grp-h", function () {
            $(this).closest(".sms-side-grp").toggleClass("open");
        });

        // Mobile sidebar toggle
        $(document).on("click", "[data-side-toggle]", function () {
            $(".sms-side").toggleClass("open");
        });

        // Theme toggle
        $(document).on("click", "[data-theme-toggle]", function () {
            $(".sms-app").toggleClass("sms-dark");
            var isDark = $(".sms-app").hasClass("sms-dark") ? 1 : 0;
            $.post(SMS.ajaxUrl, { action: "sms_save_settings", nonce: SMS.nonce, data: { dark_mode: isDark } });
        });

        // Tabs
        $(document).on("click", ".sms-tab", function () {
            var $tabs = $(this).closest(".sms-tabs");
            var target = $(this).data("target");
            $tabs.find(".sms-tab").removeClass("active");
            $(this).addClass("active");
            $(".sms-tab-pane").hide();
            if (target) $("#" + target).show();
        });

        // Generic AJAX form
        $(document).on("submit", "form.sms-ajax-form", function (e) {
            e.preventDefault();
            var $f = $(this);
            var entity = $f.data("entity");
            var data = {};
            $f.serializeArray().forEach(function (it) { data[it.name] = it.value; });

            $.post(SMS.ajaxUrl, { action: "sms_save_" + entity, nonce: SMS.nonce, data: data })
                .done(function (r) {
                    if (r && r.success) {
                        toast("Saved successfully", "ok");
                        setTimeout(function () { window.location.reload(); }, 600);
                    } else {
                        toast("Save failed", "err");
                    }
                })
                .fail(function () { toast("Network error", "err"); });
        });

        // Generic delete
        $(document).on("click", ".sms-row-actions a.del", function (e) {
            e.preventDefault();
            if (!confirm("Delete this record?")) return;
            var entity = $(this).data("entity");
            var id = $(this).data("id");
            $.post(SMS.ajaxUrl, { action: "sms_delete_" + entity, nonce: SMS.nonce, id: id })
                .done(function (r) {
                    if (r && r.success) {
                        toast("Deleted", "ok");
                        setTimeout(function () { window.location.reload(); }, 400);
                    }
                });
        });

        // Settings save
        $(document).on("submit", "form#sms-settings-form", function (e) {
            e.preventDefault();
            var data = {};
            $(this).serializeArray().forEach(function (it) { data[it.name] = it.value; });
            $.post(SMS.ajaxUrl, { action: "sms_save_settings", nonce: SMS.nonce, data: data })
                .done(function () { toast("Settings saved", "ok"); });
        });

        // Wizard navigation (used by student form)
        var $wiz = $(".sms-wiz-step");
        var max = $wiz.length;
        var step = 1;
        if (max > 0) {
            window.smsShowStep = function (s) {
                step = Math.max(1, Math.min(max, s));
                $(".sms-wiz-page").hide();
                $('.sms-wiz-page[data-step="' + step + '"]').show();
                $wiz.removeClass("active done").each(function () {
                    var n = +$(this).data("step");
                    if (n < step) $(this).addClass("done");
                    else if (n === step) $(this).addClass("active");
                });
                $("#sms-wiz-prev").prop("disabled", step === 1);
                $("#sms-wiz-next").toggle(step < max);
                $("#sms-wiz-submit").toggle(step === max);
            };
            $(document).on("click", "#sms-wiz-prev", function () { window.smsShowStep(step - 1); });
            $(document).on("click", "#sms-wiz-next", function () { window.smsShowStep(step + 1); });
        }
    });

    function toast(msg, kind) {
        var $t = $('<div class="sms-toast"></div>').text(msg);
        if (kind) $t.addClass(kind);
        $("body").append($t);
        setTimeout(function () { $t.addClass("show"); }, 30);
        setTimeout(function () { $t.removeClass("show"); setTimeout(function () { $t.remove(); }, 300); }, 2400);
    }
})(jQuery);
