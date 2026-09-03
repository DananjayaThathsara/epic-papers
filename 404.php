<?php
require_once __DIR__ . '/includes/security_headers.php';
send_security_headers();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Not Found | Epic Paper</title>
    <meta name="robots" content="noindex, follow">
    <link rel="icon" href="src/images/favicon.png" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@400;500;600;700&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --green: #00c500;
            --green-dark: #087a29;
            --green-pale: #f2f8f3;
            --ink: #17221a;
            --gray: #5b6b62;
            --white: #fff;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            overflow: hidden;
            color: var(--ink);
            background: var(--green-pale);
            font-family: "Jost", sans-serif;
        }

        .page {
            position: relative;
            width: min(100% - 32px, 980px);
            min-height: 560px;
            display: grid;
            grid-template-columns: 1fr 0.9fr;
            align-items: center;
            gap: 56px;
            padding: 48px 64px;
            overflow: hidden;
            border: 1px solid rgba(0, 197, 0, 0.2);
            border-radius: 16px;
            background: var(--white);
            box-shadow: 0 24px 60px rgba(21, 87, 36, 0.12);
        }

        .copy {
            position: relative;
            z-index: 1;
        }

        .logo {
            width: 74px;
            height: auto;
            margin-bottom: 28px;
        }

        .eyebrow {
            margin: 0 0 12px;
            color: var(--green);
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.14em;
            text-transform: uppercase;
        }

        h1 {
            max-width: 440px;
            margin: 0 0 18px;
            font-family: "Poppins", sans-serif;
            font-size: clamp(2.4rem, 6vw, 4.5rem);
            line-height: 1.05;
        }

        h1 span {
            color: var(--green);
        }

        .message {
            max-width: 430px;
            margin: 0 0 28px;
            color: var(--gray);
            font-size: 1.08rem;
            line-height: 1.65;
        }

        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }

        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 44px;
            padding: 10px 20px;
            border: 1px solid var(--green);
            border-radius: 6px;
            color: var(--green);
            font-weight: 700;
            text-decoration: none;
            transition: transform 250ms ease, background 250ms ease, color 250ms ease;
        }

        .button.primary {
            background: var(--green);
            color: var(--white);
        }

        .button:hover {
            transform: translateY(-2px);
            background: #00c500;
            color: var(--white);
        }

        .art {
            position: relative;
            z-index: 1;
            display: grid;
            place-items: center;
            min-height: 360px;
        }

        .bag {
            position: relative;
            width: 190px;
            height: 230px;
            border: 5px solid var(--green-dark);
            border-radius: 12px 12px 20px 20px;
            background: var(--white);
            transform: rotate(4deg);
            box-shadow: 14px 18px 0 rgba(0, 197, 0, 0.1);
        }

        .bag::before {
            content: "";
            position: absolute;
            left: 48px;
            top: -58px;
            width: 84px;
            height: 58px;
            border: 5px solid var(--green-dark);
            border-bottom: 0;
            border-radius: 48px 48px 0 0;
        }

        .bag::after {
            content: "+";
            position: absolute;
            left: 50%;
            top: 54px;
            display: grid;
            width: 58px;
            height: 58px;
            place-items: center;
            border: 4px solid var(--green);
            border-radius: 50%;
            color: var(--green);
            font-family: "Poppins", sans-serif;
            font-size: 2.2rem;
            line-height: 1;
            transform: translateX(-50%);
        }

        .label {
            position: absolute;
            left: 25px;
            right: 25px;
            top: 137px;
            height: 24px;
            border-radius: 5px;
            background: var(--green);
        }

        .line {
            position: absolute;
            left: 42px;
            right: 42px;
            bottom: 30px;
            height: 4px;
            background: #dcece1;
        }

        .pattern {
            position: absolute;
            inset: 0;
            pointer-events: none;
            opacity: 0.7;
            background-image: radial-gradient(rgba(0, 197, 0, 0.16) 1px, transparent 1px);
            background-size: 22px 22px;
        }

        .circle {
            position: absolute;
            width: 280px;
            height: 280px;
            border: 1px solid rgba(0, 197, 0, 0.14);
            border-radius: 50%;
        }

        .circle.one {
            right: -130px;
            bottom: -130px;
        }

        .circle.two {
            right: -80px;
            bottom: -80px;
            width: 180px;
            height: 180px;
        }

        @media (max-width: 700px) {
            body {
                overflow: auto;
                padding: 16px 0;
            }

            .page {
                grid-template-columns: 1fr;
                gap: 10px;
                min-height: auto;
                padding: 34px 24px 42px;
                text-align: center;
            }

            .logo,
            .message {
                margin-left: auto;
                margin-right: auto;
            }

            h1 {
                margin-left: auto;
                margin-right: auto;
            }

            .actions {
                justify-content: center;
            }

            .art {
                min-height: 270px;
                transform: scale(0.78);
            }
        }

        @media (prefers-reduced-motion: no-preference) {
            .copy {
                animation: rise 800ms cubic-bezier(0.16, 1, 0.3, 1) both;
            }

            .bag {
                animation: float 4s ease-in-out 300ms infinite alternate;
            }

            @keyframes rise {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            @keyframes float {
                from {
                    transform: rotate(4deg) translateY(0);
                }

                to {
                    transform: rotate(-2deg) translateY(-10px);
                }
            }
        }
    </style>
</head>

<body>
    <main class="page">
        <div class="pattern"></div>
        <div class="circle one"></div>
        <div class="circle two"></div>
        <section class="copy" aria-labelledby="error-title">
            <img class="logo" src="src/images/logo.png" alt="Epic Paper">
            <p class="eyebrow">Epic Paper Packaging Solutions</p>
            <h1 id="error-title">This page took a <span>wrong turn.</span></h1>
            <p class="message">The page you are looking for is unavailable or may have moved. Let us guide you back to our pharmaceutical paper packaging solutions.</p>
            <div class="actions">
                <a class="button primary" href="index.php">Back to Home</a>
                <a class="button" href="index.php#shop">View Products</a>
            </div>
        </section>
        <div class="art" aria-hidden="true">
            <div class="bag"><span class="label"></span><span class="line"></span></div>
        </div>
    </main>
</body>

</html>