/* Smart School Manager - Admin JS */
(function ($) {
    "use strict";

    $(function () {
        // Tab switcher
        $(document).on("click", ".ssm-tab", function () {
            var $tabs = $(this).closest(".ssm-tabs");
            var target = $(this).data("target");
            $tabs.find(".ssm-tab").removeClass("active");
            $(this).addClass("active");
            $(".ssm-tab-content").hide();
            if (target) $("#" + target).show();
        });

        // Modal open / close
        $(document).on("click", "[data-ssm-modal-open]", function (e) {
            e.preventDefault();
            var id = $(this).data("ssm-modal-open");
            $("#" + id).addClass("active");
        });
        $(document).on("click", ".ssm-modal-close, [data-ssm-modal-close], .ssm-modal-mask", function (e) {
            if (e.target !== this) return;
            $(this).closest(".ssm-modal-mask").removeClass("active");
        });

        // Generic AJAX form (entity)
        $(document).on("submit", "form.ssm-ajax-form", function (e) {
            e.preventDefault();
            var $form = $(this);
            var entity = $form.data("entity");
            var action = "ssm_save_" + entity;
            var data = {};
            $form.serializeArray().forEach(function (f) { data[f.name] = f.value; });

            $.post(SSM.ajaxUrl, {
                action: action,
                nonce: SSM.nonce,
                data: data
            }).done(function (r) {
                if (r && r.success) {
                    ssmToast("Saved successfully");
                    setTimeout(function () { window.location.reload(); }, 700);
                } else {
                    ssmToast("Save failed", "error");
                }
            }).fail(function () { ssmToast("Network error", "error"); });
        });

        // Delete row
        $(document).on("click", ".ssm-row-actions a.del", function (e) {
            e.preventDefault();
            var entity = $(this).data("entity");
            var id = $(this).data("id");
            if (!entity || !id) return;
            if (!confirm("Delete this record?")) return;
            $.post(SSM.ajaxUrl, {
                action: "ssm_delete_" + entity,
                nonce: SSM.nonce,
                id: id
            }).done(function (r) {
                if (r && r.success) {
                    ssmToast("Deleted");
                    setTimeout(function () { window.location.reload(); }, 500);
                } else { ssmToast("Delete failed", "error"); }
            });
        });

        // Settings save
        $(document).on("submit", "form#ssm-settings-form", function (e) {
            e.preventDefault();
            var data = {};
            $(this).serializeArray().forEach(function (f) { data[f.name] = f.value; });
            $.post(SSM.ajaxUrl, { action: "ssm_save_settings", nonce: SSM.nonce, data: data })
                .done(function () { ssmToast("Settings saved"); });
        });

        // Quick search
        var $sb = $(".ssm-search input");
        $sb.on("keypress", function (e) {
            if (e.which === 13) {
                var q = $(this).val();
                $.post(SSM.ajaxUrl, { action: "ssm_quick_search", nonce: SSM.nonce, q: q })
                    .done(function (r) { console.log("search results", r); });
            }
        });
    });

    function ssmToast(msg, type) {
        var $t = $("<div class='ssm-toast'></div>").text(msg);
        if (type === "error") $t.addClass("err");
        $("body").append($t);
        setTimeout(function () { $t.addClass("show"); }, 30);
        setTimeout(function () { $t.removeClass("show"); setTimeout(function () { $t.remove(); }, 300); }, 2400);
    }

    // Toast styling injected once
    var css = '.ssm-toast{position:fixed;bottom:24px;right:24px;background:#0f172a;color:#fff;padding:12px 18px;border-radius:10px;font-size:13px;font-weight:600;box-shadow:0 12px 28px rgba(0,0,0,.25);opacity:0;transform:translateY(10px);transition:all .25s ease;z-index:99999}.ssm-toast.show{opacity:1;transform:translateY(0)}.ssm-toast.err{background:#dc2626}';
    $("<style>").text(css).appendTo("head");

})(jQuery);
