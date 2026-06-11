<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reviewer Dashboard - CSRM</title>
  <link rel="stylesheet" href="<?= base_url('css/style.css'); ?>">
</head>
<body>

  <!-- Navigation -->
  <nav class="navbar animate-fade-in">
    <div class="navbar-brand">
      🔍 CSRM Reviewer Dashboard
    </div>
    <div class="navbar-menu">
      <span class="text-muted">ผู้ทรงคุณวุฒิ: <strong><?= session()->get('first_name') ?></strong></span>
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

    <!-- Assigned Papers -->
    <div class="card">
      <h2>📑 รายการบทความวิชาการที่ได้รับมอบหมาย</h2>
      <p class="text-muted mb-2">กรุณาอ่านและให้คะแนนบทความวิชาการตามหัวข้อเกณฑ์ประเมินที่กำหนด</p>

      <div class="table-responsive">
        <table class="table">
          <thead>
            <tr>
              <th>ไอดี</th>
              <th>ชื่อบทความวิชาการ</th>
              <th>ประเภท / ศาสตร์</th>
              <th>ปีการจัดงาน</th>
              <th>สถานะประเมินของฉัน</th>
              <th>จัดการ</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($reviews)): ?>
              <tr>
                <td colspan="6" class="text-center text-muted" style="padding: 3rem 0;">ไม่มีบทความวิชาการที่ต้องประเมินในขณะนี้</td>
              </tr>
            <?php else: ?>
              <?php foreach ($reviews as $rev): ?>
                <tr>
                  <td><strong>#<?= $rev['paper_id'] ?></strong></td>
                  <td style="max-width: 350px;">
                    <div style="font-weight: 600;"><?= esc($rev['title']) ?></div>
                    <div class="mt-1">
                      <a href="<?= base_url($rev['file_path']) ?>" target="_blank" class="btn btn-secondary btn-sm" style="padding: 0.1rem 0.5rem; font-size: 0.75rem;">📖 เปิดอ่าน PDF</a>
                    </div>
                  </td>
                  <td>
                    <div><?= esc($rev['track_name']) ?></div>
                    <small class="text-muted"><?= esc($rev['discipline_name']) ?></small>
                  </td>
                  <td><strong>พ.ศ. <?= esc($rev['conference_year']) ?></strong></td>
                  <td>
                    <?php if ($rev['status'] === 'completed'): ?>
                      <span class="badge badge-success">ประเมินเสร็จแล้ว</span>
                      <?php if ($rev['decision'] === 'pass'): ?>
                        <span class="badge badge-success" style="padding: 0.1rem 0.3rem; font-size: 0.65rem;">(โหวตผ่าน)</span>
                      <?php else: ?>
                        <span class="badge badge-danger" style="padding: 0.1rem 0.3rem; font-size: 0.65rem;">(โหวตไม่ผ่าน)</span>
                      <?php endif; ?>
                    <?php else: ?>
                      <span class="badge badge-pending">รอดำเนินการ</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <?php if ($rev['accept_evaluations']): ?>
                      <a href="<?= base_url('reviewer/evaluate/' . $rev['id']) ?>" class="btn btn-primary btn-sm" style="font-size: 0.8rem; padding: 0.3rem 0.6rem;">
                        <?= $rev['status'] === 'completed' ? '📝 แก้ไขผลประเมิน' : '✍️ เริ่มทำประเมิน' ?>
                      </a>
                    <?php else: ?>
                      <span class="text-muted" style="font-size: 0.8rem;">ปิดรับผลประเมิน</span>
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
