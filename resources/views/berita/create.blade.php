@extends('layouts.app')

@section('title', 'Tambah Berita')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card card-berita">
            <div class="filter-header">
                <i class="bi bi-plus-circle-fill me-1"></i> Tambah Berita
            </div>
            <div class="card-body">
                <form action="{{ route('berita.store') }}" method="POST" enctype="multipart/form-data">                    

                    <div class="mb-3">
                        <label for="judul" class="form-label fw-semibold">Judul</label>
                        <input type="text" name="judul" id="judul" class="form-control"
                               value="{{ old('judul') }}" placeholder="Judul berita...">
                        @error('judul') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="kategori_id" class="form-label fw-semibold">Kategori</label>
                        <select name="kategori_id" id="kategori_id" class="form-select">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach ($kategoris as $k)
                                <option value="{{ $k->id }}" @selected(old('kategori_id') == $k->id)>{{ $k->nama }}</option>
                            @endforeach
                        </select>
                        @error('kategori_id') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold d-block">Tag</label>
                        @foreach ($tags as $tag)
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="tags[]"
                                    id="tag{{ $tag->id }}" value="{{ $tag->id }}"
                                    @checked(in_array($tag->id, old('tags', [])))>
                                <label class="form-check-label" for="tag{{ $tag->id }}">#{{ $tag->nama }}</label>
                            </div>
                        @endforeach
                    </div>

                    <div class="mb-3">
                        <label for="isi" class="form-label fw-semibold">Isi Berita</label>
                        <textarea name="isi" id="isi" rows="6" class="form-control"
                                  placeholder="Tulis isi berita di sini...">{{ old('isi') }}</textarea>
                        @error('isi') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="gambar" class="form-label fw-semibold">Gambar (opsional)</label>
                        <input type="file" name="gambar" id="gambar" class="form-control" accept="image/*">
                        <small class="text-muted">Format JPG, PNG, atau WEBP. Maksimal 2 MB.</small>
                        @error('gambar') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>


                    <div class="d-flex gap-2 pt-2 border-top mt-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Simpan
                        </button>
                        <a href="{{ route('berita.index') }}" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
