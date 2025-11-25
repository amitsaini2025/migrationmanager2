<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\ClientPortalDetailAudit;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ClientPortalPersonalDetailsController extends Controller
{
    /**
     * Get client personal details
     * 
     * This API fetches basic information from admins table and overrides with latest audit values.
     * It does not insert records into clientportal_details or clientportal_details_audit tables.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getClientPersonalDetail(Request $request)
    {
        try {
            // Get authenticated client ID from token
            $admin = $request->user();
            $clientId = (int) $admin->id;

            // Verify the authenticated user is a client (role=7)
            if ($admin->role != 7) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied. This endpoint is only available for clients.'
                ], 403);
            }

            // Get the tab parameter from query string (default to 'all')
            $tab = $request->query('tab', 'all');
            $tab = strtolower(trim($tab));

            // Define valid tabs
            $validTabs = [
                'all',
                'basic_information',
                'phones',
                'emails',
                'passports',
                'visas',
                'addresses',
                'travels',
                'qualifications',
                'experiences',
                'occupations',
                'test_scores'
            ];

            // Validate tab parameter
            if (!in_array($tab, $validTabs)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid tab parameter. Valid values are: ' . implode(', ', $validTabs)
                ], 422);
            }

            // Initialize response data array
            $responseData = [];

            // Fetch data only for requested tab(s)
            if ($tab === 'all' || $tab === 'basic_information') {
                // Fetch basic details from admins table
                $basicInfo = [
                    'first_name' => $admin->first_name ?? null,
                    'last_name' => $admin->last_name ?? null,
                    'client_id' => $admin->client_id ?? null,
                    'date_of_birth' => $admin->dob ? $this->formatDate($admin->dob) : null,
                    'age' => $admin->age ?? null,
                    'gender' => $admin->gender ?? null,
                    'marital_status' => $admin->marital_status ?? null,
                ];

                // Get latest audit values for basic fields and override admins table values
                $basicFields = ['first_name', 'last_name', 'client_id', 'dob', 'age', 'gender', 'marital_status'];
                
                foreach ($basicFields as $field) {
                    // Get the latest audit entry for this field
                    $latestAudit = ClientPortalDetailAudit::where('client_id', $clientId)
                        ->where('meta_key', $field)
                        ->orderBy('updated_at', 'desc')
                        ->first();
                    
                    if ($latestAudit && $latestAudit->new_value !== null) {
                        // Override with audit value
                        if ($field === 'dob') {
                            $basicInfo['date_of_birth'] = $latestAudit->new_value ? $this->formatDate($latestAudit->new_value) : null;
                        } else {
                            $basicInfo[$field] = $latestAudit->new_value;
                        }
                    }
                }

                // Build full name
                $fullName = trim(($basicInfo['first_name'] ?? '') . ' ' . ($basicInfo['last_name'] ?? ''));
                $basicInfo['name'] = $fullName;
                $basicInfo['full_name'] = $fullName;
                
                // Add internal client ID
                $basicInfo['internal_client_id'] = $clientId;

                if ($tab === 'basic_information') {
                    // Return only basic information
                    return response()->json([
                        'success' => true,
                        'message' => 'Client personal details retrieved successfully',
                        'data' => [
                            'basic_information' => $basicInfo
                        ]
                    ]);
                }

                $responseData['basic_information'] = $basicInfo;
            }

            if ($tab === 'all' || $tab === 'phones') {
                $phones = $this->getPhonesData($clientId);
                if ($tab === 'phones') {
                    return response()->json([
                        'success' => true,
                        'message' => 'Client personal details retrieved successfully',
                        'data' => [
                            'phones' => $phones
                        ]
                    ]);
                }
                $responseData['phones'] = $phones;
            }

            if ($tab === 'all' || $tab === 'emails') {
                $emails = $this->getEmailsData($clientId);
                if ($tab === 'emails') {
                    return response()->json([
                        'success' => true,
                        'message' => 'Client personal details retrieved successfully',
                        'data' => [
                            'emails' => $emails
                        ]
                    ]);
                }
                $responseData['emails'] = $emails;
            }

            if ($tab === 'all' || $tab === 'passports') {
                $passports = $this->getPassportsData($clientId);
                if ($tab === 'passports') {
                    return response()->json([
                        'success' => true,
                        'message' => 'Client personal details retrieved successfully',
                        'data' => [
                            'passports' => $passports
                        ]
                    ]);
                }
                $responseData['passports'] = $passports;
            }

            if ($tab === 'all' || $tab === 'visas') {
                $visas = $this->getVisasData($clientId);
                if ($tab === 'visas') {
                    return response()->json([
                        'success' => true,
                        'message' => 'Client personal details retrieved successfully',
                        'data' => [
                            'visas' => $visas
                        ]
                    ]);
                }
                $responseData['visas'] = $visas;
            }

            if ($tab === 'all' || $tab === 'addresses') {
                $addresses = $this->getAddressesData($clientId);
                if ($tab === 'addresses') {
                    return response()->json([
                        'success' => true,
                        'message' => 'Client personal details retrieved successfully',
                        'data' => [
                            'addresses' => $addresses
                        ]
                    ]);
                }
                $responseData['addresses'] = $addresses;
            }

            if ($tab === 'all' || $tab === 'travels') {
                $travels = $this->getTravelsData($clientId);
                if ($tab === 'travels') {
                    return response()->json([
                        'success' => true,
                        'message' => 'Client personal details retrieved successfully',
                        'data' => [
                            'travels' => $travels
                        ]
                    ]);
                }
                $responseData['travels'] = $travels;
            }

            if ($tab === 'all' || $tab === 'qualifications') {
                $qualifications = $this->getQualificationsData($clientId);
                if ($tab === 'qualifications') {
                    return response()->json([
                        'success' => true,
                        'message' => 'Client personal details retrieved successfully',
                        'data' => [
                            'qualifications' => $qualifications
                        ]
                    ]);
                }
                $responseData['qualifications'] = $qualifications;
            }

            if ($tab === 'all' || $tab === 'experiences') {
                $experiences = $this->getExperiencesData($clientId);
                if ($tab === 'experiences') {
                    return response()->json([
                        'success' => true,
                        'message' => 'Client personal details retrieved successfully',
                        'data' => [
                            'experiences' => $experiences
                        ]
                    ]);
                }
                $responseData['experiences'] = $experiences;
            }

            if ($tab === 'all' || $tab === 'occupations') {
                $occupations = $this->getOccupationsData($clientId);
                if ($tab === 'occupations') {
                    return response()->json([
                        'success' => true,
                        'message' => 'Client personal details retrieved successfully',
                        'data' => [
                            'occupations' => $occupations
                        ]
                    ]);
                }
                $responseData['occupations'] = $occupations;
            }

            if ($tab === 'all' || $tab === 'test_scores') {
                $testScores = $this->getTestScoresData($clientId);
                if ($tab === 'test_scores') {
            return response()->json([
                'success' => true,
                'message' => 'Client personal details retrieved successfully',
                'data' => [
                            'test_scores' => $testScores
                        ]
                    ]);
                }
                $responseData['test_scores'] = $testScores;
            }

            // Return all data if tab is 'all'
            return response()->json([
                'success' => true,
                'message' => 'Client personal details retrieved successfully',
                'data' => $responseData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update client basic details - saves only to clientportal_details_audit table
     * 
     * This API allows clients to update their basic information.
     * Only updated fields will be saved to clientportal_details_audit table.
     * The clientportal_details table will not be used for these updates.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateClientBasicDetail(Request $request)
    {
        try {
            // Get authenticated client ID from token
            $admin = $request->user();
            $clientId = (int) $admin->id;

            // Verify the authenticated user is a client (role=7)
            if ($admin->role != 7) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied. This endpoint is only available for clients.'
                ], 403);
            }

            // Remove client_id from request if present (readonly field)
            $requestData = $request->all();
            if (isset($requestData['client_id'])) {
                unset($requestData['client_id']);
            }

            // Validate the request
            $validator = \Illuminate\Support\Facades\Validator::make($requestData, [
                'first_name' => 'sometimes|string|max:255',
                'last_name' => 'sometimes|string|max:255',
                'date_of_birth' => 'sometimes|date_format:d/m/Y|before:today',
                'gender' => 'sometimes|string|in:Male,Female,Other',
                'marital_status' => 'sometimes|string|in:Single,Married,De Facto,Divorced,Widowed,Separated',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $userId = Auth::id() ?? $clientId;
            $updatedFields = [];

            // Field mapping from request key to meta_key
            $fieldMapping = [
                'first_name' => 'first_name',
                'last_name' => 'last_name',
                'date_of_birth' => 'dob',
                'gender' => 'gender',
                'marital_status' => 'marital_status',
            ];

            DB::beginTransaction();

            try {
                foreach ($fieldMapping as $requestKey => $metaKey) {
                    if (isset($requestData[$requestKey])) {
                        $newValue = $requestData[$requestKey];
                        
                        // Handle date_of_birth conversion from dd/mm/yyyy to database format
                        if ($requestKey === 'date_of_birth' && $newValue) {
                            try {
                                $date = Carbon::createFromFormat('d/m/Y', $newValue);
                                $newValue = $date->format('Y-m-d');
                            } catch (\Exception $e) {
                                DB::rollBack();
                                return response()->json([
                                    'success' => false,
                                    'message' => 'Invalid date format for date_of_birth. Expected format: dd/mm/yyyy'
                                ], 422);
                            }
                        }

                        // Get current value (from admins table or latest audit)
                        $currentValue = $this->getCurrentFieldValue($clientId, $metaKey, $admin);
                        
                        // Convert current value to string for comparison
                        $currentValueStr = (string) $currentValue;
                        $newValueStr = (string) $newValue;
                        
                        // Only save to audit if value has changed
                        if ($currentValueStr !== $newValueStr) {
                            // Save to audit table
                            ClientPortalDetailAudit::create([
                                'client_id' => $clientId,
                                'meta_key' => $metaKey,
                                'old_value' => $currentValueStr,
                                'new_value' => $newValueStr,
                                'meta_order' => 0,
                                'meta_type' => null,
                                'action' => 'update',
                                'updated_by' => $userId,
                                'updated_at' => now(),
                            ]);
                            
                            $updatedFields[$metaKey] = $newValueStr;
                        }

                        // If DOB is updated, recalculate age and save to audit
                        if ($metaKey === 'dob' && $newValue) {
                            $calculatedAge = $this->calculateAge($newValue, null);
                            
                            // Get current age value
                            $currentAge = $this->getCurrentFieldValue($clientId, 'age', $admin, $calculatedAge);
                            
                            // Save age update to audit if changed
                            if ((string) $currentAge !== (string) $calculatedAge) {
                                ClientPortalDetailAudit::create([
                                    'client_id' => $clientId,
                                    'meta_key' => 'age',
                                    'old_value' => (string) $currentAge,
                                    'new_value' => (string) $calculatedAge,
                                    'meta_order' => 0,
                                    'meta_type' => null,
                                    'action' => 'update',
                                    'updated_by' => $userId,
                                    'updated_at' => now(),
                                ]);
                                
                            $updatedFields['age'] = $calculatedAge;
                            }
                        }
                    }
                }

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Client basic details updated successfully',
                    'data' => [
                        'updated_fields' => $updatedFields
                    ]
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get current field value from admins table or latest audit entry
     * 
     * @param int $clientId
     * @param string $metaKey
     * @param Admin $admin
     * @param mixed $defaultValue
     * @return mixed
     */
    private function getCurrentFieldValue($clientId, $metaKey, Admin $admin, $defaultValue = null)
    {
        // First check latest audit entry
        $latestAudit = ClientPortalDetailAudit::where('client_id', $clientId)
            ->where('meta_key', $metaKey)
            ->orderBy('updated_at', 'desc')
            ->first();
        
        if ($latestAudit && $latestAudit->new_value !== null) {
            return $latestAudit->new_value;
        }
        
        // If no audit entry, get from admins table
        $fieldMapping = [
            'first_name' => 'first_name',
            'last_name' => 'last_name',
            'client_id' => 'client_id',
            'dob' => 'dob',
            'age' => 'age',
            'gender' => 'gender',
            'marital_status' => 'marital_status',
        ];
        
        $adminField = $fieldMapping[$metaKey] ?? null;
        
        if ($adminField && isset($admin->$adminField)) {
            return $admin->$adminField;
        }
        
        return $defaultValue;
    }

    /**
     * Calculate age from date of birth
     * 
     * @param string|null $dob
     * @param mixed $existingAge
     * @return mixed
     */
    private function calculateAge($dob, $existingAge = null)
    {
        if ($dob) {
            try {
                $dobDate = Carbon::parse($dob);
                return $dobDate->diff(Carbon::now())->format('%y years %m months');
            } catch (\Exception $e) {
                return $existingAge;
            }
        }
        return $existingAge;
    }

    /**
     * Format date to dd/mm/yyyy
     * 
     * @param string|null $date
     * @return string|null
     */
    private function formatDate($date)
    {
        if (!$date) {
            return null;
        }

        try {
            return Carbon::parse($date)->format('d/m/Y');
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get phones data - from audit table if updated, otherwise from client_contacts table
     * 
     * @param int $clientId
     * @return array
     */
    private function getPhonesData($clientId)
    {
        // Check if there are any phone audit entries
        $hasPhoneAudits = ClientPortalDetailAudit::where('client_id', $clientId)
            ->whereIn('meta_key', ['phone', 'phone_country_code', 'phone_extension'])
                ->exists();

        if ($hasPhoneAudits) {
            // Fetch from audit table
            return $this->getPhonesFromAudit($clientId);
        } else {
            // Fetch from client_contacts table
            return $this->getPhonesFromSource($clientId);
        }
    }

    /**
     * Get phones from audit table
     * 
     * @param int $clientId
     * @return array
     */
    private function getPhonesFromAudit($clientId)
    {
        // Get all phone-related audit entries
        $auditEntries = ClientPortalDetailAudit::where('client_id', $clientId)
            ->whereIn('meta_key', ['phone', 'phone_country_code', 'phone_extension'])
                ->get();

        // Group by meta_order and meta_key, taking the latest new_value for each combination
        $phoneData = [];
        $phoneCountryCodes = [];
        $phoneExtensions = [];

        // Track latest entry for each meta_order + meta_key combination
        $latestEntries = [];

        foreach ($auditEntries as $entry) {
            $order = $entry->meta_order;
            $key = $entry->meta_key;
            $processedKey = $order . '_' . $key;
            
            // Store the latest entry for this combination
            if (!isset($latestEntries[$processedKey])) {
                $latestEntries[$processedKey] = $entry;
            } else {
                // Compare updated_at to keep the latest
                if (strtotime($entry->updated_at) > strtotime($latestEntries[$processedKey]->updated_at)) {
                    $latestEntries[$processedKey] = $entry;
                }
            }
        }

        // Process latest entries
        foreach ($latestEntries as $processedKey => $entry) {
            $order = $entry->meta_order;
            $key = $entry->meta_key;
            
            switch ($key) {
                case 'phone':
                    if (!isset($phoneData[$order])) {
                        $phoneData[$order] = [
                            'id' => null, // Audit doesn't have original id
                            'phone' => $entry->new_value,
                            'type' => $entry->meta_type ?? 'Mobile',
                            'country_code' => null,
                            'extension' => null,
                        ];
                    }
                    break;
                case 'phone_country_code':
                    $phoneCountryCodes[$order] = $entry->new_value;
                    break;
                case 'phone_extension':
                    $phoneExtensions[$order] = $entry->new_value;
                    break;
            }
        }

        // Merge country codes and extensions into phone data
        foreach ($phoneData as $order => &$phone) {
            if (isset($phoneCountryCodes[$order])) {
                $phone['country_code'] = $phoneCountryCodes[$order];
            }
            if (isset($phoneExtensions[$order])) {
                $phone['extension'] = $phoneExtensions[$order];
            }
        }

        // Determine primary phone (Personal type first, then first in list)
        $phones = array_values($phoneData);
        foreach ($phones as $index => &$phone) {
            $phoneType = strtolower($phone['type'] ?? '');
            $phone['is_primary'] = ($phoneType === 'personal') ? true : false;
        }

        // If no Personal type found, mark first as primary
        $hasPersonal = false;
        foreach ($phones as $phone) {
            if (strtolower($phone['type'] ?? '') === 'personal') {
                $hasPersonal = true;
                break;
            }
        }
        if (!$hasPersonal && !empty($phones)) {
            $phones[0]['is_primary'] = true;
        }

        return $phones;
    }

    /**
     * Get phones from client_contacts source table
     * 
     * @param int $clientId
     * @return array
     */
    private function getPhonesFromSource($clientId)
    {
        $phones = [];
        
        $clientPhones = DB::table('client_contacts')
            ->where('client_id', $clientId)
            ->orderBy('id')
            ->get();

        foreach ($clientPhones as $index => $phone) {
            $phoneType = $phone->contact_type ?? 'Mobile';
            $phones[] = [
                'id' => $phone->id,
                'phone' => $phone->phone ?? '',
                'type' => $phoneType,
                'country_code' => $phone->country_code ?? null,
                'extension' => $phone->extension ?? null,
                'is_primary' => false, // Will be set below
            ];
        }

        // Determine primary phone (Personal type first, then first in list)
        $personalFound = false;
        foreach ($phones as $index => &$phone) {
            $phoneType = strtolower($phone['type'] ?? '');
            if ($phoneType === 'personal') {
                $phone['is_primary'] = true;
                $personalFound = true;
            }
        }

        // If no Personal type found, mark first as primary
        if (!$personalFound && !empty($phones)) {
            $phones[0]['is_primary'] = true;
        }

        return $phones;
    }

    /**
     * Get emails data - from audit table if updated, otherwise from client_emails table
     * 
     * @param int $clientId
     * @return array
     */
    private function getEmailsData($clientId)
    {
        // Check if there are any email audit entries
        $hasEmailAudits = ClientPortalDetailAudit::where('client_id', $clientId)
            ->where('meta_key', 'email')
            ->exists();

        if ($hasEmailAudits) {
            // Fetch from audit table
            return $this->getEmailsFromAudit($clientId);
                    } else {
            // Fetch from client_emails table
            return $this->getEmailsFromSource($clientId);
        }
    }

    /**
     * Get emails from audit table
     * 
     * @param int $clientId
     * @return array
     */
    private function getEmailsFromAudit($clientId)
    {
        // Get all email audit entries
        $auditEntries = ClientPortalDetailAudit::where('client_id', $clientId)
            ->where('meta_key', 'email')
            ->get();

        // Group by meta_order, taking the latest new_value for each
        $emailData = [];
        $latestEntries = []; // Track latest entry for each meta_order

        foreach ($auditEntries as $entry) {
            $order = $entry->meta_order;
            
            // Store the latest entry for this meta_order
            if (!isset($latestEntries[$order])) {
                $latestEntries[$order] = $entry;
            } else {
                // Compare updated_at to keep the latest
                if (strtotime($entry->updated_at) > strtotime($latestEntries[$order]->updated_at)) {
                    $latestEntries[$order] = $entry;
                }
            }
        }

        // Process latest entries
        foreach ($latestEntries as $order => $entry) {
            $emailData[$order] = [
                'id' => null, // Audit doesn't have original id
                'email' => $entry->new_value,
                'type' => $entry->meta_type ?? 'Personal',
            ];
        }

        // Convert to indexed array
        $emails = array_values($emailData);

        // Determine primary email (Personal type first, then first in list)
        foreach ($emails as $index => &$email) {
            $emailType = strtolower($email['type'] ?? '');
            $email['is_primary'] = ($emailType === 'personal') ? true : false;
        }

        // If no Personal type found, mark first as primary
        $hasPersonal = false;
        foreach ($emails as $email) {
            if (strtolower($email['type'] ?? '') === 'personal') {
                $hasPersonal = true;
                break;
            }
        }
        if (!$hasPersonal && !empty($emails)) {
            $emails[0]['is_primary'] = true;
        }

        return $emails;
    }

    /**
     * Get emails from client_emails source table
     * 
     * @param int $clientId
     * @return array
     */
    private function getEmailsFromSource($clientId)
    {
        $emails = [];
        
        $clientEmails = DB::table('client_emails')
            ->where('client_id', $clientId)
            ->orderBy('id')
            ->get();

        foreach ($clientEmails as $email) {
            $emailType = $email->email_type ?? 'Personal';
            $emails[] = [
                'id' => $email->id,
                'email' => $email->email ?? '',
                'type' => $emailType,
                'is_primary' => false, // Will be set below
            ];
        }

        // Determine primary email (Personal type first, then first in list)
        $personalFound = false;
        foreach ($emails as $index => &$email) {
            $emailType = strtolower($email['type'] ?? '');
            if ($emailType === 'personal') {
                $email['is_primary'] = true;
                $personalFound = true;
            }
        }

        // If no Personal type found, mark first as primary
        if (!$personalFound && !empty($emails)) {
            $emails[0]['is_primary'] = true;
        }

        return $emails;
    }

    /**
     * Get passports data - from audit table if updated, otherwise from client_passport_informations table
     * 
     * @param int $clientId
     * @return array
     */
    private function getPassportsData($clientId)
    {
        // Check if there are any passport audit entries
        $hasPassportAudits = ClientPortalDetailAudit::where('client_id', $clientId)
            ->whereIn('meta_key', ['passport', 'passport_country', 'passport_issue_date', 'passport_expiry_date'])
            ->exists();

        if ($hasPassportAudits) {
            // Fetch from audit table
            return $this->getPassportsFromAudit($clientId);
        } else {
            // Fetch from client_passport_informations table
            return $this->getPassportsFromSource($clientId);
        }
    }

    /**
     * Get passports from audit table
     * 
     * @param int $clientId
     * @return array
     */
    private function getPassportsFromAudit($clientId)
    {
        // Get all passport-related audit entries
        $auditEntries = ClientPortalDetailAudit::where('client_id', $clientId)
            ->whereIn('meta_key', ['passport', 'passport_country', 'passport_issue_date', 'passport_expiry_date'])
            ->get();

        // Group by meta_order and meta_key, taking the latest new_value for each combination
        $passportData = [];
        $latestEntries = []; // Track latest entry for each meta_order + meta_key combination

        foreach ($auditEntries as $entry) {
            $order = $entry->meta_order;
            $key = $entry->meta_key;
            $processedKey = $order . '_' . $key;
            
            // Store the latest entry for this combination
            if (!isset($latestEntries[$processedKey])) {
                $latestEntries[$processedKey] = $entry;
            } else {
                // Compare updated_at to keep the latest
                if (strtotime($entry->updated_at) > strtotime($latestEntries[$processedKey]->updated_at)) {
                    $latestEntries[$processedKey] = $entry;
                }
            }
        }

        // Process latest entries
        foreach ($latestEntries as $processedKey => $entry) {
            $order = $entry->meta_order;
            $key = $entry->meta_key;
            
            if (!isset($passportData[$order])) {
                $passportData[$order] = [
                    'id' => null, // Audit doesn't have original id
                    'passport_number' => null,
                    'country' => null,
                    'issue_date' => null,
                    'expiry_date' => null,
                ];
            }
            
            switch ($key) {
                case 'passport':
                    $passportData[$order]['passport_number'] = $entry->new_value;
                    break;
                case 'passport_country':
                    $passportData[$order]['country'] = $entry->new_value;
                    break;
                case 'passport_issue_date':
                    $passportData[$order]['issue_date'] = $entry->new_value ? $this->formatDate($entry->new_value) : null;
                    break;
                case 'passport_expiry_date':
                    $passportData[$order]['expiry_date'] = $entry->new_value ? $this->formatDate($entry->new_value) : null;
                    break;
            }
        }

        // Convert to indexed array and filter out entries without passport_number
        $passports = [];
        foreach ($passportData as $order => $passport) {
            if (!empty($passport['passport_number'])) {
                $passports[] = $passport;
            }
        }

        return $passports;
    }

    /**
     * Get passports from client_passport_informations source table
     * 
     * @param int $clientId
     * @return array
     */
    private function getPassportsFromSource($clientId)
    {
        $passports = [];
        
        $clientPassports = DB::table('client_passport_informations')
            ->where('client_id', $clientId)
            ->orderBy('id')
            ->get();

        foreach ($clientPassports as $passport) {
            $passports[] = [
                'id' => $passport->id,
                'passport_number' => $passport->passport ?? '',
                'country' => $passport->passport_country ?? null,
                'issue_date' => $passport->passport_issue_date ? $this->formatDate($passport->passport_issue_date) : null,
                'expiry_date' => $passport->passport_expiry_date ? $this->formatDate($passport->passport_expiry_date) : null,
            ];
        }

        return $passports;
    }

    /**
     * Get visas data - from audit table if updated, otherwise from client_visa_countries table
     * 
     * @param int $clientId
     * @return array
     */
    private function getVisasData($clientId)
    {
        // Check if there are any visa audit entries
        $hasVisaAudits = ClientPortalDetailAudit::where('client_id', $clientId)
            ->whereIn('meta_key', ['visa', 'visa_country', 'visa_type', 'visa_description', 'visa_expiry_date', 'visa_grant_date'])
            ->exists();

        if ($hasVisaAudits) {
            // Fetch from audit table
            return $this->getVisasFromAudit($clientId);
        } else {
            // Fetch from client_visa_countries table
            return $this->getVisasFromSource($clientId);
        }
    }

    /**
     * Get visas from audit table
     * 
     * @param int $clientId
     * @return array
     */
    private function getVisasFromAudit($clientId)
    {
        // Get all visa-related audit entries
        $auditEntries = ClientPortalDetailAudit::where('client_id', $clientId)
            ->whereIn('meta_key', ['visa', 'visa_country', 'visa_type', 'visa_description', 'visa_expiry_date', 'visa_grant_date'])
            ->get();

        // Group by meta_order and meta_key, taking the latest new_value for each combination
        $visaData = [];
        $latestEntries = []; // Track latest entry for each meta_order + meta_key combination

        foreach ($auditEntries as $entry) {
            $order = $entry->meta_order;
            $key = $entry->meta_key;
            $processedKey = $order . '_' . $key;
            
            // Store the latest entry for this combination
            if (!isset($latestEntries[$processedKey])) {
                $latestEntries[$processedKey] = $entry;
            } else {
                // Compare updated_at to keep the latest
                if (strtotime($entry->updated_at) > strtotime($latestEntries[$processedKey]->updated_at)) {
                    $latestEntries[$processedKey] = $entry;
                }
            }
        }

        // Process latest entries
        foreach ($latestEntries as $processedKey => $entry) {
            $order = $entry->meta_order;
            $key = $entry->meta_key;
            
            // Initialize visa entry if not exists
            if (!isset($visaData[$order])) {
                $visaData[$order] = [
                    'id' => null, // Audit doesn't have original id
                    'visa_country' => null,
                    'visa_type' => null,
                    'visa_description' => null,
                    'visa_expiry_date' => null,
                    'visa_grant_date' => null,
                ];
            }
            
            switch ($key) {
                case 'visa':
                    // Visa marker - just confirms this visa entry exists
                    break;
                case 'visa_country':
                    $visaData[$order]['visa_country'] = $entry->new_value;
                    break;
                case 'visa_type':
                    $visaData[$order]['visa_type'] = $entry->new_value;
                    break;
                case 'visa_description':
                    $visaData[$order]['visa_description'] = $entry->new_value;
                    break;
                case 'visa_expiry_date':
                    $visaData[$order]['visa_expiry_date'] = $entry->new_value ? $this->formatDate($entry->new_value) : null;
                    break;
                case 'visa_grant_date':
                    $visaData[$order]['visa_grant_date'] = $entry->new_value ? $this->formatDate($entry->new_value) : null;
                    break;
            }
        }

        // Convert to indexed array and filter out entries without at least visa_country or visa_type
        $visas = [];
        foreach ($visaData as $order => $visa) {
            // Include visa if it has at least country or type
            if (!empty($visa['visa_country']) || !empty($visa['visa_type'])) {
                $visas[] = $visa;
            }
        }

        return $visas;
    }

    /**
     * Get visas from client_visa_countries source table
     * 
     * @param int $clientId
     * @return array
     */
    private function getVisasFromSource($clientId)
    {
        $visas = [];
        
        $clientVisas = DB::table('client_visa_countries')
            ->where('client_id', $clientId)
            ->orderBy('id')
            ->get();

        foreach ($clientVisas as $visa) {
            $visas[] = [
                'id' => $visa->id,
                'visa_country' => $visa->visa_country ?? null,
                'visa_type' => $visa->visa_type ?? null,
                'visa_description' => $visa->visa_description ?? null,
                'visa_expiry_date' => $visa->visa_expiry_date && $visa->visa_expiry_date != '0000-00-00' ? $this->formatDate($visa->visa_expiry_date) : null,
                'visa_grant_date' => $visa->visa_grant_date ? $this->formatDate($visa->visa_grant_date) : null,
            ];
        }

        return $visas;
    }

    /**
     * Get addresses data - from audit table if updated, otherwise from client_addresses table
     * 
     * @param int $clientId
     * @return array
     */
    private function getAddressesData($clientId)
    {
        // Check if there are any address audit entries
        $hasAddressAudits = ClientPortalDetailAudit::where('client_id', $clientId)
            ->whereIn('meta_key', ['address', 'address_line_1', 'address_line_2', 'address_suburb', 'address_state', 'address_postcode', 'address_country', 'address_regional_code', 'address_start_date', 'address_end_date', 'address_is_current'])
            ->exists();

        if ($hasAddressAudits) {
            // Fetch from audit table
            return $this->getAddressesFromAudit($clientId);
        } else {
            // Fetch from client_addresses table
            return $this->getAddressesFromSource($clientId);
        }
    }

    /**
     * Get addresses from audit table
     * 
     * @param int $clientId
     * @return array
     */
    private function getAddressesFromAudit($clientId)
    {
        // Get all address-related audit entries
        $auditEntries = ClientPortalDetailAudit::where('client_id', $clientId)
            ->whereIn('meta_key', ['address', 'address_line_1', 'address_line_2', 'address_suburb', 'address_state', 'address_postcode', 'address_country', 'address_regional_code', 'address_start_date', 'address_end_date', 'address_is_current'])
            ->get();

        // Group by meta_order and meta_key, taking the latest new_value for each combination
        $addressData = [];
        $latestEntries = []; // Track latest entry for each meta_order + meta_key combination

        foreach ($auditEntries as $entry) {
            $order = $entry->meta_order;
            $key = $entry->meta_key;
            $processedKey = $order . '_' . $key;
            
            // Store the latest entry for this combination
            if (!isset($latestEntries[$processedKey])) {
                $latestEntries[$processedKey] = $entry;
            } else {
                // Compare updated_at to keep the latest
                if (strtotime($entry->updated_at) > strtotime($latestEntries[$processedKey]->updated_at)) {
                    $latestEntries[$processedKey] = $entry;
                }
            }
        }

        // Process latest entries
        foreach ($latestEntries as $processedKey => $entry) {
            $order = $entry->meta_order;
            $key = $entry->meta_key;
            
            if (!isset($addressData[$order])) {
                $addressData[$order] = [
                    'id' => null, // Audit doesn't have original id
                    'search_address' => null,
                    'address_line_1' => null,
                    'address_line_2' => null,
                    'suburb' => null,
                    'state' => null,
                    'postcode' => null,
                    'country' => null,
                    'regional_code' => null,
                    'start_date' => null,
                    'end_date' => null,
                    'is_current' => false,
                ];
            }
            
            switch ($key) {
                case 'address':
                    $addressData[$order]['search_address'] = $entry->new_value;
                    break;
                case 'address_line_1':
                    $addressData[$order]['address_line_1'] = $entry->new_value;
                    break;
                case 'address_line_2':
                    $addressData[$order]['address_line_2'] = $entry->new_value;
                    break;
                case 'address_suburb':
                    $addressData[$order]['suburb'] = $entry->new_value;
                    break;
                case 'address_state':
                    $addressData[$order]['state'] = $entry->new_value;
                    break;
                case 'address_postcode':
                    $addressData[$order]['postcode'] = $entry->new_value;
                    break;
                case 'address_country':
                    $addressData[$order]['country'] = $entry->new_value;
                    break;
                case 'address_regional_code':
                    $addressData[$order]['regional_code'] = $entry->new_value;
                    break;
                case 'address_start_date':
                    $addressData[$order]['start_date'] = $entry->new_value ? $this->formatDate($entry->new_value) : null;
                    break;
                case 'address_end_date':
                    $addressData[$order]['end_date'] = $entry->new_value ? $this->formatDate($entry->new_value) : null;
                    break;
                case 'address_is_current':
                    $addressData[$order]['is_current'] = ($entry->new_value == '1' || $entry->new_value == 1) ? true : false;
                    break;
            }
        }

        // Convert to indexed array and filter out entries without at least search_address or address_line_1
        $addresses = [];
        foreach ($addressData as $order => $address) {
            // Include address if it has at least search_address or address_line_1
            if (!empty($address['search_address']) || !empty($address['address_line_1'])) {
                $addresses[] = $address;
            }
        }

        return $addresses;
    }

    /**
     * Get addresses from client_addresses source table
     * 
     * @param int $clientId
     * @return array
     */
    private function getAddressesFromSource($clientId)
    {
        $addresses = [];
        
        $clientAddresses = DB::table('client_addresses')
            ->where('client_id', $clientId)
            ->orderBy('id')
            ->get();

        foreach ($clientAddresses as $address) {
            $addresses[] = [
                'id' => $address->id,
                'search_address' => $address->address ?? null,
                'address_line_1' => $address->address_line_1 ?? null,
                'address_line_2' => $address->address_line_2 ?? null,
                'suburb' => $address->suburb ?? null,
                'state' => $address->state ?? null,
                'postcode' => $address->zip ?? null,
                'country' => $address->country ?? null,
                'regional_code' => $address->regional_code ?? null,
                'start_date' => $address->start_date ? $this->formatDate($address->start_date) : null,
                'end_date' => $address->end_date ? $this->formatDate($address->end_date) : null,
                'is_current' => ($address->is_current == 1) ? true : false,
            ];
        }

        return $addresses;
    }

    /**
     * Get travels data - from audit table if updated, otherwise from client_travel_informations table
     * 
     * @param int $clientId
     * @return array
     */
    private function getTravelsData($clientId)
    {
        // Check if there are any travel audit entries
        $hasTravelAudits = ClientPortalDetailAudit::where('client_id', $clientId)
            ->whereIn('meta_key', ['travel', 'travel_country_visited', 'travel_arrival_date', 'travel_departure_date', 'travel_purpose'])
            ->exists();

        if ($hasTravelAudits) {
            // Fetch from audit table
            return $this->getTravelsFromAudit($clientId);
        } else {
            // Fetch from client_travel_informations table
            return $this->getTravelsFromSource($clientId);
        }
    }

    /**
     * Get travels from audit table
     * 
     * @param int $clientId
     * @return array
     */
    private function getTravelsFromAudit($clientId)
    {
        // Get all travel-related audit entries
        $auditEntries = ClientPortalDetailAudit::where('client_id', $clientId)
            ->whereIn('meta_key', ['travel', 'travel_country_visited', 'travel_arrival_date', 'travel_departure_date', 'travel_purpose'])
            ->get();

        // Group by meta_order and meta_key, taking the latest new_value for each combination
        $travelData = [];
        $latestEntries = []; // Track latest entry for each meta_order + meta_key combination

        foreach ($auditEntries as $entry) {
            $order = $entry->meta_order;
            $key = $entry->meta_key;
            $processedKey = $order . '_' . $key;
            
            // Store the latest entry for this combination
            if (!isset($latestEntries[$processedKey])) {
                $latestEntries[$processedKey] = $entry;
            } else {
                // Compare updated_at to keep the latest
                if (strtotime($entry->updated_at) > strtotime($latestEntries[$processedKey]->updated_at)) {
                    $latestEntries[$processedKey] = $entry;
                }
            }
        }

        // Process latest entries
        foreach ($latestEntries as $processedKey => $entry) {
            $order = $entry->meta_order;
            $key = $entry->meta_key;
            
            // Initialize travel entry if not exists
            if (!isset($travelData[$order])) {
                $travelData[$order] = [
                    'id' => null, // Audit doesn't have original id
                    'country_visited' => null,
                    'arrival_date' => null,
                    'departure_date' => null,
                    'purpose' => null,
                ];
            }
            
            switch ($key) {
                case 'travel':
                    // Travel marker - just confirms this travel entry exists
                    break;
                case 'travel_country_visited':
                    $travelData[$order]['country_visited'] = $entry->new_value;
                    break;
                case 'travel_arrival_date':
                    $travelData[$order]['arrival_date'] = $entry->new_value ? $this->formatDate($entry->new_value) : null;
                    break;
                case 'travel_departure_date':
                    $travelData[$order]['departure_date'] = $entry->new_value ? $this->formatDate($entry->new_value) : null;
                    break;
                case 'travel_purpose':
                    $travelData[$order]['purpose'] = $entry->new_value;
                    break;
            }
        }

        // Convert to indexed array and filter out entries without at least country_visited
        $travels = [];
        foreach ($travelData as $order => $travel) {
            // Include travel if it has country_visited
            if (!empty($travel['country_visited'])) {
                $travels[] = $travel;
            }
        }

        return $travels;
    }

    /**
     * Get travels from client_travel_informations source table
     * 
     * @param int $clientId
     * @return array
     */
    private function getTravelsFromSource($clientId)
    {
        $travels = [];
        
        $clientTravels = DB::table('client_travel_informations')
            ->where('client_id', $clientId)
            ->orderBy('id')
            ->get();

        foreach ($clientTravels as $travel) {
            $travels[] = [
                'id' => $travel->id,
                'country_visited' => $travel->travel_country_visited ?? null,
                'arrival_date' => $travel->travel_arrival_date ? $this->formatDate($travel->travel_arrival_date) : null,
                'departure_date' => $travel->travel_departure_date ? $this->formatDate($travel->travel_departure_date) : null,
                'purpose' => $travel->travel_purpose ?? null,
            ];
        }

        return $travels;
    }

    /**
     * Get qualifications data - from audit table if updated, otherwise from client_qualifications table
     * 
     * @param int $clientId
     * @return array
     */
    private function getQualificationsData($clientId)
    {
        // Check if there are any qualification audit entries
        $hasQualificationAudits = ClientPortalDetailAudit::where('client_id', $clientId)
            ->whereIn('meta_key', ['qualification', 'qualification_level', 'qualification_name', 'qualification_college_name', 'qualification_campus', 'qualification_country', 'qualification_state', 'qualification_start_date', 'qualification_finish_date', 'qualification_relevant', 'qualification_specialist_education', 'qualification_stem', 'qualification_regional_study'])
            ->exists();

        if ($hasQualificationAudits) {
            // Fetch from audit table
            return $this->getQualificationsFromAudit($clientId);
        } else {
            // Fetch from client_qualifications table
            return $this->getQualificationsFromSource($clientId);
        }
    }

    /**
     * Get qualifications from audit table
     * 
     * @param int $clientId
     * @return array
     */
    private function getQualificationsFromAudit($clientId)
    {
        // Get all qualification-related audit entries
        $auditEntries = ClientPortalDetailAudit::where('client_id', $clientId)
            ->whereIn('meta_key', ['qualification', 'qualification_level', 'qualification_name', 'qualification_college_name', 'qualification_campus', 'qualification_country', 'qualification_state', 'qualification_start_date', 'qualification_finish_date', 'qualification_relevant', 'qualification_specialist_education', 'qualification_stem', 'qualification_regional_study'])
            ->get();

        // Group by meta_order and meta_key, taking the latest new_value for each combination
        $qualificationData = [];
        $latestEntries = []; // Track latest entry for each meta_order + meta_key combination

        foreach ($auditEntries as $entry) {
            $order = $entry->meta_order;
            $key = $entry->meta_key;
            $processedKey = $order . '_' . $key;
            
            // Store the latest entry for this combination
            if (!isset($latestEntries[$processedKey])) {
                $latestEntries[$processedKey] = $entry;
            } else {
                // Compare updated_at to keep the latest
                if (strtotime($entry->updated_at) > strtotime($latestEntries[$processedKey]->updated_at)) {
                    $latestEntries[$processedKey] = $entry;
                }
            }
        }

        // Process latest entries
        foreach ($latestEntries as $processedKey => $entry) {
            $order = $entry->meta_order;
            $key = $entry->meta_key;
            
            // Initialize qualification entry if not exists
            if (!isset($qualificationData[$order])) {
                $qualificationData[$order] = [
                    'id' => null, // Audit doesn't have original id
                    'level' => null,
                    'name' => null,
                    'college_name' => null,
                    'campus' => null,
                    'country' => null,
                    'state' => null,
                    'start_date' => null,
                    'finish_date' => null,
                    'relevant_qualification' => false,
                    'specialist_education' => false,
                    'stem_qualification' => false,
                    'regional_study' => false,
                ];
            }
            
            switch ($key) {
                case 'qualification':
                    // Qualification marker - just confirms this qualification entry exists
                    break;
                case 'qualification_level':
                    $qualificationData[$order]['level'] = $entry->new_value;
                    break;
                case 'qualification_name':
                    $qualificationData[$order]['name'] = $entry->new_value;
                    break;
                case 'qualification_college_name':
                    $qualificationData[$order]['college_name'] = $entry->new_value;
                    break;
                case 'qualification_campus':
                    $qualificationData[$order]['campus'] = $entry->new_value;
                    break;
                case 'qualification_country':
                    $qualificationData[$order]['country'] = $entry->new_value;
                    break;
                case 'qualification_state':
                    $qualificationData[$order]['state'] = $entry->new_value;
                    break;
                case 'qualification_start_date':
                    $qualificationData[$order]['start_date'] = $entry->new_value ? $this->formatDate($entry->new_value) : null;
                    break;
                case 'qualification_finish_date':
                    $qualificationData[$order]['finish_date'] = $entry->new_value ? $this->formatDate($entry->new_value) : null;
                    break;
                case 'qualification_relevant':
                    $qualificationData[$order]['relevant_qualification'] = ($entry->new_value == '1' || $entry->new_value == 1) ? true : false;
                    break;
                case 'qualification_specialist_education':
                    $qualificationData[$order]['specialist_education'] = ($entry->new_value == '1' || $entry->new_value == 1) ? true : false;
                    break;
                case 'qualification_stem':
                    $qualificationData[$order]['stem_qualification'] = ($entry->new_value == '1' || $entry->new_value == 1) ? true : false;
                    break;
                case 'qualification_regional_study':
                    $qualificationData[$order]['regional_study'] = ($entry->new_value == '1' || $entry->new_value == 1) ? true : false;
                    break;
            }
        }

        // Convert to indexed array and filter out entries without at least level or name
        $qualifications = [];
        foreach ($qualificationData as $order => $qualification) {
            // Include qualification if it has at least level or name
            if (!empty($qualification['level']) || !empty($qualification['name'])) {
                $qualifications[] = $qualification;
            }
        }

        return $qualifications;
    }

    /**
     * Get qualifications from client_qualifications source table
     * 
     * @param int $clientId
     * @return array
     */
    private function getQualificationsFromSource($clientId)
    {
        $qualifications = [];
        
        $clientQualifications = DB::table('client_qualifications')
            ->where('client_id', $clientId)
            ->orderBy('id')
            ->get();

        foreach ($clientQualifications as $qualification) {
            $qualifications[] = [
                'id' => $qualification->id,
                'level' => $qualification->level ?? null,
                'name' => $qualification->name ?? null,
                'college_name' => $qualification->qual_college_name ?? null,
                'campus' => $qualification->qual_campus ?? null,
                'country' => $qualification->country ?? null,
                'state' => $qualification->qual_state ?? null,
                'start_date' => $qualification->start_date ? $this->formatDate($qualification->start_date) : null,
                'finish_date' => $qualification->finish_date ? $this->formatDate($qualification->finish_date) : null,
                'relevant_qualification' => ($qualification->relevant_qualification == 1) ? true : false,
                'specialist_education' => ($qualification->specialist_education == 1) ? true : false,
                'stem_qualification' => ($qualification->stem_qualification == 1) ? true : false,
                'regional_study' => ($qualification->regional_study == 1) ? true : false,
            ];
        }

        return $qualifications;
    }

    /**
     * Get experiences data - from audit table if updated, otherwise from client_experiences table
     * 
     * @param int $clientId
     * @return array
     */
    private function getExperiencesData($clientId)
    {
        // Check if there are any experience audit entries
        $hasExperienceAudits = ClientPortalDetailAudit::where('client_id', $clientId)
            ->whereIn('meta_key', ['experience', 'experience_job_title', 'experience_job_code', 'experience_country', 'experience_start_date', 'experience_finish_date', 'experience_relevant', 'experience_employer_name', 'experience_state', 'experience_job_type', 'experience_fte_multiplier'])
            ->exists();

        if ($hasExperienceAudits) {
            // Fetch from audit table
            return $this->getExperiencesFromAudit($clientId);
        } else {
            // Fetch from client_experiences table
            return $this->getExperiencesFromSource($clientId);
        }
    }

    /**
     * Get experiences from audit table
     * 
     * @param int $clientId
     * @return array
     */
    private function getExperiencesFromAudit($clientId)
    {
        // Get all experience-related audit entries
        $auditEntries = ClientPortalDetailAudit::where('client_id', $clientId)
            ->whereIn('meta_key', ['experience', 'experience_job_title', 'experience_job_code', 'experience_country', 'experience_start_date', 'experience_finish_date', 'experience_relevant', 'experience_employer_name', 'experience_state', 'experience_job_type', 'experience_fte_multiplier'])
            ->get();

        // Group by meta_order and meta_key, taking the latest new_value for each combination
        $experienceData = [];
        $latestEntries = []; // Track latest entry for each meta_order + meta_key combination

        foreach ($auditEntries as $entry) {
            $order = $entry->meta_order;
            $key = $entry->meta_key;
            $processedKey = $order . '_' . $key;
            
            // Store the latest entry for this combination
            if (!isset($latestEntries[$processedKey])) {
                $latestEntries[$processedKey] = $entry;
            } else {
                // Compare updated_at to keep the latest
                if (strtotime($entry->updated_at) > strtotime($latestEntries[$processedKey]->updated_at)) {
                    $latestEntries[$processedKey] = $entry;
                }
            }
        }

        // Process latest entries
        foreach ($latestEntries as $processedKey => $entry) {
            $order = $entry->meta_order;
            $key = $entry->meta_key;
            
            // Initialize experience entry if not exists
            if (!isset($experienceData[$order])) {
                $experienceData[$order] = [
                    'id' => null, // Audit doesn't have original id
                    'job_title' => null,
                    'job_code' => null,
                    'country' => null,
                    'start_date' => null,
                    'finish_date' => null,
                    'relevant_experience' => false,
                    'employer_name' => null,
                    'state' => null,
                    'job_type' => null,
                    'fte_multiplier' => null,
                ];
            }
            
            switch ($key) {
                case 'experience':
                    // Experience marker - just confirms this experience entry exists
                    break;
                case 'experience_job_title':
                    $experienceData[$order]['job_title'] = $entry->new_value;
                    break;
                case 'experience_job_code':
                    $experienceData[$order]['job_code'] = $entry->new_value;
                    break;
                case 'experience_country':
                    $experienceData[$order]['country'] = $entry->new_value;
                    break;
                case 'experience_start_date':
                    $experienceData[$order]['start_date'] = $entry->new_value ? $this->formatDate($entry->new_value) : null;
                    break;
                case 'experience_finish_date':
                    $experienceData[$order]['finish_date'] = $entry->new_value ? $this->formatDate($entry->new_value) : null;
                    break;
                case 'experience_relevant':
                    $experienceData[$order]['relevant_experience'] = ($entry->new_value == '1' || $entry->new_value == 1) ? true : false;
                    break;
                case 'experience_employer_name':
                    $experienceData[$order]['employer_name'] = $entry->new_value;
                    break;
                case 'experience_state':
                    $experienceData[$order]['state'] = $entry->new_value;
                    break;
                case 'experience_job_type':
                    $experienceData[$order]['job_type'] = $entry->new_value;
                    break;
                case 'experience_fte_multiplier':
                    $experienceData[$order]['fte_multiplier'] = $entry->new_value ? (float) $entry->new_value : null;
                    break;
            }
        }

        // Convert to indexed array and filter out entries without at least job_title
        $experiences = [];
        foreach ($experienceData as $order => $experience) {
            // Include experience if it has job_title
            if (!empty($experience['job_title'])) {
                $experiences[] = $experience;
            }
        }

        return $experiences;
    }

    /**
     * Get experiences from client_experiences source table
     * 
     * @param int $clientId
     * @return array
     */
    private function getExperiencesFromSource($clientId)
    {
        $experiences = [];
        
        $clientExperiences = DB::table('client_experiences')
            ->where('client_id', $clientId)
            ->orderBy('id')
            ->get();

        foreach ($clientExperiences as $experience) {
            $experiences[] = [
                'id' => $experience->id,
                'job_title' => $experience->job_title ?? null,
                'job_code' => $experience->job_code ?? null,
                'country' => $experience->job_country ?? null,
                'start_date' => $experience->job_start_date ? $this->formatDate($experience->job_start_date) : null,
                'finish_date' => $experience->job_finish_date ? $this->formatDate($experience->job_finish_date) : null,
                'relevant_experience' => ($experience->relevant_experience == 1) ? true : false,
                'employer_name' => $experience->job_emp_name ?? null,
                'state' => $experience->job_state ?? null,
                'job_type' => $experience->job_type ?? null,
                'fte_multiplier' => $experience->fte_multiplier ? (float) $experience->fte_multiplier : null,
            ];
        }

        return $experiences;
    }

    /**
     * Get occupations data - from audit table if updated, otherwise from client_occupations table
     * 
     * @param int $clientId
     * @return array
     */
    private function getOccupationsData($clientId)
    {
        // Check if there are any occupation audit entries
        $hasOccupationAudits = ClientPortalDetailAudit::where('client_id', $clientId)
            ->whereIn('meta_key', ['occupation', 'occupation_skill_assessment', 'occupation_nominated', 'occupation_code', 'occupation_assessing_authority', 'occupation_visa_subclass', 'occupation_assessment_date', 'occupation_expiry_date', 'occupation_reference_no', 'occupation_relevant', 'occupation_anzsco_id'])
            ->exists();

        if ($hasOccupationAudits) {
            // Fetch from audit table
            return $this->getOccupationsFromAudit($clientId);
        } else {
            // Fetch from client_occupations table
            return $this->getOccupationsFromSource($clientId);
        }
    }

    /**
     * Get occupations from audit table
     * 
     * @param int $clientId
     * @return array
     */
    private function getOccupationsFromAudit($clientId)
    {
        // Get all occupation-related audit entries
        $auditEntries = ClientPortalDetailAudit::where('client_id', $clientId)
            ->whereIn('meta_key', ['occupation', 'occupation_skill_assessment', 'occupation_nominated', 'occupation_code', 'occupation_assessing_authority', 'occupation_visa_subclass', 'occupation_assessment_date', 'occupation_expiry_date', 'occupation_reference_no', 'occupation_relevant', 'occupation_anzsco_id'])
            ->get();

        // Group by meta_order and meta_key, taking the latest new_value for each combination
        $occupationData = [];
        $latestEntries = []; // Track latest entry for each meta_order + meta_key combination

        foreach ($auditEntries as $entry) {
            $order = $entry->meta_order;
            $key = $entry->meta_key;
            $processedKey = $order . '_' . $key;
            
            // Store the latest entry for this combination
            if (!isset($latestEntries[$processedKey])) {
                $latestEntries[$processedKey] = $entry;
            } else {
                // Compare updated_at to keep the latest
                if (strtotime($entry->updated_at) > strtotime($latestEntries[$processedKey]->updated_at)) {
                    $latestEntries[$processedKey] = $entry;
                }
            }
        }

        // Process latest entries
        foreach ($latestEntries as $processedKey => $entry) {
            $order = $entry->meta_order;
            $key = $entry->meta_key;
            
            // Initialize occupation entry if not exists
            if (!isset($occupationData[$order])) {
                $occupationData[$order] = [
                    'id' => null, // Audit doesn't have original id
                    'skill_assessment' => null,
                    'nominated_occupation' => null,
                    'occupation_code' => null,
                    'assessing_authority' => null,
                    'visa_subclass' => null,
                    'assessment_date' => null,
                    'expiry_date' => null,
                    'reference_no' => null,
                    'relevant_occupation' => false,
                    'anzsco_occupation_id' => null,
                ];
            }
            
            switch ($key) {
                case 'occupation':
                    // Occupation marker - just confirms this occupation entry exists
                    break;
                case 'occupation_skill_assessment':
                    $occupationData[$order]['skill_assessment'] = $entry->new_value;
                    break;
                case 'occupation_nominated':
                    $occupationData[$order]['nominated_occupation'] = $entry->new_value;
                    break;
                case 'occupation_code':
                    $occupationData[$order]['occupation_code'] = $entry->new_value;
                    break;
                case 'occupation_assessing_authority':
                    $occupationData[$order]['assessing_authority'] = $entry->new_value;
                    break;
                case 'occupation_visa_subclass':
                    $occupationData[$order]['visa_subclass'] = $entry->new_value;
                    break;
                case 'occupation_assessment_date':
                    $occupationData[$order]['assessment_date'] = $entry->new_value ? $this->formatDate($entry->new_value) : null;
                    break;
                case 'occupation_expiry_date':
                    $occupationData[$order]['expiry_date'] = $entry->new_value ? $this->formatDate($entry->new_value) : null;
                    break;
                case 'occupation_reference_no':
                    $occupationData[$order]['reference_no'] = $entry->new_value;
                    break;
                case 'occupation_relevant':
                    $occupationData[$order]['relevant_occupation'] = ($entry->new_value == '1' || $entry->new_value == 1) ? true : false;
                    break;
                case 'occupation_anzsco_id':
                    $occupationData[$order]['anzsco_occupation_id'] = $entry->new_value ? (int) $entry->new_value : null;
                    break;
            }
        }

        // Convert to indexed array and filter out entries without at least nominated_occupation or occupation_code
        $occupations = [];
        foreach ($occupationData as $order => $occupation) {
            // Include occupation if it has at least nominated_occupation or occupation_code
            if (!empty($occupation['nominated_occupation']) || !empty($occupation['occupation_code'])) {
                $occupations[] = $occupation;
            }
        }

        return $occupations;
    }

    /**
     * Get occupations from client_occupations source table
     * 
     * @param int $clientId
     * @return array
     */
    private function getOccupationsFromSource($clientId)
    {
        $occupations = [];
        
        $clientOccupations = DB::table('client_occupations')
            ->where('client_id', $clientId)
            ->orderBy('id')
            ->get();

        foreach ($clientOccupations as $occupation) {
            $occupations[] = [
                'id' => $occupation->id,
                'skill_assessment' => $occupation->skill_assessment ?? null,
                'nominated_occupation' => $occupation->nomi_occupation ?? null,
                'occupation_code' => $occupation->occupation_code ?? null,
                'assessing_authority' => $occupation->list ?? null,
                'visa_subclass' => $occupation->visa_subclass ?? null,
                'assessment_date' => $occupation->dates && $occupation->dates != '0000-00-00' ? $this->formatDate($occupation->dates) : null,
                'expiry_date' => $occupation->expiry_dates && $occupation->expiry_dates != '0000-00-00' ? $this->formatDate($occupation->expiry_dates) : null,
                'reference_no' => $occupation->occ_reference_no ?? null,
                'relevant_occupation' => ($occupation->relevant_occupation == 1) ? true : false,
                'anzsco_occupation_id' => $occupation->anzsco_occupation_id ? (int) $occupation->anzsco_occupation_id : null,
            ];
        }

        return $occupations;
    }

    /**
     * Get test scores data - from audit table if updated, otherwise from client_testscore table
     * 
     * @param int $clientId
     * @return array
     */
    private function getTestScoresData($clientId)
    {
        // Check if there are any test score audit entries
        // Since test scores may not be migrated yet, we check for common meta_keys that might be used
        $hasTestScoreAudits = ClientPortalDetailAudit::where('client_id', $clientId)
            ->whereIn('meta_key', ['test_score', 'test_score_test_type', 'test_score_listening', 'test_score_reading', 'test_score_writing', 'test_score_speaking', 'test_score_overall_score', 'test_score_test_date', 'test_score_reference_no', 'test_score_relevant'])
            ->exists();

        if ($hasTestScoreAudits) {
            // Fetch from audit table
            return $this->getTestScoresFromAudit($clientId);
        } else {
            // Fetch from client_testscore table
            return $this->getTestScoresFromSource($clientId);
        }
    }

    /**
     * Get test scores from audit table
     * 
     * @param int $clientId
     * @return array
     */
    private function getTestScoresFromAudit($clientId)
    {
        // Get all test score-related audit entries
        $auditEntries = ClientPortalDetailAudit::where('client_id', $clientId)
            ->whereIn('meta_key', ['test_score', 'test_score_test_type', 'test_score_listening', 'test_score_reading', 'test_score_writing', 'test_score_speaking', 'test_score_overall_score', 'test_score_test_date', 'test_score_reference_no', 'test_score_relevant'])
            ->get();

        // Group by meta_order and meta_key, taking the latest new_value for each combination
        $testScoreData = [];
        $latestEntries = []; // Track latest entry for each meta_order + meta_key combination

        foreach ($auditEntries as $entry) {
            $order = $entry->meta_order;
            $key = $entry->meta_key;
            $processedKey = $order . '_' . $key;
            
            // Store the latest entry for this combination
            if (!isset($latestEntries[$processedKey])) {
                $latestEntries[$processedKey] = $entry;
            } else {
                // Compare updated_at to keep the latest
                if (strtotime($entry->updated_at) > strtotime($latestEntries[$processedKey]->updated_at)) {
                    $latestEntries[$processedKey] = $entry;
                }
            }
        }

        // Process latest entries
        foreach ($latestEntries as $processedKey => $entry) {
            $order = $entry->meta_order;
            $key = $entry->meta_key;
            
            // Initialize test score entry if not exists
            if (!isset($testScoreData[$order])) {
                $testScoreData[$order] = [
                    'id' => null, // Audit doesn't have original id
                    'test_type' => null,
                    'listening' => null,
                    'reading' => null,
                    'writing' => null,
                    'speaking' => null,
                    'overall_score' => null,
                    'test_date' => null,
                    'reference_no' => null,
                    'relevant_test' => false,
                ];
            }
            
            switch ($key) {
                case 'test_score':
                    // Test score marker - just confirms this test score entry exists
                    break;
                case 'test_score_test_type':
                    $testScoreData[$order]['test_type'] = $entry->new_value;
                    break;
                case 'test_score_listening':
                    $testScoreData[$order]['listening'] = $entry->new_value ? (float) $entry->new_value : null;
                    break;
                case 'test_score_reading':
                    $testScoreData[$order]['reading'] = $entry->new_value ? (float) $entry->new_value : null;
                    break;
                case 'test_score_writing':
                    $testScoreData[$order]['writing'] = $entry->new_value ? (float) $entry->new_value : null;
                    break;
                case 'test_score_speaking':
                    $testScoreData[$order]['speaking'] = $entry->new_value ? (float) $entry->new_value : null;
                    break;
                case 'test_score_overall_score':
                    $testScoreData[$order]['overall_score'] = $entry->new_value ? (float) $entry->new_value : null;
                    break;
                case 'test_score_test_date':
                    $testScoreData[$order]['test_date'] = $entry->new_value ? $this->formatDate($entry->new_value) : null;
                    break;
                case 'test_score_reference_no':
                    $testScoreData[$order]['reference_no'] = $entry->new_value;
                    break;
                case 'test_score_relevant':
                    $testScoreData[$order]['relevant_test'] = ($entry->new_value == '1' || $entry->new_value == 1) ? true : false;
                    break;
            }
        }

        // Convert to indexed array and filter out entries without at least test_type
        $testScores = [];
        foreach ($testScoreData as $order => $testScore) {
            // Include test score if it has test_type
            if (!empty($testScore['test_type'])) {
                $testScores[] = $testScore;
            }
        }

        return $testScores;
    }

    /**
     * Get test scores from client_testscore source table
     * 
     * @param int $clientId
     * @return array
     */
    private function getTestScoresFromSource($clientId)
    {
        $testScores = [];
        
        $clientTestScores = DB::table('client_testscore')
            ->where('client_id', $clientId)
            ->orderBy('id')
            ->get();

        foreach ($clientTestScores as $testScore) {
            $testScores[] = [
                'id' => $testScore->id,
                'test_type' => $testScore->test_type ?? null,
                'listening' => $testScore->listening ? (float) $testScore->listening : null,
                'reading' => $testScore->reading ? (float) $testScore->reading : null,
                'writing' => $testScore->writing ? (float) $testScore->writing : null,
                'speaking' => $testScore->speaking ? (float) $testScore->speaking : null,
                'overall_score' => $testScore->overall_score ? (float) $testScore->overall_score : null,
                'test_date' => $testScore->test_date ? $this->formatDate($testScore->test_date) : null,
                'reference_no' => $testScore->test_reference_no ?? null,
                'relevant_test' => ($testScore->relevant_test == 1 || strtolower($testScore->relevant_test ?? '') === 'yes') ? true : false,
            ];
        }

        return $testScores;
    }
}
