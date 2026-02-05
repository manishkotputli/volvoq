@extends('layouts.admin')

@section('title','Question Editor')

@section('content')

<div class="pagetitle">
    <h1>
        {{ $mode === 'edit' ? 'Edit Question' : 'Create Question' }}
    </h1>
    <p class="text-muted">
        Question will automatically go to <b>Review</b> after save
    </p>
</div>

<form method="POST"
      action="{{ $mode === 'edit'
        ? route('admin.questions.update',$question->id)
        : route('admin.questions.store') }}">
@if ($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

@csrf
@if($mode === 'edit') @method('PUT') @endif

{{-- =========================
    QUESTION DETAILS
========================= --}}
<div class="card mb-4">
<div class="card-body">

<h5 class="card-title">Question Details</h5>

<div class="mb-3">
    <label class="fw-bold">Question Title</label>
    <input type="text"
           name="title"
           class="form-control"
           required
           value="{{ old('title',$question->title) }}">
</div>

<div class="mb-3">
    <label class="fw-bold">Category</label>
   <select name="category_id" class="form-select" required>
    <option value="">Select Category</option>

    @foreach($categories as $cat)
        <option value="{{ $cat->id }}"
            @selected($question->category_id == $cat->id)>
            {{ str_repeat('│   ', $cat->level) }}
            {{ $cat->level > 0 ? '└─ ' : '' }}
            {{ $cat->name }}
        </option>
    @endforeach
</select>

</div>

<div class="mb-3">
    <label class="fw-bold">Question Description</label>
    <textarea name="body"
              rows="4"
              class="form-control"
              id="question-editor"
              placeholder="Explain the problem clearly...">{{ old('body',$question->body) }}</textarea>
</div>


</div>
</div>

{{-- =========================
    ANSWERS BUILDER
========================= --}}
<div class="card">
<div class="card-body">

<div class="d-flex justify-content-between align-items-center mb-2">
    <h5 class="card-title mb-0">Answers</h5>
    <span class="badge bg-primary" id="answer-count">
        {{ max(1,$answers->count()) }} Answers
    </span>
</div>

<div class="alert alert-info small">
    ✔ Minimum 1 answer required<br>
    ✔ Multiple answers allowed<br>
    ✔ One answer can be marked as <b>Primary</b>
</div>

<div id="answers-wrapper">

@php
$answersData = $answers->count()
    ? $answers
    : collect([ (object)['content'=>'','answer_type'=>'detailed','is_primary'=>true] ]);
@endphp

@foreach($answersData as $i => $ans)
<div class="answer-item border rounded p-3 mb-3">

<div class="row">
    <div class="col-md-9">
        <label class="small fw-bold">
            Answer Content
        </label>
        <textarea name="answers[{{ $i }}][content]"
                  rows="3"
                  class="form-control"
                  required>{{ $ans->content ?? '' }}</textarea>
    </div>

    <div class="col-md-3">

        <label class="small fw-bold">Answer Type</label>
        <select name="answers[{{ $i }}][type]" class="form-select mb-2">
            @foreach(['short','detailed','code','beginner','advanced'] as $type)
                <option value="{{ $type }}"
                    @selected(($ans->answer_type ?? 'detailed') === $type)>
                    {{ ucfirst($type) }}
                </option>
            @endforeach
        </select>

        <div class="form-check mb-2">
            <input type="radio"
                   name="primary_answer"
                   value="{{ $i }}"
                   class="form-check-input primary-radio"
                   {{ ($ans->is_primary ?? false) ? 'checked' : '' }}>
            <label class="form-check-label small">
                Primary Answer
            </label>
        </div>

        <button type="button"
                class="btn btn-outline-danger btn-sm w-100 remove-answer">
            Remove
        </button>
    </div>
</div>

</div>
@endforeach

</div>

<button type="button"
        id="add-answer"
        class="btn btn-outline-primary btn-sm mt-2">
    ➕ Add Another Answer
</button>

</div>
</div>

{{-- =========================
    ACTIONS
========================= --}}
<div class="mt-4 text-end">
    <a href="{{ route('admin.questions.index') }}"
       class="btn btn-secondary">
        Back
    </a>

    <button class="btn btn-success">
        Save (Auto → Review)
    </button>
    <button name="action" value="draft" class="btn btn-secondary">
    Save Draft
</button>

<button name="action" value="submit" class="btn btn-success">
    Submit for Review
</button>

</div>

</form>

{{-- =========================
    JAVASCRIPT
========================= --}}
<script>
let wrapper = document.getElementById('answers-wrapper');
let countBadge = document.getElementById('answer-count');

function updateCount(){
    countBadge.innerText = wrapper.children.length + ' Answers';
}

document.getElementById('add-answer').addEventListener('click', function () {
    let index = wrapper.children.length;

    wrapper.insertAdjacentHTML('beforeend', `
        <div class="answer-item border rounded p-3 mb-3">
            <div class="row">
                <div class="col-md-9">
                    <textarea name="answers[${index}][content]"
                              rows="3"
                              class="form-control"
                              required
                              placeholder="Answer content"></textarea>
                </div>
                <div class="col-md-3">
                    <select name="answers[${index}][type]"
                            class="form-select mb-2">
                        <option value="short">Short</option>
                        <option value="detailed" selected>Detailed</option>
                        <option value="code">Code</option>
                        <option value="beginner">Beginner</option>
                        <option value="advanced">Advanced</option>
                    </select>

                    <div class="form-check mb-2">
                        <input type="radio"
                               name="primary_answer"
                               value="${index}"
                               class="form-check-input primary-radio">
                        <label class="form-check-label small">
                            Primary Answer
                        </label>
                    </div>

                    <button type="button"
                            class="btn btn-outline-danger btn-sm w-100 remove-answer">
                        Remove
                    </button>
                </div>
            </div>
        </div>
    `);

    updateCount();
});

document.addEventListener('click', function (e) {
    if (e.target.classList.contains('remove-answer')) {
        if (wrapper.children.length <= 1) {
            alert('At least one answer is required.');
            return;
        }
        e.target.closest('.answer-item').remove();
        updateCount();
    }
});
</script>

@push('scripts')
<script>
ClassicEditor
    .create(document.querySelector('#question-editor'), {
        toolbar: [
            'heading',
            '|',
            'bold',
            'italic',
            'link',
            'bulletedList',
            'numberedList',
            '|',
            'blockQuote',
            'insertTable',
            '|',
            'undo',
            'redo'
        ]
    })
    .catch(error => {
        console.error('CKEditor Error:', error);
    });
</script>
@endpush


@endsection
