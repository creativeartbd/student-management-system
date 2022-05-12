(function ($) {
    "use strict";
    $(document).ready(function () {
        $(document).on("submit", "#form", function (e) {
            e.preventDefault();
            var form = $(this);
            var data = form.serialize();
            $.ajax({
                dataType: "json",
                type: "POST",
                url: 'helper/process.php',
                data: data,
                beforeSend: function () {
                    $(".ajax-btn").val("Please wait...");
                    $(".ajax-btn").prop("disabled", true);
                },
                success: function (data) {
                    console.log(data);
                    $(".ajax-btn").prop("disabled", false);
                    $(".ajax-btn").val("Registration");
                    var messages = data.message;
                    $(".result").html('');
                    if( !data.success ) {
                        for( var key in messages ) {
                            $(".result").append("<div class='alert alert-danger'>"+data.message[key]+"</div>");
                        }
                    } else {
                        $(".result").append("<div class='alert alert-success'>"+data.message+"</div>");
                        document.getElementById("registration").reset();
                    }
                }
            });
        });
    });
})(jQuery);