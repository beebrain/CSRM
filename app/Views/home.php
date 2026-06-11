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

    /* Hero Section styling with Image Background & Overlay */
    .hero-section {
      position: relative;
      text-align: center;
      padding: 6.5rem 2rem;
      margin-bottom: 3rem;
      border-radius: var(--radius-lg);
      overflow: hidden;
      background-image: 
        linear-gradient(135deg, rgba(6, 95, 70, 0.88) 0%, rgba(15, 23, 42, 0.95) 100%),
        url('<?= base_url("images/conference_hero_banner.png"); ?>');
      background-size: cover;
      background-position: center;
      border: 1px solid rgba(6, 95, 70, 0.25);
      box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
      color: #ffffff;
    }
    
    .hero-tag {
      display: inline-block;
      padding: 0.35rem 1.2rem;
      background: rgba(255, 255, 255, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.2);
      color: #fcd34d; /* Bright gold/amber for high contrast */
      font-weight: 600;
      font-size: 0.85rem;
      border-radius: 9999px;
      margin-bottom: 1.5rem;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      backdrop-filter: blur(4px);
      -webkit-backdrop-filter: blur(4px);
    }

    .hero-title {
      font-size: 3rem;
      font-weight: 800;
      line-height: 1.2;
      color: #ffffff;
      margin-bottom: 1.25rem;
      letter-spacing: -0.02em;
      text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    }

    .hero-title span {
      color: #fbbf24; /* Bright amber gold for dark background */
      text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    }

    .hero-subtitle {
      font-size: 1.25rem;
      color: #f1f5f9; /* Slate-100 for high readability on dark overlay */
      max-width: 750px;
      margin: 0 auto 2.5rem auto;
      line-height: 1.8;
      text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
    }

    /* Hero Buttons */
    .btn-hero-primary {
      background: var(--primary-gradient);
      color: #ffffff;
      border: 1px solid rgba(255, 255, 255, 0.15);
      box-shadow: 0 4px 12px rgba(6, 95, 70, 0.3);
    }
    .btn-hero-primary:hover {
      background: #047857;
      transform: translateY(-1px);
      box-shadow: 0 6px 16px rgba(6, 95, 70, 0.45);
    }

    .btn-hero-gold {
      background: #fbbf24;
      color: #0f172a;
      border: 1px solid #d97706;
      font-weight: 700;
      box-shadow: 0 4px 12px rgba(251, 191, 36, 0.2);
    }
    .btn-hero-gold:hover {
      background: #f59e0b;
      transform: translateY(-1px);
      box-shadow: 0 6px 16px rgba(245, 158, 11, 0.4);
    }

    /* Button adjustments with WCAG AA compliance (dark slate text on gold) */
    .btn-gold {
      background: #fbbf24;
      color: #0f172a;
      border: 1px solid #d97706;
      font-weight: 700;
      box-shadow: 0 4px 12px rgba(217, 119, 6, 0.15);
    }
    .btn-gold:hover {
      background: #f59e0b;
      transform: translateY(-1px);
      box-shadow: 0 6px 16px rgba(217, 119, 6, 0.25);
    }

    /* Info grids & layouts */
    .section-title {
      text-align: center;
      margin-bottom: 0.5rem;
      font-weight: 800;
      font-size: 2rem;
      color: var(--text-primary);
    }
    
    .section-subtitle {
      text-align: center;
      color: var(--text-secondary);
      max-width: 600px;
      margin: 0 auto 3rem auto;
      font-size: 1rem;
    }

    /* About section split layout styling */
    @media (max-width: 768px) {
      .about-split {
        grid-template-columns: 1fr !important;
        gap: 1.5rem !important;
      }
    }

    /* Tracks Custom Grouping layout (removing identical card grids) */
    .tracks-container {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 2.5rem;
      margin-top: 3rem;
    }
    @media (max-width: 1024px) {
      .tracks-container {
        grid-template-columns: repeat(2, 1fr);
      }
    }
    @media (max-width: 768px) {
      .tracks-container {
        grid-template-columns: 1fr;
      }
    }
    
    .track-category-card {
      background: var(--card-bg);
      border: 1px solid var(--card-border);
      border-top: 4px solid var(--psru-green); /* Full top border accent instead of side-stripe */
      border-radius: var(--radius-md);
      padding: 2.25rem 2rem;
      box-shadow: var(--shadow);
      display: flex;
      flex-direction: column;
      gap: 1.5rem;
      transition: var(--transition);
    }
    
    .track-category-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 20px 25px -5px rgba(6, 95, 70, 0.05);
      border-color: rgba(6, 95, 70, 0.25);
      border-top-color: var(--psru-green);
    }
    
    .category-title {
      font-size: 1.25rem;
      font-weight: 700;
      color: var(--psru-green);
      border-bottom: 2px solid rgba(6, 95, 70, 0.1);
      padding-bottom: 0.5rem;
      margin-bottom: 0.5rem;
    }
    
    .track-list {
      list-style: none;
      padding: 0;
      margin: 0;
      display: flex;
      flex-direction: column;
      gap: 1.25rem;
    }
    
    .track-item {
      display: flex;
      flex-direction: column;
      gap: 0.25rem;
    }
    
    .track-name {
      font-weight: 600;
      color: var(--text-primary);
      font-size: 0.95rem;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }
    
    .track-name::before {
      content: "•";
      color: var(--psru-gold);
      font-size: 1.2rem;
      line-height: 1;
    }
    
    .track-desc {
      color: var(--text-secondary);
      font-size: 0.85rem;
      padding-left: 1rem;
      line-height: 1.5;
    }

    /* Timeline container */
    .timeline-container {
      background: var(--card-bg);
      border: 1px solid var(--card-border);
      border-radius: var(--radius-md);
      padding: 3rem 2rem;
      box-shadow: var(--shadow);
      margin-bottom: 4rem;
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
      padding: 4rem 1.5rem;
      text-align: center;
      margin-top: 5rem;
      background: rgba(255, 255, 255, 0.5);
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
    }

    /* Table styling for publication fee */
    .fee-table {
      width: 100%;
      border-collapse: collapse;
      margin: 1.5rem 0;
      font-size: 0.95rem;
    }
    .fee-table th, .fee-table td {
      border: 1px solid var(--card-border);
      padding: 0.75rem 1rem;
      text-align: left;
    }
    .fee-table th {
      background: rgba(6, 95, 70, 0.04);
      color: var(--psru-green);
      font-weight: 600;
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
      <a href="#about" class="navbar-item">หลักการและเหตุผล</a>
      <a href="#tracks" class="navbar-item">ศาสตร์การประชุม</a>
      <a href="#guidelines" class="navbar-item">การส่งบทความ</a>
      <a href="#fees" class="navbar-item">ค่าธรรมเนียมและการตีพิมพ์</a>
      <a href="#timeline" class="navbar-item">กำหนดการ</a>
      <a href="<?= base_url('auth/login'); ?>" class="btn btn-primary btn-sm">📥 ระบบส่งบทความ (Submission)</a>
    </div>
  </nav>

  <!-- Main Grid Container -->
  <div class="container">

    <!-- Hero Section -->
    <header class="hero-section animate-fade-in" style="animation-delay: 0.1s;">
      <div class="hero-tag">มหาวิทยาลัยราชภัฏพิบูลสงคราม • Pibulsongkram Rajabhat University</div>
      
      <?php if ($activeConf): ?>
        <h1 class="hero-title"><?= esc($activeConf['title']) ?></h1>
        <p class="hero-subtitle">
          ขอเชิญร่วมส่งบทความวิจัยและนำเสนอผลงานวิจัยในการประชุมวิชาการระดับชาติ ซึ่งจัดโดยเจ้าภาพหลักคือ <strong><?= esc($activeConf['host_name']) ?></strong>
        </p>
      <?php else: ?>
        <h1 class="hero-title">การประชุมคณิตศาสตร์ <span>ครั้งที่ 16</span></h1>
        <p class="hero-subtitle">
          ขอเชิญร่วมส่งบทความวิจัยและนำเสนอผลงานวิจัยในการประชุมวิชาการระดับชาติ ซึ่งจัดโดยเจ้าภาพหลักคือ <strong>มหาวิทยาลัยราชภัฏพิบูลสงคราม</strong>
        </p>
      <?php endif; ?>

      <div class="flex gap-2" style="flex-wrap: wrap; justify-content: center; align-items: center; width: 100%;">
        <a href="<?= base_url('auth/login'); ?>" class="btn btn-hero-gold" style="padding: 0.85rem 2rem;">📥 เข้าระบบส่งบทความ (Submission)</a>
        <a href="<?= base_url('auth/register'); ?>" class="btn btn-hero-primary" style="padding: 0.85rem 2rem;">👤 ลงทะเบียนสมัครสมาชิก</a>
      </div>
    </header>

    <!-- About / Rationale Section (Asymmetrical Split Layout) -->
    <section id="about" class="animate-fade-in" style="animation-delay: 0.2s; margin-top: 5rem; margin-bottom: 5rem;">
      <div class="about-split" style="display: grid; grid-template-columns: 2fr 3fr; gap: 3rem; align-items: start;">
        <div class="about-left">
          <h2 style="font-weight: 800; font-size: 2.25rem; color: var(--psru-green); line-height: 1.3; margin-bottom: 1.5rem;">
            หลักการและเหตุผล
          </h2>
          <div style="width: 60px; height: 4px; background: var(--psru-gold); border-radius: 2px; margin-bottom: 1.5rem;"></div>
          <p style="font-size: 1.1rem; font-weight: 500; color: var(--text-primary); line-height: 1.6;">
            การประชุมวิชาการระดับชาติ ประจำปี พ.ศ. 2569 ณ มหาวิทยาลัยราชภัฏพิบูลสงคราม ร่วมขับเคลื่อนการวิจัยคณิตศาสตร์และศึกษาศาสตร์สู่ระดับสากล
          </p>
        </div>
        <div class="about-right" style="background: var(--card-bg); border: 1px solid var(--card-border); border-radius: var(--radius-md); padding: 2.5rem; box-shadow: var(--shadow);">
          <p style="text-indent: 2rem; margin-bottom: 1.25rem; line-height: 1.8; text-align: justify; color: var(--text-secondary);">
            มหาวิทยาลัยราชภัฏพิบูลสงคราม ตระหนักถึงความสำคัญของการส่งเสริมการผลิตงานวิจัยและการเผยแพร่ผลงานวิชาการของคณาจารย์ นักวิจัย ตลอดจนนิสิตและนักศึกษาระดับอุดมศึกษา เพื่อเป็นเวทีกลางในการนำเสนอความก้าวหน้าและการวิจัยในสาขาวิทยาศาสตร์ คณิตศาสตร์ สถิติ และเทคโนโลยีสารสนเทศ การสร้างสรรค์ความรู้ใหม่รวมถึงการนำผลงานวิจัยไปพัฒนาเชิงพื้นที่อย่างเป็นรูปธรรม
          </p>
          <p style="text-indent: 2rem; line-height: 1.8; text-align: justify; color: var(--text-secondary); margin-bottom: 0;">
            เพื่อเป็นสื่อกลางการบูรณาการวิชาการและการนำไปใช้จริงในระดับประเทศ มหาวิทยาลัยราชภัฏพิบูลสงคราม ร่วมกับเครือข่ายความร่วมมือทางวิชาการจากมหาวิทยาลัยชั้นนำต่างๆ ทั่วประเทศ จึงกำหนดจัดโครงการประชุมวิชาการครั้งนี้ขึ้น เพื่อกระตุ้นให้เกิดเครือข่ายแลกเปลี่ยนความรู้ ยกระดับกระบวนการศึกษา และสร้างนวัตกรรมที่จะขับเคลื่อนการพัฒนาท้องถิ่นและยกระดับขีดความสามารถในการแข่งขันของประเทศให้เจริญเติบโตอย่างมั่นคง
          </p>
        </div>
      </div>
    </section>

    <!-- Tracks/Disciplines Section (Structured Grouping instead of Identical Card Grids) -->
    <section id="tracks" class="animate-fade-in" style="margin-top: 5rem; margin-bottom: 5rem;">
      <div style="text-align: center; margin-bottom: 3rem;">
        <h2 style="font-weight: 800; font-size: 2rem; color: var(--text-primary); margin-bottom: 0.5rem;">
          หัวข้อการประชุมและวิจัย
        </h2>
        <p style="color: var(--text-secondary); max-width: 600px; margin: 0 auto; font-size: 1rem;">
          สาขาวิชาการทางคณิตศาสตร์และคณิตศาสตรศึกษาที่เปิดรับข้อเสนอผลงานวิจัย
        </p>
      </div>
      
      <div class="tracks-container">
        <!-- Group 1: Pure Mathematics -->
        <div class="track-category-card">
          <h3 class="category-title">Pure Mathematics</h3>
          <ul class="track-list">
            <li class="track-item">
              <span class="track-name">พีชคณิต (Algebra)</span>
              <span class="track-desc">การศึกษาโครงสร้าง ความสัมพันธ์ และปริมาณ</span>
            </li>
            <li class="track-item">
              <span class="track-name">คณิตวิเคราะห์ (Analysis)</span>
              <span class="track-desc">ทฤษฎีลิมิต อนุพันธ์ อินทิกรัล และอนุกรมอนันต์</span>
            </li>
            <li class="track-item">
              <span class="track-name">เรขาคณิต (Geometry)</span>
              <span class="track-desc">คุณสมบัติของรูปร่าง ขนาด ตำแหน่งสัมพัทธ์ของรูปทรง</span>
            </li>
            <li class="track-item">
              <span class="track-name">ทฤษฎีจำนวน (Number Theory)</span>
              <span class="track-desc">คุณสมบัติของจำนวนเต็ม</span>
            </li>
            <li class="track-item">
              <span class="track-name">ตรรกศาสตร์คณิตศาสตร์ (Mathematical Logic)</span>
              <span class="track-desc">การศึกษาเกี่ยวกับระบบรูปแบบและการให้เหตุผล</span>
            </li>
            <li class="track-item">
              <span class="track-name">คอมบินาทอริก (Combinatorics)</span>
              <span class="track-desc">การนับ การจัดเรียง และโครงสร้าง</span>
            </li>
          </ul>
        </div>
        
        <!-- Group 2: Applied Mathematics & Statistics -->
        <div class="track-category-card">
          <h3 class="category-title">Applied & Stats</h3>
          <ul class="track-list">
            <li class="track-item">
              <span class="track-name">ความน่าจะเป็นและสถิติ (Probability & Stats)</span>
              <span class="track-desc">การวิเคราะห์และการตีความข้อมูลทางสถิติ</span>
            </li>
            <li class="track-item">
              <span class="track-name">คณิตศาสตร์ประยุกต์ (Applied Math)</span>
              <span class="track-desc">การประยุกต์ใช้วิธีการทางคณิตศาสตร์ในศาสตร์อื่นๆ</span>
            </li>
            <li class="track-item">
              <span class="track-name">คณิตศาสตร์ไม่ต่อเนื่อง (Discrete Math)</span>
              <span class="track-desc">โครงสร้างทางคณิตศาสตร์ที่มีลักษณะไม่ต่อเนื่อง</span>
            </li>
            <li class="track-item">
              <span class="track-name">สมการเชิงอนุพันธ์ (Differential Equations)</span>
              <span class="track-desc">สมการที่เกี่ยวข้องกับฟังก์ชันและอนุพันธ์</span>
            </li>
          </ul>
        </div>
        
        <!-- Group 3: Mathematics Education -->
        <div class="track-category-card">
          <h3 class="category-title">Mathematics Education</h3>
          <ul class="track-list">
            <li class="track-item">
              <span class="track-name">คณิตศาสตรศึกษา (Mathematics Education)</span>
              <span class="track-desc">การปฏิบัติและทฤษฎีการสอนและการเรียนรู้คณิตศาสตร์</span>
            </li>
          </ul>
        </div>
      </div>
    </section>

    <!-- Author Guidelines & Template Section (Asymmetrical layout and clear highlight) -->
    <section id="guidelines" class="animate-fade-in" style="margin-top: 5rem; margin-bottom: 5rem;">
      <div style="text-align: center; margin-bottom: 3rem;">
        <h2 style="font-weight: 800; font-size: 2rem; color: var(--text-primary); margin-bottom: 0.5rem;">
          การเตรียมบทความวิจัย
        </h2>
        <p style="color: var(--text-secondary); max-width: 600px; margin: 0 auto; font-size: 1rem;">
          คำแนะนำรูปแบบและเทมเพลตสำหรับผู้ส่งบทความเข้าสู่ระบบการประเมิน
        </p>
      </div>
      
      <div class="grid-2" style="align-items: stretch;">
        <!-- Card 1: Guidelines -->
        <div style="background: var(--card-bg); border: 1px solid var(--card-border); border-radius: var(--radius-md); padding: 2.5rem; box-shadow: var(--shadow); display: flex; flex-direction: column; justify-content: space-between;">
          <div>
            <h3 style="color: var(--psru-green); font-size: 1.25rem; font-weight: 700; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
              ✍️ ข้อแนะนำสำหรับผู้เขียนบทความ
            </h3>
            <ul style="list-style-type: none; padding: 0; display: flex; flex-direction: column; gap: 1rem; color: var(--text-secondary); font-size: 0.95rem; line-height: 1.7;">
              <li style="position: relative; padding-left: 1.5rem;">
                <span style="position: absolute; left: 0; color: var(--psru-gold); font-weight: bold;">1.</span>
                บทความที่ส่งต้องเป็นผลงานวิจัยใหม่ที่ไม่เคยตีพิมพ์หรือเผยแพร่ที่ใดมาก่อน
              </li>
              <li style="position: relative; padding-left: 1.5rem;">
                <span style="position: absolute; left: 0; color: var(--psru-gold); font-weight: bold;">2.</span>
                รองรับบทความวิจัยฉบับภาษาไทยและภาษาอังกฤษ
              </li>
              <li style="position: relative; padding-left: 1.5rem;">
                <span style="position: absolute; left: 0; color: var(--psru-gold); font-weight: bold;">3.</span>
                ความยาวของบทความวิจัยต้องอยู่ระหว่าง 6 - 8 หน้า ตามเทมเพลตที่กำหนด
              </li>
              <li style="position: relative; padding-left: 1.5rem;">
                <span style="position: absolute; left: 0; color: var(--psru-gold); font-weight: bold;">4.</span>
                ประเมินโดยผู้ทรงคุณวุฒิอย่างน้อย 2 ท่าน รูปแบบ Double-blind peer review
              </li>
            </ul>
          </div>
          <div style="margin-top: 2rem;">
            <a href="#" class="btn btn-secondary btn-sm" style="width: 100%; text-align: center; display: inline-block;">
              📖 อ่านคำแนะนำผู้เขียนอย่างละเอียด (PDF)
            </a>
          </div>
        </div>
        
        <!-- Card 2: Template (Highlight Card with light gradients and custom border) -->
        <div style="background: linear-gradient(135deg, rgba(6, 95, 70, 0.02) 0%, rgba(217, 119, 6, 0.02) 100%); border: 2px solid rgba(6, 95, 70, 0.15); border-radius: var(--radius-md); padding: 2.5rem; box-shadow: var(--shadow); display: flex; flex-direction: column; justify-content: space-between;">
          <div>
            <h3 style="color: var(--psru-green); font-size: 1.25rem; font-weight: 700; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
              📄 เทมเพลตบทความวิชาการ (Template)
            </h3>
            <p style="color: var(--text-secondary); font-size: 0.95rem; line-height: 1.7; margin-bottom: 1.5rem;">
              ผู้ส่งผลงานจะต้องจัดรูปแบบของบทความวิจัยให้ถูกต้องตามเอกสารต้นแบบของการประชุมวิชาการ เพื่อป้องกันความล่าช้าในขั้นตอนการกลั่นกรองบทความวิจัย (Peer Review) และขั้นตอนการรวมรูปเล่มรายงานสืบเนื่อง (Proceedings)
            </p>
          </div>
          <div>
            <a href="#" class="btn btn-gold btn-sm" style="width: 100%; text-align: center; display: inline-block;">
              📥 ดาวน์โหลด Template เอกสาร Microsoft Word (.docx)
            </a>
          </div>
        </div>
      </div>
    </section>

    <!-- Registration Fees & Publication Section (Refined table details and publication cards) -->
    <section id="fees" class="animate-fade-in" style="margin-top: 5rem; margin-bottom: 5rem;">
      <div style="text-align: center; margin-bottom: 3rem;">
        <h2 style="font-weight: 800; font-size: 2rem; color: var(--text-primary); margin-bottom: 0.5rem;">
          อัตราค่าลงทะเบียนและการตีพิมพ์
        </h2>
        <p style="color: var(--text-secondary); max-width: 600px; margin: 0 auto; font-size: 1rem;">
          ค่าธรรมเนียมการลงทะเบียนนำเสนอผลงานและข้อมูลการเผยแพร่วารสารเครือข่าย
        </p>
      </div>

      <div style="background: var(--card-bg); border: 1px solid var(--card-border); border-radius: var(--radius-md); padding: 2.5rem; box-shadow: var(--shadow); margin-bottom: 2.5rem;">
        <h3 style="color: var(--psru-green); font-size: 1.2rem; font-weight: 700; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
          💳 อัตราค่าธรรมเนียม
        </h3>
        <div class="table-responsive" style="margin-bottom: 1.5rem;">
          <table class="fee-table">
            <thead>
              <tr>
                <th style="font-weight: 700;">ประเภทผู้เข้าร่วมงาน</th>
                <th style="font-weight: 700; text-align: center;">อัตราลงทะเบียนปกติ</th>
                <th style="font-weight: 700; text-align: center; color: var(--psru-gold);">ลงทะเบียนล่วงหน้า (Early Bird)</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><strong>อาจารย์ / นักวิจัย / บุคคลทั่วไป (ผู้นำเสนอผลงาน)</strong></td>
                <td style="text-align: center;">3,000 บาท</td>
                <td style="text-align: center; font-weight: 600; color: var(--psru-green);">2,500 บาท</td>
              </tr>
              <tr>
                <td><strong>นักศึกษา (ผู้นำเสนอผลงาน) <span style="font-weight: normal; font-size: 0.85rem; color: var(--text-secondary);">*แนบบัตรนักศึกษา</span></strong></td>
                <td style="text-align: center;">2,000 บาท</td>
                <td style="text-align: center; font-weight: 600; color: var(--psru-green);">1,800 บาท</td>
              </tr>
              <tr>
                <td><strong>ผู้เข้าร่วมรับฟังการนำเสนอ (ไม่นำเสนอผลงาน)</strong></td>
                <td style="text-align: center;">1,000 บาท</td>
                <td style="text-align: center; font-weight: 600; color: var(--psru-green);">800 บาท</td>
              </tr>
            </tbody>
          </table>
        </div>
        <div style="background: rgba(6, 95, 70, 0.03); border-radius: var(--radius-sm); padding: 1rem; font-size: 0.85rem; color: var(--psru-green); line-height: 1.6;">
          <strong>หมายเหตุ:</strong> อัตราค่าลงทะเบียนครอบคลุมอาหารกลางวัน อาหารว่าง ของที่ระลึกงานประชุมวิชาการ สิทธิ์ในการนำเสนอผลงาน และการตีพิมพ์บทความลงในรายงานสืบเนื่อง (Proceedings) ของการประชุมวิชาการ
        </div>
      </div>

      <!-- Publication Network details -->
      <div style="background: var(--card-bg); border: 1px solid var(--card-border); border-radius: var(--radius-md); padding: 2.5rem; box-shadow: var(--shadow);">
        <h3 style="color: var(--psru-green); font-size: 1.2rem; font-weight: 700; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
          📘 โอกาสการได้รับการตีพิมพ์ในวารสารเครือข่าย (Publications)
        </h3>
        <div style="background: rgba(6, 95, 70, 0.02); border: 1px dashed var(--psru-green); border-radius: var(--radius-sm); padding: 2rem; text-align: center; color: var(--text-secondary); font-weight: 600; font-size: 1.05rem;">
          ⏳ อยู่ในกระบวนการติดต่อประสานงานวารสารเครือข่ายวิชาการ
        </div>
      </div>
    </section>

    <!-- Timeline Section (Refined styling with cleaner status details) -->
    <section id="timeline" class="animate-fade-in" style="margin-top: 5rem; margin-bottom: 5rem;">
      <div class="timeline-container" style="background: var(--card-bg); border: 1px solid var(--card-border); border-radius: var(--radius-md); padding: 3rem 2.5rem; box-shadow: var(--shadow);">
        <h2 style="font-weight: 800; font-size: 2rem; color: var(--text-primary); margin-bottom: 0.5rem;">
          กำหนดการและขั้นตอนสำคัญ
        </h2>
        <p style="color: var(--text-secondary); font-size: 0.95rem; margin-bottom: 3rem;">
          กรอบเวลาดำเนินงานสำหรับการเสนอผลงานบทความวิชาการระดับชาติ ประจำปี พ.ศ. 2569
        </p>
        
        <div class="timeline-list">
          <div class="timeline-item active">
            <div class="timeline-dot"></div>
            <div class="timeline-date">เฟสที่ 1: การเปิดรับบทความวิจัย</div>
            <strong style="display: block; margin-bottom: 0.5rem; font-size: 1.05rem;">Submission Phase</strong>
            <p class="text-muted" style="font-size: 0.85rem; line-height: 1.5;">
              <?= ($activeConf && $activeConf['accept_submissions']) ? '<span style="color: var(--psru-green-light); font-weight: 600;">🟢 กำลังเปิดรับผลงานใหม่</span>' : '<span style="color: var(--danger); font-weight: 600;">🔴 ปิดรับผลงานชั่วคราว</span>' ?>
            </p>
          </div>
          
          <div class="timeline-item active">
            <div class="timeline-dot"></div>
            <div class="timeline-date">เฟสที่ 2: การประเมินผลโดยผู้ทรงคุณวุฒิ</div>
            <strong style="display: block; margin-bottom: 0.5rem; font-size: 1.05rem;">Peer Review Phase</strong>
            <p class="text-muted" style="font-size: 0.85rem; line-height: 1.5;">
              <?= ($activeConf && $activeConf['accept_evaluations']) ? '<span style="color: var(--psru-green-light); font-weight: 600;">🟢 อยู่ระหว่างดำเนินการประเมิน</span>' : '<span style="color: var(--text-secondary);">⚪ รอเข้าสู่ช่วงการประเมิน</span>' ?>
            </p>
          </div>
          
          <div class="timeline-item">
            <div class="timeline-dot"></div>
            <div class="timeline-date">เฟสที่ 3: วันจัดงานประชุมวิชาการและการนำเสนอผลงาน</div>
            <strong style="display: block; margin-bottom: 0.5rem; font-size: 1.05rem;">Conference & Grading</strong>
            <p class="text-muted" style="font-size: 0.85rem; line-height: 1.5;">
              <?= ($activeConf && $activeConf['accept_grading']) ? '<span style="color: var(--psru-green-light); font-weight: 600;">🟢 กำลังบันทึกผลการนำเสนอ</span>' : '<span style="color: var(--text-secondary);">⚪ วันจัดงานประชุมวิชาการระดับชาติ</span>' ?>
            </p>
          </div>
        </div>
      </div>
    </section>

  </div>

  <!-- Contact & Footer Section -->
  <footer id="contact">
    <div style="max-width: 800px; margin: 0 auto;">
      <h3 style="color: var(--psru-green); font-weight: 700; margin-bottom: 0.75rem;">สำนักวิจัยและพัฒนา มหาวิทยาลัยราชภัฏพิบูลสงคราม</h3>
      <p class="text-muted" style="font-size: 0.85rem; line-height: 1.6; margin-bottom: 1.5rem;">
        เลขที่ 156 หมู่ 5 ถนนเลี่ยงเมืองพิษณุโลก (ส่วนทะเลแก้ว) ตำบลพลายชุมพล อำเภอเมือง จังหวัดพิษณุโลก 65000<br>
        โทรศัพท์: 0-5526-7000 ต่อ สำนักวิจัยและพัฒนา | อีเมล: research@psru.ac.th
      </p>
      <div style="border-top: 1px dashed var(--card-border); padding-top: 1.5rem; font-size: 0.8rem; color: var(--text-secondary);">
        &copy; 2026 CSRM System. All Rights Reserved. Pibulsongkram Rajabhat University.
      </div>
    </div>
  </footer>

</body>
</html>
