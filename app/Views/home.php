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
      padding: 6rem 1.5rem 4rem 1.5rem;
      position: relative;
      margin-bottom: 2rem;
    }
    
    .hero-glow {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      width: 350px;
      height: 350px;
      background: radial-gradient(circle, rgba(16, 185, 129, 0.08) 0%, transparent 70%);
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
      font-size: 3rem;
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
      font-size: 1.25rem;
      color: var(--text-secondary);
      max-width: 750px;
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

    /* Info grids */
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

    .about-card {
      background: var(--card-bg);
      border: 1px solid var(--card-border);
      border-radius: var(--radius-md);
      padding: 2.5rem;
      box-shadow: var(--shadow);
      margin-bottom: 2rem;
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

    /* Speaker profiles */
    .speaker-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 2rem;
      margin-bottom: 4rem;
    }
    .speaker-card {
      background: var(--card-bg);
      border: 1px solid var(--card-border);
      border-radius: var(--radius-md);
      padding: 2rem;
      text-align: center;
      box-shadow: var(--shadow);
      transition: var(--transition);
    }
    .speaker-card:hover {
      transform: translateY(-4px);
      border-color: rgba(6, 95, 70, 0.2);
    }
    .speaker-avatar {
      width: 110px;
      height: 110px;
      border-radius: 50%;
      background: rgba(6, 95, 70, 0.08);
      color: var(--psru-green);
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-size: 3rem;
      margin-bottom: 1.25rem;
      border: 3px solid var(--card-border);
    }
    .speaker-name {
      font-weight: 700;
      font-size: 1.15rem;
      margin-bottom: 0.25rem;
      color: var(--text-primary);
    }
    .speaker-title {
      font-size: 0.85rem;
      color: var(--psru-green);
      font-weight: 600;
      margin-bottom: 0.75rem;
    }
    .speaker-desc {
      font-size: 0.85rem;
      color: var(--text-secondary);
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
      <a href="#speakers" class="navbar-item">วิทยากร</a>
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
      <div class="hero-glow"></div>
      <div class="hero-tag">มหาวิทยาลัยราชภัฏพิบูลสงคราม • Pibulsongkram Rajabhat University</div>
      
      <?php if ($activeConf): ?>
        <h1 class="hero-title">การประชุมวิชาการระดับชาติ ประจำปี <span>พ.ศ. <?= esc($activeConf['year']) ?></span></h1>
        <h2 style="font-size: 1.35rem; font-weight: 600; color: var(--psru-green); margin-bottom: 1.5rem;"><?= esc($activeConf['title']) ?></h2>
        <p class="hero-subtitle">
          ขอเชิญร่วมส่งบทความวิจัยและนำเสนอผลงานวิจัยในการประชุมวิชาการระดับชาติ ซึ่งจัดโดยเจ้าภาพหลักคือ <strong><?= esc($activeConf['host_name']) ?></strong>
        </p>
      <?php else: ?>
        <h1 class="hero-title">งานประชุมวิชาการระดับชาติ <span>มรภ.พิบูลสงคราม</span></h1>
        <p class="hero-subtitle">
          เวทีแลกเปลี่ยนความรู้ทางวิชาการและนำเสนอผลงานวิจัยระดับประเทศ ในระบบจัดการบทความวิชาการอิเล็กทรอนิกส์
        </p>
      <?php endif; ?>

      <div class="flex justify-center gap-2" style="flex-wrap: wrap;">
        <a href="<?= base_url('auth/login'); ?>" class="btn btn-primary" style="padding: 0.85rem 2rem;">📥 เข้าระบบส่งบทความ (Submission)</a>
        <a href="<?= base_url('auth/register'); ?>" class="btn btn-gold" style="padding: 0.85rem 2rem;">👤 ลงทะเบียนสมัครสมาชิก</a>
      </div>
    </header>

    <!-- About / Rationale Section -->
    <section id="about" class="animate-fade-in" style="animation-delay: 0.2s; margin-top: 4rem;">
      <h2 class="section-title">📝 หลักการและเหตุผล</h2>
      <p class="section-subtitle">การประชุมวิชาการระดับชาติ (Conference on Science and Research in Mathematics)</p>
      
      <div class="about-card">
        <p style="text-indent: 2.5rem; margin-bottom: 1rem; line-height: 1.8; text-align: justify;">
          มหาวิทยาลัยราชภัฏพิบูลสงคราม ตระหนักถึงความสำคัญของการส่งเสริมการผลิตงานวิจัยและการเผยแพร่ผลงานวิชาการของคณาจารย์ นักวิจัย ตลอดจนนิสิตและนักศึกษาระดับอุดมศึกษา เพื่อเป็นเวทีกลางในการนำเสนอความก้าวหน้าและการวิจัยในสาขาวิทยาศาสตร์ คณิตศาสตร์ สถิติ และเทคโนโลยีสารสนเทศ การสร้างสรรค์ความรู้ใหม่รวมถึงการนำผลงานวิจัยไปพัฒนาเชิงพื้นที่อย่างเป็นรูปธรรม
        </p>
        <p style="text-indent: 2.5rem; line-height: 1.8; text-align: justify;">
          เพื่อเป็นสื่อกลางการบูรณาการวิชาการและการนำไปใช้จริงในระดับประเทศ มหาวิทยาลัยราชภัฏพิบูลสงคราม ร่วมกับเครือข่ายความร่วมมือทางวิชาการจากมหาวิทยาลัยชั้นนำต่างๆ ทั่วประเทศ จึงกำหนดจัดโครงการประชุมวิชาการครั้งนี้ขึ้น เพื่อกระตุ้นให้เกิดเครือข่ายแลกเปลี่ยนความรู้ ยกระดับกระบวนการศึกษา และสร้างนวัตกรรมที่จะขับเคลื่อนการพัฒนาท้องถิ่นและยกระดับขีดความสามารถการแข่งขันของประเทศให้เจริญเติบโตอย่างมั่นคง
        </p>
      </div>
    </section>

    <!-- Keynote Speakers Section -->
    <section id="speakers" class="animate-fade-in" style="animation-delay: 0.3s; margin-top: 4rem;">
      <h2 class="section-title">🎤 วิทยากรผู้ทรงคุณวุฒิ (Keynote Speakers)</h2>
      <p class="section-subtitle">ผู้เชี่ยวชาญระดับแนวหน้าที่จะมาแบ่งปันนวัตกรรมและวิสัยทัศน์ทางวิชาการ</p>
      
      <div class="speaker-grid">
        <!-- Speaker 1 -->
        <div class="speaker-card">
          <div class="speaker-avatar">👨‍🏫</div>
          <div class="speaker-name">ศ.ดร. ณรงค์ศักดิ์ สุขประเสริฐ</div>
          <div class="speaker-title">ศาสตราจารย์สาขาคณิตศาสตร์ประยุกต์</div>
          <div class="speaker-desc">บรรยายพิเศษในหัวข้อ "คณิตศาสตร์ประยุกต์และวิทยาการข้อมูลยุคใหม่เพื่อความร่วมมือทางวิจัยระดับชาติ"</div>
        </div>
        
        <!-- Speaker 2 -->
        <div class="speaker-card">
          <div class="speaker-avatar">👩‍💻</div>
          <div class="speaker-name">รศ.ดร. นลินี พิบูลพัฒน์</div>
          <div class="speaker-title">ผู้อำนวยการศูนย์วิจัยนวัตกรรมปัญญาประดิษฐ์</div>
          <div class="speaker-desc">บรรยายพิเศษในหัวข้อ "บทบาท AI และคอมพิวเตอร์ศาสตร์ในการยกระดับงานวิจัยท้องถิ่นสู่สากล"</div>
        </div>
        
        <!-- Speaker 3 -->
        <div class="speaker-card">
          <div class="speaker-avatar">👨‍🔬</div>
          <div class="speaker-name">ดร. สมชาย มงคลศิลป์</div>
          <div class="speaker-title">ผู้เชี่ยวชาญอาวุโสด้านเทคโนโลยีสารสนเทศ</div>
          <div class="speaker-desc">บรรยายพิเศษในหัวข้อ "ทิศทางนวัตกรรมและวิทยาการคำนวณกับการเปลี่ยนผ่านสังคมไทย 5.0"</div>
        </div>
      </div>
    </section>

    <!-- Tracks/Disciplines Section -->
    <section id="tracks" class="animate-fade-in" style="animation-delay: 0.4s; margin-top: 4rem;">
      <h2 class="section-title">📚 หัวข้อผลงานวิจัยที่เปิดรับ (Conference Tracks)</h2>
      <p class="section-subtitle">สาขาวิชาการที่รองรับการรับบทความในการประชุมวิชาการประจำปีนี้</p>
      
      <div class="grid-3">
        <div class="feature-card">
          <div class="feature-icon">🧮</div>
          <h3>Theory & Theoretical Math</h3>
          <p class="text-muted" style="font-size: 0.85rem; line-height: 1.6;">
            คณิตศาสตร์บริสุทธิ์ คณิตศาสตร์ทฤษฎี พีชคณิต ทฤษฎีจำนวน การวิเคราะห์เชิงจริง และหัวข้อที่เกี่ยวข้องทางคณิตศาสตร์เชิงลึก
          </p>
        </div>
        
        <div class="feature-card">
          <div class="feature-icon">📊</div>
          <h3>Applied Math & Statistics</h3>
          <p class="text-muted" style="font-size: 0.85rem; line-height: 1.6;">
            คณิตศาสตร์ประยุกต์ สถิติประยุกต์ การวิจัยการดำเนินงาน คณิตศาสตร์ประกันภัย และการประยุกต์แบบจำลองทางคณิตศาสตร์ในอุตสาหกรรม
          </p>
        </div>
        
        <div class="feature-card">
          <div class="feature-icon">💻</div>
          <h3>Computer Science & IT</h3>
          <p class="text-muted" style="font-size: 0.85rem; line-height: 1.6;">
            วิทยาการคอมพิวเตอร์ เทคโนโลยีสารสนเทศ ปัญญาประดิษฐ์ (AI) การประมวลผลระบบคลาวด์ การทำเหมืองข้อมูล และวิศวกรรมซอฟต์แวร์
          </p>
        </div>
      </div>
    </section>

    <!-- Author Guidelines & Template Section -->
    <section id="guidelines" class="animate-fade-in" style="animation-delay: 0.45s; margin-top: 4rem;">
      <h2 class="section-title">📄 คำแนะนำและรูปแบบการเขียนบทความ (Guidelines)</h2>
      <p class="section-subtitle">ข้อมูลคำแนะนำที่เป็นประโยชน์ต่อผู้เขียนบทความวิชาการ</p>
      
      <div class="grid-2">
        <div class="card">
          <h3 style="color: var(--psru-green); margin-bottom: 1rem;">✍️ คำแนะนำสำหรับผู้เขียนบทความ</h3>
          <p class="text-muted" style="font-size: 0.9rem; line-height: 1.7; margin-bottom: 1.25rem;">
            1. บทความที่ส่งต้องเป็นผลงานวิจัยใหม่ที่ไม่เคยตีพิมพ์หรือเผยแพร่ที่ใดมาก่อน<br>
            2. สามารถเขียนได้ทั้งภาษาไทยและภาษาอังกฤษ<br>
            3. ความยาวของบทความวิชาการต้องอยู่ระหว่าง 6 - 8 หน้า ตามเทมเพลตที่กำหนด<br>
            4. บทความวิชาการจะถูกประเมินโดยผู้ทรงคุณวุฒิอย่างน้อย 2 ท่าน (Double-blind peer review)
          </p>
          <a href="#" class="btn btn-secondary btn-sm" style="width: 100%; text-align: center;">📖 อ่านคำแนะนำผู้เขียน (PDF)</a>
        </div>
        
        <div class="card">
          <h3 style="color: var(--psru-green); margin-bottom: 1rem;">💾 รูปแบบและโครงสร้างบทความ (Template)</h3>
          <p class="text-muted" style="font-size: 0.9rem; line-height: 1.7; margin-bottom: 1.25rem;">
            ผู้ส่งผลงานจะต้องจัดรูปแบบของบทความอย่างถูกต้องตามเอกสารต้นแบบของงานสัมมนา เพื่อป้องกันความล่าช้าในขั้นตอนการกลั่นกรองบทความวิจัย และการจัดทำเล่มรายงานสืบเนื่องจากการประชุมวิชาการ (Proceedings)
          </p>
          <a href="#" class="btn btn-gold btn-sm" style="width: 100%; text-align: center;">📥 ดาวน์โหลด Template เอกสาร (.docx)</a>
        </div>
      </div>
    </section>

    <!-- Registration Fees & Publication Section -->
    <section id="fees" class="animate-fade-in" style="animation-delay: 0.5s; margin-top: 4rem;">
      <h2 class="section-title">💳 อัตราค่าลงทะเบียนและการตีพิมพ์</h2>
      <p class="section-subtitle">ค่าธรรมเนียมและสิทธิประโยชน์ในการตีพิมพ์เผยแพร่บทความสัมมนาวิชาการ</p>
      
      <div class="card">
        <h3 style="color: var(--psru-green);">💵 อัตราค่าลงทะเบียนสำหรับการนำเสนอ</h3>
        <div class="table-responsive">
          <table class="fee-table">
            <thead>
              <tr>
                <th>ประเภทผู้เข้าร่วมงาน</th>
                <th>อัตราค่าลงทะเบียนปกติ</th>
                <th>อัตราลงทะเบียนล่วงหน้า (Early Bird)</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><strong>อาจารย์ / นักวิจัย / บุคคลทั่วไป (ผู้นำเสนอผลงาน)</strong></td>
                <td>3,000 บาท</td>
                <td>2,500 บาท</td>
              </tr>
              <tr>
                <td><strong>นักศึกษา (ผู้นำเสนอผลงาน) *ต้องแนบบัตรนักศึกษา</strong></td>
                <td>2,000 บาท</td>
                <td>1,800 บาท</td>
              </tr>
              <tr>
                <td><strong>ผู้เข้าร่วมรับฟังการนำเสนอ (ไม่นำเสนอผลงาน)</strong></td>
                <td>1,000 บาท</td>
                <td>800 บาท</td>
              </tr>
            </tbody>
          </table>
        </div>
        <p class="text-muted" style="font-size: 0.8rem; margin-top: 0.5rem;">
          * ค่าลงทะเบียนครอบคลุมถึงอาหารว่าง อาหารกลางวัน ของที่ระลึกงานประชุม สิทธิ์ในการนำเสนอ และการเผยแพร่ผลงานวิชาการลงในเล่มรายงานสืบเนื่อง (Proceedings) ของการประชุมวิชาการ
        </p>

        <h3 style="color: var(--psru-green); margin-top: 2rem; margin-bottom: 1rem;">📖 การตีพิมพ์เผยแพร่ในวารสารเครือข่าย</h3>
        <p class="text-muted" style="font-size: 0.9rem; line-height: 1.7;">
          บทความวิจัยที่มีคุณภาพโดดเด่นและผ่านการคัดเลือกจากกรรมการผู้ทรงคุณวุฒิ จะได้รับสิทธิ์ในการเสนอเพื่อลงตีพิมพ์ในวารสารเครือข่ายวิชาการระดับชาติของ มหาวิทยาลัยราชภัฏพิบูลสงคราม ได้แก่:<br>
          - 📘 **วารสารวิชาการ มหาวิทยาลัยราชภัฏพิบูลสงคราม** (สาขาวิทยาศาสตร์และเทคโนโลยี - ฐานข้อมูล TCI กลุ่ม 1)<br>
          - 📙 **วารสารวิจัยและพัฒนา มรภ.พิบูลสงคราม** (ฐานข้อมูล TCI กลุ่ม 2)
        </p>
      </div>
    </section>

    <!-- Timeline Section -->
    <section id="timeline" class="animate-fade-in" style="animation-delay: 0.55s;">
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
