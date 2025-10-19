@extends('layouts.admin_client_detail')
@section('title', ucfirst($type) . ' Calendar - Website Bookings')

@section('content')

<link rel="stylesheet" href="{{URL::asset('css/fullcalendar.min.css')}}">

<style>
html, body {
    overflow-x: hidden !important;
    max-width: 100% !important;
}

/* Calendar styling */
#calendar {
    max-width: 100%;
    margin: 0 auto;
}

.fc-event {
    cursor: pointer;
    border-radius: 3px;
    padding: 2px 5px;
}

.fc-event:hover {
    opacity: 0.8;
}

/* Status colors for calendar events */
.event-pending {
    background-color: #ffc107;
    border-color: #ffc107;
    color: #000;
}

.event-confirmed {
    background-color: #28a745;
    border-color: #28a745;
    color: #fff;
}

.event-completed {
    background-color: #17a2b8;
    border-color: #17a2b8;
    color: #fff;
}

.event-cancelled {
    background-color: #dc3545;
    border-color: #dc3545;
    color: #fff;
    text-decoration: line-through;
}

.event-no-show {
    background-color: #6c757d;
    border-color: #6c757d;
    color: #fff;
    opacity: 0.7;
}

.calendar-legend {
    display: flex;
    justify-content: center;
    gap: 20px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}

.legend-item {
    display: flex;
    align-items: center;
    gap: 5px;
}

.legend-color {
    width: 20px;
    height: 20px;
    border-radius: 3px;
}

.calendar-stats {
    display: flex;
    justify-content: space-around;
    margin-bottom: 20px;
}

.stat-box {
    text-align: center;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 5px;
}

.stat-box h3 {
    margin: 0;
    font-size: 2rem;
    color: #007bff;
}

.stat-box p {
    margin: 5px 0 0 0;
    color: #6c757d;
}
</style>

<div class="section-body">
    <div class="row">
        <div class="col-12">
            <!-- Back and Calendar Type Navigation -->
            <div class="mb-3">
                <a href="{{ route('booking.appointments.index') }}" class="btn btn-sm btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
                <div class="btn-group ml-2" role="group">
                    <a href="{{ route('booking.appointments.calendar', ['type' => 'paid']) }}" 
                       class="btn btn-sm {{ $type === 'paid' ? 'btn-primary' : 'btn-outline-primary' }}">
                        <i class="far fa-calendar-check"></i> Paid Services
                    </a>
                    <a href="{{ route('booking.appointments.calendar', ['type' => 'jrp']) }}" 
                       class="btn btn-sm {{ $type === 'jrp' ? 'btn-primary' : 'btn-outline-primary' }}">
                        <i class="far fa-calendar"></i> JRP
                    </a>
                    <a href="{{ route('booking.appointments.calendar', ['type' => 'education']) }}" 
                       class="btn btn-sm {{ $type === 'education' ? 'btn-primary' : 'btn-outline-primary' }}">
                        <i class="fas fa-graduation-cap"></i> Education
                    </a>
                    <a href="{{ route('booking.appointments.calendar', ['type' => 'tourist']) }}" 
                       class="btn btn-sm {{ $type === 'tourist' ? 'btn-primary' : 'btn-outline-primary' }}">
                        <i class="fas fa-plane"></i> Tourist
                    </a>
                    <a href="{{ route('booking.appointments.calendar', ['type' => 'adelaide']) }}" 
                       class="btn btn-sm {{ $type === 'adelaide' ? 'btn-primary' : 'btn-outline-primary' }}">
                        <i class="fas fa-city"></i> Adelaide
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h4>
                        <i class="fas fa-calendar-alt mr-2"></i>
                        {{ ucfirst($type) }} Calendar
                        <small class="text-muted">(Website Bookings)</small>
                    </h4>
                    <div class="card-header-action">
                        <button onclick="location.reload()" class="btn btn-sm btn-info">
                            <i class="fas fa-sync"></i> Refresh
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Stats -->
                    <div class="calendar-stats">
                        <div class="stat-box">
                            <h3>{{ $stats['this_month'] ?? 0 }}</h3>
                            <p>This Month</p>
                        </div>
                        <div class="stat-box">
                            <h3>{{ $stats['today'] ?? 0 }}</h3>
                            <p>Today</p>
                        </div>
                        <div class="stat-box">
                            <h3>{{ $stats['upcoming'] ?? 0 }}</h3>
                            <p>Upcoming</p>
                        </div>
                        <div class="stat-box">
                            <h3>{{ $stats['pending'] ?? 0 }}</h3>
                            <p>Pending</p>
                        </div>
                    </div>

                    <!-- Legend -->
                    <div class="calendar-legend">
                        <div class="legend-item">
                            <div class="legend-color event-pending"></div>
                            <span>Pending</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color event-confirmed"></div>
                            <span>Confirmed</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color event-completed"></div>
                            <span>Completed</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color event-cancelled"></div>
                            <span>Cancelled</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color event-no-show"></div>
                            <span>No Show</span>
                        </div>
                    </div>

                    <!-- Calendar -->
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Event Detail Modal -->
<div class="modal fade" id="eventModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Appointment Details</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="eventModalBody">
                <!-- Content will be loaded dynamically -->
            </div>
            <div class="modal-footer">
                <a href="#" id="viewFullDetails" class="btn btn-primary" target="_blank">View Full Details</a>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="{{URL::asset('js/moment.min.js')}}"></script>
<script src="{{URL::asset('js/fullcalendar.min.js')}}"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        editable: false,
        selectable: false,
        selectMirror: true,
        dayMaxEvents: true,
        weekNumbers: false,
        navLinks: true,
        
        // Fetch events from server
        events: function(info, successCallback, failureCallback) {
            $.ajax({
                url: '{{ route("booking.api.appointments") }}',
                method: 'GET',
                data: {
                    type: '{{ $type }}',
                    start: info.startStr,
                    end: info.endStr,
                    format: 'calendar'
                },
                success: function(response) {
                    var events = response.data.map(function(appointment) {
                        return {
                            id: appointment.id,
                            title: appointment.client_name + ' (' + appointment.service_type + ')',
                            start: appointment.appointment_datetime,
                            end: moment(appointment.appointment_datetime).add(appointment.duration_minutes, 'minutes').toISOString(),
                            className: 'event-' + appointment.status,
                            extendedProps: {
                                client_name: appointment.client_name,
                                client_email: appointment.client_email,
                                client_phone: appointment.client_phone,
                                service_type: appointment.service_type,
                                status: appointment.status,
                                location: appointment.location,
                                consultant: appointment.consultant ? appointment.consultant.name : 'Not Assigned',
                                payment_status: appointment.is_paid ? 'Paid' : 'Free'
                            }
                        };
                    });
                    successCallback(events);
                },
                error: function() {
                    failureCallback();
                    alert('Failed to load appointments');
                }
            });
        },
        
        // Event click handler
        eventClick: function(info) {
            var event = info.event;
            var props = event.extendedProps;
            
            var modalBody = `
                <div class="appointment-details">
                    <p><strong>Client:</strong> ${props.client_name}</p>
                    <p><strong>Email:</strong> ${props.client_email}</p>
                    <p><strong>Phone:</strong> ${props.client_phone}</p>
                    <p><strong>Service:</strong> ${props.service_type}</p>
                    <p><strong>Date & Time:</strong> ${moment(event.start).format('DD MMM YYYY, hh:mm A')}</p>
                    <p><strong>Location:</strong> ${props.location}</p>
                    <p><strong>Consultant:</strong> ${props.consultant}</p>
                    <p><strong>Status:</strong> <span class="badge badge-${getStatusClass(props.status)}">${props.status.toUpperCase()}</span></p>
                    <p><strong>Payment:</strong> <span class="badge badge-${props.payment_status === 'Paid' ? 'success' : 'secondary'}">${props.payment_status}</span></p>
                </div>
            `;
            
            $('#eventModalBody').html(modalBody);
            $('#viewFullDetails').attr('href', '/admin/booking/appointments/' + event.id);
            $('#eventModal').modal('show');
        }
    });
    
    calendar.render();
    
    // Helper function for status badge class
    function getStatusClass(status) {
        switch(status) {
            case 'pending': return 'warning';
            case 'confirmed': return 'success';
            case 'completed': return 'info';
            case 'cancelled': return 'danger';
            case 'no_show': return 'dark';
            default: return 'secondary';
        }
    }
});
</script>

@endsection

