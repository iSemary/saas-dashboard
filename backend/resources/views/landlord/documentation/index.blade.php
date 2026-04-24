@extends('layouts.landlord.app')

@section('title', 'Documentation')

@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">@translate('documentation')</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">@translate('home')</a></li>
                        <li class="breadcrumb-item active">@translate('documentation')</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <!-- Documentation Sidebar -->
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-list"></i>
                                @translate('documentation_files')
                            </h3>
                        </div>
                        <div class="card-body p-0">
                            <div class="documentation-nav">
                                @if(count($files) > 0)
                                    @php
                                        $groupedFiles = [];
                                        foreach($files as $file) {
                                            if(empty($file['directory'])) {
                                                $groupedFiles['root'][] = $file;
                                            } else {
                                                $groupedFiles[$file['directory']][] = $file;
                                            }
                                        }
                                    @endphp
                                    
                                    @foreach($groupedFiles as $directory => $directoryFiles)
                                        @if($directory !== 'root')
                                            <div class="nav-group">
                                                <div class="nav-group-header">
                                                    <i class="fas fa-folder"></i>
                                                    {{ ucfirst(str_replace(['-', '_'], ' ', $directory)) }}
                                                </div>
                                                <ul class="nav-group-items">
                                                    @foreach($directoryFiles as $file)
                                                        <li class="nav-item">
                                                            <a href="#" class="nav-link documentation-link" 
                                                               data-file="{{ $file['path'] }}" 
                                                               data-name="{{ $file['display_name'] }}">
                                                                <i class="fas fa-file-alt"></i>
                                                                {{ $file['display_name'] }}
                                                            </a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @else
                                            @foreach($directoryFiles as $file)
                                                <div class="nav-item">
                                                    <a href="#" class="nav-link documentation-link" 
                                                       data-file="{{ $file['path'] }}" 
                                                       data-name="{{ $file['display_name'] }}">
                                                        <i class="fas fa-file-alt"></i>
                                                        {{ $file['display_name'] }}
                                                    </a>
                                                </div>
                                            @endforeach
                                        @endif
                                    @endforeach
                                @else
                                    <div class="text-center p-3">
                                        <i class="fas fa-folder-open text-muted" style="font-size: 2rem;"></i>
                                        <p class="text-muted mt-2">@translate('no_documentation_files_found')</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Documentation Content -->
                <div class="col-md-9">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-book-open"></i>
                                <span id="document-title">@translate('select_a_document')</span>
                            </h3>
                        </div>
                        <div class="card-body">
                            <div id="documentation-content">
                                <div class="text-center p-5">
                                    <i class="fas fa-book text-muted" style="font-size: 4rem;"></i>
                                    <h4 class="text-muted mt-3">@translate('welcome_to_documentation')</h4>
                                    <p class="text-muted">@translate('select_a_file_from_the_sidebar_to_view_its_content')</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('styles')
<style>
.documentation-nav {
    max-height: 70vh;
    overflow-y: auto;
}

.nav-group {
    margin-bottom: 1rem;
}

.nav-group-header {
    padding: 0.5rem 1rem;
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
    font-weight: 600;
    color: #495057;
    cursor: pointer;
    user-select: none;
}

.nav-group-header:hover {
    background-color: #e9ecef;
}

.nav-group-header i {
    margin-right: 0.5rem;
}

.nav-group-items {
    list-style: none;
    padding: 0;
    margin: 0;
    background-color: #fff;
}

.nav-group-items .nav-item {
    border-bottom: 1px solid #f1f3f4;
}

.nav-group-items .nav-item:last-child {
    border-bottom: none;
}

.documentation-nav .nav-link {
    display: block;
    padding: 0.75rem 1rem;
    color: #495057;
    text-decoration: none;
    border: none;
    background: none;
    width: 100%;
    text-align: left;
    transition: all 0.2s ease;
}

.documentation-nav .nav-link:hover {
    background-color: #f8f9fa;
    color: #007bff;
}

.documentation-nav .nav-link.active {
    background-color: #007bff;
    color: white;
}

.documentation-nav .nav-link i {
    margin-right: 0.5rem;
    width: 1rem;
}

#documentation-content {
    min-height: 400px;
}

#documentation-content .markdown-content {
    line-height: 1.6;
}

#documentation-content .markdown-content h1,
#documentation-content .markdown-content h2,
#documentation-content .markdown-content h3,
#documentation-content .markdown-content h4,
#documentation-content .markdown-content h5,
#documentation-content .markdown-content h6 {
    margin-top: 1.5rem;
    margin-bottom: 1rem;
    font-weight: 600;
}

#documentation-content .markdown-content h1 {
    border-bottom: 2px solid #dee2e6;
    padding-bottom: 0.5rem;
}

#documentation-content .markdown-content p {
    margin-bottom: 1rem;
}

#documentation-content .markdown-content ul,
#documentation-content .markdown-content ol {
    margin-bottom: 1rem;
    padding-left: 2rem;
}

#documentation-content .markdown-content li {
    margin-bottom: 0.5rem;
}

#documentation-content .markdown-content code {
    background-color: #f8f9fa;
    padding: 0.2rem 0.4rem;
    border-radius: 0.25rem;
    font-size: 0.875rem;
    color: #e83e8c;
}

#documentation-content .markdown-content pre {
    background-color: #f8f9fa;
    padding: 1rem;
    border-radius: 0.5rem;
    overflow-x: auto;
    margin-bottom: 1rem;
}

#documentation-content .markdown-content pre code {
    background: none;
    padding: 0;
    color: inherit;
}

#documentation-content .markdown-content blockquote {
    border-left: 4px solid #007bff;
    padding-left: 1rem;
    margin: 1rem 0;
    color: #6c757d;
    font-style: italic;
}

#documentation-content .markdown-content table {
    width: 100%;
    margin-bottom: 1rem;
    border-collapse: collapse;
}

#documentation-content .markdown-content table th,
#documentation-content .markdown-content table td {
    padding: 0.75rem;
    border: 1px solid #dee2e6;
    text-align: left;
}

#documentation-content .markdown-content table th {
    background-color: #f8f9fa;
    font-weight: 600;
}

.loading {
    text-align: center;
    padding: 2rem;
}

.loading i {
    font-size: 2rem;
    color: #007bff;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

@media (max-width: 768px) {
    .col-md-3 {
        margin-bottom: 1rem;
    }
    
    .documentation-nav {
        max-height: 300px;
    }
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/highlight.js@11.9.0/lib/highlight.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/highlight.js@11.9.0/styles/github.min.css">

<script>
$(document).ready(function() {
    // Configure marked options
    marked.setOptions({
        highlight: function(code, lang) {
            if (lang && hljs.getLanguage(lang)) {
                try {
                    return hljs.highlight(code, { language: lang }).value;
                } catch (err) {}
            }
            return hljs.highlightAuto(code).value;
        },
        breaks: true,
        gfm: true
    });

    // Handle documentation link clicks
    $('.documentation-link').on('click', function(e) {
        e.preventDefault();
        
        const file = $(this).data('file');
        const name = $(this).data('name');
        
        // Update active link
        $('.documentation-link').removeClass('active');
        $(this).addClass('active');
        
        // Update title
        $('#document-title').text(name);
        
        // Show loading
        $('#documentation-content').html(`
            <div class="loading">
                <i class="fas fa-spinner"></i>
                <p>@translate('loading_documentation')...</p>
            </div>
        `);
        
        // Fetch content via AJAX
        $.ajax({
            url: '{{ route("landlord.documentation.get-content") }}',
            method: 'POST',
            data: {
                file: file,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    // Convert markdown to HTML
                    const htmlContent = marked.parse(response.content);
                    
                    // Display content
                    $('#documentation-content').html(`
                        <div class="markdown-content">
                            ${htmlContent}
                        </div>
                    `);
                } else {
                    $('#documentation-content').html(`
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i>
                            @translate('error_loading_documentation'): ${response.message}
                        </div>
                    `);
                }
            },
            error: function(xhr) {
                let errorMessage = '@translate('error_loading_documentation')';
                
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                $('#documentation-content').html(`
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                        ${errorMessage}
                    </div>
                `);
            }
        });
    });

    // Auto-expand first file if available
    const firstLink = $('.documentation-link').first();
    if (firstLink.length > 0) {
        firstLink.trigger('click');
    }
});
</script>
@endpush
