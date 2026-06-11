<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>จัดการเกณฑ์ประเมิน - CSRM</title>
  <link rel="stylesheet" href="<?= base_url('css/style.css'); ?>">
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
      <a href="<?= base_url('admin/criteria'); ?>" class="navbar-item active">เกณฑ์ประเมิน</a>
      <a href="<?= base_url('admin/rooms'); ?>" class="navbar-item">จัดห้องพรีเซนต์</a>
      <a href="<?= base_url('admin/payments'); ?>" class="navbar-item">ยืนยันเงิน</a>
      <?php if (session()->get('role') === 'superadmin'): ?>
        <a href="<?= base_url('superadmin/dashboard'); ?>" class="btn btn-primary btn-sm" style="font-size: 0.85rem; padding: 0.5rem 1rem;">⚙️ กลับหน้า SuperAdmin</a>
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

    <div class="grid-2">
      
      <!-- Left: List -->
      <div>
        <!-- Round 1: Peer Review -->
        <div class="card mb-3">
          <h2>📑 เกณฑ์ประเมินรอบแรก (Peer Review)</h2>
          <p class="text-muted mb-2">เกณฑ์คะแนนที่ผู้ทรงคุณวุฒิ 3 ท่านใช้ลงมติประเมินบทความ</p>

          <div class="table-responsive">
            <table class="table">
              <thead>
                <tr>
                  <th>หัวข้อเกณฑ์</th>
                  <th>คะแนนเต็ม</th>
                  <th>จัดการ</th>
                </tr>
              </thead>
              <tbody>
                <?php 
                  $round1_exists = false;
                  foreach ($criteria as $crit): 
                    if ($crit['round'] == 1):
                      $round1_exists = true;
                ?>
                  <tr>
                    <td>
                      <div style="font-weight:600;"><?= esc($crit['criteria_name']) ?></div>
                      <small class="text-muted"><?= esc($crit['description']) ?></small>
                    </td>
                    <td><strong><?= esc($crit['max_score']) ?></strong> คะแนน</td>
                    <td>
                      <a href="<?= base_url('admin/deleteCriteria/' . $crit['id']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('ยืนยันที่จะลบเกณฑ์นี้?')" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">ลบ</a>
                    </td>
                  </tr>
                <?php 
                    endif;
                  endforeach; 
                  if (!$round1_exists):
                ?>
                  <tr>
                    <td colspan="3" class="text-center text-muted">ยังไม่มีเกณฑ์ประเมินสำหรับรอบที่ 1</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Round 2: Presentation -->
        <div class="card">
          <h2>📢 เกณฑ์ประเมินรอบสอง (Presentation)</h2>
          <p class="text-muted mb-2">เกณฑ์คะแนนที่คณะกรรมการประจำห้องนำเสนอใช้ประเมินวันเสนอผลงาน</p>

          <div class="table-responsive">
            <table class="table">
              <thead>
                <tr>
                  <th>หัวข้อเกณฑ์</th>
                  <th>คะแนนเต็ม</th>
                  <th>จัดการ</th>
                </tr>
              </thead>
              <tbody>
                <?php 
                  $round2_exists = false;
                  foreach ($criteria as $crit): 
                    if ($crit['round'] == 2):
                      $round2_exists = true;
                ?>
                  <tr>
                    <td>
                      <div style="font-weight:600;"><?= esc($crit['criteria_name']) ?></div>
                      <small class="text-muted"><?= esc($crit['description']) ?></small>
                    </td>
                    <td><strong><?= esc($crit['max_score']) ?></strong> คะแนน</td>
                    <td>
                      <a href="<?= base_url('admin/deleteCriteria/' . $crit['id']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('ยืนยันที่จะลบเกณฑ์นี้?')" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">ลบ</a>
                    </td>
                  </tr>
                <?php 
                    endif;
                  endforeach; 
                  if (!$round2_exists):
                ?>
                  <tr>
                    <td colspan="3" class="text-center text-muted">ยังไม่มีเกณฑ์ประเมินสำหรับรอบที่ 2</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Right: Add Form -->
      <div class="card" style="height: fit-content;">
        <h2>➕ เพิ่มเกณฑ์การประเมิน</h2>
        <p class="text-muted mb-3">กำหนดหัวข้อ และสัดส่วนคะแนนที่ต้องการเปิดประเมินผลงาน</p>

        <form action="<?= base_url('admin/addCriteria'); ?>" method="POST">
          <?= csrf_field(); ?>
          
          <div class="form-group">
            <label class="form-label" for="round">เลือกขั้นตอนการประเมิน (รอบ)</label>
            <select id="round" name="round" class="form-control" required>
              <option value="1">รอบแรก: การประเมินบทความ (Peer Review)</option>
              <option value="2">รอบสอง: การนำเสนอผลงาน (Presentation)</option>
            </select>
          </div>

          <div class="form-group">
            <label class="form-label" for="criteria_name">หัวข้อเกณฑ์ประเมิน</label>
            <input type="text" id="criteria_name" name="criteria_name" class="form-control" placeholder="เช่น ความสมบูรณ์ของทฤษฎีบท, ทักษะการพูดและสไลด์" required>
          </div>

          <div class="form-group">
            <label class="form-label" for="max_score">คะแนนเต็ม</label>
            <input type="number" id="max_score" name="max_score" class="form-control" placeholder="เช่น 20, 30, 50, 100" min="1" max="100" required>
          </div>

          <div class="form-group">
            <label class="form-label" for="description">คำอธิบายรายละเอียดเกณฑ์</label>
            <textarea id="description" name="description" class="form-control" rows="3" placeholder="ระบุรายละเอียดของเกณฑ์ประเมิน เพื่ออธิบายการตัดสินให้ผู้ทรงฯ และกรรมการทราบ..."></textarea>
          </div>

          <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">บันทึกเกณฑ์ประเมิน</button>
        </form>
      </div>

      <!-- Save as Template Form -->
      <div class="card mb-3" style="height: fit-content; margin-top: 1.5rem;">
        <h2>💾 บันทึกเป็นแบบฟอร์มสำเร็จรูป</h2>
        <p class="text-muted mb-3" style="font-size: 0.85rem;">บันทึกเกณฑ์ประเมินปัจจุบันของรอบนี้ เพื่อนำกลับมาใช้ใหม่ในปีอื่นๆ</p>

        <form action="<?= base_url('admin/saveAsTemplate'); ?>" method="POST">
          <?= csrf_field(); ?>
          <div class="form-group">
            <label class="form-label" for="tmpl_round">เลือกขั้นตอนการประเมิน</label>
            <select id="tmpl_round" name="round" class="form-control" required>
              <option value="1">รอบแรก: การประเมินบทความ (Peer Review)</option>
              <option value="2">รอบสอง: การนำเสนอผลงาน (Presentation)</option>
            </select>
          </div>

          <div class="form-group">
            <label class="form-label" for="template_name">ตั้งชื่อแบบฟอร์มคะแนน</label>
            <input type="text" id="template_name" name="template_name" class="form-control" placeholder="เช่น เกณฑ์ปี 2568, เกณฑ์มาตรฐาน สกอ." required style="font-size: 0.9rem;">
          </div>

          <button type="submit" class="btn btn-secondary" style="width: 100%; margin-top: 0.5rem; background: var(--info); color: #fff; border-color: var(--info); font-size: 0.9rem;">💾 บันทึกแบบฟอร์มคะแนน</button>
        </form>
      </div>

      <!-- Templates List -->
      <div class="card" style="height: fit-content; margin-top: 1.5rem;">
        <h2>📋 แบบฟอร์มคะแนนสำเร็จรูป</h2>
        <p class="text-muted mb-3" style="font-size: 0.85rem;">รายการแบบฟอร์มที่เคยสร้างไว้ สามารถนำเข้ามาใช้ในปีปัจจุบัน</p>

        <?php if (empty($templates)): ?>
          <div class="text-center text-muted" style="padding: 1.5rem 0; font-size: 0.9rem;">
            ยังไม่มีแบบฟอร์มสำเร็จรูปบันทึกไว้
          </div>
        <?php else: ?>
          <?php foreach ($templates as $tmpl): ?>
            <div style="border: 1px solid var(--card-border); border-radius: var(--radius-sm); padding: 1rem; margin-bottom: 1rem; background: rgba(255, 255, 255, 0.01);">
              <div class="flex justify-between align-center mb-1">
                <strong style="font-size: 0.9rem;"><?= esc($tmpl['name']) ?></strong>
                <span class="badge" style="font-size: 0.7rem; padding: 0.1rem 0.4rem; background: <?= $tmpl['round'] == 1 ? 'rgba(37, 99, 235, 0.1)' : 'rgba(22, 163, 74, 0.1)' ?>; color: <?= $tmpl['round'] == 1 ? 'var(--primary)' : 'var(--success)' ?>;">
                  <?= $tmpl['round'] == 1 ? 'Peer Review' : 'Presentation' ?>
                </span>
              </div>
              
              <ul style="font-size: 0.8rem; padding-left: 1.25rem; color: var(--text-secondary); margin-bottom: 0.75rem; list-style-type: disc;">
                <?php foreach ($tmpl['criteria'] as $tc): ?>
                  <li><?= esc($tc['criteria_name']) ?> (<?= esc($tc['max_score']) ?> คะแนน)</li>
                <?php endforeach; ?>
              </ul>

              <div class="flex gap-1" style="display: flex; gap: 0.5rem;">
                <form action="<?= base_url('admin/importTemplate'); ?>" method="POST" style="flex: 1;" onsubmit="return confirm('คำเตือน: การนำเข้าแบบฟอร์มจะเขียนทับเกณฑ์ประเมินรอบปัจจุบันทั้งหมดของปีนี้! ยืนยันการนำเข้า?')">
                  <?= csrf_field(); ?>
                  <input type="hidden" name="template_id" value="<?= $tmpl['id'] ?>">
                  <button type="submit" class="btn btn-success btn-sm" style="width: 100%; font-size: 0.75rem; padding: 0.25rem 0.5rem;">📥 นำไปใช้ในปีนี้</button>
                </form>
                <a href="<?= base_url('admin/deleteTemplate/' . $tmpl['id']) ?>" class="btn btn-danger btn-sm" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;" onclick="return confirm('ยืนยันลบแบบฟอร์มสำเร็จรูปนี้?')">ลบ</a>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>

    </div>

  </div>

</body>
</html>
