<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
  <title>Super Admin Dashboard - CSRM</title>
  <link class="styles" rel="stylesheet" href="<?= base_url('css/style.css'); ?>">
</head>
<body>

  <!-- Navigation -->
  <nav class="navbar animate-fade-in">
    <div class="navbar-brand">
      🚀 CSRM SuperAdmin
    </div>
    <div class="navbar-menu">
      <span class="text-muted">ยินดีต้อนรับ, <strong><?= session()->get('first_name') ?></strong></span>
      <a href="<?= base_url('admin/dashboard'); ?>" class="btn btn-primary btn-sm">💼 เข้าสู่ระบบจัดการหลัก (Admin Panel)</a>
      <a href="<?= base_url('auth/logout'); ?>" class="btn btn-secondary btn-sm">ออกจากระบบ</a>
    </div>
  </nav>

  <!-- Main Content -->
  <div class="container animate-fade-in" style="animation-delay: 0.1s;">
    
    <!-- Alerts -->
    <?php if (session()->getFlashdata('success')): ?>
      <div class="alert alert-success">
        <?= session()->getFlashdata('success'); ?>
      </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
      <div class="alert alert-danger">
        <?= session()->getFlashdata('error'); ?>
      </div>
    <?php endif; ?>

    <!-- System Stats -->
    <div class="grid-stats">
      <div class="stat-card">
        <div class="text-muted">ผู้ใช้งานทั้งหมด</div>
        <div class="stat-val"><?= $stats['total_users'] ?></div>
      </div>
      <div class="stat-card">
        <div class="text-muted">ผู้แต่งบทความ</div>
        <div class="stat-val"><?= $stats['authors'] ?></div>
      </div>
      <div class="stat-card">
        <div class="text-muted">ผู้ทรงคุณวุฒิ (Reviewer)</div>
        <div class="stat-val"><?= $stats['reviewers'] ?></div>
      </div>
      <div class="stat-card">
        <div class="text-muted">กรรมการห้องพรีเซนต์</div>
        <div class="stat-val"><?= $stats['committees'] ?></div>
      </div>
      <div class="stat-card">
        <div class="text-muted">แอดมินประจำปี</div>
        <div class="stat-val"><?= $stats['admins'] ?></div>
      </div>
    </div>

    <!-- Grid Layout for Configs -->
    <div class="grid-2">
      
      <!-- Left: Conferences Management -->
      <div>
        <div class="card">
          <h2 class="mb-2 text-2xl">🏆 การจัดการรอบปีการประชุม</h2>
          
          <!-- Add Conference Form -->
          <form action="<?= base_url('superadmin/createConference'); ?>" method="POST" class="mb-3" style="padding-bottom: 1.5rem; border-bottom: 1px dashed var(--card-border);">
            <?= csrf_field(); ?>
            <div class="grid-2">
              <div class="form-group">
                <label class="form-label" for="year">ปีการประชุม (พ.ศ.)</label>
                <input type="number" id="year" name="year" class="form-control" placeholder="เช่น 2569" required>
              </div>
              <div class="form-group">
                <label class="form-label" for="host_name">หน่วยงานเจ้าภาพ</label>
                <input type="text" id="host_name" name="host_name" class="form-control" placeholder="เช่น มรภ.อุตรดิตถ์" required>
              </div>
            </div>

            <div class="grid-2">
              <div class="form-group">
                <label class="form-label" for="title">หัวข้องานประชุม</label>
                <input type="text" id="title" name="title" class="form-control" placeholder="เช่น การประชุมคณิตศาสตร์ระดับชาติ ครั้งที่ 1" required>
              </div>
              <div class="form-group">
                <label class="form-label" for="default_revision_days">ระยะเวลาส่งเล่มแก้ไข (วัน)</label>
                <input type="number" id="default_revision_days" name="default_revision_days" class="form-control" placeholder="30" value="30" min="1" max="180" required>
              </div>
            </div>

            <div class="form-group">
              <label class="form-label" for="description">คำอธิบายเพิ่มเติม</label>
              <textarea id="description" name="description" class="form-control" rows="2" placeholder="รายละเอียดอื่นๆ..."></textarea>
            </div>

            <div class="form-group flex align-center gap-1">
              <input type="checkbox" id="is_active" name="is_active" value="1" checked>
              <label for="is_active" class="text-sm" style="cursor: pointer;">ตั้งเป็นปีการประชุมหลักทันที (Active Year)</label>
            </div>

            <button type="submit" class="btn btn-primary btn-sm" style="width: 100%;">➕ เพิ่มรอบปีการประชุม</button>
          </form>

          <!-- Conferences List -->
          <div class="table-responsive">
            <table class="table">
              <thead>
                <tr>
                  <th>ปี พ.ศ.</th>
                  <th>หัวข้อ / เจ้าภาพ</th>
                  <th>สถานะ</th>
                  <th>จัดการ</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($conferences as $conf): ?>
                  <tr>
                    <td><strong><?= esc($conf['year']) ?></strong></td>
                    <td>
                      <div style="font-weight: 600;"><?= esc($conf['title']) ?></div>
                      <small class="text-muted" style="display: block; margin-bottom: 0.5rem;">เจ้าภาพ: <?= esc($conf['host_name']) ?></small>
                      
                      <!-- Quick Admin Tools Link -->
                      <div class="quick-tools-grid" style="display: flex; flex-wrap: wrap; gap: 0.35rem; margin-top: 0.5rem; border-top: 1px dashed var(--card-border); padding-top: 0.5rem;">
                        <span class="text-xs" style="color: var(--text-secondary); width: 100%; font-weight: 500; display: block; margin-bottom: 0.15rem;">🛠️ เครื่องมือจัดการแอดมิน:</span>
                        <a href="<?= base_url('admin/selectConference/' . $conf['id'] . '?redirect=admin/dashboard') ?>" class="quick-tool-link" title="จัดการบทความ">📝 บทความ</a>
                        <a href="<?= base_url('admin/selectConference/' . $conf['id'] . '?redirect=admin/disciplines') ?>" class="quick-tool-link" title="จัดการศาสตร์ย่อย">📚 ศาสตร์ย่อย</a>
                        <a href="<?= base_url('admin/selectConference/' . $conf['id'] . '?redirect=admin/criteria') ?>" class="quick-tool-link" title="จัดการเกณฑ์ประเมิน">🎯 เกณฑ์</a>
                        <a href="<?= base_url('admin/selectConference/' . $conf['id'] . '?redirect=admin/rooms') ?>" class="quick-tool-link" title="จัดห้องนำเสนอ">🏫 จัดห้อง</a>
                        <a href="<?= base_url('admin/selectConference/' . $conf['id'] . '?redirect=admin/payments') ?>" class="quick-tool-link" title="ตรวจสอบเงิน">💳 ยืนยันเงิน</a>
                      </div>
                    </td>
                    <td>
                      <div class="mb-1">
                        <?php if ($conf['is_active']): ?>
                          <span class="badge badge-success text-xs">ปีการประชุมหลัก</span>
                        <?php else: ?>
                          <span class="badge badge-pending text-xs" style="opacity: 0.75;">คลังข้อมูล</span>
                        <?php endif; ?>
                      </div>
                      <div class="flex flex-col gap-1" style="margin-top: 0.4rem; gap: 0.25rem;">
                        <div>
                          <span class="badge <?= $conf['accept_submissions'] ? 'badge-success' : 'badge-danger' ?> text-xs" style="padding: 0.15rem 0.5rem;">
                            รับบทความ: <?= $conf['accept_submissions'] ? 'เปิด' : 'ปิด' ?>
                          </span>
                        </div>
                        <div>
                          <span class="badge <?= $conf['accept_evaluations'] ? 'badge-success' : 'badge-danger' ?> text-xs" style="padding: 0.15rem 0.5rem;">
                            ประเมิน: <?= $conf['accept_evaluations'] ? 'เปิด' : 'ปิด' ?>
                          </span>
                        </div>
                        <div>
                          <span class="badge <?= $conf['accept_grading'] ? 'badge-success' : 'badge-danger' ?> text-xs" style="padding: 0.15rem 0.5rem;">
                            ให้คะแนน: <?= $conf['accept_grading'] ? 'เปิด' : 'ปิด' ?>
                          </span>
                        </div>
                      </div>
                    </td>
                    <td>
                      <div class="flex gap-1" style="flex-wrap: wrap;">
                        <?php if (!$conf['is_active']): ?>
                          <a href="<?= base_url('superadmin/toggleConference/' . $conf['id']) ?>" class="btn btn-success btn-sm text-xs" style="padding: 0.25rem 0.5rem;">เปิดใช้หลัก</a>
                        <?php endif; ?>
                        <a href="<?= base_url('superadmin/editConference/' . $conf['id']) ?>" class="btn btn-secondary btn-sm text-xs" style="padding: 0.25rem 0.5rem;">⚙️ แก้ไข</a>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Right: Admin Assignment -->
      <div>
        <div class="card">
          <h2 class="mb-2 text-2xl">👤 แต่งตั้งผู้ดูแลงานประชุมประจำปี</h2>
          
          <!-- Assign Admin Form -->
          <form action="<?= base_url('superadmin/assignAdmin'); ?>" method="POST" class="mb-3" style="padding-bottom: 1.5rem; border-bottom: 1px dashed var(--card-border);">
            <?= csrf_field(); ?>
            
            <div class="form-group">
              <label class="form-label" for="conference_id">เลือกรอบปีการประชุม</label>
              <select id="conference_id" name="conference_id" class="form-control" required>
                <?php foreach ($conferences as $conf): ?>
                  <option value="<?= $conf['id'] ?>">ปี <?= esc($conf['year']) ?> - <?= esc($conf['host_name']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="form-group">
              <label class="form-label" for="user_id">เลือกผู้ใช้งานสิทธิ์แอดมิน</label>
              <select id="user_id" name="user_id" class="form-control" required>
                <?php if (empty($adminsList)): ?>
                  <option value="" disabled>ไม่พบผู้ใช้ที่มีบทบาท admin ในระบบ</option>
                <?php else: ?>
                  <?php foreach ($adminsList as $adm): ?>
                    <option value="<?= $adm['id'] ?>"><?= esc($adm['first_name']) ?> <?= esc($adm['last_name']) ?> (<?= esc($adm['email']) ?>)</option>
                  <?php endforeach; ?>
                <?php endif; ?>
              </select>
            </div>

            <button type="submit" class="btn btn-primary btn-sm" style="width: 100%;" <?= empty($adminsList) ? 'disabled' : '' ?>>👤 แต่งตั้งแอดมิน</button>
          </form>

          <!-- Assigned Admins List -->
          <h3 class="text-xl">รายชื่อผู้ดูแลระบบประจำปี</h3>
          <div class="table-responsive mt-2">
            <table class="table">
              <thead>
                <tr>
                  <th>แอดมิน</th>
                  <th>ปีดูแล</th>
                  <th>จัดการ</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($assignments)): ?>
                  <tr>
                    <td colspan="3" class="text-center text-muted text-sm">ยังไม่มีการแต่งตั้งแอดมินประจำปี</td>
                  </tr>
                <?php else: ?>
                  <?php foreach ($assignments as $assign): ?>
                    <tr>
                      <td>
                        <div><?= esc($assign['first_name']) ?> <?= esc($assign['last_name']) ?></div>
                        <small class="text-muted"><?= esc($assign['email']) ?></small>
                      </td>
                      <td><strong>ปี <?= esc($assign['year']) ?></strong></td>
                      <td>
                        <a href="<?= base_url('superadmin/removeAdmin/' . $assign['id']) ?>" class="btn btn-danger btn-sm text-xs" onclick="return confirm('ถอนสิทธิ์ผู้ดูแลปีนี้ใช่ไหม?')" style="padding: 0.25rem 0.5rem;">ถอนสิทธิ์</a>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

    </div>

  </div>

</body>
</html>
