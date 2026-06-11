<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>สมัครสมาชิก - CSRM คณิตศาสตร์</title>
  <link rel="stylesheet" href="<?= base_url('css/style.css'); ?>">
  <style>
    .auth-container {
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      padding: 1.5rem;
    }
    .auth-card {
      width: 100%;
      max-width: 500px;
      animation: fadeIn 0.6s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .auth-title {
      font-size: 1.75rem;
      font-weight: 700;
      text-align: center;
      margin-bottom: 0.5rem;
      color: var(--primary);
    }
    .auth-subtitle {
      text-align: center;
      color: var(--text-secondary);
      font-size: 0.9rem;
      margin-bottom: 2rem;
    }
  </style>
</head>
<body>

  <div class="auth-container">
    <div class="card auth-card">
      <div class="auth-title">สมัครสมาชิกใหม่</div>
      <div class="auth-subtitle">ระบบจัดการประชุมวิชาการคณิตศาสตร์</div>

      <!-- Validation Errors -->
      <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger">
          <ul style="padding-left: 1rem; margin: 0;">
            <?php foreach (session()->getFlashdata('errors') as $error): ?>
              <li><?= esc($error); ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <form action="<?= base_url('auth/register'); ?>" method="POST">
        <?= csrf_field(); ?>
        
        <div class="form-group">
          <label class="form-label" for="email">อีเมล (ใช้สำหรับล็อกอิน)</label>
          <input type="email" id="email" name="email" class="form-control" placeholder="example@domain.com" required value="<?= old('email'); ?>">
        </div>

        <div class="form-group">
          <label class="form-label" for="password">รหัสผ่าน</label>
          <input type="password" id="password" name="password" class="form-control" placeholder="อย่างน้อย 6 ตัวอักษร" required>
        </div>

        <div class="grid-2">
          <div class="form-group">
            <label class="form-label" for="first_name">ชื่อจริง</label>
            <input type="text" id="first_name" name="first_name" class="form-control" placeholder="สมชาย" required value="<?= old('first_name'); ?>">
          </div>
          <div class="form-group">
            <label class="form-label" for="last_name">นามสกุล</label>
            <input type="text" id="last_name" name="last_name" class="form-control" placeholder="ดีใจ" required value="<?= old('last_name'); ?>">
          </div>
        </div>

        <div class="form-group">
          <label class="form-label" for="user_type">ประเภทบัญชีผู้ใช้</label>
          <select id="user_type" class="form-control" required onchange="handleUserTypeChange(this.value)">
            <option value="author" <?= old('role') === 'author' || !old('role') ? 'selected' : ''; ?>>ผู้ส่ง (Author)</option>
            <option value="evaluator" <?= in_array(old('role'), ['reviewer', 'committee']) ? 'selected' : ''; ?>>ผู้ประเมิน / กรรมการ (Evaluator/Committee)</option>
          </select>
        </div>

        <input type="hidden" id="role" name="role" value="<?= old('role') ?: 'author'; ?>">

        <div class="form-group animate-fade-in" id="sub_role_container" style="display: <?= in_array(old('role'), ['reviewer', 'committee']) ? 'block' : 'none'; ?>;">
          <label class="form-label" for="sub_role">หน้าที่ในการประเมิน</label>
          <select id="sub_role" class="form-control" onchange="document.getElementById('role').value = this.value">
            <option value="reviewer" <?= old('role') === 'reviewer' ? 'selected' : ''; ?>>ผู้ทรงคุณวุฒิประเมินบทความ (Reviewer)</option>
            <option value="committee" <?= old('role') === 'committee' ? 'selected' : ''; ?>>กรรมการประจำห้องนำเสนอ (Committee)</option>
          </select>
        </div>

        <div class="form-group animate-fade-in" id="keywords_container" style="display: <?= in_array(old('role'), ['reviewer', 'committee']) ? 'block' : 'none'; ?>;">
          <label class="form-label" for="keywords">ความเชี่ยวชาญพิเศษทางคณิตศาสตร์ (แยกคำด้วยเครื่องหมายจุลภาค ,)</label>
          <input type="text" id="keywords" name="keywords" class="form-control" placeholder="เช่น Algebra, Calculus, Topology, Set Theory" value="<?= old('keywords'); ?>">
        </div>

        <script>
          function handleUserTypeChange(val) {
            var container = document.getElementById('sub_role_container');
            var keywordsContainer = document.getElementById('keywords_container');
            var roleInput = document.getElementById('role');
            var subRoleSelect = document.getElementById('sub_role');
            
            if (val === 'author') {
              container.style.display = 'none';
              keywordsContainer.style.display = 'none';
              roleInput.value = 'author';
            } else {
              container.style.display = 'block';
              keywordsContainer.style.display = 'block';
              roleInput.value = subRoleSelect.value;
            }
          }
        </script>

        <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">สมัครสมาชิก</button>
      </form>

      <div class="text-center mt-3">
        <span class="text-muted">มีบัญชีผู้ใช้อยู่แล้ว?</span>
        <a href="<?= base_url('auth/login'); ?>" style="font-weight: 600; margin-left: 0.25rem;">เข้าสู่ระบบ</a>
      </div>
    </div>
  </div>

</body>
</html>
