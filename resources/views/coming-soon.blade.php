<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} - Coming Soon</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Poppins', sans-serif;
        background: linear-gradient(135deg, #00b4db, #0083b0);
        height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        text-align: center;
        padding: 20px;
    }

    .container {
        max-width: 800px;
        padding: 40px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 20px;
        backdrop-filter: blur(10px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
    }

    .logo {
        max-width: 200px;
        margin-bottom: 30px;
    }

    h1 {
        font-size: 2.5em;
        margin-bottom: 20px;
        font-weight: 600;
    }

    p {
        font-size: 1.2em;
        margin-bottom: 30px;
        line-height: 1.6;
    }

    .social-links {
        display: flex;
        justify-content: center;
        gap: 20px;
        margin-top: 30px;
    }

    .social-links a {
        color: white;
        text-decoration: none;
        font-size: 24px;
        transition: transform 0.3s ease;
    }

    .social-links a:hover {
        transform: scale(1.2);
    }

    @media (max-width: 768px) {
        h1 {
            font-size: 2em;
        }

        p {
            font-size: 1em;
        }
    }

    .text-logo {
        margin-bottom: 40px;
    }

    .text-logo h1 {
        font-size: 3em;
        margin-bottom: 10px;
        color: #fff;
        text-transform: uppercase;
        letter-spacing: 2px;
    }

    .tagline {
        font-size: 1.2em;
        color: rgba(255, 255, 255, 0.9);
        font-weight: 300;
    }

    .content h2 {
        font-size: 2em;
        margin-bottom: 20px;
    }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <div class="container">
        <div class="text-logo">
            <h1>{{ config('app.name') }}</h1>
            <p class="tagline">Healthcare Solutions</p>
        </div>
        <div class="content">
            <h2>Coming Soon</h2>
            <p>We're working hard to bring you something amazing. Our new website is under construction and will be
                ready soon.</p>
            <p>Stay tuned for updates!</p>
        </div>

        <div class="social-links">
            <a href="#" title="Facebook"><i class="fab fa-facebook"></i></a>
            <a href="#" title="Twitter"><i class="fab fa-twitter"></i></a>
            <a href="#" title="Instagram"><i class="fab fa-instagram"></i></a>
            <a href="#" title="LinkedIn"><i class="fab fa-linkedin"></i></a>
        </div>
    </div>
</body>

</html>