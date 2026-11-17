@extends('layouts.app')

@section('title', 'User Management - DENR-4A RDAC Portal')

@section('content')
<div class="card p-3 p-md-4 shadow-sm bg-white">

  <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div>
            <h5 class="fw-bold text-primary mb-0"><i class="fa-solid fa-users-gear me-2"></i>Staff Accounts</h5>
            <div class="small text-muted">{{ $users->total() }} of {{ $total }} account{{ $total === 1 ? '' : 's' }}</div>
        </div>

        <form method="GET" action="{{ route('users.index') }}" class="d-flex flex-wrap gap-2">
        <input type="search" name="q" value="{{ $search }}" class="form-control form-control-sm" style="width:220px;"
            placeholder="Search name, office, role..." aria-label="Search accounts">
        <button type="submit" class="btn btn-sm btn-outline-primary" title="Search"><i class="fa-solid fa-magnifying-glass"></i></button>
        @if ($search !== '')
            <a href="{{ route('users.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
        @endif
        </form>
    </div>

    <a href="{{ route('users.create') }}" class="btn btn-sm btn-success">
      <i class="fa-solid fa-user-plus me-1"></i> Create Staff Account
    </a>

  <div class="table-responsive">
    <table class="table align-middle small">

      <thead class="table-light">
            <tr><th>User</th><th>Role</th><th>Office / Division</th><th>Contact</th><th>Status</th><th class="text-end">Action</th></tr>
      </thead>

      <tbody>
        @forelse ($users as $user)
          <tr>
            <td>
              <div class="d-flex align-items-center gap-2">
                <span class="avatar">{{ $user->initials }}</span>
                <div>
                  <div class="fw-semibold">
                    {{ $user->full_name }}
                    @if ($user->id === auth()->id())
                      <span class="badge bg-light text-dark border">You</span>
                    @endif
                  </div>
                  <div class="small text-muted">{{ $user->username }}</div>
                </div>
              </div>
            </td>

            <td><span class="badge role-{{ str_replace('_', '-', $user->role->value) }}">{{ $user->role->label() }}</span></td>
            <td>{{ $user->office->name }}</td>

            <td>
              <div>{{ $user->phone ?: '-' }}</div>
              <div class="small text-muted">{{ $user->email ?: '-' }}</div>
            </td>

            <td>
              <span class="badge badge-status {{ $user->is_active ? 'status-approved' : 'status-resolved' }}">
                {{ $user->is_active ? 'Active' : 'Archived' }}
              </span>
            </td>

            <td class="text-end">
              @if ($user->id === auth()->id())
                <button type="button" class="btn btn-sm btn-outline-secondary" disabled
                        title="You cannot archive your own account">
                  <i class="fa-solid fa-box-archive me-1"></i>Archive
                </button>
              @elseif ($user->is_active)
                <form method="POST" action="{{ route('users.archive', $user) }}" class="d-inline"
                      data-confirm-title="Archive this account?"
                      data-confirm="{{ '"' . $user->username . '" will be logged out immediately and cannot log in until restored.' }}"
                      data-confirm-button="Yes, archive">
                  @csrf
                  <button type="submit" class="btn btn-sm btn-outline-secondary">
                    <i class="fa-solid fa-box-archive me-1"></i>Archive
                  </button>
                </form>
              @else
                <form method="POST" action="{{ route('users.restore', $user) }}" class="d-inline"
                      data-confirm-title="Restore this account?"
                      data-confirm="{{ 'The account "' . $user->username . '" will be reactivated and can log in again.' }}"
                      data-confirm-button="Yes, restore">
                  @csrf
                  <button type="submit" class="btn btn-sm btn-outline-success">
                    <i class="fa-solid fa-box-open me-1"></i>Restore
                  </button>
                </form>
              @endif
            </td>
          </tr>
        @empty

          <tr>
            <td colspan="6" class="text-center text-muted py-5">
              <i class="fa-regular fa-user fs-3 d-block mb-2"></i>No accounts found.
            </td>
          </tr>
        @endforelse

      </tbody>
    </table>
  </div>

  <div class="mt-3">{{ $users->links() }}</div>
</div>
@endsection