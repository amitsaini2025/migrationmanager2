@extends('layouts.crm_client_detail')
@section('title', 'Edit Appointment - #' . $appointment->id)

@section('content')
<div class="section-body">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4>
                    <i class="fas fa-edit mr-2"></i>
                    Edit Appointment - #{{ $appointment->id }}
                </h4>
                <a href="{{ route('booking.appointments.show', $appointment->id) }}" class="btn btn-sm btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Appointment Details
                </a>
            </div>

            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                Use this form to change only the appointment date and time. Other details remain unchanged.
            </div>

            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif

            @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif

            <div class="card">
                <div class="card-header bg-info text-white">
                    <i class="fas fa-calendar-alt"></i> Update Date & Time
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('booking.appointments.update', $appointment->id) }}" class="needs-validation" novalidate>
                        @csrf
                        @method('PUT')
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="appointment-date">Appointment Date</label>
                                <input type="date" class="form-control" id="appointment-date" name="appointment_date" value="{{ old('appointment_date', $appointment->appointment_datetime->format('Y-m-d')) }}" required>
                                <div class="invalid-feedback">
                                    Please select a valid appointment date.
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="appointment-time">Appointment Time</label>
                                <input type="time" class="form-control" id="appointment-time" name="appointment_time" value="{{ old('appointment_time', $appointment->appointment_datetime->format('H:i')) }}" required>
                                <div class="invalid-feedback">
                                    Please select a valid appointment time.
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">Client</label>
                            <p class="mb-1">{{ $appointment->client_name }} <small class="text-muted">({{ $appointment->client_email }})</small></p>
                            <p class="mb-1"><small>{{ $appointment->client_phone }}</small></p>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">Current Schedule</label>
                            <p class="mb-1">{{ $appointment->appointment_datetime->format('l, d M Y') }}</p>
                            <p class="mb-0"><small>{{ $appointment->appointment_datetime->format('h:i A') }}</small></p>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">Time Zone</label>
                            <p class="mb-0">{{ $appointment->client_timezone ?? config('app.timezone') }}</p>
                        </div>
                        <div class="form-group mb-0">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Appointment
                            </button>
                            <a href="{{ route('booking.appointments.index') }}" class="btn btn-light ml-2">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    'use strict';
    window.addEventListener('load', function() {
        var forms = document.getElementsByClassName('needs-validation');
        Array.prototype.filter.call(forms, function(form) {
            form.addEventListener('submit', function(event) {
                if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    }, false);
})();
</script>
@endsection
