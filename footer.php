<style>
    .site-footer { 
        background-color: var(--footer-green, #B8C7A1); 
        position: relative; 
        padding: 100px 5% 20px 5%;
        color: var(--fs-text-dark, #5A483E); 
        margin-top: auto; 
    }
    
    .footer-wave { position: absolute; top: -1px; left: 0; width: 100%; overflow: hidden; line-height: 0; }
    .footer-wave svg { display: block; width: calc(100% + 1.3px); height: 70px; }
    .footer-wave .shape-fill { fill: var(--footer-green, #B8C7A1); }

    .footer-content {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr;
        gap: 40px;
        max-width: 1200px;
        margin: 0 auto;
        padding-bottom: 40px;
        border-bottom: 2px solid rgba(90, 72, 62, 0.1);
        text-align: left;
    }
    
    .footer-brand h2 { font-size: 32px; font-weight: 900; margin-bottom: 15px; letter-spacing: -0.5px;}
    .footer-brand h2 span { color: var(--fs-orange, #EF8E35); }
    .footer-brand p { font-size: 14px; font-weight: 600; line-height: 1.6; margin-bottom: 20px; max-width: 320px; }
    
    .social-icons { display: flex; gap: 12px; }
    .social-icons a { 
        width: 38px; height: 38px; 
        background-color: var(--fs-text-dark, #5A483E); 
        color: var(--footer-green, #B8C7A1); 
        border-radius: 50%; 
        display: flex; justify-content: center; align-items: center; 
        text-decoration: none; transition: all 0.2s ease; 
        font-size: 16px;
    }
    .social-icons a:hover { 
        background-color: var(--fs-orange, #EF8E35); 
        color: #fff; 
        transform: translateY(-4px);
        box-shadow: 0 4px 10px rgba(239, 142, 53, 0.4);
    }

    .footer-links h3, .footer-contact h3 { font-size: 18px; font-weight: 900; margin-bottom: 20px; text-transform: uppercase; color: #2B221D;}
    .footer-links ul { list-style: none; padding: 0; margin: 0;}
    .footer-links li { margin-bottom: 12px; }
    .footer-links a { color: var(--fs-text-dark, #5A483E); text-decoration: none; font-weight: 700; font-size: 14px; transition: all 0.2s; display: inline-block;}
    .footer-links a:hover { color: var(--fs-orange, #EF8E35); transform: translateX(5px); }

    .footer-contact p { font-size: 14px; font-weight: 700; margin-bottom: 15px; display: flex; align-items: flex-start; gap: 12px; }
    .footer-contact i { color: var(--fs-orange, #EF8E35); font-size: 18px; width: 20px; text-align: center; margin-top: 2px;}

    .footer-bottom { text-align: center; padding-top: 20px; font-size: 13px; font-weight: 700; opacity: 0.8; }

    @media (max-width: 768px) {
        .footer-content { grid-template-columns: 1fr; text-align: center; }
        .footer-brand p { margin: 0 auto 20px auto; }
        .social-icons { justify-content: center; }
        .footer-contact p { justify-content: center; }
    }
</style>

<footer class="site-footer">
    <div class="footer-wave">
        <svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
            <path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z" class="shape-fill"></path>
        </svg>
    </div>

    <div class="footer-content">
        <div class="footer-brand">
            <h2>Fluff<span>Side</span></h2>
            <p>Where Gentle Hearts Meet Waiting Paws. Your trusted community pet shelter dedicated to finding forever homes.</p>
            <div class="social-icons">
                <a href="#"><i class="fab fa-facebook-f"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-tiktok"></i></a>
            </div>
        </div>

        <div class="footer-links">
            <h3>Quick Links</h3>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="residents.php">Meet the Residents</a></li>
                <li><a href="supplies.php">Supply Shop</a></li>
                <li><a href="about.php">About FluffSide</a></li>
                <li><a href="help.php">Help & FAQs</a></li>
            </ul>
        </div>

        <div class="footer-contact">
            <h3>Contact Us</h3>
            <p><i class="fas fa-map-marker-alt"></i> 6767 Sta. Anastacia Sto. Tomas, Batangas,<br>Pet Valley, 4234</p>
            <p><i class="fas fa-envelope"></i>Fluffside@gmail.com</p>
            <p><i class="fas fa-phone"></i> +63 912 345 6789</p>
        </div>
    </div>

    <div class="footer-bottom">
        <p>&copy; <?php echo date("Y"); ?> FluffSide. All rights reserved.</p>
    </div>
</footer>