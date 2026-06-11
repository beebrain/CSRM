<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ประเมินการนำเสนอ - CSRM</title>
  <link rel="stylesheet" href="<?= base_url('css/style.css'); ?>">
</head>
<body>

  <!-- Navigation -->
  <nav class="navbar animate-fade-in">
    <div class="navbar-brand">
      📢 CSRM Committee Dashboard
    </div>
    <div class="navbar-menu">
      <span class="text-muted">กรรมการ: <strong><?= session()->get('first_name') ?></strong></span>
      <a href="<?= base_url('committee/dashboard'); ?>" class="navbar-item">หน้าแรกแดชบอร์ด</a>
      <a href="<?= base_url('auth/logout'); ?>" class="btn btn-secondary btn-sm">ออกจากระบบ</a>
    </div>
  </nav>

  <div class="container animate-fade-in" style="animation-delay: 0.1s;">
    
    <a href="<?= base_url('committee/dashboard'); ?>" class="btn btn-secondary btn-sm mb-2">⬅️ กลับไปหน้าตารางเวลา</a>

    <div class="grid-3" style="grid-template-columns: 1fr 1.5fr;">
      
      <!-- Left: Paper Info -->
      <div class="card" style="height: fit-content;">
        <h2>📖 ข้อมูลบทความนำเสนอ</h2>
        <div style="margin-top: 1rem; border-top: 1px solid var(--card-border); padding-top: 1rem;">
          <small class="text-muted">ชื่อเรื่อง:</small>
          <h3 class="mb-2" style="color: var(--primary);"><?= esc($review['title']) ?></h3>
          
          <small class="text-muted">บทคัดย่อ (Abstract):</small>
          <p class="text-muted mb-2" style="font-size: 0.9rem; text-align: justify; line-height: 1.5; max-height: 250px; overflow-y: auto; padding: 0.5rem; background: rgba(0,0,0,0.2); border-radius: 4px;">
            <?= nl2br(esc($review['abstract'])) ?>
          </p>

          <a href="<?= base_url($review['file_path']) ?>" target="_blank" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">📖 อ่านเอกสารฉบับเต็ม (PDF)</a>
        </div>
      </div>

      <!-- Right: Evaluation Form -->
      <div class="card">
        <h2>✍️ แบบประเมินการนำเสนอผลงาน (รอบที่ 2)</h2>
        <p class="text-muted mb-3">กรุณาระบุคะแนนทักษะการพรีเซนต์และการถามตอบตามเกณฑ์จริง (ห้ามเกินคะแนนเต็ม)</p>

        <form action="<?= base_url('committee/submitEvaluation/' . $review['id']); ?>" method="POST">
          <?= csrf_field(); ?>

          <!-- Dynamic Criteria Fields -->
          <?php foreach ($criteria as $crit): ?>
            <div class="form-group" style="background: rgba(255, 255, 255, 0.02); border: 1px solid var(--card-border); padding: 1rem; border-radius: var(--radius-sm);">
              <div class="flex justify-between mb-1">
                <strong><?= esc($crit['criteria_name']) ?></strong>
                <span class="text-muted" style="font-size: 0.85rem;">คะแนนเต็ม <strong><?= esc($crit['max_score']) ?></strong> คะแนน</span>
              </div>
              <p class="text-muted mb-2" style="font-size: 0.8rem;"><?= esc($crit['description']) ?></p>
              
              <div class="flex align-center gap-1">
                <input type="number" 
                       name="scores[<?= $crit['id'] ?>]" 
                       class="form-control" 
                       style="max-width: 120px;" 
                       min="0" 
                       max="<?= esc($crit['max_score']) ?>" 
                       value="<?= isset($scores[$crit['id']]) ? esc($scores[$crit['id']]) : '' ?>"
                       required>
                <span style="font-size: 0.9rem;">คะแนน</span>
              </div>
            </div>
          <?php endforeach; ?>

          <!-- Comments -->
          <div class="form-group mt-2">
            <label class="form-label" for="comments">ความคิดเห็นและข้อเสนอแนะเพิ่มเติมเกี่ยวกับการเสนอ</label>
            <textarea id="comments" name="comments" class="form-control" rows="4" placeholder="ระบุคอมเมนต์เพื่อประโยชน์ในการพัฒนาทักษะวิชาการและการสื่อสารของผู้พรีเซนต์..." required><?= esc($review['comments']) ?></textarea>
          </div>



          <button type="submit" class="btn btn-success" style="width: 100%; margin-top: 1rem; background: var(--success);" onclick="return confirm('ยืนยันบันทึกคะแนนนำเสนอนี้?')">💾 ส่งผลการประเมินการนำเสนอ</button>
        </form>
      </div>

    </div>

  </div>

</body>
</html>
