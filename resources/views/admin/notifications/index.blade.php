@extends('layouts.admin')

@section('title','Notifications')

@section('content')

<div class="pagetitle">
    <h1>Notifications</h1>
    <p class="text-muted">All system notifications</p>
</div>

<div class="card">
    <div class="card-body">

        <ul class="list-group list-group-flush">
            @forelse($notifications as $n)
                <li class="list-group-item d-flex gap-3">
                    <i class="bi 
                        {{ $n->data['action'] === 'approved'
                            ? 'bi-check-circle text-success'
                            : 'bi-x-circle text-danger' }}">
                    </i>

                    <div>
                        <div class="fw-semibold">
                            {{ $n->data['title'] }}
                        </div>

                        <small class="text-muted">
                            {{ ucfirst($n->data['action']) }}
                            @if(!empty($n->data['reason']))
                                – {{ $n->data['reason'] }}
                            @endif
                        </small>

                        <div class="small text-muted">
                            {{ $n->created_at->diffForHumans() }}
                        </div>
                    </div>
                </li>
            @empty
                <li class="list-group-item text-center text-muted">
                    No notifications found
                </li>
            @endforelse
        </ul>

        <div class="mt-3">
            {{ $notifications->links() }}
        </div>

    </div>
</div>

@endsection
