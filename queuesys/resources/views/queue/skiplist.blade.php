@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Skipped Visitors - {{ $office }}</h1>

    @if ($skipped->isEmpty())
        <div class="alert alert-info">
            No skipped visitors for today.
        </div>
    @else
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Queue Number</th>
                    <th>Name</th>
                    <th>Time Skipped</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($skipped as $visitor)
                    <tr>
                        <td>{{ $visitor->queue_number }}</td>
                        <td>{{ $visitor->name }}</td>
                        <td>{{ $visitor->updated_at->format('h:i A') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <a href="{{ route('office.queue', ['office' => strtolower($office)]) }}" class="btn btn-secondary mt-3">
        Back to Queue
    </a>
</div>
@endsection
