<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
  <title>Admin Dashboard - CSRM</title>
  <link class="styles" rel="stylesheet" href="<?= base_url('css/style.css'); ?>">
  <!-- DataTables & jQuery CDN -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
  <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
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
      <a href="<?= base_url('admin/reports'); ?>" class="navbar-item">รายงานผล</a>
      <a href="<?= base_url('admin/pending-reviews'); ?>" class="navbar-item">ติดตามผู้ทรงฯ</a>
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

    <!-- Detailed Dashboard Tracking (Progress of Papers, Payments & Scoring) -->
    <div class="card mb-3" style="padding: 1.5rem; background: linear-gradient(135deg, rgba(255, 255, 255, 0.9) 0%, rgba(255, 255, 255, 0.95) 100%);">
      <h3 style="font-size: 1.2rem; margin-bottom: 1.25rem; color: var(--text-primary); display: flex; align-items: center; gap: 0.5rem;">
        📊 แผงควบคุมและติดตามสถานะงานประชุม (Real-time Progress Tracker)
      </h3>
      
      <div class="grid-3" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.5rem;">
        
        <!-- 1. Paper Status Distribution -->
        <div style="background: rgba(0, 0, 0, 0.01); border: 1px solid var(--card-border); padding: 1rem; border-radius: var(--radius-sm);">
          <div style="font-weight: 600; font-size: 0.9rem; color: var(--text-primary); margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.35rem;">
            📝 สถานะการยื่นบทความ
          </div>
          <div style="display: flex; flex-direction: column; gap: 0.5rem; font-size: 0.8rem;">
            <?php
              $total = $stats['total_papers'] > 0 ? $stats['total_papers'] : 1;
              
              // Count each status directly from papers list
              $statuses = [
                  'submitted' => 0,
                  'under_review' => 0,
                  'revision_required' => 0,
                  'revised_submitted' => 0,
                  'passed_round1' => 0,
                  'failed_round1' => 0,
                  'passed_round2' => 0,
                  'failed_round2' => 0,
              ];
              foreach ($papers as $paper) {
                  if (isset($statuses[$paper['status']])) {
                      $statuses[$paper['status']]++;
                  }
              }
            ?>
            <div class="flex justify-between mb-1">
              <span>รอตรวจ/รอจ่ายเงิน</span>
              <strong><?= $statuses['submitted'] ?> เรื่อง (<?= round(($statuses['submitted'] / $total) * 100, 1) ?>%)</strong>
            </div>
            <div style="background: var(--card-border); height: 6px; border-radius: 3px; overflow: hidden; margin-bottom: 0.5rem;">
              <div style="background: var(--text-secondary); width: <?= ($statuses['submitted'] / $total) * 100 ?>%; height: 100%;"></div>
            </div>

            <div class="flex justify-between mb-1">
              <span>กำลังประเมินรอบแรก</span>
              <strong><?= $statuses['under_review'] ?> เรื่อง (<?= round(($statuses['under_review'] / $total) * 100, 1) ?>%)</strong>
            </div>
            <div style="background: var(--card-border); height: 6px; border-radius: 3px; overflow: hidden; margin-bottom: 0.5rem;">
              <div style="background: var(--info); width: <?= ($statuses['under_review'] / $total) * 100 ?>%; height: 100%;"></div>
            </div>

            <div class="flex justify-between mb-1">
              <span>อยู่ระหว่างการแก้ไข / ส่งแก้ไขแล้ว</span>
              <strong><?= ($statuses['revision_required'] + $statuses['revised_submitted']) ?> เรื่อง (<?= round((($statuses['revision_required'] + $statuses['revised_submitted']) / $total) * 100, 1) ?>%)</strong>
            </div>
            <div style="background: var(--card-border); height: 6px; border-radius: 3px; overflow: hidden; margin-bottom: 0.5rem;">
              <div style="background: var(--warning); width: <?= (($statuses['revision_required'] + $statuses['revised_submitted']) / $total) * 100 ?>%; height: 100%;"></div>
            </div>

            <div class="flex justify-between mb-1">
              <span>ผ่านการนำเสนอและเสร็จสิ้น</span>
              <strong><?= $statuses['passed_round2'] ?> เรื่อง (<?= round(($statuses['passed_round2'] / $total) * 100, 1) ?>%)</strong>
            </div>
            <div style="background: var(--card-border); height: 6px; border-radius: 3px; overflow: hidden;">
              <div style="background: var(--success); width: <?= ($statuses['passed_round2'] / $total) * 100 ?>%; height: 100%;"></div>
            </div>
          </div>
        </div>
        
        <!-- 2. Payment Tracking Progress -->
        <div style="background: rgba(0, 0, 0, 0.01); border: 1px solid var(--card-border); padding: 1rem; border-radius: var(--radius-sm);">
          <div style="font-weight: 600; font-size: 0.9rem; color: var(--text-primary); margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.35rem;">
            💳 สถานะการชำระเงินค่าลงทะเบียน
          </div>
          <div style="display: flex; flex-direction: column; gap: 0.5rem; font-size: 0.8rem;">
            <?php 
              $paidPct = round(($stats['paid'] / $total) * 100, 1);
              $unpaidPct = round(($stats['unpaid'] / $total) * 100, 1);
              $pendingPay = isset($stats['pending_verification_payment']) ? $stats['pending_verification_payment'] : 0;
              $pendingPayPct = round(($pendingPay / $total) * 100, 1);
            ?>
            <div class="flex justify-between mb-1">
              <span>ชำระเงินเรียบร้อย</span>
              <strong style="color: var(--success);"><?= $stats['paid'] ?> เรื่อง (<?= $paidPct ?>%)</strong>
            </div>
            <div style="background: var(--card-border); height: 6px; border-radius: 3px; overflow: hidden; margin-bottom: 0.75rem;">
              <div style="background: var(--success); width: <?= ($stats['paid'] / $total) * 100 ?>%; height: 100%;"></div>
            </div>

            <div class="flex justify-between mb-1">
              <span>รอแอดมินยืนยันสลิป</span>
              <strong style="color: var(--info);"><?= $pendingPay ?> เรื่อง (<?= $pendingPayPct ?>%)</strong>
            </div>
            <div style="background: var(--card-border); height: 6px; border-radius: 3px; overflow: hidden; margin-bottom: 0.75rem;">
              <div style="background: var(--info); width: <?= ($pendingPay / $total) * 100 ?>%; height: 100%;"></div>
            </div>

            <div class="flex justify-between mb-1">
              <span>ยังไม่ชำระเงิน</span>
              <strong style="color: var(--danger);"><?= $stats['unpaid'] ?> เรื่อง (<?= $unpaidPct ?>%)</strong>
            </div>
            <div style="background: var(--card-border); height: 6px; border-radius: 3px; overflow: hidden;">
              <div style="background: var(--danger); width: <?= ($stats['unpaid'] / $total) * 100 ?>%; height: 100%;"></div>
            </div>
          </div>
        </div>

        <!-- 3. Scoring / Evaluation Progress -->
        <div style="background: rgba(0, 0, 0, 0.01); border: 1px solid var(--card-border); padding: 1rem; border-radius: var(--radius-sm);">
          <div style="font-weight: 600; font-size: 0.9rem; color: var(--text-primary); margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.35rem;">
            ✍️ ความคืบหน้าการลงคะแนน (Scoring Progress)
          </div>
          <div style="display: flex; flex-direction: column; gap: 0.75rem; font-size: 0.8rem; height: 100%; justify-content: center;">
            <div>
              <div class="flex justify-between mb-1">
                <span>ความคืบหน้าการตรวจ Peer Review (รอบแรก)</span>
                <strong><?= $stats['peer_review_progress'] ?>%</strong>
              </div>
              <div style="font-size: 0.7rem; color: var(--text-secondary); margin-bottom: 0.25rem;">
                ตรวจแล้ว: <?= $stats['peer_review_details'] ?>
              </div>
              <div style="background: var(--card-border); height: 8px; border-radius: 4px; overflow: hidden;">
                <div style="background: linear-gradient(to right, var(--info), var(--primary)); width: <?= $stats['peer_review_progress'] ?>%; height: 100%;"></div>
              </div>
            </div>

            <div style="margin-top: 0.25rem;">
              <div class="flex justify-between mb-1">
                <span>ความคืบหน้าการประเมินห้องพรีเซนต์ (รอบสอง)</span>
                <strong><?= $stats['presentation_progress'] ?>%</strong>
              </div>
              <div style="font-size: 0.7rem; color: var(--text-secondary); margin-bottom: 0.25rem;">
                ให้คะแนนแล้ว: <?= $stats['presentation_details'] ?>
              </div>
              <div style="background: var(--card-border); height: 8px; border-radius: 4px; overflow: hidden;">
                <div style="background: linear-gradient(to right, var(--warning), var(--success)); width: <?= $stats['presentation_progress'] ?>%; height: 100%;"></div>
              </div>
            </div>
          </div>
        </div>

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

    <!-- Papers List (DataTable) -->
    <div class="card">
      <h2 style="margin-bottom: 1.5rem;">📄 รายการบทความวิชาการประจำปี</h2>
      
      <div class="table-responsive" style="border: none; padding: 0;">
        <table id="papers-table" class="display" style="width:100%;">
          <thead>
            <tr>
              <th>ชื่อบทความ / ผู้แต่ง</th>
              <th>กลุ่มศาสตร์ / สาขา</th>
              <th>ผู้ทรงคุณวุฒิประเมิน</th>
              <th>สถานะประเมิน</th>
              <th>การชำระเงิน</th>
              <th style="width: 80px; text-align: center;">จัดการ</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($papers as $paper): ?>
              <?php 
                $assigns = $paperAssignments[$paper['id']];
                $reviewerIdsStr = implode(',', array_map(function($a) { return $a['reviewer_id']; }, $assigns));
                $assignedCount = count($assigns);
                $completedCount = 0;
                foreach ($assigns as $a) {
                    if ($a['status'] === 'completed') {
                        $completedCount++;
                    }
                }
              ?>
              <tr onclick="openPaperModal(<?= $paper['id'] ?>)">
                <!-- Paper Title & Author -->
                <td>
                  <div style="font-weight: 600; font-size: 0.95rem; color: var(--text-primary); line-height: 1.4;"><?= esc($paper['title']) ?></div>
                  <div style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 0.25rem;">
                    ผู้เขียน: <strong><?= esc($paper['author_first_name']) ?> <?= esc($paper['author_last_name']) ?></strong>
                  </div>
                </td>
                
                <!-- Track / Discipline -->
                <td>
                  <span style="font-weight: 600; font-size: 0.8rem; color: var(--primary); background: rgba(37, 99, 235, 0.08); padding: 0.15rem 0.4rem; border-radius: 4px;">
                    <?= esc($paper['track_name']) ?>
                  </span>
                  <div style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 0.35rem;">
                    📂 <?= esc($paper['discipline_name']) ?>
                  </div>
                </td>
                
                <!-- Reviewers Status -->
                <td data-search="<?= $reviewerIdsStr ?: 'unassigned' ?>" data-order="<?= $assignedCount ?>">
                  <?php if ($assignedCount === 0): ?>
                    <span class="badge" style="background: rgba(220, 38, 38, 0.08); color: var(--danger); font-size: 0.75rem; border: 1px solid rgba(220, 38, 38, 0.15); padding: 0.25rem 0.5rem; font-weight: 600;">
                      👤❓ ยังไม่มอบหมาย
                    </span>
                  <?php elseif ($completedCount < $assignedCount): ?>
                    <span class="badge" style="background: rgba(217, 119, 6, 0.08); color: var(--warning); font-size: 0.75rem; border: 1px solid rgba(217, 119, 6, 0.15); padding: 0.25rem 0.5rem; font-weight: 600;">
                      ⏳ ประเมินแล้ว <?= $completedCount ?>/<?= $assignedCount ?> ท่าน
                    </span>
                  <?php else: ?>
                    <span class="badge" style="background: rgba(22, 163, 74, 0.08); color: var(--success); font-size: 0.75rem; border: 1px solid rgba(22, 163, 74, 0.15); padding: 0.25rem 0.5rem; font-weight: 600;">
                      ✅ ครบ <?= $completedCount ?>/<?= $assignedCount ?> ท่าน
                    </span>
                  <?php endif; ?>
                </td>
                
                <!-- Evaluation Status -->
                <td data-search="<?= $paper['status'] ?>">
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
                </td>
                
                <!-- Payment Status -->
                <td data-search="<?= $paper['payment_status'] ?>">
                  <?php if ($paper['payment_status'] === 'paid'): ?>
                    <span class="badge badge-success">ชำระเงินแล้ว</span>
                  <?php elseif ($paper['payment_status'] === 'pending_verification'): ?>
                    <span class="badge badge-pending">รออนุมัติสลิป</span>
                  <?php else: ?>
                    <span class="badge badge-danger">ยังไม่ชำระเงิน</span>
                  <?php endif; ?>
                </td>
                
                <!-- Actions -->
                <td style="text-align: center;">
                  <button type="button" class="btn btn-secondary btn-sm" style="padding: 0.25rem 0.5rem; font-size: 0.75rem; border-radius: 4px; display: inline-flex; align-items: center; gap: 0.25rem;" onclick="event.stopPropagation(); openPaperModal(<?= $paper['id'] ?>)">
                    🔍 เปิดดู
                  </button>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

  </div>

  <!-- Hidden Paper Details for Modal -->
  <?php foreach ($papers as $paper): ?>
    <?php 
      $assigns = $paperAssignments[$paper['id']];
      $reviewerIdsStr = implode(',', array_map(function($a) { return $a['reviewer_id']; }, $assigns));
    ?>
    <div id="paper-details-<?= $paper['id'] ?>" style="display: none;">
      <!-- Title & Track Badge -->
      <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem; flex-wrap: wrap;">
        <span class="badge" style="background: rgba(37, 99, 235, 0.1); color: var(--primary); font-weight: 600; font-size: 0.75rem; border: none; padding: 0.2rem 0.6rem;">
          <?= esc($paper['track_name']) ?> › <?= esc($paper['discipline_name']) ?>
        </span>
        <?php if (!empty($paper['keywords'])): ?>
          <?php foreach (explode(',', $paper['keywords']) as $kw): ?>
            <?php if (trim($kw) !== ''): ?>
              <span class="badge" style="background: #f1f5f9; color: var(--text-secondary); font-size: 0.7rem; font-weight: 500; border: 1px solid var(--card-border); padding: 0.15rem 0.5rem;">
                #<?= esc(trim($kw)) ?>
              </span>
            <?php endif; ?>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>

      <h3 style="font-size: 1.35rem; font-weight: 700; color: var(--text-primary); margin-bottom: 1rem; line-height: 1.4;">
        <?= esc($paper['title']) ?>
      </h3>

      <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 2rem; flex-wrap: wrap; text-align: left;">
        <!-- Left Details Column -->
        <div style="flex: 1; min-width: 300px;">
          <!-- Author details -->
          <div style="background: #f8fafc; border: 1px solid var(--card-border); padding: 0.75rem 1rem; border-radius: var(--radius-sm); margin-bottom: 1rem; display: flex; flex-direction: column; gap: 0.35rem;">
            <div style="font-size: 0.85rem; color: var(--text-secondary);">
              ผู้เขียน/ผู้ส่งบทความ: <strong style="color: var(--text-primary);"><?= esc($paper['author_first_name']) ?> <?= esc($paper['author_last_name']) ?></strong> 
              <span class="text-muted">(<?= esc($paper['author_email']) ?>)</span>
            </div>
            <div style="font-size: 0.85rem; color: var(--text-secondary);">
              🏛️ สถาบันสังกัด: <strong style="color: var(--primary);"><?= esc($paper['author_affiliation'] ?: 'ไม่ระบุ') ?></strong>
            </div>
            <div style="font-size: 0.85rem; color: var(--text-secondary); margin-top: 0.35rem; padding-top: 0.35rem; border-top: 1px dashed var(--card-border); display: flex; align-items: center; gap: 0.5rem;">
              🔍 ตรวจคัดลอก: 
              <?php if ($paper['plagiarism_status'] === 'checked'): ?>
                <span class="badge badge-success" style="font-size: 0.7rem; padding: 0.15rem 0.4rem; text-transform: none; border-radius: 4px;">
                  ผ่าน (<?= esc($paper['similarity_percent']) ?>%)
                </span>
                <a href="<?= esc($paper['plagiarism_report_url']) ?>" target="_blank" style="font-size: 0.75rem; color: var(--info); font-weight: 500; text-decoration: underline;">เปิดรายงาน</a>
              <?php elseif ($paper['plagiarism_status'] === 'failed'): ?>
                <span class="badge badge-danger" style="font-size: 0.7rem; padding: 0.15rem 0.4rem; text-transform: none; border-radius: 4px;">
                  คัดลอกสูง (<?= esc($paper['similarity_percent']) ?>%)
                </span>
                <a href="<?= esc($paper['plagiarism_report_url']) ?>" target="_blank" style="font-size: 0.75rem; color: var(--danger); font-weight: 500; text-decoration: underline;">เปิดรายงาน</a>
              <?php else: ?>
                <span class="badge badge-pending" style="font-size: 0.7rem; padding: 0.15rem 0.4rem; text-transform: none; border-radius: 4px;">รอการตรวจสอบ</span>
              <?php endif; ?>
            </div>
          </div>

          <!-- Abstract Collapsible (Always open in modal since we have space, but details look cleaner) -->
          <div style="margin-bottom: 1rem; border: 1px solid var(--card-border); border-radius: var(--radius-sm); background: #ffffff; padding: 1rem;">
            <h4 style="font-size: 0.9rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;">📖 บทคัดย่อ (Abstract)</h4>
            <div style="font-size: 0.9rem; color: var(--text-secondary); white-space: pre-line; line-height: 1.6;">
              <?= esc($paper['abstract']) ?>
            </div>
          </div>

          <!-- PDF Files and Revisions -->
          <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap; margin-bottom: 1rem;">
            <a href="<?= base_url($paper['file_path']) ?>" target="_blank" class="btn btn-secondary btn-sm" style="padding: 0.35rem 0.75rem; font-size: 0.8rem; border-radius: 6px;">
              📖 เปิดอ่าน PDF (ฉบับส่งแรก)
            </a>

            <?php if (!empty($paperRevisions[$paper['id']])): ?>
              <div style="width: 100%; margin-top: 1rem; border-top: 1px dashed var(--card-border); padding-top: 1rem;">
                <h4 style="font-size: 0.85rem; font-weight: 700; margin-bottom: 0.5rem; color: var(--text-primary);">ประวัติการส่งฉบับแก้ไข:</h4>
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
            <?php endif; ?>
          </div>
        </div>

        <!-- Right Column -->
        <div style="width: 320px; flex-shrink: 0; display: flex; flex-direction: column; gap: 1.25rem; border-left: 1px solid var(--card-border); padding-left: 1.5rem;">
          <!-- Statuses & Payment -->
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 0.5rem;">
            <div>
              <div style="font-size: 0.75rem; color: var(--text-secondary); margin-bottom: 0.25rem; font-weight: 500;">สถานะประเมิน</div>
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
            </div>

            <div>
              <div style="font-size: 0.75rem; color: var(--text-secondary); margin-bottom: 0.25rem; font-weight: 500;">การชำระเงิน</div>
              <?php if ($paper['payment_status'] === 'paid'): ?>
                <span class="badge badge-success">ชำระเงินแล้ว</span>
              <?php elseif ($paper['payment_status'] === 'pending_verification'): ?>
                <span class="badge badge-pending">รออนุมัติสลิป</span>
              <?php else: ?>
                <span class="badge badge-danger">ยังไม่ชำระเงิน</span>
              <?php endif; ?>
            </div>
          </div>

          <!-- Admin Revision Action (Approve Revision) -->
          <?php if ($paper['status'] === 'revised_submitted'): ?>
            <div style="border-top: 1px solid var(--card-border); padding-top: 1rem; margin-bottom: 0.5rem;">
              <div style="font-size: 0.8rem; font-weight: 700; color: var(--info); margin-bottom: 0.35rem; display: flex; align-items: center; gap: 0.25rem;">
                ⚙️ ดำเนินการผลงานส่งแก้ไข
              </div>
              <p style="font-size: 0.75rem; color: var(--text-secondary); margin-bottom: 0.5rem; line-height: 1.4;">
                ผู้ส่งผลงานได้แก้ไขและชี้แจงเรียบร้อยแล้ว แอดมินสามารถอนุมัติผ่านเพื่อนำเข้าห้องนำเสนอได้โดยตรง
              </p>
              <a href="<?= base_url('admin/approveRevision/' . $paper['id']) ?>" class="btn btn-primary btn-sm" style="width: 100%; justify-content: center; font-size: 0.8rem; background: var(--info); border-color: var(--info); height: 32px;" onclick="return confirm('ยืนยันที่จะอนุมัติให้บทความนี้ผ่านรอบแรกเพื่อนำเสนอผลงาน?')">
                ✅ อนุมัติผลงานเข้าห้องพรีเซนต์
              </a>
            </div>
          <?php endif; ?>

          <!-- Admin Deadline Override -->
          <?php if (in_array($paper['status'], ['revision_required', 'revised_submitted'])): ?>
            <div style="border-top: 1px solid var(--card-border); padding-top: 1rem; margin-bottom: 0.5rem;">
              <div style="font-size: 0.8rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.35rem;">
                ⏱️ กำหนดวันส่งเล่มแก้ไข
              </div>
              <div style="font-size: 0.75rem; color: var(--text-secondary); margin-bottom: 0.5rem;">
                กำหนดปัจจุบัน: <strong style="color: var(--warning);"><?= !empty($paper['revision_deadline']) ? date('d/m/Y H:i', strtotime($paper['revision_deadline'])) : 'ไม่ได้กำหนด' ?></strong>
              </div>
              <form action="<?= base_url('admin/setRevisionDeadline') ?>" method="POST" style="display: flex; flex-direction: column; gap: 0.35rem;">
                <?= csrf_field() ?>
                <input type="hidden" name="paper_id" value="<?= $paper['id'] ?>">
                <input type="datetime-local" name="revision_deadline" class="form-control" style="font-size: 0.75rem; padding: 0.25rem 0.5rem; height: 32px; font-family: inherit;" value="<?= !empty($paper['revision_deadline']) ? date('Y-m-d\TH:i', strtotime($paper['revision_deadline'])) : '' ?>" required>
                <button type="submit" class="btn btn-secondary btn-sm" style="font-size: 0.75rem; padding: 0.25rem 0.5rem; height: 32px; justify-content: center; width: 100%;">
                  🔄 ตั้งกำหนดส่งใหม่
                </button>
              </form>
            </div>
          <?php endif; ?>

          <!-- Reviewers (Round 1) -->
          <div style="border-top: 1px solid var(--card-border); padding-top: 1rem;">
            <div style="font-size: 0.8rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.35rem;">
              👤 ผู้ทรงคุณวุฒิประเมิน (รอบ 1)
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
                                data-affiliation="<?= esc($r['affiliation'] ? preg_replace('/\s+/', '', mb_strtolower($r['affiliation'])) : '') ?>"
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
  <?php endforeach; ?>

  <!-- Paper Detail Modal -->
  <div id="paper-modal" class="modal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.4); backdrop-filter: blur(4px);">
    <div class="modal-content card" style="background-color: #ffffff; margin: 4% auto; padding: 2rem; border: 1px solid var(--card-border); width: 85%; max-width: 950px; border-radius: var(--radius-md); box-shadow: var(--shadow); position: relative; animation: modalFadeIn 0.3s cubic-bezier(0.16, 1, 0.3, 1);">
      <span class="close-modal" style="position: absolute; right: 1.5rem; top: 1.5rem; font-size: 1.75rem; font-weight: bold; color: var(--text-secondary); cursor: pointer;" onclick="closePaperModal()">&times;</span>
      
      <div id="modal-loading" style="text-align: center; padding: 3rem 0;">
        <span class="spinner"></span>
        <div style="margin-top: 0.75rem; color: var(--text-secondary); font-weight: 500;">กำลังโหลดข้อมูลบทความ...</div>
      </div>
      
      <div id="modal-body" style="display: none;">
        <!-- Dynamic content cloned from hidden divs goes here -->
      </div>
    </div>
  </div>

  <script>
  // Open modal with paper details
  function openPaperModal(paperId) {
      const detailsHtml = document.getElementById('paper-details-' + paperId).innerHTML;
      const modalBody = document.getElementById('modal-body');
      const loading = document.getElementById('modal-loading');
      
      loading.style.display = 'block';
      modalBody.style.display = 'none';
      document.getElementById('paper-modal').style.display = 'block';
      
      // Load content smoothly after 150ms
      setTimeout(() => {
          modalBody.innerHTML = detailsHtml;
          loading.style.display = 'none';
          modalBody.style.display = 'block';
          
          // Re-bind the duplicate reviewer validation inside the modal form
          const modalForm = modalBody.querySelector('form[action*="assignReviewers"]');
          if (modalForm) {
              const selects = modalForm.querySelectorAll('select[name="reviewer_ids[]"]');
              selects.forEach(select => {
                  select.addEventListener('change', function() {
                      updateReviewerDropdownOptions(modalForm);
                  });
              });
              // Initial update
              updateReviewerDropdownOptions(modalForm);
          }
      }, 150);
  }

  // Close modal
  function closePaperModal() {
      document.getElementById('paper-modal').style.display = 'none';
      document.getElementById('modal-body').innerHTML = '';
  }

  // Close when clicking outside of modal content
  window.addEventListener('click', function(event) {
      const modal = document.getElementById('paper-modal');
      if (event.target === modal) {
          closePaperModal();
      }
  });

  document.addEventListener('DOMContentLoaded', function() {
      // Initialize DataTable
      const table = $('#papers-table').DataTable({
          language: {
              url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/th.json'
          },
          pageLength: 25,
          order: [[0, 'asc']], // Order by paper title
          columnDefs: [
              { orderable: false, targets: [2, 3, 4, 5] } // Disable sorting on status and actions
          ]
      });

      const searchInput = document.getElementById('search-input');
      const filterStatus = document.getElementById('filter-status');
      const filterReviewer = document.getElementById('filter-reviewer');
      const btnReset = document.getElementById('btn-reset-filters');

      function applyFilters() {
          // Global Search (Text input)
          if (searchInput) {
              table.search(searchInput.value.trim()).draw();
          }

          // Filter by Status (Column index 3)
          if (filterStatus) {
              const statusVal = filterStatus.value;
              if (statusVal === 'all') {
                  table.column(3).search('').draw();
              } else {
                  table.column(3).search('^' + statusVal + '$', true, false).draw();
              }
          }

          // Filter by Reviewer (Column index 2)
          if (filterReviewer) {
              const reviewerVal = filterReviewer.value;
              if (reviewerVal === 'all') {
                  table.column(2).search('').draw();
              } else if (reviewerVal === 'unassigned') {
                  table.column(2).search('^unassigned$', true, false).draw();
              } else {
                  // Reviewers cell contains comma-separated IDs inside data-search attribute
                  // Search using regex word-boundary or substring depending on format
                  table.column(2).search(reviewerVal).draw();
              }
          }
      }

      // Event listeners for DataTable filtering
      if (searchInput) {
          searchInput.addEventListener('input', applyFilters);
      }
      if (filterStatus) {
          filterStatus.addEventListener('change', applyFilters);
      }
      if (filterReviewer) {
          filterReviewer.addEventListener('change', applyFilters);
      }

      if (btnReset) {
          btnReset.addEventListener('click', function() {
              if (searchInput) searchInput.value = '';
              if (filterStatus) filterStatus.value = 'all';
              if (filterReviewer) filterReviewer.value = 'all';
              table.search('').column(2).search('').column(3).search('').draw();
          });
      }
  });

  // Reused function for duplicate reviewer dropdown validation
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

              // Check if another dropdown has selected a reviewer with the same non-empty affiliation
              const optAff = opt.getAttribute('data-affiliation');
              let hasAffiliationConflict = false;
              if (optAff && optAff !== '') {
                  selects.forEach(s => {
                      if (s !== select && s.value !== '') {
                          const selOpt = s.options[s.selectedIndex];
                          const aff = selOpt.getAttribute('data-affiliation');
                          if (aff && aff !== '' && aff === optAff) {
                              hasAffiliationConflict = true;
                          }
                      }
                  });
              }

              if (hasUniConflict || isSelectedElsewhere || hasAffiliationConflict) {
                  opt.disabled = true;
                  if (hasAffiliationConflict && !hasUniConflict && !isSelectedElsewhere) {
                      opt.style.color = 'var(--danger)';
                      opt.style.fontStyle = 'italic';
                      if (!opt.textContent.includes(' ⚠️ สถาบันซ้ำ')) {
                          opt.textContent = opt.textContent + ' ⚠️ สถาบันซ้ำ';
                      }
                  } else if (isSelectedElsewhere && !hasUniConflict) {
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
                  opt.textContent = opt.textContent.replace(' (เลือกแล้ว)', '').replace(' ⚠️ สถาบันซ้ำ', '');
              }
          });
      });
  }
  </script>

</body>
</html>
