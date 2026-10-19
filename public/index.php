<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CARDVAULT - ร้านคีย์ดิจิทัล & บัตรของขวัญ จ่ายปุ๊บ ได้รหัสปั๊บ</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

  <!-- Navbar -->
  <!-- Navbar -->
  <header class="navbar">
    <div class="nav-container">
      <div class="brand-group">
        <a href="index.php" class="brand">
          <span>CARDVAULT</span>
          <span class="highlight">•</span>
        </a>
        <span class="brand-sub">DIGITAL MARKETPLACE</span>
      </div>

      <div class="nav-search">
        <span class="nav-search-icon">
          <svg class="icon-svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="11" cy="11" r="8"></circle>
            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
          </svg>
        </span>
        <input type="text" id="search-input" placeholder="ค้นหาบัตรเติมเงิน, สตรีมมิ่ง, เกมมิ่ง...">
      </div>

      <div class="nav-actions">
        <button class="btn-track" id="btn-track-order">
          <svg class="icon-svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 5px;">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
            <polyline points="14 2 14 8 20 8"></polyline>
            <line x1="16" y1="13" x2="8" y2="13"></line>
            <line x1="16" y1="17" x2="8" y2="17"></line>
          </svg>
          ติดตามคำสั่งซื้อ
        </button>
        <button class="cart-btn" id="btn-open-cart">
          <svg class="icon-svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="9" cy="21" r="1"></circle>
            <circle cx="20" cy="21" r="1"></circle>
            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
          </svg>
          <span>ตะกร้าสินค้า</span>
          <span class="cart-count" id="header-cart-count">0</span>
        </button>
      </div>
    </div>
  </header>

  <!-- Hero Section -->
  <section class="hero">
    <h1>ศูนย์รวมคีย์ดิจิทัล <span>จ่ายปุ๊บ ได้รหัสปั๊บ</span></h1>
    <p>สะดวก ปลอดภัย ไม่ต้องลงทะเบียน สแกนจ่ายผ่านพร้อมเพย์ QR และรับรหัสใช้งานได้ทันที</p>
    
    <div class="hero-features">
      <div class="feature-item">
        <svg class="icon-svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary); margin-right: 6px;">
          <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
        </svg>
        จัดส่งรหัสทันทีหลังชำระเงิน
      </div>
      <div class="feature-item">
        <svg class="icon-svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary); margin-right: 6px;">
          <circle cx="12" cy="12" r="10"></circle>
          <polyline points="12 6 12 12 16 14"></polyline>
        </svg>
        ล็อกสต็อก 15 นาทีระหว่างชำระ
      </div>
      <div class="feature-item">
        <svg class="icon-svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary); margin-right: 6px;">
          <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
          <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
        </svg>
        พร้อมเพย์ QR ฟรีค่าธรรมเนียม
      </div>
    </div>
  </section>

  <!-- Category Nav Tabs -->
  <div class="catalog-nav">
    <button class="nav-tab active" data-category="all">
      <svg class="icon-svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" style="margin-right: 6px;">
        <rect x="3" y="3" width="7" height="7" rx="1.5"></rect>
        <rect x="14" y="3" width="7" height="7" rx="1.5"></rect>
        <rect x="14" y="14" width="7" height="7" rx="1.5"></rect>
        <rect x="3" y="14" width="7" height="7" rx="1.5"></rect>
      </svg>
      สินค้าทั้งหมด
    </button>
    <button class="nav-tab" data-category="gift-cards">
      <svg class="icon-svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" style="margin-right: 6px;">
        <rect x="3" y="8" width="18" height="13" rx="2"></rect>
        <path d="M12 8v13"></path>
        <path d="M19 12H5"></path>
        <path d="M7.5 8a2.5 2.5 0 0 1 0-5A4.8 8 0 0 1 12 8a4.8 8 0 0 1 4.5-5 2.5 2.5 0 0 1 0 5"></path>
      </svg>
      บัตรของขวัญ
    </button>
    <button class="nav-tab" data-category="streaming">
      <svg class="icon-svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" style="margin-right: 6px;">
        <rect x="2" y="7" width="20" height="15" rx="2"></rect>
        <polyline points="17 2 12 7 7 2"></polyline>
      </svg>
      สตรีมมิ่ง
    </button>
    <button class="nav-tab" data-category="gaming">
      <svg class="icon-svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" style="margin-right: 6px;">
        <line x1="6" y1="12" x2="10" y2="12"></line>
        <line x1="8" y1="10" x2="8" y2="14"></line>
        <circle cx="15" cy="13" r="1"></circle>
        <circle cx="18" cy="11" r="1"></circle>
        <rect x="2" y="6" width="20" height="12" rx="6"></rect>
      </svg>
      เกมมิ่ง
    </button>
  </div>

  <!-- Main Grid -->
  <main class="container">
    <h2 class="section-title">รายการสินค้าพร้อมส่ง</h2>
    <div id="catalog-grid" class="product-grid">
      <!-- Loaded dynamically via app.js -->
    </div>
  </main>

  <!-- Cart Drawer Backdrop & Sidebar -->
  <div class="drawer-overlay" id="cart-drawer-overlay"></div>
  <aside class="cart-drawer" id="cart-drawer">
    <div class="drawer-header">
      <h3>ตะกร้าสินค้า</h3>
      <button class="drawer-close-btn" id="btn-close-cart">&times;</button>
    </div>
    <div class="drawer-body" id="cart-drawer-items">
      <!-- Items dynamically rendered -->
    </div>
    <div class="drawer-footer">
      <div class="drawer-total-row">
        <span>ยอดรวมชำระ</span>
        <span class="drawer-total-price" id="drawer-total-price">0 THB</span>
      </div>
      <input type="email" id="drawer-email-input" class="drawer-email-input" placeholder="กรอกอีเมลสำหรับรับรหัสบัตร...">
      <button class="btn-checkout-drawer" id="btn-checkout-drawer">
        ดำเนินการชำระเงิน (จองตั๋ว 15 นาที)
      </button>
    </div>
  </aside>

  <!-- Payment & Fulfillment Modal -->
  <div class="modal-overlay" id="payment-modal-overlay">
    <div class="payment-modal" id="payment-modal">
      <div class="modal-header">
        <h3 class="modal-title" id="modal-title">ชำระเงินผ่าน PromptPay</h3>
        <button class="modal-close-btn" id="btn-close-modal">&times;</button>
      </div>

      <!-- Step 1: PromptPay QR Code View -->
      <div id="payment-step-qr">
        <div class="qr-box">
          <div class="qr-header">สแกน QR Code ด้วยแอปธนาคารใดก็ได้</div>
          <div class="qr-code-img">
            <!-- Simulated PromptPay QR Graphic -->
            <svg viewBox="0 0 100 100" fill="#0f172a">
              <rect width="100" height="100" fill="#ffffff"/>
              <!-- Corner markers -->
              <rect x="10" y="10" width="24" height="24" fill="#0f172a"/>
              <rect x="14" y="14" width="16" height="16" fill="#ffffff"/>
              <rect x="18" y="18" width="8" height="8" fill="#e11d48"/>

              <rect x="66" y="10" width="24" height="24" fill="#0f172a"/>
              <rect x="70" y="14" width="16" height="16" fill="#ffffff"/>
              <rect x="74" y="18" width="8" height="8" fill="#e11d48"/>

              <rect x="10" y="66" width="24" height="24" fill="#0f172a"/>
              <rect x="14" y="70" width="16" height="16" fill="#ffffff"/>
              <rect x="18" y="74" width="8" height="8" fill="#e11d48"/>

              <!-- Central simulated patterns -->
              <rect x="42" y="12" width="6" height="6" fill="#0f172a"/>
              <rect x="52" y="12" width="6" height="6" fill="#0f172a"/>
              <rect x="42" y="22" width="16" height="6" fill="#0f172a"/>
              <rect x="12" y="42" width="6" height="16" fill="#0f172a"/>
              <rect x="22" y="42" width="16" height="6" fill="#0f172a"/>
              <rect x="45" y="45" width="10" height="10" fill="#e11d48"/>
              <rect x="66" y="42" width="20" height="6" fill="#0f172a"/>
              <rect x="72" y="52" width="14" height="6" fill="#0f172a"/>
              <rect x="42" y="66" width="6" height="20" fill="#0f172a"/>
              <rect x="52" y="72" width="16" height="6" fill="#0f172a"/>
              <rect x="72" y="72" width="16" height="16" fill="#0f172a"/>
            </svg>
          </div>
          <div class="qr-amount" id="modal-qr-amount">0 THB</div>
          <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.8rem;">
            หมายเลขคำสั่งซื้อ: <strong id="modal-order-ref" style="color: var(--text-main);">CV-XXXXXX</strong>
          </p>
          <div class="qr-timer">
            เวลาชำระเงินคงเหลือ: <span id="modal-timer">15:00</span>
          </div>
        </div>

        <button class="btn-confirm-pay" id="btn-confirm-payment">
          ยืนยันการชำระเงิน (จำลองจ่ายสำเร็จ)
        </button>
      </div>

      <!-- Step 2: Fulfillment & PIN Display View (Shown after payment) -->
      <div id="payment-step-success" style="display: none;">
        <div class="success-banner">
          <h3>ชำระเงินสำเร็จแล้ว</h3>
          <p>รหัสดิจิทัลของคุณพร้อมใช้งานทันที (ส่งสำเนาเข้าอีเมลเรียบร้อย)</p>
        </div>

        <h4 style="font-size: 0.95rem; font-weight: 700; margin-bottom: 0.8rem; color: var(--text-main);">
          รหัสบัตรของคุณ (PIN Code):
        </h4>
        <div class="pin-list" id="modal-pins-container">
          <!-- PIN items injected dynamically -->
        </div>

        <div class="guide-box" id="modal-guide-container">
          <strong>คำแนะนำการเติมรหัส:</strong>
          <div id="modal-guide-text"></div>
        </div>

        <button class="btn-confirm-pay" id="btn-finish-order" style="background-color: var(--bg-subtle); color: var(--text-main);">
          เสร็จสิ้นและปิดหน้าต่าง
        </button>
      </div>
    </div>
  </div>

  <!-- Order Tracking & OTP Inbox Modal -->
  <div class="modal-overlay" id="tracking-modal-overlay">
    <div class="payment-modal" style="max-width: 580px;">
      <div class="modal-header">
        <h3 class="modal-title">ค้นหาและจัดการรหัสบัตร</h3>
        <button class="modal-close-btn" id="btn-close-tracking-modal">&times;</button>
      </div>

      <!-- Tabs: Order Search vs History -->
      <div style="display: flex; gap: 0.5rem; margin-bottom: 1.5rem; background: var(--bg-subtle); padding: 0.3rem; border-radius: var(--radius-sm);">
        <button id="tab-single-track" class="btn-buy" style="padding: 0.5rem; font-size: 0.85rem; border-radius: var(--radius-sm);">ค้นหาด้วยหมายเลขคำสั่งซื้อ</button>
        <button id="tab-otp-inbox" class="btn-track" style="padding: 0.5rem; font-size: 0.85rem; border-radius: var(--radius-sm); width: 50%;">ดูประวัติคำสั่งซื้อทั้งหมด</button>
      </div>

      <!-- Mode 1: Single Order Search -->
      <div id="view-single-track">
        <div style="display: flex; flex-direction: column; gap: 0.8rem; margin-bottom: 1.2rem;">
          <input type="text" id="track-order-ref" class="drawer-email-input" placeholder="หมายเลขคำสั่งซื้อ (เช่น CV-XXXXXX)">
          <input type="email" id="track-email" class="drawer-email-input" placeholder="อีเมลที่ใช้สั่งซื้อ">
        </div>
        <button class="btn-confirm-pay" id="btn-submit-track" style="margin-top: 0;">
          ค้นหาคำสั่งซื้อ
        </button>

        <div id="track-results-container" style="margin-top: 1.5rem; display: none;">
          <!-- Tracked results -->
        </div>
      </div>

      <!-- Mode 2: OTP Inbox -->
      <div id="view-otp-inbox" style="display: none;">
        <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 1rem; line-height: 1.5;">
          เข้าสู่ระบบชั่วคราวด้วยอีเมลเพื่อดูประวัติรหัสบัตรทั้งหมดที่คุณเคยสั่งซื้อ
        </p>

        <!-- OTP Request Step -->
        <div id="otp-step-request">
          <input type="email" id="otp-email-input" class="drawer-email-input" placeholder="กรอกอีเมลของคุณ..." style="margin-bottom: 0.8rem;">
          <button class="btn-confirm-pay" id="btn-request-otp" style="margin-top: 0;">
            ขอรับรหัส OTP 6 หลัก
          </button>
        </div>

        <!-- OTP Verify Step -->
        <div id="otp-step-verify" style="display: none; margin-top: 1rem;">
          <div style="background: var(--primary-subtle); padding: 0.8rem 1rem; border-radius: var(--radius-sm); margin-bottom: 1rem; font-size: 0.85rem; color: var(--primary);">
            ส่งรหัสไปที่: <strong id="otp-target-email"></strong> <span id="otp-demo-hint" style="display: block; font-size: 0.75rem; margin-top: 0.2rem; color: var(--text-secondary);"></span>
          </div>
          <input type="text" id="otp-code-input" class="drawer-email-input" placeholder="กรอกรหัส OTP 6 หลัก" maxlength="6" style="margin-bottom: 0.8rem; letter-spacing: 4px; font-weight: 700; text-align: center;">
          <button class="btn-confirm-pay" id="btn-verify-otp" style="margin-top: 0;">
            ยืนยันรหัส OTP
          </button>
        </div>

        <!-- OTP History List -->
        <div id="otp-history-container" style="display: none; margin-top: 1.5rem; max-height: 380px; overflow-y: auto;">
          <!-- Historical orders list -->
        </div>
      </div>
    </div>
  </div>

  <!-- Footer -->
  <footer>
    <p>© 2026 CARDVAULT Digital Key Marketplace. สงวนลิขสิทธิ์</p>
    <p style="margin-top: 0.5rem; color: #94a3b8; font-size: 0.8rem;">
      ระบบคลังคีย์เกมและบัตรเติมเงินดิจิทัลอัตโนมัติ 24 ชั่วโมง • <a href="admin.php" style="color: inherit; text-decoration: underline;">ระบบจัดการหลังบ้าน</a>
    </p>
  </footer>

  <script src="assets/js/app.js"></script>
</body>
</html>
