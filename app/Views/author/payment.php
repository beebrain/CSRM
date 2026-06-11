<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ชำระเงินค่าลงทะเบียน - CSRM</title>
  <link rel="stylesheet" href="<?= base_url('css/style.css'); ?>">
  <style>
    .payment-tab-buttons {
      display: flex;
      gap: 1rem;
      margin-bottom: 1.5rem;
    }
    .payment-tab-btn {
      flex: 1;
      padding: 1rem;
      background: rgba(255, 255, 255, 0.05);
      border: 1px solid var(--card-border);
      border-radius: var(--radius-sm);
      color: var(--text-secondary);
      font-weight: 600;
      text-align: center;
      cursor: pointer;
      transition: var(--transition);
    }
    .payment-tab-btn.active {
      background: rgba(99, 102, 241, 0.15);
      border-color: var(--primary);
      color: var(--text-primary);
      box-shadow: 0 0 15px rgba(99, 102, 241, 0.2);
    }
    .payment-pane {
      display: none;
    }
    .payment-pane.active {
      display: block;
      animation: fadeIn 0.4s ease;
    }
    .total-box {
      background: rgba(99, 102, 241, 0.05);
      border: 1px solid var(--card-border);
      padding: 1.5rem;
      border-radius: var(--radius-md);
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 2rem;
    }
    .credit-card-mock {
      background: linear-gradient(135deg, #1e1b4b 0%, #311042 100%);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 12px;
      padding: 1.5rem;
      max-width: 380px;
      margin: 0 auto 1.5rem auto;
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.4);
    }
  </style>
</head>
<body>

  <!-- Navigation -->
  <nav class="navbar animate-fade-in">
    <div class="navbar-brand">
      🚀 CSRM Author Dashboard
    </div>
    <div class="navbar-menu">
      <span class="text-muted">ผู้แต่ง: <strong><?= session()->get('first_name') ?></strong></span>
      <a href="<?= base_url('author/dashboard'); ?>" class="navbar-item">บทความของฉัน</a>
      <a href="<?= base_url('author/payment'); ?>" class="navbar-item active">ชำระเงินค่าลงทะเบียน</a>
      <a href="<?= base_url('auth/logout'); ?>" class="btn btn-secondary btn-sm">ออกจากระบบ</a>
    </div>
  </nav>

  <div class="container animate-fade-in" style="animation-delay: 0.1s;">
    
    <!-- Alerts -->
    <?php if (session()->getFlashdata('success')): ?>
      <div class="alert alert-success"><?= session()->getFlashdata('success'); ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
      <div class="alert alert-danger"><?= session()->getFlashdata('error'); ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('errors')): ?>
      <div class="alert alert-danger">
        <ul style="padding-left: 1rem; margin: 0;">
          <?php foreach (session()->getFlashdata('errors') as $err): ?>
            <li><?= esc($err) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <h2>ชำระเงินค่าลงทะเบียนเข้าร่วมนำเสนอผลงาน</h2>
    <p class="text-muted mb-3">กรุณาเลือกบทความที่คุณต้องการชำระเงิน (สามารถเลือกชำระรวมกันหลายบทความได้แบบกลุ่ม)</p>

    <?php if (empty($unpaidPapers)): ?>
      <div class="card text-center text-muted" style="padding: 4rem 2rem;">
        <h3>🎉 คุณไม่มีบทความที่ต้องค้างชำระเงินในรอบปีนี้</h3>
        <p class="mt-1">บทความทั้งหมดของคุณได้รับการชำระเงินหรืออยู่ระหว่างการตรวจสอบหลักฐานเรียบร้อยแล้ว</p>
        <a href="<?= base_url('author/dashboard'); ?>" class="btn btn-primary mt-2">กลับไปหน้า Dashboard</a>
      </div>
    <?php else: ?>
      
      <!-- Checkout Wrapper Form (Handles multi-check values and redirects to proper submission endpoint via JS or separate forms) -->
      <div class="grid-3" style="grid-template-columns: 1.5fr 1fr;">
        
        <!-- Left Column: Paper Selection -->
        <div class="card">
          <h3>1. เลือกบทความที่ต้องการชำระเงิน</h3>
          <p class="text-muted mb-2">ค่าธรรมเนียมบทความละ 1,000.00 บาท</p>

          <table class="table">
            <thead>
              <tr>
                <th width="50">เลือก</th>
                <th>ไอดีบทความ</th>
                <th>ชื่อบทความวิชาการ</th>
                <th>ประเภท / ศาสตร์</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($unpaidPapers as $paper): ?>
                <tr>
                  <td>
                    <input type="checkbox" name="paper_selection" value="<?= $paper['id'] ?>" class="paper-checkbox" checked onclick="calculateTotal()">
                  </td>
                  <td><strong>#<?= $paper['id'] ?></strong></td>
                  <td><?= esc($paper['title']) ?></td>
                  <td>
                    <div><?= esc($paper['track_name']) ?></div>
                    <small class="text-muted"><?= esc($paper['discipline_name']) ?></small>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

        <!-- Right Column: Total & Payment Methods -->
        <div>
          <!-- Total Price box -->
          <div class="total-box">
            <div>
              <div class="text-muted" style="font-size: 0.9rem;">ยอดชำระเงินสุทธิ</div>
              <small class="text-muted" id="paper-count-label">จำนวน 0 บทความ</small>
            </div>
            <div style="text-align: right;">
              <span id="total-price" style="font-size: 1.75rem; font-weight: 700; color: var(--primary);">0.00</span>
              <span style="font-weight: 600;"> บาท</span>
            </div>
          </div>

          <!-- Payment Tabs -->
          <div class="payment-tab-buttons">
            <div class="payment-tab-btn active" onclick="switchTab('bank')">🏦 โอนเงินผ่านธนาคาร</div>
            <div class="payment-tab-btn" onclick="switchTab('gateway')">💳 ชำระเงินออนไลน์</div>
          </div>

          <!-- Pane 1: Bank Transfer Form -->
          <div class="card payment-pane active" id="pane-bank">
            <h3>🏦 โอนเงินผ่านธนาคาร (แนบสลิป)</h3>
            <p class="text-muted mb-2" style="font-size: 0.85rem;">กรุณาโอนเงินตามยอดที่ระบุ และอัปโหลดไฟล์หลักฐาน (สลิปโอนเงิน)</p>

            <div style="background: rgba(255, 255, 255, 0.03); border: 1px solid var(--card-border); border-radius: var(--radius-sm); padding: 1rem; margin-bottom: 1.5rem; font-size: 0.9rem;">
              <div class="mb-1">ธนาคาร: <strong>ไทยพาณิชย์ (SCB)</strong></div>
              <div class="mb-1">เลขบัญชี: <strong>123-4-56789-0</strong></div>
              <div>ชื่อบัญชี: <strong>ระบบจัดการประชุมวิชาการ CSRM</strong></div>
            </div>

            <form action="<?= base_url('author/payBank'); ?>" method="POST" enctype="multipart/form-data" id="bank-form" onsubmit="prepareForm('bank-form')">
              <?= csrf_field(); ?>
              <!-- Hidden inputs for selected papers will be generated here -->
              <div class="hidden-inputs-container"></div>
              
              <div class="form-group">
                <label class="form-label" for="slip_file">อัปโหลดสลิปโอนเงิน (ไฟล์รูปภาพ)</label>
                <input type="file" id="slip_file" name="slip_file" class="form-control" accept="image/*" required>
                <small class="text-muted">ยอมรับไฟล์ JPG, PNG สูงสุด 5MB</small>
              </div>

              <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">ยืนยันการแนบหลักฐาน</button>
            </form>
          </div>

          <!-- Pane 2: Online Gateway Form -->
          <div class="card payment-pane" id="pane-gateway">
            <h3>💳 ชำระผ่านบัตรเครดิต (จำลอง)</h3>
            <p class="text-muted mb-3" style="font-size: 0.85rem;">ชำระเงินออนไลน์ตัดผ่านระบบ Gateway ทันทีโดยไม่ต้องรอแอดมินยืนยัน</p>

            <!-- Mock Card UI -->
            <div class="credit-card-mock">
              <div class="flex justify-between align-center mb-3">
                <span style="font-weight: 700; font-size: 1.1rem; letter-spacing: 2px;">SECURE PAY</span>
                <span style="font-size: 1.25rem;">🌐</span>
              </div>
              <div style="font-size: 1.25rem; font-weight: 600; letter-spacing: 3px; margin-bottom: 1.5rem;">•••• •••• •••• ••••</div>
              <div class="flex justify-between" style="font-size: 0.8rem; color: rgba(255,255,255,0.6);">
                <div>
                  <div>CARDHOLDER</div>
                  <div style="color: #fff; font-weight: 600; font-size: 0.9rem;">MOCK ACCOUNT</div>
                </div>
                <div>
                  <div>EXPIRES</div>
                  <div style="color: #fff; font-weight: 600; font-size: 0.9rem;">12/30</div>
                </div>
              </div>
            </div>

            <form action="<?= base_url('author/payGateway'); ?>" method="POST" id="gateway-form" onsubmit="prepareForm('gateway-form')">
              <?= csrf_field(); ?>
              <!-- Hidden inputs for selected papers will be generated here -->
              <div class="hidden-inputs-container"></div>

              <div class="form-group">
                <label class="form-label" for="card_num">หมายเลขบัตรเครดิต</label>
                <input type="text" id="card_num" class="form-control" placeholder="4111 2222 3333 4444" required>
              </div>

              <div class="grid-2">
                <div class="form-group">
                  <label class="form-label" for="card_exp">วันหมดอายุ (MM/YY)</label>
                  <input type="text" id="card_exp" class="form-control" placeholder="12/30" required>
                </div>
                <div class="form-group">
                  <label class="form-label" for="card_cvv">CVV</label>
                  <input type="password" id="card_cvv" class="form-control" placeholder="•••" maxlength="3" required>
                </div>
              </div>

              <button type="submit" class="btn btn-success" style="width: 100%; margin-top: 1rem; background: var(--success);">💳 ชำระเงินสุทธิทันที</button>
            </form>
          </div>
        </div>

      </div>

    <?php endif; ?>

  </div>

  <script>
    function switchTab(mode) {
      document.querySelectorAll('.payment-tab-btn').forEach(btn => btn.classList.remove('active'));
      document.querySelectorAll('.payment-pane').forEach(pane => pane.classList.remove('active'));

      if (mode === 'bank') {
        document.querySelectorAll('.payment-tab-btn')[0].classList.add('active');
        document.getElementById('pane-bank').classList.add('active');
      } else {
        document.querySelectorAll('.payment-tab-btn')[1].classList.add('active');
        document.getElementById('pane-gateway').classList.add('active');
      }
    }

    function calculateTotal() {
      const checkboxes = document.querySelectorAll('.paper-checkbox:checked');
      const count = checkboxes.length;
      const total = count * 1000;
      
      document.getElementById('paper-count-label').innerText = `จำนวน ${count} บทความ`;
      document.getElementById('total-price').innerText = total.toLocaleString('th-TH', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }

    function prepareForm(formId) {
      const form = document.getElementById(formId);
      const container = form.querySelector('.hidden-inputs-container');
      container.innerHTML = ''; // clear

      const checkedPapers = document.querySelectorAll('.paper-checkbox:checked');
      if (checkedPapers.length === 0) {
        alert('กรุณาเลือกบทความอย่างน้อย 1 รายการก่อนทำการชำระเงิน');
        event.preventDefault();
        return false;
      }

      checkedPapers.forEach(cb => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'paper_ids[]';
        input.value = cb.value;
        container.appendChild(input);
      });
    }

    // Run on startup
    window.onload = function() {
      if (document.querySelector('.paper-checkbox')) {
        calculateTotal();
      }
    }
  </script>

</body>
</html>
