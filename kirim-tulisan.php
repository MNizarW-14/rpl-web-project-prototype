<?php
require 'koneksi.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$page_title = 'Kirim Tulisan';

// Batas panjang mengikuti kolom pada tabel submissions.
const BATAS_NAMA     = 100;
const BATAS_PRODI    = 100;
const BATAS_NIM      = 30;
const BATAS_JUDUL    = 200;
const BATAS_PENGANTAR = 2000;
const BATAS_UKURAN_BERKAS = 5 * 1024 * 1024; // 5 MB

$errors = [];
$old    = [
    'nama'        => '',
    'prodi'       => '',
    'nim'         => '',
    'category_id' => '',
    'judul'       => '',
    'pengantar'   => '',
];

$flash = $_SESSION['flash_sukses'] ?? '';
unset($_SESSION['flash_sukses']);

// Token CSRF disimpan di session dan dipakai sekali per pengiriman.
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

// Ekstensi yang diterima beserta MIME yang sah untuk masing-masing.
$mime_diizinkan = [
    'doc'  => ['application/msword', 'application/x-ole-storage'],
    'docx' => [
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/zip',
    ],
    'pdf'  => ['application/pdf'],
];

function ambil_teks(string $key): string
{
    return isset($_POST[$key]) && is_string($_POST[$key]) ? trim($_POST[$key]) : '';
}

function siapkan_folder_uploads(string $folder): void
{
    if (!is_dir($folder) && !mkdir($folder, 0755, true) && !is_dir($folder)) {
        throw new RuntimeException('Folder uploads tidak dapat dibuat.');
    }

    $htaccess = $folder . '/.htaccess';
    if (!is_file($htaccess)) {
        $aturan = "# Nonaktifkan eksekusi skrip di folder upload\n"
            . "<FilesMatch \"\\.(php|php[0-9]|phtml|phar|cgi|pl|py|sh|asp|aspx|jsp)$\">\n"
            . "  <IfModule mod_authz_core.c>\n"
            . "    Require all denied\n"
            . "  </IfModule>\n"
            . "  <IfModule !mod_authz_core.c>\n"
            . "    Order allow,deny\n"
            . "    Deny from all\n"
            . "  </IfModule>\n"
            . "</FilesMatch>\n"
            . "<IfModule mod_mime.c>\n"
            . "  RemoveHandler .php .phtml .phar\n"
            . "</IfModule>\n";

        if (file_put_contents($htaccess, $aturan) === false) {
            throw new RuntimeException('.htaccess pada folder uploads tidak dapat dibuat.');
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old['nama']        = ambil_teks('nama');
    $old['prodi']       = ambil_teks('prodi');
    $old['nim']         = ambil_teks('nim');
    $old['category_id'] = ambil_teks('category_id');
    $old['judul']       = ambil_teks('judul');
    $old['pengantar']   = ambil_teks('pengantar');
    $token              = ambil_teks('csrf_token');

    // 1. CSRF
    if ($token === '' || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        $errors[] = 'Sesi formulir tidak valid. Muat ulang halaman lalu coba lagi.';
    }

    // 2. Data pengirim
    if ($old['nama'] === '') {
        $errors[] = 'Nama lengkap atau nama pena wajib diisi.';
    } elseif (mb_strlen($old['nama']) < 3 || mb_strlen($old['nama']) > BATAS_NAMA) {
        $errors[] = 'Nama harus terdiri dari 3–' . BATAS_NAMA . ' karakter.';
    }

    if ($old['prodi'] === '') {
        $errors[] = 'Program studi wajib diisi.';
    } elseif (mb_strlen($old['prodi']) < 2 || mb_strlen($old['prodi']) > BATAS_PRODI) {
        $errors[] = 'Program studi harus terdiri dari 2–' . BATAS_PRODI . ' karakter.';
    }

    if ($old['nim'] === '') {
        $errors[] = 'Nomor Induk Mahasiswa wajib diisi.';
    } elseif (!ctype_digit($old['nim'])) {
        $errors[] = 'NIM hanya boleh berisi angka.';
    } elseif (strlen($old['nim']) > BATAS_NIM) {
        $errors[] = 'NIM maksimal ' . BATAS_NIM . ' angka.';
    }

    // 3. Kategori harus benar-benar ada di tabel categories.
    $kategori_id = null;
    if ($old['category_id'] === '' || !ctype_digit($old['category_id'])) {
        $errors[] = 'Kategori tulisan wajib dipilih.';
    } else {
        $kategori_id = (int) $old['category_id'];
        $cek_kategori = $conn->prepare("SELECT id FROM categories WHERE id = ?");
        $cek_kategori->bind_param("i", $kategori_id);
        $cek_kategori->execute();

        if ($cek_kategori->get_result()->num_rows === 0) {
            $errors[] = 'Kategori tulisan tidak valid.';
            $kategori_id = null;
        }
        $cek_kategori->close();
    }

    // 4. Judul & pengantar
    if ($old['judul'] === '') {
        $errors[] = 'Judul tulisan wajib diisi.';
    } elseif (mb_strlen($old['judul']) < 5 || mb_strlen($old['judul']) > BATAS_JUDUL) {
        $errors[] = 'Judul harus terdiri dari 5–' . BATAS_JUDUL . ' karakter.';
    }

    if (mb_strlen($old['pengantar']) > BATAS_PENGANTAR) {
        $errors[] = 'Teks pengantar maksimal ' . BATAS_PENGANTAR . ' karakter.';
    }

    // 5. Berkas: ekstensi + ukuran + MIME (finfo)
    $berkas   = $_FILES['berkas'] ?? null;
    $ekstensi = '';

    if (!is_array($berkas) || !isset($berkas['error']) || $berkas['error'] === UPLOAD_ERR_NO_FILE) {
        $errors[] = 'Dokumen naskah wajib diunggah.';
    } elseif ($berkas['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'Berkas gagal diunggah (kode ' . (int) $berkas['error'] . '). Coba lagi.';
    } else {
        $ekstensi = strtolower(pathinfo((string) $berkas['name'], PATHINFO_EXTENSION));
        $ukuran   = (int) $berkas['size'];

        if (!isset($mime_diizinkan[$ekstensi])) {
            $errors[] = 'Format berkas harus .doc, .docx, atau .pdf.';
        } elseif ($ukuran <= 0 || $ukuran > BATAS_UKURAN_BERKAS) {
            $errors[] = 'Ukuran berkas maksimal 5 MB.';
        } elseif (!is_uploaded_file($berkas['tmp_name'])) {
            $errors[] = 'Berkas tidak valid. Silakan pilih ulang.';
        } else {
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime  = $finfo->file($berkas['tmp_name']);

            if (!in_array($mime, $mime_diizinkan[$ekstensi], true)) {
                $errors[] = 'Tipe berkas tidak sesuai dengan ekstensinya.';
            }
        }
    }

    // 6. Simpan berkas + data bila tidak ada kesalahan.
    if ($errors === []) {
        $folder_uploads = __DIR__ . '/uploads';
        $tujuan         = '';

        try {
            siapkan_folder_uploads($folder_uploads);

            $nama_berkas = bin2hex(random_bytes(16)) . '.' . $ekstensi;
            $tujuan      = $folder_uploads . '/' . $nama_berkas;

            if (!move_uploaded_file($berkas['tmp_name'], $tujuan)) {
                $errors[] = 'Berkas gagal disimpan di server. Coba lagi.';
            } else {
                $file_path = 'uploads/' . $nama_berkas;
                $pengantar = $old['pengantar'] === '' ? null : $old['pengantar'];

                $stmt = $conn->prepare(
                    "INSERT INTO submissions (category_id, nama, prodi, nim, judul, pengantar, file_path)
                     VALUES (?, ?, ?, ?, ?, ?, ?)"
                );
                $stmt->bind_param(
                    "issssss",
                    $kategori_id,
                    $old['nama'],
                    $old['prodi'],
                    $old['nim'],
                    $old['judul'],
                    $pengantar,
                    $file_path
                );

                if ($stmt->execute()) {
                    $stmt->close();
                    $_SESSION['flash_sukses'] = 'Naskah Anda berhasil dikirim. Redaksi akan meninjau dalam beberapa hari.';
                    unset($_SESSION['csrf_token']);

                    header('Location: kirim-tulisan.php');
                    exit;
                }

                $stmt->close();
                @unlink($tujuan);
                $errors[] = 'Naskah gagal disimpan. Silakan coba lagi.';
            }
        } catch (RuntimeException $exception) {
            if ($tujuan !== '' && is_file($tujuan)) {
                @unlink($tujuan);
            }
            $errors[] = 'Penyimpanan berkas bermasalah: ' . $exception->getMessage();
        }
    }
}

// Daftar kategori untuk dropdown.
$kategori_list = [];
$result_kategori = $conn->query("SELECT id, name FROM categories ORDER BY name ASC");
if ($result_kategori) {
    while ($baris = $result_kategori->fetch_assoc()) {
        $kategori_list[] = $baris;
    }
}

include 'header.php';
?>

<main class="mx-auto max-w-6xl px-4 py-10 sm:py-14">

  <!-- Pengantar halaman -->
  <div class="mx-auto max-w-2xl text-center">
    <span class="inline-block rounded-full bg-blush/60 px-4 py-1 text-[11px] font-bold uppercase tracking-[0.18em] text-brand">
      Kontribusi
    </span>
    <h1 class="mt-4 text-3xl font-bold leading-tight text-ink sm:text-4xl">Mari Bersuara!</h1>
    <p class="mt-3 text-sm leading-relaxed text-ink/70 sm:text-base">
      Kami menerima kontribusi berupa opini, esai, puisi, cerpen, maupun pers rilis kegiatan organisasi mahasiswa.
    </p>
  </div>

  <!-- Pesan sukses (PRG) -->
  <?php if ($flash !== ''): ?>
    <div role="status" class="mx-auto mt-8 max-w-3xl rounded-xl border border-brand/30 bg-blush/50 px-5 py-4 text-sm font-semibold text-ink">
      <?= e($flash) ?>
    </div>
  <?php endif; ?>

  <!-- Pesan kesalahan -->
  <?php if ($errors !== []): ?>
    <div role="alert" class="mx-auto mt-8 max-w-3xl rounded-xl border border-brand/40 bg-white px-5 py-4 shadow-sm">
      <p class="font-bold text-brand">Periksa kembali isian Anda:</p>
      <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-ink/80">
        <?php foreach ($errors as $pesan): ?>
          <li><?= e($pesan) ?></li>
        <?php endforeach; ?>
      </ul>
      <?php if (!empty($_FILES['berkas'])): ?>
        <p class="mt-2 text-xs text-ink/60">Berkas perlu dipilih ulang setelah terjadi kesalahan.</p>
      <?php endif; ?>
    </div>
  <?php endif; ?>

  <div class="mt-8 grid overflow-hidden rounded-2xl border border-blush bg-white shadow-sm md:grid-cols-5">

    <!-- Info kiri -->
    <aside class="bg-brand p-6 text-paper sm:p-8 md:col-span-2">
      <h2 class="text-xl font-bold">Ketentuan Naskah</h2>
      <ul class="mt-5 space-y-3 text-sm leading-relaxed text-blush">
        <li class="flex gap-2"><span aria-hidden="true">&#10003;</span><span>Panjang tulisan opini idealnya 500–800 kata.</span></li>
        <li class="flex gap-2"><span aria-hidden="true">&#10003;</span><span>Tulisan adalah karya orisinal dan tidak mengandung unsur SARA.</span></li>
        <li class="flex gap-2"><span aria-hidden="true">&#10003;</span><span>Redaksi berhak menyunting judul dan isi tanpa mengubah makna.</span></li>
        <li class="flex gap-2"><span aria-hidden="true">&#10003;</span><span>Dokumen naskah berformat .doc, .docx, atau .pdf.</span></li>
      </ul>
      <p class="mt-6 border-t border-paper/20 pt-4 text-xs leading-relaxed text-blush/80">
        Setiap naskah yang masuk berstatus &ldquo;baru&rdquo; dan akan ditinjau oleh redaksi.
      </p>
    </aside>

    <!-- Form kanan -->
    <div class="p-6 sm:p-8 md:col-span-3">
      <h2 class="text-xl font-bold text-ink">Formulir Pengiriman Naskah</h2>

      <form action="kirim-tulisan.php" method="POST" enctype="multipart/form-data" class="mt-6 space-y-5">
        <input type="hidden" name="csrf_token" value="<?= e($csrf_token) ?>">

        <div>
          <label for="nama" class="block text-sm font-bold text-ink">Nama Lengkap / Nama Pena <span class="text-brand">*</span></label>
          <input type="text" id="nama" name="nama" value="<?= e($old['nama']) ?>"
                 maxlength="<?= BATAS_NAMA ?>" autocomplete="name" required
                 placeholder="Masukkan nama Anda"
                 class="mt-1.5 w-full rounded-lg border border-blush bg-paper px-4 py-2.5 text-sm text-ink placeholder:text-ink/40 focus:border-brand focus:ring-2 focus:ring-brand/30">
        </div>

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
          <div>
            <label for="prodi" class="block text-sm font-bold text-ink">Program Studi <span class="text-brand">*</span></label>
            <input type="text" id="prodi" name="prodi" value="<?= e($old['prodi']) ?>"
                   maxlength="<?= BATAS_PRODI ?>" autocomplete="organization" required
                   placeholder="Cth: Teknik Informatika"
                   class="mt-1.5 w-full rounded-lg border border-blush bg-paper px-4 py-2.5 text-sm text-ink placeholder:text-ink/40 focus:border-brand focus:ring-2 focus:ring-brand/30">
          </div>
          <div>
            <label for="nim" class="block text-sm font-bold text-ink">Nomor Induk Mahasiswa <span class="text-brand">*</span></label>
            <input type="text" id="nim" name="nim" value="<?= e($old['nim']) ?>"
                   maxlength="<?= BATAS_NIM ?>" inputmode="numeric" pattern="[0-9]+" required
                   aria-describedby="nim-hint" placeholder="Cth: 202310001"
                   class="mt-1.5 w-full rounded-lg border border-blush bg-paper px-4 py-2.5 text-sm text-ink placeholder:text-ink/40 focus:border-brand focus:ring-2 focus:ring-brand/30">
            <p id="nim-hint" class="mt-1 text-xs text-ink/50">Hanya angka, maksimal <?= BATAS_NIM ?> digit.</p>
          </div>
        </div>

        <div>
          <label for="category_id" class="block text-sm font-bold text-ink">Kategori Tulisan <span class="text-brand">*</span></label>
          <select id="category_id" name="category_id" required
                  class="mt-1.5 w-full rounded-lg border border-blush bg-paper px-4 py-2.5 text-sm text-ink focus:border-brand focus:ring-2 focus:ring-brand/30">
            <option value="" disabled <?= $old['category_id'] === '' ? 'selected' : '' ?>>— Pilih kategori —</option>
            <?php foreach ($kategori_list as $kategori): ?>
              <option value="<?= e($kategori['id']) ?>" <?= (string) $old['category_id'] === (string) $kategori['id'] ? 'selected' : '' ?>>
                <?= e($kategori['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div>
          <label for="judul" class="block text-sm font-bold text-ink">Judul Tulisan <span class="text-brand">*</span></label>
          <input type="text" id="judul" name="judul" value="<?= e($old['judul']) ?>"
                 maxlength="<?= BATAS_JUDUL ?>" required
                 placeholder="Ketik judul di sini"
                 class="mt-1.5 w-full rounded-lg border border-blush bg-paper px-4 py-2.5 text-sm text-ink placeholder:text-ink/40 focus:border-brand focus:ring-2 focus:ring-brand/30">
        </div>

        <div>
          <label for="pengantar" class="block text-sm font-bold text-ink">Teks Singkat / Pengantar <span class="font-normal text-ink/50">(opsional)</span></label>
          <textarea id="pengantar" name="pengantar" rows="3" maxlength="<?= BATAS_PENGANTAR ?>"
                    aria-describedby="pengantar-hint"
                    placeholder="Berikan pengantar singkat untuk redaksi..."
                    class="mt-1.5 w-full rounded-lg border border-blush bg-paper px-4 py-2.5 text-sm text-ink placeholder:text-ink/40 focus:border-brand focus:ring-2 focus:ring-brand/30"><?= e($old['pengantar']) ?></textarea>
          <p id="pengantar-hint" class="mt-1 text-xs text-ink/50">Maksimal <?= BATAS_PENGANTAR ?> karakter.</p>
        </div>

        <div>
          <label for="berkas" class="block text-sm font-bold text-ink">
            Unggah Dokumen Naskah (.doc, .docx, .pdf, maks 5 MB) <span class="text-brand">*</span>
          </label>
          <input type="file" id="berkas" name="berkas" accept=".doc,.docx,.pdf" required
                 aria-describedby="berkas-hint"
                 class="mt-1.5 block w-full cursor-pointer rounded-lg border border-blush bg-paper text-sm text-ink/70 file:mr-4 file:border-0 file:bg-blush file:px-4 file:py-2.5 file:text-sm file:font-bold file:text-brand hover:file:bg-brand hover:file:text-paper">
          <p id="berkas-hint" class="mt-1 text-xs text-ink/50">Format .doc, .docx, atau .pdf. Ukuran maksimal 5 MB.</p>
        </div>

        <button type="submit"
                class="inline-flex w-full items-center justify-center rounded-full bg-brand px-6 py-3 text-base font-bold text-paper shadow-sm transition hover:bg-ink">
          Kirim Naskah Sekarang
        </button>

        <p class="text-center text-xs leading-relaxed text-ink/50">
          Dengan mengirim naskah, Anda menyetujui ketentuan redaksi Jurnalis Nuansa.
        </p>
      </form>
    </div>
  </div>
</main>

<?php
include 'footer.php';
?>
