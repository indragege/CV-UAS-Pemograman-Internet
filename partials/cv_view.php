<?php
/** @var array $cv */
/** @var bool $isDefault */
$viewer = current_user();
$success = flash('success');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Curriculum Vitae <?= e($cv['name']) ?>">
    <title>CV <?= e($cv['name']) ?></title>
    <link rel="stylesheet" href="<?= e(app_url('styles.css')) ?>">
</head>
<body>
    <nav class="site-nav no-print">
        <a class="brand" href="<?= e(app_url()) ?>"><span>CV</span> Multi User</a>
        <div class="nav-links">
            <?php if ($isDefault): ?><span class="default-badge">CV Default</span><?php endif; ?>
            <a href="<?= e(app_url()) ?>">Beranda</a>
            <?php if ($viewer && $viewer['role'] === 'user'): ?>
                <a href="<?= e(app_url('dashboard.php')) ?>">Dashboard</a>
            <?php elseif ($viewer && $viewer['role'] === 'admin'): ?>
                <a href="<?= e(app_url('Admin/')) ?>">Admin</a>
            <?php else: ?>
                <a href="<?= e(app_url('login.php')) ?>">Login</a>
            <?php endif; ?>
            <button class="nav-print" type="button" onclick="window.print()">Cetak</button>
        </div>
    </nav>

    <?php if ($success): ?><div class="toast no-print"><?= e($success) ?></div><?php endif; ?>

    <div class="cv-wrapper">
        <section class="top-section">
            <div class="photo-panel">
                <img src="<?= e(app_url($cv['photo_path'])) ?>" alt="Foto profil <?= e($cv['name']) ?>" class="profile-img">
                <h2><?= e($cv['name']) ?></h2>
                <p><?= e($cv['title']) ?></p>
            </div>
            <div class="intro-panel">
                <span class="label"><?= e($cv['headline']) ?></span>
                <h1><?= e($cv['name']) ?></h1>
                <h3><?= e($cv['title']) ?></h3>
                <p><?= nl2br(e($cv['summary'])) ?></p>
            </div>
        </section>

        <section class="contact-strip">
            <div class="contact-box"><span class="contact-icon">NIM</span><div><small>Nomor Induk</small><p><?= e($cv['nim']) ?></p></div></div>
            <div class="contact-box"><span class="contact-icon">@</span><div><small>Email</small><p><?= e($cv['email']) ?></p></div></div>
            <div class="contact-box"><span class="contact-icon">TEL</span><div><small>Telepon</small><p><?= e($cv['phone']) ?></p></div></div>
            <div class="contact-box"><span class="contact-icon">GIT</span><div><small>GitHub</small><p><?= e($cv['github']) ?></p></div></div>
        </section>

        <main class="content">
            <div class="grid-main">
                <div>
                    <section class="section">
                        <div class="section-title"><span class="title-icon">01</span><h3>Profil Singkat</h3></div>
                        <div class="summary-card"><p><?= nl2br(e($cv['summary'])) ?></p></div>
                    </section>

                    <section class="section">
                        <div class="section-title"><span class="title-icon">02</span><h3>Pendidikan</h3></div>
                        <?php foreach ($cv['education'] as $education): ?>
                            <article class="card-box">
                                <h4><?= e($education['title'] ?? '') ?></h4>
                                <span class="meta"><?= e($education['meta'] ?? '') ?></span>
                                <p><?= e($education['description'] ?? '') ?></p>
                            </article>
                        <?php endforeach; ?>
                    </section>

                    <?php if (!empty($cv['experience'])): ?>
                    <section class="section">
                        <div class="section-title"><span class="title-icon">03</span><h3>Pengalaman</h3></div>
                        <?php foreach ($cv['experience'] as $experience): ?>
                            <article class="card-box">
                                <h4><?= e($experience['title'] ?? '') ?></h4>
                                <span class="meta"><?= e($experience['meta'] ?? '') ?></span>
                                <p><?= e($experience['description'] ?? '') ?></p>
                            </article>
                        <?php endforeach; ?>
                    </section>
                    <?php endif; ?>

                    <section class="section">
                        <div class="section-title"><span class="title-icon">04</span><h3>Portofolio</h3></div>
                        <div class="project-card">
                            <div class="project-icon">&lt;/&gt;</div>
                            <div><h4><?= e($cv['portfolio_title']) ?></h4><p><?= nl2br(e($cv['portfolio_description'])) ?></p></div>
                        </div>
                    </section>
                </div>

                <aside>
                    <div class="side-card">
                        <h4>Data Diri</h4>
                        <div class="data-list">
                            <div><strong>Nama</strong><span><?= e($cv['name']) ?></span></div>
                            <div><strong>Program Studi</strong><span><?= e($cv['study_program']) ?></span></div>
                            <div><strong>Angkatan</strong><span><?= e($cv['cohort']) ?></span></div>
                            <div><strong>Lokasi</strong><span><?= e($cv['location']) ?></span></div>
                            <div><strong>URL Publik</strong><span>/<?= e($cv['username']) ?></span></div>
                        </div>
                    </div>

                    <div class="side-card">
                        <h4>Keahlian</h4>
                        <div class="skill-list">
                            <?php foreach ($cv['skills'] as $index => $skill): ?>
                                <span class="skill-pill <?= $index < 2 ? 'primary' : '' ?>"><?= e($skill) ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="side-card">
                        <h4>Kemampuan Teknis</h4>
                        <?php foreach ($cv['technical'] as $technical): ?>
                            <div class="progress-item">
                                <div class="progress-label"><span><?= e($technical['name'] ?? '') ?></span><strong><?= e($technical['percentage'] ?? 0) ?>%</strong></div>
                                <div class="progress"><div class="progress-bar" style="width: <?= max(0, min(100, (int) ($technical['percentage'] ?? 0))) ?>%"></div></div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="side-card">
                        <h4>Bahasa</h4>
                        <ul class="language-list">
                            <?php foreach ($cv['languages'] as $language): ?><li><?= e($language) ?></li><?php endforeach; ?>
                        </ul>
                    </div>

                    <div class="side-card database-card no-print">
                        <h4>Status Aplikasi</h4>
                        <p><?= e(storage_label()) ?></p>
                        <small>Terakhir diperbarui: <?= e($cv['updated_at']) ?></small>
                    </div>
                </aside>
            </div>
        </main>

        <footer class="footer"><?= e($cv['footer_text']) ?></footer>
    </div>
</body>
</html>
