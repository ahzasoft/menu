    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="footer-logo">🍔 <?php echo APP_NAME; ?></div>
                    <p>نقدم أفضل الوجبات السريعة والمشويات بمذاق لا ينسى وجودة لا تضاهى. هدفنا هو إرضاء ذوقكم الرفيع.</p>
                    <div class="social-links mt-3">
                        <a href="<?php echo SOCIAL_FACEBOOK; ?>" target="_blank" class="facebook"><i class="bi bi-facebook"></i></a>
                        <a href="<?php echo SOCIAL_INSTAGRAM; ?>" class="instagram"><i class="bi bi-instagram"></i></a>
                        <a href="<?php echo SOCIAL_TWITTER; ?>" class="twitter"><i class="bi bi-twitter-x"></i></a>
                        <a href="<?php echo SOCIAL_TIKTOK; ?>" class="tiktok"><i class="bi bi-tiktok"></i></a>
                    </div>
                </div>
                <div class="col-md-4 mb-4 text-center">
                    <h5>روابط سريعة</h5>
                    <ul class="list-unstyled">
                        <li><a href="#hero" class="text-decoration-none text-muted">الرئيسية</a></li>
                        <li><a href="#menu" class="text-decoration-none text-muted">القائمة</a></li>
                        <li><a href="#reservation" class="text-decoration-none text-muted">الحجز</a></li>
                    </ul>
                </div>
                <div class="col-md-4 mb-4 text-end">
                    <h5>تواصل معنا</h5>
                    <p class="mb-1">هاتف: <?php echo RESTAURANT_PHONE_DISPLAY; ?></p>
                    <p class="mb-1">بريد: <?php echo RESTAURANT_EMAIL; ?></p>
                </div>
            </div>
            <hr class="bg-secondary">
            <div class="text-center mt-4">
                <p class="mb-0">&copy; <?php echo date('Y'); ?> <?php echo APP_NAME; ?>. جميع الحقوق محفوظة.</p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- AOS JS -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <script>
        // Initialize AOS
        AOS.init({
            duration: 800,
            once: true
        });

        // Navbar Scroll Effect
        window.addEventListener('scroll', function() {
            const nav = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                nav.classList.add('bg-white', 'shadow-sm');
                nav.style.padding = '0.5rem 0';
            } else {
                nav.classList.remove('bg-white', 'shadow-sm');
                nav.style.padding = '1rem 0';
            }
        });

        // Theme Toggle
        const themeToggle = document.getElementById('themeToggle');
        let isDark = false;
        themeToggle.addEventListener('click', () => {
            isDark = !isDark;
            document.body.classList.toggle('bg-dark', isDark);
            document.body.classList.toggle('text-white', isDark);
            themeToggle.innerHTML = isDark ? '<i class="bi bi-sun-fill"></i>' : '<i class="bi bi-moon-fill"></i>';
            
            document.querySelectorAll('.card, .feature-card, .reservation-form, .menu-item-card, section').forEach(el => {
                if (isDark) {
                    if (el.tagName !== 'SECTION') {
                        el.style.backgroundColor = '#1e1e1e';
                        el.style.borderColor = '#333';
                    }
                    el.style.color = '#e0e0e0';
                } else {
                    el.style.backgroundColor = '';
                    el.style.color = '';
                    el.style.borderColor = '';
                }
            });

            // Specific fix for headings and lead text in sections
            document.querySelectorAll('h1, h2, h3, h4, h5, .lead, p').forEach(el => {
                if (isDark) {
                    el.style.color = '#e0e0e0';
                } else {
                    el.style.color = '';
                }
            });
        });

        // --- Cart Logic ---
        let cart = [];

        // Cookie Helpers
        function setCookie(name, value, days) {
            const d = new Date();
            d.setTime(d.getTime() + (days * 24 * 60 * 60 * 1000));
            let expires = "expires=" + d.toUTCString();
            document.cookie = name + "=" + JSON.stringify(value) + ";" + expires + ";path=/";
        }

        function getCookie(name) {
            let nameEQ = name + "=";
            let ca = document.cookie.split(';');
            for(let i=0; i < ca.length; i++) {
                let c = ca[i];
                while (c.charAt(0)==' ') c = c.substring(1,c.length);
                if (c.indexOf(nameEQ) == 0) return JSON.parse(c.substring(nameEQ.length,c.length));
            }
            return null;
        }

        // Load Cart from Cookies
        function loadCart() {
            const savedCart = getCookie('restaurant_cart');
            if (savedCart) {
                cart = savedCart;
                updateCartUI();
            }
        }

        // Save Cart to Cookies
        function saveCart() {
            setCookie('restaurant_cart', cart, 7);
        }

        // Add to Cart Event Delegation
        document.body.addEventListener('click', (e) => {
            const button = e.target.closest('.add-to-cart');
            if (button) {
                const id = button.getAttribute('data-id');
                const name = button.getAttribute('data-name');
                const price = parseInt(button.getAttribute('data-price'));
                const img = button.getAttribute('data-img');

                const existingItem = cart.find(item => item.id === id);
                if (existingItem) {
                    existingItem.quantity += 1;
                } else {
                    cart.push({ id, name, price, img, quantity: 1 });
                }

                saveCart();
                updateCartUI();
                
                // Visual Feedback for all buttons matching this ID (in "All" and specific category tabs)
                document.querySelectorAll(`.add-to-cart[data-id="${id}"]`).forEach(btn => {
                    const originalText = btn.getAttribute('data-original-text') || btn.textContent;
                    if (!btn.getAttribute('data-original-text')) {
                        btn.setAttribute('data-original-text', originalText);
                    }
                    btn.textContent = 'تمت الإضافة! ✓';
                    btn.classList.replace('btn-primary-custom', 'btn-success');
                    setTimeout(() => {
                        btn.textContent = originalText;
                        btn.classList.replace('btn-success', 'btn-primary-custom');
                    }, 1500);
                });
            }
        });

        function updateCartUI() {
            const container = document.getElementById('cart-items-container');
            const summary = document.getElementById('cart-summary');
            const countBadge = document.getElementById('cart-count');
            const totalDisplay = document.getElementById('cart-total');

            if (cart.length === 0) {
                container.innerHTML = '<p class="text-center text-muted my-5">سلة المشتريات فارغة</p>';
                summary.classList.add('d-none');
                countBadge.textContent = '0';
                return;
            }

            summary.classList.remove('d-none');
            container.innerHTML = '';
            let total = 0;
            let count = 0;

            cart.forEach(item => {
                total += item.price * item.quantity;
                count += item.quantity;

                const itemEl = document.createElement('div');
                itemEl.className = 'cart-item';
                itemEl.innerHTML = `
                    <img src="${item.img}" class="cart-item-img" alt="${item.name}">
                    <div class="cart-item-info">
                        <h6>${item.name}</h6>
                        <p>${item.price} جنيه × ${item.quantity}</p>
                    </div>
                    <div class="d-flex align-items-center ms-auto">
                        <button class="btn btn-sm btn-outline-secondary px-2" onclick="changeQuantity('${item.id}', -1)">-</button>
                        <span class="mx-2">${item.quantity}</span>
                        <button class="btn btn-sm btn-outline-secondary px-2" onclick="changeQuantity('${item.id}', 1)">+</button>
                        <button class="btn btn-sm text-danger ms-2" onclick="removeFromCart('${item.id}')"><i class="bi bi-trash"></i></button>
                    </div>
                `;
                container.appendChild(itemEl);
            });

            countBadge.textContent = count;
            totalDisplay.textContent = total + ' جنيه';
        }

        window.changeQuantity = function(id, delta) {
            const item = cart.find(i => i.id === id);
            if (item) {
                item.quantity += delta;
                if (item.quantity <= 0) {
                    cart = cart.filter(i => i.id !== id);
                }
                saveCart();
                updateCartUI();
            }
        };

        window.removeFromCart = function(id) {
            cart = cart.filter(i => i.id !== id);
            saveCart();
            updateCartUI();
        };

        window.clearCart = function() {
            if (confirm('هل أنت متأكد من رغبتك في إفراغ السلة؟')) {
                cart = [];
                saveCart();
                updateCartUI();
            }
        };

        // Order Type Toggle
        document.querySelectorAll('.order-type-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                // Toggle active class
                document.querySelectorAll('.order-type-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                
                // Update hidden input
                const val = this.getAttribute('data-value');
                document.getElementById('order-type').value = val;
                
                // Show/Hide table selection
                const tableContainer = document.getElementById('table-number-container');
                if (val === 'table') {
                    tableContainer.classList.remove('d-none');
                } else {
                    tableContainer.classList.add('d-none');
                }
            });
        });

        // Checkout
        window.checkout = function() {
            if (cart.length === 0) return;

            const orderType = document.getElementById('order-type').value;
            const tableNum = document.getElementById('table-number').value;
            let typeText = "";
            if (orderType === 'pickup') typeText = "استلام من المطعم";
            else if (orderType === 'takeaway') typeText = "سفري";
            else typeText = `على الطاولة (طاولة رقم ${tableNum})`;

            let message = `*طلب جديد من <?php echo APP_NAME; ?>*\n\n`;
            message += `*نوع الطلب:* ${typeText}\n`;
            message += `*الأصناف:*\n`;

            let total = 0;
            cart.forEach(item => {
                message += `- ${item.name} (${item.quantity} × ${item.price} ج.م)\n`;
                total += item.price * item.quantity;
            });

            message += `\n*الإجمالي:* ${total} جنيه`;

            const encodedMessage = encodeURIComponent(message);
            const whatsappUrl = `https://wa.me/<?php echo RESTAURANT_PHONE; ?>?text=${encodedMessage}`;

            // Redirect to WhatsApp
            window.open(whatsappUrl, '_blank');

            // Empty Cart
            cart = [];
            saveCart();
            updateCartUI();
            
            // Close Offcanvas
            const offcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('cartOffcanvas'));
            offcanvas.hide();
            
            alert('تم إرسال طلبك عبر واتساب! شكراً لتعاملك معنا.');
        };

        // Initialize
        loadCart();

        // Reservation Form Submission
        document.getElementById('resForm').addEventListener('submit', function(e) {
            e.preventDefault();
            alert('شكراً لطلب الحجز! سنتواصل معك قريباً لتأكيده.');
            this.reset();
        });
    </script>
</body>

</html>
