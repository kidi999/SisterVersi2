<!-- File Upload Component -->
<div class="card" id="fileUploadSection">
    <div class="card-header bg-light">
        <h6 class="mb-0"><i class="bi bi-paperclip"></i> Dokumen Pendukung</h6>
    </div>
    <div class="card-body">
        <div class="alert alert-info alert-sm">
            <i class="bi bi-info-circle"></i> 
            <small>
                {{ $helperText ?? 'Anda dapat mengupload hingga ' . ($maxFiles ?? 10) . ' file. Format yang didukung berdasarkan pengaturan. Ukuran maksimal per file: 3 MB' }}
            </small>
        </div>

        <!-- Upload Button -->
        <div class="mb-3">
            <button type="button" class="btn btn-outline-primary btn-sm" id="btnAddFile">
                <i class="bi bi-plus-circle"></i> Tambah File
            </button>
        </div>

        <!-- File List -->
        <div id="fileList" class="row g-2">
            <!-- Files will be added here dynamically -->
        </div>

        <!-- Hidden inputs to store file IDs as array -->
        <div id="fileIdsContainer">
            <!-- File IDs will be added here as multiple hidden inputs -->
        </div>
    </div>
</div>

<!-- File Input Template (Hidden) -->
<input type="file" id="fileInput" class="d-none" accept="{{ $accept ?? '.pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.gif' }}">

@push('scripts')
<script>
$(document).ready(function() {
    console.log('=== File Upload Component Initialized ===');
    let uploadedFiles = [];
    const maxFileSize = 3 * 1024 * 1024; // 3MB in bytes
    const maxFiles = {{ $maxFiles ?? 10 }};
    const canDelete = {!! json_encode(auth()->check()) !!};
    const fileableType = {!! json_encode($fileableType ?? 'App\\Models\\TahunAkademik') !!};
    const fileableId = {{ $fileableId ?? 'null' }};
    
    console.log('Config:', { maxFileSize, maxFiles, fileableType, fileableId });

    // Add file button click
    $('#btnAddFile').click(function() {
        console.log('Upload button clicked');
        $('#fileInput').click();
    });

    // File input change
    $('#fileInput').change(function() {
        const file = this.files[0];
        if (file) {
            // Validate file size
            if (file.size > maxFileSize) {
                alert('Ukuran file terlalu besar! Maksimal 3 MB.');
                $(this).val('');
                return;
            }

            // Upload file
            uploadFile(file);
        }
    });

    function uploadFile(file) {
        const formData = new FormData();
        formData.append('file', file);
        formData.append('fileable_type', fileableType);
        if (fileableId !== null) {
            formData.append('fileable_id', fileableId);
        }
        formData.append('category', 'general');

        // Show loading
        const loadingHtml = `
            <div class="col-12" id="uploading">
                <div class="alert alert-info">
                    <i class="bi bi-hourglass-split"></i> Mengupload <strong>${file.name}</strong>...
                </div>
            </div>
        `;
        $('#fileList').append(loadingHtml);

        $.ajax({
            url: '/api/file-upload',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('#uploading').remove();
                if (response.success) {
                    console.log('File uploaded successfully:', response.file);
                    uploadedFiles.push(response.file.id);
                    console.log('Current uploaded files:', uploadedFiles);
                    updateFileIds();
                    addFileToList(response.file);
                    $('#fileInput').val('');
                }
            },
            error: function(xhr) {
                $('#uploading').remove();
                const message = xhr.responseJSON?.message || 'Gagal mengupload file';
                alert(message);
                $('#fileInput').val('');
            }
        });
    }

    function addFileToList(file) {
        const deleteButtonHtml = canDelete
            ? `<button type="button" class="btn btn-sm btn-danger" onclick="removeFile(${file.id})">
                    <i class="bi bi-trash"></i>
               </button>`
            : '';

        const fileHtml = `
            <div class="col-md-6" id="file-${file.id}">
                <div class="card">
                    <div class="card-body p-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center flex-grow-1">
                                <i class="bi ${file.icon} fs-4 me-2"></i>
                                <div class="flex-grow-1 text-truncate">
                                    <small class="d-block text-truncate" title="${file.name}">
                                        <strong>${file.name}</strong>
                                    </small>
                                    <small class="text-muted">${file.size}</small>
                                </div>
                            </div>
                            ${deleteButtonHtml}
                        </div>
                    </div>
                </div>
            </div>
        `;
        $('#fileList').append(fileHtml);
    }

    window.removeFile = function(fileId) {
        if (!canDelete) return;
        if (!confirm('Hapus file ini?')) return;

        $.ajax({
            url: `/api/file-upload/${fileId}`,
            type: 'DELETE',
            success: function(response) {
                if (response.success) {
                    console.log('File deleted:', fileId);
                    $(`#file-${fileId}`).remove();
                    uploadedFiles = uploadedFiles.filter(id => id !== fileId);
                    console.log('Remaining files:', uploadedFiles);
                    updateFileIds();
                }
            },
            error: function(xhr) {
                alert('Gagal menghapus file');
            }
        });
    };

    function updateFileIds() {
        // Clear existing inputs
        $('#fileIdsContainer').empty();
        
        // Add each file ID as separate hidden input to create array
        uploadedFiles.forEach(function(fileId) {
            $('#fileIdsContainer').append('<input type="hidden" name="file_ids[]" value="' + fileId + '">');
        });
        
        console.log('File IDs updated:', uploadedFiles);
    }

    // Load existing files (for edit mode)
    @if(isset($existingFiles) && $existingFiles && $existingFiles->count() > 0)
        console.log('Loading existing files...');
        @foreach($existingFiles as $file)
            console.log('Adding file: {{ $file->id }} - {{ addslashes($file->file_name) }}');
            uploadedFiles.push({{ $file->id }});
            addFileToList({
                id: {{ $file->id }},
                name: '{{ addslashes($file->file_name) }}',
                size: '{{ $file->formatted_size }}',
                icon: '{{ $file->icon_class }}',
                is_image: {{ $file->is_image ? 'true' : 'false' }},
                url: '{{ asset("storage/" . $file->file_path) }}'
            });
        @endforeach
        updateFileIds();
        console.log('Total files loaded:', uploadedFiles.length);
    @else
        console.log('No existing files to load');
    @endif
});
</script>
@endpush
