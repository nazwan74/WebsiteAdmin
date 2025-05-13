<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tambah Artikel Baru</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card shadow">
        <div class="card-body">
            <h3 class="card-title text-center mb-4">Tambah Artikel Baru</h3>

            
            <form action="{{ route('admin.articel.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- Thumbnail --}}
                <div class="mb-3">
                    <label for="photoUrl" class="form-label fw-semibold">Tambah Thumbnail Artikel</label>
                    <input class="form-control @error('photoUrl') is-invalid @enderror" type="file" id="photoUrl" name="photoUrl" accept="image/*" required>
                    <small class="text-muted">Ukran Maximum File: 2MB</small>
                    @error('photoUrl')
                        <div class="invalid-feedback">
                            @if($message == 'The photo url field is required.')
                                Thumbnail artikel harus diisi
                            @elseif($message == 'The photo url must be an image.')
                                File yang diunggah harus berupa gambar
                            @elseif($message == 'The photo url must not be greater than 2048 kilobytes.')
                                Ukuran file tidak boleh lebih dari 2MB
                            @else
                                {{ $message }}
                            @endif
                        </div>
                    @enderror
                </div>

                {{-- Category --}}
                <div class="mb-3">
                    <label for="articleType" class="form-label fw-semibold">Pilih Kategori Artikel</label>
                    <select class="form-select @error('articleType') is-invalid @enderror" id="articleType" name="articleType" required>
                        <option value="">-- Pilih Kategori --</option>
                        <option value="stunting">Stunting</option>
                        <option value="bullying">Bullying</option>
                        <option value="pernikahan dini">Pernikahan Anak</option>
                        <option value="kekerasan anak">Kekerasan Anak</option>
                    </select>
                    @error('articleType')
                        <div class="invalid-feedback">
                            @if($message == 'The article type field is required.')
                                Kategori artikel harus dipilih
                            @else
                                {{ $message }}
                            @endif
                        </div>
                    @enderror
                </div>

                {{-- Title --}}
                <div class="mb-3">
                    <label for="title" class="form-label fw-semibold">Tambah Judul Artikel</label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" placeholder="Tulis Judul Artikel Disini" required>
                    @error('title')
                        <div class="invalid-feedback">
                            @if($message == 'The title field is required.')
                                Judul artikel harus diisi
                            @else
                                {{ $message }}
                            @endif
                        </div>
                    @enderror
                </div>

                {{-- Content --}}
                <div class="mb-4">
                    <label for="description" class="form-label fw-semibold">Tambah Konten Artikel</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="5" placeholder="Tulis Isi Artikel Disini..." required></textarea>
                    @error('description')
                        <div class="invalid-feedback">
                            @if($message == 'The description field is required.')
                                Konten artikel harus diisi
                            @else
                                {{ $message }}
                            @endif
                        </div>
                    @enderror
                </div>

                {{-- Buttons --}}
                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.articel.index') }}" class="btn btn-danger">
                        <i class="bi bi-arrow-left"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Simpan Artikel
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

<script>
    CKEDITOR.replace('description');
</script>
</body>
</html>
