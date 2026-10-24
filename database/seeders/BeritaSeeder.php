<?php

namespace Database\Seeders;

use App\Models\Berita;
use App\Models\Kategori;
use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BeritaSeeder extends Seeder
{
    public function run(): void
    {
        $olahraga = Kategori::firstOrCreate(['nama' => 'Olahraga']);
        $pendidikan = Kategori::firstOrCreate(['nama' => 'Pendidikan']);
        $potensi = Kategori::firstOrCreate(['nama' => 'Potensi']);

        $beritas = [
            [
                'judul' => 'Turnamen Voli Antar Dusun Resmi Dibuka',
                'isi' => 'Turnamen voli tahunan antar dusun resmi dibuka oleh kepala desa...',
                'kategori_id' => $olahraga->id,
                'tags' => ['desa', 'kegiatan'],
            ],
            [
                'judul' => 'Sosialisasi Bank Sampah untuk Warga',
                'isi' => 'Pemerintah desa mengadakan sosialisasi pengelolaan sampah...',
                'kategori_id' => $pendidikan->id,
                'tags' => ['warga', 'lingkungan'],
            ],
            [
                'judul' => 'Panen Raya Kelompok Wanita Tani',
                'isi' => 'Kelompok wanita tani desa berhasil panen sayuran organik...',
                'kategori_id' => $potensi->id,
                'tags' => ['desa', 'ekonomi'],
            ],
        ];

        foreach ($beritas as $item) {
            $tags = $item['tags'];
            unset($item['tags']);

            $item['slug'] = Str::slug($item['judul']);
            $item['tanggal'] = now()->toDateString();
            $item['ringkasan'] = Str::limit(strip_tags($item['isi']), 150);
            $item['views'] = 0;

            $berita = Berita::updateOrCreate(
                ['slug' => $item['slug']],
                $item
            );

            $tagIds = collect($tags)->map(function ($nama) {
                return Tag::firstOrCreate(['nama' => $nama])->id;
            });

            $berita->tags()->sync($tagIds);
        }
    }
}
