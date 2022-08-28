<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
<form id="form">
    <input type="text" name="login">
    <input type="email" name="email">
    <input type="password" name="password">
    <input type="submit">
</form>

<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

<script>
    $('#form').on('submit', function(e) {
        e.preventDefault()

        $.ajax({
            method: 'POST',
            url: '{{route('api.auth.signup')}}',
            dataType: 'json',
            data: $(this).serialize(),
            success: function (data) {
                document.cookie = "token=" + data.token + "; path=/; max-age=" + 60*60*24*30
                console.log(data.token)
            }
        })
    })

</script>

</body>
</html>
