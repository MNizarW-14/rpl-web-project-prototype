<?php
require 'koneksi.php';

// Menangkap ID dari URL (contoh: artikel.php?id=1)
// intval() digunakan untuk keamanan mencegah SQL Injection dasar
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Jika tidak ada ID di URL, kembalikan ke beranda
if ($id === 0) {
    header("Location: index.php");
    exit;
}

// Mengambil data artikel spesifik beserta kategorinya dan nama penulisnya
$sql = "SELECT articles.*, categories.name AS cat_name, categories.color_class, users.name AS author_name 
        FROM articles 
        JOIN categories ON articles.category_id = categories.id 
        LEFT JOIN users ON articles.author_id = users.id 
        WHERE articles.id = ? AND articles.status = 'terbit'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

// Jika ID artikel tidak ditemukan di database
if ($result->num_rows === 0) {
    http_response_code(404);
    include 'header.php';
?>
<div class="bg-white">
  <main class="mx-auto max-w-xl px-4 py-24 text-center">
    <p class="text-[13px] font-bold uppercase tracking-[0.2em] text-brand">Error 404</p>
    <h1 class="mt-3 font-display text-3xl font-bold text-ink sm:text-4xl">Artikel tidak ditemukan</h1>
    <p class="mt-3 text-base leading-relaxed text-ink/60">Artikel yang Anda cari tidak tersedia atau belum diterbitkan.</p>
    <a href="index.php" class="mt-8 inline-flex items-center justify-center rounded-full bg-brand px-6 py-3 text-sm font-bold text-paper transition hover:bg-ink">
      &larr; Kembali ke Beranda
    </a>
  </main>
</div>
<?php
    include 'footer.php';
    exit;
}

$article = $result->fetch_assoc();

// Naikkan jumlah views setiap artikel dibuka
$stmt_views = $conn->prepare("UPDATE articles SET views = views + 1 WHERE id = ?");
$stmt_views->bind_param("i", $id);
$stmt_views->execute();
$stmt_views->close();

// Berita terkait: 3 artikel lain dari kategori yang sama
$kategori_artikel = (int) $article['category_id'];
$id_artikel       = (int) $article['id'];

$stmt_terkait = $conn->prepare(
    "SELECT articles.id, articles.title, articles.image_url, articles.published_at, categories.name AS cat_name
     FROM articles
     JOIN categories ON articles.category_id = categories.id
     WHERE articles.category_id = ? AND articles.id <> ? AND articles.status = 'terbit'
     ORDER BY articles.published_at DESC
     LIMIT 3"
);
$stmt_terkait->bind_param("ii", $kategori_artikel, $id_artikel);
$stmt_terkait->execute();
$terkait = $stmt_terkait->get_result()->fetch_all(MYSQLI_ASSOC);

// URL artikel & tautan berbagi
$skema  = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
$path   = strtok($_SERVER['REQUEST_URI'] ?? '/artikel.php', '?');
$tautan_artikel = $skema . '://' . $host . $path . '?id=' . $id;

$bagikan_wa = 'https://wa.me/?text=' . rawurlencode($article['title'] . ' ' . $tautan_artikel);
$bagikan_x  = 'https://twitter.com/intent/tweet?text=' . rawurlencode($article['title']) . '&url=' . rawurlencode($tautan_artikel);
$bagikan_fb = 'https://www.facebook.com/sharer/sharer.php?u=' . rawurlencode($tautan_artikel);

// Memanggil Bagian Header
include 'header.php';
?>

<div class="min-h-screen bg-white">
  <main class="mx-auto max-w-7xl px-4 py-10 sm:py-14">

    <!-- Header Artikel -->
    <header class="mx-auto max-w-4xl text-center">
      <a href="berita.php?kategori=<?= e($article['category_id']) ?>"
         class="text-[13px] font-bold uppercase tracking-[0.2em] text-brand hover:underline">
        <?= e($article['cat_name']) ?>
      </a>
      <h1 class="mt-4 font-display text-3xl font-bold leading-tight text-ink sm:text-4xl lg:text-5xl">
        <?= e($article['title']) ?>
      </h1>
      <div class="mt-5 flex flex-wrap items-center justify-center gap-x-3 gap-y-1 text-[13px] font-medium text-ink/60">
        <span>Oleh <strong class="font-semibold text-ink"><?= e($article['author_name']) ?></strong></span>
        <span class="text-ink/30" aria-hidden="true">&bull;</span>
        <span><?= e(date('d M Y', strtotime($article['published_at']))) ?></span>
      </div>
    </header>

    <!-- Gambar utama -->
    <figure class="mx-auto mt-10 max-w-4xl">
      <div class="relative aspect-video overflow-hidden rounded-2xl bg-blush">
        <img src="<?= e($article['image_url']) ?>" alt="<?= e($article['title']) ?>" loading="lazy"
             onerror="this.style.display='none'"
             class="absolute inset-0 h-full w-full object-cover">
        <div class="absolute inset-0 bg-brand/10 mix-blend-multiply" aria-hidden="true"></div>
      </div>
      <?php if (!empty($article['image_caption'])): ?>
        <figcaption class="mt-3 text-center text-[13px] text-ink/60"><?= e($article['image_caption']) ?></figcaption>
      <?php endif; ?>
    </figure>

    <!-- Isi Berita -->
    <div class="mx-auto mt-10 max-w-[68ch]">
      <article class="text-[18px] leading-[1.8] text-ink/85 sm:text-[19px] [&_p]:mb-6 [&_a]:text-brand [&_a]:underline [&_blockquote]:my-8 [&_blockquote]:border-l-4 [&_blockquote]:border-brand [&_blockquote]:pl-5 [&_blockquote]:font-display [&_blockquote]:text-xl [&_blockquote]:italic [&_img]:my-8 [&_img]:w-full [&_img]:rounded-xl">
        <?= $article['content'] ?>
      </article>

      <!-- Bagikan -->
      <div class="mt-10 flex flex-wrap items-center gap-3 border-y border-black/10 py-4">
        <span class="text-[13px] font-bold uppercase tracking-[0.14em] text-ink/60">Bagikan</span>
        <a href="<?= e($bagikan_wa) ?>" target="_blank" rel="noopener"
           class="rounded-full border border-black/10 px-4 py-1.5 text-[13px] font-semibold text-ink transition hover:border-brand hover:text-brand">
          WhatsApp
        </a>
        <a href="<?= e($bagikan_x) ?>" target="_blank" rel="noopener"
           class="rounded-full border border-black/10 px-4 py-1.5 text-[13px] font-semibold text-ink transition hover:border-brand hover:text-brand">
          X
        </a>
        <a href="<?= e($bagikan_fb) ?>" target="_blank" rel="noopener"
           class="rounded-full border border-black/10 px-4 py-1.5 text-[13px] font-semibold text-ink transition hover:border-brand hover:text-brand">
          Facebook
        </a>
      </div>

      <!-- Author Box -->
      <div class="mt-10 flex items-center gap-5 rounded-2xl bg-paper p-6">
        <div class="flex h-14 w-14 flex-shrink-0 items-center justify-center rounded-full bg-brand font-display text-lg font-bold text-paper">
          JN
        </div>
        <div>
          <h4 class="font-display text-lg font-bold text-ink">Redaksi Jurnalis Nuansa</h4>
          <p class="mt-1 text-[13px] leading-relaxed text-ink/60">
            Lembaga pers mahasiswa yang fokus pada isu-isu kampus, transparansi akademik, dan aspirasi mahasiswa.
          </p>
        </div>
      </div>

      <div class="mt-10">
        <a href="index.php" class="inline-flex items-center justify-center rounded-full border border-ink/15 px-6 py-3 text-sm font-bold text-ink transition hover:border-brand hover:text-brand">
          &larr; Kembali ke Beranda
        </a>
      </div>
    </div>

    <!-- Berita Terkait -->
    <?php if ($terkait): ?>
    <section class="mx-auto mt-16 max-w-7xl border-t border-black/10 pt-10">
      <div class="flex items-end justify-between gap-4">
        <h2 class="font-display text-2xl font-bold text-ink sm:text-3xl">Berita Terkait</h2>
        <a href="berita.php?kategori=<?= e($article['category_id']) ?>" class="text-[13px] font-bold uppercase tracking-[0.12em] text-brand hover:underline">
          Lihat Semua &rarr;
        </a>
      </div>
      <div class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <?php foreach ($terkait as $item): ?>
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
          <h3 class="mt-2 font-display text-lg font-bold leading-snug text-ink transition group-hover:text-brand">
            <a href="artikel.php?id=<?= e($item['id']) ?>" class="after:absolute after:inset-0">
              <?= e($item['title']) ?>
            </a>
          </h3>
        </article>
        <?php endforeach; ?>
      </div>
    </section>
    <?php endif; ?>

  </main>
</div>

<?php
// Memanggil Bagian Footer
include 'footer.php';
?>
