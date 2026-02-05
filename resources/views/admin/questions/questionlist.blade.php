@extends('layouts.admin')

@section('title','Questions')

@section('content')

<div class="pagetitle">
    <h1>Questions</h1>
    <p class="text-muted">Manage all questions</p>
</div>

<section class="section dashboard">

{{-- =========================
    FILTER CARD (CI STYLE)
========================= --}}
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">

                <h5 class="card-title">Filter</h5>
                <hr>

                <form method="GET">
                    <div class="row g-3">

                        <div class="col-md-3">
                            <label class="form-label">Question Title</label>
                            <input type="text"
                                   name="q"
                                   value="{{ request('q') }}"
                                   class="form-control form-control-sm"
                                   placeholder="Search question">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Category</label>
                            <select name="category_id"
                                    class="form-select form-select-sm">
                                <option value="">All Categories</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}"
                                        @selected(request('category_id')==$cat->id)>
                                        {{ str_repeat('— ', $cat->level) }} {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Status</label>
                            <select name="status"
                                    class="form-select form-select-sm">
                                <option value="">All</option>
                                <option value="review" @selected(request('status')=='review')>
                                    In Review
                                </option>
                                <option value="published" @selected(request('status')=='published')>
                                    Published
                                </option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <button class="btn btn-primary btn-sm w-100">
                                Filter
                            </button>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <a href="{{ route('admin.questions.index') }}"
                               class="btn btn-danger btn-sm w-100">
                                Reset
                            </a>
                        </div>

                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

{{-- =========================
    TABLE + BULK ACTION CARD
========================= --}}
<form method="POST" action="{{ route('admin.questions.bulk') }}">
@csrf

<div class="row">
    <div class="col-12">
        <div class="card recent-sales overflow-auto">

            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title mb-0">
                        Questions List
                        <span class="badge bg-secondary ms-2">
                            {{ $questions->total() }}
                        </span>
                    </h5>

                    <a href="{{ route('admin.questions.create') }}"
                       class="btn btn-success btn-sm">
                        + New Question
                    </a>
                </div>

                {{-- BULK ACTION BAR --}}
                <div class="row mb-3">
                    <div class="col-md-3">
                        <select name="action"
                                class="form-select form-select-sm"
                                required>
                            <option value="">Bulk Action</option>
                            <option value="publish">Publish</option>
                            <option value="review">Move to Review</option>
                            <option value="delete">Delete</option>
                            @if(request('status') === 'review')

    <option value="">Bulk Action</option>
    <option value="approve">Approve</option>
    <option value="reject">Reject</option>

@endif
                        </select>
                        
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary btn-sm">
                            Apply
                        </button>
                    </div>
                </div>

                {{-- TABLE --}}
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-light">
                        <tr>
                            <th width="30">
                                <input type="checkbox" id="checkAll">
                            </th>
                            <th>Title</th>
                            <th width="200">Category</th>
                            <th width="120">Status</th>
                            <th width="160">Created</th>
                            <th width="100">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($questions as $q)
                        <tr>
                            <td>
                                <input type="checkbox"
                                       name="ids[]"
                                       value="{{ $q->id }}">
                            </td>

                            <td>
                                <div class="fw-semibold">
                                    {{ $q->title }}
                                </div>
                                <small class="text-muted">
                                    {{ Str::limit(strip_tags($q->body), 60) }}
                                </small>
                            </td>

                            <td>
                                {{ $q->category->name ?? '-' }}
                            </td>

                            <td>
                                <span class="badge rounded-pill bg-{{ match($q->status) {
    'review'    => 'warning',
    'approved'  => 'info',
    'published' => 'success',
    'rejected'  => 'danger',
    default     => 'secondary'
} }}">
    {{ ucfirst($q->status) }}
</span>
@if($q->isSlaBreached())
    <span class="badge bg-danger ms-2">
        SLA 24h+
    </span>
@endif

                            </td>

                            <td>
                                {{ $q->created_at->diffForHumans() }}
                            </td>

                           <td class="text-nowrap">

    {{-- EDIT (allowed states only) --}}
    @if(in_array($q->status, ['draft','review','rejected']))
        <a href="{{ route('admin.questions.edit',$q->id) }}"
           class="btn btn-sm btn-outline-primary">
            Edit
        </a>
    @endif

    {{-- SEO PREVIEW --}}
    <a href="{{ url('/preview/question/'.$q->slug) }}"
       target="_blank"
       class="btn btn-sm btn-outline-secondary">
        SEO Preview
    </a>

    {{-- REVIEW ACTIONS --}}
    @if($q->status === 'review')
        <form method="POST"
              action="{{ route('admin.questions.approve',$q) }}"
              class="d-inline">
            @csrf
            <button class="btn btn-sm btn-success">
                Approve
            </button>
        </form>

        <button class="btn btn-sm btn-danger"
                data-bs-toggle="modal"
                data-bs-target="#rejectModal{{ $q->id }}">
            Reject
        </button>
    @endif

    {{-- SUPER ADMIN PUBLISH --}}
    @if(auth()->user()->isSuperAdmin() && $q->status === 'approved')
        <form method="POST"
              action="{{ route('admin.questions.bulk') }}"
              class="d-inline">
            @csrf
            <input type="hidden" name="action" value="publish">
            <input type="hidden" name="ids[]" value="{{ $q->id }}">
            <button class="btn btn-sm btn-success">
                Publish
            </button>
        </form>
    @endif

</td>

@if($q->status === 'review')
<div class="modal fade" id="rejectModal{{ $q->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST"
              action="{{ route('admin.questions.reject',$q) }}">
            @csrf

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        Reject Question
                    </h5>
                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <p class="small text-muted mb-2">
                        <strong>{{ $q->title }}</strong>
                    </p>

                    <textarea name="reason"
                              class="form-control"
                              rows="3"
                              required
                              placeholder="Reason for rejection"></textarea>
                </div>

                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">
                        Cancel
                    </button>

                    <button class="btn btn-danger">
                        Reject
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endif



                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">
                                No questions found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                {{-- PAGINATION --}}
                <div class="mt-3">
                    {{ $questions->links() }}
                </div>

            </div>
        </div>
    </div>
</div>

</form>

</section>


{{-- =========================
    JS
========================= --}}
<script>
document.getElementById('checkAll').addEventListener('click', function(){
    document.querySelectorAll('input[name="ids[]"]').forEach(cb => {
        cb.checked = this.checked;
    });
});
</script>

@endsection
