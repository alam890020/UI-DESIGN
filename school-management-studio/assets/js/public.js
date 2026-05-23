/* School Management Studio - frontend JS */
(function ($) {
    "use strict";
    $(function () {
        $(document).on("submit", "form.sms-fp-form", function (e) {
            e.preventDefault();
            var $f = $(this);
            var type = $f.data("sms-public");
            var $out = $f.find(".sms-fp-result").removeClass("ok error").text("");
            var data = {};
            $f.serializeArray().forEach(function (it) { data[it.name] = it.value; });

            $f.find("button").prop("disabled", true);
            $.post(SMSPublic.ajaxUrl, { action: "sms_public_submit", nonce: SMSPublic.nonce, type: type, data: data })
                .done(function (r) {
                    if (r && r.success) {
                        $out.addClass("ok").text(r.data && r.data.message ? r.data.message : "Submitted.");
                        $f[0].reset();
                    } else {
                        $out.addClass("error").text(r && r.data && r.data.message ? r.data.message : "Something went wrong.");
                    }
                })
                .fail(function () { $out.addClass("error").text("Network error."); })
                .always(function () { $f.find("button").prop("disabled", false); });
        });
    });
})(jQuery);
