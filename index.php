<?php
require 'koneksi.php';

// 1. Ambil 1 Artikel Utama (Featured)
$sql_featured = "SELECT articles.*, categories.name AS cat_name, categories.color_class 
                 FROM articles 
                 JOIN categories ON articles.category_id = categories.id 
                 WHERE is_featured = TRUE AND articles.status = 'terbit' 
                 ORDER BY published_at DESC LIMIT 1";
$result_featured = $conn->query($sql_featured);
$featured = $result_featured->fetch_assoc();

// 2. Ambil 3 Artikel Sekunder (Hero Section Kanan)
$sql_secondary = "SELECT articles.*, categories.name AS cat_name, categories.color_class 
                  FROM articles 
                  JOIN categories ON articles.category_id = categories.id 
                  WHERE is_featured = FALSE AND articles.status = 'terbit' 
                  ORDER BY published_at DESC LIMIT 3";
$result_secondary = $conn->query($sql_secondary);
$secondary = $result_secondary->fetch_all(MYSQLI_ASSOC);

// 3. Ambil Artikel untuk "Berita Terkini", kecuali yang sudah tampil di hero
$hero_ids = [];
if ($featured) {
    $hero_ids[] = (int) $featured['id'];
}
foreach ($secondary as $row) {
    $hero_ids[] = (int) $row['id'];
}

$sql_latest = "SELECT articles.*, categories.name AS cat_name, categories.color_class 
               FROM articles 
               JOIN categories ON articles.category_id = categories.id 
               WHERE articles.status = 'terbit'";

if (count($hero_ids) > 0) {
    $placeholders = implode(', ', array_fill(0, count($hero_ids), '?'));
    $sql_latest .= " AND articles.id NOT IN ($placeholders)";
}

$sql_latest .= " ORDER BY published_at DESC LIMIT 5";

$stmt_latest = $conn->prepare($sql_latest);
if (count($hero_ids) > 0) {
    $tipe = str_repeat('i', count($hero_ids));
    $stmt_latest->bind_param($tipe, ...$hero_ids);
}
$stmt_latest->execute();
$result_latest = $stmt_latest->get_result();

// 4. Widget Terpopuler: 3 artikel dengan views terbanyak
$sql_populer = "SELECT id, title 
                FROM articles 
                WHERE status = 'terbit' 
                ORDER BY views DESC, published_at DESC 
                LIMIT 3";
$result_populer = $conn->query($sql_populer);

// 5. Kategori untuk sidebar
$kategori_sidebar = [];
$result_kategori = $conn->query("SELECT id, name FROM categories ORDER BY name ASC");
if ($result_kategori) {
    while ($kategori = $result_kategori->fetch_assoc()) {
        $kategori_sidebar[] = $kategori;
    }
}

// Bagi berita terkini: 2 teratas jadi kartu besar, sisanya daftar ringkas.
$latest_rows  = $result_latest->fetch_all(MYSQLI_ASSOC);
$latest_utama = array_slice($latest_rows, 0, 2);
$latest_sisa  = array_slice($latest_rows, 2);

// Panggil Header
include 'header.php';
?>

<div class="min-h-screen bg-white">
  <main class="mx-auto max-w-7xl px-4 py-10 sm:py-14">

    <!-- HERO -->
    <section class="grid gap-6 lg:grid-cols-3 lg:gap-8">

      <!-- Berita Utama -->
      <?php if ($featured): ?>
      <article class="group relative overflow-hidden rounded-2xl bg-ink lg:col-span-2">
        <div class="relative aspect-[4/3] bg-blush sm:aspect-video">
          <img src="<?= e($featured['image_url']) ?>" alt="<?= e($featured['title']) ?>" loading="lazy"
               onerror="this.style.display='none'"
               class="absolute inset-0 h-full w-full object-cover transition duration-700 group-hover:scale-[1.03]">
          <div class="absolute inset-0 bg-brand/10 mix-blend-multiply" aria-hidden="true"></div>
          <div class="absolute inset-x-0 bottom-0 h-2/5 bg-gradient-to-t from-ink via-ink/70 to-transparent" aria-hidden="true"></div>
          <div class="absolute inset-x-0 bottom-0 z-10 p-5 sm:p-8">
            <span class="text-[13px] font-semibold uppercase tracking-[0.16em] text-blush">
              <?= e($featured['cat_name']) ?>
            </span>
            <h1 class="mt-2 font-display text-2xl font-bold leading-tight text-white sm:text-4xl lg:text-5xl">
              <a href="artikel.php?id=<?= e($featured['id']) ?>" class="after:absolute after:inset-0">
                <?= e($featured['title']) ?>
              </a>
            </h1>
            <p class="mt-3 hidden max-w-2xl text-sm leading-relaxed text-white/80 sm:line-clamp-2">
              <?= e($featured['excerpt']) ?>
            </p>
            <p class="mt-3 text-[13px] font-medium text-white/70">
              <?= e(date('d M Y', strtotime($featured['published_at']))) ?>
            </p>
          </div>
        </div>
      </article>
      <?php endif; ?>

      <!-- Berita Sekunder -->
      <div class="flex flex-col gap-6">
        <?php foreach ($secondary as $row): ?>
        <a href="artikel.php?id=<?= e($row['id']) ?>" class="group grid grid-cols-[7rem_1fr] items-center gap-4 sm:grid-cols-[9rem_1fr] sm:gap-5">
          <div class="relative aspect-[4/3] overflow-hidden rounded-xl bg-blush">
            <img src="<?= e($row['image_url']) ?>" alt="<?= e($row['title']) ?>" loading="lazy"
                 onerror="this.style.display='none'"
                 class="absolute inset-0 h-full w-full object-cover transition duration-500 group-hover:scale-[1.03]">
            <div class="absolute inset-0 bg-brand/10 mix-blend-multiply" aria-hidden="true"></div>
          </div>
          <div class="min-w-0">
            <span class="text-[13px] font-semibold uppercase tracking-[0.12em] text-brand">
              <?= e($row['cat_name']) ?>
            </span>
            <h2 class="mt-1 font-display text-lg font-bold leading-snug text-ink transition group-hover:text-brand sm:text-xl">
              <?= e($row['title']) ?>
            </h2>
            <p class="mt-2 text-[13px] font-medium text-ink/60">
              <?= e(date('d M Y', strtotime($row['published_at']))) ?>
            </p>
          </div>
        </a>
        <?php endforeach; ?>
      </div>
    </section>

    <!-- KONTEN UTAMA & SIDEBAR -->
    <div class="mt-14 grid gap-12 lg:grid-cols-3 lg:gap-10">

      <!-- Berita Terkini -->
      <section class="lg:col-span-2">
        <div class="flex items-end justify-between gap-4 border-b border-black/10 pb-3">
          <h2 class="font-display text-2xl font-bold text-ink sm:text-3xl">Berita Terkini</h2>
          <a href="berita.php" class="text-[13px] font-bold uppercase tracking-[0.12em] text-brand hover:underline">
            Lihat Semua &rarr;
          </a>
        </div>

        <!-- Dua kartu besar -->
        <?php if ($latest_utama): ?>
        <div class="mt-8 grid gap-8 sm:grid-cols-2">
          <?php foreach ($latest_utama as $latest): ?>
          <article class="group relative">
            <div class="relative aspect-video overflow-hidden rounded-xl bg-blush">
              <img src="<?= e($latest['image_url']) ?>" alt="<?= e($latest['title']) ?>" loading="lazy"
                   onerror="this.style.display='none'"
                   class="absolute inset-0 h-full w-full object-cover transition duration-500 group-hover:scale-[1.03]">
              <div class="absolute inset-0 bg-brand/10 mix-blend-multiply" aria-hidden="true"></div>
            </div>
            <div class="mt-4 flex flex-wrap items-center gap-x-2 gap-y-1 text-[13px]">
              <span class="font-semibold uppercase tracking-[0.12em] text-brand"><?= e($latest['cat_name']) ?></span>
              <span class="text-ink/30" aria-hidden="true">&bull;</span>
              <span class="font-medium text-ink/60"><?= e(date('d M Y', strtotime($latest['published_at']))) ?></span>
            </div>
            <h3 class="mt-2 font-display text-xl font-bold leading-snug text-ink transition group-hover:text-brand sm:text-2xl">
              <a href="artikel.php?id=<?= e($latest['id']) ?>" class="after:absolute after:inset-0">
                <?= e($latest['title']) ?>
              </a>
            </h3>
            <p class="mt-2 text-sm leading-relaxed text-ink/70 line-clamp-2">
              <?= e($latest['excerpt']) ?>
            </p>
          </article>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Daftar ringkas -->
        <?php if ($latest_sisa): ?>
        <div class="mt-10 divide-y divide-black/10 border-t border-black/10">
          <?php foreach ($latest_sisa as $latest): ?>
          <article class="group relative flex items-start gap-4 py-5 sm:gap-6">
            <div class="relative aspect-[4/3] w-24 shrink-0 overflow-hidden rounded-lg bg-blush sm:w-32">
              <img src="<?= e($latest['image_url']) ?>" alt="<?= e($latest['title']) ?>" loading="lazy"
                   onerror="this.style.display='none'"
                   class="absolute inset-0 h-full w-full object-cover transition duration-500 group-hover:scale-[1.03]">
              <div class="absolute inset-0 bg-brand/10 mix-blend-multiply" aria-hidden="true"></div>
            </div>
            <div class="min-w-0">
              <div class="flex flex-wrap items-center gap-x-2 gap-y-1 text-[13px]">
                <span class="font-semibold uppercase tracking-[0.12em] text-brand"><?= e($latest['cat_name']) ?></span>
                <span class="text-ink/30" aria-hidden="true">&bull;</span>
                <span class="font-medium text-ink/60"><?= e(date('d M Y', strtotime($latest['published_at']))) ?></span>
              </div>
              <h3 class="mt-1 font-display text-lg font-bold leading-snug text-ink transition group-hover:text-brand">
                <a href="artikel.php?id=<?= e($latest['id']) ?>" class="after:absolute after:inset-0">
                  <?= e($latest['title']) ?>
                </a>
              </h3>
            </div>
          </article>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </section>

      <!-- Sidebar -->
      <aside class="space-y-6 lg:sticky lg:top-24 lg:self-start">

        <!-- Terpopuler -->
        <section class="rounded-2xl bg-paper p-6">
          <h2 class="font-display text-lg font-bold uppercase tracking-wide text-ink">Terpopuler</h2>
          <ol class="mt-4 divide-y divide-brand/10">
            <?php $nomor = 0; while ($populer = $result_populer->fetch_assoc()): $nomor++; ?>
            <li class="flex items-start gap-4 py-3 first:pt-0 last:pb-0">
              <span class="font-display text-2xl font-bold leading-none text-brand"><?= e(sprintf('%02d', $nomor)) ?></span>
              <a href="artikel.php?id=<?= e($populer['id']) ?>" class="text-sm font-semibold leading-snug text-ink transition hover:text-brand">
                <?= e($populer['title']) ?>
              </a>
            </li>
            <?php endwhile; ?>
          </ol>
        </section>

        <!-- Kategori -->
        <?php if ($kategori_sidebar): ?>
        <section class="rounded-2xl bg-paper p-6">
          <h2 class="font-display text-lg font-bold uppercase tracking-wide text-ink">Kategori</h2>
          <ul class="mt-4 flex flex-wrap gap-2">
            <?php foreach ($kategori_sidebar as $kategori): ?>
            <li>
              <a href="berita.php?kategori=<?= e($kategori['id']) ?>"
                 class="inline-flex rounded-full bg-white/80 px-3 py-1.5 text-[13px] font-semibold text-ink/80 transition hover:bg-brand hover:text-paper">
                <?= e($kategori['name']) ?>
              </a>
            </li>
            <?php endforeach; ?>
          </ul>
        </section>
        <?php endif; ?>

        <!-- CTA -->
        <section class="rounded-2xl bg-brand p-6 text-paper">
          <h2 class="font-display text-xl font-bold leading-snug">Punya tulisan?</h2>
          <p class="mt-2 text-sm leading-relaxed text-blush">
            Kirim karya opini, esai, atau liputanmu ke redaksi Jurnalis Nuansa.
          </p>
          <a href="kirim-tulisan.php"
             class="mt-4 inline-flex items-center justify-center rounded-full bg-paper px-5 py-2.5 text-sm font-bold text-brand transition hover:bg-white">
            Kirim ke Redaksi
          </a>
        </section>
      </aside>

    </div>
  </main>
</div>

<?php
// Panggil Footer
include 'footer.php';
?>
