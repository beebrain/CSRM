<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>เข้าสู่ระบบ - CSRM คณิตศาสตร์</title>
  <link rel="stylesheet" href="<?= base_url('css/style.css'); ?>">
  <style>
    .auth-container {
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      padding: 1rem;
    }
    .auth-card {
      width: 100%;
      max-width: 450px;
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
    .mock-verify-box {
      background: rgba(99, 102, 241, 0.1);
      border: 1px dashed var(--primary);
      padding: 1rem;
      border-radius: var(--radius-sm);
      margin-bottom: 1.5rem;
      font-size: 0.85rem;
    }
  </style>
</head>
<body>

  <div class="auth-container">
    <div class="card auth-card">
      <div class="auth-title">CSRM System</div>
      <div class="auth-subtitle">ระบบจัดการประชุมวิชาการคณิตศาสตร์</div>

      <!-- Flash Messages -->
      <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger">
          <?= session()->getFlashdata('error'); ?>
        </div>
      <?php endif; ?>

      <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success">
          <?= session()->getFlashdata('success'); ?>
        </div>
      <?php endif; ?>

      <!-- Mock Email Link (For Dev Testing) -->
      <?php if (session()->getFlashdata('temp_verify_url')): ?>
        <div class="mock-verify-box">
          <p class="text-muted" style="font-weight: 600; margin-bottom: 0.25rem;">📬 [จำลองการส่งเมล] ลิงก์ยืนยันตัวตน:</p>
          <a href="<?= session()->getFlashdata('temp_verify_url'); ?>" style="word-break: break-all; text-decoration: underline;">
            <?= session()->getFlashdata('temp_verify_url'); ?>
          </a>
        </div>
      <?php endif; ?>

      <form action="<?= base_url('auth/login'); ?>" method="POST">
        <?= csrf_field(); ?>
        
        <div class="form-group">
          <label class="form-label" for="email">อีเมล</label>
          <input type="email" id="email" name="email" class="form-control" placeholder="example@domain.com" required value="<?= old('email'); ?>">
        </div>

        <div class="form-group">
          <label class="form-label" for="password">รหัสผ่าน</label>
          <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required>
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">เข้าสู่ระบบ</button>
      </form>

      <div class="text-center mt-3">
        <span class="text-muted">ยังไม่มีบัญชีผู้ใช้?</span>
        <a href="<?= base_url('auth/register'); ?>" style="font-weight: 600; margin-left: 0.25rem;">สมัครสมาชิก</a>
      </div>
    </div>
  </div>

</body>
</html>
