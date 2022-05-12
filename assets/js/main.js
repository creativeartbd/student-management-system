(function ($) {
    "use strict";
    $(document).ready(function () {
        $(document).on("submit", "#registration", function (e) {
            e.preventDefault();
            var form = $(this);
            var data = form.serialize();
            $.ajax({
                dataType: "html",
                type: "POST",
                url: 'helper/process.php',
                data: data,
                beforeSend: function () {
                    $(".auth-form-btn").val("Please wait...");
                     $(".auth-form-btn").prop("disabled", true);
                },
                success: function (data) {
                    $(".result").html(data);
                    $(".auth-form-btn").val("Registration");
                    $(".auth-form-btn").prop("disabled", false);
                }
            });
        });
    });
})(jQuery);