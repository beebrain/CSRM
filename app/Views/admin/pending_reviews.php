<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
  <title>ติดตามผลงานค้างประเมิน - CSRM</title>
  <link class="styles" rel="stylesheet" href="<?= base_url('css/style.css'); ?>">
  <style>
    /* Tab Styling */
    .tab-container {
      display: flex;
      gap: 0.5rem;
      margin-bottom: 1.5rem;
      border-bottom: 2px solid var(--card-border);
      padding-bottom: 0.5rem;
    }
    .tab-button {
      padding: 0.75rem 1.25rem;
      font-weight: 600;
      font-size: 0.95rem;
      color: var(--text-secondary);
      background: transparent;
      border: none;
      border-bottom: 3px solid transparent;
      cursor: pointer;
      transition: var(--transition);
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
    }
    .tab-button:hover {
      color: var(--primary);
    }
    .tab-button.active {
      color: var(--primary);
      border-bottom-color: var(--primary);
    }
    .tab-content {
      display: none;
    }
    .tab-content.active {
      display: block;
      animation: fadeIn 0.4s ease forwards;
    }

    /* Email button style override */
    .btn-email {
      background: rgba(37, 99, 235, 0.08);
      color: var(--primary);
      border: 1px solid rgba(37, 99, 235, 0.2);
      padding: 0.35rem 0.75rem;
      font-size: 0.8rem;
      border-radius: 6px;
      display: inline-flex;
      align-items: center;
      gap: 0.35rem;
      font-weight: 600;
    }
    .btn-email:hover {
      background: var(--primary);
      color: #ffffff;
      transform: translateY(-1px);
    }

    .meta-item {
      display: inline-flex;
      align-items: center;
      gap: 0.35rem;
      font-size: 0.8rem;
      color: var(--text-secondary);
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
      <select onchange="window.location.href='<?= base_url('admin/selectConference'); ?>/'+this.value+'?redirect=admin/pending-reviews'" class="form-control text-sm" style="padding: 0.25rem 0.5rem; width: auto; background: rgba(255, 255, 255, 0.08); border: 1px solid var(--card-border);">
        <?php foreach ($allowedConfs as $conf): ?>
          <option value="<?= $conf['id'] ?>" <?= $conf['id'] == $currentConfId ? 'selected' : '' ?>>ปี พ.ศ. <?= esc($conf['year']) ?></option>
        <?php endforeach; ?>
      </select>
      <a href="<?= base_url('admin/dashboard'); ?>" class="navbar-item">บทความ</a>
      <a href="<?= base_url('admin/disciplines'); ?>" class="navbar-item">ศาสตร์ย่อย</a>
      <a href="<?= base_url('admin/criteria'); ?>" class="navbar-item">เกณฑ์ประเมิน</a>
      <a href="<?= base_url('admin/rooms'); ?>" class="navbar-item">จัดห้องพรีเซนต์</a>
      <a href="<?= base_url('admin/payments'); ?>" class="navbar-item">ยืนยันเงิน</a>
      <a href="<?= base_url('admin/reports'); ?>" class="navbar-item">รายงานผล</a>
      <a href="<?= base_url('admin/pending-reviews'); ?>" class="navbar-item active">ติดตามผู้ทรงฯ</a>
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
        <h1 style="margin-bottom: 0.25rem;">🔍 ติดตามผลงานค้างประเมิน (Pending Evaluations)</h1>
        <p class="text-muted">
          งานประชุมวิชาการประจำปี พ.ศ. <strong><?= esc($selectedConference['year']) ?></strong> | 
          เจ้าภาพ: <strong><?= esc($selectedConference['host_name']) ?></strong>
        </p>
      </div>
    </div>

    <!-- Quick Statistics -->
    <div class="grid-stats mb-3" style="grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));">
      <div class="stat-card">
        <div class="text-muted">ผู้ทรงคุณวุฒิที่ค้างประเมิน (รอบ 1)</div>
        <div class="stat-val" style="color: var(--warning);"><?= count($pendingPeerReviews) ?> รายการ</div>
      </div>
      <div class="stat-card">
        <div class="text-muted">กรรมการห้องที่ค้างประเมิน (รอบ 2)</div>
        <div class="stat-val" style="color: var(--info);"><?= count($pendingPresReviews) ?> รายการ</div>
      </div>
    </div>

    <!-- Search & Filter Controls -->
    <div class="card mb-3" style="padding: 1.25rem 1.5rem;">
      <div style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap; width: 100%;">
        <div style="flex: 1; min-width: 250px;">
          <input type="text" id="search-box" class="form-control" placeholder="🔍 ค้นหาตามชื่อผู้ทรงฯ, ชื่องานวิจัย หรือหน่วยงานต้นสังกัด..." style="padding: 0.5rem 0.75rem; font-size: 0.9rem;">
        </div>
        <div>
          <button type="button" onclick="clearSearch()" class="btn btn-secondary" style="padding: 0.5rem 1rem; font-size: 0.85rem; height: 38px;">🔄 ล้างคำค้น</button>
        </div>
      </div>
    </div>

    <!-- Tabs for different evaluation rounds -->
    <div class="tab-container">
      <button class="tab-button active" onclick="switchTab('peer-tab', this)">
        👤 1. ผู้ทรงคุณวุฒิประเมินบทความ (Peer Review)
        <span class="badge badge-pending" style="margin-left: 0.25rem; font-size: 0.7rem; padding: 0.15rem 0.4rem;"><?= count($pendingPeerReviews) ?></span>
      </button>
      <button class="tab-button" onclick="switchTab('pres-tab', this)">
        🏫 2. กรรมการห้องนำเสนอผลงาน (Presentation Review)
        <span class="badge badge-info" style="margin-left: 0.25rem; font-size: 0.7rem; padding: 0.15rem 0.4rem; background: rgba(2, 132, 199, 0.08); color: var(--info); border: 1px solid rgba(2, 132, 199, 0.15);"><?= count($pendingPresReviews) ?></span>
      </button>
    </div>

    <!-- Tab 1: Peer Review -->
    <div id="peer-tab" class="tab-content active">
      <div class="card">
        <h2 style="margin-bottom: 1rem; font-size: 1.25rem; color: var(--text-primary);">📝 รายการบทความที่อยู่ระหว่างการตรวจรอบแรก (Peer Review)</h2>
        
        <?php if (empty($pendingPeerReviews)): ?>
          <div class="text-center text-muted" style="padding: 3rem 0;">
            🎉 ยอดเยี่ยม! ไม่มีผู้ทรงคุณวุฒิค้างประเมินในรอบแรก
          </div>
        <?php else: ?>
          <div class="table-responsive">
            <table class="table" id="peer-table">
              <thead>
                <tr>
                  <th style="width: 250px;">ผู้ทรงคุณวุฒิ / สถาบัน</th>
                  <th style="width: 320px;">บทความวิชาการ / ผู้แต่ง</th>
                  <th style="width: 150px; text-align: center;">วันที่มอบหมาย</th>
                  <th style="width: 150px; text-align: center;">ค้างส่งแล้ว</th>
                  <th style="width: 150px; text-align: center;">การติดต่อ</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($pendingPeerReviews as $rev): ?>
                  <?php 
                    $assignedDate = new DateTime($rev['assigned_at']);
                    $now = new DateTime();
                    $daysDiff = $assignedDate->diff($now)->days;
                    
                    // Construct prefilled email
                    $subject = rawurlencode("ติดตามผลการประเมินบทความวิชาการ (งานประชุม CSRM)");
                    $body = rawurlencode(
                        "เรียน อาจารย์ " . $rev['reviewer_first'] . " " . $rev['reviewer_last'] . "\n\n" .
                        "ตามที่ฝ่ายประสานงานการประชุมวิชาการ CSRM " . $selectedConference['year'] . " ได้รับความอนุเคราะห์จากท่านในการประเมินบทความวิชาการเรื่อง:\n" .
                        "\"" . $rev['paper_title'] . "\"\n\n" .
                        "เนื่องจากขณะนี้ใกล้ครบกำหนดส่งผลการประเมินแล้ว ระบบจึงขออนุญาตแจ้งเพื่อติดตามผลการประเมินจากท่าน โดยท่านสามารถเข้าระบบประเมินได้ทาง:\n" .
                        base_url('reviewer/dashboard') . "\n\n" .
                        "ทางคณะจัดงานขอขอบพระคุณในความอนุเคราะห์ของท่านเป็นอย่างสูง\n\n" .
                        "ฝ่ายจัดการประเมินบทความ CSRM " . $selectedConference['year']
                    );
                    $mailtoUrl = "mailto:" . esc($rev['reviewer_email']) . "?subject=" . $subject . "&body=" . $body;
                  ?>
                  <tr class="searchable-row">
                    <!-- Reviewer Info -->
                    <td class="reviewer-cell">
                      <div style="font-weight: 600; color: var(--text-primary);"><?= esc($rev['reviewer_first']) ?> <?= esc($rev['reviewer_last']) ?></div>
                      <div style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 0.15rem;">
                        🏛️ <?= esc($rev['reviewer_affiliation'] ?: 'ไม่ได้ระบุหน่วยงาน') ?>
                      </div>
                      <div style="font-size: 0.75rem; color: var(--primary); margin-top: 0.15rem; font-family: monospace;">
                        ✉️ <?= esc($rev['reviewer_email']) ?>
                      </div>
                    </td>

                    <!-- Paper Info -->
                    <td class="paper-cell">
                      <div style="font-weight: 600; color: var(--text-primary); line-height: 1.4;"><?= esc($rev['paper_title']) ?></div>
                      <div style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 0.25rem;">
                        ผู้แต่ง: <strong><?= esc($rev['author_first']) ?> <?= esc($rev['author_last']) ?></strong> 
                        | สถานะ: 
                        <?php if ($rev['paper_status'] === 'under_review'): ?>
                          <span class="badge badge-info" style="font-size: 0.65rem; padding: 0.1rem 0.35rem;">กำลังประเมิน</span>
                        <?php else: ?>
                          <span class="badge badge-pending" style="font-size: 0.65rem; padding: 0.1rem 0.35rem;"><?= esc($rev['paper_status']) ?></span>
                        <?php endif; ?>
                      </div>
                    </td>

                    <!-- Date Assigned -->
                    <td style="text-align: center; font-size: 0.85rem;" data-order="<?= esc($rev['assigned_at']) ?>">
                      <?= date('d/m/Y H:i', strtotime($rev['assigned_at'])) ?>
                    </td>

                    <!-- Days pending -->
                    <td style="text-align: center;" data-order="<?= $daysDiff ?>">
                      <?php if ($daysDiff <= 3): ?>
                        <span class="badge badge-success" style="font-size: 0.8rem; border-radius: 4px; padding: 0.2rem 0.5rem; font-weight: 700;">
                          <?= $daysDiff ?> วัน
                        </span>
                      <?php elseif ($daysDiff <= 7): ?>
                        <span class="badge badge-pending" style="font-size: 0.8rem; border-radius: 4px; padding: 0.2rem 0.5rem; font-weight: 700;">
                          <?= $daysDiff ?> วัน
                        </span>
                      <?php else: ?>
                        <span class="badge badge-danger" style="font-size: 0.8rem; border-radius: 4px; padding: 0.2rem 0.5rem; font-weight: 700;">
                          <?= $daysDiff ?> วัน ⚠️
                        </span>
                      <?php endif; ?>
                    </td>

                    <!-- Actions -->
                    <td style="text-align: center;">
                      <a href="<?= $mailtoUrl ?>" class="btn-email">
                        ✉️ ส่งอีเมลทวงถาม
                      </a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Tab 2: Presentation Review -->
    <div id="pres-tab" class="tab-content">
      <div class="card">
        <h2 style="margin-bottom: 1rem; font-size: 1.25rem; color: var(--text-primary);">🏫 รายการกรรมการห้องที่ค้างประเมินผลนำเสนอ (Presentation Review)</h2>
        
        <?php if (empty($pendingPresReviews)): ?>
          <div class="text-center text-muted" style="padding: 3rem 0;">
            🎉 ยอดเยี่ยม! ไม่มีกรรมการห้องค้างส่งผลการนำเสนอ
          </div>
        <?php else: ?>
          <div class="table-responsive">
            <table class="table" id="pres-table">
              <thead>
                <tr>
                  <th style="width: 250px;">กรรมการประเมิน / สถาบัน</th>
                  <th style="width: 300px;">บทความวิชาการ / ห้องนำเสนอ</th>
                  <th style="width: 150px; text-align: center;">วันที่จัดห้อง</th>
                  <th style="width: 150px; text-align: center;">ค้างส่งแล้ว</th>
                  <th style="width: 150px; text-align: center;">การติดต่อ</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($pendingPresReviews as $rev): ?>
                  <?php 
                    $assignedDate = new DateTime($rev['assigned_at']);
                    $now = new DateTime();
                    $daysDiff = $assignedDate->diff($now)->days;

                    // Construct prefilled email
                    $subject = rawurlencode("ติดตามการลงคะแนนห้องนำเสนอผลงาน (งานประชุม CSRM)");
                    $body = rawurlencode(
                        "เรียน อาจารย์ " . $rev['committee_first'] . " " . $rev['committee_last'] . "\n\n" .
                        "ตามที่ท่านได้รับแต่งตั้งเป็นกรรมการประเมินการนำเสนอผลงานในห้อง:\n" .
                        "\"" . ($rev['room_name'] ?: 'ไม่ระบุห้อง') . "\"\n" .
                        "สำหรับบทความเรื่อง \"" . $rev['paper_title'] . "\"\n\n" .
                        "ระบบการประชุมวิชาการ CSRM " . $selectedConference['year'] . " ขอเรียนติดตามการลงคะแนนประเมินผลการนำเสนอผลงานจากท่าน โดยท่านสามารถกรอกคะแนนในระบบได้ที่:\n" .
                        base_url('committee/dashboard') . "\n\n" .
                        "คณะทำงานขอขอบพระคุณในความร่วมมือและช่วยเหลือเป็นอย่างดีเสมอมา\n\n" .
                        "ฝ่ายการประเมินนำเสนอ CSRM " . $selectedConference['year']
                    );
                    $mailtoUrl = "mailto:" . esc($rev['committee_email']) . "?subject=" . $subject . "&body=" . $body;
                  ?>
                  <tr class="searchable-row">
                    <!-- Committee Info -->
                    <td class="reviewer-cell">
                      <div style="font-weight: 600; color: var(--text-primary);"><?= esc($rev['committee_first']) ?> <?= esc($rev['committee_last']) ?></div>
                      <div style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 0.15rem;">
                        🏛️ <?= esc($rev['committee_affiliation'] ?: 'ไม่ได้ระบุหน่วยงาน') ?>
                      </div>
                      <div style="font-size: 0.75rem; color: var(--primary); margin-top: 0.15rem; font-family: monospace;">
                        ✉️ <?= esc($rev['committee_email']) ?>
                      </div>
                    </td>

                    <!-- Paper & Room Info -->
                    <td class="paper-cell">
                      <div style="font-weight: 600; color: var(--text-primary); line-height: 1.4;"><?= esc($rev['paper_title']) ?></div>
                      <div style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 0.25rem;">
                        ผู้แต่ง: <strong><?= esc($rev['author_first']) ?> <?= esc($rev['author_last']) ?></strong> 
                        | 🏫 ห้อง: <strong style="color: var(--primary);"><?= esc($rev['room_name'] ?: 'ไม่ได้ระบุห้องนำเสนอ') ?></strong>
                      </div>
                    </td>

                    <!-- Date Assigned -->
                    <td style="text-align: center; font-size: 0.85rem;" data-order="<?= esc($rev['assigned_at']) ?>">
                      <?= date('d/m/Y H:i', strtotime($rev['assigned_at'])) ?>
                    </td>

                    <!-- Days pending -->
                    <td style="text-align: center;" data-order="<?= $daysDiff ?>">
                      <?php if ($daysDiff <= 3): ?>
                        <span class="badge badge-success" style="font-size: 0.8rem; border-radius: 4px; padding: 0.2rem 0.5rem; font-weight: 700;">
                          <?= $daysDiff ?> วัน
                        </span>
                      <?php elseif ($daysDiff <= 7): ?>
                        <span class="badge badge-pending" style="font-size: 0.8rem; border-radius: 4px; padding: 0.2rem 0.5rem; font-weight: 700;">
                          <?= $daysDiff ?> วัน
                        </span>
                      <?php else: ?>
                        <span class="badge badge-danger" style="font-size: 0.8rem; border-radius: 4px; padding: 0.2rem 0.5rem; font-weight: 700;">
                          <?= $daysDiff ?> วัน ⚠️
                        </span>
                      <?php endif; ?>
                    </td>

                    <!-- Actions -->
                    <td style="text-align: center;">
                      <a href="<?= $mailtoUrl ?>" class="btn-email">
                        ✉️ ส่งอีเมลทวงถาม
                      </a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div>
    </div>

  </div>

  <script>
    // Tab switching logic
    function switchTab(tabId, btn) {
      // Hide all tabs
      const contents = document.querySelectorAll('.tab-content');
      contents.forEach(content => content.classList.remove('active'));
      
      const buttons = document.querySelectorAll('.tab-button');
      buttons.forEach(button => button.classList.remove('active'));
      
      // Show chosen tab
      document.getElementById(tabId).classList.add('active');
      btn.classList.add('active');
    }

    // Client-side quick search/filter
    document.getElementById('search-box').addEventListener('input', function(e) {
      const query = e.target.value.toLowerCase().trim();
      const rows = document.querySelectorAll('.searchable-row');
      
      rows.forEach(row => {
        const text = row.innerText.toLowerCase();
        if (text.includes(query)) {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      });
    });

    function clearSearch() {
      document.getElementById('search-box').value = '';
      const rows = document.querySelectorAll('.searchable-row');
      rows.forEach(row => row.style.display = '');
    }
  </script>

</body>
</html>
