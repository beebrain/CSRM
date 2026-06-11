<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Author Dashboard - CSRM</title>
  <link rel="stylesheet" href="<?= base_url('css/style.css'); ?>">
</head>
<body>

  <!-- Navigation -->
  <nav class="navbar animate-fade-in">
    <div class="navbar-brand">
      🚀 CSRM Author Dashboard
    </div>
    <div class="navbar-menu">
      <span class="text-muted">ผู้แต่ง: <strong><?= session()->get('first_name') ?></strong></span>
      <a href="<?= base_url('author/dashboard'); ?>" class="navbar-item active">บทความของฉัน</a>
      <a href="<?= base_url('author/payment'); ?>" class="navbar-item">ชำระเงินค่าลงทะเบียน</a>
      <a href="<?= base_url('auth/logout'); ?>" class="btn btn-secondary btn-sm">ออกจากระบบ</a>
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
    <?php if (session()->getFlashdata('errors')): ?>
      <div class="alert alert-danger">
        <ul style="padding-left: 1rem; margin: 0;">
          <?php foreach (session()->getFlashdata('errors') as $err): ?>
            <li><?= esc($err) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <!-- Active Conference Info -->
    <div class="card mb-3" style="background: linear-gradient(135deg, rgba(99, 102, 241, 0.1) 0%, rgba(168, 85, 247, 0.05) 100%);">
      <?php if ($activeConf): ?>
        <h2>📢 การเปิดรับผลงานปัจจุบัน: <?= esc($activeConf['title']) ?></h2>
        <p class="text-muted mt-1">เจ้าภาพ: <strong><?= esc($activeConf['host_name']) ?></strong> | ค่าลงทะเบียนบทความละ 1,000 บาท</p>
      <?php else: ?>
        <h2>❌ ขณะนี้ระบบปิดรับส่งผลงานการประชุมวิชาการประจำปี</h2>
        <p class="text-muted mt-1">กรุณารอแอดมินหรือเจ้าภาพดำเนินการเปิดการประชุมวิชาการรอบปีการจัดงานใหม่</p>
      <?php endif; ?>
    </div>

    <div class="grid-3" style="grid-template-columns: 2fr 1fr;">
      
      <!-- List of Submitted Papers -->
      <div class="card">
        <h2>📄 บทความวิชาการของฉัน</h2>
        <p class="text-muted mb-2">ติดตามสถานะและการประเมินผลงานของคุณ</p>

        <div class="table-responsive">
          <table class="table">
            <thead>
              <tr>
                <th>ชื่อบทความ / เลขไอดี</th>
                <th>ศาสตร์ย่อย</th>
                <th>สถานะประเมิน</th>
                <th>การเงิน</th>
                <th>ห้องนำเสนอ/วันเวลา</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($papers)): ?>
                <tr>
                  <td colspan="5" class="text-center text-muted" style="padding: 2rem;">คุณยังไม่ได้ส่งบทความวิชาการเข้าร่วม</td>
                </tr>
              <?php else: ?>
                <?php foreach ($papers as $paper): ?>
                  <tr>
                    <td>
                      <div style="font-weight: 600;"><?= esc($paper['title']) ?></div>
                      <small class="text-muted">ปีการประชุม: <?= esc($paper['conference_year']) ?></small>
                      <?php if (!empty($paper['keywords'])): ?>
                        <div style="font-size: 0.75rem;" class="text-muted">คำสำคัญ: <?= esc($paper['keywords']) ?></div>
                      <?php endif; ?>
                      <div class="mt-1">
                        <a href="<?= base_url($paper['file_path']) ?>" target="_blank" class="btn btn-secondary btn-sm" style="padding: 0.1rem 0.5rem; font-size: 0.75rem;">📖 เปิดอ่าน PDF</a>
                      </div>

                      <!-- Revision upload section -->
                      <?php if ($paper['status'] === 'revision_required'): ?>
                        <div class="mt-2" style="padding: 1rem; background: var(--warning-bg); border: 1px solid rgba(217, 119, 6, 0.15); border-radius: var(--radius-sm);">
                          <strong style="color: #92400e; font-size: 0.85rem;">📬 ข้อแนะนำการแก้ไขจากผู้ประเมิน:</strong>
                          <ul style="padding-left: 1.25rem; font-size: 0.8rem; color: #92400e; margin: 0.5rem 0 0.75rem 0;">
                            <?php if (empty($comments[$paper['id']])): ?>
                              <li>ไม่มีคอมเมนต์เพิ่มเติมระบุไว้</li>
                            <?php else: ?>
                              <?php foreach ($comments[$paper['id']] as $comment): ?>
                                <?php if (!empty(trim($comment['comments']))): ?>
                                  <li>"<?= esc($comment['comments']) ?>"</li>
                                <?php endif; ?>
                              <?php endforeach; ?>
                            <?php endif; ?>
                          </ul>
                          <form action="<?= base_url('author/submitRevision'); ?>" method="POST" enctype="multipart/form-data">
                            <?= csrf_field(); ?>
                            <input type="hidden" name="paper_id" value="<?= $paper['id'] ?>">
                            <div class="form-group mb-2">
                              <label class="form-label" style="font-size: 0.75rem;" for="comments_<?= $paper['id'] ?>">คำชี้แจงการแก้ไข (Response comments)</label>
                              <textarea id="comments_<?= $paper['id'] ?>" name="comments" class="form-control" rows="2" placeholder="อธิบายจุดที่ได้แก้ไขตามคอมเมนต์..." required style="font-size: 0.8rem; padding: 0.5rem;"></textarea>
                            </div>
                            <div class="form-group mb-2">
                              <label class="form-label" style="font-size: 0.75rem;" for="rev_file_<?= $paper['id'] ?>">ไฟล์บทความวิชาการฉบับแก้ไข (PDF)</label>
                              <input type="file" id="rev_file_<?= $paper['id'] ?>" name="pdf_file" class="form-control" accept="application/pdf" required style="font-size: 0.8rem; padding: 0.25rem 0.5rem;">
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm" style="width: 100%; font-size: 0.8rem; padding: 0.4rem;">🚀 ส่งไฟล์เอกสารฉบับแก้ไข</button>
                          </form>
                        </div>
                      <?php endif; ?>
                      <?php if (!empty($presentationComments[$paper['id']])): ?>
                        <div class="mt-2" style="padding: 1rem; background: var(--success-bg); border: 1px solid rgba(22, 163, 74, 0.15); border-radius: var(--radius-sm);">
                          <strong style="color: var(--success); font-size: 0.85rem;">📢 ความคิดเห็นการนำเสนอผลงาน:</strong>
                          <ul style="padding-left: 1.25rem; font-size: 0.8rem; color: var(--text-secondary); margin: 0.5rem 0 0 0; list-style-type: disc;">
                            <?php foreach ($presentationComments[$paper['id']] as $pcomm): ?>
                              <li style="margin-bottom: 0.25rem;">
                                "<?= esc($pcomm['comments']) ?>" 
                                <span class="text-muted" style="font-size: 0.75rem;">— โดย <?= esc($pcomm['first_name']) ?> <?= esc($pcomm['last_name']) ?></span>
                              </li>
                            <?php endforeach; ?>
                          </ul>
                        </div>
                      <?php endif; ?>
                    </td>
                    <td>
                      <div><?= esc($paper['track_name']) ?></div>
                      <small class="text-muted"><?= esc($paper['discipline_name']) ?></small>
                    </td>
                    <td>
                      <?php if ($paper['status'] === 'submitted'): ?>
                        <span class="badge badge-pending">รอตรวจสอบเอกสาร</span>
                      <?php elseif ($paper['status'] === 'under_review'): ?>
                        <span class="badge badge-info">กำลังประเมินรอบแรก (Peer)</span>
                      <?php elseif ($paper['status'] === 'revision_required'): ?>
                        <span class="badge badge-pending">ต้องแก้ไขบทความ</span>
                      <?php elseif ($paper['status'] === 'revised_submitted'): ?>
                        <span class="badge badge-info">ส่งไฟล์แก้ไขแล้ว</span>
                      <?php elseif ($paper['status'] === 'passed_round1'): ?>
                        <span class="badge badge-success">ผ่านรอบแรก (รอพรีเซนต์)</span>
                      <?php elseif ($paper['status'] === 'failed_round1'): ?>
                        <span class="badge badge-danger">ไม่ผ่านรอบแรก</span>
                      <?php elseif ($paper['status'] === 'passed_round2'): ?>
                        <span class="badge badge-success" style="background: rgba(16, 185, 129, 0.3); border: 1px solid var(--success);">เสร็จสิ้นการนำเสนอ</span>
                        <?php if (isset($paper['presentation_score']) && $paper['presentation_score'] !== null): ?>
                          <div style="font-size: 0.8rem; font-weight: bold; margin-top: 0.25rem; color: var(--success);">
                            คะแนนเฉลี่ย: <?= number_format($paper['presentation_score'], 2) ?> คะแนน
                          </div>
                        <?php endif; ?>
                      <?php elseif ($paper['status'] === 'failed_round2'): ?>
                        <span class="badge badge-danger">ไม่ผ่านรอบนำเสนอ</span>
                      <?php endif; ?>
                    </td>
                    <td>
                      <?php if ($paper['payment_status'] === 'paid'): ?>
                        <span class="badge badge-success">ชำระเงินแล้ว</span>
                      <?php elseif ($paper['payment_status'] === 'pending_verification'): ?>
                        <span class="badge badge-pending">รอตรวจสอบสลิป</span>
                      <?php else: ?>
                        <span class="badge badge-danger">ยังไม่จ่ายเงิน</span>
                      <?php endif; ?>
                    </td>
                    <td>
                      <!-- Scheduled Room info -->
                      <?php 
                        $sched = $schedules[$paper['id']];
                        if ($sched): 
                      ?>
                        <div style="font-size: 0.85rem;">
                          <strong style="color: var(--primary);"><?= esc($sched['room_name']) ?></strong>
                          <div class="text-muted" style="font-size: 0.75rem;">สถานที่: <?= esc($sched['location']) ?></div>
                          <div class="text-muted" style="font-size: 0.75rem;">เวลานำเสนอ: <?= esc($sched['presentation_time']) ?></div>
                        </div>
                      <?php else: ?>
                        <span class="text-muted" style="font-size: 0.8rem;">รอผู้ดูแลจัดห้องนำเสนอ...</span>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Submit Form -->
      <div class="card" style="height: fit-content;">
        <h2>✍️ ส่งบทความวิชาการใหม่</h2>
        <p class="text-muted mb-3">อัปโหลดเอกสารบทความเพื่อเริ่มกระบวนการประเมิน</p>

        <?php if ($activeConf && $activeConf['accept_submissions']): ?>
          <form action="<?= base_url('author/submitPaper'); ?>" method="POST" enctype="multipart/form-data">
            <?= csrf_field(); ?>
            
            <div class="form-group">
              <label class="form-label" for="title">ชื่อบทความวิชาการ</label>
              <input type="text" id="title" name="title" class="form-control" placeholder="ระบุชื่อภาษาไทย/อังกฤษ" required value="<?= old('title'); ?>">
            </div>

            <div class="form-group">
              <label class="form-label" for="discipline_id">ศาสตร์ทางคณิตศาสตร์ / ประเภท</label>
              <select id="discipline_id" name="discipline_id" class="form-control" required>
                <option value="">เลือกศาสตร์ทางคณิตศาสตร์</option>
                <?php foreach ($disciplines as $disc): ?>
                  <option value="<?= $disc['id'] ?>" <?= old('discipline_id') == $disc['id'] ? 'selected' : ''; ?>>
                    [<?= esc($disc['track_name']) ?>] <?= esc($disc['name']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="form-group">
              <label class="form-label" for="abstract">บทคัดย่อ (Abstract)</label>
              <textarea id="abstract" name="abstract" class="form-control" rows="4" placeholder="ระบุบทคัดย่อเชิงสรุป..." required><?= old('abstract'); ?></textarea>
            </div>

            <div class="form-group">
              <label class="form-label" for="keywords">คำสำคัญ / คีย์เวิร์ด (แยกคำด้วยเครื่องหมายจุลภาค ,)</label>
              <input type="text" id="keywords" name="keywords" class="form-control" placeholder="เช่น Algebra, Matrices, Ring" value="<?= old('keywords'); ?>" required>
            </div>

            <div class="form-group">
              <label class="form-label" for="pdf_file">ไฟล์บทความวิชาการ (PDF เท่านั้น)</label>
              <input type="file" id="pdf_file" name="pdf_file" class="form-control" accept="application/pdf" required>
              <small class="text-muted" style="font-size: 0.75rem;">ขนาดไฟล์สูงสุดไม่เกิน 10MB</small>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">➕ ส่งบทความวิชาการ</button>
          </form>
        <?php else: ?>
          <div class="text-center text-muted" style="padding: 2rem 0;">
            <p><?= !$activeConf ? 'ระบบปิดรับสมัครส่งผลงานบทความวิชาการในขณะนี้' : 'ขออภัย ขณะนี้ปิดรับบทความสำหรับการประชุมรอบปีนี้แล้ว' ?></p>
          </div>
        <?php endif; ?>
      </div>

    </div>

  </div>

</body>
</html>
