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
    <title>Help & FAQs - FluffSide</title>
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
            background-color: #ffffff;
            color: var(--fs-text-dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        body {
            background: url('YOUR_HELP_BACKGROUND.png') no-repeat center top;
            background-size: 100% auto;
            background-color: #ffffff;
            color: var(--fs-text-dark);
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

        .help-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            box-sizing: border-box;
        }

        .help-section {
            padding: 60px 0;
            min-height: 550px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        #helpSectionOne {
            background: url('Assets/Help1.png') no-repeat center top;
            background-size: 100% auto;
        }

        #helpSectionTwo {
            background: url('Assets/Help2.png') no-repeat center top;
            background-size: 85% auto;
        }

        .help-main-title {
            text-align: center;
            font-size: 2.6rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0 0 10px 0;
            color: var(--title-gray);
        }

        .help-subtitle {
            text-align: center;
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--fs-orange);
            max-width: 600px;
            margin: 0 auto 50px auto;
            line-height: 1.5;
        }

        .policy-main-title {
            text-align: center;
            font-size: 2.4rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0 0 5px 0;
            color: var(--fs-orange);
        }

        .policy-subtitle {
            text-align: center;
            font-size: 1.8rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0 0 40px 0;
            color: var(--title-gray);
        }

        .faq-grid-two-col {
            display: grid;
            grid-template-columns: 1.3fr 1fr;
            gap: 40px;
            align-items: center;
        }

        .faq-grid-flipped {
            display: grid;
            grid-template-columns: 1fr 2.2fr;
            gap: 40px;
            align-items: center;
        }

        .faq-sub-cards-layout {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
        }

        .policy-grid-layout {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }

        .help-card {
            background-color: var(--fs-card-bg);
            border-radius: 30px;
            padding: 30px;
            display: flex;
            gap: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.01);
            box-sizing: border-box;
        }

        .help-card-icon {
            font-size: 2.8rem;
            color: var(--fs-orange);
            line-height: 1;
            display: flex;
            align-items: flex-start;
            padding-top: 5px;
            min-width: 50px;
        }

        .help-card-body h3 {
            margin: 0 0 12px 0;
            font-size: 1.15rem;
            font-weight: 800;
            color: #2b2b2b;
            line-height: 1.4;
            border-bottom: 2px solid var(--fs-orange);
            padding-bottom: 8px;
            display: inline-block;
        }

        .help-card-body p {
            margin: 0;
            font-size: 0.95rem;
            line-height: 1.6;
            font-weight: 600;
            color: var(--fs-text-dark);
        }

        .policy-card {
            background-color: var(--fs-card-bg);
            border-radius: 35px;
            padding: 40px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.01);
            box-sizing: border-box;
        }

        .policy-card h3 {
            margin: 0 0 25px 0;
            font-size: 1.4rem;
            font-weight: 800;
            color: var(--title-gray);
        }

        .policy-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .policy-list li {
            position: relative;
            padding-left: 20px;
            margin-bottom: 20px;
            font-size: 0.98rem;
            line-height: 1.6;
            font-weight: 600;
            color: var(--fs-text-dark);
            border-left: 3px solid var(--fs-orange);
            padding-left: 15px;
        }

        .policy-list li strong {
            color: #2b2b2b;
            font-weight: 800;
        }

        .visual-spacer-block {
            min-height: 350px;
        }

        .compact-contact-section {
            padding: 40px 0 80px 0;
            display: flex;
            justify-content: center;
        }

        .compact-contact-card {
            background-color: var(--fs-card-bg);
            border-radius: 30px;
            padding: 30px 40px;
            max-width: 500px;
            width: 100%;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.01);
            box-sizing: border-box;
        }

        .compact-contact-card h3 {
            margin: 0 0 15px 0;
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--title-gray);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .contact-details-text {
            font-size: 0.98rem;
            line-height: 1.6;
            font-weight: 700;
            color: var(--fs-text-dark);
            margin: 0 0 20px 0;
        }

        .contact-details-text i {
            color: var(--fs-orange);
            margin-right: 8px;
            width: 16px;
        }

        .contact-details-text a {
            color: var(--fs-text-dark);
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .contact-details-text a:hover {
            color: var(--fs-orange);
        }

        .social-media-links {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 15px;
        }

        .social-icon-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
            background-color: #ffffff;
            color: var(--fs-orange);
            border-radius: 50%;
            font-size: 1.25rem;
            text-decoration: none;
            transition: all 0.2s ease;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
        }

        .social-icon-btn:hover {
            background-color: var(--fs-orange);
            color: #ffffff;
            transform: translateY(-3px);
        }

        @media (max-width: 992px) {

            .faq-grid-two-col,
            .faq-grid-flipped,
            .policy-grid-layout {
                grid-template-columns: 1fr !important;
            }

            .faq-sub-cards-layout {
                grid-template-columns: 1fr;
            }

            .visual-spacer-block {
                display: none;
            }

            .help-section {
                background-size: cover !important;
                padding: 40px 0;
            }

            .compact-contact-card {
                padding: 25px 20px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <?php include 'header.php'; ?>
        
        <main class="help-container">

            <section class="help-section" id="helpSectionOne">
                <h2 class="help-main-title">How Can We Help You?</h2>
                <p class="help-subtitle">Whether you're a first-time foster or an experienced adopter, we're here to guide you every step of the way.</p>

                <div class="faq-grid-two-col">
                    <div class="faq-sub-cards-layout">

                        <div class="help-card">
                            <div class="help-card-icon"><i class="fas fa-dog"></i></div>
                            <div class="help-card-body">
                                <h3>Q. How does the adoption process work?</h3>
                                <p>Browse residents, apply, meet and finalize.</p>
                            </div>
                        </div>

                        <div class="help-card">
                            <div class="help-card-icon"><i class="fas fa-heart"></i></div>
                            <div class="help-card-body">
                                <h3>Q. What is the difference between foster and adopt?</h3>
                                <p>Foster is temporary care; adopt is forever.</p>
                            </div>
                        </div>

                        <div class="help-card">
                            <div class="help-card-icon"><i class="fas fa-bone"></i></div>
                            <div class="help-card-body">
                                <h3>Q. Do the animals have vaccinations and basic training?</h3>
                                <p>Yes! All of them are prepared before going home.</p>
                            </div>
                        </div>

                        <div class="help-card">
                            <div class="help-card-icon"><i class="fas fa-truck"></i></div>
                            <div class="help-card-body">
                                <h3>Q. How can I track my supplies order?</h3>
                                <p>Check your "Dashboard" for real-time tracking.</p>
                            </div>
                        </div>

                    </div>

                    <div class="visual-spacer-block"></div>
                </div>
            </section>

            <section class="help-section" id="helpSectionTwo">
                <div class="faq-grid-flipped">
                    <div class="visual-spacer-block"></div>

                    <div class="faq-sub-cards-layout">

                        <div class="help-card">
                            <div class="help-card-icon"><i class="fas fa-reply"></i></div>
                            <div class="help-card-body">
                                <h3>Q. What if I need to return an adopted pet?</h3>
                                <p>We understand that life circumstances can change unpredictably. If you can no longer care for your pet, Fluffside has a lifelong intake policy. Please contact us to coordinate a safe return. We ask that you never abandon a resident or pass them to an unverified home.</p>
                            </div>
                        </div>

                        <div class="help-card">
                            <div class="help-card-icon"><i class="fas fa-hand-holding-dollar"></i></div>
                            <div class="help-card-body">
                                <h3>Q. Are there any adoption or fostering fees?</h3>
                                <p>Fostering is 100% free! For adoption, we request a minimal rehoming fee. Every single peso of this fee goes directly toward medical checkups, vaccinations, and high-quality food for the other waiting residents in our shelter network.</p>
                            </div>
                        </div>

                    </div>
                </div>
            </section>

            <section class="help-section" id="helpSectionThree">
                <h2 class="policy-main-title">Adoption & Fostering:</h2>
                <h3 class="policy-subtitle">Your Guidelines and Policies</h3>

                <div class="policy-grid-layout">

                    <div class="policy-card">
                        <h3>Adoption Guidelines & Policies</h3>
                        <ul class="policy-list">
                            <li><strong>Age Requirement:</strong> Applicants are encouraged to be at least 18 years old and must present a valid government-issued ID. Minors may still apply, provided a parent or legal guardian submits their information, gives consent, and accompanies the applicant throughout the entire adoption process.</li>
                            <li><strong>Environment Check:</strong> If you are renting an apartment, condominium, or shared residence, proof or written confirmation from your landlord, association, or property management allowing pets may be required.</li>
                            <li><strong>Safe Living Conditions:</strong> Adopters must provide a clean, secure, and safe environment suitable for the pet’s species, size, and needs. Pets must not be intentionally placed in dangerous, abusive, or neglectful conditions.</li>
                            <li><strong>Commitment to Proper Care:</strong> Adopters agree to provide adequate food, clean water, shelter, exercise, grooming, medical attention, and emotional care for the pet throughout its lifetime.</li>
                            <li><strong>Post-Adoption Updates:</strong> Adopters agree to share occasional updates, photos, or messages with Fluffside during the first few months after adoption to help ensure a smooth and healthy transition.</li>
                            <li><strong>Medical Responsibility:</strong> Once the adoption process is finalized, adopters assume responsibility for the pet’s future medical care, vaccinations, treatments, and general well-being unless otherwise stated by the shelter.</li>
                            <li><strong>No Unauthorized Transfer:</strong>Adopted pets must not be sold, abandoned, given away, or transferred to another owner without informing Fluffside first.</li>
                            <li><strong>Return Policy:</strong> If the adopter becomes unable to continue caring for the pet, the pet must be returned to Fluffside or coordinated with the shelter before any rehoming attempts are made.</li>
                            <li><strong>Neglect or Abuse Clause:</strong> Any evidence of abuse, neglect, intentional harm, unsafe living conditions, or failure to provide proper care may result in the shelter reclaiming the pet and restricting future adoption applications.</li>
                        </ul>
                    </div>

                    <div class="policy-card">
                        <h3>Fostering Guidelines & Policies</h3>
                        <ul class="policy-list">
                            <li><strong>Age Requirement:</strong> Applicants are encouraged to be at least 18 years old. Minors may foster with the full supervision, consent, and involvement of a parent or legal guardian, who must remain responsible throughout the foster period.</li>
                            <li><strong>Fluffside Medical Support:</strong> Fluffside covers approved veterinary and medical expenses for the foster pet during the agreed foster duration.</li>
                            <li><strong>Open Communication:</strong> Foster parents must provide regular updates regarding the pet’s health, behavior, appetite, and adjustment so the shelter can monitor progress and update the pet’s public profile accurately.</li>
                            <li><strong>Supplies Provided::</strong> Fluffside provides essential starter supplies such as food samples and basic gear. Foster parents are responsible for daily care, socialization, cleanliness, supervision, and maintaining a safe environment.</li>
                            <li><strong>Safe and Humane Treatment:</strong> Foster parents must handle pets with patience and care at all times. Physical punishment, neglect, abandonment, or unsafe treatment is strictly prohibited.</li>
                            <li><strong>Indoor & Safety Expectations:</strong> Foster pets should be kept in secure environments and must not be intentionally exposed to hazardous situations, unsafe outdoor conditions, or harmful individuals/animals.</li>
                            <li><strong>Emergency Reporting:</strong> Any illness, injury, escape, aggressive incident, or emergency involving the foster pet must be reported to Fluffside immediately.</li>
                            <li><strong>No Unauthorized Rehoming:</strong> Foster parents are not permitted to sell, give away, transfer, or permanently keep the pet without approval from Fluffside.</li>
                            <li><strong>Return & Withdrawal Policy:</strong> If a foster parent can no longer care for the pet, Fluffside must be informed immediately so proper arrangements can be made for the pet’s safe return or transfer.</li>
                            <li><strong>Liability for Intentional Harm or Negligence:</strong> Foster parents may be held accountable for damages, injuries, or health issues caused by intentional harm, severe negligence, or violation of shelter policies.</li>
                            <li><strong>Right of Removal:</strong> Fluffside reserves the right to retrieve a foster pet at any time if the environment is found unsafe, unsuitable, or not aligned with the shelter’s welfare standards.</li>
                            <li><strong>Adoption Interest:</strong> Foster parents interested in permanently adopting their foster pet must still complete the official adoption evaluation and approval process.</li>
                        </ul>
                    </div>

                </div>
            </section>

        

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