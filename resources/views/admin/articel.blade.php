@extends('admin.layouts.app', [
    'activePage' => 'articel',
    'navbarTitle' => 'Manajemen Artikel',
    'navbarSubtitle' => 'Admin'
])

@section('title', 'Manajemen Artikel')

@section('styles')
<style>
    /* Warna Latar Belakang */
    .bg-light-pink {
        background-color: #ffeef2;
    }
    
    .bg-light-red {
        background-color: #fff0f0;
    }
    
    .bg-light-orange {
        background-color: #fff9e6;
    }
    
    .bg-light-green {
        background-color: #eafaf2;
    }
    
    /* Gaya Badge */
    .badge.bg-pernikahan {
        background-color: #ffeef2;
        color: #212529;
    }
    
    .badge.bg-kekerasan {
        background-color: #fff0f0;
        color: #212529;
    }
    
    .badge.bg-bullying {
        background-color: #fff9e6;
        color: #212529;
    }
    
    .badge.bg-stunting {
        background-color: #eafaf2;
        color: #212529;
    }
    
    /* Gaya Tombol */
    .btn-outline-primary {
        color: #3498db;
        border-color: #3498db;
    }
    
    .btn-outline-danger {
        color: #e74c3c;
        border-color: #e74c3c;
    }
</style>
@endsection

@section('content')
<!-- Bagian Kartu Kategori Konten -->
<h2>Kategori Konten</h2>
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-light-pink text-dark h-100">
            <div class="card-body">
                <h5><span class="text-danger">❤</span> Pernikahan Anak</h5>
                <p class="text-end mb-0">{{ count(array_filter($articles, function($article) { return $article['articleType'] == 'pernikahan dini'; })) }} Artikel</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-light-red text-dark h-100">
            <div class="card-body">
                <h5><span class="text-danger">✋</span> Kekerasan Anak</h5>
                <p class="text-end mb-0">{{ count(array_filter($articles, function($article) { return $article['articleType'] == 'kekerasan anak'; })) }} Artikel</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-light-orange text-dark h-100">
            <div class="card-body">
                <h5><span class="text-warning">😣</span> Bullying</h5>
                <p class="text-end mb-0">{{ count(array_filter($articles, function($article) { return $article['articleType'] == 'bullying'; })) }} Artikel</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-light-green text-dark h-100">
            <div class="card-body">
                <h5><span class="text-success">↑</span> Stunting</h5>
                <p class="text-end mb-0">{{ count(array_filter($articles, function($article) { return $article['articleType'] == 'stunting'; })) }} Artikel</p>
            </div>
        </div>
    </div>
</div>

<!-- Bagian Daftar Artikel -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h2>Daftar Artikel</h2>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.articel.refresh') }}" class="btn btn-outline-secondary" title="Refresh Data">
                <i class="bi bi-arrow-clockwise"></i>
            </a>
            <a href="{{ route('admin.articel.downloadList') }}" id="downloadListBtn" class="btn btn-success">
                <i class="bi bi-download me-1"></i>Download List
            </a>
            <a href="{{ route('admin.articel.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i>Tambah Artikel
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-6">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Cari artikel..." id="search-input">
                    <button class="btn btn-outline-secondary" type="button">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </div>
            <div class="col-md-6 text-end">
                <select class="form-select float-end" style="width: auto;" id="category-filter">
                    <option selected value="all">Semua Kategori</option>
                    <option value="pernikahan dini">Pernikahan Anak</option>
                    <option value="kekerasan anak">Kekerasan Anak</option>
                    <option value="bullying">Bullying</option>
                    <option value="stunting">Stunting</option>
                </select>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <!-- Tata Letak Tabel Untuk Artikel -->
        <div class="table-responsive">
            <table class="table table-hover" id="articles-table">
                <thead>
                    <tr>
                        <th>Judul Artikel</th>
                        <th>Kategori</th>
                        <th>Tanggal Rilis</th>
                        <th>Tanggal Edit</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($articles as $article)
                        <tr class="article-row"
                        data-kategori="{{ $article['articleType'] }}"
                        data-description="{{ strtolower($article['description']) }}">
                            <td>{{ $article['title'] }}</td>
                            <td>
                                @php
                                    $badgeClass = '';
                                    switch($article['articleType']) {
                                        case 'pernikahan dini':
                                            $badgeClass = 'bg-pernikahan';
                                            $displayText = 'Pernikahan Anak';
                                            break;
                                        case 'kekerasan anak':
                                            $badgeClass = 'bg-kekerasan';
                                            $displayText = 'Kekerasan Anak';
                                            break;
                                        case 'bullying':
                                            $badgeClass = 'bg-bullying';
                                            $displayText = 'Bullying';
                                            break;
                                        case 'stunting':
                                            $badgeClass = 'bg-stunting';
                                            $displayText = 'Stunting';
                                            break;
                                        default:
                                            $badgeClass = 'bg-secondary';
                                            $displayText = ucfirst($article['articleType']);
                                    }
                                @endphp
                                <span class="badge {{ $badgeClass }}">{{ $displayText }}</span>
                            </td>
                            <td>
                                @if(isset($article['releasedDate']))
                                    @php
                                        if ($article['releasedDate'] instanceof Illuminate\Support\Carbon) {
                                            $date = $article['releasedDate']->format('d M Y');
                                        } elseif (is_string($article['releasedDate'])) {
                                            $date = date('d M Y', strtotime($article['releasedDate']));
                                        } else {
                                            $date = $article['releasedDate']->get()->format('d M Y');
                                        }
                                    @endphp
                                    {{ $date }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if(isset($article['updateDate']))
                                    @php
                                        if ($article['updateDate'] instanceof Illuminate\Support\Carbon) {
                                            $updateDate = $article['updateDate']->format('d M Y');
                                        } elseif (is_string($article['updateDate'])) {
                                            $updateDate = date('d M Y', strtotime($article['updateDate']));
                                        } else {
                                            $updateDate = $article['updateDate']->get()->format('d M Y');
                                        }
                                    @endphp
                                    <span class="text-info">
                                        <i class="bi bi-pencil-square"></i> {{ $updateDate }}
                                    </span>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.articel.edit', $article['id']) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.articel.destroy', $article['id']) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-outline-danger delete-article-btn" data-title="{{ $article['title'] }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">Tidak ada artikel yang tersedia.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- Pagination -->
        <div id="paginationWrapper" class="pagination-wrapper d-none">
            <div class="pagination-info" id="paginationInfo"></div>
            <nav aria-label="Navigasi halaman artikel">
                <ul class="pagination mb-0" id="paginationNav"></ul>
            </nav>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Fungsi saat dokumen siap
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('search-input');
        const categoryFilter = document.getElementById('category-filter');
        const rows = document.querySelectorAll('#articles-table tbody tr.article-row');

        const perPage = 10;
        let currentPage = 1;

        function applyFilters() {
            const searchTerm = searchInput.value.toLowerCase();
            const selectedCategory = categoryFilter.value;

            rows.forEach(row => {
                const title = row.querySelector('td:first-child').textContent.toLowerCase();
                const description = row.getAttribute('data-description') || '';
                const category = row.getAttribute('data-kategori');

                const matchCategory = selectedCategory === 'all' || category === selectedCategory;
                const matchSearch = title.includes(searchTerm) ||
                    description.includes(searchTerm);

                row.style.display = (matchCategory && matchSearch) ? '' : 'none';
            });

            const visibleRows = Array.from(rows).filter(r => r.style.display !== 'none');
            const totalVisible = visibleRows.length;
            const totalPages = Math.max(1, Math.ceil(totalVisible / perPage));
            if (currentPage > totalPages) currentPage = totalPages;
            const start = (currentPage - 1) * perPage;
            const end = start + perPage;
            visibleRows.forEach((row, i) => {
                row.style.display = (i >= start && i < end) ? '' : 'none';
            });

            updatePaginationUI(totalVisible, totalPages);
            updateDownloadLink();
        }

        function updatePaginationUI(totalVisible, totalPages) {
            const wrapper = document.getElementById('paginationWrapper');
            const infoEl = document.getElementById('paginationInfo');
            const navEl = document.getElementById('paginationNav');
            if (!wrapper || !infoEl || !navEl) return;
            if (totalVisible === 0) {
                wrapper.classList.add('d-none');
                return;
            }
            wrapper.classList.remove('d-none');
            const start = (currentPage - 1) * perPage + 1;
            const end = Math.min(currentPage * perPage, totalVisible);
            infoEl.textContent = 'Menampilkan ' + start + '–' + end + ' dari ' + totalVisible + ' artikel';

            navEl.innerHTML = '';
            function addPageItem(label, pageNum, disabled, active) {
                const li = document.createElement('li');
                li.className = 'page-item' + (disabled ? ' disabled' : '') + (active ? ' active' : '');
                const a = document.createElement('a');
                a.className = 'page-link';
                a.href = '#';
                a.textContent = label;
                if (!disabled) {
                    a.addEventListener('click', function(e) {
                        e.preventDefault();
                        currentPage = pageNum;
                        applyFilters();
                    });
                }
                li.appendChild(a);
                navEl.appendChild(li);
            }
            addPageItem('Sebelumnya', currentPage - 1, currentPage <= 1, false);
            const maxButtons = 5;
            let from = Math.max(1, currentPage - Math.floor(maxButtons / 2));
            let to = Math.min(totalPages, from + maxButtons - 1);
            if (to - from + 1 < maxButtons) from = Math.max(1, to - maxButtons + 1);
            for (let p = from; p <= to; p++) addPageItem(p, p, false, p === currentPage);
            addPageItem('Selanjutnya', currentPage + 1, currentPage >= totalPages, false);
        }

        function updateDownloadLink() {
            const baseUrl = '{{ route("admin.articel.downloadList") }}';
            const params = [];
            const cat = categoryFilter.value;
            if (cat && cat !== 'all') params.push('kategori=' + encodeURIComponent(cat));
            const searchVal = searchInput.value.trim();
            if (searchVal) params.push('search=' + encodeURIComponent(searchVal));
            const url = params.length ? baseUrl + '?' + params.join('&') : baseUrl;
            const btn = document.getElementById('downloadListBtn');
            if (btn) btn.setAttribute('href', url);
        }

        searchInput.addEventListener('input', function() {
            currentPage = 1;
            applyFilters();
        });
        categoryFilter.addEventListener('change', function() {
            currentPage = 1;
            applyFilters();
        });

        updateDownloadLink();
        applyFilters();

        // Konfirmasi hapus dengan SweetAlert
        const deleteButtons = document.querySelectorAll('.delete-article-btn');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const form = this.closest('form');
                const articleTitle = this.getAttribute('data-title');

                Swal.fire({
                    title: 'Konfirmasi Hapus',
                    text: `Apakah Anda yakin ingin menghapus artikel "${articleTitle}"?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#6c757d',
                    cancelButtonColor: '#28a745',
                    confirmButtonText: 'Hapus',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    focusCancel: true,
                    customClass: {
                        confirmButton: 'btn btn-secondary',
                        cancelButton: 'btn btn-success'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
</script>
@endsection