<?php

namespace App\Http\Controllers;

use App\Models\Berita;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Kategori;
use App\Models\Tag;
use Illuminate\Support\Arr;
use App\Http\Controllers\KomentarController;
use Illuminate\Support\Facades\Storage;


class BeritaController extends Controller
{
    public function index(Request $request)
    {
        $beritas = Berita::with(['kategori', 'tags'])
            ->when($request->kata_kunci, function ($q, $kata) {
                $q->where('judul', 'like', "%{$kata}%");
            })
            ->when($request->kategori, function ($q, $id) {
                $q->where('kategori_id', $id);
            })
            ->when($request->tag, function ($q, $id) {
                $q->whereHas('tags', fn ($t) => $t->where('tags.id', $id));
            })
            ->latest()
            ->paginate(6)
            ->withQueryString();
    
        $kategoris = Kategori::orderBy('nama')->get();
    
        return view('berita.index', compact('beritas', 'kategoris'));
    }
    
    public function create()
    {
        return view('berita.create', [
            'kategoris' => Kategori::orderBy('nama')->get(),
            'tags'      => Tag::orderBy('nama')->get(),
        ]);
    }
    
    public function store(Request $request)
    {
        $data = $request->validate([
            'judul'       => 'required|max:255',
            'kategori_id' => 'required|exists:kategoris,id',
            'isi'         => 'required|min:10',
            'gambar'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'tags'        => 'nullable|array',
            'tags.*'      => 'exists:tags,id',
        ]);
    
        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')->store('berita', 'public');
        }
    
        $data['slug'] = Str::slug($data['judul']);
        $data['tanggal'] = now()->toDateString();

        $berita = Berita::create(Arr::except($data, 'tags'));
        $berita->tags()->sync($data['tags'] ?? []);
    
        return redirect()->route('berita.index')
            ->with('success', 'Berita berhasil ditambahkan!');
    }

    
    public function show(Berita $berita)
    {
        $berita->increment('views');
        $berita->load(['kategori', 'tags']);

        $komentars = $berita->komentars()->latest()->get();

        $terkait = Berita::where('id', '!=', $berita->id)
            ->latest()
            ->take(5)
            ->get();

        $soal = KomentarController::buatSoal();

        return view('berita.show', compact(
            'berita',
            'komentars',
            'terkait',
            'soal'
        ));
    }
    
    public function edit(Berita $berita)
    {
        return view('berita.edit', [
            'berita'    => $berita->load('tags'),
            'kategoris' => Kategori::orderBy('nama')->get(),
            'tags'      => Tag::orderBy('nama')->get(),
        ]);
    }
    
    public function update(Request $request, Berita $berita)
    {
        $data = $request->validate([
            'judul'       => 'required|max:255',
            'kategori_id' => 'required|exists:kategoris,id',
            'isi'         => 'required|min:10',
            'gambar'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'tags'        => 'nullable|array',
            'tags.*'      => 'exists:tags,id',
        ]);
    
        if ($request->hasFile('gambar')) {
            // hapus gambar lama supaya storage tidak penuh sampah
            if ($berita->gambar) {
                Storage::disk('public')->delete($berita->gambar);
            }
            $data['gambar'] = $request->file('gambar')->store('berita', 'public');
        } else {
            // tidak ada upload baru: jangan timpa gambar lama dengan null
            unset($data['gambar']);
        }

        $data['slug'] = Str::slug($data['judul']);
        $data['tanggal'] = now()->toDateString();
    
        $berita->update(Arr::except($data, 'tags'));
        $berita->tags()->sync($data['tags'] ?? []);
    
        return redirect()->route('berita.index')
            ->with('success', 'Berita berhasil diperbarui!');
    }



    public function destroy(Berita $berita)
    {
        if ($berita->gambar) {
            Storage::disk('public')->delete($berita->gambar);
        }
    
        $berita->delete();
    
        return redirect()->route('berita.index')
            ->with('success', 'Berita berhasil dihapus!');
    }

}
