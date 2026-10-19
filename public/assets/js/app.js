/**
 * CardVault Storefront Engine - Modern Flat Design (No Emoji, Pure SVG Icons)
 */

document.addEventListener('DOMContentLoaded', () => {
    let catalogData = [];
    let activeCategory = 'all';
    let searchQuery = '';

    const catalogGrid = document.getElementById('catalog-grid');
    const navTabs = document.querySelectorAll('.nav-tab');
    const searchInput = document.getElementById('search-input');

    // === ReactBits Micro-Interactions (Vanilla Implementation) ===

    // 1. CountUp (ReactBits TextAnimation): Smooth numerical easing
    function animateNumber(element, startVal, endVal, duration = 350, prefix = '', suffix = '') {
        if (!element) return;
        if (startVal === endVal) {
            element.innerText = `${prefix}${endVal.toLocaleString()}${suffix}`;
            return;
        }
        const startTime = performance.now();
        function step(currentTime) {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            const ease = progress === 1 ? 1 : 1 - Math.pow(2, -10 * progress);
            const current = Math.round(startVal + (endVal - startVal) * ease);
            element.innerText = `${prefix}${current.toLocaleString()}${suffix}`;
            if (progress < 1) {
                requestAnimationFrame(step);
            }
        }
        requestAnimationFrame(step);
    }

    // 2. SpotlightCard (ReactBits Component): Cursor radial illumination
    function initSpotlightCards() {
        const cards = document.querySelectorAll('.product-card');
        cards.forEach(card => {
            card.addEventListener('pointermove', (e) => {
                const rect = card.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                card.style.setProperty('--spotlight-x', `${x}px`);
                card.style.setProperty('--spotlight-y', `${y}px`);
            });
            card.addEventListener('pointerleave', () => {
                card.style.removeProperty('--spotlight-x');
                card.style.removeProperty('--spotlight-y');
            });
        });
    }

    // 3. Magnet (ReactBits Component): Proximity pull on interactive buttons
    function initMagneticButtons() {
        const magnets = document.querySelectorAll('.cart-btn, .btn-track');
        magnets.forEach(btn => {
            btn.addEventListener('mousemove', (e) => {
                const rect = btn.getBoundingClientRect();
                const x = e.clientX - (rect.left + rect.width / 2);
                const y = e.clientY - (rect.top + rect.height / 2);
                btn.style.transform = `translate(${x * 0.16}px, ${y * 0.16}px)`;
            });
            btn.addEventListener('mouseleave', () => {
                btn.style.transform = '';
            });
        });
    }

    // 4. ClickSpark (ReactBits Animation): Canvas-based geometric micro-sparks
    function initClickSpark() {
        let canvas = document.getElementById('click-spark-canvas');
        if (!canvas) {
            canvas = document.createElement('canvas');
            canvas.id = 'click-spark-canvas';
            document.body.appendChild(canvas);
        }
        const ctx = canvas.getContext('2d');
        let width = window.innerWidth;
        let height = window.innerHeight;
        const dpr = Math.min(window.devicePixelRatio || 1, 2);

        function resize() {
            width = window.innerWidth;
            height = window.innerHeight;
            canvas.width = Math.ceil(width * dpr);
            canvas.height = Math.ceil(height * dpr);
            ctx.setTransform(1, 0, 0, 1, 0, 0);
            ctx.scale(dpr, dpr);
        }
        resize();
        window.addEventListener('resize', resize);

        const sparks = [];
        let animId = null;

        function addSparks(clientX, clientY) {
            const count = 8;
            const colors = ['#e11d48', '#fb7185', '#f43f5e', '#fda4af'];
            for (let i = 0; i < count; i++) {
                const angle = (Math.PI * 2 / count) * i + (Math.random() - 0.5) * 0.5;
                const speed = 2.4 + Math.random() * 2.6;
                sparks.push({
                    x: clientX,
                    y: clientY,
                    vx: Math.cos(angle) * speed,
                    vy: Math.sin(angle) * speed,
                    len: 8 + Math.random() * 7,
                    life: 1,
                    decay: 0.04 + Math.random() * 0.025,
                    color: colors[Math.floor(Math.random() * colors.length)],
                    width: 2 + Math.random() * 0.8
                });
            }
            if (!animId) {
                animId = requestAnimationFrame(loop);
            }
        }

        function loop() {
            ctx.clearRect(0, 0, width, height);
            for (let i = sparks.length - 1; i >= 0; i--) {
                const s = sparks[i];
                s.x += s.vx;
                s.y += s.vy;
                s.life -= s.decay;

                if (s.life <= 0) {
                    sparks.splice(i, 1);
                    continue;
                }

                ctx.save();
                ctx.strokeStyle = s.color;
                ctx.lineWidth = s.width * s.life;
                ctx.lineCap = 'round';
                ctx.globalAlpha = Math.max(0, s.life);

                ctx.beginPath();
                ctx.moveTo(s.x, s.y);
                ctx.lineTo(s.x + (s.vx / 2.4) * s.len * s.life, s.y + (s.vy / 2.4) * s.len * s.life);
                ctx.stroke();
                ctx.restore();
            }

            if (sparks.length > 0) {
                animId = requestAnimationFrame(loop);
            } else {
                animId = null;
            }
        }

        document.addEventListener('pointerdown', (e) => {
            const target = e.target.closest('button, .nav-tab, .btn-track, .cart-btn, .btn-buy, .btn-add-cart, .btn-checkout-drawer, #btn-confirm-pay, .btn-copy-pin, .qty-btn, .item-delete-btn');
            if (target && !target.disabled) {
                addSparks(e.clientX, e.clientY);
            }
        });
    }

    // 1. Fetch Catalog from API
    async function loadCatalog() {
        try {
            const res = await fetch('api/products.php');
            const data = await res.json();
            if (data.status === 'success') {
                catalogData = data.data;
                renderProducts();
            } else {
                catalogGrid.innerHTML = `<p style="color: var(--danger); text-align: center;">เกิดข้อผิดพลาดในการโหลดแคตตาล็อกสินค้า</p>`;
            }
        } catch (err) {
            console.error('Error fetching catalog:', err);
            catalogGrid.innerHTML = `<p style="color: var(--danger); text-align: center;">ไม่สามารถเชื่อมต่อ API สินค้าได้</p>`;
        }
    }

    // Platform Brand SVG Icons (Authentic Official Vector Brandmarks)
    function getPlatformIcon(slug) {
        switch (slug) {
            case 'steam':
                return `<svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M11.979 0C5.64 0 .463 4.908.024 11.16l5.72 2.368a3.57 3.57 0 0 1 2.03-.628c.174 0 .343.013.509.038l2.584-3.754V9.16a4.8 4.8 0 0 1 4.793-4.8 4.8 4.8 0 0 1 4.8 4.8 4.8 4.8 0 0 1-4.8 4.8c-.085 0-.168-.006-.252-.01l-3.69 2.628c.017.142.028.286.028.433a3.593 3.593 0 0 1-3.593 3.593c-1.785 0-3.26-1.304-3.535-3.007L.344 14.896C1.654 20.08 6.368 24 11.979 24c6.627 0 12-5.373 12-12s-5.373-12-12-12zm-3.66 17.58a2.128 2.128 0 0 0 2.125-2.125 2.128 2.128 0 0 0-2.125-2.125 2.13 2.13 0 0 0-1.897 1.157l1.458.604a1.066 1.066 0 0 1 1.01 1.014c0 .588-.476 1.065-1.065 1.065-.41 0-.766-.232-.942-.572l-1.464-.607c.307 1.48 1.62 2.59 3.208 2.59zm7.34-5.62a2.8 2.8 0 0 0 2.8-2.8 2.8 2.8 0 0 0-2.8-2.8 2.8 2.8 0 0 0-2.8 2.8 2.8 2.8 0 0 0 2.8 2.8z"/>
                </svg>`;
            case 'netflix':
                return `<svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path d="M5.5 1.5H9.5V22.5H5.5V1.5Z" fill="#B81D24"/>
                    <path d="M14.5 1.5H18.5V22.5H14.5V1.5Z" fill="#B81D24"/>
                    <path d="M18.5 22.5L9.5 1.5H5.5L14.5 22.5H18.5Z" fill="#E50914"/>
                </svg>`;
            case 'apple':
                return `<svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.81-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M15.97 6.37c.62-.75 1.04-1.8 0.92-2.85-.9.04-1.99.6-2.63 1.35-.57.66-.99 1.72-.86 2.73.99.08 2.02-.51 2.57-1.23z"/>
                </svg>`;
            case 'google-play':
                return `<svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path d="M3.609 1.813A2.08 2.08 0 0 0 3 3.327v17.346c0 .594.225 1.139.609 1.55l.081.076 9.734-9.734v-.23L3.69 2.607l-.081.076z" fill="#00D2FF"/>
                    <path d="M16.69 15.836l-3.266-3.267v-.23l3.266-3.267.074.042 3.867 2.197c1.104.628 1.104 1.656 0 2.285l-3.867 2.198-.074.042z" fill="#FFCE00"/>
                    <path d="M13.424 12.339L3.609 22.154c.365.388.949.444 1.58.085l11.49-6.533-3.255-3.367z" fill="#FF3A44"/>
                    <path d="M13.424 11.661l3.255-3.367L5.189 1.761c-.631-.359-1.215-.303-1.58.085l9.815 9.815z" fill="#00F076"/>
                </svg>`;
            default:
                return `<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="2" y="5" width="20" height="14" rx="3"></rect>
                    <line x1="2" y1="10" x2="22" y2="10"></line>
                </svg>`;
        }
    }

    // 2. Render Products
    function renderProducts() {
        let platformsToRender = [];

        catalogData.forEach(cat => {
            if (activeCategory === 'all' || cat.slug === activeCategory) {
                cat.platforms.forEach(platform => {
                    if (!searchQuery || platform.name.toLowerCase().includes(searchQuery.toLowerCase())) {
                        platformsToRender.push({ ...platform, categoryName: cat.name });
                    }
                });
            }
        });

        if (platformsToRender.length === 0) {
            catalogGrid.innerHTML = `
                <div style="grid-column: 1/-1; text-align: center; padding: 3.5rem; color: var(--text-muted); background: var(--bg-surface); border-radius: var(--radius-md);">
                    <p style="font-size: 1.05rem; font-weight: 600; color: var(--text-secondary);">ไม่พบรายการสินค้าที่ค้นหา</p>
                </div>
            `;
            return;
        }

        catalogGrid.innerHTML = platformsToRender.map((platform, idx) => {
            const defaultDenom = platform.denominations[0] || {};
            const isAvailable = defaultDenom.stock_count > 0;
            const regionLabel = platform.region === 'GLOBAL' ? 'ทั่วโลก (Global)' : 'ประเทศไทย (TH)';

            const denomOptions = platform.denominations.map(d => `
                <option value="${d.id}" data-price="${d.price}" data-stock="${d.stock_count}" data-name="${d.name}">
                    ${d.name} (${d.price} บาท)
                </option>
            `).join('');

            return `
                <div class="product-card" data-platform-id="${platform.id}" style="--card-index: ${idx};">
                    <div class="card-header-bar">
                        <div class="platform-avatar">
                            ${getPlatformIcon(platform.slug)}
                        </div>
                        <div class="platform-header-text">
                            <h3 class="card-title">${platform.name}</h3>
                            <div class="platform-sub-line">
                                <span class="platform-region">${regionLabel}</span>
                                <span class="dot-separator">•</span>
                                <span class="delivery-hint">จัดส่งทันที</span>
                            </div>
                        </div>
                    </div>

                    <h3 class="card-title">${platform.name}</h3>
                    <p class="card-desc">${platform.description}</p>

                    <div class="denom-selector">
                        <label class="denom-label">เลือกแพ็กเกจ / ราคา</label>
                        <div class="select-wrapper">
                            <select class="denom-select" onchange="window.cardVault.onDenomChange(this, ${platform.id})">
                                ${denomOptions}
                            </select>
                            <span class="select-chevron">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg>
                            </span>
                        </div>
                    </div>

                    <div class="card-price-row">
                        <div class="price-box">
                            <span class="price-value" id="price-${platform.id}">${defaultDenom.price || 0}</span>
                            <span class="currency">THB</span>
                        </div>
                        <div class="stock-tag ${isAvailable ? 'stock-in' : 'stock-out'}" id="stock-${platform.id}">
                            <span class="stock-dot ${isAvailable ? 'in-stock' : 'out-stock'}"></span>
                            ${isAvailable ? `มีสินค้า (${defaultDenom.stock_count} ชิ้น)` : 'สินค้าหมดชั่วคราว'}
                        </div>
                    </div>

                    <div class="card-actions">
                        <button class="btn-buy" id="btn-buy-${platform.id}" ${!isAvailable ? 'disabled' : ''} onclick="window.cardVault.handleBuyNow(${platform.id})">
                            ซื้อทันที
                        </button>
                        <button class="btn-add-cart" id="btn-cart-${platform.id}" ${!isAvailable ? 'disabled' : ''} onclick="window.cardVault.handleAddToCart(${platform.id})" title="เพิ่มลงตะกร้า">
                            <svg class="icon-svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                <circle cx="9" cy="21" r="1"></circle>
                                <circle cx="20" cy="21" r="1"></circle>
                                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            `;
        }).join('');

        initSpotlightCards();
    }

    // 3. Tab switching
    navTabs.forEach(tab => {
        tab.addEventListener('click', () => {
            navTabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            activeCategory = tab.dataset.category;
            renderProducts();
        });
    });

    // 4. Search filter
    searchInput.addEventListener('input', (e) => {
        searchQuery = e.target.value.trim();
        renderProducts();
    });

    // 5. Cart Drawer Management
    const cart = [];
    const cartDrawer = document.getElementById('cart-drawer');
    const drawerOverlay = document.getElementById('cart-drawer-overlay');
    const btnOpenCart = document.getElementById('btn-open-cart');
    const btnCloseCart = document.getElementById('btn-close-cart');
    const drawerItemsContainer = document.getElementById('cart-drawer-items');
    const drawerTotalPrice = document.getElementById('drawer-total-price');
    const drawerEmailInput = document.getElementById('drawer-email-input');
    const btnCheckoutDrawer = document.getElementById('btn-checkout-drawer');
    const headerCartCount = document.getElementById('header-cart-count');

    function openCart() {
        cartDrawer.classList.add('active');
        drawerOverlay.classList.add('active');
    }

    function closeCart() {
        cartDrawer.classList.remove('active');
        drawerOverlay.classList.remove('active');
    }

    btnOpenCart.addEventListener('click', openCart);
    btnCloseCart.addEventListener('click', closeCart);
    drawerOverlay.addEventListener('click', closeCart);

    let lastCartCount = 0;
    let lastCartTotal = 0;

    function updateCartUI() {
        const totalCount = cart.reduce((sum, item) => sum + item.quantity, 0);
        animateNumber(headerCartCount, lastCartCount, totalCount, 250);
        lastCartCount = totalCount;

        const totalAmount = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        animateNumber(drawerTotalPrice, lastCartTotal, totalAmount, 350, '', ' THB');
        lastCartTotal = totalAmount;

        if (cart.length === 0) {
            drawerItemsContainer.innerHTML = `
                <div style="text-align: center; padding: 2.5rem 1rem; color: var(--text-muted);">
                    <p style="font-size: 1rem; font-weight: 500;">ยังไม่มีสินค้าในตะกร้า</p>
                </div>
            `;
            btnCheckoutDrawer.disabled = true;
            return;
        }

        btnCheckoutDrawer.disabled = false;
        drawerItemsContainer.innerHTML = cart.map((item, idx) => `
            <div class="cart-item">
                <div class="cart-item-info">
                    <h4>${item.name}</h4>
                    <p>${(item.price * item.quantity).toLocaleString()} THB</p>
                </div>
                <div class="cart-item-controls">
                    <button class="qty-btn" onclick="window.cardVault.changeQty(${idx}, -1)">-</button>
                    <span class="qty-val">${item.quantity}</span>
                    <button class="qty-btn" onclick="window.cardVault.changeQty(${idx}, 1)">+</button>
                    <button class="item-delete-btn" onclick="window.cardVault.removeFromCart(${idx})" title="ลบรายการ">
                        <svg class="icon-svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="3 6 5 6 21 6"></polyline>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                        </svg>
                    </button>
                </div>
            </div>
        `).join('');
    }

    window.addEventListener('cardvault:add-to-cart', (e) => {
        const { denomId, name, price, quantity } = e.detail;
        const existing = cart.find(i => i.denomId === denomId);
        if (existing) {
            if (existing.quantity < 10) existing.quantity += quantity;
        } else {
            cart.push({ denomId, name, price, quantity });
        }
        updateCartUI();
        openCart();
    });

    window.addEventListener('cardvault:instant-buy', (e) => {
        const { denomId, name, price, quantity } = e.detail;
        cart.length = 0;
        cart.push({ denomId, name, price, quantity });
        updateCartUI();
        openCart();
        drawerEmailInput.focus();
    });

    // 6. Drawer Checkout
    btnCheckoutDrawer.addEventListener('click', async () => {
        const email = drawerEmailInput.value.trim();
        if (!email || !email.includes('@')) {
            alert('กรุณากรอกที่อยู่อีเมลที่ถูกต้อง เพื่อรับรหัสดิจิทัล');
            drawerEmailInput.focus();
            return;
        }

        btnCheckoutDrawer.disabled = true;
        btnCheckoutDrawer.innerText = 'กำลังตรวจสอบสต็อกและจองตั๋ว...';

        try {
            const payload = {
                email: email,
                items: cart.map(i => ({ denomination_id: i.denomId, quantity: i.quantity })),
                payment_method: 'promptpay'
            };

            const res = await fetch('api/checkout.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });

            const data = await res.json();
            if (res.ok && data.status === 'success') {
                closeCart();
                window.dispatchEvent(new CustomEvent('cardvault:order-created', {
                    detail: data.data
                }));
            } else {
                alert(`ไม่สามารถทำรายการได้: ${data.message}`);
            }
        } catch (err) {
            console.error('Checkout error:', err);
            alert('เกิดข้อผิดพลาดในการเชื่อมต่อ กรุณาลองใหม่อีกครั้ง');
        } finally {
            btnCheckoutDrawer.disabled = false;
            btnCheckoutDrawer.innerText = 'ดำเนินการชำระเงิน (จองตั๋ว 15 นาที)';
        }
    });

    // 7. Payment Modal & Countdown Management
    const paymentModalOverlay = document.getElementById('payment-modal-overlay');
    const btnCloseModal = document.getElementById('btn-close-modal');
    const modalQrAmount = document.getElementById('modal-qr-amount');
    const modalOrderRef = document.getElementById('modal-order-ref');
    const modalTimer = document.getElementById('modal-timer');
    const btnConfirmPayment = document.getElementById('btn-confirm-payment');
    const paymentStepQr = document.getElementById('payment-step-qr');
    const paymentStepSuccess = document.getElementById('payment-step-success');
    const modalPinsContainer = document.getElementById('modal-pins-container');
    const modalGuideText = document.getElementById('modal-guide-text');
    const btnFinishOrder = document.getElementById('btn-finish-order');

    let currentOrder = null;
    let countdownInterval = null;

    function openPaymentModal(order) {
        currentOrder = order;
        modalOrderRef.innerText = order.order_ref;
        modalQrAmount.innerText = `${parseFloat(order.total_amount).toLocaleString()} THB`;

        paymentStepQr.style.display = 'block';
        paymentStepSuccess.style.display = 'none';
        document.getElementById('modal-title').innerText = 'ชำระเงินผ่าน PromptPay';

        btnConfirmPayment.disabled = false;
        btnConfirmPayment.innerText = 'ยืนยันการชำระเงิน (จำลองจ่ายสำเร็จ)';
        modalTimer.classList.remove('expired');

        startCountdown(order);
        paymentModalOverlay.classList.add('active');
    }

    function closePaymentModal() {
        paymentModalOverlay.classList.remove('active');
        if (countdownInterval) clearInterval(countdownInterval);
        loadCatalog();
    }

    btnCloseModal.addEventListener('click', closePaymentModal);
    btnFinishOrder.addEventListener('click', closePaymentModal);

    function startCountdown(order) {
        if (countdownInterval) clearInterval(countdownInterval);

        // Compute safe expiry timestamp
        let expiryTime;
        const totalSeconds = (order && order.expires_in_seconds) ? order.expires_in_seconds : 15 * 60;

        if (order && order.expires_at) {
            const parsed = new Date(order.expires_at.replace(' ', 'T')).getTime();
            // If parsed time is NaN or in the past by more than 30 seconds (due to server UTC vs client timezone drift)
            if (isNaN(parsed) || parsed < Date.now() - 30000) {
                expiryTime = Date.now() + (totalSeconds * 1000);
            } else {
                expiryTime = parsed;
            }
        } else {
            expiryTime = Date.now() + (totalSeconds * 1000);
        }

        function tick() {
            const now = Date.now();
            const diff = expiryTime - now;

            if (diff <= 0) {
                clearInterval(countdownInterval);
                modalTimer.innerText = 'หมดเวลาชำระเงิน (00:00)';
                modalTimer.classList.add('expired');
                btnConfirmPayment.disabled = true;
                btnConfirmPayment.innerText = 'คำสั่งซื้อหมดอายุแล้ว';
                return;
            }

            modalTimer.classList.remove('expired');
            const minutes = Math.floor(diff / 60000);
            const seconds = Math.floor((diff % 60000) / 1000);
            modalTimer.innerText = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
        }

        tick();
        countdownInterval = setInterval(tick, 1000);
    }

    window.addEventListener('cardvault:order-created', (e) => {
        openPaymentModal(e.detail);
    });

    // 8. Confirm Payment API Call
    btnConfirmPayment.addEventListener('click', async () => {
        if (!currentOrder) return;

        btnConfirmPayment.disabled = true;
        btnConfirmPayment.innerText = 'กำลังตรวจสอบการชำระเงิน...';

        try {
            const res = await fetch('api/payment.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ order_ref: currentOrder.order_ref })
            });

            const data = await res.json();
            if (res.ok && data.status === 'success') {
                if (countdownInterval) clearInterval(countdownInterval);

                paymentStepQr.style.display = 'none';
                paymentStepSuccess.style.display = 'block';
                document.getElementById('modal-title').innerText = 'รับรหัสดิจิทัลสำเร็จ';

                cart.length = 0;
                updateCartUI();

                const fulfillment = data.data;
                modalPinsContainer.innerHTML = fulfillment.vouchers.map(v => `
                    <div class="pin-card">
                        <div>
                            <span class="pin-code-text" id="pin-${v.id}">${v.code}</span>
                            ${v.serial ? `<div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.2rem;">S/N: ${v.serial}</div>` : ''}
                        </div>
                        <button class="btn-copy-pin" onclick="window.cardVault.copyPin('${v.code}', this)">
                            คัดลอกรหัส
                        </button>
                    </div>
                `).join('');

                const guides = Object.entries(fulfillment.redemption_guide || {}).map(([platform, guide]) => `
                    <p style="margin-bottom: 0.4rem;"><strong>${platform}:</strong> ${guide}</p>
                `).join('');
                modalGuideText.innerHTML = guides || '<p>นำรหัส PIN ไปแลกรับในระบบของแพลตฟอร์มที่ระบุได้ทันที</p>';

            } else {
                alert(`เกิดข้อผิดพลาดในการยืนยันชำระเงิน: ${data.message}`);
            }
        } catch (err) {
            console.error('Payment error:', err);
            alert('ไม่สามารถเชื่อมต่อระบบชำระเงินได้');
        } finally {
            btnConfirmPayment.disabled = false;
            btnConfirmPayment.innerText = 'ยืนยันการชำระเงิน (จำลองจ่ายสำเร็จ)';
        }
    });

    // 9. Order Tracking & OTP Inbox Management
    const trackingModalOverlay = document.getElementById('tracking-modal-overlay');
    const btnOpenTrackingModal = document.getElementById('btn-track-order');
    const btnCloseTrackingModal = document.getElementById('btn-close-tracking-modal');
    const tabSingleTrack = document.getElementById('tab-single-track');
    const tabOtpInbox = document.getElementById('tab-otp-inbox');
    const viewSingleTrack = document.getElementById('view-single-track');
    const viewOtpInbox = document.getElementById('view-otp-inbox');

    const trackOrderRef = document.getElementById('track-order-ref');
    const trackEmail = document.getElementById('track-email');
    const btnSubmitTrack = document.getElementById('btn-submit-track');
    const trackResultsContainer = document.getElementById('track-results-container');

    const otpStepRequest = document.getElementById('otp-step-request');
    const otpStepVerify = document.getElementById('otp-step-verify');
    const otpEmailInput = document.getElementById('otp-email-input');
    const btnRequestOtp = document.getElementById('btn-request-otp');
    const otpTargetEmail = document.getElementById('otp-target-email');
    const otpDemoHint = document.getElementById('otp-demo-hint');
    const otpCodeInput = document.getElementById('otp-code-input');
    const btnVerifyOtp = document.getElementById('btn-verify-otp');
    const otpHistoryContainer = document.getElementById('otp-history-container');

    function openTrackingModal() {
        trackingModalOverlay.classList.add('active');
    }

    function closeTrackingModal() {
        trackingModalOverlay.classList.remove('active');
    }

    btnOpenTrackingModal.addEventListener('click', openTrackingModal);
    btnCloseTrackingModal.addEventListener('click', closeTrackingModal);

    tabSingleTrack.addEventListener('click', () => {
        tabSingleTrack.className = 'btn-buy';
        tabSingleTrack.style.width = '';
        tabOtpInbox.className = 'btn-track';
        tabOtpInbox.style.width = '50%';
        viewSingleTrack.style.display = 'block';
        viewOtpInbox.style.display = 'none';
    });

    tabOtpInbox.addEventListener('click', () => {
        tabOtpInbox.className = 'btn-buy';
        tabOtpInbox.style.width = '50%';
        tabSingleTrack.className = 'btn-track';
        tabSingleTrack.style.width = '50%';
        viewSingleTrack.style.display = 'none';
        viewOtpInbox.style.display = 'block';
    });

    btnSubmitTrack.addEventListener('click', async () => {
        const ref = trackOrderRef.value.trim();
        const email = trackEmail.value.trim();

        if (!ref || !email) {
            alert('กรุณากรอกทั้งหมายเลขคำสั่งซื้อและอีเมลให้ครบถ้วน');
            return;
        }

        btnSubmitTrack.disabled = true;
        btnSubmitTrack.innerText = 'กำลังค้นหา...';

        try {
            const res = await fetch(`api/track.php?order_ref=${encodeURIComponent(ref)}&email=${encodeURIComponent(email)}`);
            const data = await res.json();

            if (res.ok && data.status === 'success') {
                const fulfillment = data.data;
                const order = fulfillment.order;
                const isPaid = order.status === 'completed';

                trackResultsContainer.style.display = 'block';
                trackResultsContainer.innerHTML = `
                    <div style="background: var(--bg-subtle); padding: 1.2rem; border-radius: var(--radius-sm); margin-bottom: 1rem;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                            <strong>หมายเลข: ${order.order_ref}</strong>
                            <span style="color: ${isPaid ? 'var(--success)' : 'var(--danger)'}; font-weight: 700;">
                                ${isPaid ? 'ชำระแล้ว' : 'รอชำระเงิน'}
                            </span>
                        </div>
                        <p style="font-size: 0.85rem; color: var(--text-secondary);">ยอดรวม: ${order.total_amount} THB | อีเมล: ${order.guest_email}</p>
                    </div>

                    <h4 style="font-size: 0.95rem; font-weight: 700; margin-bottom: 0.6rem;">รหัสบัตรของคุณ:</h4>
                    <div class="pin-list">
                        ${fulfillment.vouchers.map(v => `
                            <div class="pin-card">
                                <div>
                                    <span class="pin-code-text">${v.code}</span>
                                    ${v.serial ? `<div style="font-size: 0.75rem; color: var(--text-muted);">S/N: ${v.serial}</div>` : ''}
                                </div>
                                ${v.is_unmasked ? `
                                    <button class="btn-copy-pin" onclick="window.cardVault.copyPin('${v.code}', this)">
                                        คัดลอกรหัส
                                    </button>
                                ` : '<span style="font-size: 0.75rem; color: var(--danger);">ยังไม่ชำระเงิน</span>'}
                            </div>
                        `).join('')}
                    </div>
                `;
            } else {
                trackResultsContainer.style.display = 'block';
                trackResultsContainer.innerHTML = `
                    <div style="background: var(--primary-subtle); color: var(--danger); padding: 1rem; border-radius: var(--radius-sm); font-size: 0.88rem;">
                        ${data.message}
                    </div>
                `;
            }
        } catch (err) {
            console.error('Track error:', err);
            alert('ไม่สามารถเชื่อมต่อระบบติดตามคำสั่งซื้อได้');
        } finally {
            btnSubmitTrack.disabled = false;
            btnSubmitTrack.innerText = 'ค้นหาคำสั่งซื้อ';
        }
    });

    btnRequestOtp.addEventListener('click', async () => {
        const email = otpEmailInput.value.trim();
        if (!email || !email.includes('@')) {
            alert('กรุณากรอกอีเมลที่ถูกต้อง');
            return;
        }

        btnRequestOtp.disabled = true;
        btnRequestOtp.innerText = 'กำลังส่งรหัส...';

        try {
            const res = await fetch('api/otp.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'request', email })
            });

            const data = await res.json();
            if (res.ok && data.status === 'success') {
                otpStepRequest.style.display = 'none';
                otpStepVerify.style.display = 'block';
                otpTargetEmail.innerText = email;
                otpDemoHint.innerText = `[DEMO]: รหัส OTP คือ ${data.data.demo_otp}`;
                otpCodeInput.value = data.data.demo_otp;
            } else {
                alert(data.message);
            }
        } catch (err) {
            console.error('OTP request error:', err);
            alert('ไม่สามารถขอรหัส OTP ได้');
        } finally {
            btnRequestOtp.disabled = false;
            btnRequestOtp.innerText = 'ขอรับรหัส OTP 6 หลัก';
        }
    });

    btnVerifyOtp.addEventListener('click', async () => {
        const email = otpEmailInput.value.trim();
        const code = otpCodeInput.value.trim();

        if (code.length !== 6) {
            alert('กรุณากรอกรหัส OTP 6 หลัก');
            return;
        }

        btnVerifyOtp.disabled = true;
        btnVerifyOtp.innerText = 'กำลังตรวจสอบ...';

        try {
            const res = await fetch('api/otp.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'verify', email, otp_code: code })
            });

            const data = await res.json();
            if (res.ok && data.status === 'success') {
                otpStepVerify.style.display = 'none';
                otpHistoryContainer.style.display = 'block';

                const orders = data.data.orders;
                if (orders.length === 0) {
                    otpHistoryContainer.innerHTML = '<p style="text-align: center; color: var(--text-muted); padding: 2rem;">ไม่พบประวัติการสั่งซื้อสำหรับอีเมลนี้</p>';
                    return;
                }

                otpHistoryContainer.innerHTML = orders.map(ord => `
                    <div style="background: var(--bg-main); padding: 1.2rem; border-radius: var(--radius-sm); margin-bottom: 1rem;">
                        <div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 0.5rem;">
                            <strong style="color: var(--text-main); font-size: 0.95rem;">${ord.order_ref}</strong>
                            <span style="font-size: 0.8rem; font-weight: 700; color: ${ord.status === 'completed' ? 'var(--success)' : 'var(--danger)'};">
                                ${ord.status === 'completed' ? 'สำเร็จ' : ord.status}
                            </span>
                        </div>
                        <p style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 0.8rem;">
                            วันที่สั่งซื้อ: ${ord.created_at} | ยอดรวม: ${ord.total_amount} THB
                        </p>
                        <div class="pin-list" style="margin-bottom: 0;">
                            ${ord.vouchers.map(v => `
                                <div class="pin-card" style="background: var(--bg-surface); padding: 0.7rem 1rem;">
                                    <span class="pin-code-text" style="font-size: 1.05rem;">${v.code}</span>
                                    ${v.is_unmasked ? `
                                        <button class="btn-copy-pin" onclick="window.cardVault.copyPin('${v.code}', this)">
                                            คัดลอก
                                        </button>
                                    ` : '<span style="font-size: 0.75rem; color: var(--danger);">ไม่พร้อมใช้งาน</span>'}
                                </div>
                            `).join('')}
                        </div>
                    </div>
                `).join('');
            } else {
                alert(data.message);
            }
        } catch (err) {
            console.error('OTP verify error:', err);
            alert('ไม่สามารถยืนยันรหัส OTP ได้');
        } finally {
            btnVerifyOtp.disabled = false;
            btnVerifyOtp.innerText = 'ยืนยันรหัส OTP';
        }
    });

    // Global helper methods
    window.cardVault = {
        copyPin(code, btn) {
            navigator.clipboard.writeText(code).then(() => {
                const originalText = btn.innerText;
                btn.innerText = 'คัดลอกแล้ว';
                btn.style.backgroundColor = 'var(--primary-subtle)';
                btn.style.color = 'var(--primary)';
                setTimeout(() => {
                    btn.innerText = originalText;
                    btn.style.backgroundColor = '';
                    btn.style.color = '';
                }, 2000);
            });
        },

        changeQty(idx, delta) {
            const item = cart[idx];
            if (!item) return;
            const newQty = item.quantity + delta;
            if (newQty >= 1 && newQty <= 10) {
                item.quantity = newQty;
                updateCartUI();
            }
        },

        removeFromCart(idx) {
            cart.splice(idx, 1);
            updateCartUI();
        },

        onDenomChange(selectElem, platformId) {
            const selectedOpt = selectElem.options[selectElem.selectedIndex];
            const newPrice = parseFloat(selectedOpt.dataset.price) || 0;
            const stock = parseInt(selectedOpt.dataset.stock, 10);

            const priceElem = document.getElementById(`price-${platformId}`);
            const oldPrice = parseFloat(priceElem.innerText.replace(/,/g, '')) || 0;
            animateNumber(priceElem, oldPrice, newPrice, 250);

            const stockElem = document.getElementById(`stock-${platformId}`);
            const buyBtn = document.getElementById(`btn-buy-${platformId}`);
            const cartBtn = document.getElementById(`btn-cart-${platformId}`);

            if (stock > 0) {
                stockElem.className = 'stock-tag stock-in';
                stockElem.innerHTML = `<span class="stock-dot in-stock"></span>มีสินค้า (${stock} ชิ้น)`;
                buyBtn.disabled = false;
                cartBtn.disabled = false;
            } else {
                stockElem.className = 'stock-tag stock-out';
                stockElem.innerHTML = `<span class="stock-dot out-stock"></span>สินค้าหมดชั่วคราว`;
                buyBtn.disabled = true;
                cartBtn.disabled = true;
            }
        },

        handleBuyNow(platformId) {
            const card = document.querySelector(`.product-card[data-platform-id="${platformId}"]`);
            const select = card.querySelector('.denom-select');
            const selectedOpt = select.options[select.selectedIndex];
            const denomId = parseInt(select.value, 10);
            const name = selectedOpt.dataset.name;
            const price = parseFloat(selectedOpt.dataset.price);

            window.dispatchEvent(new CustomEvent('cardvault:instant-buy', {
                detail: { denomId, name, price, quantity: 1 }
            }));
        },

        handleAddToCart(platformId) {
            const card = document.querySelector(`.product-card[data-platform-id="${platformId}"]`);
            const select = card.querySelector('.denom-select');
            const selectedOpt = select.options[select.selectedIndex];
            const denomId = parseInt(select.value, 10);
            const name = selectedOpt.dataset.name;
            const price = parseFloat(selectedOpt.dataset.price);

            window.dispatchEvent(new CustomEvent('cardvault:add-to-cart', {
                detail: { denomId, name, price, quantity: 1 }
            }));
        }
    };

    updateCartUI();
    loadCatalog();
    initClickSpark();
    initMagneticButtons();
});
