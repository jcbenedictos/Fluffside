<<<<<<< HEAD
<?php
session_start();
require_once 'db.inc.php';

$is_logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - FluffSide</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --fs-orange: #EF8E35;
            --fs-orange-light: #FDF1E6;
            --fs-text-dark: #655345;
            --fs-card-bg: #FDF1E6;
            --title-gray: #4A3E3D;
            --footer-green: #B8C7A1;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Nunito', sans-serif;
        }

        body {
            font-family: 'Nunito', sans-serif;
            color: var(--fs-text-dark);
            background: url('Assets/about_background.png') no-repeat center top;
            background-size: 100% auto;
            background-color: #ffffff;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        .container {
            max-width: 100%;
            margin: 0;
            padding: 0 5%;
            width: 100%;
        }



        .site-footer {
            background-color: var(--footer-green);
            position: relative;
            padding: 100px 0 50px 0;
            text-align: center;
            margin-top: auto;
        }

        .footer-wave {
            position: absolute;
            top: -1px;
            left: 0;
            width: 100%;
            overflow: hidden;
            line-height: 0;
        }

        .footer-wave svg {
            display: block;
            width: calc(100% + 1.3px);
            height: 70px;
        }

        .footer-wave .shape-fill {
            fill: var(--footer-green);
        }

        .site-footer h1 {
            font-size: 36px;
            font-weight: 600;
            color: #1A3026;
            letter-spacing: 2px;
            position: relative;
            z-index: 2;
        }

        .about-page-wrapper {
            max-width: 1200px;
            margin: 0 auto;
            padding: 80px 20px 120px 20px;
            min-height: 600px;
            box-sizing: border-box;
            flex: 1;
        }

        .about-main-title {
            text-align: center;
            font-size: 2.5rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 50px;
            color: #4A3E3D;
        }

        .about-main-title span {
            color: var(--fs-orange);
        }

        .about-intro-container {
            display: grid;
            grid-template-columns: 1.4fr 1fr;
            gap: 40px;
            margin-bottom: 60px;
            align-items: center;
        }

        .about-mission-box {
            background-color: var(--fs-card-bg);
            border-radius: 35px;
            padding: 35px 40px;
            line-height: 1.7;
            font-size: 1.15rem;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
        }

        .about-mission-box strong {
            color: var(--fs-orange);
            font-weight: 800;
        }

        .about-hero-spacer {
            min-height: 250px;
        }

        .about-pillars-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 25px;
        }

        .pillar-card {
            background-color: var(--fs-card-bg);
            border-radius: 30px;
            padding: 30px 25px;
            display: flex;
            gap: 20px;
            align-items: flex-start;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
            min-height: 140px;
        }

        .pillar-icon-area {
            font-size: 3.2rem;
            color: var(--fs-orange);
            display: flex;
            align-items: center;
            justify-content: center;
            line-height: 1;
            padding-top: 5px;
        }

        .pillar-text-area h3 {
            margin: 0 0 10px 0;
            font-size: 1.25rem;
            font-weight: 800;
            text-transform: uppercase;
            color: #4A3E3D;
            letter-spacing: 0.3px;
        }

        .pillar-text-area p {
            margin: 0;
            font-size: 0.95rem;
            line-height: 1.5;
            font-weight: 600;
            color: var(--fs-text-dark);
        }

        @media (max-width: 992px) {
            .about-intro-container {
                grid-template-columns: 1fr;
            }

            .about-hero-spacer {
                display: none;
            }

            .about-pillars-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

    <div class="container">
        <!-- ════ HEADER ════ -->
        <?php include 'header.php'; ?>

        <main class="about-page-wrapper">

            <h2 class="about-main-title">Where Passion Meets The <span>Paws</span></h2>

            <div class="about-intro-container">
                <div class="about-mission-box">
                    <strong>Fluffside</strong> is a community-first pet shelter platform dedicated to bridging the gap between rescue animals and loving homes. We believe every domesticated companion—from the largest protective pup to the tiniest energetic hamster—deserves a safe haven and a family that perfectly matches their unique personality.
                </div>

                <div class="about-hero-spacer"></div>
            </div>

            <div class="about-pillars-grid">

                <div class="pillar-card">
                    <div class="pillar-icon-area">
                        <span style="position: relative; display: inline-block;">
                            <i class="fa-solid fa-house-chimney-window"></i>
                        </span>
                    </div>
                    <div class="pillar-text-area">
                        <h3>Safe Havens</h3>
                        <p>To simplify the adoption and fostering journey, making sure no resident is left behind.</p>
                    </div>
                </div>

                <div class="pillar-card">
                    <div class="pillar-icon-area">
                        <i class="fa-solid fa-shield-cat"></i>
                    </div>
                    <div class="pillar-text-area">
                        <h3>Healthy & Loved</h3>
                        <p>Every Fluffside resident is fully vaccinated, vetted, and lovingly prepared before meeting you.</p>
                    </div>
                </div>

                <div class="pillar-card">
                    <div class="pillar-icon-area">
                        <i class="fa-solid fa-paw"></i>
                    </div>
                    <div class="pillar-text-area">
                        <h3>All Fluff Welcomed</h3>
                        <p>Big or small, we advocate for all pets. Because love comes in every shape, breed, and size!</p>
                    </div>
                </div>

            </div>

        </main>
    </div>

    <?php include 'footer.php'; ?>

=======
<?php
session_start();

$is_logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - FluffSide</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --fs-orange: #EF8E35;
            --fs-orange-light: #FDF1E6;
            --fs-text-dark: #655345;
            --fs-card-bg: #FDF1E6;
            --title-gray: #4A3E3D;
            --footer-green: #B8C7A1;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Nunito', sans-serif;
        }

        body {
            font-family: 'Nunito', sans-serif;
            color: var(--fs-text-dark);
            background: url('Assets/about_background.png') no-repeat center top;
            background-size: 100% auto;
            background-color: #ffffff;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        .container {
            max-width: 100%;
            margin: 0;
            padding: 0 5%;
            width: 100%;
        }



        .site-footer {
            background-color: var(--footer-green);
            position: relative;
            padding: 100px 0 50px 0;
            text-align: center;
            margin-top: auto;
        }

        .footer-wave {
            position: absolute;
            top: -1px;
            left: 0;
            width: 100%;
            overflow: hidden;
            line-height: 0;
        }

        .footer-wave svg {
            display: block;
            width: calc(100% + 1.3px);
            height: 70px;
        }

        .footer-wave .shape-fill {
            fill: var(--footer-green);
        }

        .site-footer h1 {
            font-size: 36px;
            font-weight: 600;
            color: #1A3026;
            letter-spacing: 2px;
            position: relative;
            z-index: 2;
        }

        .about-page-wrapper {
            max-width: 1200px;
            margin: 0 auto;
            padding: 80px 20px 120px 20px;
            min-height: 600px;
            box-sizing: border-box;
            flex: 1;
        }

        .about-main-title {
            text-align: center;
            font-size: 2.5rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 50px;
            color: #4A3E3D;
        }

        .about-main-title span {
            color: var(--fs-orange);
        }

        .about-intro-container {
            display: grid;
            grid-template-columns: 1.4fr 1fr;
            gap: 40px;
            margin-bottom: 60px;
            align-items: center;
        }

        .about-mission-box {
            background-color: var(--fs-card-bg);
            border-radius: 35px;
            padding: 35px 40px;
            line-height: 1.7;
            font-size: 1.15rem;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
        }

        .about-mission-box strong {
            color: var(--fs-orange);
            font-weight: 800;
        }

        .about-hero-spacer {
            min-height: 250px;
        }

        .about-pillars-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 25px;
        }

        .pillar-card {
            background-color: var(--fs-card-bg);
            border-radius: 30px;
            padding: 30px 25px;
            display: flex;
            gap: 20px;
            align-items: flex-start;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
            min-height: 140px;
        }

        .pillar-icon-area {
            font-size: 3.2rem;
            color: var(--fs-orange);
            display: flex;
            align-items: center;
            justify-content: center;
            line-height: 1;
            padding-top: 5px;
        }

        .pillar-text-area h3 {
            margin: 0 0 10px 0;
            font-size: 1.25rem;
            font-weight: 800;
            text-transform: uppercase;
            color: #4A3E3D;
            letter-spacing: 0.3px;
        }

        .pillar-text-area p {
            margin: 0;
            font-size: 0.95rem;
            line-height: 1.5;
            font-weight: 600;
            color: var(--fs-text-dark);
        }

        @media (max-width: 992px) {
            .about-intro-container {
                grid-template-columns: 1fr;
            }

            .about-hero-spacer {
                display: none;
            }

            .about-pillars-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

    <div class="container">
        <!-- ════ HEADER ════ -->
        <?php include 'header.php'; ?>

        <main class="about-page-wrapper">

            <h2 class="about-main-title">Where Passion Meets The <span>Paws</span></h2>

            <div class="about-intro-container">
                <div class="about-mission-box">
                    <strong>Fluffside</strong> is a community-first pet shelter platform dedicated to bridging the gap between rescue animals and loving homes. We believe every domesticated companion—from the largest protective pup to the tiniest energetic hamster—deserves a safe haven and a family that perfectly matches their unique personality.
                </div>

                <div class="about-hero-spacer"></div>
            </div>

            <div class="about-pillars-grid">

                <div class="pillar-card">
                    <div class="pillar-icon-area">
                        <span style="position: relative; display: inline-block;">
                            <i class="fa-solid fa-house-chimney-window"></i>
                        </span>
                    </div>
                    <div class="pillar-text-area">
                        <h3>Safe Havens</h3>
                        <p>To simplify the adoption and fostering journey, making sure no resident is left behind.</p>
                    </div>
                </div>

                <div class="pillar-card">
                    <div class="pillar-icon-area">
                        <i class="fa-solid fa-shield-cat"></i>
                    </div>
                    <div class="pillar-text-area">
                        <h3>Healthy & Loved</h3>
                        <p>Every Fluffside resident is fully vaccinated, vetted, and lovingly prepared before meeting you.</p>
                    </div>
                </div>

                <div class="pillar-card">
                    <div class="pillar-icon-area">
                        <i class="fa-solid fa-paw"></i>
                    </div>
                    <div class="pillar-text-area">
                        <h3>All Fluff Welcomed</h3>
                        <p>Big or small, we advocate for all pets. Because love comes in every shape, breed, and size!</p>
                    </div>
                </div>

            </div>

        </main>
    </div>

    <?php include 'footer.php'; ?>

>>>>>>> 5811b114e5fd1e327cc690ba83d3e4517f2253b4
    <script>
        // 30-second inactivity logout
        let inactivityTimer;
        function resetTimer() {
            clearTimeout(inactivityTimer);
            inactivityTimer = setTimeout(function() {
                window.location.href = 'logout.php?reason=inactive';
            }, 30000);
        }
        ['mousemove','keydown','click','scroll','touchstart'].forEach(function(e) {
            document.addEventListener(e, resetTimer);
        });
        resetTimer();
    </script>
<<<<<<< HEAD
</body>

=======
</body>

>>>>>>> 5811b114e5fd1e327cc690ba83d3e4517f2253b4
</html>