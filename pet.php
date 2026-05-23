<?php
// pet.php — Single dynamic page for ALL pets.
// URL: pet.php?id=scout  →  loads Scout's data
// URL: pet.php?id=luna   →  loads Luna's data
// No duplicate files needed — ever.

session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php?msg=login_required");
    exit;
}

require_once 'pets.inc.php';

// ── Grab the pet ID from the URL ──────────────────────────────
$pet_id = strtolower(trim($_GET['id'] ?? ''));

// ── Guard: 404 if ID missing or not in our data ───────────────
if ($pet_id === '' || !array_key_exists($pet_id, $pets)) {
    http_response_code(404);
    die('<!DOCTYPE html><html><head><title>Pet not found — FluffSide</title>
         <style>body{font-family:Nunito,sans-serif;display:flex;align-items:center;
         justify-content:center;height:100vh;flex-direction:column;gap:16px;color:#5A483E;}
         a{color:#EF8E35;font-weight:800;}</style></head>
         <body><h2> Pet not found.</h2>
         <a href="residents.php">← Back to Residents</a></body></html>');
}

$pet = $pets[$pet_id];

// ── Helpers ───────────────────────────────────────────────────
function h(string $s): string
{
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

// ── Tag colour logic (not hardcoded — driven by data) ─────────
function type_class(string $type): string
{
    return match (strtoupper($type)) {
        'DOG'    => 'tag-dog',
        'CAT'    => 'tag-cat',
        'RABBIT' => 'tag-rabbit',
        'HAMSTER' => 'tag-hamster',
        default  => 'tag-gray',
    };
}
function gender_class(string $gender): string
{
    return strtoupper($gender) === 'MALE' ? 'tag-male' : 'tag-female';
}
function age_class(string $age): string
{
    // Pink for babies, yellow for older
    return (stripos($age, 'week') !== false || stripos($age, 'month') !== false) ? 'tag-age-baby' : 'tag-age';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($pet['name']) ?> — FluffSide</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* ── CSS Variables (same palette as residents.php) ── */
        :root {
            --primary-orange: #EF8E35;
            --primary-hover: #D67A26;
            --bg-light: #FDFBF5;
            --text-dark: #5A483E;
            --text-light: #8E8279;
            --box-yellow: #FFF9EE;
            --box-green-header: #EAE6D1;
            --tag-yellow: #FCEABB;
            --tag-blue: #DEEBF7;
            --tag-pink: #F8E1DF;
            --tag-green: #E1E8B8;
            --tag-gray: #F0F0F0;
            --white: #FFFFFF;
        }

        html {
            min-height: 100%;
            overflow-y: scroll;
        }

        *,
        *::before,
        *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: var(--bg-light);
            color: var(--text-dark);
            font-family: 'Nunito', sans-serif;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* ── Container (matches residents.php) ── */
        .container {
            max-width: 100%;
            margin: 0;
            padding: 0 5%;
            width: 100%;
        }


        /* ── Back link ── */
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            font-weight: 700;
            color: var(--text-light);
            text-decoration: none;
            margin-bottom: 28px;
            transition: color .2s;
            position: relative;
            z-index: 1;
        }

        .back-link:hover {
            color: var(--primary-orange);
        }

        .back-link i {
            font-size: 11px;
        }

        /* ── Pet detail layout ── */
        .pet-detail {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            margin-bottom: 80px;
            align-items: flex-start;
        }

        /* ── LEFT: Gallery ── */
        .gallery {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .gallery-main {
            width: 100%;
            aspect-ratio: 4/3;
            object-fit: cover;
            border-radius: 18px;
            background: #e8e8e8;
            display: block;
            transition: opacity .25s;
        }

        .gallery-thumbs {
            display: flex;
            gap: 12px;
        }

        .gallery-thumb {
            width: 120px;
            height: 84px;
            object-fit: cover;
            border-radius: 10px;
            cursor: pointer;
            border: 3px solid transparent;
            transition: border-color .2s, opacity .2s;
            flex-shrink: 0;
            background: #e8e8e8;
        }

        .gallery-thumb:hover {
            opacity: .85;
        }

        .gallery-thumb.active {
            border-color: var(--primary-orange);
        }

        /* ── RIGHT: Info ── */
        .pet-info-panel {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .pet-name {
            font-size: 52px;
            font-weight: 900;
            line-height: 1;
            color: var(--text-dark);
            letter-spacing: -1px;
        }

        .pet-breed-line {
            font-size: 15px;
            font-weight: 700;
            color: var(--text-dark);
            margin-top: 4px;
        }

        .pet-breed-line span {
            color: var(--text-light);
        }

        /* ── Status tags (type / gender / age) — data-driven ── */
        .basic-tags {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 2px;
        }

        .tag-basic {
            padding: 5px 14px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .03em;
        }

        .tag-dog {
            background: var(--tag-yellow);
        }

        .tag-cat {
            background: var(--tag-green);
        }

        .tag-rabbit {
            background: var(--tag-pink);
        }

        .tag-hamster {
            background: #FEE4CB;
        }

        .tag-gray {
            background: var(--tag-gray);
        }

        .tag-male {
            background: var(--tag-blue);
        }

        .tag-female {
            background: var(--tag-pink);
        }

        .tag-age {
            background: var(--tag-yellow);
        }

        .tag-age-baby {
            background: var(--tag-pink);
        }

        /* ── Trait tags ── */
        .section-label {
            font-size: 13px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .04em;
            margin-bottom: 8px;
            color: var(--text-dark);
        }

        .trait-tags {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .tag-trait {
            padding: 5px 14px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            border: 1.5px solid var(--primary-orange);
            color: var(--text-dark);
            background: transparent;
            transition: background .15s;
        }

        .tag-trait:hover {
            background: #fff3e6;
        }

        /* ── Likes / Dislikes ── */
        .like-dislike {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .ld-block {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .ld-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 5px;
            margin-top: 6px;
        }

        .ld-list li {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-dark);
            display: flex;
            align-items: center;
            gap: 7px;
        }

        .ld-list li::before {
            content: '•';
            color: var(--primary-orange);
            font-size: 16px;
            line-height: 1;
            flex-shrink: 0;
        }

        /* ── Description ── */
        .pet-description-block {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .pet-description {
            font-size: 13.5px;
            font-weight: 600;
            line-height: 1.75;
            color: var(--text-dark);
            margin-top: 6px;
            text-align: justify;
        }

        /* ── CTA Buttons ── */
        .cta-buttons {
            display: flex;
            gap: 16px;
            margin-top: 8px;
        }

        .btn-cta {
            flex: 1;
            padding: 16px 20px;
            border-radius: 10px;
            border: none;
            font-family: 'Nunito', sans-serif;
            font-size: 16px;
            font-weight: 900;
            letter-spacing: .06em;
            text-transform: uppercase;
            cursor: pointer;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background .2s, transform .1s;
        }

        .btn-cta:active {
            transform: scale(.97);
        }

        .btn-adopt {
            background: var(--primary-orange);
            color: var(--white);
        }

        .btn-adopt:hover {
            background: var(--primary-hover);
        }

        .btn-foster {
            background: var(--primary-orange);
            color: var(--white);
        }

        .btn-foster:hover {
            background: var(--primary-hover);
        }

        /* ── Divider between sections ── */
        .info-divider {
            border: none;
            border-top: 1.5px solid #EAE3D9;
            margin: 0;
        }

        /* ── Responsive ── */
        @media (max-width: 900px) {
            .pet-detail {
                grid-template-columns: 1fr;
                gap: 30px;
            }

            .pet-name {
                font-size: 38px;
            }

            .like-dislike {
                grid-template-columns: 1fr;
                gap: 16px;
            }
        }

        @media (max-width: 600px) {
            .gallery-thumb {
                width: 80px;
                height: 56px;
            }

            .cta-buttons {
                flex-direction: column;
            }
        }
    </style>
</head>

<body>

    <div class="container">

        <!-- ════ HEADER════ -->
        <?php include 'header.php'; ?>

        <!-- ════ BACK LINK ════ -->
        <a href="residents.php" class="back-link">
            <i class="fas fa-chevron-left"></i> Back to Results
        </a>

        <!-- ════ PET DETAIL ════ -->
        <div class="pet-detail">

            <!-- ── LEFT: Photo gallery ── -->
            <div class="gallery">
                <img
                    src="<?= h($pet['gallery'][0] ?? $pet['image']) ?>"
                    alt="<?= h($pet['name']) ?>"
                    class="gallery-main"
                    id="mainPhoto"
                    onerror="this.src='placeholder.jpg'; this.style.background='#ddd';">
                <?php if (count($pet['gallery']) > 1): ?>
                    <div class="gallery-thumbs">
                        <?php foreach ($pet['gallery'] as $i => $img): ?>
                            <img
                                src="<?= h($img) ?>"
                                alt="<?= h($pet['name']) ?> photo <?= $i + 1 ?>"
                                class="gallery-thumb <?= $i === 0 ? 'active' : '' ?>"
                                onclick="switchPhoto(this, '<?= h($img) ?>')"
                                onerror="this.style.display='none';">
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- ── RIGHT: Info panel ── -->
            <div class="pet-info-panel">

                <!-- Name + Breed -->
                <div>
                    <div class="pet-name"><?= h($pet['name']) ?></div>
                    <div class="pet-breed-line"><span>BREED: </span><?= h($pet['breed']) ?></div>
                </div>

                <!-- Data-driven type / gender / age tags -->
                <div class="basic-tags">
                    <span class="tag-basic <?= type_class($pet['type']) ?>">
                        <?= h($pet['type']) ?>
                    </span>
                    <span class="tag-basic <?= gender_class($pet['gender']) ?>">
                        <?= h($pet['gender']) ?>
                    </span>
                    <span class="tag-basic <?= age_class($pet['age']) ?>">
                        <?= h($pet['age']) ?>
                    </span>
                </div>

                <hr class="info-divider">

                <!-- Personality & Trait tags -->
                <div>
                    <div class="section-label">Personality &amp; Traits tags:</div>
                    <div class="trait-tags">
                        <?php foreach ($pet['traits'] as $trait): ?>
                            <span class="tag-trait"><?= h($trait) ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>

                <hr class="info-divider">

                <!-- Likes / Dislikes -->
                <div class="like-dislike">
                    <div class="ld-block">
                        <div class="section-label">Likes:</div>
                        <ul class="ld-list">
                            <?php foreach ($pet['likes'] as $like): ?>
                                <li><?= h($like) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <div class="ld-block">
                        <div class="section-label">Dislikes:</div>
                        <ul class="ld-list">
                            <?php foreach ($pet['dislikes'] as $dislike): ?>
                                <li><?= h($dislike) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>

                <hr class="info-divider">

                <!-- Description -->
                <div class="pet-description-block">
                    <div class="section-label">Pet Description:</div>
                    <p class="pet-description"><?= h($pet['description']) ?></p>
                </div>

                <!-- CTA Buttons -->
                <div class="cta-buttons">
                    <a href="adoptform.php?pet=<?= h($pet['id']) ?>" class="btn-cta btn-adopt">ADOPT</a>
                    <a href="fosterform.php?pet=<?= h($pet['id']) ?>" class="btn-cta btn-foster">FOSTER</a>
                </div>

            </div><!-- /.pet-info-panel -->
        </div><!-- /.pet-detail -->

    </div><!-- /.container -->

    <script>
        function switchPhoto(thumb, src) {
            // Swap main image
            const main = document.getElementById('mainPhoto');
            main.style.opacity = '0';
            setTimeout(() => {
                main.src = src;
                main.style.opacity = '1';
            }, 150);

            // Update active thumb
            document.querySelectorAll('.gallery-thumb').forEach(t => t.classList.remove('active'));
            thumb.classList.add('active');
        }
    </script>

</body>

</html>