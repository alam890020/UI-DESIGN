/* Smart School Manager - frontend (shortcodes/portal) JS */
(function ($) {
    "use strict";

    $(function () {
        $(document).on("submit", "form.ssm-public-form", function (e) {
            e.preventDefault();
            var $form = $(this);
            var type  = $form.data("ssm-public");
            var $out  = $form.find(".ssm-public-result").removeClass("ok error").text("");

            var data = {};
            $form.serializeArray().forEach(function (f) { data[f.name] = f.value; });

            $form.find("button").prop("disabled", true).addClass("loading");

            $.post(SSMPublic.ajaxUrl, {
                action: "ssm_public_submit",
                nonce: SSMPublic.nonce,
                type: type,
                data: data
            }).done(function (r) {
                if (r && r.success) {
                    $out.addClass("ok").text(r.data && r.data.message ? r.data.message : "Submitted.");
                    $form[0].reset();
                } else {
                    $out.addClass("error").text(r && r.data && r.data.message ? r.data.message : "Something went wrong.");
                }
            }).fail(function () {
                $out.addClass("error").text("Network error. Please try again.");
            }).always(function () {
                $form.find("button").prop("disabled", false).removeClass("loading");
            });
        });
    });
})(jQuery);
