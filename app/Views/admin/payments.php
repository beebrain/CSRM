<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
  <title>ตรวจสอบสลิปชำระเงิน - CSRM</title>
  <link rel="stylesheet" href="<?= base_url('css/style.css'); ?>">
</head>
<body>

  <!-- Navigation -->
  <nav class="navbar animate-fade-in">
    <div class="navbar-brand">
      🎓 CSRM Admin (ประจำปี)
    </div>
    <div class="navbar-menu">
      <select onchange="window.location.href='<?= base_url('admin/selectConference'); ?>/'+this.value" class="form-control text-sm" style="padding: 0.25rem 0.5rem; width: auto; background: rgba(255, 255, 255, 0.08); border: 1px solid var(--card-border);">
        <?php foreach ($allowedConfs as $conf): ?>
          <option value="<?= $conf['id'] ?>" <?= $conf['id'] == $currentConfId ? 'selected' : '' ?>>ปี พ.ศ. <?= esc($conf['year']) ?></option>
        <?php endforeach; ?>
      </select>
      <a href="<?= base_url('admin/dashboard'); ?>" class="navbar-item">บทความ</a>
      <a href="<?= base_url('admin/disciplines'); ?>" class="navbar-item">ศาสตร์ย่อย</a>
      <a href="<?= base_url('admin/criteria'); ?>" class="navbar-item">เกณฑ์ประเมิน</a>
      <a href="<?= base_url('admin/rooms'); ?>" class="navbar-item">จัดห้องพรีเซนต์</a>
      <a href="<?= base_url('admin/payments'); ?>" class="navbar-item active">ยืนยันเงิน</a>
      <a href="<?= base_url('admin/reports'); ?>" class="navbar-item">รายงานผล</a>
      <a href="<?= base_url('admin/pending-reviews'); ?>" class="navbar-item">ติดตามผู้ทรงฯ</a>
      <?php if (session()->get('role') === 'superadmin'): ?>
        <a href="<?= base_url('superadmin/dashboard'); ?>" class="btn btn-primary btn-sm">⚙️ กลับหน้า SuperAdmin</a>
      <?php endif; ?>
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
      <h2 class="text-2xl">💰 รายการชำระเงินและตรวจสอบหลักฐาน (เดี่ยว/กลุ่ม)</h2>
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
                      <span class="badge badge-info text-xs">โอนเงินแนบสลิป</span>
                    <?php else: ?>
                      <span class="badge badge-success text-xs" style="background: rgba(16, 185, 129, 0.2);">Gateway ออนไลน์</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <strong class="text-lg" style="color: var(--primary);"><?= number_format($pay['amount'], 2) ?></strong> บาท
                  </td>
                  <td>
                    <ul class="text-sm" style="padding-left: 1rem;">
                      <?php foreach ($paymentPapers[$pay['id']] as $p): ?>
                        <li class="mb-1">
                          <strong>[ID: <?= $p['id'] ?>]</strong> <?= esc($p['title']) ?> 
                          <div class="text-muted text-xs"><?= esc($p['discipline_name']) ?></div>
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
                      <div class="text-xs">Ref: <code><?= esc($pay['transaction_reference']) ?></code></div>
                    <?php endif; ?>
                  </td>
                  <td>
                    <?php if ($pay['status'] === 'approved'): ?>
                      <span class="badge badge-success text-xs">อนุมัติแล้ว</span>
                    <?php elseif ($pay['status'] === 'rejected'): ?>
                      <span class="badge badge-danger text-xs">ปฏิเสธแล้ว</span>
                    <?php else: ?>
                      <span class="badge badge-pending text-xs">รอตรวจสอบ</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <?php if ($pay['status'] === 'pending'): ?>
                      <div class="flex gap-1">
                        <a href="<?= base_url('admin/approvePayment/' . $pay['id']) ?>" class="btn btn-success btn-sm text-xs" onclick="return confirm('ยืนยันอนุมัติสลิปนี้? ทุกบทความในรายการนี้จะเปลี่ยนสถานะเป็นชำระเงินสำเร็จ')" style="padding: 0.25rem 0.5rem; white-space: nowrap;">อนุมัติ</a>
                        <a href="<?= base_url('admin/rejectPayment/' . $pay['id']) ?>" class="btn btn-danger btn-sm text-xs" onclick="return confirm('ยืนยันปฏิเสธสลิปนี้?')" style="padding: 0.25rem 0.5rem; white-space: nowrap;">ปฏิเสธ</a>
                      </div>
                    <?php else: ?>
                      <span class="text-muted text-xs">เสร็จสิ้น</span>
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
