<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>จัดการศาสตร์คณิตศาสตร์ - CSRM</title>
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
      <a href="<?= base_url('admin/disciplines'); ?>" class="navbar-item active">ศาสตร์ย่อย</a>
      <a href="<?= base_url('admin/criteria'); ?>" class="navbar-item">เกณฑ์ประเมิน</a>
      <a href="<?= base_url('admin/rooms'); ?>" class="navbar-item">จัดห้องพรีเซนต์</a>
      <a href="<?= base_url('admin/payments'); ?>" class="navbar-item">ยืนยันเงิน</a>
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
      
      <!-- List -->
      <div class="card">
        <h2>📚 ศาสตร์ย่อยทางคณิตศาสตร์ปัจจุบัน</h2>
        <p class="text-muted mb-2">แสดงรายการศาสตร์ย่อยทั้งหมดที่ใช้สำหรับจำแนกบทความวิชาการ</p>

        <div class="table-responsive">
          <table class="table">
            <thead>
              <tr>
                <th>ศาสตร์ / สาขาย่อย</th>
                <th>ประเภทห้องประชุม (Track)</th>
                <th>จัดการ</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($disciplines)): ?>
                <tr>
                  <td colspan="3" class="text-center text-muted">ยังไม่มีการเพิ่มศาสตร์ย่อยในระบบ</td>
                </tr>
              <?php else: ?>
                <?php foreach ($disciplines as $disc): ?>
                  <tr>
                    <td><strong><?= esc($disc['name']) ?></strong></td>
                    <td><span class="badge badge-info"><?= esc($disc['track_name']) ?></span></td>
                    <td>
                      <a href="<?= base_url('admin/deleteDiscipline/' . $disc['id']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('ยืนยันลบศาสตร์ย่อยนี้?')" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">ลบ</a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Add Form -->
      <div class="card" style="height: fit-content;">
        <h2>➕ เพิ่มศาสตร์ย่อยทางคณิตศาสตร์</h2>
        <p class="text-muted mb-3">ระบุชื่อและเชื่อมโยงกับประเภทหลักของการประชุม</p>

        <form action="<?= base_url('admin/addDiscipline'); ?>" method="POST">
          <?= csrf_field(); ?>
          
          <div class="form-group">
            <label class="form-label" for="track_id">เลือกประเภทงานประชุม (Track)</label>
            <select id="track_id" name="track_id" class="form-control" required>
              <?php foreach ($tracks as $t): ?>
                <option value="<?= $t['id'] ?>"><?= esc($t['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group">
            <label class="form-label" for="name">ชื่อศาสตร์คณิตศาสตร์</label>
            <input type="text" id="name" name="name" class="form-control" placeholder="เช่น แคลคูลัส (Calculus), พีชคณิตเชิงเส้น (Linear Algebra)" required>
          </div>

          <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">เพิ่มข้อมูลศาสตร์ย่อย</button>
        </form>
      </div>

    </div>

  </div>

</body>
</html>
