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
<div id="game">
    <div class="player player-main" style="left: 25%"></div>
    <div class="column"></div>
    <div class="player player-opponent" style="left: 75%"></div>

    <div class="wall wall_center" style="top: -50px"></div>
    <div class="wall wall_right" style="top: -50px"></div>
    <div class="wall wall_left" style="top: -50px"></div>

    <div class="modal-window modal-window_lose" style="display: none">
        <h2 class="modal-window__caption">Игра окончена</h2>
        <h4 class="modal-window__content">Ты проиграл</h4>
        <a href="/" class="modal-window__button">В меню</a>
    </div>
    <div class="modal-window modal-window_win" style="display: none">
        <h2 class="modal-window__caption">Игра окончена</h2>
        <h4 class="modal-window__content">Ты выиграл</h4>
        <a href="/" class="modal-window__button">В меню</a>
    </div>
</div>
</body>

<style>
    a {
        text-decoration: none;
        color: inherit;
    }

    body {
        margin: 0;
        padding: 0;
    }

    #game {
        position: relative;
        width: 100%;
        height: 100vh;
        overflow: hidden;
    }

    .player {
        position: absolute;
        background-color: #000;
        width: 50px;
        height: 200px;
        bottom: 30px;
        transform: translateX(-50%);
        transition: left .8s ease;
    }

    .column {
        position: absolute;
        background-color: #000;
        width: 5px;
        bottom: 0;
        top: 0;
        transform: translateX(-50%);
        left: 50%;
    }

    .wall {
        width: 10%;
        position: absolute;
        top: 0;
        height: 20px;
        transform: translateX(-50%);
        background-color: #000;
        transition: top 2s linear;
    }

    .wall_center {
        left: 25%;
    }

    .wall_right {
        left: 45%;
    }

    .wall_left {
        left: 5%;
    }

    .modal-window {
        font-family: sans-serif;
        position: fixed;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        background-color: rgba(0, 0, 0, 0.8);
        color: #E3E3E3;
        text-transform: uppercase;
        text-align: center;
    }

    .modal-window__caption {
        font-size: 48px;
        margin-top: 125px;
    }

    .modal-window__content {
        font-size: 32px;
        margin-top: 75px;
    }

    .modal-window_lose .modal-window__content {
        color: indianred;
    }

    .modal-window_win .modal-window__content {
        color: limegreen;
    }

    .modal-window__button {
        display: block;
        width: 100px;
        border-radius: 20px;
        background-color: #2d3748;
        padding: 15px 20px;
        margin: 50px auto 0;
        transition: .3s ease transform;
    }

    .modal-window__button:hover {
        transform: scale(1.05);
    }
</style>

<script>
    const socket = new WebSocket('ws://racer:8080')

    const game = document.querySelector('#game')
    const main_player = document.querySelector('.player-main')
    const opponent_player = document.querySelector('.player-opponent')

    const wall_center = document.querySelector('.wall_center')
    const wall_right = document.querySelector('.wall_right')
    const wall_left = document.querySelector('.wall_left')
    const variations = [
        [wall_left],
        [wall_center],
        [wall_right],
        [wall_left, wall_right],
        [wall_left, wall_center],
        [wall_center, wall_right]
    ]

    let left_indent = 25

    let interval = 2000

    let generator_interval_id

    let change_interval_id = setInterval(changeInterval, 8000)
    let check_player_in_wall_interval_id = setInterval(checkPlayerInWall, 50)

    addEventListener("keydown", keyListener)

    function keyListener(e) {
        switch (e.key) {
            case 'Ф':
            case 'ф':
            case 'A':
            case 'a':
            case 'Left':
            case 'ArrowLeft':
                if (left_indent !== 40 && left_indent !== 25 && left_indent <= 10) {
                    break
                }
                left_indent -= 15
                changePosition(main_player, left_indent)

                break

            case 'В':
            case 'в':
            case 'D':
            case 'd':
            case 'Right':
            case 'ArrowRight':
                if (left_indent >= 40 && left_indent !== 25 && left_indent !== 10) {
                    break
                }
                left_indent += 15
                changePosition(main_player, left_indent)

                break
        }
    }

    function changePosition(main_player, left_indent) {
        socket.send(JSON.stringify({
            "left_indent": left_indent
        }))

        main_player.style.left = left_indent + "%"
    }

    socket.onmessage = e => {
        let data = JSON.parse(e.data)
        let left_indent = data["left_indent"]

        opponent_player.style.left = left_indent + 50 + "%"
    }


    function generator() {
        const variation_index = Math.floor(Math.random() * variations.length)
        const variation = variations[variation_index]
        console.log(variation)
        socket.send(JSON.stringify({
            "variation": JSON.stringify(variation)
        }))

        variation.forEach(wall => {
            const wall_clone = wall.cloneNode(false)
            game.appendChild(wall_clone)

            wall_clone.style.top = "-50px"

            setTimeout(() => {
                wall_clone.style.top = "calc(100vh + 50px)"
            }, 50)

            setTimeout(() => {
                game.removeChild(wall_clone)
            }, 2050)
        })
    }

    function changeInterval() {
        if (interval > 1000) {
            clearInterval(generator_interval_id)
            interval -= 100
            generator_interval_id = setInterval(generator, interval)
        }
    }

    function checkPlayerInWall() {
        let player_coordinates = main_player.getBoundingClientRect()


        document.querySelectorAll('.wall').forEach(wall => {
            let wall_coordinates = wall.getBoundingClientRect()

            if (wall_coordinates.bottom > player_coordinates.top
                && wall_coordinates.bottom <= player_coordinates.bottom
                && wall_coordinates.right + wall_coordinates.width >= player_coordinates.right
                && wall_coordinates.left < player_coordinates.left + player_coordinates.width
            )
            {
                game.removeChild(main_player)

                modal_window_lose = document.querySelector(".modal-window_lose")
                modal_window_lose.style.display = 'block'

                clearInterval(check_player_in_wall_interval_id)
                clearInterval(change_interval_id)
                clearInterval(generator_interval_id)
                removeEventListener('keydown', keyListener)

                socket.send(JSON.stringify({
                    "game_over": true
                }))

                return
            }
        })
    }
</script>

</html>

