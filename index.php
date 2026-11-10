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

// Panggil Header
include 'header.php';
?>

  <main class="max-w-7xl mx-auto px-4 py-8">

    <!-- HERO ZONE -->
    <section class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-12">
      
      <!-- Berita Utama -->
      <?php if($featured): ?>
      <div class="lg:col-span-2 group cursor-pointer relative bg-[#000000] rounded-xl overflow-hidden shadow-lg min-h-[400px] flex items-end">
        <img src="<?= e($featured['image_url']) ?>" alt="<?= e($featured['title']) ?>" class="absolute inset-0 w-full h-full object-cover opacity-60 group-hover:scale-105 transition duration-500">
        <div class="relative z-10 p-6 w-full bg-gradient-to-t from-[#000000] via-[#000000]/80 to-transparent">
          <span class="bg-[#B01050] text-[#FFF0F5] text-xs font-bold uppercase px-3 py-1 rounded"><?= e($featured['cat_name']) ?></span>
          <h1 class="text-2xl md:text-3xl font-black text-[#FFF0F5] mt-3 leading-snug group-hover:text-[#FDD4E6] transition">
            <!-- Link ke artikel.php -->
            <a href="artikel.php?id=<?= e($featured['id']) ?>"><?= e($featured['title']) ?></a>
          </h1>
          <p class="text-[#FDD4E6] text-sm mt-2 line-clamp-2">
            <?= e($featured['excerpt']) ?>
          </p>
          <div class="text-xs text-[#FFF0F5]/70 mt-3 font-semibold">Dipublikasikan pada <?= e(date('d M Y', strtotime($featured['published_at']))) ?></div>
        </div>
      </div>
      <?php endif; ?>

      <!-- Berita Sekunder -->
      <div class="flex flex-col space-y-4 justify-between">
        <?php foreach($secondary as $row): ?>
        <div class="flex space-x-4 items-center bg-white p-3 rounded-lg border-2 border-[#FDD4E6] hover:bg-[#FDD4E6]/30 transition cursor-pointer group">
          <img src="<?= e($row['image_url']) ?>" alt="Thumb" class="w-24 h-24 object-cover rounded-md flex-shrink-0">
          <div>
            <span class="text-xs font-black <?= e($row['color_class']) ?> uppercase tracking-wider"><?= e($row['cat_name']) ?></span>
            <h2 class="text-sm font-bold text-[#000000] mt-1 leading-snug group-hover:text-[#B01050]">
              <!-- Link ke artikel.php -->
              <a href="artikel.php?id=<?= e($row['id']) ?>"><?= e($row['title']) ?></a>
            </h2>
            <span class="text-xs text-[#000000]/50 mt-2 block font-semibold"><?= e(date('d M Y', strtotime($row['published_at']))) ?></span>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </section>

    <!-- KONTEN UTAMA & SIDEBAR -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
      
      <!-- Kolom Berita Terbaru (Sekarang Dinamis!) -->
      <section class="lg:col-span-2 space-y-6">
        <div class="border-b-4 border-[#B01050] pb-2 flex justify-between items-center">
          <h3 class="text-2xl font-black uppercase tracking-tight text-[#000000]">Berita Terkini</h3>
          <a href="#" class="text-sm font-bold text-[#B01050] hover:underline">Lihat Semua →</a>
        </div>

        <!-- Looping Berita Terkini -->
        <?php while($latest = $result_latest->fetch_assoc()): ?>
        <article class="flex flex-col sm:flex-row gap-5 bg-white p-4 rounded-xl border border-[#FDD4E6] shadow-sm hover:shadow-md hover:border-[#B01050] transition group cursor-pointer">
          <img src="<?= e($latest['image_url']) ?>" alt="Artikel" class="w-full sm:w-48 h-32 object-cover rounded-lg">
          <div class="flex flex-col justify-between w-full">
            <div>
              <span class="text-xs font-bold <?= e($latest['color_class']) ?> uppercase"><?= e($latest['cat_name']) ?></span>
              <h4 class="text-lg font-bold text-[#000000] mt-1 group-hover:text-[#B01050]">
                <!-- Link ke artikel.php -->
                <a href="artikel.php?id=<?= e($latest['id']) ?>"><?= e($latest['title']) ?></a>
              </h4>
              <p class="text-[#000000]/70 text-sm mt-1 line-clamp-2">
                <?= e($latest['excerpt']) ?>
              </p>
            </div>
            <div class="text-xs text-[#000000]/50 mt-2 font-semibold">Dipublikasikan pada <?= e(date('d M Y', strtotime($latest['published_at']))) ?></div>
          </div>
        </article>
        <?php endwhile; ?>

      </section>

      <!-- Sidebar -->
      <aside class="space-y-8">
        <!-- Widget Terpopuler -->
        <div class="bg-white p-6 rounded-xl border-2 border-[#FDD4E6] shadow-sm">
          <h3 class="text-lg font-black uppercase border-b-2 border-[#000000] pb-2 mb-5 text-[#000000]">
            Terpopuler
          </h3>
          <ol class="space-y-5">
            <?php $nomor = 0; while($populer = $result_populer->fetch_assoc()): $nomor++; ?>
            <li class="flex space-x-3 items-start">
              <span class="text-3xl font-black text-[#FDD4E6] leading-none"><?= e(sprintf('%02d', $nomor)) ?></span>
              <a href="artikel.php?id=<?= e($populer['id']) ?>" class="text-sm font-bold text-[#000000] hover:text-[#B01050] leading-snug">
                <?= e($populer['title']) ?>
              </a>
            </li>
            <?php endwhile; ?>
          </ol>
        </div>
      </aside>

    </div>
  </main>

<?php 
// Panggil Footer
include 'footer.php'; 
?>  