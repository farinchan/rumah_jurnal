<?php

namespace Database\Seeders;

use App\Models\News;
use App\Models\NewsCategory;
use App\Models\SettingBanner;
use App\Models\SettingWebsite;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        Role::create(['name' => 'super-admin']);
        Role::create(['name' => 'keuangan']);
        Role::create(['name' => 'editor']);

       User::create([
            'name' => 'Fajri - Developer',
            'email' => 'fajri@gariskode.com',
            'password' => bcrypt('password'),
        ])->assignRole('super-admin');

        SettingWebsite::create([
            'name' => 'Rumah Jurnal UIN Sjech M.Djamil Djambek bukttinggi',
            'logo' => 'logo.png',
            'favicon' => 'favicon.png',
            'email' => 'rumahjurnal.uinbukittinggi@gmail.com',
            'phone' => '089613390766',
            'address' => 'Data Center Building, 2nd floor, State Islamic University of Sjech M. Djamil Djambek Bukittinggi. Gurun Aua St, Kubang Putih, Banuhampu, Agam - West Sumatra - Indonesia.',
            'latitude' => '-0.32177371869479526',
            'longitude' => '100.39795359131934',
            'about' => '<p><strong>Rumah Jurnal UIN Sjech M. Djamil Djambek Bukittinggi</strong> adalah portal publikasi ilmiah resmi Universitas Islam Negeri Sjech M. Djamil Djambek Bukittinggi yang menyediakan akses terbuka terhadap berbagai jurnal akademik dari berbagai disiplin ilmu. Website ini bertujuan untuk mendukung pengembangan dan penyebaran penelitian ilmiah yang berkualitas serta menjadi wadah bagi akademisi, peneliti, dan mahasiswa dalam mempublikasikan hasil penelitian mereka.</p><p>Melalui platform ini, pengguna dapat mengakses, mengunduh, serta membaca artikel-artikel ilmiah yang telah melewati proses peer-review. Selain itu, Rumah Jurnal UIN Sjech M. Djamil Djambek Bukittinggi juga menyediakan informasi mengenai kebijakan penerbitan, petunjuk penulisan, serta jadwal penerbitan jurnal untuk mendukung transparansi dan kualitas publikasi. Dengan adanya portal ini, universitas berkomitmen untuk berkontribusi dalam pengembangan ilmu pengetahuan yang lebih luas, baik di tingkat nasional maupun internasional.</p>',
        ]);

        SettingBanner::create([
            'title' => 'Rumah Jurnal UIN Sjech M. Djamil Djambek Bukittinggi',
            'subtitle' => 'Portal Publikasi Ilmiah Resmi Universitas Islam Negeri Sjech M. Djamil Djambek Bukittinggi',
            'image' => 'setting/banner/vC5qyP6SqARhMTDtFaUm.png',
            'url' => 'https://uinbukittinggi.ac.id',
        ]);

        SettingBanner::create([
            'title' => 'Pusat Riset dan Publikasi Ilmiah UIN Sjech M. Djamil Djambek Bukittinggi',
            'subtitle' => 'Mendorong Pengembangan dan Penyebaran Penelitian Ilmiah yang Berkualitas di Indonesia',
            'image' => 'setting/banner/qJplFaRe6aIcaEwwiyPO.png',
            'url' => 'https://uinbukittinggi.ac.id',
        ]);

        NewsCategory::create([
            'name' => 'Berita',
            'slug' => 'berita',
            'description' => 'Kategori berita adalah kategori yang berisi informasi terkini dan terbaru mengenai kegiatan, acara, dan informasi penting lainnya yang terjadi di lingkungan Universitas Islam Negeri Sjech M. Djamil Djambek Bukittinggi.',
        ]);

        NewsCategory::create([
            'name' => 'Call for Paper',
            'slug' => 'call-for-paper',
            'description' => 'Kategori Call for Paper adalah kategori yang berisi informasi mengenai panggilan untuk mengirimkan artikel ilmiah ke berbagai jurnal akademik yang terindeks dan terakreditasi.',
        ]);



        News::create([
            'title' => 'Perkuat Reputasi Akademik, Dua Jurnal UIN Bukittinggi Raih Akreditasi SINTA 2',
            'slug' => 'perkuat-reputasi-akademik-dua-jurnal-uin-bukittinggi-raih-akreditasi-sinta-2',
            'news_category_id' => 1,
            'thumbnail' => 'news/20250306064812_perkuat-reputasi-akademik-dua-jurnal-uin-bukittinggi-raih-akreditasi-sinta-2.jpeg',
            'content' => '<p class="ql-align-justify">Bukittinggi (Humas) – Dua jurnal ilmiah dari Universitas Islam Negeri (UIN) Sjech M. Djamil Djambek Bukittinggi, yakni Jurnal Islam Realitas dan Jurnal Humanisma, resmi terakreditasi SINTA 2 Dikti. Pencapaian ini didasarkan pada Surat Keputusan Direktur Jenderal Pendidikan Tinggi, Riset dan Teknologi Nomor 117/E/KPT/2024.</p><p class="ql-align-justify">Akreditasi SINTA Dikti merupakan salah satu indikator kualitas publikasi ilmiah di Indonesia yang dikeluarkan oleh Kementerian Pendidikan, Kebudayaan, Riset, dan Teknologi. Akreditasi ini menjadi tolak ukur dan pengakuan terhadap kualitas jurnal. Akreditasi tersebut juga menjadi parameter untuk mendorong penelitian dan pengembangan ilmu pengetahuan yang lebih terarah di tingkat nasional.</p><p class="ql-align-justify">Rektor UIN Bukittinggi, Prof. Silfia Hanani, mengaku bangga atas pencapaian ini. “Akreditasi ini membuktikan komitmen kampus dalam meningkatkan kualitas riset dan publikasi ilmiah di UIN Bukittinggi,” pungkasnya.</p><p class="ql-align-justify">Dikatakannya, penghargaan akreditasi ini tidak hanya sekedar symbol pengakuan, tapi juga tantangan untuk terus berinovasi dan meningkatkan mutu. “Kami berharap pencapaian ini akan semakin memperkuat kontribusi UIN Bukittinggi dalam dunia akademik nasional dan internasional,” ujarnya.</p><p><br></p>',
            'user_id' => 1,
            'status' => 'published',
            'meta_title' => 'Perkuat Reputasi Akademik, Dua Jurnal UIN Bukittinggi Raih Akreditasi SINTA 2',
            'meta_description' => 'Bukittinggi (Humas) – Dua jurnal ilmiah dari Universitas Islam Negeri (UIN) Sjech M. Djamil Djambek Bukittinggi, yakni Jurnal Islam Realitas dan Jurna...',
            'meta_keywords' => 'journal; uin bukittinggi; sinta; akreditasi; jurnal ilmiah',
        ]);

        News::create([
            'title' => 'Panggilan Artikel Ilmiah untuk Edisi Terbaru Jurnal Ilmiah',
            'slug' => 'panggilan-artikel-ilmiah-untuk-edisi-terbaru-jurnal-ilmiah',
            'news_category_id' => 2,
            'thumbnail' => 'news/20250306064812_perkuat-reputasi-akademik-dua-jurnal-uin-bukittinggi-raih-akreditasi-sinta-2.jpeg',
            'content' => '<p>Sehubungan dengan akan diterbitkannya edisi terbaru Jurnal Ilmiah, kami mengundang para peneliti, akademisi, dan praktisi untuk mengirimkan artikel ilmiah yang berkualitas dan orisinil. Artikel yang dikirimkan akan melalui proses peer-review
            yang ketat dan transparan untuk memastikan kualitas dan keaslian artikel. Jurnal Ilmiah merupakan jurnal akademik yang terindeks dan terakreditasi sehingga artikel yang diterbitkan akan memiliki nilai yang tinggi dalam dunia ilmiah.</p>',
            'user_id' => 1,
            'status' => 'published',
            'meta_title' => 'Panggilan Artikel Ilmiah untuk Edisi Terbaru Jurnal Ilmiah',
            'meta_description' => 'Panggilan Artikel Ilmiah untuk Edisi Terbaru Jurnal Ilmiah',
            'meta_keywords' => 'panggilan artikel ilmiah, jurnal ilmiah, artikel ilmiah, call for paper',
        ]);

    }
}
