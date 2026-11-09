<?php

namespace Database\Seeders;

use App\Models\Buku;
use App\Models\Kategori;
use Illuminate\Database\Seeder;

class BukuSeeder extends Seeder
{
    public function run(): void
    {
        $kategori = Kategori::pluck('id', 'nama');

        // [judul, penulis, penerbit, tahun, kategori, halaman, bahasa, sinopsis]
        $data = [
            ['Laskar Pelangi', 'Andrea Hirata', 'Bentang Pustaka', 2005, 'Fiksi & Sastra', 529, 'Indonesia', 'Kisah sekelompok anak di Belitung yang berjuang menimba ilmu di sekolah sederhana dengan mimpi yang besar.'],
            ['Bumi Manusia', 'Pramoedya Ananta Toer', 'Hasta Mitra', 1980, 'Fiksi & Sastra', 535, 'Indonesia', 'Roman sejarah tentang Minke di masa kolonial, pergulatan identitas, cinta, dan keadilan.'],
            ['Negeri 5 Menara', 'Ahmad Fuadi', 'Gramedia Pustaka Utama', 2009, 'Fiksi & Sastra', 423, 'Indonesia', 'Perjalanan enam santri di pesantren yang belajar bahwa man jadda wajada: siapa bersungguh-sungguh akan berhasil.'],
            ['Pulang', 'Leila S. Chudori', 'Kepustakaan Populer Gramedia', 2012, 'Fiksi & Sastra', 464, 'Indonesia', 'Novel tentang para eksil politik Indonesia di Paris dan pencarian makna rumah.'],
            ['Laut Bercerita', 'Leila S. Chudori', 'Kepustakaan Populer Gramedia', 2017, 'Fiksi & Sastra', 379, 'Indonesia', 'Kisah persahabatan dan kehilangan para aktivis mahasiswa menjelang runtuhnya Orde Baru.'],
            ['Cantik Itu Luka', 'Eka Kurniawan', 'Gramedia Pustaka Utama', 2002, 'Fiksi & Sastra', 520, 'Indonesia', 'Saga keluarga lintas generasi yang memadukan sejarah, mitos, dan realisme magis.'],
            ['Bumi', 'Tere Liye', 'Gramedia Pustaka Utama', 2014, 'Fiksi & Sastra', 440, 'Indonesia', 'Petualangan fantasi seorang remaja yang menemukan dunia paralel di balik kehidupan sehari-hari.'],
            ['The Great Gatsby', 'F. Scott Fitzgerald', 'Scribner', 1925, 'Fiksi & Sastra', 180, 'Inggris', 'Potret ambisi, cinta, dan kemewahan semu di Amerika era Jazz.'],
            ['To Kill a Mockingbird', 'Harper Lee', 'J. B. Lippincott & Co.', 1960, 'Fiksi & Sastra', 281, 'Inggris', 'Kisah tentang keadilan dan prasangka rasial dilihat dari mata seorang anak di Amerika bagian selatan.'],
            ['1984', 'George Orwell', 'Secker & Warburg', 1949, 'Fiksi & Sastra', 328, 'Inggris', 'Distopia klasik tentang pengawasan total, propaganda, dan hilangnya kebenaran.'],

            ['A Brief History of Time', 'Stephen Hawking', 'Bantam Books', 1988, 'Sains & Teknologi', 256, 'Inggris', 'Pengantar populer tentang asal-usul alam semesta, lubang hitam, dan hakikat waktu.'],
            ['Cosmos', 'Carl Sagan', 'Random House', 1980, 'Sains & Teknologi', 396, 'Inggris', 'Perjalanan memukau melintasi sejarah sains dan tempat manusia di alam semesta.'],
            ['The Selfish Gene', 'Richard Dawkins', 'Oxford University Press', 1976, 'Sains & Teknologi', 224, 'Inggris', 'Pandangan evolusi yang berpusat pada gen dan gagasan tentang meme.'],
            ['Clean Code', 'Robert C. Martin', 'Prentice Hall', 2008, 'Sains & Teknologi', 464, 'Inggris', 'Panduan menulis kode yang bersih, mudah dibaca, dan mudah dipelihara.'],
            ['The Pragmatic Programmer', 'Andrew Hunt & David Thomas', 'Addison-Wesley', 1999, 'Sains & Teknologi', 352, 'Inggris', 'Kumpulan praktik dan sikap profesional bagi pengembang perangkat lunak.'],
            ['Introduction to Algorithms', 'Thomas H. Cormen dkk.', 'MIT Press', 2009, 'Sains & Teknologi', 1312, 'Inggris', 'Rujukan komprehensif tentang algoritma dan struktur data.'],
            ['Laravel: Up & Running', 'Matt Stauffer', "O'Reilly Media", 2019, 'Sains & Teknologi', 550, 'Inggris', 'Panduan membangun aplikasi web modern dengan framework Laravel.'],

            ['Sapiens: Riwayat Singkat Umat Manusia', 'Yuval Noah Harari', 'Kepustakaan Populer Gramedia', 2017, 'Sejarah & Sosial', 544, 'Indonesia', 'Menelusuri sejarah Homo sapiens dari zaman batu hingga revolusi teknologi.'],
            ['Guns, Germs, and Steel', 'Jared Diamond', 'W. W. Norton', 1997, 'Sejarah & Sosial', 480, 'Inggris', 'Mengapa peradaban berkembang berbeda di berbagai benua? Sebuah tinjauan lintas disiplin.'],
            ['Sejarah Indonesia Modern 1200–2008', 'M. C. Ricklefs', 'Serambi', 2008, 'Sejarah & Sosial', 800, 'Indonesia', 'Tinjauan menyeluruh sejarah Nusantara dari masa awal hingga era reformasi.'],

            ['Atomic Habits', 'James Clear', 'Avery', 2018, 'Bisnis & Pengembangan Diri', 320, 'Inggris', 'Cara praktis membangun kebiasaan baik lewat perubahan kecil yang konsisten.'],
            ['Thinking, Fast and Slow', 'Daniel Kahneman', 'Farrar, Straus and Giroux', 2011, 'Bisnis & Pengembangan Diri', 499, 'Inggris', 'Dua sistem berpikir manusia dan bias yang memengaruhi keputusan kita.'],
            ['The Lean Startup', 'Eric Ries', 'Crown Business', 2011, 'Bisnis & Pengembangan Diri', 336, 'Inggris', 'Pendekatan membangun bisnis melalui eksperimen cepat dan pembelajaran tervalidasi.'],
            ['Rich Dad Poor Dad', 'Robert T. Kiyosaki', 'Warner Books', 1997, 'Bisnis & Pengembangan Diri', 336, 'Inggris', 'Pelajaran dasar literasi finansial dari dua sudut pandang tentang uang.'],
            ['Filosofi Teras', 'Henry Manampiring', 'Penerbit Buku Kompas', 2018, 'Filsafat & Pemikiran', 346, 'Indonesia', 'Pengantar Stoisisme untuk menghadapi kecemasan dan emosi negatif sehari-hari.'],

            ['Dunia Sophie', 'Jostein Gaarder', 'Mizan', 1991, 'Filsafat & Pemikiran', 798, 'Indonesia', 'Sejarah filsafat Barat yang dikemas dalam novel misteri yang memikat.'],

            ["Harry Potter and the Philosopher's Stone", 'J. K. Rowling', 'Bloomsbury', 1997, 'Anak & Remaja', 223, 'Inggris', 'Seorang anak yatim menemukan dirinya seorang penyihir dan memulai petualangan di Hogwarts.'],
            ['The Little Prince', 'Antoine de Saint-Exupéry', 'Reynal & Hitchcock', 1943, 'Anak & Remaja', 96, 'Inggris', 'Dongeng filosofis tentang persahabatan, kehilangan, dan hal-hal yang sungguh penting.'],

            ['The Design of Everyday Things', 'Don Norman', 'Basic Books', 2013, 'Seni & Desain', 368, 'Inggris', 'Prinsip desain yang berpusat pada manusia untuk benda dan antarmuka sehari-hari.'],
            ["Don't Make Me Think", 'Steve Krug', 'New Riders', 2014, 'Seni & Desain', 216, 'Inggris', 'Panduan ringkas tentang kegunaan (usability) situs web dan aplikasi.'],
            ['Steal Like an Artist', 'Austin Kleon', 'Workman Publishing', 2012, 'Seni & Desain', 160, 'Inggris', 'Sepuluh gagasan tentang kreativitas dan cara berkarya dari pengaruh di sekitar kita.'],
        ];

        foreach ($data as $i => [$judul, $penulis, $penerbit, $tahun, $kat, $halaman, $bahasa, $sinopsis]) {
            // Variasi stok yang tetap (deterministik) agar data contoh konsisten.
            $stok = 3 + (($i * 5) % 5);

            Buku::create([
                'kode' => 'BK-'.str_pad((string) ($i + 1), 4, '0', STR_PAD_LEFT),
                'judul' => $judul,
                'penulis' => $penulis,
                'penerbit' => $penerbit,
                'tahun_terbit' => $tahun,
                'kategori_id' => $kategori[$kat] ?? null,
                'jumlah_stok' => $stok,
                'tersedia' => $stok,
                'halaman' => $halaman,
                'bahasa' => $bahasa,
                'lokasi_rak' => chr(65 + ($i % 6)).'-'.str_pad((string) (($i % 9) + 1), 2, '0', STR_PAD_LEFT),
                'sinopsis' => $sinopsis,
            ]);
        }
    }
}
