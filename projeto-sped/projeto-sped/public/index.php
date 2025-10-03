<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">  
    <title>Document</title>
    <style>
        
        body {
            background-image: url('../public/backg.png');
            background-repeat: no-repeat;
            background-size: cover;
            justify-content: center;
            display: flex;
            align-items: center;
            height: 98.3vh;
            font-family: poppins;
        }

        button {
            all: unset;
        }

        .button {
            position: relative;
            display: inline-flex;
            height: 3.5rem;
            align-items: center;
            border-radius: 9999px;
            padding-left: 2rem;
            padding-right: 2rem;
            font-family: poppins;
            color: #fafaf6;
            letter-spacing: -0.06em;
        }

        .button:hover{
            transform: scale(1.1);
            transition: 0.3s all ease-in-out;
            cursor: pointer;
        }

        .button:not(:hover){
            transform: scale(1);
            transition: 0.3s all ease-in-out;
        }

        .button:active{
            transform: scale(1);
        }

        .button-item {
            background-color: transparent;
            color: #1d1d1f;
        }

        .button-item .button-bg {
            border-color: #cdffd8; 
            background-color: #cdffd8;
        }

        .button-inner,
        .button-inner-hover,
        .button-inner-static {
            pointer-events: none;
            display: block;
        }

        .button-inner {
            position: relative;
        }

        .button-inner-hover {
            position: absolute;
            top: 0;
            left: 0;
            opacity: 0;
            transform: translateY(70%);
        }

        .button-bg {
            overflow: hidden;
            border-radius: 2rem;
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            transform: scale(1);
            transition: transform 1.8s cubic-bezier(0.19, 1, 0.22, 1);
        }

        .button-bg,
        .button-bg-layer,
        .button-bg-layers {
            display: block;
        }

        .button-bg-layers {
            position: absolute;
            left: 50%;
            transform: translate(-50%);
            top: -60%;
            aspect-ratio: 1 / 1;
            width: max(200%, 10rem);
        }

        .button-bg-layer {
            border-radius: 9999px;
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            transform: scale(0);
        }

        .button-bg-layer.-purple {
            background-color: #94b9ff;
        }

        .button-bg-layer.-turquoise {
            background-color: #a2d1e8;
        }

        .button-bg-layer.-yellow {
            --tw-bg-opacity: 1;
            background-color: #cdffd8;
        }

        .button:hover .button-inner-static {
            opacity: 0;
            transform: translateY(-70%);
            transition:
                transform 1.4s cubic-bezier(0.19, 1, 0.22, 1),
                opacity 0.3s linear;
        }

        .button:hover .button-inner-hover {
            opacity: 1;
            transform: translateY(0);
            transition:
                transform 1.4s cubic-bezier(0.19, 1, 0.22, 1),
                opacity 1.4s cubic-bezier(0.19, 1, 0.22, 1);
        }

        .button:hover .button-bg-layer {
            transition:
                transform 1.3s cubic-bezier(0.19, 1, 0.22, 1),
                opacity 0.3s linear;
        }

        .button:hover .button-bg-layer-1 {
            transform: scale(1);
        }

        .button:hover .button-bg-layer-2 {
            transition-delay: 0.1s;
            transform: scale(1);
        }

        .button:hover .button-bg-layer-3 {
            transition-delay: 0.2s;
            transform: scale(1);
        }
    </style>
</head>

<body>
    <a href="cadastro.php">
        <button class="button button-item">
            <span class="button-bg">
                <span class="button-bg-layers">
                    <span class="button-bg-layer button-bg-layer-1 -purple"></span>
                    <span class="button-bg-layer button-bg-layer-2 -turquoise"></span>
                    <span class="button-bg-layer button-bg-layer-3 -yellow"></span>
                    <span class="button-bg-layer button-bg-layer-4 -pink"></span>
                </span>
            </span>
            <span class="button-inner">
                <span class="button-inner-static">Iniciar processo</span>
                <span class="button-inner-hover">Iniciar processo</span>
            </span>
        </button>
    </a>
</body>

</html>