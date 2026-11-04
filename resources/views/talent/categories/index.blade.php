@extends('layouts.app')

@section('title', 'Talent Categories')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.celebrities.index') }}">Talent</a></li>
    <li class="breadcrumb-item active" aria-current="page">Categories</li>
@endsection

@section('content')
<div class="container-fluid px-0" style="max-width: 900px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark">Talent Categories</h4>
            <p class="text-muted small mb-0">Classify artists into domains (Actors, Singers, Influencers, Models, Sports)</p>
        </div>
        <button class="btn btn-primary rounded-pill px-3 btn-sm" data-bs-toggle="modal" data-bs-target="#newCategoryModal">
            <i class="bi bi-plus-lg me-1"></i> New Category
        </button>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Category Name</th>
                            <th>Description</th>
                            <th>Artists Enrolled</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categories as $cat)
                            <tr>
                                <td class="fw-bold text-dark">{{ $cat->name }}</td>
                                <td class="text-muted small">{{ $cat->description }}</td>
                                <td>
                                    <span class="badge bg-primary-subtle text-primary fw-semibold">{{ $cat->celebrities_count }} Artists</span>
                                </td>
                                <td>
                                    <span class="badge bg-success-subtle text-success badge-status text-capitalize">{{ $cat->status }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="newCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('admin.categories.store') }}" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Add Talent Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Category Name</label>
                    <input type="text" name="name" class="form-control" placeholder="e.g. Stand-up Comedian" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Description</label>
                    <textarea name="description" class="form-control" rows="2"></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Status</label>
                    <select name="status" class="form-select">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary px-3">Save Category</button>
            </div>
        </form>
    </div>
</div>
@endsection

