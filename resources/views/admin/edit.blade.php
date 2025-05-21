<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Article</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Summernote CSS -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card shadow">
        <div class="card-body">
            <h3 class="card-title text-center mb-4">Edit Artikel</h3>

            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('admin.articel.update', $articleData['id']) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- Thumbnail --}}
                <div class="mb-3">
                    <label for="photoUrl" class="form-label fw-semibold">Edit Thumbnail Artikel</label>
                    @if(isset($articleData['gambar_url']))
                    <div class="mb-2 text-center">
                        <img src="{{ $articleData['gambar_url'] }}" alt="Current Image" class="img-thumbnail" style="max-height: 150px;">
                    </div>
                    @else
                    <div class="mb-2 text-center">
                        <div class="alert alert-info">Tidak ada gambar tersedia</div>
                    </div>
                    @endif
                    <input class="form-control @error('photoUrl') is-invalid @enderror" type="file" id="photoUrl" name="photoUrl" accept="image/*">
                    <small class="text-muted">Ukuran Maximum File: 2MB</small>
                    <small class="d-block text-muted">Kosongkan jika tidak ingin mengganti gambar</small>
                    @error('photoUrl')
                        <div class="invalid-feedback">
                            @if($message == 'The photo url must be an image.')
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
                    <label for="articleType" class="form-label fw-semibold">Edit Kategori</label>
                    <select class="form-select @error('articleType') is-invalid @enderror" id="articleType" name="articleType" required>
                        <option value="">--Pilih Kategori--</option>
                        <option value="stunting" {{ $articleData['articleType'] == 'stunting' ? 'selected' : '' }}>Stunting</option>
                        <option value="bullying" {{ $articleData['articleType'] == 'bullying' ? 'selected' : '' }}>Bullying</option>
                        <option value="pernikahan dini" {{ $articleData['articleType'] == 'pernikahan dini' ? 'selected' : '' }}>Pernikahan Anak</option>
                        <option value="kekerasan anak" {{ $articleData['articleType'] == 'kekerasan anak' ? 'selected' : '' }}>Kekerasan Anak</option>
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
                    <label for="title" class="form-label fw-semibold">Edit Judul Artikel</label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" placeholder="Tulis Judul Artikel Disini" value="{{ $articleData['title'] }}" required>
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

                {{-- Hashtags --}}
                <div class="mb-3">
                    <label for="hashtags" class="form-label fw-semibold">Edit Hashtag</label>
                    <input type="text" class="form-control @error('hashtags') is-invalid @enderror" id="hashtags" name="hashtags" placeholder="Contoh: #stunting #anak #kesehatan" value="{{ isset($articleData['hashtags']) ? $articleData['hashtags'] : '' }}" required>
                    <small class="text-muted">Pisahkan setiap hashtag dengan spasi</small>
                    @error('hashtags')
                        <div class="invalid-feedback">
                            @if($message == 'The hashtags field is required.')
                                Hashtag harus diisi
                            @else
                                {{ $message }}
                            @endif
                        </div>
                    @enderror
                </div>

                {{-- Content --}}
                <div class="mb-4">
                    <label for="description" class="form-label fw-semibold">Edit Konten Artikel</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="5" placeholder="Tulis Isi Artikel Disini..." required>{{ $articleData['description'] }}</textarea>
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
                        <i class="bi bi-arrow-repeat"></i> Perbaharui
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Summernote JS -->
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>

<script>
    $(document).ready(function() {
        $('#description').summernote({
            height: 300,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ]
        });

        // Add form submit handler to combine hashtags with content
        $('form').on('submit', function(e) {
            e.preventDefault();
            
            // Get hashtags value
            const hashtags = $('#hashtags').val();
            
            // Get Summernote content
            let content = $('#description').summernote('code');
            
            // Remove existing hashtags from content if they exist
            content = content.replace(/<br><br>#.*$/, '');
            
            // Combine content with hashtags
            const combinedContent = content + '<br><br>' + hashtags;
            
            // Set the combined content back to Summernote
            $('#description').summernote('code', combinedContent);
            
            // Submit the form
            this.submit();
        });
    });
</script>
</body>
</html>
