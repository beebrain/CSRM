<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>งานประชุมวิชาการระดับชาติ - มหาวิทยาลัยราชภัฏพิบูลสงคราม</title>
  <link rel="stylesheet" href="<?= base_url('css/style.css'); ?>">
  <style>
    /* Custom Green-Gold Theme override for PSRU branding */
    :root {
      --psru-green: #065f46; /* Emerald/Deep Green */
      --psru-green-light: #10b981;
      --psru-gold: #d97706; /* Amber/Gold */
      --psru-gold-light: #fbbf24;
      --primary: var(--psru-green);
      --primary-hover: #047857;
      --primary-gradient: linear-gradient(135deg, var(--psru-green) 0%, #047857 100%);
      --text-glow: 0 0 20px rgba(16, 185, 129, 0.15);
    }

    body {
      background-image: 
        radial-gradient(at 0% 0%, rgba(6, 95, 70, 0.05) 0px, transparent 50%),
        radial-gradient(at 100% 100%, rgba(217, 119, 6, 0.05) 0px, transparent 50%);
    }

    /* Hero Section styling */
    .hero-section {
      text-align: center;
      padding: 5rem 1.5rem;
      position: relative;
      margin-bottom: 2rem;
    }
    
    .hero-glow {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      width: 300px;
      height: 300px;
      background: radial-gradient(circle, rgba(16, 185, 129, 0.1) 0%, transparent 70%);
      pointer-events: none;
      z-index: -1;
    }

    .hero-tag {
      display: inline-block;
      padding: 0.35rem 1rem;
      background: rgba(6, 95, 70, 0.08);
      border: 1px solid rgba(6, 95, 70, 0.15);
      color: var(--psru-green);
      font-weight: 600;
      font-size: 0.85rem;
      border-radius: 9999px;
      margin-bottom: 1.5rem;
      text-transform: uppercase;
    }

    .hero-title {
      font-size: 2.75rem;
      font-weight: 800;
      line-height: 1.2;
      color: var(--text-primary);
      margin-bottom: 1rem;
      letter-spacing: -0.02em;
    }

    .hero-title span {
      background: linear-gradient(135deg, var(--psru-green) 0%, var(--psru-gold) 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }

    .hero-subtitle {
      font-size: 1.2rem;
      color: var(--text-secondary);
      max-width: 700px;
      margin: 0 auto 2.5rem auto;
      line-height: 1.7;
    }

    /* Button adjustments */
    .btn-gold {
      background: linear-gradient(135deg, var(--psru-gold) 0%, #b45309 100%);
      color: #ffffff;
      box-shadow: 0 4px 12px rgba(217, 119, 6, 0.2);
    }
    .btn-gold:hover {
      transform: translateY(-1px);
      box-shadow: 0 6px 16px rgba(217, 119, 6, 0.35);
    }

    /* Info grid styling */
    .info-section-title {
      text-align: center;
      margin-bottom: 2.5rem;
      font-weight: 700;
      font-size: 1.85rem;
    }

    .feature-card {
      background: var(--card-bg);
      border: 1px solid var(--card-border);
      border-radius: var(--radius-md);
      padding: 2rem;
      box-shadow: var(--shadow);
      transition: var(--transition);
      display: flex;
      flex-direction: column;
      gap: 1rem;
    }
    .feature-card:hover {
      transform: translateY(-4px);
      border-color: rgba(6, 95, 70, 0.2);
      box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05);
    }

    .feature-icon {
      width: 48px;
      height: 48px;
      border-radius: var(--radius-sm);
      background: rgba(6, 95, 70, 0.08);
      color: var(--psru-green);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.5rem;
    }

    /* Timeline component */
    .timeline-container {
      margin-top: 3.5rem;
      background: var(--card-bg);
      border: 1px solid var(--card-border);
      border-radius: var(--radius-md);
      padding: 2.5rem 2rem;
      box-shadow: var(--shadow);
    }

    .timeline-list {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 2rem;
      margin-top: 2rem;
      position: relative;
    }

    .timeline-item {
      position: relative;
      padding-left: 1.25rem;
      border-left: 2px solid var(--card-border);
    }

    .timeline-item.active {
      border-left-color: var(--psru-green);
    }

    .timeline-dot {
      position: absolute;
      left: -6px;
      top: 6px;
      width: 10px;
      height: 10px;
      border-radius: 50%;
      background: #cbd5e1;
    }

    .timeline-item.active .timeline-dot {
      background: var(--psru-green);
      box-shadow: 0 0 8px var(--psru-green-light);
    }

    .timeline-date {
      font-size: 0.8rem;
      font-weight: 700;
      color: var(--psru-green);
      margin-bottom: 0.25rem;
    }

    /* Footer styling */
    footer {
      border-top: 1px solid var(--card-border);
      padding: 3rem 1.5rem;
      text-align: center;
      margin-top: 5rem;
      background: rgba(255, 255, 255, 0.5);
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
    }
  </style>
</head>
<body>

  <!-- Navigation Bar -->
  <nav class="navbar animate-fade-in">
    <div class="navbar-brand" style="color: var(--psru-green); font-weight: 800;">
      🎓 PSRU CSRM
    </div>
    <div class="navbar-menu">
      <a href="#about" class="navbar-item">เกี่ยวกับ มรภ.พิบูลสงคราม</a>
      <a href="#tracks" class="navbar-item">ศาสตร์บทความ</a>
      <a href="#timeline" class="navbar-item">กำหนดการ</a>
      <a href="#contact" class="navbar-item">ติดต่อสอบถาม</a>
      <a href="<?= base_url('auth/login'); ?>" class="btn btn-primary btn-sm">📥 ระบบส่งบทความ (Submission)</a>
    </div>
  </nav>

  <!-- Main Grid Container -->
  <div class="container">

    <!-- Hero Section -->
    <header class="hero-section animate-fade-in" style="animation-delay: 0.1s;">
      <div class="hero-glow"></div>
      <div class="hero-tag">มหาวิทยาลัยราชภัฏพิบูลสงคราม • Pibulsongkram Rajabhat University</div>
      
      <?php if ($activeConf): ?>
        <h1 class="hero-title">การประชุมวิชาการระดับชาติ ประจำปี <span>พ.ศ. <?= esc($activeConf['year']) ?></span></h1>
        <h2 style="font-size: 1.35rem; font-weight: 600; color: var(--psru-green); margin-bottom: 1.5rem;"><?= esc($activeConf['title']) ?></h2>
        <p class="hero-subtitle">
          ขอเชิญนักวิจัย คณาจารย์ นิสิต นักศึกษา และผู้สนใจ ส่งบทความวิจัยเข้าร่วมการนำเสนอผลงานวิจัยในการประชุมวิชาการระดับชาติ ณ มหาวิทยาลัยราชภัฏพิบูลสงคราม โดยมีเจ้าภาพร่วมจัดงานคือ <strong><?= esc($activeConf['host_name']) ?></strong>
        </p>
      <?php else: ?>
        <h1 class="hero-title">งานประชุมวิชาการระดับชาติ <span>มรภ.พิบูลสงคราม</span></h1>
        <p class="hero-subtitle">
          ขอเชิญนักวิจัย คณาจารย์ และนิสิตนักศึกษาเข้าร่วมการแลกเปลี่ยนความรู้ทางวิชาการและนำเสนอผลงานวิจัยระดับประเทศ ในระบบจัดการบทความวิชาการอิเล็กทรอนิกส์
        </p>
      <?php endif; ?>

      <div class="flex justify-center gap-2" style="flex-wrap: wrap;">
        <a href="<?= base_url('auth/login'); ?>" class="btn btn-primary" style="padding: 0.85rem 2rem;">📥 เข้าระบบส่งบทความ (Submission)</a>
        <a href="<?= base_url('auth/register'); ?>" class="btn btn-gold" style="padding: 0.85rem 2rem;">👤 สมัครสมาชิกผู้แต่งใหม่</a>
      </div>
    </header>

    <!-- About PSRU Section -->
    <section id="about" class="animate-fade-in" style="animation-delay: 0.25s; margin-top: 4rem;">
      <h2 class="info-section-title">🏫 แนะนำมหาวิทยาลัยราชภัฏพิบูลสงคราม</h2>
      <div class="grid-3">
        <div class="feature-card">
          <div class="feature-icon">🏛️</div>
          <h3>เกี่ยวกับมหาวิทยาลัย</h3>
          <p class="text-muted" style="font-size: 0.9rem; line-height: 1.6;">
            มหาวิทยาลัยราชภัฏพิบูลสงคราม (PSRU) ตั้งอยู่ที่จังหวัดพิษณุโลก เป็นสถาบันการศึกษาระดับอุดมศึกษาที่มีบทบาทสำคัญในการพัฒนาชุมชนและท้องถิ่น มุ่งเน้นการสร้างองค์ความรู้ นวัตกรรม และให้บริการวิชาการแก่สังคมอย่างยั่งยืน
          </p>
        </div>
        
        <div class="feature-card">
          <div class="feature-icon">🔬</div>
          <h3>งานวิจัยและวิชาการ</h3>
          <p class="text-muted" style="font-size: 0.9rem; line-height: 1.6;">
            มุ่งสนับสนุนการค้นคว้าและส่งเสริมการสร้างผลงานวิจัยของคณาจารย์และนิสิตเพื่อประยุกต์ใช้ในการแก้ไขปัญหาของชุมชน ตลอดจนการพัฒนาเครือข่ายความร่วมมือวิชาการระหว่างสถาบันทั่วประเทศ
          </p>
        </div>
        
        <div class="feature-card">
          <div class="feature-icon">📍</div>
          <h3>สถานที่จัดการประชุม</h3>
          <p class="text-muted" style="font-size: 0.9rem; line-height: 1.6;">
            งานจัดขึ้น ณ ศูนย์วัฒนธรรมภาคเหนือตอนล่าง วังจันทน์ริเวอร์วิว หรือห้องประชุมใหญ่ของทางมหาวิทยาลัย พร้อมเทคโนโลยีและการอำนวยความสะดวกที่เพียบพร้อมสำหรับผู้ส่งมอบผลงานและการพรีเซนต์
          </p>
        </div>
      </div>
    </section>

    <!-- Tracks/Disciplines Section -->
    <section id="tracks" class="animate-fade-in" style="animation-delay: 0.4s; margin-top: 5rem;">
      <h2 class="info-section-title">📚 ศาสตร์วิชาการที่เปิดรับสมัคร</h2>
      <p class="text-center text-muted" style="max-width: 600px; margin: -1.5rem auto 2.5rem auto;">
        เรายินดีต้อนรับการนำเสนอผลงานวิจัยครอบคลุมในหลายสาขาและประยุกต์ใช้เพื่อการพัฒนาองค์ความรู้
      </p>
      
      <div class="grid-2">
        <div class="card">
          <h3 style="color: var(--psru-green); margin-bottom: 1rem;">🧮 วิทยาศาสตร์และคณิตศาสตร์ระดับชาติ</h3>
          <ul style="list-style: none; padding-left: 0; display: flex; flex-direction: column; gap: 0.5rem; font-size: 0.95rem;">
            <li>🔹 คณิตศาสตร์เชิงทฤษฎีและคณิตศาสตร์บริสุทธิ์</li>
            <li>🔹 คณิตศาสตร์ประยุกต์ วิศวกรรมศาสตร์ และวิทยาการคอมพิวเตอร์</li>
            <li>🔹 สถิติศาสตร์ คณิตศาสตร์ประกันภัย และการวิเคราะห์ข้อมูลเชิงลึก</li>
          </ul>
        </div>
        <div class="card">
          <h3 style="color: var(--psru-green); margin-bottom: 1rem;">💻 เทคโนโลยีและนวัตกรรมสร้างสรรค์</h3>
          <ul style="list-style: none; padding-left: 0; display: flex; flex-direction: column; gap: 0.5rem; font-size: 0.95rem;">
            <li>🔹 คอมพิวเตอร์ เทคโนโลยีสารสนเทศ และระบบปัญญาประดิษฐ์</li>
            <li>🔹 วิทยาการข้อมูล (Data Science) และเทคโนโลยีประยุกต์</li>
            <li>🔹 งานวิจัยร่วมวิชาการและนวัตกรรมสีเขียวเพื่อสังคม</li>
          </ul>
        </div>
      </div>
    </section>

    <!-- Timeline Section -->
    <section id="timeline" class="animate-fade-in" style="animation-delay: 0.5s;">
      <div class="timeline-container">
        <h2 style="font-weight: 700; font-size: 1.5rem; margin-bottom: 0.5rem;">📅 กำหนดการและขั้นตอนการส่งบทความ</h2>
        <p class="text-muted" style="font-size: 0.9rem;">ติดตามช่วงเวลาสำคัญในการดำเนินการบทความวิชาการประจำปีนี้</p>
        
        <div class="timeline-list">
          <div class="timeline-item active">
            <div class="timeline-dot"></div>
            <div class="timeline-date">เฟสที่ 1: การรับส่งบทความวิชาการ</div>
            <strong>Submission Phase</strong>
            <p class="text-muted" style="font-size: 0.8rem; margin-top: 0.25rem;">
              <?= ($activeConf && $activeConf['accept_submissions']) ? '🟢 ขณะนี้ระบบกำลังเปิดรับผลงานใหม่' : '🔴 ขณะนี้ปิดรับผลงานชั่วคราว/คลังข้อมูล' ?>
            </p>
          </div>
          
          <div class="timeline-item active">
            <div class="timeline-dot"></div>
            <div class="timeline-date">เฟสที่ 2: การประเมินบทความ</div>
            <strong>Peer Review Phase</strong>
            <p class="text-muted" style="font-size: 0.8rem; margin-top: 0.25rem;">
              <?= ($activeConf && $activeConf['accept_evaluations']) ? '🟢 ผู้ทรงคุณวุฒิกำลังดำเนินการประเมินผล' : '⚪ รอเปิดการประเมินวิชาการ' ?>
            </p>
          </div>
          
          <div class="timeline-item">
            <div class="timeline-dot"></div>
            <div class="timeline-date">เฟสที่ 3: วันจัดงานนำเสนอและให้คะแนน</div>
            <strong>Conference & Grading</strong>
            <p class="text-muted" style="font-size: 0.8rem; margin-top: 0.25rem;">
              <?= ($activeConf && $activeConf['accept_grading']) ? '🟢 คณะกรรมการกำลังดำเนินการบันทึกคะแนนห้องพรีเซนต์' : '⚪ วันจัดงานประชุมวิชาการระดับชาติ' ?>
            </p>
          </div>
        </div>
      </div>
    </section>

  </div>

  <!-- Contact & Footer Section -->
  <footer id="contact">
    <div style="max-width: 800px; margin: 0 auto;">
      <h3 style="color: var(--psru-green); font-weight: 700; margin-bottom: 0.75rem;">มหาวิทยาลัยราชภัฏพิบูลสงคราม (ส่วนทะเลแก้ว)</h3>
      <p class="text-muted" style="font-size: 0.85rem; line-height: 1.6; margin-bottom: 1.5rem;">
        เลขที่ 156 หมู่ 5 ถนนเลี่ยงเมืองพิษณุโลก ตำบลพลายชุมพล อำเภอเมือง จังหวัดพิษณุโลก 65000<br>
        โทรศัพท์: 0-5526-7000 ต่อ สำนักวิจัยและพัฒนา | อีเมล: research@psru.ac.th
      </p>
      <div style="border-top: 1px dashed var(--card-border); padding-top: 1.5rem; font-size: 0.8rem; color: var(--text-secondary);">
        &copy; 2026 CSRM System. All Rights Reserved. Pibulsongkram Rajabhat University.
      </div>
    </div>
  </footer>

</body>
</html>
