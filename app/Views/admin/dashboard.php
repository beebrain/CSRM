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
      <?php if (session()->get('role') === 'superadmin'): ?>
        <a href="<?= base_url('superadmin/dashboard'); ?>" class="btn btn-primary btn-sm" style="font-size: 0.85rem; padding: 0.5rem 1rem;">⚙️ กลับหน้า SuperAdmin</a>
      <?php endif; ?>
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

    <!-- Search & Filters -->
    <div class="card mb-3" style="padding: 1.5rem;">
      <h3 style="font-size: 1.1rem; margin-bottom: 1rem; color: var(--text-primary); display: flex; align-items: center; gap: 0.5rem;">
        🔍 ค้นหาและกรองข้อมูลบทความ
      </h3>
      <div class="grid-filters" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; align-items: end;">
        <!-- Search bar -->
        <div class="form-group" style="margin-bottom: 0;">
          <label class="form-label" for="search-input" style="font-size: 0.8rem; margin-bottom: 0.25rem;">🔍 ค้นหา (ชื่อบทความ, ผู้แต่ง, สถาบัน หรืออีเมล)</label>
          <input type="text" id="search-input" class="form-control" placeholder="พิมพ์คำค้นหา..." style="font-size: 0.9rem; padding: 0.5rem 0.75rem;">
        </div>

        <!-- Filter by Status -->
        <div class="form-group" style="margin-bottom: 0;">
          <label class="form-label" for="filter-status" style="font-size: 0.8rem; margin-bottom: 0.25rem;">📊 สถานะประเมิน</label>
          <select id="filter-status" class="form-control" style="font-size: 0.9rem; padding: 0.5rem 0.75rem;">
            <option value="all">ทั้งหมด</option>
            <option value="submitted">รอตรวจ/รอจ่ายเงิน</option>
            <option value="under_review">กำลังประเมินรอบแรก</option>
            <option value="revision_required">ต้องการแก้ไข</option>
            <option value="revised_submitted">ส่งฉบับแก้ไขแล้ว</option>
            <option value="passed_round1">ผ่านรอบแรก (รอพรีเซนต์)</option>
            <option value="failed_round1">ไม่ผ่านรอบแรก</option>
            <option value="passed_round2">เสร็จสิ้นการนำเสนอ</option>
            <option value="failed_round2">ไม่ผ่านการนำเสนอ</option>
          </select>
        </div>

        <!-- Filter by Reviewer -->
        <div class="form-group" style="margin-bottom: 0;">
          <label class="form-label" for="filter-reviewer" style="font-size: 0.8rem; margin-bottom: 0.25rem;">👤 ผู้ทรงประเมิน</label>
          <select id="filter-reviewer" class="form-control" style="font-size: 0.9rem; padding: 0.5rem 0.75rem;">
            <option value="all">ทั้งหมด</option>
            <option value="unassigned">ยังไม่ได้มอบหมายผู้ทรง</option>
            <?php foreach ($reviewers as $r): ?>
              <option value="<?= $r['id'] ?>"><?= esc($r['first_name']) ?> <?= esc($r['last_name']) ?> (🏛️ <?= esc($r['affiliation'] ?: 'ไม่ระบุ') ?>)</option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- Reset Button -->
        <div>
          <button type="button" id="btn-reset-filters" class="btn btn-secondary" style="font-size: 0.85rem; padding: 0.5rem 1rem; width: 100%; height: 38px; justify-content: center;">
            🔄 ล้างตัวกรอง
          </button>
        </div>
      </div>
    </div>

        <!-- Papers List grouped by Track (Accordion List) -->
    <?php
      // Group papers by track_name
      $groupedPapers = [];
      foreach ($papers as $paper) {
          $track = $paper['track_name'] ?: 'อื่นๆ';
          $groupedPapers[$track][] = $paper;
      }
    ?>

    <style>
      .accordion-header:hover {
          background: rgba(37, 99, 235, 0.03);
      }
      .paper-accordion-item.open {
          box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
          border-color: rgba(37, 99, 235, 0.4) !important;
      }
      .accordion-arrow {
          display: inline-block;
          font-size: 0.7rem;
          transition: transform 0.2s;
          color: var(--text-secondary);
      }
    </style>

    <?php if (empty($papers)): ?>
      <div class="card text-center text-muted" style="padding: 3rem;">
        ยังไม่มีการส่งบทความเข้าร่วมในปีนี้
      </div>
    <?php else: ?>
      <div id="no-results-box" class="card text-center text-muted" style="display: none; padding: 3rem; margin-bottom: 1.5rem;">
        ไม่พบข้อมูลบทความวิชาการที่ตรงกับเงื่อนไขการค้นหา
      </div>

      <?php foreach ($groupedPapers as $trackName => $trackPapers): ?>
        <div class="track-group-section mb-4" data-track-name="<?= esc($trackName) ?>">
          <h3 style="font-size: 1.25rem; font-weight: 700; color: var(--primary); border-left: 4px solid var(--primary); padding-left: 0.75rem; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
            📚 กลุ่มศาสตร์: <?= esc($trackName) ?>
            <span class="badge track-badge-count" style="background: rgba(37, 99, 235, 0.1); color: var(--primary); font-size: 0.8rem; font-weight: 600; border: none; padding: 0.2rem 0.5rem;">
              <?= count($trackPapers) ?> บทความ
            </span>
          </h3>

          <div style="display: flex; flex-direction: column; gap: 0.75rem; margin-bottom: 2rem;">
            <?php foreach ($trackPapers as $paper): ?>
              <?php 
                $assigns = $paperAssignments[$paper['id']];
                $reviewerIdsStr = implode(',', array_map(function($a) { return $a['reviewer_id']; }, $assigns));
                
                // Calculate reviewer status
                $assignedCount = count($assigns);
                $completedCount = 0;
                foreach ($assigns as $a) {
                    if ($a['status'] === 'completed') {
                        $completedCount++;
                    }
                }
              ?>
              
              <div class="paper-accordion-item"
                   style="border: 1px solid var(--card-border); border-radius: var(--radius-sm); background: #ffffff; overflow: hidden; transition: var(--transition);"
                   data-title="<?= esc(mb_strtolower($paper['title'])) ?>"
                   data-author-name="<?= esc(mb_strtolower($paper['author_first_name'] . ' ' . $paper['author_last_name'])) ?>"
                   data-author-affiliation="<?= esc(mb_strtolower($paper['author_affiliation'] ?: '')) ?>"
                   data-author-email="<?= esc(mb_strtolower($paper['author_email'])) ?>"
                   data-keywords="<?= esc(mb_strtolower($paper['keywords'] ?: '')) ?>"
                   data-status="<?= esc($paper['status']) ?>"
                   data-reviewers="<?= esc($reviewerIdsStr) ?>">
                
                <!-- Accordion Header (1 Row) -->
                <div class="accordion-header" 
                     style="padding: 1rem 1.5rem; display: flex; justify-content: space-between; align-items: center; cursor: pointer; user-select: none; transition: background 0.2s;"
                     onclick="toggleAccordion(this)">
                  
                  <div style="display: flex; align-items: center; gap: 1.25rem; flex: 1; min-width: 0;">
                    <!-- Arrow indicator -->
                    <span class="accordion-arrow">▶</span>
                    
                    <div style="min-width: 0; flex: 1;">
                      <!-- Title -->
                      <div class="paper-title-text" style="font-weight: 600; font-size: 1.05rem; color: var(--text-primary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; padding-right: 1.5rem;">
                        <?= esc($paper['title']) ?>
                      </div>
                      <!-- Discipline / Branch -->
                      <div style="font-size: 0.8rem; color: var(--text-secondary); margin-top: 0.15rem; display: flex; align-items: center; gap: 0.5rem;">
                        <span>📂 <?= esc($paper['discipline_name']) ?></span>
                        <?php if (!empty($paper['keywords'])): ?>
                          <span style="color: #cbd5e1;">|</span>
                          <span style="font-size: 0.75rem; color: var(--text-secondary);">🔑 <?= esc($paper['keywords']) ?></span>
                        <?php endif; ?>
                      </div>
                    </div>
                  </div>

                  <!-- Right Side Status Icons / Badges -->
                  <div style="display: flex; align-items: center; gap: 0.75rem; flex-shrink: 0;">
                    <!-- Reviewer Status Icon -->
                    <?php if ($assignedCount === 0): ?>
                      <span class="badge" style="background: rgba(220, 38, 38, 0.08); color: var(--danger); font-size: 0.75rem; border: 1px solid rgba(220, 38, 38, 0.15); padding: 0.25rem 0.6rem; font-weight: 600;">
                        👤❓ ยังไม่ระบุผู้ประเมิน
                      </span>
                    <?php elseif ($completedCount < $assignedCount): ?>
                      <span class="badge" style="background: rgba(217, 119, 6, 0.08); color: var(--warning); font-size: 0.75rem; border: 1px solid rgba(217, 119, 6, 0.15); padding: 0.25rem 0.6rem; font-weight: 600;">
                        ⏳ มอบหมายแล้ว (ประเมินแล้ว <?= $completedCount ?>/<?= $assignedCount ?> ท่าน)
                      </span>
                    <?php else: ?>
                      <span class="badge" style="background: rgba(22, 163, 74, 0.08); color: var(--success); font-size: 0.75rem; border: 1px solid rgba(22, 163, 74, 0.15); padding: 0.25rem 0.6rem; font-weight: 600;">
                        ✅ ประเมินครบถ้วน (<?= $completedCount ?>/<?= $assignedCount ?>)
                      </span>
                    <?php endif; ?>

                    <!-- Paper Status Badge -->
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
                    <?php elseif ($paper['status'] === 'failed_round2'): ?>
                      <span class="badge badge-danger">ไม่ผ่านการนำเสนอ</span>
                    <?php endif; ?>

                    <!-- Payment Badge -->
                    <?php if ($paper['payment_status'] === 'paid'): ?>
                      <span class="badge badge-success">ชำระเงินแล้ว</span>
                    <?php elseif ($paper['payment_status'] === 'pending_verification'): ?>
                      <span class="badge badge-pending">รออนุมัติสลิป</span>
                    <?php else: ?>
                      <span class="badge badge-danger">ยังไม่ชำระเงิน</span>
                    <?php endif; ?>
                  </div>

                </div>

                <!-- Accordion Content (Collapsed by default) -->
                <div class="accordion-content" style="max-height: 0; overflow: hidden; transition: max-height 0.3s cubic-bezier(0.16, 1, 0.3, 1); background: #fafbfc; border-top: 0 solid var(--card-border);">
                  <div style="padding: 1.5rem 1.75rem; border-top: 1px solid var(--card-border);">
                    
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 2rem; flex-wrap: wrap;">
                      <!-- Left Details Column -->
                      <div style="flex: 1; min-width: 300px;">
                        
                        <!-- Paper Full Title -->
                        <h4 style="font-size: 1.25rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.75rem; line-height: 1.4;">
                          <?= esc($paper['title']) ?>
                        </h4>

                        <!-- Author Details -->
                        <div style="background: #ffffff; border: 1px solid var(--card-border); padding: 0.75rem 1rem; border-radius: var(--radius-sm); margin-bottom: 1rem; display: flex; flex-direction: column; gap: 0.35rem;">
                          <div style="font-size: 0.85rem; color: var(--text-secondary);">
                            ผู้เขียน/ผู้ส่งผลงาน: <strong style="color: var(--text-primary);"><?= esc($paper['author_first_name']) ?> <?= esc($paper['author_last_name']) ?></strong> 
                            <span class="text-muted">(<?= esc($paper['author_email']) ?>)</span>
                          </div>
                          <div style="font-size: 0.85rem; color: var(--text-secondary);">
                            🏛️ สถาบันสังกัด: <strong style="color: var(--primary);"><?= esc($paper['author_affiliation'] ?: 'ไม่ระบุ') ?></strong>
                          </div>
                        </div>

                        <!-- Abstract Collapsible -->
                        <details style="margin-bottom: 1rem; border: 1px solid var(--card-border); border-radius: var(--radius-sm); background: #ffffff;">
                          <summary style="font-size: 0.85rem; font-weight: 600; color: var(--primary); cursor: pointer; user-select: none; padding: 0.6rem 1rem;">
                            📖 แสดงบทคัดย่อ (Abstract)
                          </summary>
                          <div style="font-size: 0.9rem; color: var(--text-secondary); border-top: 1px solid var(--card-border); padding: 1rem; white-space: pre-line; line-height: 1.6; background: #fafbfc;">
                            <?= esc($paper['abstract']) ?>
                          </div>
                        </details>

                        <!-- PDF Files and Revisions -->
                        <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
                          <a href="<?= base_url($paper['file_path']) ?>" target="_blank" class="btn btn-secondary btn-sm" style="padding: 0.35rem 0.75rem; font-size: 0.8rem; border-radius: 6px;">
                            📖 เปิดอ่าน PDF (ฉบับส่งแรก)
                          </a>

                          <?php if (!empty($paperRevisions[$paper['id']])): ?>
                            <details style="position: relative; display: inline-block;">
                              <summary style="font-size: 0.8rem; font-weight: 600; color: var(--info); cursor: pointer; padding: 0.35rem 0.75rem; border: 1px solid var(--card-border); border-radius: 6px; background: #ffffff; list-style: none; display: flex; align-items: center; gap: 0.25rem;">
                                🔄 ฉบับปรับปรุงแก้ไข (<?= count($paperRevisions[$paper['id']]) ?>) ▼
                              </summary>
                              <div class="card" style="position: absolute; z-index: 100; margin-top: 0.5rem; padding: 1rem; min-width: 300px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1), 0 8px 10px -6px rgba(0,0,0,0.1); border: 1px solid var(--card-border); background: #ffffff;">
                                <h4 style="font-size: 0.85rem; font-weight: 700; margin-bottom: 0.5rem; border-bottom: 1px solid var(--card-border); padding-bottom: 0.25rem; color: var(--text-primary);">ประวัติการส่งฉบับแก้ไข</h4>
                                <ul style="padding-left: 1.25rem; margin: 0; font-size: 0.8rem; list-style-type: disc; color: var(--text-secondary);">
                                  <?php foreach ($paperRevisions[$paper['id']] as $idx => $rev): ?>
                                    <li style="margin-bottom: 0.5rem;">
                                      <a href="<?= base_url($rev['file_path']) ?>" target="_blank" style="font-weight: 600; color: var(--primary);">ฉบับแก้ไข #<?= $idx + 1 ?></a>
                                      <span style="font-size: 0.75rem; color: var(--text-secondary);">(<?= date('d M Y H:i', strtotime($rev['created_at'])) ?>)</span>
                                      <?php if (!empty($rev['comments'])): ?>
                                        <div style="font-style: italic; color: var(--text-secondary); margin-top: 0.15rem; line-height: 1.3; font-size: 0.75rem; background: #f8fafc; padding: 0.25rem 0.5rem; border-radius: 4px;">
                                          "<?= esc($rev['comments']) ?>"
                                        </div>
                                      <?php endif; ?>
                                    </li>
                                  <?php endforeach; ?>
                                </ul>
                              </div>
                            </details>
                          <?php endif; ?>
                        </div>
                      </div>

                      <!-- Right Reviewer & Status Column -->
                      <div style="width: 340px; flex-shrink: 0; display: flex; flex-direction: column; gap: 1.25rem; border-left: 1px solid var(--card-border); padding-left: 2rem;">
                        <!-- Status details and scores if available -->
                        <?php if ($paper['status'] === 'passed_round2' && isset($paper['presentation_score']) && $paper['presentation_score'] !== null): ?>
                          <div style="background: rgba(22, 163, 74, 0.05); border: 1px solid rgba(22, 163, 74, 0.15); padding: 0.75rem 1rem; border-radius: var(--radius-sm);">
                            <span style="font-size: 0.8rem; color: var(--success); font-weight: 600;">📈 คะแนนเฉลี่ยการนำเสนอ</span>
                            <div style="font-size: 1.5rem; font-weight: bold; color: var(--success); margin-top: 0.1rem;">
                              <?= number_format($paper['presentation_score'], 2) ?> คะแนน
                            </div>
                          </div>
                        <?php endif; ?>

                        <!-- Reviewers (Round 1) Assignment and status -->
                        <div>
                          <div style="font-size: 0.8rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.35rem;">
                            👤 รายละเอียดผู้ทรงคุณวุฒิ (รอบ 1)
                          </div>

                          <?php if (empty($assigns)): ?>
                            <!-- Assignment Form -->
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
                              
                              <?php for ($i = 1; $i <= 3; $i++): ?>
                                <div class="form-group mb-1">
                                  <select name="reviewer_ids[]" class="form-control" style="font-size: 0.8rem; padding: 0.25rem 0.5rem; height: 32px;" required>
                                    <option value="">เลือกคนที่ <?= $i ?></option>
                                    <?php foreach ($reviewers as $r): ?>
                                      <?php
                                        $rId = $r['id'];
                                        $rKeywords = isset($reviewerExpertise[$rId]) ? $reviewerExpertise[$rId] : [];
                                        $matches = array_intersect($paperKeywords, $rKeywords);
                                        $isMatch = !empty($matches);
                                        
                                        // Check affiliation conflict
                                        $isSameAffiliation = false;
                                        if (!empty($r['affiliation']) && !empty($paper['author_affiliation'])) {
                                            $rAff = preg_replace('/\s+/', '', mb_strtolower($r['affiliation']));
                                            $aAff = preg_replace('/\s+/', '', mb_strtolower($paper['author_affiliation']));
                                            if ($rAff === $aAff) {
                                                $isSameAffiliation = true;
                                            }
                                        }

                                        $matchText = '';
                                        if ($isMatch) {
                                            $matchWords = [];
                                            foreach ($matches as $m) {
                                                $matchWords[] = ucwords($m);
                                            }
                                            $matchText = ' (Match: ' . implode(', ', $matchWords) . ')';
                                        }

                                        $affText = $r['affiliation'] ? ' [' . $r['affiliation'] . ']' : ' [ไม่ระบุสถาบัน]';
                                      ?>
                                      <option value="<?= $r['id'] ?>" 
                                              data-uni-conflict="<?= $isSameAffiliation ? 'true' : 'false' ?>"
                                              <?= $isSameAffiliation ? 'disabled style="color: var(--danger); font-style: italic;"' : ($isMatch ? 'style="color: var(--success); font-weight: 600;"' : '') ?>>
                                        <?= esc($r['first_name']) ?> <?= esc($r['last_name']) ?><?= esc($affText) ?><?= esc($matchText) ?><?= $isSameAffiliation ? ' ⚠️ สถาบันเดียวกัน' : '' ?>
                                      </option>
                                    <?php endforeach; ?>
                                  </select>
                                </div>
                              <?php endfor; ?>
                              
                              <button type="submit" class="btn btn-primary btn-sm" style="font-size: 0.75rem; padding: 0.35rem 0.5rem; width: 100%; border-radius: 6px; margin-top: 0.5rem; justify-content: center; height: 32px;">
                                👤 ส่งให้ผู้ทรง 3 ท่าน
                              </button>
                            </form>
                          <?php else: ?>
                            <!-- Show reviewer statuses -->
                            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                              <?php foreach ($assigns as $a): ?>
                                <div class="reviewer-badge" style="display: block; margin: 0; padding: 0.5rem; background: #ffffff; border: 1px solid var(--card-border); border-radius: var(--radius-sm);">
                                  <div style="font-weight: 600; font-size: 0.8rem; color: var(--text-primary);">
                                    👤 <?= esc($a['first_name']) ?> <?= esc($a['last_name']) ?>
                                  </div>
                                  <div style="font-size: 0.7rem; color: var(--text-secondary); margin-top: 0.05rem;">
                                    🏛️ <?= esc($a['affiliation'] ?: 'ไม่ระบุสถาบัน') ?>
                                  </div>
                                  <div style="margin-top: 0.25rem; font-size: 0.75rem; display: flex; justify-content: space-between; align-items: center; border-top: 1px dashed var(--card-border); padding-top: 0.2rem;">
                                    <span class="text-muted" style="font-size: 0.7rem;">ผลประเมิน:</span>
                                    <?php if ($a['status'] === 'pending'): ?>
                                      <span style="color: var(--text-secondary); font-weight: 600; font-size: 0.7rem;">⏳ รอการประเมิน</span>
                                    <?php else: ?>
                                      <?php if ($a['decision'] === 'pass'): ?>
                                        <span style="color: var(--success); font-weight: 700; font-size: 0.7rem;">✅ ผ่าน</span>
                                      <?php elseif ($a['decision'] === 'revision'): ?>
                                        <span style="color: var(--warning); font-weight: 700; font-size: 0.7rem;">🔧 แก้ไข</span>
                                      <?php else: ?>
                                        <span style="color: var(--danger); font-weight: 700; font-size: 0.7rem;">❌ ไม่ผ่าน</span>
                                      <?php endif; ?>
                                    <?php endif; ?>
                                  </div>
                                </div>
                              <?php endforeach; ?>
                            </div>
                          <?php endif; ?>
                        </div>
                      </div>
                    </div>

                  </div>
                </div>

              </div>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>

  </div>

  <script>
  // Dynamic Accordion Open/Close
  function toggleAccordion(header) {
      const item = header.closest('.paper-accordion-item');
      const content = item.querySelector('.accordion-content');
      const arrow = item.querySelector('.accordion-arrow');
      
      const isOpen = item.classList.contains('open');
      
      if (isOpen) {
          content.style.maxHeight = content.scrollHeight + 'px';
          // Force reflow
          content.offsetHeight;
          content.style.maxHeight = '0';
          item.classList.remove('open');
          arrow.style.transform = 'rotate(0deg)';
      } else {
          content.style.maxHeight = content.scrollHeight + 'px';
          item.classList.add('open');
          arrow.style.transform = 'rotate(90deg)';
          
          content.addEventListener('transitionend', function handler() {
              if (item.classList.contains('open')) {
                  content.style.maxHeight = 'none';
              }
              content.removeEventListener('transitionend', handler);
          });
      }
  }

  document.addEventListener('DOMContentLoaded', function() {
      const searchInput = document.getElementById('search-input');
      const filterStatus = document.getElementById('filter-status');
      const filterReviewer = document.getElementById('filter-reviewer');
      const btnReset = document.getElementById('btn-reset-filters');
      const items = document.querySelectorAll('.paper-accordion-item');
      const trackSections = document.querySelectorAll('.track-group-section');
      const noResultsBox = document.getElementById('no-results-box');

      function applyFilters() {
          const query = searchInput.value.toLowerCase().trim();
          const status = filterStatus.value;
          const reviewer = filterReviewer.value;
          let visibleCount = 0;

          // 1. Filter each paper item
          items.forEach(item => {
              const title = item.getAttribute('data-title') || '';
              const authorName = item.getAttribute('data-author-name') || '';
              const authorAff = item.getAttribute('data-author-affiliation') || '';
              const keywords = item.getAttribute('data-keywords') || '';
              const authorEmail = item.getAttribute('data-author-email') || '';
              const paperStatus = item.getAttribute('data-status') || '';
              
              // Reviewer IDs are comma-separated string
              const reviewerIds = (item.getAttribute('data-reviewers') || '').split(',').filter(id => id !== '');

              // Check search query
              const matchesSearch = title.includes(query) || 
                                    authorName.includes(query) || 
                                    authorAff.includes(query) || 
                                    authorEmail.includes(query) ||
                                    keywords.includes(query);

              // Check status filter
              const matchesStatus = (status === 'all') || (paperStatus === status);

              // Check reviewer filter
              let matchesReviewer = false;
              if (reviewer === 'all') {
                  matchesReviewer = true;
              } else if (reviewer === 'unassigned') {
                  matchesReviewer = (reviewerIds.length === 0);
              } else {
                  matchesReviewer = reviewerIds.includes(reviewer);
              }

              // Set visibility
              if (matchesSearch && matchesStatus && matchesReviewer) {
                  item.style.display = '';
                  visibleCount++;
              } else {
                  item.style.display = 'none';
              }
          });

          // 2. Hide/show track sections based on visible items
          trackSections.forEach(section => {
              const sectionItems = section.querySelectorAll('.paper-accordion-item');
              let sectionVisibleCount = 0;
              sectionItems.forEach(c => {
                  if (c.style.display !== 'none') {
                      sectionVisibleCount++;
                  }
              });

              if (sectionVisibleCount > 0) {
                  section.style.display = '';
                  // Update badge count dynamically
                  const badge = section.querySelector('.track-badge-count');
                  if (badge) {
                      badge.textContent = sectionVisibleCount + ' บทความ';
                  }
              } else {
                  section.style.display = 'none';
              }
          });

          // 3. Show/hide "No results" box
          if (visibleCount === 0 && items.length > 0) {
              if (noResultsBox) noResultsBox.style.display = '';
          } else {
              if (noResultsBox) noResultsBox.style.display = 'none';
          }
      }

      // Event listeners
      if (searchInput) searchInput.addEventListener('input', applyFilters);
      if (filterStatus) filterStatus.addEventListener('change', applyFilters);
      if (filterReviewer) filterReviewer.addEventListener('change', applyFilters);

      if (btnReset) {
          btnReset.addEventListener('click', function() {
              searchInput.value = '';
              filterStatus.value = 'all';
              filterReviewer.value = 'all';
              applyFilters();
          });
      }

      // Monitor duplicate reviewer selections in the same assignment form
      const assignmentForms = document.querySelectorAll('form[action*="assignReviewers"]');
      assignmentForms.forEach(form => {
          const selects = form.querySelectorAll('select[name="reviewer_ids[]"]');
          selects.forEach(select => {
              select.addEventListener('change', function() {
                  updateReviewerDropdownOptions(form);
              });
          });
          // Initial update
          updateReviewerDropdownOptions(form);
      });

      function updateReviewerDropdownOptions(form) {
          const selects = form.querySelectorAll('select[name="reviewer_ids[]"]');
          const selectedValues = Array.from(selects).map(s => s.value).filter(val => val !== '');

          selects.forEach(select => {
              const currentValue = select.value;
              const options = select.querySelectorAll('option');
              options.forEach(opt => {
                  const optVal = opt.value;
                  if (optVal === '') return;

                  const hasUniConflict = opt.getAttribute('data-uni-conflict') === 'true';
                  const isSelectedElsewhere = selectedValues.includes(optVal) && optVal !== currentValue;

                  if (hasUniConflict || isSelectedElsewhere) {
                      opt.disabled = true;
                      if (isSelectedElsewhere && !hasUniConflict) {
                          opt.style.color = 'var(--text-secondary)';
                          opt.style.fontStyle = 'italic';
                          if (!opt.textContent.includes(' (เลือกแล้ว)')) {
                              opt.textContent = opt.textContent + ' (เลือกแล้ว)';
                          }
                      }
                  } else {
                      opt.disabled = false;
                      opt.style.color = '';
                      opt.style.fontStyle = '';
                      opt.textContent = opt.textContent.replace(' (เลือกแล้ว)', '');
                  }
              });
          });
      }

      // Run once at start
      applyFilters();
  });
  </script>

</body>
</html>
