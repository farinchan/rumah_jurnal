@extends('front.app')
@section('seo')
    <title>{{ $meta['description'] }}</title>
    <meta name="description" content="{{ $meta['description'] }}">
    <meta name="keywords" content="{{ $meta['keywords'] }}">
    <meta name="author" content="UIN Sjech M.Djamil Djambek Bukittinggi">

    <meta property="og:title" content="{{ $meta['title'] }}">
    <meta property="og:description" content="{{ $meta['description'] }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <link rel="canonical" href="{{ url()->current() }}">
    <meta property="og:image" content="{{ Storage::url($meta['favicon']) }}">
@endsection

@section('content')
    @include('front.partials.breadcrumb')

    <!-- TERMS OF SERVICE AREA START -->
    <div class="ltn__page-details-area ltn__service-details-area mb-120">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="ltn__page-details-inner ltn__service-details-inner">
                        <div class="ltn__blog-meta">
                            <ul>
                                <li class="ltn__blog-date">
                                    <i class="far fa-calendar-alt"></i>
                                    Terakhir diperbarui: {{ now()->format('d M Y') }}
                                </li>
                            </ul>
                        </div>

                        <h2>Syarat dan Ketentuan Layanan</h2>
                        <p>Selamat datang di {{ $setting_web->name }}. Syarat dan ketentuan ini mengatur penggunaan layanan jurnal online kami. Dengan menggunakan situs web ini, Anda menyetujui untuk mematuhi dan terikat oleh ketentuan berikut.</p>

                        <h3>1. Penerimaan Ketentuan</h3>
                        <p>Dengan mengakses dan menggunakan situs web ini, Anda menerima dan menyetujui untuk terikat dan mematuhi ketentuan dan kondisi penggunaan. Jika Anda tidak menyetujui syarat-syarat ini, harap jangan menggunakan situs web ini.</p>

                        <h3>2. Definisi</h3>
                        <ul>
                            <li><strong>"Kami"</strong> atau <strong>"Situs"</strong> mengacu pada {{ $setting_web->name }}</li>
                            <li><strong>"Pengguna"</strong> mengacu pada setiap individu yang mengakses atau menggunakan layanan kami</li>
                            <li><strong>"Konten"</strong> mengacu pada artikel, naskah, dan materi lain yang diunggah ke platform</li>
                            <li><strong>"Layanan"</strong> mengacu pada semua fitur dan fungsi yang tersedia di platform</li>
                        </ul>

                        <h3>3. Registrasi dan Akun Pengguna</h3>
                        <h4>3.1 Persyaratan Registrasi</h4>
                        <ul>
                            <li>Anda harus berusia minimal 18 tahun atau memiliki izin dari wali</li>
                            <li>Informasi yang Anda berikan harus akurat dan lengkap</li>
                            <li>Anda bertanggung jawab menjaga keamanan akun dan password Anda</li>
                            <li>Satu orang hanya diperbolehkan memiliki satu akun</li>
                        </ul>

                        <h4>3.2 Login dengan Google</h4>
                        <p>Ketika menggunakan fitur "Login dengan Google", Anda menyetujui bahwa:</p>
                        <ul>
                            <li>Kami dapat mengakses informasi dasar profil Google Anda</li>
                            <li>Anda bertanggung jawab atas keamanan akun Google Anda</li>
                            <li>Kami tidak bertanggung jawab atas masalah yang timbul dari layanan Google</li>
                        </ul>

                        <h3>4. Penggunaan Layanan</h3>
                        <h4>4.1 Penggunaan yang Diperbolehkan</h4>
                        <ul>
                            <li>Menggunakan platform untuk tujuan akademik yang sah</li>
                            <li>Mengunggah artikel dan naskah yang asli</li>
                            <li>Berpartisipasi dalam proses peer review</li>
                            <li>Mengakses dan mengunduh konten yang tersedia</li>
                        </ul>

                        <h4>4.2 Penggunaan yang Dilarang</h4>
                        <p>Anda dilarang untuk:</p>
                        <ul>
                            <li>Mengunggah konten yang melanggar hak cipta</li>
                            <li>Menyebarkan konten yang menyinggung, cabul, atau ilegal</li>
                            <li>Melakukan plagiarisme atau pelanggaran etika akademik</li>
                            <li>Menggunakan sistem untuk spam atau aktivitas komersial tidak sah</li>
                            <li>Mencoba mengakses akun pengguna lain</li>
                            <li>Mengganggu operasi normal platform</li>
                            <li>Menggunakan bot atau script otomatis tanpa izin</li>
                        </ul>

                        <h3>5. Konten dan Hak Kekayaan Intelektual</h3>
                        <h4>5.1 Konten Pengguna</h4>
                        <ul>
                            <li>Anda mempertahankan hak cipta atas konten yang Anda unggah</li>
                            <li>Anda memberikan lisensi kepada kami untuk mempublikasikan dan mendistribusikan konten Anda</li>
                            <li>Anda menjamin bahwa konten yang diunggah adalah karya asli Anda</li>
                            <li>Anda bertanggung jawab penuh atas konten yang Anda publikasikan</li>
                        </ul>

                        <h4>5.2 Konten Platform</h4>
                        <ul>
                            <li>Semua konten platform dilindungi hak cipta</li>
                            <li>Anda tidak diperbolehkan menggunakan konten platform tanpa izin</li>
                            <li>Logo, merek dagang, dan desain adalah milik kami</li>
                        </ul>

                        <h3>6. Proses Editorial dan Peer Review</h3>
                        <h4>6.1 Submission Artikel</h4>
                        <ul>
                            <li>Artikel yang disubmit harus mengikuti guidelines yang berlaku</li>
                            <li>Semua artikel akan melalui proses peer review</li>
                            <li>Keputusan editorial bersifat final</li>
                            <li>Kami berhak menolak artikel yang tidak sesuai standar</li>
                        </ul>

                        <h4>6.2 Proses Review</h4>
                        <ul>
                            <li>Reviewer harus menjaga kerahasiaan naskah</li>
                            <li>Review harus objektif dan konstruktif</li>
                            <li>Conflict of interest harus dilaporkan</li>
                        </ul>

                        <h3>7. Pembayaran dan Biaya</h3>
                        <h4>7.1 Biaya Publikasi</h4>
                        <ul>
                            <li>Biaya publikasi dikenakan sesuai dengan yang tercantum</li>
                            <li>Pembayaran harus dilakukan sebelum publikasi</li>
                            <li>Biaya yang sudah dibayar tidak dapat dikembalikan</li>
                            <li>Kami berhak mengubah struktur biaya dengan pemberitahuan</li>
                        </ul>

                        <h4>7.2 Metode Pembayaran</h4>
                        <ul>
                            <li>Pembayaran dapat dilakukan melalui metode yang tersedia</li>
                            <li>Bukti pembayaran harus disimpan sebagai dokumen</li>
                            <li>Konfirmasi pembayaran diperlukan untuk proses lebih lanjut</li>
                        </ul>

                        <h3>8. Privasi dan Perlindungan Data</h3>
                        <p>Penggunaan data pribadi Anda diatur dalam <a href="{{ route('privacy.policy') }}">Kebijakan Privasi</a> kami. Dengan menggunakan layanan ini, Anda menyetujui pengumpulan dan penggunaan informasi sesuai dengan kebijakan tersebut.</p>

                        <h3>9. Penangguhan dan Penghentian</h3>
                        <p>Kami berhak untuk:</p>
                        <ul>
                            <li>Menangguhkan atau menghentikan akun yang melanggar ketentuan</li>
                            <li>Menghapus konten yang tidak sesuai</li>
                            <li>Mengakhiri layanan dengan pemberitahuan yang wajar</li>
                            <li>Memblokir akses dari IP atau wilayah tertentu</li>
                        </ul>

                        <h3>10. Batasan Tanggung Jawab</h3>
                        <ul>
                            <li>Layanan disediakan "sebagaimana adanya" tanpa jaminan</li>
                            <li>Kami tidak bertanggung jawab atas kerugian tidak langsung</li>
                            <li>Tanggung jawab kami terbatas pada nilai layanan yang dibayar</li>
                            <li>Kami tidak menjamin ketersediaan layanan 100%</li>
                        </ul>

                        <h3>11. Ganti Rugi</h3>
                        <p>Anda setuju untuk mengganti rugi dan membebaskan kami dari setiap tuntutan, kerugian, atau biaya yang timbul dari:</p>
                        <ul>
                            <li>Pelanggaran Anda terhadap syarat dan ketentuan ini</li>
                            <li>Pelanggaran hak pihak ketiga oleh konten Anda</li>
                            <li>Penggunaan layanan yang tidak sah oleh Anda</li>
                        </ul>

                        <h3>12. Perubahan Ketentuan</h3>
                        <p>Kami berhak mengubah syarat dan ketentuan ini kapan saja. Perubahan akan berlaku setelah dipublikasikan di situs web. Penggunaan berkelanjutan setelah perubahan dianggap sebagai penerimaan terhadap ketentuan baru.</p>

                        <h3>13. Hukum yang Berlaku</h3>
                        <p>Syarat dan ketentuan ini diatur oleh hukum Republik Indonesia. Setiap sengketa akan diselesaikan melalui pengadilan yang berwenang di Indonesia.</p>

                        <h3>14. Pemisahan Klausul</h3>
                        <p>Jika bagian dari ketentuan ini dinyatakan tidak sah, bagian lainnya tetap berlaku dan dapat ditegakkan.</p>

                        <h3>15. Kontak</h3>
                        <p>Untuk pertanyaan mengenai Syarat dan Ketentuan ini, silakan hubungi kami:</p>
                        <div class="row mt-40">
                            <div class="col-md-6">
                                <div class="ltn__contact-info-item ltn__contact-info-item-3 ltn__contact-info-item-3-bg-white">
                                    <div class="ltn__contact-info-icon">
                                        <i class="flaticon-location"></i>
                                    </div>
                                    <div class="ltn__contact-info-brief">
                                        <h6>Alamat</h6>
                                        <p>{{ $setting_web->address ?? 'Alamat tidak tersedia' }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="ltn__contact-info-item ltn__contact-info-item-3 ltn__contact-info-item-3-bg-white">
                                    <div class="ltn__contact-info-icon">
                                        <i class="flaticon-call"></i>
                                    </div>
                                    <div class="ltn__contact-info-brief">
                                        <h6>Telepon</h6>
                                        <p>{{ $setting_web->phone ?? 'Telepon tidak tersedia' }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="ltn__contact-info-item ltn__contact-info-item-3 ltn__contact-info-item-3-bg-white">
                                    <div class="ltn__contact-info-icon">
                                        <i class="flaticon-mail"></i>
                                    </div>
                                    <div class="ltn__contact-info-brief">
                                        <h6>Email</h6>
                                        <p>{{ $setting_web->email ?? 'Email tidak tersedia' }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="ltn__contact-info-item ltn__contact-info-item-3 ltn__contact-info-item-3-bg-white">
                                    <div class="ltn__contact-info-icon">
                                        <i class="flaticon-clock"></i>
                                    </div>
                                    <div class="ltn__contact-info-brief">
                                        <h6>Jam Operasional</h6>
                                        <p>Senin - Jumat: 08:00 - 16:00 WIB</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="mt-50">
                        <div class="alert alert-warning">
                            <strong>Penting:</strong> Dengan mendaftar dan menggunakan layanan kami, Anda dianggap telah membaca, memahami, dan menyetujui seluruh syarat dan ketentuan yang berlaku.
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- TERMS OF SERVICE AREA END -->
@endsection
