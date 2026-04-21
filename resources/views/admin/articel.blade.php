@extends('admin.layouts.app', [
    'activePage' => 'articel',
    'navbarTitle' => 'Manajemen Artikel',
    'navbarSubtitle' => 'Admin'
])

@section('title', 'Manajemen Artikel')

@section('styles')
<style>
    .category-card {
        border: none;
        border-radius: 20px;
        padding: 24px;
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        position: relative;
        overflow: hidden;
        z-index: 1;
        background: #fff;
        box-shadow: 0 10px 30px rgba(0,0,0,0.02);
    }
    
    .category-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 45px rgba(0,0,0,0.08);
    }

    .category-icon-wrapper {
        width: 56px;
        height: 56px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 20px;
        font-size: 1.5rem;
    }

    .category-card .bg-decoration {
        position: absolute;
        top: -20px;
        right: -20px;
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background: currentColor;
        opacity: 0.05;
        z-index: -1;
    }

    .status-pill-category {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.02em;
    }

    /* Category Themes */
    .theme-pernikahan { border-top: 4px solid #fb7185; }
    .theme-pernikahan .category-icon-wrapper { background: #fff1f2; color: #e11d48; }
    
    .theme-kekerasan { border-top: 4px solid #f87171; }
    .theme-kekerasan .category-icon-wrapper { background: #fef2f2; color: #dc2626; }
    
    .theme-bullying { border-top: 4px solid #FFCB05; }
    .theme-bullying .category-icon-wrapper { background: #FFFAE6; color: #E6B800; }
    
    .theme-stunting { border-top: 4px solid #10b981; }
    .theme-stunting .category-icon-wrapper { background: #ecfdf5; color: #059669; }
</style>
@endsection

@section('content')
<!-- Bagian Kartu Kategori Konten -->
<div class="mb-5">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-0">Eksplorasi Kategori</h4>
            <p class="text-muted small mb-0">Distribusi konten edukasi berdasarkan topik</p>
        </div>
    </div>
    <div class="row g-4">
        <div class="col-md-3">
            <div class="category-card theme-pernikahan">
                <div class="bg-decoration"></div>
                <div class="category-icon-wrapper shadow-sm">
                    <i class="bi bi-heart-fill"></i>
                </div>
                <h6 class="text-muted small fw-bold text-uppercase mb-1">Pernikahan Anak</h6>
                <div class="d-flex align-items-end gap-2">
                    <h2 class="fw-bold mb-0 text-dark">{{ count(array_filter($articles, function($article) { return $article['articleType'] == 'pernikahan dini'; })) }}</h2>
                    <span class="text-muted small mb-1">Artikel</span>
                </div>
                <div class="mt-3">
                    <span class="status-pill-category theme-pernikahan px-0" style="background: transparent; border: none;">Edukasi Dini</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="category-card theme-kekerasan">
                <div class="bg-decoration"></div>
                <div class="category-icon-wrapper shadow-sm">
                    <i class="bi bi-shield-lock-fill"></i>
                </div>
                <h6 class="text-muted small fw-bold text-uppercase mb-1">Kekerasan Anak</h6>
                <div class="d-flex align-items-end gap-2">
                    <h2 class="fw-bold mb-0 text-dark">{{ count(array_filter($articles, function($article) { return $article['articleType'] == 'kekerasan anak'; })) }}</h2>
                    <span class="text-muted small mb-1">Artikel</span>
                </div>
                <div class="mt-3">
                    <span class="status-pill-category theme-kekerasan px-0" style="background: transparent; border: none;">Perlindungan</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="category-card theme-bullying">
                <div class="bg-decoration"></div>
                <div class="category-icon-wrapper shadow-sm">
                    <i class="bi bi-chat-heart-fill"></i>
                </div>
                <h6 class="text-muted small fw-bold text-uppercase mb-1">Bullying</h6>
                <div class="d-flex align-items-end gap-2">
                    <h2 class="fw-bold mb-0 text-dark">{{ count(array_filter($articles, function($article) { return $article['articleType'] == 'bullying'; })) }}</h2>
                    <span class="text-muted small mb-1">Artikel</span>
                </div>
                <div class="mt-3">
                    <span class="status-pill-category theme-bullying px-0" style="background: transparent; border: none;">Anti-Bullying</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="category-card theme-stunting">
                <div class="bg-decoration"></div>
                <div class="category-icon-wrapper shadow-sm">
                    <i class="bi bi-graph-up-arrow"></i>
                </div>
                <h6 class="text-muted small fw-bold text-uppercase mb-1">Stunting</h6>
                <div class="d-flex align-items-end gap-2">
                    <h2 class="fw-bold mb-0 text-dark">{{ count(array_filter($articles, function($article) { return $article['articleType'] == 'stunting'; })) }}</h2>
                    <span class="text-muted small mb-1">Artikel</span>
                </div>
                <div class="mt-3">
                    <span class="status-pill-category theme-stunting px-0" style="background: transparent; border: none;">Kesehatan</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bagian Daftar Artikel -->
<div class="glass-card overflow-hidden mb-5">
    <div class="p-4 d-flex justify-content-between align-items-center flex-wrap gap-3 border-bottom bg-white bg-opacity-50">
        <div>
            <h4 class="fw-bold mb-0">Daftar Artikel</h4>
            <p class="text-muted small mb-0">Kelola konten edukasi untuk pengguna</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.articel.refresh') }}" class="btn btn-light shadow-sm" style="border-radius: 10px;" title="Refresh Data">
                <i class="bi bi-arrow-clockwise" style="color: #FFCB05;"></i>
            </a>
            <a href="{{ route('admin.articel.downloadList') }}" id="downloadListBtn" class="btn btn-light shadow-sm" style="border-radius: 10px; border: 1px solid rgba(255, 203, 5, 0.2); color: #B45309;">
                <i class="bi bi-download me-1"></i>Ekspor CSV
            </a>
            <button type="button" id="bulkDeleteBtn" class="btn btn-danger shadow-sm d-none" style="border-radius: 10px;">
                <i class="bi bi-trash me-1"></i>Hapus (<span id="selectedCount">0</span>)
            </button>
            <a href="{{ route('admin.articel.create') }}" class="btn btn-primary shadow-sm px-4 border-0" style="border-radius: 10px; background: linear-gradient(135deg, #FFCB05 0%, #E6B800 100%); color: #333;">
                <i class="bi bi-plus-lg me-1"></i>Tambah Artikel
            </a>
        </div>
    </div>
    <div class="p-4">
        <div class="row g-3 mb-4">
            <div class="col-md-8">
                <div class="input-group shadow-sm" style="border-radius: 12px; overflow: hidden;">
                    <span class="input-group-text border-0 bg-white ps-3"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" class="form-control border-0 py-2" placeholder="Cari judul atau isi artikel..." id="search-input">
                </div>
            </div>
            <div class="col-md-4">
                <select class="form-select border-0 shadow-sm py-2" style="border-radius: 12px;" id="category-filter">
                    <option selected value="all">Semua Kategori</option>
                    <option value="pernikahan dini">Pernikahan Anak</option>
                    <option value="kekerasan anak">Kekerasan Anak</option>
                    <option value="bullying">Bullying</option>
                    <option value="stunting">Stunting</option>
                </select>
            </div>
        </div>

        <!-- Tata Letak Tabel Untuk Artikel -->
        <div class="table-responsive">
            <table class="table table-hover align-middle" id="articles-table">
                <thead>
                    <tr>
                        <th class="ps-4" style="width: 50px;">
                            <input type="checkbox" id="selectAll" class="form-check-input">
                        </th>
                        <th>Judul Artikel</th>
                        <th>Kategori</th>
                        <th>Rilis</th>
                        <th>Update Terakhir</th>
                        <th class="pe-4 text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($articles as $article)
                        <tr class="article-row"
                        data-kategori="{{ $article['articleType'] }}"
                        data-description="{{ strtolower($article['description'] ?? '') }}">
                            <td class="ps-4">
                                <input type="checkbox" class="form-check-input article-checkbox" value="{{ $article['id'] }}">
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $article['title'] }}</div>
                                <div class="text-muted small text-truncate" style="max-width: 250px;">{{ strip_tags($article['description']) }}</div>
                            </td>
                            <td>
                                @php
                                    $pillClass = match($article['articleType']) {
                                        'pernikahan dini' => 'theme-pernikahan',
                                        'kekerasan anak' => 'theme-kekerasan',
                                        'bullying' => 'theme-bullying',
                                        'stunting' => 'theme-stunting',
                                        default => 'bg-light text-dark border',
                                    };
                                    $displayText = match($article['articleType']) {
                                        'pernikahan dini' => 'Pernikahan Anak',
                                        'kekerasan anak' => 'Kekerasan Anak',
                                        'bullying' => 'Bullying',
                                        'stunting' => 'Stunting',
                                        default => ucfirst($article['articleType']),
                                    };
                                @endphp
                                <span class="status-pill-category {{ $pillClass }}" style="border-radius: 8px; font-size: 0.65rem;">{{ $displayText }}</span>
                            </td>
                            <td>
                                <span class="text-muted small"><i class="bi bi-calendar3 me-1"></i>
                                @if(isset($article['releasedDate']))
                                    @php
                                        $val = $article['releasedDate'];
                                        if ($val instanceof \Google\Cloud\Core\Timestamp) {
                                            $relDate = \Carbon\Carbon::instance($val->get());
                                        } elseif (is_string($val)) {
                                            $relDate = \Carbon\Carbon::parse($val);
                                        } else {
                                            $relDate = \Carbon\Carbon::instance($val);
                                        }
                                    @endphp
                                    {{ $relDate->format('d/m/y') }}
                                @else - @endif
                                </span>
                            </td>
                            <td>
                                @if(isset($article['updateDate']))
                                    @php
                                        $valUpd = $article['updateDate'];
                                        if ($valUpd instanceof \Google\Cloud\Core\Timestamp) {
                                            $updDate = \Carbon\Carbon::instance($valUpd->get());
                                        } elseif (is_string($valUpd)) {
                                            $updDate = \Carbon\Carbon::parse($valUpd);
                                        } else {
                                            $updDate = \Carbon\Carbon::instance($valUpd);
                                        }
                                    @endphp
                                    <div class="text-primary small fw-semibold">
                                        <i class="bi bi-clock-history me-1"></i> {{ $updDate->diffForHumans() }}
                                    </div>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td class="pe-4 text-end">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('admin.articel.edit', $article['id']) }}" class="btn btn-light btn-sm shadow-sm border" style="border-radius: 8px;" title="Edit">
                                        <i class="bi bi-pencil-fill text-primary"></i>
                                    </a>
                                    <form action="{{ route('admin.articel.destroy', $article['id']) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-light btn-sm shadow-sm border delete-article-btn" data-title="{{ $article['title'] }}" style="border-radius: 8px;" title="Hapus">
                                            <i class="bi bi-trash-fill text-danger"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-journal-x fs-1 d-block mb-2 opacity-25"></i>
                                Tidak ada artikel yang tersedia.
                            </td>
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
                    confirmButtonColor: '#FFCB05',
                    cancelButtonColor: '#f3f4f6',
                    confirmButtonText: 'Hapus',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    focusCancel: true,
                    customClass: {
                        confirmButton: 'btn btn-dark text-white',
                        cancelButton: 'btn btn-light border'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
        
        // --- LOGIKA BULK DELETE ---
        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.article-checkbox');
        const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
        const selectedCountSpan = document.getElementById('selectedCount');

        function updateBulkDeleteUI() {
            const checkedCount = document.querySelectorAll('.article-checkbox:checked').length;
            selectedCountSpan.textContent = checkedCount;
            if (checkedCount > 0) {
                bulkDeleteBtn.classList.remove('d-none');
            } else {
                bulkDeleteBtn.classList.add('d-none');
            }
            // Sync selectAll checkbox
            selectAll.checked = (checkedCount === checkboxes.length && checkboxes.length > 0);
        }

        selectAll.addEventListener('change', function() {
            checkboxes.forEach(cb => {
                // Hanya centang yang terlihat (kalau ada filter)
                if (cb.closest('tr').style.display !== 'none') {
                    cb.checked = selectAll.checked;
                }
            });
            updateBulkDeleteUI();
        });

        checkboxes.forEach(cb => {
            cb.addEventListener('change', updateBulkDeleteUI);
        });

        bulkDeleteBtn.addEventListener('click', function() {
            const selectedIds = Array.from(document.querySelectorAll('.article-checkbox:checked')).map(cb => cb.value);
            
            Swal.fire({
                title: 'Hapus Massal?',
                text: `Anda akan menghapus ${selectedIds.length} artikel sekaligus. Tindakan ini tidak bisa dibatalkan!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus Semua!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Buat form dinamis untuk submit
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ route("admin.articel.bulkDelete") }}';
                    
                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';
                    form.appendChild(csrfToken);

                    selectedIds.forEach(id => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'ids[]';
                        input.value = id;
                        form.appendChild(input);
                    });

                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });
    });
</script>
@endsection