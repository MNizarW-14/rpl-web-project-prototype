<?php
// Judul halaman: halaman pemanggil boleh mengisi $page_title.
if (!isset($page_title)) {
    $page_title = isset($article['title'])
        ? $article['title'] . ' - Jurnalis Nuansa'
        : 'Jurnalis Nuansa - Media Kampus';
}

// Tanggal hari ini dalam bahasa Indonesia (tanpa mengubah timezone global).
$hari_indonesia  = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
$bulan_indonesia = [
    1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
];
$sekarang          = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
$tanggal_indonesia = $hari_indonesia[(int) $sekarang->format('w')] . ', '
    . $sekarang->format('j') . ' '
    . $bulan_indonesia[(int) $sekarang->format('n')] . ' '
    . $sekarang->format('Y');

// Halaman & kategori aktif untuk penanda menu otomatis.
$halaman_aktif  = basename($_SERVER['SCRIPT_NAME'] ?? 'index.php');
$kategori_aktif = isset($_GET['kategori']) && is_string($_GET['kategori']) ? $_GET['kategori'] : '';

if ($kategori_aktif === '' && isset($article['cat_name'])) {
    $peta_kategori = [
        'Laporan Utama' => 'liputan-khusus',
        'Kampus'        => 'berita-kampus',
        'Opini'         => 'opini-esai',
        'Seni Budaya'   => 'seni-budaya',
    ];
    $kategori_aktif = $peta_kategori[$article['cat_name']] ?? '';
}

$menu_utama = [
    ['label' => 'Beranda',        'href' => 'index.php',                        'kategori' => ''],
    ['label' => 'Berita Kampus',  'href' => 'index.php?kategori=berita-kampus',  'kategori' => 'berita-kampus'],
    ['label' => 'Opini & Esai',   'href' => 'index.php?kategori=opini-esai',     'kategori' => 'opini-esai'],
    ['label' => 'Liputan Khusus', 'href' => 'index.php?kategori=liputan-khusus', 'kategori' => 'liputan-khusus'],
    ['label' => 'Seni & Budaya',  'href' => 'index.php?kategori=seni-budaya',    'kategori' => 'seni-budaya'],
];

function menu_aktif(array $item, string $halaman, string $kategori): bool
{
    if ($item['kategori'] === '') {
        return $halaman === 'index.php' && $kategori === '';
    }

    return $kategori === $item['kategori'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($page_title) ?></title>
  <meta name="description" content="Jurnalis Nuansa - media pers mahasiswa: berita kampus, opini, liputan khusus, dan seni budaya.">

  <!-- Tailwind CDN + tema didefinisikan sekali di sini -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            brand: '#B01050',
            blush: '#FDD4E6',
            paper: '#FFF0F5',
            ink: '#111111'
          },
          fontFamily: {
            sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
            display: ['"Playfair Display"', 'Georgia', 'serif']
          }
        }
      }
    };
  </script>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Playfair+Display:wght@600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-paper font-sans text-ink antialiased">

  <!-- HEADER ZONE -->
  <header class="sticky top-0 z-50 border-b border-blush bg-paper/90 backdrop-blur-md">
    <!-- Top Bar -->
    <div class="bg-ink text-paper/90">
      <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-1 px-4 py-2 text-[11px] sm:flex-row sm:text-xs">
        <span class="tracking-wide">
          <?= e($tanggal_indonesia) ?> <span class="text-blush/60">|</span> Edisi Digital Pers Kampus
        </span>
        <div class="flex items-center gap-4 font-semibold">
          <a href="#" class="transition hover:text-blush">Tentang Redaksi</a>
        </div>
      </div>
    </div>

    <!-- Branding, Navigasi & CTA -->
    <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3 sm:py-4">
      <a href="index.php" class="group flex min-w-0 flex-col">
        <span class="font-display text-2xl font-bold uppercase leading-none tracking-tight text-ink sm:text-3xl">
          Jurnalis <span class="text-brand">Nuansa</span>
        </span>
        <span class="mt-1.5 truncate text-[10px] font-semibold uppercase tracking-[0.14em] text-brand sm:text-[11px]">
          The Power of Writing, Satu Tulisan Sejuta Perubahan
        </span>
      </a>

      <!-- Navigasi desktop -->
      <nav class="hidden items-center gap-6 lg:flex" aria-label="Navigasi utama">
        <?php foreach ($menu_utama as $item): ?>
          <?php $aktif = menu_aktif($item, $halaman_aktif, $kategori_aktif); ?>
          <a href="<?= e($item['href']) ?>"
             class="border-b-2 pb-1 text-sm font-semibold transition <?= $aktif ? 'border-brand text-brand' : 'border-transparent text-ink/70 hover:border-blush hover:text-brand' ?>"
             <?= $aktif ? 'aria-current="page"' : '' ?>>
            <?= e($item['label']) ?>
          </a>
        <?php endforeach; ?>
      </nav>

      <div class="flex flex-shrink-0 items-center gap-2">
        <a href="kirim-tulisan.php"
           class="hidden items-center justify-center rounded-full bg-brand px-5 py-2.5 text-sm font-bold text-paper shadow-sm transition hover:bg-ink sm:inline-flex">
          Kirim Tulisan
        </a>

        <!-- Tombol hamburger (mobile) -->
        <button id="menu-toggle" type="button"
                class="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-blush text-ink transition hover:border-brand hover:text-brand lg:hidden"
                aria-controls="mobile-menu" aria-expanded="false">
          <span class="sr-only">Buka menu</span>
          <svg data-icon="open" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
          </svg>
          <svg data-icon="close" class="hidden h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6L6 18"/>
          </svg>
        </button>
      </div>
    </div>

    <!-- Menu mobile -->
    <div id="mobile-menu" class="hidden border-t border-blush bg-paper lg:hidden">
      <nav class="mx-auto flex max-w-7xl flex-col gap-1 px-4 py-4" aria-label="Navigasi mobile">
        <?php foreach ($menu_utama as $item): ?>
          <?php $aktif = menu_aktif($item, $halaman_aktif, $kategori_aktif); ?>
          <a href="<?= e($item['href']) ?>"
             class="rounded-lg px-3 py-2.5 text-sm font-semibold transition <?= $aktif ? 'bg-blush/40 text-brand' : 'text-ink/80 hover:bg-blush/20 hover:text-brand' ?>"
             <?= $aktif ? 'aria-current="page"' : '' ?>>
            <?= e($item['label']) ?>
          </a>
        <?php endforeach; ?>
        <a href="kirim-tulisan.php"
           class="mt-3 inline-flex items-center justify-center rounded-full bg-brand px-5 py-2.5 text-sm font-bold text-paper transition hover:bg-ink">
          Kirim Tulisan
        </a>
      </nav>
    </div>
  </header>
