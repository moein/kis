$(function(){
    $('#document-list').on('click', 'button', function(e){
        e.preventDefault();
        var password = $('#passoword');
        if (!password)
        {
            alert('Please provide your password');
            $('#password').focus();

            return;
        }

        $('#submitted').val('1');
        $('#get-document').attr('action', $(this).data('url')).submit();
    });

    $('#get-document').submit(function(e){
        if ($('#submitted').val() != '1')
        {
            e.preventDefault();
        }
    })
});