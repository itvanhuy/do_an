<footer class="footer">
    <div class="footer-content">
        <div class="footer-logo">
            <img src="../img/logo.png" alt="TechShop Logo">
            <h3>TECHSHOP</h3>
            <p>Your one-stop destination for premium gaming gear and esports tournaments.</p>
        </div>
        
        <div class="footer-links">
            <h4>Quick Links</h4>
            <ul>
                <li><a href="home.php">Home</a></li>
                <li><a href="shop.php">Shop</a></li>
                <li><a href="tournament.php">Tournament</a></li>
                <li><a href="news.php">News</a></li>
                <li><a href="about.php">About Us</a></li>
            </ul>
        </div>
        
        <div class="footer-contact">
            <h4>Contact Us</h4>
            <p><i class="fas fa-map-marker-alt"></i> 123 Gaming Street, Tech City</p>
            <p><i class="fas fa-phone"></i> +84 896 492 400</p>
            <p><i class="fas fa-envelope"></i> levahuy06042003@gmail.com</p>
        </div>
        
        <div class="footer-social">
            <h4>Follow Us</h4>
            <div class="social-icons">
                <a href="https://www.facebook.com/huylv.2k3" target="_blank"><i class="fab fa-facebook-f"></i></a>
                <a href="#" target="_blank"><i class="fab fa-twitter"></i></a>
                <a href="#" target="_blank"><i class="fab fa-instagram"></i></a>
                <a href="#" target="_blank"><i class="fab fa-youtube"></i></a>
            </div>
            
            <div class="footer-newsletter">
                <h5>Subscribe to Newsletter</h5>
                <form class="newsletter-form" action="../includes/newsletter.php" method="POST">
                    <input type="email" name="email" placeholder="Your email address" required>
                    <button type="submit" class="btn-subscribe"><i class="fas fa-paper-plane"></i></button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="footer-bottom">
        <div class="footer-bottom-content">
            <p>&copy; <span class="current-year"><?php echo date('Y'); ?></span> TechShop. All rights reserved.</p>
            
            <div class="footer-legal">
                <a href="#">Privacy Policy</a>
                <a href="#">Terms of Service</a>
                <a href="#">Cookie Policy</a>
            </div>
            
            <div class="payment-methods">
                <i class="fab fa-cc-visa" title="Visa"></i>
                <i class="fab fa-cc-mastercard" title="Mastercard"></i>
                <i class="fab fa-cc-paypal" title="Paypal"></i>
                <i class="fas fa-money-bill-wave" title="Cash on Delivery"></i>
            </div>
        </div>
    </div>
    
    <button id="backToTop" class="back-to-top" aria-label="Back to top">
        <i class="fas fa-arrow-up"></i>
    </button>
    
    <script>
        // Back to Top Button Logic
        const backToTopBtn = document.getElementById('backToTop');
        
        window.addEventListener('scroll', () => {
            if (window.scrollY > 300) {
                backToTopBtn.classList.add('visible');
            } else {
                backToTopBtn.classList.remove('visible');
            }
        });
        
        backToTopBtn.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    </script>
</footer>