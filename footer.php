<!-- FOOTER ZONE -->
  <footer class="mt-16 border-t-4 border-brand bg-ink text-blush">
    <div class="mx-auto grid max-w-7xl grid-cols-1 gap-10 px-4 py-12 md:grid-cols-3">
      <div>
        <p class="font-display text-2xl font-bold uppercase tracking-tight text-paper">
          Jurnalis <span class="text-brand">Nuansa</span>
        </p>
        <p class="mt-2 text-[11px] font-semibold uppercase tracking-[0.14em] text-blush/80">
          The Power of Writing, Satu Tulisan Sejuta Perubahan
        </p>
        <p class="mt-4 text-sm leading-relaxed text-blush/80">
          Lembaga Pers Mahasiswa independen yang berkomitmen menyajikan informasi akurat, tajam, dan berimbang di lingkungan universitas.
        </p>
      </div>

      <div>
        <h5 class="font-display text-lg font-bold text-paper">Redaksi &amp; Kontak</h5>
        <ul class="mt-4 space-y-2 text-sm text-blush/80">
          <li>Gedung Student Center Lt. 2</li>
          <li>
            <a href="mailto:redaksi@jurnalisnuansa.test" class="transition hover:text-paper hover:underline">
              redaksi@jurnalisnuansa.test
            </a>
          </li>
          <li>
            <a href="https://instagram.com/jurnalisnuansa" class="transition hover:text-paper hover:underline" target="_blank" rel="noopener">
              Instagram: @jurnalisnuansa
            </a>
          </li>
        </ul>
      </div>

      <div>
        <h5 class="font-display text-lg font-bold text-paper">Kontribusi</h5>
        <p class="mt-4 text-sm text-blush/80">Ingin karya tulisan, opini, atau liputan Anda diterbitkan?</p>
        <a href="kirim-tulisan.php"
           class="mt-5 inline-flex items-center justify-center rounded-full bg-brand px-5 py-2.5 text-sm font-bold text-paper transition hover:bg-paper hover:text-ink">
          Kirim Naskah Artikel
        </a>
      </div>
    </div>

    <div class="border-t border-blush/20">
      <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-2 px-4 py-6 text-xs text-blush/70 sm:flex-row">
        <p>&copy; <?= e(date('Y')) ?> Redaksi Jurnalis Nuansa. Hak Cipta Dilindungi.</p>
        <a href="index.php" class="transition hover:text-paper">Kembali ke atas &uarr;</a>
      </div>
    </div>
  </footer>

  <script src="assets/js/main.js"></script>
</body>
</html>
