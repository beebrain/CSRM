<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>แก้ไขรอบปีการประชุม - CSRM</title>
  <link rel="stylesheet" href="<?= base_url('css/style.css'); ?>">
  <style>
    .switch-group {
      background: rgba(255, 255, 255, 0.02);
      border: 1px solid var(--card-border);
      border-radius: var(--radius-sm);
      padding: 1rem 1.5rem;
      margin-bottom: 1.5rem;
    }
    .switch-item {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 0.75rem 0;
    }
    .switch-item:not(:last-child) {
      border-bottom: 1px dashed var(--card-border);
    }
    /* Simple custom toggle styling */
    .toggle-control {
      position: relative;
      display: inline-block;
      width: 50px;
      height: 26px;
    }
    .toggle-control input {
      opacity: 0;
      width: 0;
      height: 0;
    }
    .slider {
      position: absolute;
      cursor: pointer;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-color: #cbd5e1;
      transition: .4s;
      border-radius: 34px;
    }
    .slider:before {
      position: absolute;
      content: "";
      height: 18px;
      width: 18px;
      left: 4px;
      bottom: 4px;
      background-color: white;
      transition: .4s;
      border-radius: 50%;
    }
    input:checked + .slider {
      background-color: var(--primary);
    }
    input:checked + .slider:before {
      transform: translateX(24px);
    }
  </style>
</head>
<body>

  <!-- Navigation -->
  <nav class="navbar animate-fade-in">
    <div class="navbar-brand">
      🚀 CSRM SuperAdmin
    </div>
    <div class="navbar-menu">
      <a href="<?= base_url('superadmin/dashboard'); ?>" class="btn btn-secondary btn-sm">⬅️ กลับหน้าแดชบอร์ด</a>
    </div>
  </nav>

  <!-- Main Content -->
  <div class="container animate-fade-in" style="max-width: 700px;">
    
    <div class="card">
      <h2 class="mb-2">🏆 แก้ไขรอบปีการประชุม</h2>
      <p class="text-muted mb-4">ปรับปรุงรายละเอียดการประชุมวิชาการประจำปีและจัดการเปิด/ปิดสถานะกระบวนการต่างๆ</p>

      <form action="<?= base_url('superadmin/editConference/' . $conference['id']); ?>" method="POST">
        <?= csrf_field(); ?>

        <div class="grid-2">
          <div class="form-group">
            <label class="form-label" for="year">ปีการประชุม (พ.ศ.)</label>
            <input type="number" id="year" name="year" class="form-control" value="<?= esc($conference['year']) ?>" required>
          </div>
          <div class="form-group">
            <label class="form-label" for="host_name">หน่วยงานเจ้าภาพ</label>
            <input type="text" id="host_name" name="host_name" class="form-control" value="<?= esc($conference['host_name']) ?>" required>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label" for="title">หัวข้องานประชุม</label>
          <input type="text" id="title" name="title" class="form-control" value="<?= esc($conference['title']) ?>" required>
        </div>

        <div class="form-group">
          <label class="form-label" for="description">คำอธิบายเพิ่มเติม</label>
          <textarea id="description" name="description" class="form-control" rows="3"><?= esc($conference['description']) ?></textarea>
        </div>

        <h3 class="mb-2" style="font-size: 1.1rem; color: var(--text-primary);">⚙️ การจัดการช่วงการจัดงาน</h3>
        <p class="text-muted mb-3" style="font-size: 0.85rem;">เปิดหรือปิดการดำเนินการในขั้นตอนต่างๆ ของบทความวิชาการ</p>

        <div class="switch-group">
          <!-- 1. Submission Phase -->
          <div class="switch-item">
            <div>
              <strong>📥 การรับบทความ</strong>
              <div class="text-muted" style="font-size: 0.8rem;">ผู้แต่ง (Author) จะสามารถส่งบทความวิชาการใหม่เข้าระบบได้</div>
            </div>
            <label class="toggle-control">
              <input type="checkbox" name="accept_submissions" value="1" <?= $conference['accept_submissions'] ? 'checked' : '' ?>>
              <span class="slider"></span>
            </label>
          </div>

          <!-- 2. Evaluation Phase -->
          <div class="switch-item">
            <div>
              <strong>🔍 การประเมินบทความ</strong>
              <div class="text-muted" style="font-size: 0.8rem;">ผู้ทรงคุณวุฒิ (Reviewer) จะสามารถทำการประเมินและส่งผลการประเมินได้</div>
            </div>
            <label class="toggle-control">
              <input type="checkbox" name="accept_evaluations" value="1" <?= $conference['accept_evaluations'] ? 'checked' : '' ?>>
              <span class="slider"></span>
            </label>
          </div>

          <!-- 3. Scoring/Grading Phase -->
          <div class="switch-item">
            <div>
              <strong>💯 การให้คะแนนนำเสนอ</strong>
              <div class="text-muted" style="font-size: 0.8rem;">กรรมการห้องพรีเซนต์ (Committee) จะสามารถให้คะแนนการนำเสนอผลงานได้</div>
            </div>
            <label class="toggle-control">
              <input type="checkbox" name="accept_grading" value="1" <?= $conference['accept_grading'] ? 'checked' : '' ?>>
              <span class="slider"></span>
            </label>
          </div>
        </div>

        <div class="flex gap-2 mt-4">
          <button type="submit" class="btn btn-primary" style="flex: 1;">💾 บันทึกการแก้ไข</button>
          <a href="<?= base_url('superadmin/dashboard'); ?>" class="btn btn-secondary" style="flex: 1; text-align: center;">ยกเลิก</a>
        </div>
      </form>
    </div>

  </div>

</body>
</html>
