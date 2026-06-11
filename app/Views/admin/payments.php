<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ตรวจสอบสลิปชำระเงิน - CSRM</title>
  <link rel="stylesheet" href="<?= base_url('css/style.css'); ?>">
  <style>
    .slip-thumbnail {
      width: 80px;
      height: 80px;
      object-fit: cover;
      border-radius: var(--radius-sm);
      border: 1px solid var(--card-border);
      cursor: pointer;
      transition: var(--transition);
    }
    .slip-thumbnail:hover {
      transform: scale(1.1);
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
    }
  </style>
</head>
<body>

  <!-- Navigation -->
  <nav class="navbar animate-fade-in">
    <div class="navbar-brand">
      🎓 CSRM Admin (ประจำปี)
    </div>
    <div class="navbar-menu">
      <select onchange="window.location.href='<?= base_url('admin/selectConference'); ?>/'+this.value" class="form-control" style="padding: 0.25rem 0.5rem; font-size: 0.85rem; width: auto; background: rgba(255, 255, 255, 0.08); border: 1px solid var(--card-border);">
        <?php foreach ($allowedConfs as $conf): ?>
          <option value="<?= $conf['id'] ?>" <?= $conf['id'] == $currentConfId ? 'selected' : '' ?>>ปี พ.ศ. <?= esc($conf['year']) ?></option>
        <?php endforeach; ?>
      </select>
      <a href="<?= base_url('admin/dashboard'); ?>" class="navbar-item">บทความ</a>
      <a href="<?= base_url('admin/disciplines'); ?>" class="navbar-item">ศาสตร์ย่อย</a>
      <a href="<?= base_url('admin/criteria'); ?>" class="navbar-item">เกณฑ์ประเมิน</a>
      <a href="<?= base_url('admin/rooms'); ?>" class="navbar-item">จัดห้องพรีเซนต์</a>
      <a href="<?= base_url('admin/payments'); ?>" class="navbar-item active">ยืนยันเงิน</a>
      <a href="<?= base_url('auth/logout'); ?>" class="btn btn-secondary btn-sm">ออกระบบ</a>
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

    <!-- Payments List -->
    <div class="card">
      <h2>💰 รายการชำระเงินและตรวจสอบหลักฐาน (เดี่ยว/กลุ่ม)</h2>
      <p class="text-muted mb-2">ตรวจสอบหลักฐานการชำระเงิน และการสแกนจ่ายผ่านระบบ Gateway</p>

      <div class="table-responsive">
        <table class="table">
          <thead>
            <tr>
              <th>ผู้ชำระเงิน</th>
              <th>ประเภทชำระ</th>
              <th>จำนวนเงิน</th>
              <th>บทความที่ชำระ (กลุ่ม/เดี่ยว)</th>
              <th>หลักฐาน (สลิป/เลขอ้างอิง)</th>
              <th>สถานะ</th>
              <th>จัดการ</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($payments)): ?>
              <tr>
                <td colspan="7" class="text-center text-muted">ยังไม่มีรายการชำระเงินในรอบปีนี้</td>
              </tr>
            <?php else: ?>
              <?php foreach ($payments as $pay): ?>
                <tr>
                  <td>
                    <div><?= esc($pay['first_name']) ?> <?= esc($pay['last_name']) ?></div>
                    <small class="text-muted"><?= esc($pay['email']) ?></small>
                  </td>
                  <td>
                    <?php if ($pay['payment_method'] === 'bank_transfer'): ?>
                      <span class="badge badge-info" style="font-size: 0.7rem;">โอนเงินแนบสลิป</span>
                    <?php else: ?>
                      <span class="badge badge-success" style="font-size: 0.7rem; background: rgba(16, 185, 129, 0.2);">Gateway ออนไลน์</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <strong style="color: var(--primary); font-size: 1.1rem;"><?= number_format($pay['amount'], 2) ?></strong> บาท
                  </td>
                  <td>
                    <ul style="padding-left: 1rem; font-size: 0.85rem;">
                      <?php foreach ($paymentPapers[$pay['id']] as $p): ?>
                        <li class="mb-1">
                          <strong>[ID: <?= $p['id'] ?>]</strong> <?= esc($p['title']) ?> 
                          <div class="text-muted" style="font-size: 0.7rem;"><?= esc($p['discipline_name']) ?></div>
                        </li>
                      <?php endforeach; ?>
                    </ul>
                  </td>
                  <td>
                    <?php if ($pay['payment_method'] === 'bank_transfer'): ?>
                      <?php if ($pay['slip_path']): ?>
                        <a href="<?= base_url($pay['slip_path']) ?>" target="_blank">
                          <img src="<?= base_url($pay['slip_path']) ?>" alt="Slip" class="slip-thumbnail">
                        </a>
                      <?php else: ?>
                        <span class="text-muted">ไม่พบไฟล์สลิป</span>
                      <?php endif; ?>
                    <?php else: ?>
                      <div style="font-size: 0.8rem;">Ref: <code><?= esc($pay['transaction_reference']) ?></code></div>
                    <?php endif; ?>
                  </td>
                  <td>
                    <?php if ($pay['status'] === 'approved'): ?>
                      <span class="badge badge-success">อนุมัติแล้ว</span>
                    <?php elseif ($pay['status'] === 'rejected'): ?>
                      <span class="badge badge-danger">ปฏิเสธแล้ว</span>
                    <?php else: ?>
                      <span class="badge badge-pending">รอตรวจสอบ</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <?php if ($pay['status'] === 'pending'): ?>
                      <div class="flex gap-1">
                        <a href="<?= base_url('admin/approvePayment/' . $pay['id']) ?>" class="btn btn-success btn-sm" onclick="return confirm('ยืนยันอนุมัติสลิปนี้? ทุกบทความในรายการนี้จะเปลี่ยนสถานะเป็นชำระเงินสำเร็จ')" style="font-size: 0.75rem; padding: 0.25rem 0.5rem; white-space: nowrap;">อนุมัติ</a>
                        <a href="<?= base_url('admin/rejectPayment/' . $pay['id']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('ยืนยันปฏิเสธสลิปนี้?')" style="font-size: 0.75rem; padding: 0.25rem 0.5rem; white-space: nowrap;">ปฏิเสธ</a>
                      </div>
                    <?php else: ?>
                      <span class="text-muted" style="font-size: 0.8rem;">เสร็จสิ้น</span>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

  </div>

</body>
</html>
