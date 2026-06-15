<?php 
session_start();
date_default_timezone_set('Asia/Manila');

require_once 'db.inc.php';
require_once 'db_helper.inc.php';
require_once 'pets.inc.php';

$homepage_stats = get_homepage_stats();

function h(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}
function type_class(string $type): string {
    return match (strtoupper($type)) {
        'DOG'     => 'tag-dog',
        'CAT'     => 'tag-cat',
        'RABBIT'  => 'tag-rabbit',
        'HAMSTER' => 'tag-hamster',
        default   => 'tag-gray',
    };
}
function gender_class(string $gender): string {
    return strtoupper($gender) === 'MALE' ? 'tag-male' : 'tag-female';
}
function age_class(string $age, string $age_group = ''): string {
    if (stripos($age, 'week') !== false || stripos($age, 'month') !== false)
        return 'tag-age-baby';
    return strtoupper($age_group) === 'SENIOR' ? 'tag-age-senior' : 'tag-age';
}

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php?msg=login_required");
    exit; 
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FluffSide - Pet Shelter</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="container">

        <?php include 'header.php'; ?>

        <section class="hero">
            <div class="hero-content">
                <div class="trust-badge">FLUFFSIDE, YOUR TRUSTED COMMUNITY PET SHELTER.</div>
                <h1>Where Gentle Hearts<br>Meet Waiting <span>Paws</span></h1>
                <h3>Looking for your future furry companion?</h3>
                <p>Fluffside offers a safe and welcoming platform where fur residents can find loving adopters, temporary foster families, and the care they deserve through a compassionate community.</p>
                <div class="cta-group">
                    <a href="residents.php" class="btn"><i class="fas fa-paw"></i> MEET RESIDENTS</a>
                    <a href="supplies.php" class="btn btn-outline"><i class="fas fa-bone"></i> BUY SUPPLIES</a>
                </div>
                <div class="guarantee">
                    <i class="fas fa-check-square"></i>
                    <span>Residents listed on Fluffside are vaccinated and carefully prepared before entering adoption or foster programs.</span>
                </div>
            </div>
            <div class="hero-visuals">
                <img src="Assets/Rightvisuals.png" alt="Pets" class="hero-image" onerror="this.style.display='none'">
            </div>
        </section>

        <section class="dashboard">
            <div class="dash-left">
                <h4>TODAY AT FLUFFSIDE</h4>
                <div class="date" id="currentDate"><?php echo date('D, M j'); ?></div>
                <div class="time" id="currentTime"><?php echo date('h:i:s a') . ' PHT'; ?></div>
            </div>
            <div class="dash-right">
                <div class="stat-item"><div class="stat-icon"><i class="fas fa-paw"></i></div><div class="stat-text"><h2><?= $homepage_stats['waiting'] ?></h2><p>Residents Waiting</p></div></div>
                <div class="stat-item"><div class="stat-icon"><i class="fas fa-home"></i></div><div class="stat-text"><h2><?= $homepage_stats['adopted'] ?></h2><p>Recently Adopted</p></div></div>
                <div class="stat-item"><div class="stat-icon"><i class="fas fa-dog"></i></div><div class="stat-text"><h2><?= $homepage_stats['fostered'] ?></h2><p>Recently Fostered</p></div></div>
            </div>
        </section>

        <section class="how-it-works">
            <h2 class="section-title">HOW FLUFFSIDE <span>WORKS?</span></h2>
            <div class="steps-grid">
                <div class="step-card" style="background-color: var(--bg-yellow-light);">
                    <h4>1. Find a Resident</h4><p>Browse residents and find the one who matches your lifestyle and heart.</p>
                </div>
                <div class="step-card" style="background-color: var(--bg-pink-light);">
                    <h4>2. Apply to Foster or Adopt</h4><p>Fill out the application so we can get to know you better.</p>
                </div>
                <div class="step-card" style="background-color: var(--accent-green-light);">
                    <h4>3. We Review Your Application</h4><p>Our team will review and reach out for the next steps.</p>
                </div>
                <div class="step-card" style="background-color: var(--bg-blue-light);">
                    <h4>4. Start Your Journey</h4><p>Welcome your new family member or foster resident into a happier life.</p>
                </div>
            </div>
        </section>

        <section class="featured-pets">

            <div class="section-heading">
                <h2 class="section-title">
                    MEET OUR <span>RESIDENTS</span>
                </h2>

                <p>
                    Some of our adorable residents currently waiting for a loving home.
                </p>
            </div>

            <div class="featured-slider">
                <div class="pet-cards" id="petSlider">
                    <?php
                    $sample = array_slice(array_values($pets), 0, 6);
                    foreach ($sample as $pet):
                    ?>
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
                            <div class="basic-tags">
                                <span class="tag-basic <?= type_class($pet['type']) ?>"><?= h($pet['type']) ?></span>
                                <span class="tag-basic <?= gender_class($pet['gender']) ?>"><?= h($pet['gender']) ?></span>
                                <span class="tag-basic <?= age_class($pet['age'], $pet['age_group']) ?>"><?= h($pet['age']) ?></span>
                            </div>
                            <div class="trait-tags">
                                <?php foreach (array_slice($pet['traits'], 0, 3) as $trait): ?>
                                    <span class="tag-trait"><?= h($trait) ?></span>
                                <?php endforeach; ?>
                            </div>
                            <a href="pet.php?id=<?= h($pet['id']) ?>" class="btn-view">VIEW</a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="slider-arrow" id="slideRight"><i class="fas fa-chevron-right"></i></div>
                <div class="slider-arrow left" id="slideLeft">
                    <i class="fas fa-chevron-left"></i>
                </div>
                <a href="residents.php" class="btn btn-square">View All Residents</a>
            </div>


            
        </section>

        <section class="supply-banner">
            <img src="Assets/Supply.png" alt="Supplies" class="supply-img-large" onerror="this.style.display='none'">
            <div class="supply-content-center">
                <h2>CHECK OUT NEW TOYS AND<br>FOOD AT THE SUPPLY SHOP!</h2>
                <p>From treats to toys, Fluffside has all the essentials.<br>10% of every purchase goes to supporting rescued animals.</p>
                <a href="supplies.php" class="btn btn-square" style="background-color: var(--btn-green); border: none;">SHOP NOW</a>
            </div>
            <img src="Assets/supplies.png" alt="Supplies" class="supply-img-large" onerror="this.style.display='none'">
        </section>

    </div>

    <?php include 'footer.php'; ?>
    
    <script>
        document.getElementById('slideLeft').addEventListener('click', function() {
            const slider = document.getElementById('petSlider');
            const cardWidth = slider.querySelector('.pet-card').offsetWidth + 25;

            slider.scrollBy({ left: -cardWidth, behavior: 'smooth' });
        });

        document.getElementById('slideRight').addEventListener('click', function() {
            const slider = document.getElementById('petSlider');
            const cardWidth = slider.querySelector('.pet-card').offsetWidth + 25; 
            slider.scrollBy({ left: cardWidth, behavior: 'smooth' });
            if (slider.scrollLeft + slider.clientWidth >= slider.scrollWidth - 10) {
                slider.scrollTo({ left: 0, behavior: 'smooth' });
            }
        });

        const currentDateEl = document.getElementById('currentDate');
        const currentTimeEl = document.getElementById('currentTime');
        let currentTimestamp = <?php echo time() * 1000; ?>;

        function pad(value) {
            return value.toString().padStart(2, '0');
        }

        function updateClock() {
            const now = new Date(currentTimestamp);
            const dateString = now.toLocaleDateString('en-US', {
                weekday: 'short',
                month: 'short',
                day: 'numeric'
            });
            const timeString = `${pad(now.getHours() % 12 || 12)}:${pad(now.getMinutes())}:${pad(now.getSeconds())} ${now.getHours() >= 12 ? 'PM' : 'AM'} PHT`;
            currentDateEl.textContent = dateString;
            currentTimeEl.textContent = timeString;
        }

        setInterval(() => {
            currentTimestamp += 1000;
            updateClock();
        }, 1000);

        updateClock();
    </script>

    <?php 
    $profile_incomplete = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true 
        && (empty($_SESSION['address']) || empty($_SESSION['phone']) || empty($_SESSION['dob']));
    ?>
    <?php if ($profile_incomplete): ?>
    <div id="finishProfileModal" style="display:flex;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:9999;justify-content:center;align-items:center;">
        <div style="background:#F5F1ED;border:2px solid #EF8E35;border-radius:20px;padding:40px;max-width:420px;width:90%;text-align:center;font-family:'Nunito',sans-serif;">
            <i class="fas fa-paw" style="font-size:36px;color:#EF8E35;margin-bottom:15px;"></i>
            <h2 style="font-size:22px;font-weight:900;color:#5A483E;margin-bottom:10px;">Complete Your Profile!</h2>
            <p style="font-size:14px;font-weight:600;color:#8E8279;margin-bottom:30px;">Help us get to know you better. Fill in your remaining details so we can match you with the perfect pet!</p>
            <div style="display:flex;gap:15px;justify-content:center;">
                <a href="profile.php?highlight=1" style="background:#EF8E35;color:#fff;padding:12px 24px;border-radius:25px;text-decoration:none;font-weight:800;font-size:14px;">Go to Profile</a>
                <button onclick="document.getElementById('finishProfileModal').style.display='none'" style="background:transparent;border:2px solid #EF8E35;color:#5A483E;padding:12px 24px;border-radius:25px;font-weight:800;font-size:14px;cursor:pointer;font-family:'Nunito',sans-serif;">Skip, Do It Later</button>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <script>
        // 30-second inactivity logout
        let inactivityTimer;
        function resetTimer() {
            clearTimeout(inactivityTimer);
            inactivityTimer = setTimeout(function() {
                window.location.href = 'logout.php?reason=inactive';
            }, 60000);
        }
        ['mousemove','keydown','click','scroll','touchstart'].forEach(function(e) {
            document.addEventListener(e, resetTimer);
        });
        resetTimer();
    </script>
</body>
</html>