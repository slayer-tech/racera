<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Chat</title>
</head>
<body>
<div style="width: 70%; overflow-y: scroll; overflow-x: hidden; border: 1px solid #000; height: 60vh; margin: 0 auto 10vh; padding: 10px" class="messages">

</div>

<div class="form" style="height: 30vh;">
    <form id="form" style="display: flex; justify-content: center; align-items: center">
        <textarea name="content" style="resize: none"></textarea>
        <input style="margin-left: 10px" type="submit">
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

<script>
    const socket = new WebSocket('ws://racer:8080')
    let user_id = 0

    $.ajax({
        method: 'get',
        url: 'http://racer/api/auth/user',
        headers: {
            'Authorization': 'Bearer ' + getCookie('token')
        },
        success: data => {
            user_id = data.id
            if (socket.readyState == 1) {
                console.log(123)
                socket.send(JSON.stringify({"command": "subscribe", "chat": user_id}))
            }
        }
    })

        $('#form').on('submit', function(e) {
        e.preventDefault()

        $('.messages').html($('.messages').html() +
            `<div style="float: right; text-align: right; width: 50%; margin-left: 100%" class="message">
                    <p>${$('textarea').val()}</p>
                 </div>`)

            $('.messages').scrollTop(Number.MAX_SAFE_INTEGER)

            let data = $(this).serialize()

            $('textarea').val("")


        $.ajax({
            url: '{{ route('api.message.store') }}',
            method: 'post',
            dataType: 'json',
            headers: {
                'Authorization': 'Bearer ' + getCookie('token')
            },
            data: data + "&recipient_id={{ $id }}",
            success: function(message){
                socket.send(JSON.stringify({"command": "send", "chat": "{{ $id }}", "message": message.content}))
            }
        });
    })


    $.ajax({
        url: '{{ route('api.chat.show', ['id' => $id]) }}',
        method: 'get',
        dataType: 'json',
        headers: {
            'Authorization': 'Bearer ' + getCookie('token')
        },
        success: function (data) {
            data.messages.forEach((message) => {
                let float = 'right'
                let margin = 'left'
                if (message.profile_id == data.recipient.id) {
                    float = 'left'
                    margin = 'right'
                }

                $('.messages').html($('.messages').html() +
                    `<div style="float: ${float}; text-align: ${float}; width: 50%; margin-${margin}: 100%" class="message">
                        <p>${message.content}</p>
                     </div>`)
            })

            $('.messages').scrollTop(Number.MAX_SAFE_INTEGER)
        }
    })

    socket.onmessage = (e) => {
        $('.messages').html($('.messages').html() +
            `<div style="float: left; text-align: left; width: 50%; margin-right: 100%" class="message">
                        <p>${e.data}</p>
                     </div>`)

        $('.messages').scrollTop(Number.MAX_SAFE_INTEGER)
    }

    function getCookie(name) {
        let matches = document.cookie.match(new RegExp(
            "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
        ));
        return matches ? decodeURIComponent(matches[1]) : undefined;
    }


</script>
</body>
</html>
