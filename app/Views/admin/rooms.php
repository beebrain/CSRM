<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
  <title>จัดห้องนำเสนอและกรรมการ - CSRM</title>
  <link class="styles" rel="stylesheet" href="<?= base_url('css/style.css'); ?>">
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
      <a href="<?= base_url('admin/rooms'); ?>" class="navbar-item active">จัดห้องพรีเซนต์</a>
      <a href="<?= base_url('admin/payments'); ?>" class="navbar-item">ยืนยันเงิน</a>
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

    <div class="grid-3" style="grid-template-columns: 1fr 2fr;">
      
      <!-- Left: Create Room Form -->
      <div class="card" style="height: fit-content;">
        <h2 class="text-2xl">➕ สร้างห้องนำเสนอผลงาน</h2>
        <p class="text-muted mb-3">สร้างห้องสำหรับเสนอผลงานในรอบที่สอง</p>

        <form action="<?= base_url('admin/createRoom'); ?>" method="POST">
          <?= csrf_field(); ?>
          <div class="form-group">
            <label class="form-label" for="name">ชื่อห้องนำเสนอ</label>
            <input type="text" id="name" name="name" class="form-control" placeholder="เช่น ห้อง 1 - พีชคณิต" required>
          </div>

          <div class="form-group">
            <label class="form-label" for="location">สถานที่ / ลิงก์ออนไลน์</label>
            <input type="text" id="location" name="location" class="form-control" placeholder="เช่น อาคาร 4 ห้อง 401 หรือ Zoom link" required>
          </div>

          <div class="form-group">
            <label class="form-label" for="date_time">วันและเวลานำเสนอ</label>
            <input type="datetime-local" id="date_time" name="date_time" class="form-control" required>
          </div>

          <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">สร้างห้อง</button>
        </form>
      </div>

      <!-- Right: Rooms Details & Assignments -->
      <div>
        <?php if (empty($rooms)): ?>
          <div class="card text-center text-muted" style="padding: 3rem;">
            <h3 class="text-xl">ยังไม่มีการสร้างห้องนำเสนอผลงาน</h3>
            <p>กรุณาสร้างห้องนำเสนอที่กล่องควบคุมฝั่งซ้ายมือ</p>
          </div>
        <?php else: ?>
          <?php foreach ($rooms as $room): ?>
            <div class="card room-card">
              <div class="flex justify-between align-center mb-2">
                <div>
                  <h2 class="text-xl" style="color: var(--primary);"><?= esc($room['name']) ?></h2>
                  <small class="text-muted">สถานที่: <strong><?= esc($room['location']) ?></strong> | วันเวลา: <?= esc($room['date_time']) ?></small>
                </div>
                <a href="<?= base_url('admin/deleteRoom/' . $room['id']) ?>" class="btn btn-danger btn-sm text-xs" onclick="return confirm('ยืนยันลบห้องและถอนการจัดสรรทั้งหมด?')" style="padding: 0.25rem 0.5rem;">ลบห้อง</a>
              </div>

              <div class="grid-2">
                <!-- 1. Committee Assignment -->
                <div class="assignment-box">
                  <div class="assignment-title">
                    <span>👥 กรรมการประจำห้อง</span>
                  </div>
                  
                  <!-- List of assigned committees -->
                  <ul class="mb-2" style="list-style: none;">
                    <?php if (empty($roomDetails[$room['id']]['committees'])): ?>
                      <li class="text-muted text-center text-sm" style="padding: 0.5rem 0;">ยังไม่มีกรรมการประจำห้อง</li>
                    <?php else: ?>
                      <?php foreach ($roomDetails[$room['id']]['committees'] as $c): ?>
                        <li class="flex justify-between align-center mb-1 text-sm" style="background: rgba(255, 255, 255, 0.02); padding: 0.25rem 0.5rem; border-radius: 4px;">
                          <div>
                            <strong><?= esc($c['first_name']) ?></strong>
                            <div class="text-muted text-xs"><?= esc($c['email']) ?></div>
                          </div>
                          <a href="<?= base_url('admin/removeCommittee/' . $c['assignment_id']) ?>" class="text-xs" style="color: var(--danger); font-weight: 600;">ถอน</a>
                        </li>
                      <?php endforeach; ?>
                    <?php endif; ?>
                  </ul>

                  <!-- Form to assign committee -->
                  <form action="<?= base_url('admin/assignCommittee'); ?>" method="POST" class="flex gap-1">
                    <?= csrf_field(); ?>
                    <input type="hidden" name="room_id" value="<?= $room['id'] ?>">
                    <select name="committee_id" class="form-control text-xs" style="padding: 0.25rem;" required>
                      <option value="">+ เลือกกรรมการ</option>
                      <?php foreach ($committees as $comm): ?>
                        <option value="<?= $comm['id'] ?>"><?= esc($comm['first_name']) ?></option>
                      <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn btn-primary btn-sm text-xs" style="padding: 0.25rem 0.5rem;">เพิ่ม</button>
                  </form>
                </div>

                <!-- 2. Papers Assignment -->
                <div class="assignment-box">
                  <div class="assignment-title">
                    <span>📄 บทความในห้องนี้</span>
                  </div>
                  
                  <!-- List of assigned papers -->
                  <ul class="mb-2" style="list-style: none;">
                    <?php if (empty($roomDetails[$room['id']]['papers'])): ?>
                      <li class="text-muted text-center text-sm" style="padding: 0.5rem 0;">ยังไม่มีบทความนำเสนอในห้องนี้</li>
                    <?php else: ?>
                      <?php foreach ($roomDetails[$room['id']]['papers'] as $p): ?>
                        <li class="flex justify-between align-center mb-1 text-sm" style="background: rgba(255, 255, 255, 0.02); padding: 0.25rem 0.5rem; border-radius: 4px;">
                          <div style="max-width: 80%;">
                            <div style="font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?= esc($p['title']) ?></div>
                            <small class="text-muted">ผู้นำเสนอ: <?= esc($p['author_first']) ?> | เวลา: <strong><?= esc($p['presentation_time']) ?></strong></small>
                          </div>
                          <a href="<?= base_url('admin/removePaper/' . $p['assignment_id']) ?>" class="text-xs" style="color: var(--danger); font-weight: 600;">ถอน</a>
                        </li>
                      <?php endforeach; ?>
                    <?php endif; ?>
                  </ul>

                  <!-- Form to assign paper -->
                  <form action="<?= base_url('admin/assignPaper'); ?>" method="POST" class="mt-1">
                    <?= csrf_field(); ?>
                    <input type="hidden" name="room_id" value="<?= $room['id'] ?>">
                    
                    <div class="flex gap-1 mb-1">
                      <select name="paper_id" class="form-control text-xs" style="padding: 0.25rem;" required>
                        <option value="">+ จัดบทความเข้าห้อง</option>
                        <?php foreach ($papers as $paper): ?>
                          <option value="<?= $paper['id'] ?>"><?= esc($paper['title']) ?> (<?= esc($paper['author_first_name']) ?>)</option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                    <div class="flex gap-1">
                      <input type="time" name="presentation_time" class="form-control text-xs" style="padding: 0.25rem;" required>
                      <button type="submit" class="btn btn-primary btn-sm text-xs" style="padding: 0.25rem 0.5rem; white-space: nowrap;">จัดสรร</button>
                    </div>
                  </form>
                </div>
              </div>

            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>

    </div>

  </div>

</body>
</html>
