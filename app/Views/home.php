<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
  <title>งานประชุมวิชาการระดับชาติ - มหาวิทยาลัยราชภัฏพิบูลสงคราม</title>
  <link rel="stylesheet" href="<?= base_url('css/style.css'); ?>">

</head>
<body>

  <!-- Navigation Bar -->
  <nav class="navbar animate-fade-in">
    <div class="navbar-brand" style="color: var(--psru-green); font-weight: 800;">
      🎓 PSRU CSRM
    </div>
    <input type="checkbox" id="nav-toggle" class="nav-toggle">
    <label for="nav-toggle" class="nav-toggle-label">
      <span></span>
    </label>
    <div class="navbar-menu">
      <a href="#about" class="navbar-item">หลักการและเหตุผล</a>
      <a href="#tracks" class="navbar-item">หัวข้อการประชุม</a>
      <a href="#guidelines" class="navbar-item">การส่งบทความ</a>
      <a href="#fees" class="navbar-item">ค่าธรรมเนียมและการตีพิมพ์</a>
      <a href="#timeline" class="navbar-item">กำหนดการ</a>
      <a href="<?= base_url('auth/login'); ?>" class="btn btn-primary btn-sm">📥 ระบบส่งบทความ (Submission)</a>
    </div>
  </nav>

  <!-- Main Grid Container -->
  <main class="container">

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
            การประชุมวิชาการระดับชาติ ประจำปี พ.ศ. 2569 ณ มหาวิทยาลัยราชภัฏพิบูลสงคราม ร่วมขับเคลื่อนการวิจัยคณิตศาสตร์และคณิตศาสตรศึกษาสู่ระดับสากล
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
                <span style="position: absolute; left: 0; color: var(--psru-gold-dark); font-weight: bold;">1.</span>
                บทความที่ส่งต้องเป็นผลงานวิจัยใหม่ที่ไม่เคยตีพิมพ์หรือเผยแพร่ที่ใดมาก่อน
              </li>
              <li style="position: relative; padding-left: 1.5rem;">
                <span style="position: absolute; left: 0; color: var(--psru-gold-dark); font-weight: bold;">2.</span>
                รองรับบทความวิจัยฉบับภาษาไทยและภาษาอังกฤษ
              </li>
              <li style="position: relative; padding-left: 1.5rem;">
                <span style="position: absolute; left: 0; color: var(--psru-gold-dark); font-weight: bold;">3.</span>
                ความยาวของบทความวิจัยต้องอยู่ระหว่าง 6 - 8 หน้า ตามเทมเพลตที่กำหนด
              </li>
              <li style="position: relative; padding-left: 1.5rem;">
                <span style="position: absolute; left: 0; color: var(--psru-gold-dark); font-weight: bold;">4.</span>
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
                <th style="font-weight: 700; text-align: center; color: var(--psru-gold-dark);">ลงทะเบียนล่วงหน้า (Early Bird)</th>
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
              <?= ($activeConf && $activeConf['accept_submissions']) ? '<span class="badge badge-success">กำลังเปิดรับผลงานใหม่</span>' : '<span class="badge badge-danger">ปิดรับผลงานชั่วคราว</span>' ?>
            </p>
          </div>
          
          <div class="timeline-item active">
            <div class="timeline-dot"></div>
            <div class="timeline-date">เฟสที่ 2: การประเมินผลโดยผู้ทรงคุณวุฒิ</div>
            <strong style="display: block; margin-bottom: 0.5rem; font-size: 1.05rem;">Peer Review Phase</strong>
            <p class="text-muted" style="font-size: 0.85rem; line-height: 1.5;">
              <?= ($activeConf && $activeConf['accept_evaluations']) ? '<span class="badge badge-success">อยู่ระหว่างดำเนินการประเมิน</span>' : '<span class="badge badge-pending">รอเข้าสู่ช่วงการประเมิน</span>' ?>
            </p>
          </div>
          
          <div class="timeline-item">
            <div class="timeline-dot"></div>
            <div class="timeline-date">เฟสที่ 3: วันจัดงานประชุมวิชาการและการนำเสนอผลงาน</div>
            <strong style="display: block; margin-bottom: 0.5rem; font-size: 1.05rem;">Conference & Proceedings</strong>
            <p class="text-muted" style="font-size: 0.85rem; line-height: 1.5;">
              <?= ($activeConf && $activeConf['accept_grading']) ? '<span class="badge badge-success">กำลังบันทึกผลการนำเสนอ</span>' : '<span class="badge badge-info">วันจัดงานประชุมวิชาการระดับชาติ</span>' ?>
            </p>
          </div>
        </div>
      </div>
    </section>

  </main>

  <!-- Contact & Footer Section -->
  <footer id="contact">
    <div style="max-width: 800px; margin: 0 auto;">
      <p style="color: var(--psru-green); font-weight: 700; font-size: 1.15rem; margin-bottom: 0.75rem;">สาขาวิชาคณิตศาสตร์ คณะวิทยาศาสตร์และเทคโนโลยี มหาวิทยาลัยราชภัฏพิบูลสงคราม</p>
      <p class="text-muted" style="font-size: 0.85rem; line-height: 1.6; margin-bottom: 1.5rem;">
        เลขที่ 156 หมู่ 5 ถนนเลี่ยงเมืองพิษณุโลก (ส่วนทะเลแก้ว) ตำบลพลายชุมพล อำเภอเมือง จังหวัดพิษณุโลก 65000<br>
        โทรศัพท์: 0-5526-7000 ต่อ คณะวิทยาศาสตร์และเทคโนโลยี | อีเมล: science@psru.ac.th
      </p>
      <div style="border-top: 1px dashed var(--card-border); padding-top: 1.5rem; font-size: 0.8rem; color: var(--text-secondary);">
        &copy; 2026 CSRM System. All Rights Reserved. Pibulsongkram Rajabhat University.
      </div>
    </div>
  </footer>

</body>
</html>
