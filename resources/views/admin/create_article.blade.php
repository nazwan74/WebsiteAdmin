@extends('admin.layouts.app', [
    'activePage' => 'articel',
    'navbarTitle' => 'Tambah Artikel',
    'navbarSubtitle' => 'Manajemen Artikel'
])

@section('title', 'Tambah Artikel Baru')

@section('head-scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
@endsection

@section('styles')
<style>
    .card-title {
        color: #333;
        font-weight: 600;
    }
    
    .form-label {
        color: #495057;
        margin-bottom: 8px;
    }
    
    .text-muted {
        font-size: 0.875rem;
        color: #6c757d;
    }
    
    .invalid-feedback {
        font-size: 0.875rem;
        color: #dc3545;
    }
    
    /* Gaya Summernote */
    .note-editor {
        border-radius: 8px;
        border: 1px solid #dee2e6;
    }
    
    .note-editor.note-frame {
        border-radius: 8px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid pb-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="glass-card overflow-hidden">
                <div class="p-4 bg-white bg-opacity-50 border-bottom d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="fw-bold mb-0 text-dark">Tambah Artikel Baru</h4>
                        <p class="text-muted small mb-0">Publikasikan konten edukasi berkualitas untuk masyarakat</p>
                    </div>
                    <a href="{{ route('admin.articel.index') }}" class="btn btn-light btn-sm px-3 shadow-sm border" style="border-radius: 8px;">
                        <i class="bi bi-arrow-left me-1" style="color: #FFCB05;"></i> Kembali
                    </a>
                </div>
                
                <div class="p-4">
                    <form action="{{ route('admin.articel.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
            
                        <div class="row g-4">
                            <!-- Left Column: Title & Content -->
                            <div class="col-lg-8">
                                <div class="mb-4">
                                    <label for="title" class="form-label fw-bold small text-uppercase" style="letter-spacing: 0.05em; color: #64748b;">Judul Artikel</label>
                                    <input type="text" class="form-control border-0 shadow-sm py-2 px-3 @error('title') is-invalid @enderror" id="title" name="title" placeholder="Tulis judul yang menarik..." style="border-radius: 12px; font-size: 1.1rem; font-weight: 600;" value="{{ old('title') }}" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                    
                                <div class="mb-3">
                                    <label for="description" class="form-label fw-bold small text-uppercase" style="letter-spacing: 0.05em; color: #64748b;">Isi Konten</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" required>{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Right Column: Thumbnail & Category -->
                            <div class="col-lg-4">
                                <div class="glass-card p-4 bg-light bg-opacity-50" style="border-radius: 16px;">
                                    <div class="mb-4">
                                        <label for="photoUrl" class="form-label fw-bold small text-uppercase" style="letter-spacing: 0.05em; color: #64748b;">Thumbnail</label>
                                        <div class="mb-3 p-3 text-center border-2 border-dashed rounded-4 bg-white" style="border-style: dashed !important; border-color: #cbd5e1 !important;">
                                            <i class="bi bi-cloud-arrow-up fs-1 opacity-50" style="color: #FFCB05;"></i>
                                            <p class="small text-muted mt-2">Format: JPG, PNG, WEBP (Max 2MB)</p>
                                            <input class="form-control form-control-sm @error('photoUrl') is-invalid @enderror" type="file" id="photoUrl" name="photoUrl" accept="image/*" required>
                                        </div>
                                        @error('photoUrl')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                        
                                    <div class="mb-4">
                                        <label for="articleType" class="form-label fw-bold small text-uppercase" style="letter-spacing: 0.05em; color: #64748b;">Kategori</label>
                                        <select class="form-select border-0 shadow-sm py-2 px-3 @error('articleType') is-invalid @enderror" id="articleType" name="articleType" style="border-radius: 12px;" required>
                                            <option value="">Pilih Kategori</option>
                                            <option value="stunting" {{ old('articleType') == 'stunting' ? 'selected' : '' }}>Stunting</option>
                                            <option value="bullying" {{ old('articleType') == 'bullying' ? 'selected' : '' }}>Bullying</option>
                                            <option value="pernikahan dini" {{ old('articleType') == 'pernikahan dini' ? 'selected' : '' }}>Pernikahan Anak</option>
                                            <option value="kekerasan anak" {{ old('articleType') == 'kekerasan anak' ? 'selected' : '' }}>Kekerasan Anak</option>
                                        </select>
                                        @error('articleType')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <hr class="my-4 opacity-10">

                                    <button type="submit" class="btn btn-primary w-100 py-3 shadow-sm d-flex align-items-center justify-content-center gap-2 border-0" style="background: linear-gradient(135deg, #FFCB05 0%, #E6B800 100%); border-radius: 14px; color: #333;">
                                        <i class="bi bi-send-fill"></i>
                                        <span class="fw-bold">Publikasikan Artikel</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
<script>
    // Inisialisasi Summernote Editor
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
    });
</script>
@endsection
