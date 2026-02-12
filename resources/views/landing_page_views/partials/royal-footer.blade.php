<!--================ start footer Area  =================-->	
<footer class="footer-area section_gap modern-footer">
    <div class="footer-bg-image"></div>
    <div class="footer-overlay"></div>
    <div class="container" style="position: relative; z-index: 2;">
        <div class="row">
            <!-- Section 1: Logo -->
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="single-footer-widget footer-logo-section">
                    <div class="footer-logo">
                        <img src="{{ asset('royal-master/image/logo/Logo.png') }}" alt="PrimeLand Hotel Logo">
                    </div>
                    <p style="color: rgba(255,255,255,0.9); line-height: 1.8; margin-top: 15px;">Experience luxury and comfort at PrimeLand Hotel. Your perfect stay awaits in the heart of Kilimanjaro.</p>
                </div>
            </div>
            
            <!-- Section 2: Payment Methods -->
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="single-footer-widget">
                    <h6 class="footer_title">Payment Methods</h6>
                    <p style="color: rgba(255,255,255,0.8); margin-bottom: 20px;">We accept the following payment methods:</p>
                    <div class="payment-gateways">
                        <div class="payment-icon" title="VISA" style="font-size: 32px; color: #1A1F71; margin-right: 20px; background: white; padding: 2px 5px; border-radius: 4px;">
                            <i class="fa fa-cc-visa"></i>
                        </div>
                        <div class="payment-icon" title="Bank Transfer" style="font-size: 32px; color: #DAA520; margin-right: 20px;">
                            <i class="fa fa-university"></i>
                        </div>
                        <div class="payment-icon" title="Cash" style="font-size: 32px; color: #85bb65; margin-right: 20px;">
                            <i class="fa fa-money"></i>
                        </div>
                        <div class="payment-icon" title="Mobile Money" style="font-size: 32px; color: #f39c12;">
                            <i class="fa fa-mobile-phone"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Section 3: Newsletter -->
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="single-footer-widget">
                    <h6 class="footer_title">Newsletter</h6>
                    <p style="color: rgba(255,255,255,0.8);">Stay updated with our latest offers and news</p>		
                    <div id="mc_embed_signup">
                        <form id="newsletter-form" class="subscribe_form relative">
                            @csrf
                            <div class="input-group d-flex flex-row">
                                <input name="email" id="newsletter-email" placeholder="Email Address" onfocus="this.placeholder = ''" onblur="this.placeholder = 'Email Address '" required="" type="email" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.3); color: #fff;">
                                <button type="submit" class="btn sub-btn newsletter-submit-btn" style="background: #e77a3a; border: none;"><span class="lnr lnr-location"></span></button>		
                            </div>									
                            <div class="mt-10 info newsletter-message" style="color: rgba(255,255,255,0.9); font-size: 14px; margin-top: 10px;"></div>
                        </form>
                    </div>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const form = document.getElementById('newsletter-form');
                            const emailInput = document.getElementById('newsletter-email');
                            const submitBtn = form.querySelector('.newsletter-submit-btn');
                            const messageDiv = form.querySelector('.newsletter-message');
                            
                            form.addEventListener('submit', function(e) {
                                e.preventDefault();
                                
                                const email = emailInput.value.trim();
                                
                                if (!email) {
                                    showMessage('Please enter your email address.', 'error');
                                    return;
                                }
                                
                                // Disable submit button
                                submitBtn.disabled = true;
                                submitBtn.innerHTML = '<span class="lnr lnr-hourglass-empty"></span>';
                                messageDiv.textContent = '';
                                
                                // Submit via AJAX
                                fetch('{{ route("newsletter.subscribe") }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Accept': 'application/json'
                                    },
                                    body: JSON.stringify({ email: email })
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        showMessage(data.message, 'success');
                                        emailInput.value = '';
                                    } else {
                                        showMessage(data.message, 'error');
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    showMessage('An error occurred. Please try again later.', 'error');
                                })
                                .finally(() => {
                                    // Re-enable submit button
                                    submitBtn.disabled = false;
                                    submitBtn.innerHTML = '<span class="lnr lnr-location"></span>';
                                });
                            });
                            
                            function showMessage(message, type) {
                                messageDiv.textContent = message;
                                messageDiv.style.color = type === 'success' ? '#4ade80' : '#f87171';
                                messageDiv.style.display = 'block';
                                
                                // Hide message after 5 seconds
                                setTimeout(function() {
                                    messageDiv.style.display = 'none';
                                }, 5000);
                            }
                        });
                    </script>
                </div>
            </div>
            
            <!-- Section 4: Social Media -->
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="single-footer-widget">
                    <h6 class="footer_title">Follow Us</h6>
                    <p style="color: rgba(255,255,255,0.8); margin-bottom: 20px;">Connect with us on social media</p>
                    <div class="footer-social-icons">
                        <a href="https://www.instagram.com/primeland_hotel?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw==" class="social-media-icon" target="_blank" title="Instagram" style="color: #E1306C; font-size: 36px; margin-right: 25px;">
                            <i class="fa fa-instagram"></i>
                        </a>
                        <a href="#" class="social-media-icon" target="_blank" title="Facebook" style="color: #1877F2; font-size: 36px; margin-right: 25px;">
                            <i class="fa fa-facebook-official"></i>
                        </a>
                        <a href="#" class="social-media-icon" target="_blank" title="Twitter" style="color: #1DA1F2; font-size: 36px; margin-right: 25px;">
                            <i class="fa fa-twitter-square"></i>
                        </a>
                        <a href="#" class="social-media-icon" target="_blank" title="LinkedIn" style="color: #0077B5; font-size: 36px; margin-right: 25px;">
                            <i class="fa fa-linkedin-square"></i>
                        </a>
                        <a href="#" class="social-media-icon" target="_blank" title="YouTube" style="color: #FF0000; font-size: 36px;">
                            <i class="fa fa-youtube-play"></i>
                        </a>
                    </div>
                </div>
            </div>						
        </div>
        <div class="border_line" style="border-color: rgba(255,255,255,0.2); margin: 40px 0 30px;"></div>
        <div class="row footer-bottom d-flex justify-content-between align-items-center">
            <p class="col-lg-6 col-sm-12 footer-text m-0" style="color: rgba(255,255,255,0.9);">Copyright &copy;<script>document.write(new Date().getFullYear());</script> All rights reserved | PrimeLand Hotel</p>
            <p class="col-lg-6 col-sm-12 footer-text m-0 text-right" style="color: rgba(255,255,255,0.9);">
                Powered By <a href="https://emca.tech/#" target="_blank" style="color: #ff0000; font-weight: 600; text-decoration: none;">EmCa Techonologies</a>
            </p>
        </div>
    </div>
</footer>
<!--================ End footer Area  =================-->
<style>
    /* Center footer logo on mobile */
    @media (max-width: 768px) {
        .footer-logo-section {
            text-align: center !important;
        }
        .footer-logo {
            display: flex !important;
            justify-content: center !important;
            align-items: center !important;
        }
        .footer-logo img {
            margin: 0 auto !important;
        }
    }
</style>


