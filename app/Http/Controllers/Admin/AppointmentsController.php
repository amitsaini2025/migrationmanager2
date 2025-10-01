<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Services\AppointmentApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Auth;

class AppointmentsController extends Controller
{
    protected $apiService;

    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->apiService = new AppointmentApiService();
    }

    /**
     * Display appointments list
     */
    public function index(Request $request)
    { 
        $token = env('SOURCE_API_TOKEN'); 
        $baseUrl = env('SOURCE_API_URL');

        try {
            // Build query parameters for filtering and pagination
            $params = [];

            // Add appointment date filter if provided
            if ($request->has('appointment_date_field') && !empty($request->appointment_date_field)) {
                $params['appointment_date_field'] = $request->appointment_date_field;
            }

            // Add other field filter if provided
            if ($request->has('other_field') && !empty($request->other_field)) {
                $params['other_field'] = $request->other_field;
            }

            // Add pagination if provided
            if ($request->has('page') && !empty($request->page)) {
                $params['page'] = $request->page;
            }
            
            // Get appointments with filters and pagination
            $response = Http::withToken($token)->get($baseUrl . '/api/appointments', $params);
            $appointments = $response->json();

            // Pass the current request parameters to the view for maintaining filters in pagination
            $currentParams = $request->only(['appointment_date_field', 'other_field', 'page']);

            return view('Admin.appointments.index', compact('appointments', 'currentParams'));
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to fetch appointments: ' . $e->getMessage());
        }
    }

    /**
     * Create new appointment
     */
    public function store(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email',
            'date' => 'required|date',
            'time' => 'required',
            'description' => 'required|string',
            'status' => 'required|in:pending,confirmed,completed,cancelled,rescheduled'
        ]);

        try {
            $appointment = $this->apiService->createAppointment($request->all());
            return redirect()->route('appointments.index')->with('success', 'Appointment created successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create appointment: ' . $e->getMessage());
        }
    }

    /**
     * Update appointment
     */
    public function update(Request $request, $id)
    {
        // Get appointment details to check service type
        $token = env('SOURCE_API_TOKEN');
        $baseUrl = env('SOURCE_API_URL');
        
        $appointmentResponse = Http::withToken($token)->get($baseUrl . '/api/appointments/'.$id);
        $appointmentData = $appointmentResponse->json();
        $appointment = isset($appointmentData['data']) ? $appointmentData['data'] : $appointmentData;
        
        // Set validation rules based on service type
        $appointmentDetailsRules = 'required|in:phone,in_person';
        if (isset($appointment['service']['title']) && $appointment['service']['title'] == 'Migration Advice') {
            $appointmentDetailsRules .= ',zoom_google_meeting';
        }

        $request->validate([
            'date' => 'required',
            'time' => 'required',
            'description' => 'required|string',
            'status' => 'required|in:0,1,2,3,4,6,7,8,9,10,11',
            'appointment_details' => $appointmentDetailsRules,
            'preferred_language' => 'required|in:Hindi,English,Punjabi'
        ]);

        try {
            // Convert date format from DD/MM/YYYY to YYYY-MM-DD for API
            $dateFormatted = '';
            if ($request->date) {
                $dateParts = explode('/', $request->date);
                if (count($dateParts) == 3) {
                    $dateFormatted = $dateParts[2] . '-' . $dateParts[1] . '-' . $dateParts[0];
                }
            }

            // Prepare data for API
            $updateData = [
                'date' => $dateFormatted ?: $request->date,
                'time' => $request->time,
                'description' => $request->description,
                'status' => $request->status,
                'appointment_details' => $request->appointment_details,
                'preferred_language' => $request->preferred_language
            ];

            // Make PUT request to the API
            $response = Http::withToken($token)->put($baseUrl . '/api/appointments/'.$id, $updateData);

            if ($response->successful()) {
                return redirect()->route('appointments.index')->with('success', 'Appointment updated successfully');
            } else {
                $errorData = $response->json();
                $errorMessage = $errorData['message'] ?? 'Failed to update appointment';
                return back()->with('error', $errorMessage)->withInput();
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update appointment: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Delete appointment
     */
    public function destroy($id) {
        try {
            $token = env('SOURCE_API_TOKEN');
            $baseUrl = env('SOURCE_API_URL');

            // Make DELETE request to the API
            $response = Http::withToken($token)->delete($baseUrl . '/api/appointments/'.$id);

            // Check if the request was successful
            if ($response->successful()) {
                return redirect()->route('appointments.index')->with('success', 'Appointment deleted successfully');
            } else {
                return back()->with('error', 'Failed to delete appointment: ' . $response->body());
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete appointment: ' . $e->getMessage());
        }
    }



    /**
     * Display the specified appointment
     */
    public function appointmentsEducation(Request $request){
		$type='Education';
        $token = env('SOURCE_API_TOKEN');
        $baseUrl = env('SOURCE_API_URL');
        try {
            $response = Http::withToken($token)->get($baseUrl . '/api/appointments/calendar?type='.$type);
            $appointments = $response->json();
            return view('Admin.appointments.calender', compact('appointments','type'));
        } catch (\Exception $e) {
            return back()->with('error', 'Appointment not found: ' . $e->getMessage());
        }
	}

	public function appointmentsJrp(Request $request){
		$type='Jrp';
        $token = env('SOURCE_API_TOKEN');
        $baseUrl = env('SOURCE_API_URL');
        try {
            $response = Http::withToken($token)->get($baseUrl . '/api/appointments/calendar?type='.$type);
            $appointments = $response->json();
            return view('Admin.appointments.calender', compact('appointments','type'));
        } catch (\Exception $e) {
            return back()->with('error', 'Appointment not found: ' . $e->getMessage());
        }
	}

	public function appointmentsTourist(Request $request){
		$type='Tourist';
        $token = env('SOURCE_API_TOKEN');
        $baseUrl = env('SOURCE_API_URL');
        try {
            $response = Http::withToken($token)->get($baseUrl . '/api/appointments/calendar?type='.$type);
            $appointments = $response->json();
            return view('Admin.appointments.calender', compact('appointments','type'));
        } catch (\Exception $e) {
            return back()->with('error', 'Appointment not found: ' . $e->getMessage());
        }
	}

	public function appointmentsOthers(Request $request){
		$type='Others';
        $token = env('SOURCE_API_TOKEN');
        $baseUrl = env('SOURCE_API_URL');
        try {
            $response = Http::withToken($token)->get($baseUrl . '/api/appointments/calendar?type='.$type);
            $appointments = $response->json();
            return view('Admin.appointments.calender', compact('appointments','type'));
        } catch (\Exception $e) {
            return back()->with('error', 'Appointment not found: ' . $e->getMessage());
        }
	}

    public function appointmentsAdelaide(Request $request){
		$type = 'Adelaide';
        $token = env('SOURCE_API_TOKEN');
        $baseUrl = env('SOURCE_API_URL');
        try {
            $response = Http::withToken($token)->get($baseUrl . '/api/appointments/calendar?type='.$type);
            $appointments = $response->json();
            return view('Admin.appointments.calender', compact('appointments','type'));
        } catch (\Exception $e) {
            return back()->with('error', 'Appointment not found: ' . $e->getMessage());
        }
	}


    /**
     * Show the form for creating a new appointment
     */
    public function create()
    {
        return view('Admin.appointments.create');
    }

    /**
     * Display the specified appointment
     */
    public function show($id)
    {
        $token = env('SOURCE_API_TOKEN');
        $baseUrl = env('SOURCE_API_URL');
        
        try {
            // Get appointment details from API
            $response = Http::withToken($token)->get($baseUrl . '/api/appointments/'.$id);
            
            if ($response->successful()) {
                $appointmentData = $response->json();
                
                // Check if API response has the expected structure
                if (isset($appointmentData['data'])) {
                    $appointment = $appointmentData['data'];
                } else {
                    $appointment = $appointmentData;
                }
                
                return view('Admin.appointments.show', compact('appointment'));
            } else {
                return back()->with('error', 'Failed to fetch appointment: ' . $response->status());
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Appointment not found: ' . $e->getMessage());
        }
    }



    /**
     * Show the form for editing the specified appointment
     */
    public function edit($id)
    {
        $token = env('SOURCE_API_TOKEN');
        $baseUrl = env('SOURCE_API_URL');
        
        try {
            // Get appointment details from API
            $response = Http::withToken($token)->get($baseUrl . '/api/appointments/'.$id);
            
            if ($response->successful()) {
                $appointmentData = $response->json();
                
                // Check if API response has the expected structure
                if (isset($appointmentData['data'])) {
                    $appointment = $appointmentData['data'];
                } else {
                    $appointment = $appointmentData;
                }
                
                return view('Admin.appointments.edit', compact('appointment'));
            } else {
                return back()->with('error', 'Failed to fetch appointment: ' . $response->status());
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Appointment not found: ' . $e->getMessage());
        }
    }

    /**
     * API endpoint for AJAX requests
     */
    public function apiIndex(Request $request)
    {
        try {
            $filters = $request->all();
            $appointments = $this->apiService->getAppointments($filters);
            return response()->json($appointments);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}

