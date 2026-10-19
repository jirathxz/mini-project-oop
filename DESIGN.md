# CARDVAULT Design System

## 1. Design Concept & Philosophy

- **Modern Minimalist UI**: เรียบง่าย ทันสมัย สบายตา และมีโครงสร้างที่สะอาดหมดจด
- **60-30-10 / 80-20 Rule**: คุมโทนสีพื้นหลังและโครงสร้างหลักเป็น White & Light Grey 80% และเน้นจุดนำสายตา (Primary Actions / Call-to-Action) ด้วยโทน Red-Rose 20%
- **Flat & Clean Precision**:
  - **No Border**: ไม่ใช้เส้นขอบ (Borders) อาศัยการสลับระดับสีพื้นผิว (Surface Contrast) เพื่อแยกสัดส่วน
  - **No Shadow**: ไม่ใช้เงาฟุ้ง (Drop Shadows) เพื่อให้ได้ความคมชัดแบบ Pure Flat Modern
  - **No Badge Clutter (อย่าใช้ No Badge เกลื่อน)**: ห้ามสร้างกล่อง Badge หรือแปะเม็ด Pill Chip กระจัดกระจายพร่ำเพรื่อบนหน้าจอ ห้ามสร้างป้ายสถานะหยุมหยิม ให้พื้นที่โปร่งสบายตา (Clean Whitespace) และอาศัยลำดับชั้นตัวอักษร (Typography Hierarchy)
  - **No Gradient**: ใช้สีเดี่ยว (Solid Flat Colors) 100% ไม่มีแถบไล่เฉดสี
  - **Real Authentic Brand Icons (ใช้ไอคอนจริงของแบรนด์ ไม่ใช่อีโมจิหรือเส้นสัญลักษณ์บิดเบี้ยว)**: สัญลักษณ์ของแพลตฟอร์มสินค้า (เช่น Netflix, Steam, Apple, Google Play) ต้องใช้ไอคอนเวกเตอร์ SVG จริงที่เป็นทางการ คมชัด ถูกต้องตามอัตลักษณ์ของแบรนด์
  - **Zero Architecture Leaks (ห้ามระบุสถาปัตยกรรมและเทคโนโลยีการพัฒนาเว็บในหน้าเว็บเด็ดขาด)**: หน้าเว็บของลูกค้าร้านค้าต้องไม่มีการเปิดเผยสถาปัตยกรรมระบบ, โครงสร้างข้อมูล, ดีไซน์แพทเทิร์น, หรือศัพท์เทคนิควิศวกรรมซอฟต์แวร์ใดๆ ทั้งสิ้น ข้อความทุกจุดต้องสื่อสารแบบ Consumer-First เป็นมิตรกับผู้ใช้งาน 100%
  - **Perfect Border Radius**: โค้งมนอย่างสมส่วน (Buttons & Inputs: `10px`, Cards: `16px`, Nav Tabs: `9999px`)
- **Language**: ภาษาไทยเป็นหลัก ชัดเจน ตรงไปตรงมา กระชับ และเป็นมิตรกับผู้ใช้งานทั่วไป

---

## 2. Color Palette Token

| Token | Value | สัดส่วน / หน้าที่ |
|---|---|---|
| `--bg-main` | `#f7f8fa` | พื้นหลังหลักทั้งหน้าเว็บ (Light Grey) |
| `--bg-surface` | `#ffffff` | การ์ดสินค้าและกล่องข้อมูล (Pure White) |
| `--bg-subtle` | `#f0f2f5` | พื้นหลังปุ่มรอง, ช่องค้นหา, แถบตัวเลือกย่อย |
| `--bg-active` | `#e4e7eb` | สถานะ Hover / Active ของกล่องตัวเลือก |
| `--primary` | `#e11d48` | สีหลัก **Red-Rose** (20% Accent: ปุ่มซื้อ, สัญลักษณ์สำคัญ) |
| `--primary-hover`| `#be123c` | สี Red-Rose เข้มขึ้นเมื่อเลื่อนเมาส์ผ่าน |
| `--primary-light`| `#fff1f2` | สีชมพูระเรื่อสำหรับแถบข้อความสำคัญ |
| `--text-primary` | `#0f172a` | ข้อความหลัก สีเทาดำเข้ม อ่านง่ายคมชัด |
| `--text-secondary`| `#475569`| ข้อความอธิบาย ข้อมูลประกอบ |
| `--text-muted` | `#94a3b8` | ข้อความสถานะรอง ตัวอักษรบอกหน่วยเงิน |
| `--success` | `#059669` | สีสถานะมีสินค้า (Emerald Green) |
| `--danger` | `#e11d48` | สีสถานะสินค้าหมด (Red-Rose) |

---

## 3. Border Radius System

- **Small (`8px` - `10px`)**: สำหรับปุ่มขนาดเล็ก, Dropdown, ช่อง Input
- **Medium (`16px`)**: สำหรับการ์ดสินค้า (`product-card`), กล่องสรุปคำสั่งซื้อ
- **Large (`24px`)**: สำหรับหน้าต่าง Modal, กล่อง Drawer ตะกร้าสินค้า
- **Full (`9999px`)**: สำหรับแท็บเลือกหมวดหมู่ (`nav-tab`), ช่องค้นหาหลัก

---

## 4. Typography & Visual Hierarchy

- ฟอนต์หลัก: `Inter`, `Prompt`, ระบบ System UI Font
- ขนาดหัวข้อหลัก: `1.8rem - 2.2rem` (น้ำหนัก 800)
- ขนาดหัวข้อการ์ดสินค้า: `1.15rem` (น้ำหนัก 700)
- ป้ายระบุภูมิภาคและรูปแบบจัดส่ง: แสดงเป็นข้อความเรียบหรู เช่น `ภูมิภาค: ไทย • จัดส่งรหัสทันที` แทนการใช้กล่อง Badge

---

## 5. Iconography System: No Emoji, Just Use Icon

### กฎข้อบังคับเรื่อง Iconography:
- **Zero Emoji Policy**: ห้ามใช้อีโมจิอักขระ Unicode (เช่น 🛒, 🔍, ⚡, 🔑, ⏱️, ✅, ❌, 📦, 💳, 📋) ในส่วนใดๆ ของ UI, ปุ่ม, ข้อความแจ้งเตือน, หัวข้อ หรือ Modal ทั้งสิ้น
- **Just Use Icon (Pure Vector SVG Only)**: ใช้เฉพาะไอคอนเวกเตอร์ SVG (Clean Line Icons) ที่มีความคมชัดสูง ไม่แตกตามขนาดหน้าจอ และมีสไตล์กลมกลืนกับมินิมอลดีไซน์
- **SVG Standards**:
  - `viewBox="0 0 24 24"`
  - `fill="none"`
  - `stroke="currentColor"`
  - `stroke-width="2"` หรือ `2.2`
  - `stroke-linecap="round"`
  - `stroke-linejoin="round"`
- **Color Inheritance**: ไอคอนต้องใช้สีตามโทนที่กำหนดผ่าน `currentColor` โดยเชื่อมโยงกับ Color Tokens เช่น `--text-primary`, `--text-secondary`, หรือ `--primary`
- **Sizing Tokens**:
  - Inline / Text Icon: `16px` × `16px`
  - Button / Action Icon: `18px` × `18px` ถึง `20px` × `20px`
  - Modal / Hero Icon: `24px` × `24px` ถึง `32px` × `32px`

### ตารางเปรียบเทียบมาตรฐานไอคอน (Prohibited vs Standard):

| วัตถุประสงค์ / จุดใช้งาน | ห้ามใช้ (Emoji) | ให้ใช้ไอคอน SVG เท่านั้น (Icon) | สเปก / ฟิลด์ |
|---|---|---|---|
| ตะกร้าสินค้า (Cart Drawer) | ❌ 🛒 | ✅ SVG Shopping Cart | `stroke="currentColor" 20px` |
| ช่องค้นหา (Search Bar) | ❌ 🔍 | ✅ SVG Magnifying Glass | `stroke="currentColor" 18px` |
| รหัสสินค้า / Digital Key | ❌ 🔑 | ✅ SVG Key | `stroke="currentColor" 16px` |
| เวลานับถอยหลัง / หมดอายุ | ❌ ⏱️ / ⏳ | ✅ SVG Clock | `stroke="currentColor" 16px` |
| คัดลอกรหัส (Copy PIN) | ❌ 📋 / 📑 | ✅ SVG Copy / Clipboard | `stroke="currentColor" 16px` |
| สถานะสำเร็จ (Success) | ❌ ✅ | ✅ SVG Checkmark Circle | `stroke="var(--success)" 20px` |
| ปิดหน้าต่าง / ผิดพลาด | ❌ ❌ | ✅ SVG Cross / X | `stroke="currentColor" 18px` |
| ระบบความปลอดภัย / รับประกัน | ❌ 🛡️ | ✅ SVG Shield Check | `stroke="var(--primary)" 20px` |
| ตรวจสอบคำสั่งซื้อ / กล่องข้อความ | ❌ 📥 / ✉️ | ✅ SVG Mail / Inbox | `stroke="currentColor" 20px` |
| การจัดส่งรวดเร็ว | ❌ ⚡ | ✅ SVG Zap / Bolt | `stroke="var(--primary)" 16px` |

---

## 6. ReactBits Micro-Interactions System (Modern Motion Architecture)

เพื่อยกระดับความพรีเมียมของหน้าเว็บโดยไม่ทำลายกฎ **No Border, No Shadow, No Gradient** ระบบได้แปลงคอมโพเนนต์ชั้นนำจาก **ReactBits** มาเป็น Vanilla JS & CSS 100% ดังนี้:

| องค์ประกอบจาก ReactBits | บทบาทในหน้าร้าน CARDVAULT | การทำงานทางเทคนิค (Vanilla Implementation) |
|---|---|---|
| **DotGrid (Backgrounds)** | ลวดลายจุด Geometric Matrix บน Hero Banner | CSS `radial-gradient(rgba(15, 23, 42, 0.065) 1.5px, transparent 1.5px)` ขนาด `24px 24px` ให้มิติที่คมชัด |
| **SpotlightCard (Components)** | แสงเรืองวงกลมที่เคลื่อนที่ตามเคอร์เซอร์เมาส์บนการ์ดสินค้า | คำนวณพิกัด `(x, y)` บน `.product-card` แบบเรียลไทม์ และฉายแสง `radial-gradient` สี Red-Rose โปร่งใส 7.5% บน `::before` |
| **ClickSpark (Animations)** | ประกายเส้นเรขาคณิตกระจายตัวเมื่อคลิกปุ่ม Action | Canvas 2D Overlay ระดับ 60fps สร้างสะเก็ดเส้น 8 แฉกสี Rose เมื่อคลิกปุ่มซื้อ, ปุ่มคัดลอก, ตะกร้า และแท็บเมนู |
| **CountUp (TextAnimations)** | ตัวเลขราคาสินค้าและยอดรวมในตะกร้าเลื่อนปรับค่าอย่างนุ่มนวล | อนิเมชันตัวเลขอิง `requestAnimationFrame` ร่วมกับ `expo easeOut` อัปเดตราคาแพ็กเกจและยอดเงินอย่างต่อเนื่อง |
| **BlurText / FadeContent (Animations)** | หัวข้อและสินค้าทยอยเบลอปรับเข้าสู่ความคมชัดเมื่อเปิดหน้า | CSS `@keyframes blurIn` และ `cardStaggerIn` สลับดีเลย์ตามลำดับดัชนีการ์ด (`--card-index`) |
| **Magnet (Components)** | ปุ่มดึงดูดเคอร์เซอร์เมื่อเลื่อนเมาส์เข้าใกล้ | คำนวณระยะห่างระหว่างจุดกึ่งกลางปุ่มกับเคอร์เซอร์เพื่อขยับตำแหน่งปุ่มเล็กน้อย (`translate`) ก่อนดีดตัวกลับ |
| **AnimatedList (Components)** | รายการสินค้าในตะกร้า Slide-in อย่างเป็นระเบียบ | CSS `@keyframes itemSlideIn` ให้ความรู้สึกต่อเนื่องและมีชีวิตชีวาเมื่อเพิ่มหรือลบสินค้า |

---

## 7. Zero Tech Architecture on Public UI (กฎเด็ดขาด: ห้ามบอกสถาปัตยกรรมและเทคโนโลยีบนหน้าเว็บ)

ลูกค้าทั่วไปที่เข้ามาซื้อสินค้าไม่จำเป็นต้องรับรู้และ**ต้องไม่เห็นคำศัพท์ทางเทคนิค สถาปัตยกรรมระบบ หรือชื่อเฟรมเวิร์ก/เครื่องมือพัฒนาบนหน้าเว็บเด็ดขาด**:

| ❌ สิ่งที่ห้ามแสดงบนหน้าเว็บเด็ดขาด (Technical Jargon) | ✅ สิ่งที่ให้แสดงแทน (Consumer-First Copy) | เหตุผล |
|---|---|---|
| `Dual-Key Single Track`, `Dual-Key` | `ค้นหาด้วยหมายเลขคำสั่งซื้อ` | Dual-Key เป็นชื่อสถาปัตยกรรมความปลอดภัย ลูกค้าต้องการแค่ค้นหาด้วยเลขออเดอร์ + อีเมล |
| `OTP Inbox` | `ดูประวัติคำสั่งซื้อทั้งหมด` | ลูกค้าต้องการดูประวัติ ไม่ใช่ชื่อกลไกการยืนยันตัวตน |
| `Guest Checkout`, `Guest Session` | `สั่งซื้อได้ทันที ไม่ต้องมีบัญชี` | ลูกค้าเข้าใจคำว่าไม่ต้องสมัครสมาชิกได้ชัดเจนกว่าคำว่า Guest |
| `SQLite Database`, `DDL Schema`, `Entity` | (ห้ามกล่าวถึงบน UI) | โครงสร้าง Database เบื้องหลังเป็นเรื่องภายในระบบ |
| `ReactBits`, `Vanilla JS`, `PHP Built-in Server` | (ห้ามกล่าวถึงบน UI) | เทคโนโลยีการสร้างเว็บต้องไม่ปรากฏเป็นข้อความหรือคำโฆษณา |
| `Single-Merchant Engine`, `Tracer-Bullet` | `ระบบจัดส่งอัตโนมัติ 24 ชั่วโมง` | ใช้ภาษาธุรกิจที่สร้างความเชื่อมั่นแทนชื่อโค้ดเนมสถาปัตยกรรม |

---

## 8. No Badge Clutter Principle (อย่าใช้ No Badge เกลื่อน)

การออกแบบต้องมีระดับความสำคัญทางสายตา (Visual Hierarchy) ไม่ใช่การนำทุกข้อความมาใส่กรอบมนกลายเป็นป้าย Pill Chip หรือ Badge ลอยเกลื่อนหน้าจอ:

1. **ห้ามทำกล่อง Pill ครอบทุกข้อความ**:
   - ข้อความจุดเด่น (Features): จัดแสดงเป็นข้อความสะอาดคู่กับไอคอน SVG (เช่น `<svg>...</svg> จัดส่งรหัสทันที`) ไม่ต้องใส่พื้นหลังแคปซูล (`.feature-pill`)
   - ป้ายระบุประเทศและรูปแบบจัดส่งในการ์ด: รวมอยู่ในบรรทัดย่อยใต้ชื่อแบรนด์อย่างกลมกลืน เช่น `ประเทศไทย (TH) • จัดส่งทันที` ไม่ต้องทำ Badge สองก้อนลอยตรงมุม
2. **จุดบอกสต็อก (Stock Status)**:
   - ใช้เพียงจุดสี (`.stock-dot`) เขียว/เทา คู่กับข้อความธรรมดา เช่น `มีสินค้า (5 ชิ้น)` ไม่สร้างกรอบ Badge ครอบให้รกสายตา
3. **รักษาความโล่งโปร่ง (Whitespace Elegance)**:
   - ปล่อยให้พื้นผิวการ์ดและพื้นที่ว่างทำหน้าที่แยกส่วนประกอบ แทนการพึ่งพากล่อง Badge เล็กๆ นับสิบชิ้นบนหน้าจอ


