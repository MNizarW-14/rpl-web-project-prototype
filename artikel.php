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
<main class="max-w-3xl mx-auto px-4 py-24 text-center">
  <h2 class="text-3xl font-black text-[#000000]">Artikel tidak ditemukan!</h2>
  <p class="text-sm text-[#000000]/60 mt-3 font-semibold">Artikel yang Anda cari tidak tersedia atau belum diterbitkan.</p>
  <a href="index.php" class="inline-block mt-8 bg-[#B01050] hover:bg-[#8f0d41] text-[#FFF0F5] font-bold px-6 py-3 rounded-lg transition">
    ← Kembali ke Beranda
  </a>
</main>
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

// Memanggil Bagian Header
include 'header.php';
?>

<!-- KONTEN BACA ARTIKEL -->
<main class="max-w-3xl mx-auto px-4 py-12">
  
  <!-- Header Artikel -->
  <div class="mb-8 text-center">
    <span class="bg-[#B01050] text-[#FFF0F5] text-xs font-bold uppercase px-3 py-1 rounded tracking-wider">
      <?= e($article['cat_name']) ?>
    </span>
    <h1 class="text-3xl md:text-4xl font-black mt-5 leading-snug text-[#000000]">
      <?= e($article['title']) ?>
    </h1>
    <div class="flex items-center justify-center space-x-2 text-sm text-[#000000]/60 mt-4 font-semibold">
      <span>Oleh <strong><?= e($article['author_name']) ?></strong></span>
      <span>•</span>
      <span><?= e(date('d M Y', strtotime($article['published_at']))) ?></span>
    </div>
  </div>

  <!-- Featured Image -->
  <div class="mb-10">
    <img src="<?= e($article['image_url']) ?>" alt="<?= e($article['title']) ?>" class="w-full h-auto max-h-[450px] rounded-xl border-4 border-[#FDD4E6] shadow-md object-cover">
    <?php if (!empty($article['image_caption'])): ?>
    <p class="text-xs text-center text-[#000000]/50 mt-2"><?= e($article['image_caption']) ?></p>
    <?php endif; ?>
  </div>

  <!-- Isi Berita (Body Content) -->
  <article class="text-gray-800 text-lg leading-relaxed space-y-5">
    <?= $article['content'] ?>
  </article>

  <!-- Author Box -->
  <div class="mt-16 bg-white p-6 rounded-xl border-2 border-[#FDD4E6] flex items-center space-x-5 shadow-sm">
    <div class="w-16 h-16 rounded-full border-2 border-[#B01050] flex items-center justify-center bg-[#FDD4E6] text-[#B01050] font-black text-xl flex-shrink-0">
      JN
    </div>
    <div>
      <h4 class="font-black text-lg text-[#000000]">Redaksi Jurnalis Nuansa</h4>
      <p class="text-sm text-[#000000]/70 mt-1">Lembaga pers mahasiswa yang fokus pada isu-isu kampus, transparansi akademik, dan aspirasi mahasiswa.</p>
    </div>
  </div>

  <!-- Tombol Kembali -->
  <div class="mt-8 text-center border-t border-[#FDD4E6] pt-8">
    <a href="index.php" class="inline-block bg-[#000000] hover:bg-[#B01050] text-[#FFF0F5] font-bold px-6 py-3 rounded-lg transition">
      ← Kembali ke Beranda
    </a>
  </div>

</main>

<?php 
// Memanggil Bagian Footer
include 'footer.php'; 
?>
