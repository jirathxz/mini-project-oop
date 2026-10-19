document.addEventListener('DOMContentLoaded', () => {
    const sectionLogin = document.getElementById('section-login');
    const sectionDashboard = document.getElementById('section-dashboard');
    const navAuthActions = document.getElementById('nav-auth-actions');
    const adminName = document.getElementById('admin-name');
    const loginErrorMsg = document.getElementById('login-error-msg');

    const formLogin = document.getElementById('form-admin-login');
    const btnLogout = document.getElementById('btn-admin-logout');
    const btnRefresh = document.getElementById('btn-refresh-dashboard');

    const statRevenue = document.getElementById('stat-revenue');
    const statOrdersCompleted = document.getElementById('stat-orders-completed');
    const statOrdersTotal = document.getElementById('stat-orders-total');
    const statVouchersAvailable = document.getElementById('stat-vouchers-available');
    const statVouchersSold = document.getElementById('stat-vouchers-sold');

    const importDenomSelect = document.getElementById('import-denom-select');
    const formImport = document.getElementById('form-import-vouchers');
    const importCodesInput = document.getElementById('import-codes-input');
    const importMsg = document.getElementById('import-msg');

    const stockTableBody = document.getElementById('stock-table-body');
    const ordersTableBody = document.getElementById('orders-table-body');

    const orderModalOverlay = document.getElementById('admin-order-modal-overlay');
    const orderModalBody = document.getElementById('admin-order-modal-body');
    const btnCloseModal = document.getElementById('btn-close-admin-modal');

    // 1. Initial Auth Check
    checkAuth();

    async function checkAuth() {
        try {
            const res = await fetch('api/admin.php?action=check_auth');
            const data = await res.json();
            if (data.authenticated) {
                showDashboard(data.admin);
            } else {
                showLogin();
            }
        } catch (err) {
            console.error('Check auth error:', err);
            showLogin();
        }
    }

    function showLogin() {
        sectionLogin.style.display = 'block';
        sectionDashboard.style.display = 'none';
        navAuthActions.style.display = 'none';
    }

    function showDashboard(admin) {
        sectionLogin.style.display = 'none';
        sectionDashboard.style.display = 'block';
        navAuthActions.style.display = 'flex';
        adminName.textContent = admin.name || admin.username;
        loadDashboardData();
    }

    // 2. Handle Login
    formLogin.addEventListener('submit', async (e) => {
        e.preventDefault();
        loginErrorMsg.style.display = 'none';

        const username = document.getElementById('login-username').value.trim();
        const password = document.getElementById('login-password').value;

        try {
            const res = await fetch('api/admin.php?action=login', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ username, password })
            });
            const data = await res.json();

            if (data.success) {
                showDashboard(data.admin);
            } else {
                loginErrorMsg.textContent = data.error || 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง';
                loginErrorMsg.style.display = 'block';
            }
        } catch (err) {
            loginErrorMsg.textContent = 'ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้';
            loginErrorMsg.style.display = 'block';
        }
    });

    // 3. Handle Logout
    btnLogout.addEventListener('click', async () => {
        try {
            await fetch('api/admin.php?action=logout');
            showLogin();
        } catch (err) {
            console.error('Logout error:', err);
        }
    });

    // 4. Refresh Dashboard
    btnRefresh.addEventListener('click', loadDashboardData);

    async function loadDashboardData() {
        loadMetrics();
        loadStockOverview();
        loadOrders();
    }

    // 5. Load Metrics
    async function loadMetrics() {
        try {
            const res = await fetch('api/admin.php?action=dashboard');
            const resData = await res.json();
            if (resData.success) {
                const data = resData.data;
                statRevenue.textContent = (data.revenue.total_amount || 0).toLocaleString('th-TH', { minimumFractionDigits: 2 }) + ' THB';
                statOrdersCompleted.textContent = data.orders.completed || 0;
                statOrdersTotal.textContent = `จากทั้งหมด ${data.orders.total || 0} คำสั่งซื้อ (รอดำเนินการ ${data.orders.pending || 0})`;
                statVouchersAvailable.textContent = data.inventory.total_available || 0;
                statVouchersSold.textContent = `จัดส่งให้ลูกค้าแล้ว ${data.inventory.total_sold || 0} ชิ้น`;
            }
        } catch (err) {
            console.error('Load metrics error:', err);
        }
    }

    // 6. Load Stock Overview & Populate Denomination Dropdown
    async function loadStockOverview() {
        try {
            const res = await fetch('api/admin.php?action=stock_overview');
            const resData = await res.json();
            if (resData.success) {
                const items = resData.data;

                // Render Stock Table
                if (items.length === 0) {
                    stockTableBody.innerHTML = `<tr><td colspan="6" style="text-align:center; padding: 2rem;">ไม่มีข้อมูลสินค้า</td></tr>`;
                } else {
                    stockTableBody.innerHTML = items.map(item => {
                        const stock = parseInt(item.available_stock, 10);
                        const isLow = stock <= 2;
                        const stockColor = isLow ? 'var(--danger)' : 'var(--success)';
                        const stockBadge = isLow ? `<span style="color: var(--danger); font-size: 0.76rem; font-weight: 700; margin-left: 4px;">(สต็อกใกล้หมด)</span>` : '';

                        return `
                            <tr>
                                <td><strong>${item.platform_name}</strong></td>
                                <td>${item.denomination_name}</td>
                                <td><span style="color: var(--text-secondary); font-size: 0.8rem;">${item.category_name}</span></td>
                                <td>${parseFloat(item.price).toLocaleString()} THB</td>
                                <td>
                                    <span style="display: inline-flex; align-items: center; gap: 0.4rem; font-weight: 700; color: ${stockColor};">
                                        <span style="width: 7px; height: 7px; border-radius: 50%; background: ${stockColor}; display: inline-block;"></span>
                                        ${stock} ชิ้น ${stockBadge}
                                    </span>
                                </td>
                                <td><span style="color: var(--text-muted);">${item.sold_count} ชิ้น</span></td>
                            </tr>
                        `;
                    }).join('');
                }

                // Populate Dropdown
                importDenomSelect.innerHTML = items.map(item => `
                    <option value="${item.denomination_id}">
                        ${item.platform_name} - ${item.denomination_name} (สต็อกปัจจุบัน: ${item.available_stock} ชิ้น)
                    </option>
                `).join('');
            }
        } catch (err) {
            console.error('Load stock error:', err);
        }
    }

    // 7. Bulk Import Vouchers
    formImport.addEventListener('submit', async (e) => {
        e.preventDefault();
        const denomId = parseInt(importDenomSelect.value, 10);
        const codesRaw = importCodesInput.value;

        if (!denomId) {
            alert('กรุณาเลือกแพ็กเกจสินค้า');
            return;
        }

        importMsg.style.color = 'var(--text-secondary)';
        importMsg.textContent = 'กำลังนำเข้ารหัส...';

        try {
            const res = await fetch('api/admin.php?action=import_vouchers', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    denomination_id: denomId,
                    codes: codesRaw
                })
            });
            const data = await res.json();

            if (data.success) {
                importMsg.style.color = 'var(--success)';
                importMsg.textContent = data.message;
                importCodesInput.value = '';
                loadMetrics();
                loadStockOverview();
            } else {
                importMsg.style.color = 'var(--danger)';
                importMsg.textContent = data.error || 'เกิดข้อผิดพลาดในการนำเข้า';
            }
        } catch (err) {
            importMsg.style.color = 'var(--danger)';
            importMsg.textContent = 'เกิดข้อผิดพลาดในการเชื่อมต่อ';
        }
    });

    // 8. Load Recent Orders
    async function loadOrders() {
        try {
            const res = await fetch('api/admin.php?action=orders&limit=50');
            const resData = await res.json();
            if (resData.success) {
                const orders = resData.data;

                if (orders.length === 0) {
                    ordersTableBody.innerHTML = `<tr><td colspan="7" style="text-align: center; padding: 2rem;">ยังไม่มีคำสั่งซื้อในระบบ</td></tr>`;
                    return;
                }

                ordersTableBody.innerHTML = orders.map(o => {
                    const statusClass = o.status === 'completed' ? 'var(--success)' : (o.status === 'pending' ? 'var(--primary)' : 'var(--text-muted)');
                    const statusLabel = o.status === 'completed' ? 'ชำระเงินแล้ว' : (o.status === 'pending' ? 'รอดำเนินการ' : 'หมดอายุ');

                    return `
                        <tr>
                            <td><strong style="font-family: monospace; letter-spacing: 0.5px;">${o.order_ref}</strong></td>
                            <td>${o.guest_email}</td>
                            <td><strong>${parseFloat(o.total_amount).toLocaleString()} THB</strong></td>
                            <td><span style="font-size: 0.8rem; color: var(--text-secondary); text-transform: uppercase;">${o.payment_method}</span></td>
                            <td>
                                <span style="display: inline-flex; align-items: center; gap: 0.35rem; font-weight: 700; font-size: 0.82rem; color: ${statusClass};">
                                    <span style="width: 6px; height: 6px; border-radius: 50%; background: ${statusClass}; display: inline-block;"></span>
                                    ${statusLabel}
                                </span>
                            </td>
                            <td><span style="font-size: 0.8rem; color: var(--text-muted);">${o.created_at}</span></td>
                            <td>
                                <button class="btn-track" onclick="window.cardVaultAdmin.viewOrderDetails('${o.order_ref}')" style="padding: 0.35rem 0.75rem; font-size: 0.78rem; border-radius: var(--radius-sm);">
                                    ดูข้อมูล
                                </button>
                            </td>
                        </tr>
                    `;
                }).join('');
            }
        } catch (err) {
            console.error('Load orders error:', err);
        }
    }

    // 9. View Order Details Modal
    window.cardVaultAdmin = {
        async viewOrderDetails(orderRef) {
            orderModalOverlay.classList.add('active');
            orderModalBody.innerHTML = `<p style="text-align: center; padding: 2rem; color: var(--text-muted);">กำลังโหลดข้อมูลคำสั่งซื้อ...</p>`;

            try {
                const res = await fetch(`api/admin.php?action=order_details&order_ref=${orderRef}`);
                const resData = await res.json();

                if (!resData.success) {
                    orderModalBody.innerHTML = `<p style="color: var(--danger); text-align: center;">${resData.error || 'ไม่พบคำสั่งซื้อ'}</p>`;
                    return;
                }

                const { order, items, vouchers } = resData.data;

                const itemsHtml = items.map(it => `
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.6rem 0; border-bottom: 1px solid var(--bg-subtle);">
                        <div>
                            <strong>${it.platform_name}</strong> - ${it.denomination_name}
                            <div style="font-size: 0.78rem; color: var(--text-muted);">จำนวน: ${it.quantity} x ${parseFloat(it.unit_price).toLocaleString()} THB</div>
                        </div>
                        <strong>${parseFloat(it.subtotal).toLocaleString()} THB</strong>
                    </div>
                `).join('');

                const vouchersHtml = vouchers.length === 0 
                    ? `<p style="color: var(--text-muted); font-size: 0.84rem;">ไม่มีรหัสบัตรที่จัดส่ง</p>`
                    : vouchers.map(v => `
                        <div style="background: var(--bg-subtle); padding: 0.6rem 0.85rem; border-radius: var(--radius-sm); margin-bottom: 0.45rem; font-family: monospace; display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-weight: 700; letter-spacing: 1px; color: var(--text-main);">${v.code}</span>
                            <span style="font-size: 0.75rem; color: var(--text-muted);">${v.serial || ''} (${v.status})</span>
                        </div>
                    `).join('');

                orderModalBody.innerHTML = `
                    <div style="margin-bottom: 1rem;">
                        <div style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 0.25rem;">หมายเลขคำสั่งซื้อ: <strong>${order.order_ref}</strong></div>
                        <div style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 0.25rem;">อีเมลผู้สั่งซื้อ: <strong>${order.guest_email}</strong></div>
                        <div style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 0.25rem;">สถานะ: <strong>${order.status}</strong></div>
                        <div style="font-size: 0.85rem; color: var(--text-secondary);">ยอดเงินรวม: <strong style="color: var(--primary); font-size: 1.1rem;">${parseFloat(order.total_amount).toLocaleString()} THB</strong></div>
                    </div>

                    <h4 style="font-size: 0.95rem; font-weight: 700; margin: 1.25rem 0 0.5rem; color: var(--text-main);">รายการสินค้า</h4>
                    ${itemsHtml}

                    <h4 style="font-size: 0.95rem; font-weight: 700; margin: 1.25rem 0 0.5rem; color: var(--text-main);">รหัสบัตรที่ถูกล็อก / จัดส่ง</h4>
                    ${vouchersHtml}
                `;
            } catch (err) {
                orderModalBody.innerHTML = `<p style="color: var(--danger); text-align: center;">เกิดข้อผิดพลาดในการโหลดข้อมูล</p>`;
            }
        }
    };

    btnCloseModal.addEventListener('click', () => {
        orderModalOverlay.classList.remove('active');
    });
    orderModalOverlay.addEventListener('click', (e) => {
        if (e.target === orderModalOverlay) {
            orderModalOverlay.classList.remove('active');
        }
    });
});
