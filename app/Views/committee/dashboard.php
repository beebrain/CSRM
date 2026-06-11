<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Committee Dashboard - CSRM</title>
  <link rel="stylesheet" href="<?= base_url('css/style.css'); ?>">
  <style>
    .room-card {
      margin-bottom: 2.5rem;
      border-top: 4px solid var(--primary);
    }
  </style>
</head>
<body>

  <!-- Navigation -->
  <nav class="navbar animate-fade-in">
    <div class="navbar-brand">
      📢 CSRM Committee Dashboard
    </div>
    <div class="navbar-menu">
      <span class="text-muted">กรรมการ: <strong><?= session()->get('first_name') ?></strong></span>
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

    <h2>ห้องนำเสนอผลงานที่ได้รับมอบหมาย</h2>
    <p class="text-muted mb-3">ตรวจสอบกำหนดเวลา และประเมินทักษะการนำเสนอในวันนำเสนอจริง</p>

    <?php if (empty($rooms)): ?>
      <div class="card text-center text-muted" style="padding: 4rem 2rem;">
        <h3>ยังไม่มีการมอบหมายคุณให้ดูแลห้องนำเสนอใดๆ ในขณะนี้</h3>
        <p class="mt-1">โปรดติดต่อผู้ดูแลระบบเพื่อจัดสรรสิทธิ์คณะกรรมการประจำห้อง</p>
      </div>
    <?php else: ?>
      <?php foreach ($rooms as $room): ?>
        <div class="card room-card animate-fade-in">
          <div class="flex justify-between align-center mb-2" style="border-bottom: 1px dashed var(--card-border); padding-bottom: 1rem;">
            <div>
              <h2 style="color: var(--primary);"><?= esc($room['name']) ?></h2>
              <div class="text-muted mt-1">
                สถานที่: <strong><?= esc($room['location']) ?></strong> | 
                เวลาเริ่มห้อง: <?= esc($room['date_time']) ?> | 
                งานรอบปี: พ.ศ. <?= esc($room['conference_year']) ?> (เจ้าภาพ: <?= esc($room['host_name']) ?>)
              </div>
            </div>
            <span class="badge badge-success">ประธานกรรมการร่วม</span>
          </div>

          <h3>ตารางเวลาการเสนอผลงาน</h3>
          <div class="table-responsive mt-1">
            <table class="table">
              <thead>
                <tr>
                  <th width="100">เวลานำเสนอ</th>
                  <th>ไอดี</th>
                  <th>หัวข้อบทความวิชาการ</th>
                  <th>ประเภท / ศาสตร์ย่อย</th>
                  <th>สถานะประเมินของท่าน</th>
                  <th>จัดการ</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($roomPapers[$room['id']])): ?>
                  <tr>
                    <td colspan="6" class="text-center text-muted">ยังไม่มีการจัดตารางบทความวิชาการเข้ามาในห้องนี้</td>
                  </tr>
                <?php else: ?>
                  <?php foreach ($roomPapers[$room['id']] as $paper): ?>
                    <tr>
                      <td><strong style="color: var(--info); font-size: 1.05rem;">⏰ <?= esc($paper['presentation_time']) ?></strong></td>
                      <td>#<?= $paper['paper_id'] ?></td>
                      <td style="max-width: 320px;">
                        <div style="font-weight: 600;"><?= esc($paper['title']) ?></div>
                        <div class="mt-1">
                          <a href="<?= base_url($paper['file_path']) ?>" target="_blank" class="btn btn-secondary btn-sm" style="padding: 0.1rem 0.5rem; font-size: 0.75rem;">📖 เปิดอ่าน PDF</a>
                        </div>
                      </td>
                      <td>
                        <div><?= esc($paper['track_name']) ?></div>
                        <small class="text-muted"><?= esc($paper['discipline_name']) ?></small>
                      </td>
                      <td>
                        <?php if ($paper['review_status'] === 'completed'): ?>
                          <span class="badge badge-success">ประเมินเสร็จแล้ว</span>
                          <?php if ($paper['my_score'] !== null): ?>
                            <span class="badge badge-info" style="padding: 0.1rem 0.3rem; font-size: 0.65rem; background: rgba(2, 132, 199, 0.15); color: var(--info); border: 1px solid var(--info);">(<?= esc($paper['my_score']) ?> คะแนน)</span>
                          <?php endif; ?>
                        <?php else: ?>
                          <span class="badge badge-pending">รอนำเสนอ/รอประเมิน</span>
                        <?php endif; ?>
                      </td>
                      <td>
                        <?php if ($paper['review_id']): ?>
                          <a href="<?= base_url('committee/evaluate/' . $paper['review_id']) ?>" class="btn btn-primary btn-sm" style="font-size: 0.8rem; padding: 0.3rem 0.6rem;">
                            <?= $paper['review_status'] === 'completed' ? '📝 แก้ไขผลประเมิน' : '✍️ ให้คะแนนนำเสนอ' ?>
                          </a>
                        <?php else: ?>
                          <span class="text-muted" style="font-size: 0.8rem;">ไม่พบลิงก์ประเมิน</span>
                        <?php endif; ?>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>

  </div>

</body>
</html>
