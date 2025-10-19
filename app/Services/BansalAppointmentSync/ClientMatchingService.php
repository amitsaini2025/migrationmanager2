<?php

namespace App\Services\BansalAppointmentSync;

use App\Models\Admin;
use App\Models\ClientContact;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ClientMatchingService
{
    /**
     * Find or create client from appointment data
     */
    public function findOrCreateClient(array $appointmentData): ?Admin
    {
        $email = $appointmentData['email'] ?? null;
        $phone = $appointmentData['phone'] ?? null;
        $fullName = $appointmentData['full_name'] ?? null;

        if (!$email && !$phone) {
            Log::warning('Cannot match/create client without email or phone', [
                'appointment_id' => $appointmentData['id'] ?? null
            ]);
            return null;
        }

        // Try to find existing client
        $client = $this->findClientByEmail($email);
        if ($client) {
            Log::info('Found existing client by email', [
                'client_id' => $client->id,
                'email' => $email
            ]);
            return $client;
        }

        $client = $this->findClientByPhone($phone);
        if ($client) {
            Log::info('Found existing client by phone', [
                'client_id' => $client->id,
                'phone' => $phone
            ]);
            return $client;
        }

        // Create new client
        return $this->createNewClient($appointmentData);
    }

    /**
     * Find client by email
     */
    protected function findClientByEmail(?string $email): ?Admin
    {
        if (empty($email)) {
            return null;
        }

        return Admin::where('role', 7)
            ->where('email', $email)
            ->first();
    }

    /**
     * Find client by phone
     */
    protected function findClientByPhone(?string $phone): ?Admin
    {
        if (empty($phone)) {
            return null;
        }

        // Try exact match first
        $client = Admin::where('role', 7)
            ->where('phone', $phone)
            ->first();

        if ($client) {
            return $client;
        }

        // Try phone in client_contacts table
        $contact = ClientContact::where('phone', $phone)->first();
        if ($contact) {
            return Admin::where('role', 7)->find($contact->client_id);
        }

        return null;
    }

    /**
     * Create new client (copied from ClientsController logic)
     */
    protected function createNewClient(array $appointmentData): ?Admin
    {
        DB::beginTransaction();

        try {
            // Generate client_counter and client_id
            $clientCntExist = DB::table('admins')->where('role', 7)->count();
            if ($clientCntExist > 0) {
                $clientLatestArr = DB::table('admins')
                    ->select('client_counter')
                    ->where('role', 7)
                    ->latest()
                    ->first();
                $client_latest_counter = $clientLatestArr ? $clientLatestArr->client_counter : "00000";
            } else {
                $client_latest_counter = "00000";
            }

            $client_current_counter = $this->getNextCounter($client_latest_counter);
            
            // Parse name
            $nameParts = $this->parseFullName($appointmentData['full_name'] ?? 'Unknown');
            $firstName = $nameParts['first_name'];
            $lastName = $nameParts['last_name'];

            $firstFourLetters = strtoupper(strlen($firstName) >= 4
                ? substr($firstName, 0, 4)
                : $firstName);
            $client_id = $firstFourLetters . date('y') . $client_current_counter;

            // Create client
            $client = new Admin();
            $client->first_name = $firstName;
            $client->last_name = $lastName;
            $client->email = $appointmentData['email'] ?? null;
            $client->phone = $appointmentData['phone'] ?? null;
            $client->country_code = $this->extractCountryCode($appointmentData['phone']);
            $client->client_counter = $client_current_counter;
            $client->client_id = $client_id;
            $client->role = 7; // Client role
            $client->type = 'lead'; // Start as lead
            $client->source = 'Bansal Website';
            $client->created_at = now();
            $client->updated_at = now();
            $client->save();

            // Create client contact entry if phone exists
            if (!empty($appointmentData['phone'])) {
                ClientContact::create([
                    'client_id' => $client->id,
                    'admin_id' => Auth::id() ?? config('app.system_user_id', 1), // System user for automated processes
                    'contact_type' => 'Mobile',
                    'country_code' => $client->country_code,
                    'phone' => $appointmentData['phone'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::commit();

            Log::info('Created new client from appointment', [
                'client_id' => $client->id,
                'client_code' => $client_id,
                'email' => $client->email
            ]);

            return $client;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create client from appointment', [
                'error' => $e->getMessage(),
                'appointment_data' => $appointmentData
            ]);
            return null;
        }
    }

    /**
     * Get next counter (copied from ClientsController)
     */
    protected function getNextCounter(string $lastCounter): string
    {
        $numericPart = (int)$lastCounter;
        $nextNumericPart = $numericPart + 1;
        return str_pad($nextNumericPart, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Parse full name into first and last name
     */
    protected function parseFullName(string $fullName): array
    {
        $parts = explode(' ', trim($fullName), 2);
        return [
            'first_name' => $parts[0] ?? 'Unknown',
            'last_name' => $parts[1] ?? null,
        ];
    }

    /**
     * Extract country code from phone (basic implementation)
     */
    protected function extractCountryCode(?string $phone): ?string
    {
        if (empty($phone)) {
            return null;
        }

        // If phone starts with +, extract country code
        if (str_starts_with($phone, '+61')) {
            return '+61';
        }

        // Default to Australia
        return '+61';
    }
}

