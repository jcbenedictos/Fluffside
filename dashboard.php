<?php 
session_start();
require_once 'db.inc.php';

$is_logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;


 if (!$is_logged_in) {
     header("Location: login.php?msg=login_required");
     exit; 
 }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Applications - Dashboard | FluffSide</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="dashboardstyle.css">
</head>
<body>

    <div class="container">
       <!-- ════ HEADER ════ -->
        <?php include 'header.php'; ?> 

        <div class="dash-header-section">
            <h1>My Applications</h1>
            <p>Track your adoption and foster applications in real time.</p>
        </div>

        <main class="dashboard-layout">
            
            <div class="dash-main">
                
                <div class="dash-tabs">
                    <button class="tab-btn active">Active Applications</button>
                    <button class="tab-btn">Past Applications</button>
                    <button class="tab-btn">Messages</button>
                </div>

                <div class="app-card">
                    <div class="app-card-top">
                        <div class="app-pet-info">
                            <img src="scout.jpg" alt="Scout" class="app-pet-img" onerror="this.src='placeholder.jpg';">
                            <div class="app-pet-details">
                                <h2>SCOUT</h2>
                                <p>Golden Retriever</p>
                                <span class="tag-type tag-adoption">ADOPTION</span>
                            </div>
                        </div>
                        <div class="app-status-area">
                            <div class="status-badge status-review">Under Review</div>
                            <div class="submit-date">
                                Submitted on
                                <strong>May 14, 2026</strong>
                            </div>
                        </div>
                    </div>

                    <div class="stepper-container">
                        <div class="step completed">
                            <div class="step-icon"><i class="fas fa-check"></i></div>
                            <span class="step-label">Application<br>Submitted</span>
                        </div>
                        <div class="step-line line-green"></div>
                        
                        <div class="step completed">
                            <div class="step-icon"><i class="fas fa-check"></i></div>
                            <span class="step-label">Reviewed</span>
                        </div>
                        <div class="step-line line-green"></div>
                        
                        <div class="step action-yellow">
                            <div class="step-icon"><i class="fas fa-clock"></i></div>
                            <span class="step-label">Online<br>Interview</span>
                        </div>
                        <div class="step-line"></div> <!-- Gray line -->
                        
                        <div class="step">
                            <div class="step-icon"></div>
                            <span class="step-label">Application<br>Approval</span>
                        </div>
                        <div class="step-line"></div>
                        
                        <div class="step">
                            <div class="step-icon"></div>
                            <span class="step-label">Meet and<br>Greet</span>
                        </div>
                        <div class="step-line"></div>
                        
                        <div class="step">
                            <div class="step-icon"></div>
                            <span class="step-label">Take Home</span>
                        </div>
                    </div>

                    <div class="app-card-bottom">
                        <div class="update-msg">
                            <strong>Last Update:</strong> We've reviewed your application and would like to schedule an interview, please check your messages and coordinate with us.
                        </div>
                        <button class="btn-cancel">Cancel</button>
                    </div>
                </div>

                <div class="app-card">
                    <div class="app-card-top">
                        <div class="app-pet-info">
                            <img src="#" alt="#" class="app-pet-img" onerror="this.src='placeholder.jpg';">
                            <div class="app-pet-details">
                                <h2>BENNY</h2>
                                <p>Persian Cat</p>
                                <span class="tag-type tag-foster">FOSTER</span>
                            </div>
                        </div>
                        <div class="app-status-area">
                            <div class="status-badge status-approved">Approved</div>
                            <div class="submit-date">
                                Submitted on
                                <strong>May 11, 2026</strong>
                            </div>
                        </div>
                    </div>

                    <div class="stepper-container">
                        <div class="step completed">
                            <div class="step-icon"><i class="fas fa-check"></i></div>
                            <span class="step-label">Application<br>Submitted</span>
                        </div>
                        <div class="step-line line-green"></div>
                        
                        <div class="step completed">
                            <div class="step-icon"><i class="fas fa-check"></i></div>
                            <span class="step-label">Reviewed</span>
                        </div>
                        <div class="step-line line-green"></div>
                        
                        <div class="step completed">
                            <div class="step-icon"><i class="fas fa-check"></i></div>
                            <span class="step-label">Online<br>Interview</span>
                        </div>
                        <div class="step-line line-green"></div>
                        
                        <div class="step completed">
                            <div class="step-icon"><i class="fas fa-check"></i></div>
                            <span class="step-label">Application<br>Approved</span>
                        </div>
                        <div class="step-line line-green"></div>
                        
                        <div class="step action-green">
                            <div class="step-icon"><i class="fas fa-exclamation"></i></div>
                            <span class="step-label">Meet and<br>Greet</span>
                        </div>
                        <div class="step-line"></div>
                        
                        <div class="step">
                            <div class="step-icon"></div>
                            <span class="step-label">Take Home</span>
                        </div>
                    </div>

                    <div class="app-card-bottom">
                        <div class="update-msg">
                            <strong>Last Update:</strong> We've reviewed your application and would like to schedule an interview, please check your messages and coordinate with us.
                        </div>
                        <button class="btn-cancel" disabled>Cancel</button> <!-- Disabled Cancel Button -->
                    </div>
                </div>

            </div>

            <aside class="dash-sidebar">

                <div class="side-card">
                    <h3>Application Summary</h3>
                    
                    <div class="summary-list">
                        <div class="summary-item">
                            <div class="sum-icon icon-orange"><i class="fas fa-file-alt"></i></div>
                            <div class="sum-text">
                                <h4>2</h4>
                                <p>Active Applications</p>
                            </div>
                        </div>

                        <div class="summary-item">
                            <div class="sum-icon icon-yellow"></div>
                            <div class="sum-text">
                                <h4>1</h4>
                                <p>Approved</p>
                            </div>
                        </div>

                        <div class="summary-item">
                            <div class="sum-icon icon-green"></div>
                            <div class="sum-text">
                                <h4>1</h4>
                                <p>In Progress</p>
                            </div>
                        </div>

                        <div class="summary-item">
                            <div class="sum-icon icon-red"><i class="fas fa-times"></i></div>
                            <div class="sum-text">
                                <h4>2</h4>
                                <p>Withdrawn/ Declined</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="side-card">
                    <h3>Quick Links</h3>
                    
                    <div class="quick-links-list">
                        <a href="residents.php" class="btn-quick-link">Browse Available Residents</a>
                        <a href="supplies.php" class="btn-quick-link">Check Out Pet Supplies</a>
                        <a href="help.php" class="btn-quick-link">Help Center</a>
                    </div>
                </div>

            </aside>
        </main>
    </div>

    <?php include 'footer.php'; ?>

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
</body>
</html>