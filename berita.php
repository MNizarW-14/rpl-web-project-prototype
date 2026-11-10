<?php
require 'koneksi.php';

$page_title  = 'Semua Berita';
$per_halaman = 9;

// Filter kategori opsional: berita.php?kategori=ID
$kategori_id          = 0;
$kategori_filter_nama = '';

if (isset($_GET['kategori']) && is_string($_GET['kategori']) && ctype_digit($_GET['kategori'])) {
    $kategori_id = (int) $_GET['kategori'];

    $stmt_kategori = $conn->prepare("SELECT name FROM categories WHERE id = ?");
    $stmt_kategori->bind_param("i", $kategori_id);
    $stmt_kategori->execute();
    $hasil_kategori = $stmt_kategori->get_result()->fetch_assoc();
    $stmt_kategori->close();

    if ($hasil_kategori) {
        $kategori_filter_nama = $hasil_kategori['name'];
        $page_title = 'Berita ' . $kategori_filter_nama;
    } else {
        $kategori_id = 0;
    }
}

// Hitung total untuk pagination
if ($kategori_id > 0) {
    $stmt_hitung = $conn->prepare("SELECT COUNT(*) AS total FROM articles WHERE status = 'terbit' AND category_id = ?");
    $stmt_hitung->bind_param("i", $kategori_id);
    $stmt_hitung->execute();
    $total = (int) $stmt_hitung->get_result()->fetch_assoc()['total'];
    $stmt_hitung->close();
} else {
    $total = (int) $conn->query("SELECT COUNT(*) AS total FROM articles WHERE status = 'terbit'")->fetch_assoc()['total'];
}

$total_halaman = max(1, (int) ceil($total / $per_halaman));

$halaman = 1;
if (isset($_GET['halaman']) && is_string($_GET['halaman']) && ctype_digit($_GET['halaman'])) {
    $halaman = max(1, (int) $_GET['halaman']);
}
$halaman = min($halaman, $total_halaman);
$offset  = ($halaman - 1) * $per_halaman;

// Ambil 9 artikel untuk halaman ini
$sql = "SELECT articles.*, categories.name AS cat_name
        FROM articles
        JOIN categories ON articles.category_id = categories.id
        WHERE articles.status = 'terbit'";

if ($kategori_id > 0) {
    $sql .= " AND articles.category_id = ?";
}

$sql .= " ORDER BY articles.published_at DESC LIMIT ? OFFSET ?";

$stmt = $conn->prepare($sql);
if ($kategori_id > 0) {
    $stmt->bind_param("iii", $kategori_id, $per_halaman, $offset);
} else {
    $stmt->bind_param("ii", $per_halaman, $offset);
}
$stmt->execute();
$daftar = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

function tautan_berita(int $nomor, int $kategori_id): string
{
    $params = ['halaman' => $nomor];
    if ($kategori_id > 0) {
        $params['kategori'] = $kategori_id;
    }

    return 'berita.php?' . http_build_query($params);
}

include 'header.php';
?>

<div class="min-h-screen bg-white">
  <main class="mx-auto max-w-7xl px-4 py-10 sm:py-14">

    <!-- Judul halaman -->
    <div class="flex flex-col gap-3 border-b border-black/10 pb-6 sm:flex-row sm:items-end sm:justify-between">
      <div>
        <p class="text-[13px] font-bold uppercase tracking-[0.2em] text-brand">Arsip Berita</p>
        <h1 class="mt-2 font-display text-3xl font-bold leading-tight text-ink sm:text-4xl">
          <?= e($kategori_filter_nama !== '' ? $kategori_filter_nama : 'Semua Berita') ?>
        </h1>
        <p class="mt-2 text-[13px] font-medium text-ink/60"><?= e($total) ?> artikel terbit</p>
      </div>

      <?php if ($kategori_filter_nama !== ''): ?>
        <a href="berita.php" class="text-[13px] font-bold uppercase tracking-[0.12em] text-brand hover:underline">
          &larr; Semua Berita
        </a>
      <?php endif; ?>
    </div>

    <!-- Daftar artikel -->
    <?php if ($daftar): ?>
      <div class="mt-8 grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
        <?php foreach ($daftar as $item): ?>
        <article class="group relative">
          <div class="relative aspect-[4/3] overflow-hidden rounded-xl bg-blush">
            <img src="<?= e($item['image_url']) ?>" alt="<?= e($item['title']) ?>" loading="lazy"
                 onerror="this.style.display='none'"
                 class="absolute inset-0 h-full w-full object-cover transition duration-500 group-hover:scale-[1.03]">
            <div class="absolute inset-0 bg-brand/10 mix-blend-multiply" aria-hidden="true"></div>
          </div>
          <div class="mt-4 flex flex-wrap items-center gap-x-2 gap-y-1 text-[13px]">
            <span class="font-semibold uppercase tracking-[0.12em] text-brand"><?= e($item['cat_name']) ?></span>
            <span class="text-ink/30" aria-hidden="true">&bull;</span>
            <span class="font-medium text-ink/60"><?= e(date('d M Y', strtotime($item['published_at']))) ?></span>
          </div>
          <h2 class="mt-2 font-display text-xl font-bold leading-snug text-ink transition group-hover:text-brand">
            <a href="artikel.php?id=<?= e($item['id']) ?>" class="after:absolute after:inset-0">
              <?= e($item['title']) ?>
            </a>
          </h2>
          <p class="mt-2 text-sm leading-relaxed text-ink/70 line-clamp-2">
            <?= e($item['excerpt']) ?>
          </p>
        </article>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="mt-16 text-center">
        <p class="font-display text-2xl font-bold text-ink">Belum ada artikel</p>
        <p class="mt-2 text-sm text-ink/60">Silakan kembali ke beranda untuk melihat berita lainnya.</p>
        <a href="index.php" class="mt-6 inline-flex rounded-full bg-brand px-6 py-3 text-sm font-bold text-paper transition hover:bg-ink">
          &larr; Ke Beranda
        </a>
      </div>
    <?php endif; ?>

    <!-- Pagination -->
    <?php if ($total_halaman > 1): ?>
      <nav class="mt-14 flex flex-wrap items-center justify-center gap-2" aria-label="Navigasi halaman">
        <?php if ($halaman > 1): ?>
          <a href="<?= e(tautan_berita($halaman - 1, $kategori_id)) ?>"
             class="rounded-full border border-black/10 px-4 py-2 text-[13px] font-semibold text-ink transition hover:border-brand hover:text-brand">
            &larr; Sebelumnya
          </a>
        <?php endif; ?>

        <?php for ($nomor = 1; $nomor <= $total_halaman; $nomor++): ?>
          <a href="<?= e(tautan_berita($nomor, $kategori_id)) ?>"
             class="inline-flex h-9 min-w-9 items-center justify-center rounded-full px-3 text-[13px] font-bold transition <?= $nomor === $halaman ? 'bg-brand text-paper' : 'border border-black/10 text-ink hover:border-brand hover:text-brand' ?>"
             <?= $nomor === $halaman ? 'aria-current="page"' : '' ?>>
            <?= e($nomor) ?>
          </a>
        <?php endfor; ?>

        <?php if ($halaman < $total_halaman): ?>
          <a href="<?= e(tautan_berita($halaman + 1, $kategori_id)) ?>"
             class="rounded-full border border-black/10 px-4 py-2 text-[13px] font-semibold text-ink transition hover:border-brand hover:text-brand">
            Berikutnya &rarr;
          </a>
        <?php endif; ?>
      </nav>
    <?php endif; ?>

  </main>
</div>

<?php
include 'footer.php';
?>
