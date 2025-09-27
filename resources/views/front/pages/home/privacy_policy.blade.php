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

    <!-- PRIVACY POLICY AREA START -->
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

                        <h2>Kebijakan Privasi</h2>
                        <p>Kebijakan Privasi ini menjelaskan bagaimana {{ $setting_web->name }} ("kami", "kita", atau "milik kami") mengumpulkan, menggunakan, dan melindungi informasi Anda ketika Anda menggunakan layanan kami.</p>

                        <h3>1. Informasi yang Kami Kumpulkan</h3>

                        <h4>1.1 Informasi Pribadi</h4>
                        <p>Kami dapat mengumpulkan informasi pribadi berikut ini:</p>
                        <ul>
                            <li>Nama lengkap</li>
                            <li>Alamat email</li>
                            <li>Nomor telepon</li>
                            <li>Alamat</li>
                            <li>Informasi akademik dan profesional</li>
                            <li>Foto profil</li>
                        </ul>

                        <h4>1.2 Informasi melalui Google OAuth</h4>
                        <p>Ketika Anda menggunakan fitur "Login dengan Google", kami mengakses informasi berikut dari akun Google Anda:</p>
                        <ul>
                            <li>Nama lengkap</li>
                            <li>Alamat email</li>
                            <li>Foto profil</li>
                            <li>ID unik Google</li>
                        </ul>
                        <p>Kami hanya mengakses informasi yang diperlukan untuk memberikan layanan dan tidak mengakses data Google lainnya tanpa persetujuan eksplisit Anda.</p>

                        <h4>1.3 Informasi Teknis</h4>
                        <p>Kami secara otomatis mengumpulkan informasi teknis tertentu, termasuk:</p>
                        <ul>
                            <li>Alamat IP</li>
                            <li>Jenis browser dan versi</li>
                            <li>Sistem operasi</li>
                            <li>Halaman yang dikunjungi</li>
                            <li>Waktu dan durasi kunjungan</li>
                            <li>Sumber rujukan</li>
                        </ul>

                        <h3>2. Bagaimana Kami Menggunakan Informasi</h3>
                        <p>Kami menggunakan informasi yang dikumpulkan untuk tujuan berikut:</p>
                        <ul>
                            <li>Menyediakan dan memelihara layanan kami</li>
                            <li>Memproses pendaftaran dan autentikasi pengguna</li>
                            <li>Mengelola submission artikel dan proses peer review</li>
                            <li>Berkomunikasi dengan pengguna mengenai layanan</li>
                            <li>Mengirim notifikasi dan update penting</li>
                            <li>Memproses pembayaran dan transaksi</li>
                            <li>Menganalisis penggunaan untuk meningkatkan layanan</li>
                            <li>Mencegah penyalahgunaan dan aktivitas yang melanggar hukum</li>
                            <li>Mematuhi kewajiban hukum</li>
                        </ul>

                        <h3>3. Berbagi Informasi</h3>
                        <p>Kami tidak menjual, memperdagangkan, atau mentransfer informasi pribadi Anda kepada pihak ketiga, kecuali dalam situasi berikut:</p>
                        <ul>
                            <li><strong>Penyedia Layanan:</strong> Kami dapat berbagi informasi dengan penyedia layanan tepercaya yang membantu operasional kami</li>
                            <li><strong>Kewajiban Hukum:</strong> Ketika diwajibkan oleh hukum atau untuk melindungi hak-hak kami</li>
                            <li><strong>Persetujuan Anda:</strong> Ketika Anda memberikan persetujuan eksplisit</li>
                            <li><strong>Proses Editorial:</strong> Informasi reviewer dan editor dapat dibagikan untuk keperluan proses editorial dan penerbitan</li>
                        </ul>

                        <h3>4. Keamanan Data</h3>
                        <p>Kami menerapkan langkah-langkah keamanan yang sesuai untuk melindungi informasi pribadi Anda, termasuk:</p>
                        <ul>
                            <li>Enkripsi data sensitif</li>
                            <li>Akses terbatas pada informasi pribadi</li>
                            <li>Pemantauan sistem keamanan secara berkala</li>
                            <li>Pelatihan keamanan untuk staf</li>
                        </ul>

                        <h3>5. Cookies dan Teknologi Serupa</h3>
                        <p>Kami menggunakan cookies dan teknologi serupa untuk:</p>
                        <ul>
                            <li>Mengingat preferensi Anda</li>
                            <li>Memahami bagaimana Anda menggunakan situs kami</li>
                            <li>Meningkatkan pengalaman pengguna</li>
                            <li>Menyediakan fitur login otomatis</li>
                        </ul>
                        <p>Anda dapat mengatur browser Anda untuk menolak cookies, namun hal ini dapat mempengaruhi fungsi website.</p>

                        <h3>6. Hak Anda</h3>
                        <p>Anda memiliki hak untuk:</p>
                        <ul>
                            <li>Mengakses informasi pribadi yang kami miliki tentang Anda</li>
                            <li>Memperbarui atau memperbaiki informasi yang tidak akurat</li>
                            <li>Meminta penghapusan informasi pribadi Anda</li>
                            <li>Membatasi pemrosesan informasi Anda</li>
                            <li>Menarik persetujuan yang telah diberikan</li>
                            <li>Memindahkan data Anda ke layanan lain</li>
                        </ul>

                        <h3>7. Integrasi dengan Google</h3>
                        <p>Untuk fitur Google OAuth, kami mematuhi Kebijakan Privasi Google dan persyaratan berikut:</p>
                        <ul>
                            <li>Kami hanya meminta akses minimum yang diperlukan</li>
                            <li>Kami tidak menyimpan kredensial Google Anda</li>
                            <li>Anda dapat mencabut akses kapan saja melalui pengaturan akun Google Anda</li>
                            <li>Kami tidak menggunakan data Google untuk tujuan iklan</li>
                        </ul>

                        <h3>8. Penyimpanan Data</h3>
                        <p>Kami menyimpan informasi pribadi Anda selama:</p>
                        <ul>
                            <li>Akun Anda aktif</li>
                            <li>Diperlukan untuk memberikan layanan</li>
                            <li>Diwajibkan oleh hukum</li>
                            <li>Diperlukan untuk kepentingan akademik yang sah</li>
                        </ul>

                        <h3>9. Transfer Data Internasional</h3>
                        <p>Data Anda dapat diproses di server yang berlokasi di luar negara Anda. Kami memastikan tingkat perlindungan yang memadai sesuai dengan hukum yang berlaku.</p>

                        <h3>10. Perubahan Kebijakan</h3>
                        <p>Kami dapat memperbarui Kebijakan Privasi ini dari waktu ke waktu. Perubahan akan dipublikasikan di halaman ini dengan tanggal revisi terbaru. Kami mendorong Anda untuk meninjau kebijakan ini secara berkala.</p>

                        <h3>11. Privasi Anak</h3>
                        <p>Layanan kami tidak ditujukan untuk anak-anak di bawah usia 13 tahun. Kami tidak secara sadar mengumpulkan informasi pribadi dari anak-anak di bawah 13 tahun.</p>

                        <h3>12. Kontak</h3>
                        <p>Jika Anda memiliki pertanyaan tentang Kebijakan Privasi ini atau ingin menggunakan hak Anda, silakan hubungi kami:</p>
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
                        <div class="alert alert-info">
                            <strong>Catatan Penting:</strong> Dengan menggunakan layanan kami, Anda menyatakan bahwa Anda telah membaca, memahami, dan menyetujui Kebijakan Privasi ini.
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- PRIVACY POLICY AREA END -->
@endsection
