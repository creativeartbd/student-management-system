(function ($) {
    "use strict";
    $(document).ready(function () {

        (function worker() {
            var data = {
                form : 'getchat'
            }
            $.ajax({
                dataType: "html",
                type: "POST",
                url: 'helper/process.php',
                data : data,
                success: function(data) {
                    $(".all-message").html(data);
                },
                complete: function() {
                // Schedule the next request when the current one's complete
                setTimeout(worker, 1000);
                }
            });
        })();

          

        $(".registration_type").change( function() {
            var current = $(this).val();
            if( current == 2 ) {
                $(".hide_me").hide('slow');
            } else {
                $(".hide_me").show('slow');
            }
        });
        $(".approve_project").click(function(){
           var username = $(this).data('username');
           $(".set_username").val( username );
        });
        $(".approve_goal").click(function(){
            var st_id = $(this).data('st-id');
            var goal_id = $(this).data('goal-id');
            $(".set_st_id").val( st_id );
            $(".set_goal_id").val( goal_id );
         });
        $(".goal_answer").click(function(){
            var goal_id = $(this).data('goal-id');
            $(".set_goal_id").val( goal_id );
         });
        $(document).on("submit", "#form", function (e) {
            e.preventDefault();
            var form = $(this);
            var data = new FormData(this);
            var btn_level = $(".ajax-btn").val();
            $.ajax({
                dataType: "json",
                type: "POST",
                url: 'helper/process.php',
                data: data,
                processData : false,
                contentType : false,
                beforeSend: function () {
                    $(".ajax-btn").val("Please wait...");
                    $(".ajax-btn").prop("disabled", true);
                },
                success: function (data) {
                    console.log(btn_level);
                    $(".ajax-btn").prop("disabled", false);
                    $(".ajax-btn").val( btn_level );
                    var messages = data.message;
                    $(".result").html('');
                    if( !data.success ) {
                        for( var key in messages ) {
                            $(".result").append("<div class='alert alert-danger'>"+data.message[key]+"</div>");
                        }
                    } else {
                        // $(".ajax-btn").prop("disabled", true);
                        $(".result").append("<div class='alert alert-success'>"+data.message+"</div>");
                        document.getElementById("form").reset();
                        if( data.redirect ) {
                            setTimeout(function() {
                                window.location = data.redirect;
                            }, 3000);
                        }
                        if( data.reload ) {
                            setTimeout(function() {
                                window.location.reload();
                            }, 3000);
                        }
                    }
                }
            });
        });

        $(document).on("submit", "#chat", function (e) {
            e.preventDefault();
            var form = $(this);
            var data = new FormData(this);
            var btn_level = $(".ajax-btn").val();

            $.ajax({
                dataType: "json",
                type: "POST",
                url: 'helper/process.php',
                data: data,
                processData : false,
                contentType : false,
                beforeSend: function () {
                    $(".ajax-btn").val("Please wait...");
                    $(".ajax-btn").prop("disabled", true);
                },
                success: function (data) {
                    console.log(btn_level);
                    $(".ajax-btn").prop("disabled", false);
                    $(".ajax-btn").val( btn_level );
                    var messages = data.message;
                    if( !data.success ) {
                        for( var key in messages ) {
                            $(".result").append("<div class='alert alert-danger'>"+data.message[key]+"</div>");
                        }
                    } else {
                        // $(".ajax-btn").prop("disabled", true);
                        $(".all-message").append(data.message);
                        document.getElementById("chat").reset();
                        if( data.redirect ) {
                            setTimeout(function() {
                                window.location = data.redirect;
                            }, 3000);
                        }
                        if( data.reload ) {
                            setTimeout(function() {
                                window.location.reload();
                            }, 3000);
                        }
                    }
                }
            });
        });
    });
})(jQuery);