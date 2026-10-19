<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CARDVAULT Backoffice - ระบบจัดการร้านค้า</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    .admin-container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 2rem 1.5rem 5rem;
      width: 100%;
    }
    .admin-header-bar {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 2rem;
      flex-wrap: wrap;
      gap: 1rem;
    }
    .admin-title-group h1 {
      font-size: 1.6rem;
      font-weight: 800;
      color: var(--text-main);
      margin: 0 0 0.25rem;
      letter-spacing: -0.5px;
    }
    .admin-title-group p {
      font-size: 0.88rem;
      color: var(--text-muted);
      margin: 0;
    }
    .admin-stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
      gap: 1.25rem;
      margin-bottom: 2rem;
    }
    .stat-card {
      background: var(--bg-surface);
      border-radius: var(--radius-md);
      padding: 1.4rem;
      display: flex;
      flex-direction: column;
      gap: 0.4rem;
    }
    .stat-card-label {
      font-size: 0.82rem;
      font-weight: 600;
      color: var(--text-secondary);
      display: flex;
      align-items: center;
      gap: 0.4rem;
    }
    .stat-card-value {
      font-size: 1.85rem;
      font-weight: 800;
      color: var(--text-main);
      letter-spacing: -0.5px;
    }
    .stat-card-sub {
      font-size: 0.76rem;
      color: var(--text-muted);
    }
    .admin-card {
      background: var(--bg-surface);
      border-radius: var(--radius-md);
      padding: 1.75rem;
      margin-bottom: 2rem;
    }
    .admin-card-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 1.25rem;
      flex-wrap: wrap;
      gap: 0.8rem;
    }
    .admin-card-title {
      font-size: 1.2rem;
      font-weight: 800;
      color: var(--text-main);
      margin: 0;
    }
    .admin-table-wrapper {
      width: 100%;
      overflow-x: auto;
    }
    .admin-table {
      width: 100%;
      border-collapse: collapse;
      text-align: left;
      font-size: 0.88rem;
    }
    .admin-table th {
      background: var(--bg-subtle);
      color: var(--text-secondary);
      font-weight: 700;
      padding: 0.85rem 1rem;
      white-space: nowrap;
    }
    .admin-table th:first-child { border-radius: var(--radius-sm) 0 0 var(--radius-sm); }
    .admin-table th:last-child { border-radius: 0 var(--radius-sm) var(--radius-sm) 0; }
    .admin-table td {
      padding: 0.95rem 1rem;
      border-bottom: 1px solid var(--bg-subtle);
      color: var(--text-main);
      vertical-align: middle;
    }
    .admin-table tr:last-child td {
      border-bottom: none;
    }
    .form-group {
      margin-bottom: 1.25rem;
    }
    .form-group label {
      display: block;
      font-size: 0.82rem;
      font-weight: 600;
      color: var(--text-secondary);
      margin-bottom: 0.45rem;
    }
    .form-control {
      width: 100%;
      background: var(--bg-subtle);
      padding: 0.75rem 1rem;
      border-radius: var(--radius-sm);
      font-family: var(--font-family);
      font-size: 0.9rem;
      color: var(--text-main);
      transition: background-color 0.2s;
    }
    .form-control:focus {
      background: var(--bg-hover);
    }
    textarea.form-control {
      min-height: 120px;
      resize: vertical;
      line-height: 1.5;
      font-family: monospace;
    }
    .login-box-container {
      max-width: 420px;
      margin: 5rem auto;
      background: var(--bg-surface);
      border-radius: var(--radius-md);
      padding: 2.5rem 2rem;
      text-align: center;
    }
    .login-box-container h2 {
      font-size: 1.5rem;
      font-weight: 800;
      margin-bottom: 0.35rem;
      color: var(--text-main);
    }
    .login-box-container p {
      color: var(--text-muted);
      font-size: 0.88rem;
      margin-bottom: 1.75rem;
    }
    .admin-badge {
      display: inline-flex;
      align-items: center;
      gap: 0.4rem;
      font-size: 0.76rem;
      font-weight: 600;
      color: var(--primary);
    }
  </style>
</head>
<body>

  <!-- Top Navigation -->
  <header class="navbar">
    <div class="nav-container">
      <div class="brand-group">
        <a href="index.php" class="brand">
          <span>CARDVAULT</span>
          <span class="highlight">•</span>
        </a>
        <span class="brand-sub">ADMIN BACKOFFICE</span>
      </div>

      <div class="nav-actions" id="nav-auth-actions" style="display: none;">
        <span class="admin-badge" id="admin-user-display">
          <svg class="icon-svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
            <circle cx="12" cy="7" r="4"></circle>
          </svg>
          <span id="admin-name">Admin</span>
        </span>
        <a href="index.php" class="btn-track" style="font-size: 0.85rem; padding: 0.55rem 1rem; border-radius: var(--radius-sm); text-decoration: none;">
          ไปหน้าร้าน
        </a>
        <button id="btn-admin-logout" class="btn-track" style="font-size: 0.85rem; padding: 0.55rem 1rem; border-radius: var(--radius-sm); color: var(--danger);">
          ออกจากระบบ
        </button>
      </div>
    </div>
  </header>

  <!-- Login Section (shown when unauthenticated) -->
  <div id="section-login" style="display: none;">
    <div class="login-box-container">
      <h2>เข้าสู่ระบบหลังบ้าน</h2>
      <p>กรุณาลงชื่อเข้าใช้ด้วยบัญชีผู้ดูแลระบบ CARDVAULT</p>
      <form id="form-admin-login">
        <div class="form-group" style="text-align: left;">
          <label>ชื่อผู้ใช้งาน</label>
          <input type="text" id="login-username" class="form-control" placeholder="ชื่อผู้ใช้งาน (เช่น admin)" required>
        </div>
        <div class="form-group" style="text-align: left;">
          <label>รหัสผ่าน</label>
          <input type="password" id="login-password" class="form-control" placeholder="รหัสผ่าน" required>
        </div>
        <div id="login-error-msg" style="color: var(--danger); font-size: 0.84rem; margin-bottom: 1rem; display: none;"></div>
        <button type="submit" class="btn-buy" style="width: 100%; padding: 0.85rem; border-radius: var(--radius-sm);">
          เข้าสู่ระบบ
        </button>
        <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 1rem;">
          ค่าเริ่มต้นสำหรับการทดสอบ: admin / admin1234
        </p>
      </form>
    </div>
  </div>

  <!-- Dashboard Main Content (shown when authenticated) -->
  <main id="section-dashboard" class="admin-container" style="display: none;">
    <div class="admin-header-bar">
      <div class="admin-title-group">
        <h1>แดชบอร์ดและการจัดการคลังสินค้า</h1>
        <p>ตรวจสอบสถิติยอดขาย จัดการสต็อกรหัสบัตร และตรวจสอบประวัติคำสั่งซื้อแบบเรียลไทม์</p>
      </div>
      <div>
        <button id="btn-refresh-dashboard" class="btn-track" style="padding: 0.6rem 1.2rem; border-radius: var(--radius-sm); font-size: 0.85rem;">
          <svg class="icon-svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" style="margin-right: 4px;">
            <path d="M23 4v6h-6"></path>
            <path d="M1 20v-6h6"></path>
            <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
          </svg>
          รีเฟรชข้อมูล
        </button>
      </div>
    </div>

    <!-- Summary Stats Grid -->
    <div class="admin-stats-grid">
      <div class="stat-card">
        <div class="stat-card-label">
          <svg class="icon-svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" style="color: var(--primary);">
            <line x1="12" y1="1" x2="12" y2="23"></line>
            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
          </svg>
          ยอดขายรวมทั้งหมด
        </div>
        <div class="stat-card-value" id="stat-revenue">0.00 THB</div>
        <div class="stat-card-sub" id="stat-revenue-sub">จากออเดอร์ที่ชำระสำเร็จ</div>
      </div>

      <div class="stat-card">
        <div class="stat-card-label">
          <svg class="icon-svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" style="color: var(--success);">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
            <polyline points="22 4 12 14.01 9 11.01"></polyline>
          </svg>
          คำสั่งซื้อที่สำเร็จ
        </div>
        <div class="stat-card-value" id="stat-orders-completed">0</div>
        <div class="stat-card-sub" id="stat-orders-total">จากทั้งหมด 0 คำสั่งซื้อ</div>
      </div>

      <div class="stat-card">
        <div class="stat-card-label">
          <svg class="icon-svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" style="color: var(--primary);">
            <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
            <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
          </svg>
          รหัสบัตรพร้อมจำหน่าย
        </div>
        <div class="stat-card-value" id="stat-vouchers-available">0</div>
        <div class="stat-card-sub" id="stat-vouchers-sold">จัดส่งให้ลูกค้าแล้ว 0 ชิ้น</div>
      </div>
    </div>

    <!-- Bulk Import Card -->
    <div class="admin-card">
      <div class="admin-card-header">
        <h2 class="admin-card-title">
          เติมสต็อกรหัสบัตรดิจิทัล (Bulk Inventory Import)
        </h2>
      </div>
      <form id="form-import-vouchers">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1rem;">
          <div class="form-group">
            <label>เลือกแพ็กเกจสินค้าที่จะเติมสต็อก</label>
            <select id="import-denom-select" class="form-control" required>
              <option value="">กำลังโหลดรายการสินค้า...</option>
            </select>
          </div>
          <div class="form-group">
            <label>กรอกรหัสบัตร (PIN Codes) บรรทัดละ 1 รหัส</label>
            <textarea id="import-codes-input" class="form-control" placeholder="ตัวอย่าง:&#10;NETFLIX-9921-AA88-1029&#10;NETFLIX-4412-BB19-5501&#10;NETFLIX-8812-CC44-7711" required></textarea>
          </div>
        </div>
        <div style="display: flex; justify-content: flex-end; align-items: center; gap: 1rem; margin-top: 0.5rem;">
          <span id="import-msg" style="font-size: 0.85rem; font-weight: 600;"></span>
          <button type="submit" id="btn-submit-import" class="btn-buy" style="padding: 0.75rem 1.75rem; border-radius: var(--radius-sm);">
            นำเข้ารหัสบัตรเข้าคลัง
          </button>
        </div>
      </form>
    </div>

    <!-- Stock Overview Table Card -->
    <div class="admin-card">
      <div class="admin-card-header">
        <h2 class="admin-card-title">สถานะคลังสินค้าปัจจุบัน (Inventory Breakdown)</h2>
      </div>
      <div class="admin-table-wrapper">
        <table class="admin-table">
          <thead>
            <tr>
              <th>แพลตฟอร์ม</th>
              <th>แพ็กเกจ / รายการ</th>
              <th>หมวดหมู่</th>
              <th>ราคาต่อหน่วย</th>
              <th>คงเหลือในคลัง</th>
              <th>ขายแล้ว</th>
            </tr>
          </thead>
          <tbody id="stock-table-body">
            <tr>
              <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 2rem;">กำลังโหลดข้อมูล...</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Recent Orders Table Card -->
    <div class="admin-card">
      <div class="admin-card-header">
        <h2 class="admin-card-title">ประวัติคำสั่งซื้อล่าสุด (Recent Orders)</h2>
      </div>
      <div class="admin-table-wrapper">
        <table class="admin-table">
          <thead>
            <tr>
              <th>หมายเลขคำสั่งซื้อ</th>
              <th>อีเมลผู้สั่งซื้อ</th>
              <th>ยอดเงินรวม</th>
              <th>ช่องทางชำระ</th>
              <th>สถานะ</th>
              <th>เวลาที่สั่งซื้อ</th>
              <th>การจัดการ</th>
            </tr>
          </thead>
          <tbody id="orders-table-body">
            <tr>
              <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 2rem;">กำลังโหลดข้อมูล...</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </main>

  <!-- Order Details Modal -->
  <div class="modal-overlay" id="admin-order-modal-overlay">
    <div class="payment-modal" style="max-width: 600px;">
      <div class="modal-header">
        <h3 class="modal-title">รายละเอียดคำสั่งซื้อ</h3>
        <button class="modal-close-btn" id="btn-close-admin-modal">&times;</button>
      </div>
      <div id="admin-order-modal-body" style="padding-top: 0.5rem;">
        <!-- Loaded dynamically -->
      </div>
    </div>
  </div>

  <script src="assets/js/admin.js"></script>
</body>
</html>
