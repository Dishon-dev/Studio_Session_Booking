$(function() {
    //$(document).ready(function()
    $('.pagination_btn').click(function(e){
        e.preventDefault();

        var page = $(this).text();
        $.post('pagination.php', {page: page}, (response) => {
            // response from PHP back-end
            //console.log(response);
            $('.tabular').html(response);
        });
    });

    $('.search_btn').click(function(e){
        e.preventDefault();

        var genre = $('#song_search').val(); 

        if(genre == ''){
            $('#song_search').focus();
            return false;
        }

        $.ajax({
            url: 'songs.php',
            type: 'post',
            data: {genre: genre},
            success: function (data){
                //console.log(data);
                $(".songs_table").html(data);
                $('#Search_form')[0].reset();
            }
        });
    });

    $('#song_search').keyup(function(){
        $.ajax({
            url: 'search.php',
            type: 'post',
            data: {search: $(this).val()},
            success: function(result){
                $(".songs_table").html(result);
            },
        });
    });

    //contact form
    $('.contact_submit').click(function(e){
        e.preventDefault();

        var name = $('#name').val(); 
        var email = $('#email').val(); 
        var subject = $('#subject').val(); 
        var message = $('#message').val(); 

        $.ajax({
            url: 'email.php',
            type: 'post',
            data: {name: name,
                email: email,
                subject: subject,
                message: message
            },
            success: function (data){
                //console.log(data); -->alert('Thank you for contacting Bounce Studio.\nWe will get back to you in the meantime.');
                $('.contact_msg_display').css("display","block");
                $('#msg_display').html(data);
                $('.contact_form')[0].reset();
            }
        });
    });
});