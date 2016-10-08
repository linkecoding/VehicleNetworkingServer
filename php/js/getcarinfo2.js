$(function(){
    $('#subbtn').click(function(){
         $.ajax({
             type: "POST",
             url: "../php/getinfo.php",
             data: {username:$("#username").val(), password:$("#password").val(), action:$("#action").val()},
             dataType: "json",
             success: function(data){
                         $('#subbtn').attr('disabled', true);
                         $.each(data.data, function(commentIndex, comment){
                               $('#sel_car').append('<option value ="' + comment.car_id + '">' + comment.car_id + '</option>');
                         });
                      }
         });
    });

    $('#sel_car').change(function(){
        var sel_value = $('#sel_car').val();
        $.ajax({
             type: "POST",
             url: "../php/getinfo.php",
             data: {username:$("#username").val(), password:$("#password").val(), action:$("#action").val()},
             dataType: "json",
             success: function(data){
                         $.each(data.data, function(commentIndex, comment){
                               if (comment.car_id == sel_value) {
                                    $('#car_id').val(comment.car_id);
                                    $('#username2').val(comment.username);
                                    $('#car_mileage').val(comment.car_mileage);
                                    $('#car_gasnum').val(comment.car_gasnum);
                                    $('#car_engine_ok').val(comment.car_engine_ok);
                                    $('#car_transmission_ok').val(comment.car_transmission_ok);
                                    $('#car_light_ok').val(comment.car_light_ok);
                               }
                         });
                      }
         });

    });
});