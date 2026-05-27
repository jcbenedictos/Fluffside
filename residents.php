<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php?msg=login_required");
    exit;
}

require_once 'pets.inc.php';

// ── Search ────────────────────────────────────────────────────
$search_query = trim($_GET['search'] ?? '');
$filtered = $pets;

if ($search_query !== '') {
    $filtered = array_filter($pets, function ($pet) use ($search_query) {
        if (stripos($pet['name'],  $search_query) !== false) return true;
        if (stripos($pet['breed'], $search_query) !== false) return true;
        if (stripos($pet['type'],  $search_query) !== false) return true;
        foreach ($pet['traits'] as $t) {
            if (stripos($t, $search_query) !== false) return true;
        }
        return false;
    });
}

// ── Filter (checkbox) ─────────────────────────────────────────
$filter_types   = $_GET['type']   ?? [];  // e.g. ['DOG','CAT']
$filter_ages    = $_GET['age']    ?? [];  // e.g. ['Young','Adult', 'Senior']
$filter_genders = $_GET['gender'] ?? [];  // e.g. ['MALE']

if (!empty($filter_types)) {
    $filtered = array_filter($filtered, fn($p) => in_array(strtoupper($p['type']), array_map('strtoupper', $filter_types)));
}
if (!empty($filter_ages)) {
    $filtered = array_filter($filtered, fn($p) => in_array($p['age_group'], $filter_ages));
}
if (!empty($filter_genders)) {
    $filtered = array_filter($filtered, fn($p) => in_array(strtoupper($p['gender']), array_map('strtoupper', $filter_genders)));
}

//-trait tags-//
$filter_traits = $_GET['trait'] ?? [];

if (!empty($filter_traits)) {
    $filtered = array_filter($filtered, function($p) use ($filter_traits) {
        foreach ($filter_traits as $ft) {
            foreach ($p['traits'] as $t) {
                if (stripos($t, $ft) !== false) return true;
            }
        }
        return false;
    });
}

function h(string $s): string
{
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

function type_class(string $type): string
{
    return match (strtoupper($type)) {
        'DOG' => 'tag-dog',
        'CAT' => 'tag-cat',
        'RABBIT' => 'tag-rabbit',
        'HAMSTER' => 'tag-hamster',
        default => 'tag-gray',
    };
}
function gender_class(string $gender): string
{
    return strtoupper($gender) === 'MALE' ? 'tag-male' : 'tag-female';
}
function age_class(string $age, string $age_group = ''): string
{
    if (stripos($age, 'week') !== false || stripos($age, 'month') !== false) {
        return 'tag-age-baby';
    }

    return strtoupper($age_group) === 'SENIOR' ? 'tag-age-senior' : 'tag-age';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Residents — FluffSide</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-orange: #EF8E35;
            --primary-hover: #D67A26;
            --bg-light: #FDFBF5;
            --text-dark: #5A483E;
            --text-light: #8E8279;
            --box-yellow: #FFF9EE;
            --box-green-header: #EAE6D1;
            --tag-dog: #FEE8BE;
            --tag-cat: #FEE8BE;
            --tag-rabbit: #ECD4F4;
            --tag-hamster: #FEE8BE;
            --tag-age: #FBD9BE;
            --tag-male: #E4F1FB;
            --tag-female: #FDEAE3;
            --tag-gray: #F0F0F0;
            --white: #FFFFFF;
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

        .page-layout {
            display: flex;
            gap: 40px;
            margin-top: 20px;
            margin-bottom: 60px;
            align-items: flex-start;
        }

        /* ── Sidebar ── */
        .sidebar {
            flex: 0 0 300px;
            position: sticky;
            top: 20px;
        }

        .sidebar h1 {
            font-size: 32px;
            font-weight: 900;
            line-height: 1.2;
            margin-bottom: 10px;
        }

        .sidebar p.subtitle {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .search-box {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--primary-orange);
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 30px;
            outline: none;
            background: var(--white);
        }

        .filter-container {
            background-color: var(--box-yellow);
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid #EAE3D9;
            margin-bottom: 30px;
        }

        .filter-header {
            background-color: var(--box-green-header);
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 900;
        }

        .filter-header a {
            font-size: 12px;
            font-weight: 700;
            color: var(--text-dark);
            text-decoration: none;
        }

        .filter-header a:hover {
            color: var(--primary-orange);
        }

        .filter-body {
            padding: 20px;
        }

        .filter-group {
            margin-bottom: 20px;
        }

        .filter-group-title {
            display: flex;
            justify-content: space-between;
            font-weight: 800;
            font-size: 14px;
            margin-bottom: 10px;
            text-transform: uppercase;
            cursor: pointer;
            user-select: none;
        }

        .filter-items {
            display: flex;
            flex-direction: column;
            gap: 0;
        }

        .filter-item {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 8px;
            font-size: 13px;
            font-weight: 600;
        }

        .filter-item input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: var(--primary-orange);
            cursor: pointer;
        }

        .filter-item label {
            cursor: pointer;
        }

        .btn-apply {
            width: 100%;
            background-color: var(--tag-yellow);
            border: 1px solid #EAE3D9;
            padding: 12px;
            border-radius: 8px;
            font-weight: 800;
            cursor: pointer;
            transition: .2s;
            font-size: 13px;
        }

        .btn-apply:hover {
            background-color: #f5dc90;
        }

        .info-box {
            background-color: var(--box-yellow);
            border-radius: 12px;
            padding: 30px 25px;
            border: 1px solid #EAE3D9;
        }

        .info-section {
            margin-bottom: 30px;
        }

        .info-section:last-child {
            margin-bottom: 0;
        }

        .info-section h3 {
            font-size: 15px;
            font-weight: 900;
            text-transform: uppercase;
            margin-bottom: 15px;
        }

        .info-section p,
        .info-section li {
            font-size: 12px;
            line-height: 1.6;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .info-section ol {
            padding-left: 15px;
        }

        .info-section a {
            color: var(--primary-orange);
            text-decoration: none;
            font-weight: 700;
        }

        .info-section a:hover {
            text-decoration: underline;
        }

        /* ── Main content ── */
        .main-content {
            flex: 1;
            min-width: 0;
        }

        .match-banner {
            background-color: var(--box-yellow);
            border: 1px solid #EAE3D9;
            border-radius: 15px;
            padding: 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            position: relative;
            overflow: hidden;
        }

        .banner-text {
            position: relative;
            z-index: 2;
            max-width: 400px;
        }

        .banner-text h2 {
            font-size: 28px;
            font-weight: 900;
            margin-bottom: 10px;
        }

        .banner-text p {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .btn-match {
            background-color: var(--primary-orange);
            color: var(--white);
            padding: 12px 24px;
            border-radius: 8px;
            border: none;
            font-weight: 800;
            cursor: pointer;
            font-size: 14px;
            font-family: 'Nunito', sans-serif;
        }

        .btn-match:hover {
            background-color: var(--primary-hover);
        }

        .banner-image {
            position: absolute;
            right: -20px;
            top: 0;
            height: 110%;
            mix-blend-mode: multiply;
            z-index: 1;
        }

        .results-count {
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 20px;
            color: var(--text-dark);
        }

        /* ── Pet grid ── */
        .pet-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .pet-card {
            background-color: var(--white);
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid #EAE3D9;
            display: flex;
            flex-direction: column;
            transition: transform .2s, box-shadow .2s;
        }

        .pet-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, .05);
        }

        .pet-img {
            width: 100%;
            height: 250px;
            object-fit: cover;
            background-color: #f0f0f0;
            display: block;
        }

        .pet-info {
            padding: 20px;
            display: flex;
            flex-direction: column;
            flex: 1;
        }

        .pet-header {
            margin-bottom: 15px;
        }

        .pet-header h3 {
            font-size: 18px;
            font-weight: 900;
            text-transform: uppercase;
            line-height: 1;
            margin-bottom: 4px;
        }

        .pet-header p {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-light);
        }

        .basic-tags {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-bottom: 15px;
        }

        .tag-basic {
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: 800;
            text-transform: uppercase;
        }

        .tag-dog {
            background: var(--tag-dog);
        }

        .tag-cat {
            background: var(--tag-cat);
        }

        .tag-rabbit {
            background: var(--tag-rabbit);
        }

        .tag-hamster {
            background: var(--tag-hamster);
        }

        .tag-male {
            background: var(--tag-male);
        }

        .tag-female {
            background: var(--tag-female);
        }

        .tag-age,
        .tag-age-baby,
        .tag-age-senior {
            background: var(--tag-age);
        }

        .tag-gray {
            background: var(--tag-gray);
        }

        .trait-tags {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
            margin-bottom: 25px;
        }

        .tag-trait {
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: 700;
            border: 1px solid var(--primary-orange);
            color: var(--text-dark);
        }

        /* VIEW button — links to pet.php?id=xxx */
        .btn-view {
            margin-top: auto;
            width: 60%;
            align-self: center;
            background-color: var(--primary-orange);
            color: var(--white);
            padding: 10px;
            border-radius: 20px;
            border: none;
            font-weight: 900;
            font-size: 13px;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
            display: block;
            transition: .2s;
            font-family: 'Nunito', sans-serif;
        }

        .btn-view:hover {
            background-color: var(--primary-hover);
        }

        /* No results */
        .no-results {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-light);
            font-size: 15px;
            font-weight: 600;
        }

        .no-results i {
            font-size: 40px;
            margin-bottom: 12px;
            display: block;
        }

        @media (max-width:1024px) {
            .pet-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width:768px) {
            .page-layout {
                flex-direction: column;
            }

            .sidebar {
                flex: none;
                width: 100%;
                position: static;
            }
        }

        @media (max-width:480px) {
            .pet-grid {
                grid-template-columns: 1fr;
            }
        }

         .trait-filter-cloud {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 14px;
        }

        .trait-tag {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 800;
            border: 1.5px solid var(--primary-orange);
            color: var(--text-dark);
            cursor: pointer;
            transition: background .15s, color .15s;
            user-select: none;
        }

        .trait-tag input[type="checkbox"] {
            display: none;
        }

        .trait-tag:hover {
            background: #fff3e6;
        }

        .trait-tag-active {
            background: var(--primary-orange);
            color: var(--white);
        }

        .trait-clear-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            font-weight: 700;
            color: var(--text-light);
            text-decoration: none;
            margin-top: 4px;
            transition: color .2s;
        }

        .trait-clear-btn:hover {
            color: var(--primary-orange);
        }
        
    </style>
</head>

<body>
    <div class="container">
        <!-- ════ HEADER ════ -->
        <?php include 'header.php'; ?>
        
        <main class="page-layout">

            <!-- ════ SIDEBAR ════ -->
            <aside class="sidebar">
                <h1>Find your<br>perfect match!</h1>
                <p class="subtitle">Browse lovable pets looking<br>for their forever homes.</p>

                <!-- Search + Filter in one form so GET params stay together -->
                <form method="GET" action="residents.php" id="filterForm">

                    <input type="text" name="search" class="search-box"
                        placeholder="Search by breed, animal type or keywords"
                        value="<?= h($search_query) ?>">

                    <div class="filter-container">
                        <div class="filter-header">
                            <span>Filters</span>
                            <a href="residents.php">Clear all</a>
                        </div>
                        <div class="filter-body">

                            <div class="filter-group">
                                <div class="filter-group-title" onclick="toggleGroup('group-type')">
                                    PET TYPE <i class="fas fa-chevron-up" id="icon-group-type"></i>
                                </div>
                                <div class="filter-items" id="group-type">
                                    <?php foreach (['Cat', 'Dog', 'Rabbit', 'Hamster', 'Bird'] as $t): ?>
                                        <label class="filter-item">
                                            <input type="checkbox" name="type[]" value="<?= h($t) ?>"
                                                <?= in_array(strtolower($t), array_map('strtolower', $filter_types)) ? 'checked' : '' ?>>
                                            <?= h($t) ?>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <div class="filter-group">
                                <div class="filter-group-title" onclick="toggleGroup('group-age')">
                                    AGE <i class="fas fa-chevron-up" id="icon-group-age"></i>
                                </div>
                                <div class="filter-items" id="group-age">
                                    <?php foreach (['Young', 'Adult', 'Senior'] as $a): ?>
                                        <label class="filter-item">
                                            <input type="checkbox" name="age[]" value="<?= h($a) ?>"
                                                <?= in_array($a, $filter_ages) ? 'checked' : '' ?>>
                                            <?= h($a) ?>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <div class="filter-group">
                                <div class="filter-group-title" onclick="toggleGroup('group-sex')">
                                    SEX <i class="fas fa-chevron-up" id="icon-group-sex"></i>
                                </div>
                                <div class="filter-items" id="group-sex">
                                    <?php foreach (['Female', 'Male'] as $g): ?>
                                        <label class="filter-item">
                                            <input type="checkbox" name="gender[]" value="<?= h($g) ?>"
                                                <?= in_array(strtolower($g), array_map('strtolower', $filter_genders)) ? 'checked' : '' ?>>
                                            <?= h($g) ?>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <button type="submit" class="btn-apply">Apply Filter</button>
                        </div>
                    </div>

                </form>

                <div class="info-box">
                    <div class="info-section">
                        <h3>ADOPTION PROCESS</h3>
                        <p>All of our adoptable cats and dogs are <i>already spayed/neutered (kapon)</i> and vaccinated before they are placed into loving homes.</p>
                    </div>
                    <div class="info-section">
                        <h3>HOW TO APPLY?</h3>
                        <ol>
                            <li>Complete the foster/adoption application form.</li>
                            <li><a href="dashboard.php">Check Dashboard</a> to monitor your application. Once approved, attend a short Zoom interview.</li>
                            <li>Visit the shelter and meet our rescued animals.</li>
                            <li>Spend time with your chosen pet to confirm the match.</li>
                            <li>Wait for veterinary clearance and pick-up scheduling.</li>
                            <li>Settle the adoption fee:
                                <ul style="padding-left:14px; margin-top:4px;">
                                    <li>₱500 for cats</li>
                                    <li>₱1,000 for dogs</li>
                                </ul>
                            </li>
                            <li>Welcome your new companion home!</li>
                        </ol>
                    </div>
                    <div class="info-section">
                        <h3>WHY ADOPT?</h3>
                        <p>Adopting gives rescued animals a second chance at a safe and loving life. Every adoption also helps shelters rescue and care for more pets in need.</p>
                    </div>
                    <div class="info-section">
                        <h3>WHY FOSTER?</h3>
                        <p>Fostering provides temporary love and care while animals wait for their forever homes. Even short-term fostering can make a huge difference.</p>
                    </div>
                </div>
            </aside>

            <!-- ════ MAIN CONTENT ════ -->
            <section class="main-content">

                     <div class="match-banner">
                        <div class="banner-text">
                            <h2>Match Corner</h2>
                            <p>Pick traits that match your lifestyle — we'll show you residents that fit!</p>

                            <form method="GET" action="residents.php" id="traitForm">
                                <?php if ($search_query): ?>
                                    <input type="hidden" name="search" value="<?= h($search_query) ?>">
                                <?php endif; ?>
                                <?php foreach ($filter_types as $ft): ?>
                                    <input type="hidden" name="type[]" value="<?= h($ft) ?>">
                                <?php endforeach; ?>
                                <?php foreach ($filter_ages as $fa): ?>
                                    <input type="hidden" name="age[]" value="<?= h($fa) ?>">
                                <?php endforeach; ?>
                                <?php foreach ($filter_genders as $fg): ?>
                                    <input type="hidden" name="gender[]" value="<?= h($fg) ?>">
                                <?php endforeach; ?>

                                <div class="trait-filter-cloud">
                                    <?php
                                    $all_traits = ['Playful', 'Sweet', 'Protective', 'Gentle', 'Quiet',
                                                'Social', 'Energetic', 'Independent', 'Shy', 'Smart',
                                                'Affectionate', 'Curious', 'Friendly'];
                                    foreach ($all_traits as $trait):
                                        $active = in_array($trait, $filter_traits) ? 'trait-tag-active' : '';
                                    ?>
                                        <label class="trait-tag <?= $active ?>">
                                            <input type="checkbox" name="trait[]" value="<?= h($trait) ?>"
                                                <?= $active ? 'checked' : '' ?>
                                                onchange="document.getElementById('traitForm').submit()">
                                            <?= h($trait) ?>
                                        </label>
                                    <?php endforeach; ?>
                                </div>

                                <?php if (!empty($filter_traits)): ?>
                                    <?php
                                    $keep = [];
                                    if ($search_query) $keep[] = 'search=' . urlencode($search_query);
                                    foreach ($filter_types   as $v) $keep[] = 'type[]='   . urlencode($v);
                                    foreach ($filter_ages    as $v) $keep[] = 'age[]='    . urlencode($v);
                                    foreach ($filter_genders as $v) $keep[] = 'gender[]=' . urlencode($v);
                                    $clear_url = 'residents.php' . ($keep ? '?' . implode('&', $keep) : '');
                                    ?>
                                    <a href="<?= h($clear_url) ?>" class="trait-clear-btn">
                                        <i class="fas fa-times"></i> Clear trait filters
                                    </a>
                                <?php endif; ?>
                            </form>
                        </div>
                        <img src="Assets/banner-dogs.png" alt="Happy Dogs" class="banner-image" onerror="this.style.display='none'">
                    </div>

                <p class="results-count">
                    Showing <strong><?= count($filtered) ?></strong>
                    <?= count($filtered) === 1 ? 'resident' : 'residents' ?> at FluffSide
                    <?= $search_query !== '' ? '— results for "<strong>' . h($search_query) . '</strong>"' : '' ?>
                </p>

                <?php if (empty($filtered)): ?>
                    <div class="no-results">
                        <i class="fas fa-paw"></i>
                        No pets found matching your search. Try different keywords or clear the filters.
                    </div>
                <?php else: ?>
                    <div class="pet-grid">
                        <?php foreach ($filtered as $pet): ?>
                            <div class="pet-card">
                                <img src="<?= h($pet['image']) ?>"
                                    alt="<?= h($pet['name']) ?>"
                                    class="pet-img"
                                    onerror="this.src='placeholder.jpg'; this.style.backgroundColor='#ddd';">

                                <div class="pet-info">
                                    <div class="pet-header">
                                        <h3><?= h($pet['name']) ?></h3>
                                        <p><?= h($pet['breed']) ?></p>
                                    </div>

                                    <!-- Tags: ALL driven by data, not hardcoded -->
                                    <div class="basic-tags">
                                        <span class="tag-basic <?= type_class($pet['type']) ?>"><?= h($pet['type']) ?></span>
                                        <span class="tag-basic <?= gender_class($pet['gender']) ?>"><?= h($pet['gender']) ?></span>
                                        <span class="tag-basic <?= age_class($pet['age'], $pet['age_group']) ?>"><?= h($pet['age']) ?></span>
                                    </div>

                                    <div class="trait-tags">
                                        <?php foreach ($pet['traits'] as $trait): ?>
                                            <span class="tag-trait"><?= h($trait) ?></span>
                                        <?php endforeach; ?>
                                    </div>

                                    <!-- VIEW → pet.php?id=scout (dynamic, no duplicate files) -->
                                    <a href="pet.php?id=<?= h($pet['id']) ?>" class="btn-view">VIEW</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

            </section>
        </main>
    </div>

    <?php include 'footer.php'; ?>

    <script>
        // Collapsible filter groups
        function toggleGroup(id) {
            const group = document.getElementById(id);
            const icon = document.getElementById('icon-' + id);
            const isOpen = group.style.display !== 'none';
            group.style.display = isOpen ? 'none' : '';
            icon.style.transform = isOpen ? 'rotate(180deg)' : '';
        }

        // Auto-submit on search input (optional — lets Enter also work via form)
        document.querySelector('.search-box').addEventListener('keydown', function(e) {
            if (e.key === 'Enter') document.getElementById('filterForm').submit();
        });
    </script>
</body>

</html>