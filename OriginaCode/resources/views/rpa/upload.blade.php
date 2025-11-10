<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>RPA - Excel File Upload</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px 0;
        }
        .upload-container {
            max-width: 900px;
            margin: 0 auto;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            padding: 20px;
        }
        .drop-zone {
            border: 3px dashed #667eea;
            border-radius: 10px;
            padding: 50px;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
            background: #f8f9fa;
        }
        .drop-zone:hover {
            background: #e9ecef;
            border-color: #764ba2;
        }
        .drop-zone.dragover {
            background: #d1e7ff;
            border-color: #0056b3;
        }
        .file-icon {
            font-size: 64px;
            color: #667eea;
            margin-bottom: 20px;
        }
        .btn-upload {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px 40px;
            font-weight: bold;
        }
        .btn-upload:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .file-table {
            margin-top: 30px;
        }
        .file-row:hover {
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>
    <div class="upload-container">
        <div class="card">
            <div class="card-header">
                <h3 class="mb-0"><i class="fas fa-file-excel me-2"></i>RPA - Excel File Upload</h3>
                <p class="mb-0 mt-2">Upload and manage your Excel files</p>
            </div>
            <div class="card-body p-4">
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                <form action="{{ route('rpa.upload') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                    @csrf
                    <div class="drop-zone" id="dropZone">
                        <div class="file-icon">
                            <i class="fas fa-cloud-upload-alt"></i>
                        </div>
                        <h5>Drag & Drop your Excel file here</h5>
                        <p class="text-muted">or click to browse</p>
                        <input type="file" name="excel_file" id="fileInput" class="d-none" accept=".xlsx,.xls,.csv">
                        <p class="text-muted small mt-3">Supported formats: .xlsx, .xls, .csv (Max: 10MB)</p>
                    </div>
                    <div id="fileInfo" class="mt-3 d-none">
                        <div class="alert alert-info">
                            <i class="fas fa-file me-2"></i><span id="fileName"></span>
                            <span class="badge bg-primary ms-2" id="fileSize"></span>
                        </div>
                    </div>
                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary btn-upload" id="uploadBtn" disabled>
                            <i class="fas fa-upload me-2"></i>Upload File
                        </button>
                    </div>
                </form>

                @if(count($files) > 0)
                <div class="file-table">
                    <h5 class="mb-3"><i class="fas fa-folder-open me-2"></i>Uploaded Files</h5>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th><i class="fas fa-file me-2"></i>File Name</th>
                                    <th><i class="fas fa-calendar me-2"></i>Upload Date</th>
                                    <th><i class="fas fa-hdd me-2"></i>Size</th>
                                    <th class="text-end"><i class="fas fa-cog me-2"></i>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($files as $file)
                                <tr class="file-row">
                                    <td>
                                        <i class="fas fa-file-excel text-success me-2"></i>
                                        {{ $file['name'] }}
                                    </td>
                                    <td>{{ $file['date'] }}</td>
                                    <td>{{ number_format($file['size'] / 1024, 2) }} KB</td>
                                    <td class="text-end">
                                        <a href="{{ route('rpa.download', $file['name']) }}" class="btn btn-sm btn-success" title="Download">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        <form action="{{ route('rpa.delete', $file['name']) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this file?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const dropZone = document.getElementById('dropZone');
        const fileInput = document.getElementById('fileInput');
        const fileInfo = document.getElementById('fileInfo');
        const fileName = document.getElementById('fileName');
        const fileSize = document.getElementById('fileSize');
        const uploadBtn = document.getElementById('uploadBtn');

        dropZone.addEventListener('click', () => fileInput.click());

        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.classList.add('dragover');
        });

        dropZone.addEventListener('dragleave', () => {
            dropZone.classList.remove('dragover');
        });

        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.classList.remove('dragover');
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                displayFileInfo(files[0]);
            }
        });

        fileInput.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                displayFileInfo(e.target.files[0]);
            }
        });

        function displayFileInfo(file) {
            fileName.textContent = file.name;
            fileSize.textContent = (file.size / 1024).toFixed(2) + ' KB';
            fileInfo.classList.remove('d-none');
            uploadBtn.disabled = false;
        }
    </script>
</body>
</html>
