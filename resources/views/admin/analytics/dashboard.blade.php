@extends('layouts.app')
@section('content')
<div class="container">
    <h1>Analytics Dashboard</h1>
    <div class="row">
        <div class="col-md-4">
            <h3>Sales Total</h3>
            <p>{{ number_format($salesTotal, 2) }} TND</p>
        </div>
        <div class="col-md-4">
            <h3>Export Timeline (last 30 days)</h3>
            <ul>
                @foreach($exportTimeline as $row)
                    <li>{{ $row->date }}: {{ $row->count }} exports</li>
                @endforeach
            </ul>
        </div>
        <div class="col-md-4">
            <h3>Trust Score Evolution (last 30 days)</h3>
            <ul>
                @foreach($trustEvolution as $row)
                    <li>{{ $row->date }}: {{ number_format($row->avg_trust, 2) }}</li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endsection
