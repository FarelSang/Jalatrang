<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Berita extends Model
{
    protected $fillable = [
        'judul',
        'slug',
        'kategori_id',
        'kategori',
        'gambar',
        'isi',
        'penulis',
        'tanggal',
        'ringkasan',
        'views',
    ];

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function komentars(): HasMany
    {
        return $this->hasMany(Komentar::class);
    }
}
