@extends('layouts.admin')
@section('title', 'Booking Calendar')
@section('content')
    <div class="container py-4">
        <h1 class="fw-bold">Booking Calendar</h1>
        <p class="text-secondary">Block entire days to prevent new bookings. Existing bookings remain visible.</p>
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif
        <form method="POST" action="{{ route('admin.calendar.block') }}" class="row g-3 mb-4">
            @csrf
            <div class="col-12 col-md-4">
                <label class="form-label" for="blocked-date">Date</label>
                <input type="date" id="blocked-date" name="date" value="{{ old('date') }}" min="{{ now()->toDateString() }}" class="form-control" required>
            </div>
            <div class="col-12 col-md-5">
                <label class="form-label" for="blocked-reason">Reason</label>
                <input type="text" id="blocked-reason" name="reason" value="{{ old('reason') }}" maxlength="255" class="form-control">
            </div>
            <div class="col-12 col-md-3 d-flex align-items-end">
                <button class="btn btn-primary w-100" type="submit">Block date</button>
            </div>
        </form>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead><tr><th>Date</th><th>Reason</th><th class="text-end">Actions</th></tr></thead>
                <tbody>
                    @forelse($blockedDates as $blockedDate)
                        <tr>
                            <td class="text-nowrap">{{ $blockedDate->date->format('Y-m-d') }}</td>
                            <td>{{ $blockedDate->reason ?: '—' }}</td>
                            <td class="text-end">
                                <form method="POST" action="{{ route('admin.calendar.unblock') }}">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="id" value="{{ $blockedDate->id }}">
                                    <button class="btn btn-outline-danger btn-sm" type="submit">Unblock</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-center text-secondary">No blocked dates.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $blockedDates->links() }}
    </div>
@endsection
