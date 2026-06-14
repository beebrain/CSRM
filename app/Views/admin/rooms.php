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

    <div class="grid-3" style="grid-template-columns: 1fr 2fr;">
      
      <!-- Left: Create Room & Auto-Assign Forms -->
      <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        <!-- 1. Create Room Card -->
        <div class="card" style="height: fit-content; margin-bottom: 0;">
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

        <!-- 2. Auto Assign Card -->
        <div class="card" style="height: fit-content; margin-bottom: 0; background: linear-gradient(135deg, rgba(99, 102, 241, 0.05) 0%, rgba(168, 85, 247, 0.02) 100%); border: 1px dashed rgba(99, 102, 241, 0.25);">
          <h2 class="text-2xl" style="color: var(--primary);">🤖 จัดห้องอัตโนมัติตามศาสตร์</h2>
          <p class="text-muted mb-3" style="font-size: 0.8rem; line-height: 1.4;">ระบบจะสแกนบทความที่ผ่านรอบแรกและยังไม่ได้จัดห้อง จากนั้นจะจำแนกจับกลุ่มตามสาขาศาสตร์และกระจายเข้าแต่ละห้องเฉลี่ยให้สมดุลที่สุด</p>

          <form action="<?= base_url('admin/autoAssignRooms'); ?>" method="POST">
            <?= csrf_field(); ?>
            <div class="form-group">
              <label class="form-label" for="room_count">จำนวนห้องนำเสนอที่ต้องการ</label>
              <input type="number" id="room_count" name="room_count" class="form-control" placeholder="เช่น 2 หรือ 3" min="1" max="10" required style="font-size: 0.9rem;">
            </div>
            
            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem; background: linear-gradient(to right, var(--primary), var(--secondary)); border: none; font-weight: bold;" onclick="return confirm('ระบบจะทำการสร้างห้องนำเสนอใหม่ตามศาสตร์และจำนวนห้องที่ระบุ และจัดเรียงบทความให้โดยอัตโนมัติ ยืนยันดำเนินการ?')">
              ⚡ เริ่มจัดห้องอัตโนมัติ
            </button>
          </form>
        </div>
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
                <?php 
                  $commCount = count($roomDetails[$room['id']]['committees']);
                ?>
                <div class="assignment-box">
                  <div class="assignment-title" style="display: flex; justify-content: space-between; align-items: center;">
                    <span>👥 กรรมการประจำห้อง</span>
                    <?php if ($commCount === 3): ?>
                      <span class="badge badge-success" style="font-size: 0.65rem; padding: 0.1rem 0.4rem; text-transform: none;">ครบ 3 ท่าน</span>
                    <?php else: ?>
                      <span class="badge badge-pending" style="font-size: 0.65rem; padding: 0.1rem 0.4rem; text-transform: none; background: rgba(220, 38, 38, 0.08); color: var(--danger); border-color: rgba(220, 38, 38, 0.15);">ยังไม่ครบ (<?= $commCount ?>/3)</span>
                    <?php endif; ?>
                  </div>
                  
                  <!-- List of assigned committees -->
                  <ul class="mb-2" style="list-style: none;">
                    <?php if (empty($roomDetails[$room['id']]['committees'])): ?>
                      <li class="text-muted text-center text-sm" style="padding: 0.5rem 0;">ยังไม่มีกรรมการประจำห้อง</li>
                    <?php else: ?>
                      <?php foreach ($roomDetails[$room['id']]['committees'] as $c): ?>
                        <li class="flex justify-between align-center mb-1 text-sm" style="background: rgba(255, 255, 255, 0.02); padding: 0.25rem 0.5rem; border-radius: 4px;">
                          <div>
                            <strong><?= esc($c['first_name']) ?> <?= esc($c['last_name']) ?></strong>
                            <div class="text-muted text-xs"><?= esc($c['email']) ?></div>
                          </div>
                          <a href="<?= base_url('admin/removeCommittee/' . $c['assignment_id']) ?>" class="text-xs" style="color: var(--danger); font-weight: 600;">ถอน</a>
                        </li>
                      <?php endforeach; ?>
                    <?php endif; ?>
                  </ul>

                  <!-- Form to assign committee -->
                  <?php if ($commCount < 3): ?>
                    <form action="<?= base_url('admin/assignCommittee'); ?>" method="POST" class="flex gap-1">
                      <?= csrf_field(); ?>
                      <input type="hidden" name="room_id" value="<?= $room['id'] ?>">
                      <select name="committee_id" class="form-control text-xs" style="padding: 0.25rem;" required>
                        <option value="">+ เลือกกรรมการ</option>
                        <?php foreach ($committees as $comm): ?>
                          <option value="<?= $comm['id'] ?>"><?= esc($comm['first_name']) ?> <?= esc($comm['last_name']) ?></option>
                        <?php endforeach; ?>
                      </select>
                      <button type="submit" class="btn btn-primary btn-sm text-xs" style="padding: 0.25rem 0.5rem;">เพิ่ม</button>
                    </form>
                  <?php else: ?>
                    <div class="text-center text-muted text-xs" style="padding: 0.35rem 0; border: 1px dashed var(--card-border); border-radius: 4px; background: rgba(22, 163, 74, 0.03); font-weight: 600;">
                      ✅ กำหนดกรรมการประจำห้องครบ 3 ท่านแล้ว
                    </div>
                  <?php endif; ?>
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
