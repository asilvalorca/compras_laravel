<!-- resources/views/partials/app-header.blade.php -->
<div class="app-content-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-6">
                <h3 class="mb-0">{{ $title }}</h3>  <!-- Título dinámico -->
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">
                        {{ $breadcrumb }}
                    </li>
                </ol>
            </div>
        </div>
    </div>
</div>
