<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ประเมินบทความวิชาการ - CSRM</title>
  <link rel="stylesheet" href="<?= base_url('css/style.css'); ?>">
</head>
<body>

  <!-- Navigation -->
  <nav class="navbar animate-fade-in">
    <div class="navbar-brand">
      🔍 CSRM Reviewer Dashboard
    </div>
    <div class="navbar-menu">
      <span class="text-muted">ผู้ทรงคุณวุฒิ: <strong><?= session()->get('first_name') ?></strong></span>
      <a href="<?= base_url('reviewer/dashboard'); ?>" class="navbar-item">หน้าแรกแดชบอร์ด</a>
      <a href="<?= base_url('auth/logout'); ?>" class="btn btn-secondary btn-sm">ออกจากระบบ</a>
    </div>
  </nav>

  <div class="container animate-fade-in" style="animation-delay: 0.1s;">
    
    <a href="<?= base_url('reviewer/dashboard'); ?>" class="btn btn-secondary btn-sm mb-2">⬅️ กลับไปหน้าหลัก</a>

    <div class="grid-3" style="grid-template-columns: 1fr 1.5fr;">
      
      <!-- Left: Paper Info -->
      <div class="card" style="height: fit-content;">
        <h2>📖 ข้อมูลบทความวิชาการ</h2>
        <div style="margin-top: 1rem; border-top: 1px solid var(--card-border); padding-top: 1rem;">
          <small class="text-muted">ชื่อเรื่อง:</small>
          <h3 class="mb-2" style="color: var(--primary);"><?= esc($review['title']) ?></h3>
          
          <small class="text-muted">บทคัดย่อ (Abstract):</small>
          <p class="text-muted mb-2" style="font-size: 0.9rem; text-align: justify; line-height: 1.5; max-height: 250px; overflow-y: auto; padding: 0.5rem; background: rgba(0,0,0,0.2); border-radius: 4px;">
            <?= nl2br(esc($review['abstract'])) ?>
          </p>

          <a href="<?= base_url($review['file_path']) ?>" target="_blank" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">📖 อ่านบทความตัวเต็ม (PDF)</a>

          <!-- Revision history display -->
          <?php if (!empty($revisions)): ?>
            <div class="mt-3" style="border-top: 1px dashed var(--card-border); padding-top: 1rem;">
              <h4 style="font-size: 0.9rem; color: var(--primary);" class="mb-2">🔄 ประวัติการส่งไฟล์แก้ไข (Revisions)</h4>
              <ul style="list-style: none; font-size: 0.85rem; padding: 0;">
                <?php foreach ($revisions as $index => $rev): ?>
                  <li class="mb-2" style="padding: 0.75rem; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: var(--radius-sm);">
                    <div><strong>ฉบับแก้ไขครั้งที่ <?= $index + 1 ?></strong></div>
                    <div class="text-muted" style="font-size: 0.75rem;">ส่งเมื่อ: <?= $rev['created_at'] ?></div>
                    <div style="margin: 0.25rem 0; font-style: italic; font-size: 0.8rem;">"คำชี้แจง: <?= esc($rev['comments']) ?>"</div>
                    <a href="<?= base_url($rev['file_path']) ?>" target="_blank" style="font-weight: 600; text-decoration: underline; font-size: 0.8rem;">📖 อ่านฉบับแก้ไขนี้ (PDF)</a>
                  </li>
                <?php endforeach; ?>
              </ul>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- Right: Evaluation Form -->
      <div class="card">
        <h2>✍️ แบบประเมินบทความวิชาการ (รอบที่ 1)</h2>
        <p class="text-muted mb-3">กรุณาระบุคะแนนในแต่ละเกณฑ์ (ห้ามเกินคะแนนเต็ม) พร้อมใส่ข้อเสนอแนะและผลการตัดสิน</p>

        <form action="<?= base_url('reviewer/submitEvaluation/' . $review['id']); ?>" method="POST">
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
            <label class="form-label" for="comments">ข้อวิจารณ์และข้อเสนอแนะเพิ่มเติม</label>
            <textarea id="comments" name="comments" class="form-control" rows="4" placeholder="ระบุความคิดเห็นที่เป็นประโยชน์ต่อบทความเพื่อให้นักวิจัยไปปรับปรุง..." required><?= esc($review['comments']) ?></textarea>
          </div>

          <!-- Final Decision -->
          <div class="form-group">
            <label class="form-label" for="decision">ผลการตัดสินในส่วนของท่าน</label>
            <select id="decision" name="decision" class="form-control" required style="border-color: var(--primary);">
              <option value="">เลือกผลการตัดสิน</option>
              <option value="pass" <?= $review['decision'] === 'pass' ? 'selected' : '' ?>>ผ่าน (Pass) - สมควรเข้าร่วมนำเสนอผลงาน</option>
              <option value="revision" <?= $review['decision'] === 'revision' ? 'selected' : '' ?>>ควรแก้ไขบทความ (Revision) - ให้ส่งกลับไปปรับปรุงแก้ไขตามคอมเมนต์</option>
              <option value="fail" <?= $review['decision'] === 'fail' ? 'selected' : '' ?>>ไม่ผ่าน (Fail) - ข้อมูลยังไม่สมบูรณ์หรือไม่ตรงเกณฑ์</option>
            </select>
          </div>

          <button type="submit" class="btn btn-success" style="width: 100%; margin-top: 1rem; background: var(--success);" onclick="return confirm('ยืนยันส่งผลการประเมินนี้? เมื่อส่งแล้วสามารถกลับมาแก้ไขได้จนกว่างานจะปิดรับผล')">💾 ส่งผลการประเมินบทความ</button>
        </form>
      </div>

    </div>

  </div>

</body>
</html>
