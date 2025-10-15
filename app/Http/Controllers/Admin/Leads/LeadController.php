<?php
namespace App\Http\Controllers\Admin\Leads;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use App\Models\Admin;
use App\Models\Lead;
use App\Models\ClientContact;
use App\Models\ClientEmail;
use App\Models\ClientVisaCountry;
use App\Models\ClientPassportInformation;
use App\Models\Matter;
use Carbon\Carbon;

class LeadController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * Display a listing of leads
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $roles = \App\Models\UserRole::find(Auth::user()->role);
        $module_access = (array) json_decode($roles->module_access ?? '[]');
        
        if (array_key_exists('20', $module_access)) {
            // Using Lead model - automatically filters by role=7, type='lead', and is_deleted=null
            $query = Lead::where('is_archived', 0);

            $totalData = $query->count();
            
            // Apply filters using modern syntax
            $query->when($request->filled('client_id'), function ($q) use ($request) {
                return $q->where('client_id', $request->input('client_id'));
            });

            $query->when($request->filled('type'), function ($q) use ($request) {
                return $q->where('type', $request->input('type'));
            });

            $query->when($request->filled('name'), function ($q) use ($request) {
                return $q->where('first_name', 'LIKE', '%' . $request->input('name') . '%');
            });

            $query->when($request->filled('email'), function ($q) use ($request) {
                return $q->where('email', $request->input('email'));
            });

            $query->when($request->filled('phone'), function ($q) use ($request) {
                return $q->where(function ($subQuery) use ($request) {
                    $subQuery->where('phone', 'LIKE', '%' . $request->input('phone') . '%')
                             ->orWhere('att_phone', 'LIKE', '%' . $request->input('phone') . '%');
                });
            });

            $lists = $query->sortable(['id' => 'desc'])->paginate(20);
        } else {
            $lists = Lead::whereRaw('1 = 0')->sortable(['id' => 'desc'])->paginate(20);
            $totalData = 0;
        }
        
        return view('Admin.leads.index', compact('lists', 'totalData'));
    }

    /**
     * Display the specified lead's details
     * Shows comprehensive view of a single lead
     */
    public function detail(Request $request, $id = null)
    {
        if (isset($id) && !empty($id)) {
            $id = $this->decodeString($id);
            
            if (!$id) {
                return Redirect::to('/admin/leads')->with('error', config('constants.decode_string'));
            }
            
            // Using Lead model with withArchived scope to include archived leads
            $fetchedData = Lead::withArchived()->where('id', $id)->first();
            
            if ($fetchedData) {
                return view('Admin.leads.detail', compact('fetchedData'));
            } else {
                return Redirect::to('/admin/leads')->with('error', 'Lead does not exist');
            }
        } else {
            return Redirect::to('/admin/leads')->with('error', config('constants.unauthorized'));
        }
    }

    /**
     * Show the form for creating a new lead
     */
    public function create(Request $request)
    {
        // Get countries for dropdowns
        $countries = \App\Models\Country::orderBy('name', 'asc')->get();
        
        return view('Admin.leads.create', compact('countries'));
    }

    /**
     * Store a newly created lead
     */
    public function store(Request $request)
    {
        if ($request->isMethod('post')) {
            $requestData = $request->all();
            
            // Extract primary phone and email from dynamic arrays
            $primaryPhone = null;
            $primaryEmail = null;
            
            // Get the first phone number marked as "Personal" or the first one available
            if (isset($requestData['phone']) && is_array($requestData['phone'])) {
                $primaryPhone = reset($requestData['phone']); // Get first phone number
            }
            
            // Get the first email marked as "Personal" or the first one available  
            if (isset($requestData['email']) && is_array($requestData['email'])) {
                $primaryEmail = reset($requestData['email']); // Get first email
            }

            // Validate required fields
            $this->validate($request, [
                'first_name' => 'required|max:255',
                'last_name' => 'required|max:255',
                'gender' => 'required|max:255',
            ]);

            // Custom validation for dynamic phone and email fields
            if (!$primaryPhone) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['phone' => 'At least one phone number is required.']);
            }

            if (!$primaryEmail) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['email' => 'At least one email address is required.']);
            }

            // Additional validation for uniqueness
            if ($primaryPhone) {
                $existingPhone = Lead::where('phone', $primaryPhone)->first();
                if ($existingPhone) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['phone' => 'This phone number is already registered.']);
                }
            }

            if ($primaryEmail) {
                $existingEmail = Lead::where('email', $primaryEmail)->first();
                if ($existingEmail) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['email' => 'This email address is already registered.']);
                }
            }

            // Process related files with type validation
            $related_files = '';
            if (isset($requestData['related_files']) && is_array($requestData['related_files'])) {
                $related_files = implode(',', $requestData['related_files']);
            }

            // Process dates with validation
            $dob = null;
            if (!empty($requestData['dob'])) {
                $dobs = explode('/', $requestData['dob']);
                if (count($dobs) === 3) {
                    $dob = $dobs[2] . '-' . $dobs[1] . '-' . $dobs[0];
                }
            }

            $visa_expiry_date = null;
            if (!empty($requestData['visa_expiry_date'])) {
                $visa_expiry_dates = explode('/', $requestData['visa_expiry_date']);
                if (count($visa_expiry_dates) === 3) {
                    $visa_expiry_date = $visa_expiry_dates[2] . '-' . $visa_expiry_dates[1] . '-' . $visa_expiry_dates[0];
                }
            }

            // Use database transaction for data integrity
            DB::beginTransaction();
            
            try {
                // Handle profile image upload
                $profile_img = null;
                if ($request->hasfile('profile_img')) {
                    $profile_img = $this->uploadFile($request->file('profile_img'), config('constants.profile_imgs'));
                    if (!$profile_img) {
                        throw new \Exception('Profile image upload failed');
                    }
                }

                // Create new lead using Lead model
                $lead = new Lead();
                $lead->user_id = Auth::user()->id;
                $lead->password = Hash::make(uniqid()); // Required field, set a random password for leads
                $lead->first_name = $requestData['first_name'];
                $lead->last_name = $requestData['last_name'];
                $lead->gender = $requestData['gender'];
                $lead->dob = $dob;
                $lead->age = $requestData['age'] ?? null;
                $lead->marital_status = $requestData['marital_status'] ?? null;
                $lead->passport_number = $requestData['passport_no'] ?? null;
                $lead->visa_type = $requestData['visa_type'] ?? null;
                $lead->visaExpiry = $visa_expiry_date;
                $lead->tagname = $requestData['tags_label'] ?? null;
                $lead->contact_type = is_array($requestData['contact_type_hidden'] ?? null) ? ($requestData['contact_type_hidden'][0] ?? null) : ($requestData['contact_type_hidden'] ?? null);
                $lead->country_code = is_array($requestData['country_code'] ?? null) ? ($requestData['country_code'][0] ?? null) : ($requestData['country_code'] ?? null);
                $lead->phone = $primaryPhone;
                $lead->email_type = is_array($requestData['email_type_hidden'] ?? null) ? ($requestData['email_type_hidden'][0] ?? null) : ($requestData['email_type_hidden'] ?? null);
                $lead->email = $primaryEmail;
                $lead->service = $requestData['service'] ?? null;
                $lead->assignee = $requestData['assign_to'] ?? null;
                $lead->status = $requestData['status'] ?? null;
                $lead->lead_quality = $requestData['lead_quality'] ?? null;
                $lead->att_country_code = $requestData['att_country_code'] ?? null;
                $lead->att_phone = $requestData['att_phone'] ?? null;
                $lead->att_email = $requestData['att_email'] ?? null;
                $lead->source = $requestData['lead_source'] ?? null;
                $lead->related_files = rtrim($related_files, ',');
                $lead->comments_note = $requestData['comments_note'] ?? null;
                $lead->profile_img = $profile_img;

                // Additional fields
                $lead->preferredIntake = $requestData['preferredIntake'] ?? null;
                $lead->country_passport = $requestData['country_passport'] ?? null;
                $lead->address = $requestData['address'] ?? null;
                $lead->city = $requestData['city'] ?? null;
                $lead->state = $requestData['state'] ?? null;
                $lead->zip = $requestData['zip'] ?? null;
                $lead->country = $requestData['country'] ?? null;
                $lead->nomi_occupation = $requestData['nomi_occupation'] ?? null;
                $lead->skill_assessment = $requestData['skill_assessment'] ?? null;
                $lead->high_quali_aus = $requestData['high_quali_aus'] ?? null;
                $lead->high_quali_overseas = $requestData['high_quali_overseas'] ?? null;
                $lead->relevant_work_exp_aus = $requestData['relevant_work_exp_aus'] ?? null;
                $lead->relevant_work_exp_over = $requestData['relevant_work_exp_over'] ?? null;
                $lead->naati_py = $requestData['naati_py'] ?? null;
                $lead->married_partner = $requestData['married_partner'] ?? null;
                $lead->total_points = $requestData['total_points'] ?? null;
                $lead->start_process = $requestData['start_process'] ?? null;

                $lead->save();
                
                // Save ALL phone numbers to client_contacts table
                if (isset($requestData['contact_type_hidden']) && is_array($requestData['contact_type_hidden'])) {
                    foreach ($requestData['contact_type_hidden'] as $index => $contactType) {
                        $phone = $requestData['phone'][$index] ?? null;
                        $countryCode = $requestData['country_code'][$index] ?? '';
                        
                        if (!empty($phone)) {
                            ClientContact::create([
                                'admin_id' => Auth::user()->id,
                                'client_id' => $lead->id,
                                'contact_type' => $contactType,
                                'phone' => $phone,
                                'country_code' => $countryCode,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    }
                }
                
                // Save ALL emails to client_emails table
                if (isset($requestData['email_type_hidden']) && is_array($requestData['email_type_hidden'])) {
                    foreach ($requestData['email_type_hidden'] as $index => $emailType) {
                        $email = $requestData['email'][$index] ?? null;
                        
                        if (!empty($email)) {
                            ClientEmail::create([
                                'admin_id' => Auth::user()->id,
                                'client_id' => $lead->id,
                                'email_type' => $emailType,
                                'email' => $email,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    }
                }
                
                DB::commit();
                
                return Redirect::to('/admin/leads')->with('success', 'Lead added successfully');
            } catch (\Exception $e) {
                DB::rollBack();
                
                // Clean up uploaded file if exists
                if ($profile_img) {
                    $this->unlinkFile($profile_img, config('constants.profile_imgs'));
                }
                
                return redirect()->back()
                    ->withInput()
                    ->with('error', config('constants.server_error'));
            }
        }
        
        // If not POST, return error
        return redirect()->route('admin.leads.create')
            ->with('error', 'Invalid request method');
    }

    /**
     * Show the form for editing the specified lead
     */
    public function edit(Request $request, $id)
    {
        // Check authorization
        $check = $this->checkAuthorizationAction('edit_lead', $request->route()->getActionMethod(), Auth::user()->role);
        if ($check) {
            return Redirect::to('/admin/dashboard')->with('error', config('constants.unauthorized'));
        }

        $id = $this->decodeString($id);
        
        if (!$id) {
            return Redirect::to('/admin/leads')->with('error', config('constants.decode_string'));
        }
        
        // Using Lead model - automatically handles filtering
        $fetchedData = Lead::find($id);
        
        if (!$fetchedData) {
            return Redirect::to('/admin/leads')->with('error', 'Lead not found');
        }

        // Get countries for dropdown
        $countries = \App\Models\Country::orderBy('name', 'asc')->get();
        
        // Load contact data (required by edit form)
        $clientContacts = ClientContact::where('client_id', $id)->get() ?? collect();
        $emails = ClientEmail::where('client_id', $id)->get() ?? collect();
        
        // Load other related data for the edit form
        $visaCountries = \App\Models\ClientVisaCountry::where('client_id', $id)
            ->with('matter:id,title,nick_name')
            ->get() ?? collect();
        $clientPassports = \App\Models\ClientPassportInformation::where('client_id', $id)->get() ?? collect();
        $visaTypes = \App\Models\Matter::where('title', 'not like', '%skill assessment%')
            ->where('status', 1)
            ->orderBy('title', 'ASC')
            ->get();
        
        return view('Admin.leads.edit', compact(
            'fetchedData', 'countries', 'clientContacts', 'emails', 
            'visaCountries', 'clientPassports', 'visaTypes'
        ));
    }

    /**
     * Update the specified lead in storage
     */
    public function update(Request $request, $id)
    {
        // Check authorization
        $check = $this->checkAuthorizationAction('edit_lead', $request->route()->getActionMethod(), Auth::user()->role);
        if ($check) {
            return Redirect::to('/admin/dashboard')->with('error', config('constants.unauthorized'));
        }

        $id = $this->decodeString($id);
        
        if (!$id) {
            return Redirect::to('/admin/leads')->with('error', config('constants.decode_string'));
        }

        $requestData = $request->all();
        $requestData['id'] = $id; // Ensure ID is set for validation
        
        // Validate basic fields only (NOT phone/email as they are arrays)
        $this->validate($request, [
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'gender' => 'required|max:255',
        ]);

        // Custom validation for phone array
        if (empty($requestData['phone']) || !array_filter($requestData['phone'])) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['phone' => 'At least one phone number is required.']);
        }

        // Custom validation for email array
        if (empty($requestData['email']) || !array_filter($requestData['email'])) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['email' => 'At least one email address is required.']);
        }

        // Check for duplicate phones (excluding current lead)
        foreach ($requestData['phone'] as $phone) {
            if (!empty($phone)) {
                $existingPhone = Lead::where('phone', $phone)
                    ->where('id', '!=', $id)
                    ->first();
                if ($existingPhone) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['phone' => "Phone number {$phone} is already registered."]);
                }
            }
        }

        // Check for duplicate emails (excluding current lead)
        foreach ($requestData['email'] as $email) {
            if (!empty($email)) {
                $existingEmail = Lead::where('email', $email)
                    ->where('id', '!=', $id)
                    ->first();
                if ($existingEmail) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['email' => "Email {$email} is already registered."]);
                }
            }
        }

        // Find the lead by ID using Lead model
        $lead = Lead::find($id);
        
        // Check if the lead exists
        if (!$lead) {
            return redirect()->back()->with('error', 'Lead not found.');
        }

        // Process related files with type validation
        $related_files = '';
        if (isset($requestData['related_files']) && is_array($requestData['related_files'])) {
            $related_files = implode(',', $requestData['related_files']);
        }

        // Process dates with validation
        $dob = null;
        if (!empty($requestData['dob'])) {
            $dobs = explode('/', $requestData['dob']);
            if (count($dobs) === 3) {
                $dob = $dobs[2] . '-' . $dobs[1] . '-' . $dobs[0];
            }
        }

        $visa_expiry_date = null;
        if (!empty($requestData['visa_expiry_date'])) {
            $visa_expiry_dates = explode('/', $requestData['visa_expiry_date']);
            if (count($visa_expiry_dates) === 3) {
                $visa_expiry_date = $visa_expiry_dates[2] . '-' . $visa_expiry_dates[1] . '-' . $visa_expiry_dates[0];
            }
        }

        // Use database transaction for data integrity
        DB::beginTransaction();
        
        try {
            // Update lead data
            $lead->first_name = $requestData['first_name'];
            $lead->last_name = $requestData['last_name'];
            $lead->gender = $requestData['gender'];
            $lead->dob = $dob;
            $lead->age = $requestData['age'] ?? null;
            $lead->marital_status = $requestData['marital_status'] ?? null;
            $lead->passport_number = $requestData['passport_no'] ?? null;
            $lead->visa_type = $requestData['visa_type'] ?? null;
            $lead->visaExpiry = $visa_expiry_date;
            $lead->tagname = $requestData['tags_label'] ?? null;
            
            // Extract LAST phone from array (following ClientPersonalDetailsController pattern)
            $lastPhone = null;
            $lastCountryCode = null;
            $lastContactType = null;
            
            if (isset($requestData['phone']) && is_array($requestData['phone'])) {
                $phoneCount = count($requestData['phone']);
                for ($i = $phoneCount - 1; $i >= 0; $i--) {
                    if (!empty($requestData['phone'][$i])) {
                        $lastPhone = $requestData['phone'][$i];
                        $lastCountryCode = $requestData['country_code'][$i] ?? null;
                        $lastContactType = $requestData['contact_type_hidden'][$i] ?? null;
                        break;
                    }
                }
            }
            
            // Extract LAST email from array (following ClientPersonalDetailsController pattern)
            $lastEmail = null;
            $lastEmailType = null;
            
            if (isset($requestData['email']) && is_array($requestData['email'])) {
                $emailCount = count($requestData['email']);
                for ($i = $emailCount - 1; $i >= 0; $i--) {
                    if (!empty($requestData['email'][$i])) {
                        $lastEmail = $requestData['email'][$i];
                        $lastEmailType = $requestData['email_type_hidden'][$i] ?? null;
                        break;
                    }
                }
            }
            
            $lead->contact_type = $lastContactType;
            $lead->country_code = $lastCountryCode;
            $lead->phone = $lastPhone;
            $lead->email_type = $lastEmailType;
            $lead->email = $lastEmail;
            $lead->service = $requestData['service'] ?? null;
            $lead->assignee = $requestData['assign_to'] ?? null;
            $lead->status = $requestData['status'] ?? null;
            $lead->lead_quality = $requestData['lead_quality'] ?? null;
            $lead->att_country_code = $requestData['att_country_code'] ?? null;
            $lead->att_phone = $requestData['att_phone'] ?? null;
            $lead->att_email = $requestData['att_email'] ?? null;
            $lead->source = $requestData['lead_source'] ?? null;
            $lead->related_files = rtrim($related_files, ',');

            // Handle profile image upload with error handling
            if ($request->hasfile('profile_img')) {
                $new_profile_img = $this->uploadFile($request->file('profile_img'), config('constants.profile_imgs'));
                
                if ($new_profile_img) {
                    // Only delete old image after successful upload
                    if (!empty($requestData['old_profile_img'])) {
                        $this->unlinkFile($requestData['old_profile_img'], config('constants.profile_imgs'));
                    }
                    $lead->profile_img = $new_profile_img;
                } else {
                    throw new \Exception('Profile image upload failed');
                }
            } else {
                $lead->profile_img = $requestData['old_profile_img'] ?? null;
            }

            // Additional fields with null coalescing
            $lead->preferredIntake = $requestData['preferredIntake'] ?? null;
            $lead->country_passport = $requestData['country_passport'] ?? null;
            $lead->address = $requestData['address'] ?? null;
            $lead->city = $requestData['city'] ?? null;
            $lead->state = $requestData['state'] ?? null;
            $lead->zip = $requestData['zip'] ?? null;
            $lead->country = $requestData['country'] ?? null;
            $lead->nomi_occupation = $requestData['nomi_occupation'] ?? null;
            $lead->skill_assessment = $requestData['skill_assessment'] ?? null;
            $lead->high_quali_aus = $requestData['high_quali_aus'] ?? null;
            $lead->high_quali_overseas = $requestData['high_quali_overseas'] ?? null;
            $lead->relevant_work_exp_aus = $requestData['relevant_work_exp_aus'] ?? null;
            $lead->relevant_work_exp_over = $requestData['relevant_work_exp_over'] ?? null;
            $lead->naati_py = $requestData['naati_py'] ?? null;
            $lead->married_partner = $requestData['married_partner'] ?? null;
            $lead->total_points = $requestData['total_points'] ?? null;
            $lead->start_process = $requestData['start_process'] ?? null;

            $lead->save();
            
            // Update phone numbers in client_contacts table (following ClientPersonalDetailsController pattern)
            if (isset($requestData['contact_type_hidden']) && is_array($requestData['contact_type_hidden'])) {
                $processedPhoneIds = [];
                
                foreach ($requestData['contact_type_hidden'] as $key => $contactType) {
                    $contactId = $requestData['contact_id'][$key] ?? null;
                    $phone = $requestData['phone'][$key] ?? null;
                    $countryCode = $requestData['country_code'][$key] ?? '';
                    
                    if (!empty($phone)) {
                        if ($contactId) {
                            // Update existing contact
                            $existingContact = ClientContact::find($contactId);
                            if ($existingContact && $existingContact->client_id == $lead->id) {
                                $existingContact->update([
                                    'admin_id' => Auth::user()->id,
                                    'contact_type' => $contactType,
                                    'phone' => $phone,
                                    'country_code' => $countryCode
                                ]);
                                $processedPhoneIds[] = $existingContact->id;
                            }
                        } else {
                            // Create new contact
                            $newContact = ClientContact::create([
                                'admin_id' => Auth::user()->id,
                                'client_id' => $lead->id,
                                'contact_type' => $contactType,
                                'phone' => $phone,
                                'country_code' => $countryCode
                            ]);
                            $processedPhoneIds[] = $newContact->id;
                        }
                    }
                }
                
                // Delete contacts not in the processed list (user removed them)
                if (!empty($processedPhoneIds)) {
                    ClientContact::where('client_id', $lead->id)
                        ->whereNotIn('id', $processedPhoneIds)
                        ->delete();
                }
            }
            
            // Update emails in client_emails table (following ClientPersonalDetailsController pattern)
            if (isset($requestData['email_type_hidden']) && is_array($requestData['email_type_hidden'])) {
                $processedEmailIds = [];
                
                foreach ($requestData['email_type_hidden'] as $key => $emailType) {
                    $emailId = $requestData['email_id'][$key] ?? null;
                    $email = $requestData['email'][$key] ?? null;
                    
                    if (!empty($email)) {
                        if ($emailId) {
                            // Update existing email
                            $existingEmail = ClientEmail::find($emailId);
                            if ($existingEmail && $existingEmail->client_id == $lead->id) {
                                $existingEmail->update([
                                    'admin_id' => Auth::user()->id,
                                    'email_type' => $emailType,
                                    'email' => $email
                                ]);
                                $processedEmailIds[] = $existingEmail->id;
                            }
                        } else {
                            // Create new email
                            $newEmail = ClientEmail::create([
                                'admin_id' => Auth::user()->id,
                                'client_id' => $lead->id,
                                'email_type' => $emailType,
                                'email' => $email
                            ]);
                            $processedEmailIds[] = $newEmail->id;
                        }
                    }
                }
                
                // Delete emails not in the processed list (user removed them)
                if (!empty($processedEmailIds)) {
                    ClientEmail::where('client_id', $lead->id)
                        ->whereNotIn('id', $processedEmailIds)
                        ->delete();
                }
            }
            
            DB::commit();
            
            return redirect()->route('admin.leads.edit', base64_encode(convert_uuencode($id)))
                ->with('success', 'Lead updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->withInput()
                ->with('error', config('constants.server_error'));
        }
    }

    /**
     * Display the specified lead's history
     * Anyone can view lead history
     */
    public function history(Request $request, $id = null)
    {
        if (isset($id) && !empty($id)) {
            $id = $this->decodeString($id);
            
            if (!$id) {
                return Redirect::to('/admin/leads')->with('error', config('constants.decode_string'));
            }
            
            // Using Lead model with withArchived scope to include archived leads
            $fetchedData = Lead::withArchived()->where('id', $id)->first();
            
            if ($fetchedData) {
                return view('Admin.leads.history', compact('fetchedData'));
            } else {
                return Redirect::to('/admin/leads')->with('error', 'Lead does not exist');
            }
        } else {
            return Redirect::to('/admin/leads')->with('error', config('constants.unauthorized'));
        }
    }

    /**
     * Check if email is unique across leads AND clients
     * Prevents duplicate emails in the system
     */
    public function is_email_unique(Request $request)
    {
        $email = $request->input('email');
        $excludeId = $request->input('id'); // Optional - for edit operations
        
        // Check in leads (admins table where role=7, type='lead')
        $leadQuery = Lead::where('email', $email);
        if ($excludeId) {
            $leadQuery->where('id', '!=', $excludeId);
        }
        $lead_count = $leadQuery->count();
        
        // Check in clients (admins table where role=7, type='client')
        $client_count = Admin::where('role', 7)
            ->where('type', 'client')
            ->where('email', $email)
            ->when($excludeId, function($q) use ($excludeId) {
                return $q->where('id', '!=', $excludeId);
            })
            ->count();
        
        $total_count = $lead_count + $client_count;
        
        $response = [
            'status' => $total_count > 0 ? 1 : 0,
            'message' => $total_count > 0 ? 'The email has already been taken.' : '',
        ];
        
        return response()->json($response);
    }

    /**
     * Check if contact number is unique across leads AND clients
     * Prevents duplicate phone numbers in the system
     */
    public function is_contactno_unique(Request $request)
    {
        $contact = $request->input('contact');
        $excludeId = $request->input('id'); // Optional - for edit operations
        
        // Check in leads (admins table where role=7, type='lead')
        $leadQuery = Lead::where('phone', 'LIKE', '%' . $contact . '%');
        if ($excludeId) {
            $leadQuery->where('id', '!=', $excludeId);
        }
        $lead_count = $leadQuery->count();
        
        // Check in clients (admins table where role=7, type='client')
        $client_count = Admin::where('role', 7)
            ->where('type', 'client')
            ->where('phone', 'LIKE', '%' . $contact . '%')
            ->when($excludeId, function($q) use ($excludeId) {
                return $q->where('id', '!=', $excludeId);
            })
            ->count();
        
        $total_count = $lead_count + $client_count;
        
        $response = [
            'status' => $total_count > 0 ? 1 : 0,
            'message' => $total_count > 0 ? 'The phone has already been taken.' : '',
        ];
        
        return response()->json($response);
    }

    /**
     * Legacy method - Lead pin functionality (deprecated)
     */
    public function leadPin(Request $request, $id)
    {
        return redirect()->back()->with('error', 'Followup functionality has been removed');
    }

    /**
     * Legacy method - Delete lead notes (deprecated)
     */
    public function leaddeleteNotes(Request $request, $id = null)
    {
        return redirect()->back()->with('error', 'Followup functionality has been removed');
    }

    /**
     * Legacy method - Get note detail (deprecated)
     */
    public function getnotedetail(Request $request)
    {
        return response()->json([
            'status' => 0,
            'message' => 'Followup functionality has been removed'
        ]);
    }

    /**
     * Decode string helper method - consistent with parent behavior
     * 
     * @param string|null $string
     * @return string|false
     */
    public function decodeString($string = null)
    {
        if (empty($string)) {
            return false;
        }
        
        if (base64_encode(base64_decode($string, true)) === $string) {
            return convert_uudecode(base64_decode($string));
        }
        
        return false;
    }
}
