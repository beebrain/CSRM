<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
  <title>รายงานผลการประเมิน - CSRM</title>
  <link class="styles" rel="stylesheet" href="<?= base_url('css/style.css'); ?>">
  <style>
    /* Print styles */
    @media print {
      body {
        background: #ffffff !important;
        color: #000000 !important;
        font-size: 11pt !important;
      }
      .navbar, .btn, .no-print, #btn-reset-filters, .grid-filters {
        display: none !important;
      }
      .container {
        padding: 0 !important;
        max-width: 100% !important;
      }
      .card {
        border: none !important;
        box-shadow: none !important;
        padding: 0 !important;
        margin-bottom: 0 !important;
      }
      .table {
        border: 1px solid #000000 !important;
        width: 100% !important;
      }
      .table th, .table td {
        border: 1px solid #000000 !important;
        padding: 0.5rem !important;
        color: #000000 !important;
      }
      .badge {
        border: 1px solid #000000 !important;
        color: #000000 !important;
        background: transparent !important;
      }
      .page-break {
        page-break-before: always;
      }
    }

    .report-details-table {
      font-size: 0.85rem;
      width: 100%;
      border-collapse: collapse;
      margin-top: 0.5rem;
      background: rgba(0, 0, 0, 0.01);
    }
    .report-details-table th, .report-details-table td {
      border: 1px solid var(--card-border);
      padding: 0.4rem 0.6rem;
      text-align: left;
    }
    .report-details-table th {
      background: rgba(0, 0, 0, 0.03);
      font-weight: 600;
    }
    
    .report-card-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 1.5rem;
      margin-top: 1.5rem;
    }
  </style>
</head>
<body>

  <!-- Navigation -->
  <nav class="navbar animate-fade-in no-print">
    <div class="navbar-brand">
      🎓 CSRM Admin (ประจำปี)
    </div>
    <div class="navbar-menu">
      <select onchange="window.location.href='<?= base_url('admin/selectConference'); ?>/'+this.value+'?redirect=admin/reports'" class="form-control text-sm" style="padding: 0.25rem 0.5rem; width: auto; background: rgba(255, 255, 255, 0.08); border: 1px solid var(--card-border);">
        <?php foreach ($allowedConfs as $conf): ?>
          <option value="<?= $conf['id'] ?>" <?= $conf['id'] == $currentConfId ? 'selected' : '' ?>>ปี พ.ศ. <?= esc($conf['year']) ?></option>
        <?php endforeach; ?>
      </select>
      <a href="<?= base_url('admin/dashboard'); ?>" class="navbar-item">บทความ</a>
      <a href="<?= base_url('admin/disciplines'); ?>" class="navbar-item">ศาสตร์ย่อย</a>
      <a href="<?= base_url('admin/criteria'); ?>" class="navbar-item">เกณฑ์ประเมิน</a>
      <a href="<?= base_url('admin/rooms'); ?>" class="navbar-item">จัดห้องพรีเซนต์</a>
      <a href="<?= base_url('admin/payments'); ?>" class="navbar-item">ยืนยันเงิน</a>
      <a href="<?= base_url('admin/reports'); ?>" class="navbar-item active">รายงานผล</a>
      <?php if (session()->get('role') === 'superadmin'): ?>
        <a href="<?= base_url('superadmin/dashboard'); ?>" class="btn btn-primary btn-sm">⚙️ กลับหน้า SuperAdmin</a>
      <?php endif; ?>
      <a href="<?= base_url('auth/logout'); ?>" class="btn btn-secondary btn-sm">ออกระบบ</a>
    </div>
  </nav>

  <div class="container animate-fade-in" style="animation-delay: 0.1s;">
    
    <!-- Header -->
    <div class="card mb-3" style="background: linear-gradient(135deg, rgba(99, 102, 241, 0.1) 0%, rgba(168, 85, 247, 0.05) 100%); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
      <div>
        <h1 style="margin-bottom: 0.25rem;">📊 รายงานผลการประเมินบทความวิชาการ</h1>
        <p class="text-muted">
          งานประชุมวิชาการประจำปี พ.ศ. <strong><?= esc($selectedConference['year']) ?></strong> | 
          เจ้าภาพ: <strong><?= esc($selectedConference['host_name']) ?></strong>
        </p>
      </div>
      <div class="no-print">
        <button onclick="window.print()" class="btn btn-primary" style="background: var(--info); border-color: var(--info); display: inline-flex; align-items: center; gap: 0.35rem;">
          🖨️ พิมพ์ / ส่งออก PDF
        </button>
      </div>
    </div>

    <!-- Summary Reports List -->
    <div class="card">
      <h2 style="margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: space-between;">
        <span>📝 สรุปผลคะแนนและสถานะบทความทั้งหมด</span>
        <span class="text-muted text-sm font-normal">จำนวนบทความทั้งหมด: <?= count($papers) ?> เรื่อง</span>
      </h2>

      <?php if (empty($papers)): ?>
        <div class="text-center text-muted" style="padding: 3rem 0;">
          ยังไม่มีการส่งบทความวิชาการเข้าสู่ระบบในปีนี้
        </div>
      <?php else: ?>
        <div class="table-responsive">
          <table class="table" style="width: 100%;">
            <thead>
              <tr>
                <th style="width: 80px;">ไอดี</th>
                <th style="width: 280px;">ชื่อเรื่อง / ผู้แต่ง</th>
                <th>สถานะ / ตรวจคัดลอก</th>
                <th style="width: 250px;">การประเมินรอบแรก (Peer Review)</th>
                <th style="width: 250px;">การประเมินรอบสอง (Presentation)</th>
                <th>ผลลัพธ์สุทธิ</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($papers as $paper): ?>
                <?php 
                  $pId = $paper['id'];
                  $assigns = $paperAssignments[$pId];
                  $revs = $paperRevisions[$pId];
                  $sched = $presentationSchedules[$pId];
                  $presRevs = $presentationReviews[$pId];
                ?>
                <tr>
                  <!-- Paper ID -->
                  <td><strong>#<?= $pId ?></strong></td>

                  <!-- Title & Author -->
                  <td>
                    <div style="font-weight: 600; color: var(--text-primary); line-height: 1.4;"><?= esc($paper['title']) ?></div>
                    <div style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 0.25rem;">
                      ผู้เขียน: <strong><?= esc($paper['author_first_name']) ?> <?= esc($paper['author_last_name']) ?></strong> 
                      (🏛️ <?= esc($paper['author_affiliation'] ?: 'ไม่ระบุ') ?>)
                    </div>
                  </td>

                  <!-- Status & Plagiarism -->
                  <td>
                    <!-- Paper Status -->
                    <div class="mb-1">
                      <?php if ($paper['status'] === 'submitted'): ?>
                        <span class="badge badge-pending">รอตรวจ/รอจ่ายเงิน</span>
                      <?php elseif ($paper['status'] === 'under_review'): ?>
                        <span class="badge badge-info">กำลังประเมินรอบ 1</span>
                      <?php elseif ($paper['status'] === 'revision_required'): ?>
                        <span class="badge badge-pending" style="background: rgba(217, 119, 6, 0.15); color: var(--warning); border-color: var(--warning);">ต้องการแก้ไข</span>
                      <?php elseif ($paper['status'] === 'revised_submitted'): ?>
                        <span class="badge badge-info" style="background: rgba(2, 132, 199, 0.15); color: var(--info); border-color: var(--info);">ส่งฉบับแก้ไขแล้ว</span>
                      <?php elseif ($paper['status'] === 'passed_round1'): ?>
                        <span class="badge badge-success">ผ่านรอบ 1 (รอพรีเซนต์)</span>
                      <?php elseif ($paper['status'] === 'failed_round1'): ?>
                        <span class="badge badge-danger">ไม่ผ่านรอบ 1</span>
                      <?php elseif ($paper['status'] === 'passed_round2'): ?>
                        <span class="badge badge-success" style="background: rgba(16, 185, 129, 0.3); border-color: var(--success);">ผ่านการประเมินสุดท้าย</span>
                      <?php elseif ($paper['status'] === 'failed_round2'): ?>
                        <span class="badge badge-danger">ไม่ผ่านการนำเสนอ</span>
                      <?php endif; ?>
                    </div>
                    
                    <!-- Plagiarism -->
                    <div style="font-size: 0.7rem; color: var(--text-secondary);">
                      คัดลอก: 
                      <?php if ($paper['plagiarism_status'] === 'checked'): ?>
                        <span style="color: var(--success); font-weight: 600;">ผ่าน (<?= esc($paper['similarity_percent']) ?>%)</span>
                      <?php elseif ($paper['plagiarism_status'] === 'failed'): ?>
                        <span style="color: var(--danger); font-weight: 600;">ไม่ผ่าน (<?= esc($paper['similarity_percent']) ?>%)</span>
                      <?php else: ?>
                        <span class="text-muted">รอตรวจสอบ</span>
                      <?php endif; ?>
                    </div>
                  </td>

                  <!-- Round 1: Peer Review -->
                  <td>
                    <?php if (empty($assigns)): ?>
                      <span class="text-muted text-xs">ยังไม่ได้มอบหมายผู้ทรงฯ</span>
                    <?php else: ?>
                      <table class="report-details-table">
                        <thead>
                          <tr>
                            <th>ผู้ทรงคุณวุฒิ</th>
                            <th>มติ</th>
                            <th>คะแนน</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php foreach ($assigns as $a): ?>
                            <?php 
                              $scoreSum = 0;
                              foreach ($a['scores'] as $sc) {
                                  $scoreSum += $sc['score'];
                              }
                            ?>
                            <tr>
                              <td>
                                <div style="font-weight: 600;"><?= esc($a['first_name']) ?></div>
                                <small class="text-muted text-xs"><?= esc($a['affiliation'] ?: 'ไม่ระบุ') ?></small>
                              </td>
                              <td>
                                <?php if ($a['status'] === 'pending'): ?>
                                  <span class="text-muted text-xs">⏳ รอ</span>
                                <?php elseif ($a['decision'] === 'pass'): ?>
                                  <span style="color: var(--success); font-weight: 700; font-size: 0.75rem;">✅ ผ่าน</span>
                                <?php elseif ($a['decision'] === 'revision'): ?>
                                  <span style="color: var(--warning); font-weight: 700; font-size: 0.75rem;">🔧 แก้ไข</span>
                                <?php else: ?>
                                  <span style="color: var(--danger); font-weight: 700; font-size: 0.75rem;">❌ ตก</span>
                                <?php endif; ?>
                              </td>
                              <td>
                                <?= $a['status'] === 'completed' ? '<strong>' . $scoreSum . '</strong>' : '-' ?>
                              </td>
                            </tr>
                          <?php endforeach; ?>
                        </tbody>
                      </table>
                    <?php endif; ?>

                    <!-- Revisions info -->
                    <?php if (!empty($revs)): ?>
                      <div style="margin-top: 0.5rem; font-size: 0.75rem; background: rgba(2, 132, 199, 0.05); padding: 0.35rem 0.5rem; border-radius: 4px; border-left: 3px solid var(--info);">
                        📬 มีเล่มแก้ไขส่งมาแล้ว (<?= count($revs) ?> ครั้ง)
                      </div>
                    <?php endif; ?>
                  </td>

                  <!-- Round 2: Presentation -->
                  <td>
                    <?php if (!$sched): ?>
                      <span class="text-muted text-xs">ยังไม่มีการจัดห้องนำเสนอ</span>
                    <?php else: ?>
                      <div class="text-xs" style="margin-bottom: 0.35rem; font-weight: 600; color: var(--primary);">
                        🏫 ห้อง: <?= esc($sched['room_name']) ?> (เวลา: <?= esc($sched['presentation_time']) ?>)
                      </div>
                      
                      <?php if (empty($presRevs)): ?>
                        <span class="text-muted text-xs">รอการแต่งตั้งกรรมการห้อง</span>
                      <?php else: ?>
                        <table class="report-details-table">
                          <thead>
                            <tr>
                              <th>กรรมการ</th>
                              <th>คะแนน (เต็มข้อละ 10)</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php foreach ($presRevs as $pr): ?>
                              <?php 
                                $prSum = 0;
                                foreach ($pr['scores'] as $sc) {
                                    $prSum += $sc['score'];
                                }
                              ?>
                              <tr>
                                <td><?= esc($pr['first_name']) ?></td>
                                <td>
                                  <?= $pr['status'] === 'completed' ? '<strong>' . $prSum . '</strong>' : '<span class="text-muted">⏳ รอ</span>' ?>
                                </td>
                              </tr>
                            <?php endforeach; ?>
                          </tbody>
                        </table>
                      <?php endif; ?>
                    <?php endif; ?>
                  </td>

                  <!-- Outcome / Result -->
                  <td>
                    <?php if ($paper['status'] === 'passed_round2'): ?>
                      <div style="text-align: center;">
                        <span class="badge badge-success" style="font-size: 0.8rem; border-radius: 4px;">ผ่านสมบูรณ์</span>
                        <?php if (isset($paper['presentation_score']) && $paper['presentation_score'] !== null): ?>
                          <div style="font-size: 0.85rem; font-weight: 800; color: var(--success); margin-top: 0.35rem;">
                            เฉลี่ย: <?= number_format($paper['presentation_score'], 2) ?>
                          </div>
                        <?php endif; ?>
                      </div>
                    <?php elseif ($paper['status'] === 'failed_round2'): ?>
                      <div style="text-align: center;">
                        <span class="badge badge-danger" style="font-size: 0.8rem; border-radius: 4px;">ไม่ผ่านพรีเซนต์</span>
                        <div style="font-size: 0.85rem; font-weight: 800; color: var(--danger); margin-top: 0.35rem;">
                          เฉลี่ย: <?= number_format($paper['presentation_score'] ?? 0, 2) ?>
                        </div>
                      </div>
                    <?php elseif ($paper['status'] === 'failed_round1'): ?>
                      <div style="text-align: center;">
                        <span class="badge badge-danger" style="font-size: 0.8rem; border-radius: 4px;">ไม่ผ่านรอบแรก</span>
                      </div>
                    <?php else: ?>
                      <div style="text-align: center;" class="text-muted text-xs">
                        อยู่ระหว่างดำเนินการ
                      </div>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>

  </div>

</body>
</html>
