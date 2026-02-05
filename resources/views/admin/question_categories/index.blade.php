@extends('layouts.admin')

@section('title','Question Categories')

@section('content')

<div class="pagetitle d-flex justify-content-between align-items-center">
    <div>
        <h1>Question Categories</h1>
        <p class="text-muted mb-0">
            Manage category hierarchy (Super → Sub → Child)
        </p>
    </div>

    @if($mode === 'list')
        <a href="{{ route('admin.categories.create') }}"
           class="btn btn-primary btn-sm">
            + Add Category
        </a>
    @else
        <a href="{{ route('admin.categories.index') }}"
           class="btn btn-secondary btn-sm">
            ← Back to List
        </a>
    @endif
</div>

<section class="section dashboard mt-3">

{{-- =========================
    LIST MODE
========================= --}}
@if($mode === 'list')

{{-- FILTER (future-ready, CI style) --}}
<div class="card mb-4">
    <div class="card-body">
        <h5 class="card-title">Filter</h5>
        <hr>

        <form method="GET">
            <div class="row g-3">

                <div class="col-md-4">
                    <label class="form-label">Category Name</label>
                    <input type="text"
                           name="q"
                           value="{{ request('q') }}"
                           class="form-control form-control-sm"
                           placeholder="Search category">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="is_active"
                            class="form-select form-select-sm">
                        <option value="">All</option>
                        <option value="1" @selected(request('is_active')==='1')>Active</option>
                        <option value="0" @selected(request('is_active')==='0')>Disabled</option>
                    </select>
                </div>

                <div class="col-md-2 align-self-end">
                    <button class="btn btn-primary btn-sm w-100">
                        Filter
                    </button>
                </div>

                <div class="col-md-2 align-self-end">
                    <a href="{{ route('admin.categories.index') }}"
                       class="btn btn-danger btn-sm w-100">
                        Reset
                    </a>
                </div>

            </div>
        </form>
    </div>
</div>

{{-- TABLE CARD --}}
<div class="card recent-sales overflow-auto">
    <div class="card-body">

        <h5 class="card-title">
            Category List
            <span class="badge bg-secondary ms-2">
                {{ count($categories) }}
            </span>
        </h5>

        <table class="table table-bordered table-striped align-middle">
            <thead class="table-light">
                <tr>
                    <th>Category Name</th>
                    <th width="180">Parent</th>
                    <th width="80">Level</th>
                    <th width="120">Status</th>
                    <th width="160">Action</th>
                </tr>
            </thead>
            <tbody>

                @forelse($categories as $cat)
                <tr>
                    <td>
                        <span class="fw-semibold">
                            {!! str_repeat('— ', $cat->level) !!}
                            {{ $cat->name }}
                        </span>
                    </td>

                    <td>
                        {{ $cat->parent?->name ?? '—' }}
                    </td>

                    <td>
                        <span class="badge bg-info">
                            L{{ $cat->level }}
                        </span>
                    </td>

                    <td>
                        <span class="badge bg-{{ $cat->is_active ? 'success':'secondary' }}">
                            {{ $cat->is_active ? 'Active':'Disabled' }}
                        </span>
                    </td>

                    <td>
                        <a href="{{ route('admin.categories.edit',$cat) }}"
                           class="btn btn-sm btn-warning">
                            Edit
                        </a>

                        <form method="POST"
                              action="{{ route('admin.categories.destroy',$cat) }}"
                              class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger"
                                onclick="return confirm('Delete this category?')">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted">
                        No categories found
                    </td>
                </tr>
                @endforelse

            </tbody>
        </table>

    </div>
</div>

@endif

{{-- =========================
    CREATE / EDIT MODE
========================= --}}
@if(in_array($mode,['create','edit']))

<form method="POST"
      action="{{ $mode === 'edit'
        ? route('admin.categories.update',$category)
        : route('admin.categories.store') }}">

@csrf
@if($mode === 'edit') @method('PUT') @endif

<div class="card mt-4">
<div class="card-body">

    <h5 class="card-title">
        {{ $mode === 'edit' ? 'Edit Category' : 'Create Category' }}
    </h5>
    <hr>

    <div class="row">

        <div class="col-md-6 mb-3">
            <label class="form-label">Category Name</label>
            <input name="name"
                   class="form-control"
                   required
                   value="{{ old('name',$category->name ?? '') }}">
        </div>

        <div class="col-md-6 mb-3">
            <label class="form-label">Parent Category</label>
            <select name="parent_id" class="form-select">
                <option value="">— Super Category —</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}"
                        @selected(($category->parent_id ?? '') == $cat->id)>
                        {!! str_repeat('— ', $cat->level) !!} {{ $cat->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-6 mb-3">
            <label class="form-label">SEO Title</label>
            <input name="seo_title"
                   class="form-control"
                   value="{{ old('seo_title',$category->seo_title ?? '') }}">
        </div>

        <div class="col-md-6 mb-3">
            <label class="form-label">Sort Order</label>
            <input type="number"
                   name="sort_order"
                   class="form-control"
                   value="{{ old('sort_order',$category->sort_order ?? 0) }}">
        </div>

        <div class="col-md-12 mb-3">
            <label class="form-label">SEO Description</label>
            <textarea name="seo_description"
                      rows="3"
                      class="form-control">{{ old('seo_description',$category->seo_description ?? '') }}</textarea>
        </div>

        <div class="col-md-12 mb-3">
            <div class="form-check">
                <input type="checkbox"
                       name="is_active"
                       class="form-check-input"
                       id="activeCheck"
                       @checked(old('is_active',$category->is_active ?? true))>
                <label class="form-check-label" for="activeCheck">
                    Active
                </label>
            </div>
        </div>

    </div>

    <div class="text-end">
        <button class="btn btn-primary">
            {{ $mode === 'edit' ? 'Update Category' : 'Create Category' }}
        </button>
    </div>

</div>
</div>

</form>

@endif

</section>

@endsection
