<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard - CSRM</title>
  <link rel="stylesheet" href="<?= base_url('css/style.css'); ?>">
  <style>
    .grid-stats {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
      gap: 1.5rem;
      margin-bottom: 2rem;
    }
    .stat-card {
      padding: 1.25rem;
      border-radius: var(--radius-md);
      background: rgba(255, 255, 255, 0.03);
      border: 1px solid var(--card-border);
      text-align: center;
    }
    .stat-val {
      font-size: 1.75rem;
      font-weight: 700;
      color: var(--primary);
      margin-top: 0.25rem;
    }
    .reviewer-badge {
      display: inline-block;
      margin: 0.15rem;
      padding: 0.2rem 0.5rem;
      font-size: 0.75rem;
      border-radius: 4px;
      background: rgba(255, 255, 255, 0.05);
    }
  </style>
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
      <a href="<?= base_url('admin/dashboard'); ?>" class="navbar-item active">บทความ</a>
      <a href="<?= base_url('admin/disciplines'); ?>" class="navbar-item">ศาสตร์ย่อย</a>
      <a href="<?= base_url('admin/criteria'); ?>" class="navbar-item">เกณฑ์ประเมิน</a>
      <a href="<?= base_url('admin/rooms'); ?>" class="navbar-item">จัดห้องพรีเซนต์</a>
      <a href="<?= base_url('admin/payments'); ?>" class="navbar-item">ยืนยันเงิน</a>
      <a href="<?= base_url('auth/logout'); ?>" class="btn btn-secondary btn-sm">ออกระบบ</a>
    </div>
  </nav>

  <div class="container animate-fade-in" style="animation-delay: 0.1s;">
    
    <!-- Header -->
    <div class="card mb-3" style="background: linear-gradient(135deg, rgba(99, 102, 241, 0.1) 0%, rgba(168, 85, 247, 0.05) 100%);">
      <h1>ปีการประชุม พ.ศ. <?= esc($selectedConference['year']) ?></h1>
      <p class="text-muted" style="margin-top: 0.25rem;">
        เจ้าภาพ: <strong><?= esc($selectedConference['host_name']) ?></strong> | 
        หัวข้อ: <?= esc($selectedConference['title']) ?>
      </p>
    </div>

    <!-- Alert Messages -->
    <?php if (session()->getFlashdata('success')): ?>
      <div class="alert alert-success"><?= session()->getFlashdata('success'); ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
      <div class="alert alert-danger"><?= session()->getFlashdata('error'); ?></div>
    <?php endif; ?>

    <!-- Statistics -->
    <div class="grid-stats">
      <div class="stat-card">
        <div class="text-muted">บทความทั้งหมด</div>
        <div class="stat-val"><?= $stats['total_papers'] ?></div>
      </div>
      <div class="stat-card">
        <div class="text-muted">อยู่ระหว่างประเมิน</div>
        <div class="stat-val"><?= $stats['under_review'] ?></div>
      </div>
      <div class="stat-card">
        <div class="text-muted">ผ่านรอบแรก (Peer)</div>
        <div class="stat-val"><?= $stats['passed_round1'] ?></div>
      </div>
      <div class="stat-card">
        <div class="text-muted">นำเสนอเสร็จสิ้น</div>
        <div class="stat-val"><?= $stats['passed_round2'] ?></div>
      </div>
      <div class="stat-card">
        <div class="text-muted">ชำระเงินแล้ว</div>
        <div class="stat-val" style="color: var(--success);"><?= $stats['paid'] ?></div>
      </div>
      <div class="stat-card">
        <div class="text-muted">ยังไม่ชำระเงิน</div>
        <div class="stat-val" style="color: var(--danger);"><?= $stats['unpaid'] ?></div>
      </div>
    </div>

    <!-- Papers List -->
    <div class="card">
      <h2>📄 รายการบทความวิชาการประจำปี</h2>
      
      <div class="table-responsive">
        <table class="table">
          <thead>
            <tr>
              <th>ชื่อบทความ / ผู้แต่ง</th>
              <th>ประเภท / ศาสตร์ย่อย</th>
              <th>สถานะประเมิน</th>
              <th>การชำระเงิน</th>
              <th>ผู้ทรงคุณวุฒิประเมิน (รอบ 1)</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($papers)): ?>
              <tr>
                <td colspan="5" class="text-center text-muted">ยังไม่มีการส่งบทความเข้าร่วมในปีนี้</td>
              </tr>
            <?php else: ?>
              <?php foreach ($papers as $paper): ?>
                <tr>
                  <td style="max-width: 300px;">
                    <div style="font-weight: 600;"><?= esc($paper['title']) ?></div>
                    <small class="text-muted">ผู้เขียน: <?= esc($paper['author_first_name']) ?> <?= esc($paper['author_last_name']) ?> (<?= esc($paper['author_email']) ?>)</small>
                    
                    <?php if (!empty($paper['keywords'])): ?>
                      <div style="font-size: 0.8rem; margin-top: 0.25rem;">
                        <span style="font-weight: 600; color: var(--primary);">Keywords:</span> 
                        <span class="text-muted"><?= esc($paper['keywords']) ?></span>
                      </div>
                    <?php endif; ?>

                    <div class="mt-1">
                      <a href="<?= base_url($paper['file_path']) ?>" target="_blank" class="btn btn-secondary btn-sm" style="padding: 0.1rem 0.5rem; font-size: 0.75rem;">📖 เปิดอ่าน PDF (ต้นฉบับ)</a>
                    </div>

                    <?php if (!empty($paperRevisions[$paper['id']])): ?>
                      <div class="mt-2" style="font-size: 0.75rem; border-top: 1px dashed var(--card-border); padding-top: 0.25rem;">
                        <strong style="color: var(--primary);">ประวัติไฟล์แก้ไข:</strong>
                        <ul style="padding-left: 1rem; margin: 0.15rem 0 0 0; list-style-type: disc;">
                          <?php foreach ($paperRevisions[$paper['id']] as $idx => $rev): ?>
                            <li style="margin-bottom: 0.25rem;">
                              <a href="<?= base_url($rev['file_path']) ?>" target="_blank" style="font-weight: 500;">ฉบับแก้ไข #<?= $idx + 1 ?></a>
                              <span class="text-muted">(<?= date('d M Y H:i', strtotime($rev['created_at'])) ?>)</span>
                              <?php if (!empty($rev['comments'])): ?>
                                <div style="font-style: italic; color: var(--text-secondary); margin-top: 0.05rem; line-height: 1.2;">
                                  "<?= esc($rev['comments']) ?>"
                                </div>
                              <?php endif; ?>
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
                      <span class="badge badge-pending">รอตรวจ/รอจ่ายเงิน</span>
                    <?php elseif ($paper['status'] === 'under_review'): ?>
                      <span class="badge badge-info">กำลังประเมินรอบแรก</span>
                    <?php elseif ($paper['status'] === 'revision_required'): ?>
                      <span class="badge badge-pending" style="background: rgba(217, 119, 6, 0.15); color: var(--warning); border: 1px solid var(--warning);">ต้องการแก้ไข</span>
                    <?php elseif ($paper['status'] === 'revised_submitted'): ?>
                      <span class="badge badge-info" style="background: rgba(2, 132, 199, 0.15); color: var(--info); border: 1px solid var(--info);">ส่งฉบับแก้ไขแล้ว</span>
                    <?php elseif ($paper['status'] === 'passed_round1'): ?>
                      <span class="badge badge-success">ผ่านรอบแรก (รอพรีเซนต์)</span>
                    <?php elseif ($paper['status'] === 'failed_round1'): ?>
                      <span class="badge badge-danger">ไม่ผ่านรอบแรก</span>
                    <?php elseif ($paper['status'] === 'passed_round2'): ?>
                      <span class="badge badge-success" style="background: rgba(16, 185, 129, 0.3); border: 1px solid var(--success);">เสร็จสิ้นการนำเสนอ</span>
                      <?php if (isset($paper['presentation_score']) && $paper['presentation_score'] !== null): ?>
                        <div style="font-size: 0.8rem; font-weight: bold; margin-top: 0.25rem; color: var(--success);">
                          เฉลี่ย: <?= number_format($paper['presentation_score'], 2) ?> คะแนน
                        </div>
                      <?php endif; ?>
                    <?php elseif ($paper['status'] === 'failed_round2'): ?>
                      <span class="badge badge-danger">ไม่ผ่านการนำเสนอ</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <?php if ($paper['payment_status'] === 'paid'): ?>
                      <span class="badge badge-success">ชำระเงินแล้ว</span>
                    <?php elseif ($paper['payment_status'] === 'pending_verification'): ?>
                      <span class="badge badge-pending">รออนุมัติสลิป</span>
                    <?php else: ?>
                      <span class="badge badge-danger">ยังไม่ชำระเงิน</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <!-- Reviewer assignment / status info -->
                    <?php 
                      $assigns = $paperAssignments[$paper['id']];
                      if (empty($assigns)): 
                    ?>
                      <!-- Need 3 reviewers -->
                      <?php
                        $paperKeywords = [];
                        if (!empty($paper['keywords'])) {
                            $rawKeywords = explode(',', $paper['keywords']);
                            foreach ($rawKeywords as $k) {
                                $trimmed = strtolower(trim($k));
                                if ($trimmed !== '') {
                                    $paperKeywords[] = $trimmed;
                                }
                            }
                        }
                      ?>
                      <form action="<?= base_url('admin/assignReviewers'); ?>" method="POST">
                        <?= csrf_field(); ?>
                        <input type="hidden" name="paper_id" value="<?= $paper['id'] ?>">
                        <div class="form-group mb-1">
                          <select name="reviewer_ids[]" class="form-control" style="font-size: 0.8rem; padding: 0.25rem;" required>
                            <option value="">เลือกคนที่ 1</option>
                            <?php foreach ($reviewers as $r): ?>
                              <?php
                                $rId = $r['id'];
                                $rKeywords = isset($reviewerExpertise[$rId]) ? $reviewerExpertise[$rId] : [];
                                $matches = array_intersect($paperKeywords, $rKeywords);
                                $isMatch = !empty($matches);
                                $matchText = '';
                                if ($isMatch) {
                                    $matchWords = [];
                                    foreach ($matches as $m) {
                                        $matchWords[] = ucwords($m);
                                    }
                                    $matchText = ' (Match: ' . implode(', ', $matchWords) . ')';
                                }
                              ?>
                              <option value="<?= $r['id'] ?>" <?= $isMatch ? 'style="color: var(--success); font-weight: 600;"' : '' ?>>
                                <?= esc($r['first_name']) ?> <?= esc($r['last_name']) ?><?= esc($matchText) ?>
                              </option>
                            <?php endforeach; ?>
                          </select>
                        </div>
                        <div class="form-group mb-1">
                          <select name="reviewer_ids[]" class="form-control" style="font-size: 0.8rem; padding: 0.25rem;" required>
                            <option value="">เลือกคนที่ 2</option>
                            <?php foreach ($reviewers as $r): ?>
                              <?php
                                $rId = $r['id'];
                                $rKeywords = isset($reviewerExpertise[$rId]) ? $reviewerExpertise[$rId] : [];
                                $matches = array_intersect($paperKeywords, $rKeywords);
                                $isMatch = !empty($matches);
                                $matchText = '';
                                if ($isMatch) {
                                    $matchWords = [];
                                    foreach ($matches as $m) {
                                        $matchWords[] = ucwords($m);
                                    }
                                    $matchText = ' (Match: ' . implode(', ', $matchWords) . ')';
                                }
                              ?>
                              <option value="<?= $r['id'] ?>" <?= $isMatch ? 'style="color: var(--success); font-weight: 600;"' : '' ?>>
                                <?= esc($r['first_name']) ?> <?= esc($r['last_name']) ?><?= esc($matchText) ?>
                              </option>
                            <?php endforeach; ?>
                          </select>
                        </div>
                        <div class="form-group mb-2">
                          <select name="reviewer_ids[]" class="form-control" style="font-size: 0.8rem; padding: 0.25rem;" required>
                            <option value="">เลือกคนที่ 3</option>
                            <?php foreach ($reviewers as $r): ?>
                              <?php
                                $rId = $r['id'];
                                $rKeywords = isset($reviewerExpertise[$rId]) ? $reviewerExpertise[$rId] : [];
                                $matches = array_intersect($paperKeywords, $rKeywords);
                                $isMatch = !empty($matches);
                                $matchText = '';
                                if ($isMatch) {
                                    $matchWords = [];
                                    foreach ($matches as $m) {
                                        $matchWords[] = ucwords($m);
                                    }
                                    $matchText = ' (Match: ' . implode(', ', $matchWords) . ')';
                                }
                              ?>
                              <option value="<?= $r['id'] ?>" <?= $isMatch ? 'style="color: var(--success); font-weight: 600;"' : '' ?>>
                                <?= esc($r['first_name']) ?> <?= esc($r['last_name']) ?><?= esc($matchText) ?>
                              </option>
                            <?php endforeach; ?>
                          </select>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm" style="font-size: 0.75rem; padding: 0.25rem 0.5rem; width: 100%;">👤 ส่งให้ผู้ทรง 3 ท่าน</button>
                      </form>
                    <?php else: ?>
                      <!-- Show reviewer statuses -->
                      <div class="flex flex-column gap-1">
                        <?php foreach ($assigns as $a): ?>
                          <div class="reviewer-badge">
                            <strong><?= esc($a['first_name']) ?> <?= esc($a['last_name']) ?></strong>: 
                            <?php if ($a['status'] === 'pending'): ?>
                              <span class="text-muted" style="font-size: 0.7rem;">รอ...</span>
                            <?php else: ?>
                              <?php if ($a['decision'] === 'pass'): ?>
                                <span style="font-size: 0.7rem; color: var(--success); font-weight: 600;">ผ่าน</span>
                              <?php elseif ($a['decision'] === 'revision'): ?>
                                <span style="font-size: 0.7rem; color: var(--warning); font-weight: 600;">แก้ไข</span>
                              <?php else: ?>
                                <span style="font-size: 0.7rem; color: var(--danger); font-weight: 600;">ไม่ผ่าน</span>
                              <?php endif; ?>
                            <?php endif; ?>
                          </div>
                        <?php endforeach; ?>
                      </div>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

  </div>

</body>
</html>
