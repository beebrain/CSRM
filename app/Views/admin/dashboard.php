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
              <tr id="no-results-row" style="display: none;">
                <td colspan="5" class="text-center text-muted" style="padding: 2rem;">ไม่พบข้อมูลบทความวิชาการที่ตรงกับเงื่อนไขการค้นหา</td>
              </tr>
              <?php foreach ($papers as $paper): ?>
                <?php 
                  $assigns = $paperAssignments[$paper['id']];
                  $reviewerIdsStr = implode(',', array_map(function($a) { return $a['reviewer_id']; }, $assigns));
                ?>
                <tr class="paper-row"
                    data-title="<?= esc(mb_strtolower($paper['title'])) ?>"
                    data-author-name="<?= esc(mb_strtolower($paper['author_first_name'] . ' ' . $paper['author_last_name'])) ?>"
                    data-author-affiliation="<?= esc(mb_strtolower($paper['author_affiliation'] ?: '')) ?>"
                    data-author-email="<?= esc(mb_strtolower($paper['author_email'])) ?>"
                    data-keywords="<?= esc(mb_strtolower($paper['keywords'] ?: '')) ?>"
                    data-status="<?= esc($paper['status']) ?>"
                    data-reviewers="<?= esc($reviewerIdsStr) ?>">
                  <td style="max-width: 300px;">
                    <div style="font-weight: 600;"><?= esc($paper['title']) ?></div>
                    <div style="margin-top: 0.25rem;">
                      <small class="text-muted" style="display: block;">ผู้เขียน: <strong><?= esc($paper['author_first_name']) ?> <?= esc($paper['author_last_name']) ?></strong> (<?= esc($paper['author_email']) ?>)</small>
                      <small class="text-muted" style="display: block; margin-top: 0.1rem;">
                        🏛️ สถาบัน: <strong style="color: var(--primary); font-weight: 600;"><?= esc($paper['author_affiliation'] ?: 'ไม่ระบุ') ?></strong>
                      </small>
                    </div>
                    
                    <?php if (!empty($paper['keywords'])): ?>
                      <div style="font-size: 0.8rem; margin-top: 0.35rem;">
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
                    <?php if (empty($assigns)): ?>
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
                        
                        <?php for ($i = 1; $i <= 3; $i++): ?>
                          <div class="form-group mb-1">
                            <select name="reviewer_ids[]" class="form-control" style="font-size: 0.8rem; padding: 0.25rem;" required>
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
                                        <?= $isSameAffiliation ? 'disabled style="color: var(--danger); font-style: italic;"' : ($isMatch ? 'style="color: var(--success); font-weight: 600;"' : '') ?>>
                                  <?= esc($r['first_name']) ?> <?= esc($r['last_name']) ?><?= esc($affText) ?><?= esc($matchText) ?><?= $isSameAffiliation ? ' ⚠️ สถาบันเดียวกัน (ห้ามเลือก)' : '' ?>
                                </option>
                              <?php endforeach; ?>
                            </select>
                          </div>
                        <?php endfor; ?>
                        
                        <button type="submit" class="btn btn-primary btn-sm" style="font-size: 0.75rem; padding: 0.25rem 0.5rem; width: 100%;">👤 ส่งให้ผู้ทรง 3 ท่าน</button>
                      </form>
                    <?php else: ?>
                      <!-- Show reviewer statuses -->
                      <div class="flex flex-column gap-1" style="display: flex; flex-direction: column; gap: 0.35rem;">
                        <?php foreach ($assigns as $a): ?>
                          <div class="reviewer-badge" style="display: block; margin: 0; padding: 0.35rem 0.5rem; background: rgba(0, 0, 0, 0.02); border: 1px solid var(--card-border); border-radius: var(--radius-sm);">
                            <div style="font-weight: 600; font-size: 0.8rem; color: var(--text-primary);">
                              👤 <?= esc($a['first_name']) ?> <?= esc($a['last_name']) ?>
                            </div>
                            <div style="font-size: 0.7rem; color: var(--text-secondary); margin-top: 0.05rem;">
                              🏛️ <?= esc($a['affiliation'] ?: 'ไม่ระบุสถาบัน') ?>
                            </div>
                            <div style="margin-top: 0.2rem; font-size: 0.75rem;">
                              สถานะ: 
                              <?php if ($a['status'] === 'pending'): ?>
                                <span style="color: var(--text-secondary); font-weight: 500;">รอการประเมิน</span>
                              <?php else: ?>
                                <?php if ($a['decision'] === 'pass'): ?>
                                  <span style="color: var(--success); font-weight: 600;">ผ่าน</span>
                                <?php elseif ($a['decision'] === 'revision'): ?>
                                  <span style="color: var(--warning); font-weight: 600;">แก้ไข</span>
                                <?php else: ?>
                                  <span style="color: var(--danger); font-weight: 600;">ไม่ผ่าน</span>
                                <?php endif; ?>
                              <?php endif; ?>
                            </div>
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

  <script>
  document.addEventListener('DOMContentLoaded', function() {
      const searchInput = document.getElementById('search-input');
      const filterStatus = document.getElementById('filter-status');
      const filterReviewer = document.getElementById('filter-reviewer');
      const btnReset = document.getElementById('btn-reset-filters');
      const rows = document.querySelectorAll('.paper-row');
      const noResultsRow = document.getElementById('no-results-row');

      function applyFilters() {
          const query = searchInput.value.toLowerCase().trim();
          const status = filterStatus.value;
          const reviewer = filterReviewer.value;
          let visibleCount = 0;

          rows.forEach(row => {
              // Read data attributes
              const title = row.getAttribute('data-title') || '';
              const authorName = row.getAttribute('data-author-name') || '';
              const authorAff = row.getAttribute('data-author-affiliation') || '';
              const keywords = row.getAttribute('data-keywords') || '';
              const authorEmail = row.getAttribute('data-author-email') || '';
              const paperStatus = row.getAttribute('data-status') || '';
              
              // Reviewer IDs are comma-separated string
              const reviewerIds = (row.getAttribute('data-reviewers') || '').split(',').filter(id => id !== '');

              // 1. Check search query
              const matchesSearch = title.includes(query) || 
                                    authorName.includes(query) || 
                                    authorAff.includes(query) || 
                                    authorEmail.includes(query) ||
                                    keywords.includes(query);

              // 2. Check status filter
              const matchesStatus = (status === 'all') || (paperStatus === status);

              // 3. Check reviewer filter
              let matchesReviewer = false;
              if (reviewer === 'all') {
                  matchesReviewer = true;
              } else if (reviewer === 'unassigned') {
                  matchesReviewer = (reviewerIds.length === 0);
              } else {
                  matchesReviewer = reviewerIds.includes(reviewer);
              }

              // Determine visibility
              if (matchesSearch && matchesStatus && matchesReviewer) {
                  row.style.display = '';
                  visibleCount++;
              } else {
                  row.style.display = 'none';
              }
          });

          // Show/hide "No results" row
          if (visibleCount === 0 && rows.length > 0) {
              if (noResultsRow) noResultsRow.style.display = '';
          } else {
              if (noResultsRow) noResultsRow.style.display = 'none';
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

      // Run once at start
      applyFilters();
  });
  </script>

</body>
</html>
