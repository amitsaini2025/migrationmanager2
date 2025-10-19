# **🚀 Appointment Sync System - Complete Implementation Plan**
### **Polling-Based Architecture for Bansal Immigration CRM Integration**

---

## **📋 EXECUTIVE SUMMARY**

### **Project:** Appointment Synchronization from Bansal Immigration Website to CRM
### **Approach:** Polling-Based API Sync (No Webhooks - Phase 1)
### **Timeline:** 10 weeks
### **Laravel Version:** 12.x (PHP 8.2+) ✅ **VERIFIED IN composer.json**
### **Integration Type:** One-way read-only sync with local CRM management

---

## **📚 IMPORTANT: COMPANION DOCUMENTS**

This plan has a companion document with all missing components:

📄 **`APPOINTMENT_SYNC_MISSING_COMPONENTS.md`** - Contains:
- ✅ Console Commands (4 commands)
- ✅ Mail Classes (AppointmentDetailedConfirmation)
- ✅ Model Scopes & Helpers
- ✅ Config Updates
- ✅ .env Variables
- ✅ Service Provider Registration
- ✅ Quick Start Checklist
- ✅ Troubleshooting Guide

**👉 Review that document after reading this plan!**

---

## **⚠️ IMPORTANT: PROJECT SCOPE - WHAT THIS DOES & DOESN'T DO**

### **✅ WHAT THIS PROJECT DOES:**

1. **Sync website bookings** from Bansal Immigration website
2. **Store them separately** in new `booking_appointments` table
3. **Display in new UI** with globe icon 🌐 in top navigation
4. **Allow staff to manage** synced appointments (view, update status, add notes, send reminders)
5. **Track consultant assignments** using new 5-calendar structure
6. **Send reminders** to clients 24h before appointments

### **❌ WHAT THIS PROJECT DOES NOT DO:**

1. **Does NOT change manual booking system** - Staff continues using old calendar
2. **Does NOT modify `appointments` table** - Legacy table stays untouched
3. **Does NOT change existing AppointmentsController** - Old code stays as-is
4. **Does NOT allow staff to create appointments in new system** - New system is VIEW-ONLY for synced data
5. **Does NOT modify 5 calendar views** - Old calendars (Paid, JRP, Education, Tourist, Adelaide) stay working

---

### **🎯 RESULT: TWO SEPARATE APPOINTMENT SYSTEMS**

```
┌─────────────────────────────────────────────────────────────┐
│ 📅 OLD SYSTEM (Calendar Icon in Navigation)                 │
│ ─────────────────────────────────────────────────────────── │
│ Table:      appointments (existing, untouched)              │
│ Controller: AppointmentsController.php (no changes)         │
│ Views:      Admin/appointments/* (no changes)               │
│                                                             │
│ ✅ Staff manually books appointments here                   │
│ ✅ Same 5 calendar views (Paid, JRP, Education, etc.)      │
│ ✅ All existing features work exactly as before            │
│ ✅ ZERO changes to this system                             │
└─────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────┐
│ 🌐 NEW SYSTEM (Globe Icon in Navigation) - THIS PROJECT     │
│ ─────────────────────────────────────────────────────────── │
│ Table:      booking_appointments (new, created by this)     │
│ Controller: BookingAppointmentsController.php (new)         │
│ Views:      Admin/booking/appointments/* (new)              │
│                                                             │
│ ✅ Website bookings synced automatically every 10 min       │
│ ✅ Staff can VIEW, manage, update status, add notes        │
│ ✅ Staff can send reminders, reassign consultants          │
│ ❌ Staff CANNOT create new appointments here                │
│    (For now - can add this feature later if needed)        │
└─────────────────────────────────────────────────────────────┘

BOTH SYSTEMS SHARE:
  - admins table (clients)
  - client_contacts table (phone/email)
```

---

### **💡 WHY KEEP THEM SEPARATE?**

**Phase 1 (Weeks 1-10): Focus on Sync**
- Get website sync working perfectly
- Zero risk to existing workflows
- Staff familiar with old system
- No training needed
- If new system has issues, old system unaffected

**Phase 2 (Optional - Future):**
- After sync proven stable (8+ weeks)
- Can add manual booking form to new system
- Train staff on unified interface
- Gradually migrate all to new system
- Archive old system when ready

**For Now: Keep simple, get sync working first! ✅**

---

## **🎯 SYSTEM OVERVIEW**

### **Architecture Flow:**

```
┌─────────────────────────────────────────────────┐
│  Bansal Immigration Website (External)          │
│  https://bansalimmigration.com                  │
│                                                  │
│  • Customer books appointment online            │
│  • Processes payment (Stripe)                   │
│  • Sends instant confirmation email/SMS         │
│  • Stores in appointments table                 │
│  • Exposes REST API:                            │
│    GET /api/crm/appointments/recent?minutes=10  │
└────────────────┬────────────────────────────────┘
                 │
                 │ HTTP GET Request (every 10 min)
                 │ Authorization: Bearer {token}
                 │
                 ▼
┌─────────────────────────────────────────────────┐
│  This CRM (migrationmanager)                    │
│  C:\xampp\htdocs\migrationmanager               │
│                                                  │
│  ┌──────────────────────────────────────────┐  │
│  │ Cron Job: Every 10 minutes               │  │
│  │ php artisan booking:sync-appointments    │  │
│  └──────────────┬───────────────────────────┘  │
│                 │                                │
│                 ▼                                │
│  ┌──────────────────────────────────────────┐  │
│  │ BansalApiClient                          │  │
│  │ • Fetches recent appointments            │  │
│  │ • Validates API response                 │  │
│  └──────────────┬───────────────────────────┘  │
│                 │                                │
│                 ▼                                │
│  ┌──────────────────────────────────────────┐  │
│  │ AppointmentSyncService                   │  │
│  │ • Checks if appointment exists (ID)      │  │
│  │ • Skips duplicates                       │  │
│  └──────────────┬───────────────────────────┘  │
│                 │                                │
│                 ▼                                │
│  ┌──────────────────────────────────────────┐  │
│  │ ClientMatchingService                    │  │
│  │ • Finds client by email/phone            │  │
│  │ • Creates new client if not found        │  │
│  │ • Returns client_id                      │  │
│  └──────────────┬───────────────────────────┘  │
│                 │                                │
│                 ▼                                │
│  ┌──────────────────────────────────────────┐  │
│  │ ConsultantAssignmentService              │  │
│  │ • Determines calendar type:              │  │
│  │   - Education (noe_id=5)                 │  │
│  │   - JRP (noe_id=2,3)                     │  │
│  │   - Tourist (noe_id=4)                   │  │
│  │   - Others/Paid (noe_id=1,6,7,8)         │  │
│  │   - Adelaide (location=1)                │  │
│  │ • Assigns default consultant             │  │
│  └──────────────┬───────────────────────────┘  │
│                 │                                │
│                 ▼                                │
│  ┌──────────────────────────────────────────┐  │
│  │ Save to booking_appointments table       │  │
│  │ • Store all appointment data             │  │
│  │ • Link to client_id                      │  │
│  │ • Link to consultant_id                  │  │
│  │ • Set status = 'pending'                 │  │
│  └──────────────┬───────────────────────────┘  │
│                 │                                │
│                 ▼                                │
│  ┌──────────────────────────────────────────┐  │
│  │ NotificationService (Optional)           │  │
│  │ • Send detailed follow-up email          │  │
│  │ • Include consultant details             │  │
│  │ • Add calendar invite (.ics)             │  │
│  └──────────────────────────────────────────┘  │
│                                                  │
│  ┌──────────────────────────────────────────┐  │
│  │ Daily Reminder Job                       │  │
│  │ php artisan booking:send-reminders       │  │
│  │ • Sends SMS 24h before appointment       │  │
│  └──────────────────────────────────────────┘  │
└─────────────────────────────────────────────────┘
```

---

## **🗄️ DATABASE SCHEMA**

### **📊 TABLE STRATEGY: Hybrid Approach (Shared + New)**

**IMPORTANT:** The new system uses a **mix of shared and new tables** to enable gradual migration.

#### **Tables Used:**

| Table | Strategy | Purpose |
|-------|----------|---------|
| **`admins`** | ✅ **SHARED (Existing)** | Client records (role=7) - both systems use same clients |
| **`client_contacts`** | ✅ **SHARED (Existing)** | Phone numbers - reuse for client matching |
| **`appointment_consultants`** | ⭐ **NEW** | Consultant/calendar assignments (5 calendars) |
| **`booking_appointments`** | ⭐ **NEW** | Website bookings (separate from manual appointments) |
| **`appointment_sync_logs`** | ⭐ **NEW** | Track sync operations |
| **`appointments`** | 🔄 **KEEP (Legacy)** | Old manual appointments - NOT touched, eventually archive |
| **`appointment_logs`** | 🔄 **KEEP (Legacy)** | Old logs - keep for history |

---

### **🎯 Migration Strategy:**

```
Phase 1 (Weeks 1-8): PARALLEL SYSTEMS
┌─────────────────────────────────────────────────────────┐
│ SHARED TABLES (Both Systems Use)                       │
│ ├─ admins (clients)              ✓ No changes needed   │
│ └─ client_contacts               ✓ No changes needed   │
└─────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────┐
│ OLD SYSTEM (Keep Running)                               │
│ ├─ appointments                  ✓ Keep as-is           │
│ ├─ appointment_logs              ✓ Keep as-is           │
│ └─ AppointmentsController.php    ✓ No changes          │
└─────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────┐
│ NEW SYSTEM (Build Alongside)                            │
│ ├─ appointment_consultants       ⭐ CREATE NEW          │
│ ├─ booking_appointments          ⭐ CREATE NEW          │
│ ├─ appointment_sync_logs         ⭐ CREATE NEW          │
│ └─ BookingAppointmentsController ⭐ CREATE NEW          │
└─────────────────────────────────────────────────────────┘

Result: TWO SEPARATE APPOINTMENT SYSTEMS
  - Staff manually book → appointments table (old system)
  - Website bookings → booking_appointments table (new system)
  - Both link to same clients in admins table


Phase 2 (Week 9+): OPTIONAL CONSOLIDATION
┌─────────────────────────────────────────────────────────┐
│ After 4-6 weeks of stable operation, IF DESIRED:       │
│                                                         │
│ Option A: ARCHIVE OLD SYSTEM                            │
│   - Rename appointments → appointments_archive          │
│   - Keep for historical reference                       │
│   - Stop using old booking UI                           │
│                                                         │
│ Option B: MERGE INTO ONE TABLE                          │
│   - Migrate appointments → booking_appointments         │
│   - Add 'source' field: 'manual' vs 'website'          │
│   - Unified system for all bookings                     │
│                                                         │
│ Option C: KEEP BOTH (Recommended Initially)             │
│   - Manual bookings still use old system                │
│   - Website bookings use new system                     │
│   - No disruption, clear separation                     │
└─────────────────────────────────────────────────────────┘
```

---

### **🔑 Key Design Decisions:**

#### **1. Why SHARED `admins` table?**
✅ **Single source of truth for clients**
- Both appointment systems link to same client records
- No duplicate client data
- Staff sees all client info regardless of booking source
- When syncing website booking, system finds/creates client in `admins` table

```php
// ClientMatchingService looks in shared admins table
$client = Admin::where('role', 7)
    ->where('email', $appointmentData['email'])
    ->first();

// Both systems use same client_id
Old Appointment: appointments.client_id → admins.id
New Appointment: booking_appointments.client_id → admins.id
```

#### **2. Why SEPARATE `booking_appointments` table?**
✅ **Clean separation, zero risk to existing system**
- Old system unchanged - no migration risk
- Different data structures (new has sync metadata, payment info from API)
- Can run in parallel safely
- Easy to test without affecting production

**Schema Differences:**
```sql
-- OLD: appointments (manual booking)
- id, user_id, client_id, service_id, noe_id
- date, time, timeslot_full
- status (integer 0-11)  ← Hard to read
- inperson_address (1 or 2)
- NO: sync metadata, payment details, bansal_appointment_id

-- NEW: booking_appointments (website sync)
- id, bansal_appointment_id (unique!)  ← Links to external system
- client_id → same admins table
- consultant_id → new consultants table
- appointment_datetime, timeslot_full
- status (enum: 'pending', 'confirmed')  ← Clear strings
- location (enum: 'melbourne', 'adelaide')  ← Clear strings
- payment_status, amount, promo_code  ← From API
- synced_from_bansal_at, sync_status  ← Sync tracking
```

#### **3. Why NEW `appointment_consultants` table?**
✅ **Better organization than hardcoded logic**

**Current system:** Calendar logic is hardcoded in blade views
```php
// OLD: Logic scattered in calender.blade.php
if($type=="Jrp"){ 
    $appointments = Appointment::whereIn('noe_id', [2,3])
        ->where('service_id', 2)->get();
}
```

**New system:** Structured consultant records
```php
// NEW: Data-driven approach
$consultant = AppointmentConsultant::where('calendar_type', 'jrp')
    ->where('is_active', true)
    ->first();

$appointment->consultant_id = $consultant->id;
```

#### **4. Why KEEP old `appointments` table?**
✅ **No disruption to existing workflows**
- Staff still manually book appointments using old system
- Historical data preserved
- No retraining needed immediately
- Can gradually phase out when ready

---

### **📈 Data Flow Example:**

**Scenario: Customer books appointment on Bansal website**

```
1. Customer books on Bansal website
   ↓
   Bansal website saves to their appointments table
   ↓
2. CRM polls API every 10 min
   ↓
   BansalApiClient.getRecentAppointments()
   ↓
3. ClientMatchingService checks:
   Does client exist in admins table?
   ├─ YES → Use existing client_id
   └─ NO  → Create new client in admins table
   ↓
4. ConsultantAssignmentService determines calendar
   ↓
5. Save to booking_appointments table
   {
     bansal_appointment_id: 123,
     client_id: 456,  ← Points to admins table (shared)
     consultant_id: 2, ← Points to appointment_consultants (new)
     status: 'pending',
     ...
   }
   ↓
6. Staff sees in CRM:
   - New globe icon 🌐 shows badge (1 pending)
   - Click → sees appointment details
   - Client link opens same client detail page
   - Can add notes, change status, reassign consultant
```

---

### **🗂️ Relationships Diagram:**

```
┌─────────────────────────────────────────────────────────┐
│           SHARED TABLE (Both Systems)                   │
│                                                         │
│  ┌──────────────────────────────────────────────────┐  │
│  │ admins (role=7 = clients)                       │  │
│  │ ├─ id (PK)                                       │  │
│  │ ├─ first_name, last_name                        │  │
│  │ ├─ email, phone                                  │  │
│  │ ├─ client_id (e.g., JOHN2400001)               │  │
│  │ └─ ...other client fields                       │  │
│  └──────────────────────────────────────────────────┘  │
│           ↑                           ↑                 │
└───────────┼───────────────────────────┼─────────────────┘
            │                           │
    ┌───────┴───────┐         ┌────────┴────────┐
    │               │         │                 │
┌───▼─────────────────────┐ ┌─▼───────────────────────────┐
│ OLD SYSTEM              │ │ NEW SYSTEM                  │
│                         │ │                             │
│ appointments            │ │ booking_appointments        │
│ ├─ id                   │ │ ├─ id                       │
│ ├─ client_id (FK) ──────┼─┤ ├─ client_id (FK) ──────────┤
│ ├─ user_id              │ │ ├─ bansal_appointment_id    │
│ ├─ service_id           │ │ ├─ consultant_id (FK)       │
│ ├─ noe_id               │ │ │   │                        │
│ ├─ date, time           │ │ ├─ appointment_datetime     │
│ ├─ status (0-11)        │ │ ├─ status (enum)            │
│ └─ ...                  │ │ ├─ payment_status           │
│                         │ │ ├─ synced_from_bansal_at    │
│ Manual booking by staff │ │ └─ ...                      │
│ 📅 Calendar icon        │ │                             │
└─────────────────────────┘ │ Synced from Bansal website  │
                            │ 🌐 Globe icon               │
                            │         │                   │
                            │         ▼                   │
                            │ ┌──────────────────────┐    │
                            │ │appointment_consultants│   │
                            │ │ ├─ id (PK)            │   │
                            │ │ ├─ name               │   │
                            │ │ ├─ calendar_type      │   │
                            │ │ │   (paid, jrp, etc) │   │
                            │ │ └─ ...                │   │
                            │ └──────────────────────┘    │
                            └─────────────────────────────┘
```

---

### **⚠️ Important Notes:**

1. **NO changes to existing `appointments` table**
   - Foreign keys remain intact
   - Existing code continues working
   - Zero downtime, zero risk

2. **Client matching reuses existing logic**
   - Same email/phone lookup in `admins` table
   - Same client creation process from `ClientsController`
   - Same client_counter generation

3. **Two appointment views in Client Detail page**
   ```
   Client Detail Page Tabs:
   ├─ Personal Info
   ├─ Appointments ← Shows appointments (manual bookings)
   ├─ Website Bookings ← NEW tab (synced bookings)
   ├─ Applications
   └─ Documents
   ```

4. **Eventual cleanup (optional, 4-6 weeks later)**
   ```sql
   -- Option 1: Archive old table
   RENAME TABLE appointments TO appointments_archive_2024;
   
   -- Option 2: Add source field to unified table
   ALTER TABLE booking_appointments 
     ADD COLUMN source ENUM('manual', 'website') DEFAULT 'website';
   
   -- Then migrate old appointments into booking_appointments
   -- with source='manual'
   ```

---

### **🗑️ GRADUAL CLEANUP PLAN (Optional - Week 9+)**

**After 4-6 weeks of stable operation, you can choose to:**

#### **Timeline:**

```
Week 1-8: BUILD & TEST
├─ New system running in parallel
├─ Old system unchanged
├─ Both working independently
└─ Staff familiar with new UI

Week 9-10: EVALUATION
├─ Review sync logs
├─ Check data quality
├─ Ensure no issues
└─ Decision point: merge or keep separate?

Week 11+: CLEANUP (if desired)
├─ Archive old appointments table
├─ OR keep both systems running
└─ OR merge into unified table
```

---

#### **Cleanup Option 1: ARCHIVE OLD TABLE (Safest)**

```sql
-- Step 1: Stop using old booking UI (optional)
-- Just stop showing old calendar views in navigation

-- Step 2: Rename old table for archival
RENAME TABLE appointments TO appointments_archive_2024;
RENAME TABLE appointment_logs TO appointment_logs_archive_2024;

-- Step 3: Keep for historical reference
-- Don't drop! Keep for auditing and historical data
```

**Pros:** ✅ Safest, can always restore  
**Cons:** ❌ Takes disk space (but minimal)

---

#### **Cleanup Option 2: MERGE INTO UNIFIED TABLE (Advanced)**

```sql
-- Step 1: Add source field to booking_appointments
ALTER TABLE booking_appointments 
  ADD COLUMN booking_source ENUM('manual', 'website') 
  DEFAULT 'website';

-- Step 2: Migrate old appointments
INSERT INTO booking_appointments (
  booking_source,
  client_id,
  client_name,
  client_email,
  appointment_datetime,
  location,
  status,
  -- ... map other fields
  created_at,
  updated_at
)
SELECT 
  'manual' as booking_source,
  a.client_id,
  CONCAT(ad.first_name, ' ', ad.last_name) as client_name,
  ad.email as client_email,
  CONCAT(a.date, ' ', a.time) as appointment_datetime,
  CASE 
    WHEN a.inperson_address = 1 THEN 'adelaide'
    WHEN a.inperson_address = 2 THEN 'melbourne'
    ELSE 'melbourne'
  END as location,
  CASE a.status
    WHEN 0 THEN 'pending'
    WHEN 1 THEN 'confirmed'
    WHEN 2 THEN 'completed'
    WHEN 7 THEN 'cancelled'
    ELSE 'pending'
  END as status,
  -- ... map other fields
  a.created_at,
  a.updated_at
FROM appointments a
LEFT JOIN admins ad ON a.client_id = ad.id
WHERE a.date >= '2024-01-01'; -- Only recent appointments

-- Step 3: Verify count
SELECT booking_source, COUNT(*) 
FROM booking_appointments 
GROUP BY booking_source;

-- Step 4: Archive old table
RENAME TABLE appointments TO appointments_migrated_archive_2024;
```

**Pros:** ✅ Unified view of all appointments  
**Cons:** ❌ More complex, irreversible without backup

---

#### **Cleanup Option 3: KEEP BOTH (Recommended)**

**Just keep running both systems!**

```
✅ No migration needed
✅ Clear separation: manual vs website bookings
✅ Zero risk
✅ Staff familiar with both
✅ Old appointments visible in old calendar
✅ New appointments in new calendar

Eventually (1 year+):
  - Archive appointments older than X months
  - Keep recent appointments in old system
```

---

### **💾 What Gets Dropped Eventually?**

**NEVER DROP (Keep Forever):**
- ❌ **admins** - Core client data
- ❌ **client_contacts** - Contact information
- ❌ **appointments_archive** - Historical data

**CAN ARCHIVE (6+ months later):**
- ✅ **appointments** → **appointments_archive_2024**
- ✅ **appointment_logs** → **appointment_logs_archive_2024**

**NEVER CREATED (Old System Files Stay):**
- ❌ No changes to `AppointmentsController.php` (old controller)
- ❌ No changes to `resources/views/Admin/appointments/` (old views)
- ❌ No changes to existing routes

---

### **📊 Disk Space Considerations:**

**Typical sizes:**
```
appointments table (10,000 records): ~5 MB
appointment_logs table (50,000 records): ~20 MB
booking_appointments table (new): ~2 MB/month

Total: ~30 MB for 5 years of data (negligible)
```

**Recommendation:** Keep everything archived, disk space is cheap, data recovery is expensive!

---

### **🎯 Summary of Table Strategy:**

| What | Action | When | Why |
|------|--------|------|-----|
| **`admins`** | ✅ Keep & Share | Forever | Core client data |
| **`client_contacts`** | ✅ Keep & Share | Forever | Contact info |
| **`appointments`** | 🔄 Keep as-is | 8 weeks | Manual bookings continue |
| **`appointments`** (later) | 📦 Archive | 8+ weeks | Rename to `_archive` |
| **`appointment_consultants`** | ⭐ Create New | Week 1 | 5 calendar structure |
| **`booking_appointments`** | ⭐ Create New | Week 1 | Website bookings |
| **`appointment_sync_logs`** | ⭐ Create New | Week 1 | Sync tracking |

---

### **Migration 1: Create Appointment Consultants Table**

```php
<?php
// database/migrations/2024_10_20_000001_create_appointment_consultants_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('appointment_consultants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->enum('calendar_type', ['paid', 'jrp', 'education', 'tourist', 'adelaide']);
            $table->enum('location', ['melbourne', 'adelaide'])->default('melbourne');
            $table->json('specializations')->nullable()->comment('Array of noe_ids this consultant handles');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Laravel 12: Better index naming
            $table->index('calendar_type', 'idx_calendar_type');
            $table->index('is_active', 'idx_is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointment_consultants');
    }
};
```

### **Migration 2: Create Booking Appointments Table**

```php
<?php
// database/migrations/2024_10_20_000002_create_booking_appointments_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_appointments', function (Blueprint $table) {
            $table->id();
            
            // External Reference (Bansal Website)
            $table->unsignedBigInteger('bansal_appointment_id')->unique()->comment('ID from Bansal website');
            $table->string('order_hash')->nullable()->comment('Payment order hash from Bansal');
            
            // Relationships
            $table->unsignedBigInteger('client_id')->nullable()->comment('FK to admins.id (role=7)');
            $table->unsignedBigInteger('consultant_id')->nullable()->comment('FK to appointment_consultants.id');
            $table->unsignedBigInteger('assigned_by_admin_id')->nullable()->comment('Admin who assigned consultant');
            
            // Client Information (from Bansal API)
            $table->string('client_name');
            $table->string('client_email');
            $table->string('client_phone', 50)->nullable();
            $table->string('client_timezone', 50)->default('Australia/Melbourne');
            
            // Appointment Details
            $table->dateTime('appointment_datetime');
            $table->string('timeslot_full', 50)->nullable()->comment('e.g., "9:00 AM - 9:15 AM"');
            $table->integer('duration_minutes')->default(15);
            $table->enum('location', ['melbourne', 'adelaide']);
            $table->tinyInteger('inperson_address')->nullable()->comment('Legacy: 1=Adelaide, 2=Melbourne');
            $table->enum('meeting_type', ['in_person', 'phone', 'video'])->default('in_person');
            $table->string('preferred_language', 50)->default('English');
            
            // Service Details (from Bansal)
            $table->tinyInteger('service_id')->nullable()->comment('Legacy: 1=Paid, 2=Free');
            $table->tinyInteger('noe_id')->nullable()->comment('Legacy: Nature of Enquiry ID');
            $table->string('enquiry_type', 100)->nullable()->comment('tr, tourist, education, etc.');
            $table->string('service_type', 100)->nullable()->comment('Display name');
            $table->text('enquiry_details')->nullable();
            
            // Status & Lifecycle
            $table->enum('status', ['pending', 'confirmed', 'completed', 'cancelled', 'no_show', 'rescheduled'])->default('pending');
            $table->dateTime('confirmed_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->dateTime('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            
            // Payment Info (from Bansal, read-only)
            $table->boolean('is_paid')->default(false);
            $table->decimal('amount', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('final_amount', 10, 2)->default(0);
            $table->string('promo_code', 50)->nullable();
            $table->enum('payment_status', ['pending', 'completed', 'failed', 'refunded'])->nullable();
            $table->string('payment_method', 50)->nullable();
            $table->dateTime('paid_at')->nullable();
            
            // CRM-specific Fields (editable by staff)
            $table->text('admin_notes')->nullable();
            $table->boolean('follow_up_required')->default(false);
            $table->date('follow_up_date')->nullable();
            
            // Notification Tracking
            $table->boolean('confirmation_email_sent')->default(false);
            $table->dateTime('confirmation_email_sent_at')->nullable();
            $table->boolean('reminder_sms_sent')->default(false);
            $table->dateTime('reminder_sms_sent_at')->nullable();
            
            // Sync Metadata
            $table->dateTime('synced_from_bansal_at')->nullable();
            $table->dateTime('last_synced_at')->nullable();
            $table->enum('sync_status', ['new', 'synced', 'error'])->default('new');
            $table->text('sync_error')->nullable();
            
            $table->timestamps();
            
            // Foreign Keys
            $table->foreign('client_id')->references('id')->on('admins')->onDelete('set null');
            $table->foreign('consultant_id')->references('id')->on('appointment_consultants')->onDelete('set null');
            $table->foreign('assigned_by_admin_id')->references('id')->on('admins')->onDelete('set null');
            
            // Indexes
            $table->index('client_id');
            $table->index('consultant_id');
            $table->index('appointment_datetime');
            $table->index('status');
            $table->index('location');
            $table->index(['service_id', 'noe_id']);
            $table->index('sync_status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_appointments');
    }
};
```

### **Migration 3: Create Appointment Sync Logs Table**

```php
<?php
// database/migrations/2024_10_20_000003_create_appointment_sync_logs_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointment_sync_logs', function (Blueprint $table) {
            $table->id();
            $table->enum('sync_type', ['polling', 'manual', 'backfill'])->default('polling');
            $table->dateTime('started_at');
            $table->dateTime('completed_at')->nullable();
            $table->enum('status', ['running', 'success', 'failed'])->default('running');
            
            // Statistics
            $table->integer('appointments_fetched')->default(0);
            $table->integer('appointments_new')->default(0);
            $table->integer('appointments_updated')->default(0);
            $table->integer('appointments_skipped')->default(0);
            $table->integer('appointments_failed')->default(0);
            
            // Details
            $table->text('error_message')->nullable();
            $table->json('sync_details')->nullable()->comment('API response, errors, etc.');
            
            $table->timestamps();
            
            $table->index('sync_type');
            $table->index('status');
            $table->index('started_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointment_sync_logs');
    }
};
```

### **Seeder: Initial Consultants**

```php
<?php
// database/seeders/AppointmentConsultantSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AppointmentConsultantSeeder extends Seeder
{
    public function run(): void
    {
        $consultants = [
            [
                'name' => 'Arun Kumar (Paid Services)',
                'email' => 'arun@bansalimmigration.com',
                'calendar_type' => 'paid',
                'location' => 'melbourne',
                'specializations' => json_encode([1, 6, 7, 8]),
                'is_active' => true,
            ],
            [
                'name' => 'Shubham/Yadwinder (JRP)',
                'email' => 'shubham@bansalimmigration.com',
                'calendar_type' => 'jrp',
                'location' => 'melbourne',
                'specializations' => json_encode([2, 3]),
                'is_active' => true,
            ],
            [
                'name' => 'Education Team',
                'email' => 'education@bansalimmigration.com',
                'calendar_type' => 'education',
                'location' => 'melbourne',
                'specializations' => json_encode([5]),
                'is_active' => true,
            ],
            [
                'name' => 'Tourist Visa Team',
                'email' => 'tourist@bansalimmigration.com',
                'calendar_type' => 'tourist',
                'location' => 'melbourne',
                'specializations' => json_encode([4]),
                'is_active' => true,
            ],
            [
                'name' => 'Adelaide Office',
                'email' => 'adelaide@bansalimmigration.com',
                'calendar_type' => 'adelaide',
                'location' => 'adelaide',
                'specializations' => json_encode([1, 2, 3, 4, 5, 6, 7, 8]),
                'is_active' => true,
            ],
        ];

        foreach ($consultants as $consultant) {
            DB::table('appointment_consultants')->insert(array_merge($consultant, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
```

---

## **📂 SERVICE CLASSES**

### **Service 1: Bansal API Client**

```php
<?php
// app/Services/BansalAppointmentSync/BansalApiClient.php

namespace App\Services\BansalAppointmentSync;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Exception;

class BansalApiClient
{
    /**
     * Create a new API client instance.
     */
    public function __construct(
        protected readonly string $baseUrl = '',
        protected readonly string $apiToken = '',
        protected readonly int $timeout = 30
    ) {
        $this->baseUrl = $baseUrl ?: config('services.bansal_api.url', 'https://bansalimmigration.com/api/crm');
        $this->apiToken = $apiToken ?: config('services.bansal_api.token');
        $this->timeout = $timeout ?: config('services.bansal_api.timeout', 30);

        if (empty($this->apiToken)) {
            throw new Exception('Bansal API token not configured. Set BANSAL_API_TOKEN in .env');
        }
    }

    /**
     * Get configured HTTP client.
     */
    protected function client(): PendingRequest
    {
        return Http::timeout($this->timeout)
            ->withToken($this->apiToken)
            ->acceptJson()
            ->throw(); // Laravel 12: Automatic exception throwing
    }

    /**
     * Fetch recent appointments from Bansal API.
     */
    public function getRecentAppointments(int $minutes = 10): array
    {
        try {
            // Laravel 12: Cleaner HTTP client with throw()
            $response = $this->client()
                ->get("{$this->baseUrl}/appointments/recent", [
                    'minutes' => $minutes
                ]);

            $data = $response->json();

            // Laravel 12: Use throw_if for cleaner validation
            throw_if(
                !isset($data['success']) || $data['success'] !== true,
                new Exception("API returned unsuccessful response: " . json_encode($data))
            );

            return $data['data'] ?? [];
        } catch (Exception $e) {
            Log::error('Bansal API Client Error', [
                'method' => 'getRecentAppointments',
                'error' => $e->getMessage(),
                'minutes' => $minutes
            ]);
            throw $e;
        }
    }

    /**
     * Fetch single appointment by ID
     */
    public function getAppointmentById(int $id): ?array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->withToken($this->apiToken)
                ->get("{$this->baseUrl}/appointments/{$id}");

            if ($response->status() === 404) {
                return null;
            }

            if ($response->failed()) {
                throw new Exception("API request failed: {$response->status()}");
            }

            $data = $response->json();
            return $data['data'] ?? null;
        } catch (Exception $e) {
            Log::error('Bansal API Client Error', [
                'method' => 'getAppointmentById',
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Fetch all appointments in date range (for backfill)
     */
    public function getAppointmentsByDateRange(string $startDate, string $endDate, int $page = 1): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->withToken($this->apiToken)
                ->get("{$this->baseUrl}/appointments", [
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'page' => $page,
                    'per_page' => 100
                ]);

            if ($response->failed()) {
                throw new Exception("API request failed: {$response->status()}");
            }

            return $response->json();
        } catch (Exception $e) {
            Log::error('Bansal API Client Error', [
                'method' => 'getAppointmentsByDateRange',
                'error' => $e->getMessage(),
                'start_date' => $startDate,
                'end_date' => $endDate
            ]);
            throw $e;
        }
    }

    /**
     * Test API connection
     */
    public function testConnection(): bool
    {
        try {
            $response = Http::timeout($this->timeout)
                ->withToken($this->apiToken)
                ->get("{$this->baseUrl}/appointments/recent", ['minutes' => 1]);

            return $response->successful();
        } catch (Exception $e) {
            Log::error('Bansal API Connection Test Failed', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}
```

### **Service 2: Client Matching Service**

```php
<?php
// app/Services/BansalAppointmentSync/ClientMatchingService.php

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
```

### **Service 3: Consultant Assignment Service**

```php
<?php
// app/Services/BansalAppointmentSync/ConsultantAssignmentService.php

namespace App\Services\BansalAppointmentSync;

use App\Models\AppointmentConsultant;
use Illuminate\Support\Facades\Log;

class ConsultantAssignmentService
{
    /**
     * Assign consultant based on appointment details
     * Mimics the 5-calendar logic from AppointmentsController
     */
    public function assignConsultant(array $appointmentData): ?AppointmentConsultant
    {
        $calendarType = $this->determineCalendarType($appointmentData);
        
        if (!$calendarType) {
            Log::warning('Could not determine calendar type for appointment', [
                'appointment_id' => $appointmentData['id'] ?? null,
                'noe_id' => $appointmentData['noe_id'] ?? null,
                'service_id' => $appointmentData['service_id'] ?? null,
                'location' => $appointmentData['location'] ?? null
            ]);
            return null;
        }

        $consultant = AppointmentConsultant::where('calendar_type', $calendarType)
            ->where('is_active', true)
            ->first();

        if (!$consultant) {
            Log::error('No active consultant found for calendar type', [
                'calendar_type' => $calendarType
            ]);
        }

        return $consultant;
    }

    /**
     * Determine calendar type based on appointment data
     * Logic copied from resources/views/Admin/appointments/calender.blade.php
     */
    protected function determineCalendarType(array $appointment): ?string
    {
        $location = $appointment['location'] ?? null;
        $inpersonAddress = $appointment['inperson_address'] ?? null;
        $noeId = $appointment['noe_id'] ?? null;
        $serviceId = $appointment['service_id'] ?? null;
        
        // Map location string to inperson_address if needed
        if ($location === 'adelaide' || $inpersonAddress == 1) {
            return 'adelaide';
        }

        // Melbourne calendars (based on noe_id and service_id)
        if ($location === 'melbourne' || $inpersonAddress == 2 || empty($inpersonAddress)) {
            
            // Education: noe_id=5, service_id=2 (Free)
            if ($noeId == 5 && $serviceId == 2) {
                return 'education';
            }

            // JRP: noe_id in [2,3], service_id=2 (Free)
            if (in_array($noeId, [2, 3]) && $serviceId == 2) {
                return 'jrp';
            }

            // Tourist: noe_id=4, service_id=2 (Free)
            if ($noeId == 4 && $serviceId == 2) {
                return 'tourist';
            }

            // Others/Paid:
            // - service_id=1 (Paid) with any noe_id in [1,2,3,4,5,6,7,8]
            // - OR service_id=2 (Free) with noe_id in [1,6,7]
            if ($serviceId == 1 && in_array($noeId, [1, 2, 3, 4, 5, 6, 7, 8])) {
                return 'paid';
            }
            if ($serviceId == 2 && in_array($noeId, [1, 6, 7])) {
                return 'paid';
            }
        }

        return null;
    }

    /**
     * Get consultant by calendar type
     */
    public function getConsultantByCalendarType(string $calendarType): ?AppointmentConsultant
    {
        return AppointmentConsultant::where('calendar_type', $calendarType)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Get all active consultants
     */
    public function getAllConsultants(): \Illuminate\Database\Eloquent\Collection
    {
        return AppointmentConsultant::where('is_active', true)->get();
    }
}
```

### **Service 4: Appointment Sync Service (Main Orchestrator)**

```php
<?php
// app/Services/BansalAppointmentSync/AppointmentSyncService.php

namespace App\Services\BansalAppointmentSync;

use App\Models\BookingAppointment;
use App\Models\AppointmentSyncLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Exception;

class AppointmentSyncService
{
    protected BansalApiClient $apiClient;
    protected ClientMatchingService $clientMatcher;
    protected ConsultantAssignmentService $consultantAssigner;
    
    protected AppointmentSyncLog $syncLog;

    public function __construct(
        BansalApiClient $apiClient,
        ClientMatchingService $clientMatcher,
        ConsultantAssignmentService $consultantAssigner
    ) {
        $this->apiClient = $apiClient;
        $this->clientMatcher = $clientMatcher;
        $this->consultantAssigner = $consultantAssigner;
    }

    /**
     * Sync recent appointments (main polling method)
     */
    public function syncRecentAppointments(int $minutes = 10): array
    {
        // Create sync log
        $this->syncLog = AppointmentSyncLog::create([
            'sync_type' => 'polling',
            'started_at' => now(),
            'status' => 'running'
        ]);

        $stats = [
            'fetched' => 0,
            'new' => 0,
            'updated' => 0,
            'skipped' => 0,
            'failed' => 0,
            'errors' => []
        ];

        try {
            Log::info('Starting appointment sync', ['minutes' => $minutes]);

            // Fetch appointments from Bansal API
            $appointments = $this->apiClient->getRecentAppointments($minutes);
            $stats['fetched'] = count($appointments);

            Log::info("Fetched {$stats['fetched']} appointments from Bansal API");

            // Process each appointment
            foreach ($appointments as $appointmentData) {
                try {
                    $result = $this->processAppointment($appointmentData);
                    
                    if ($result === 'new') {
                        $stats['new']++;
                    } elseif ($result === 'updated') {
                        $stats['updated']++;
                    } elseif ($result === 'skipped') {
                        $stats['skipped']++;
                    }
                } catch (Exception $e) {
                    $stats['failed']++;
                    $stats['errors'][] = [
                        'appointment_id' => $appointmentData['id'] ?? 'unknown',
                        'error' => $e->getMessage()
                    ];
                    
                    Log::error('Failed to process appointment', [
                        'appointment_id' => $appointmentData['id'] ?? null,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }

            // Update sync log
            $this->syncLog->update([
                'completed_at' => now(),
                'status' => $stats['failed'] > 0 ? 'failed' : 'success',
                'appointments_fetched' => $stats['fetched'],
                'appointments_new' => $stats['new'],
                'appointments_updated' => $stats['updated'],
                'appointments_skipped' => $stats['skipped'],
                'appointments_failed' => $stats['failed'],
                'sync_details' => json_encode($stats)
            ]);

            Log::info('Appointment sync completed', $stats);

            return $stats;
        } catch (Exception $e) {
            $this->syncLog->update([
                'completed_at' => now(),
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'appointments_fetched' => $stats['fetched'],
                'appointments_failed' => $stats['failed']
            ]);

            Log::error('Appointment sync failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw $e;
        }
    }

    /**
     * Process single appointment
     */
    protected function processAppointment(array $appointmentData): string
    {
        $bansalId = $appointmentData['id'];

        // Check if already exists
        $existingAppointment = BookingAppointment::where('bansal_appointment_id', $bansalId)->first();

        if ($existingAppointment) {
            // Update if needed (optional - you might want to skip updates)
            Log::info('Appointment already exists, skipping', ['bansal_id' => $bansalId]);
            return 'skipped';
        }

        // Match or create client
        $client = $this->clientMatcher->findOrCreateClient($appointmentData);

        // Assign consultant
        $consultant = $this->consultantAssigner->assignConsultant($appointmentData);

        // Map status
        $status = $this->mapStatus($appointmentData['status'] ?? 'pending');

        // Create appointment record
        $appointment = BookingAppointment::create([
            'bansal_appointment_id' => $bansalId,
            'order_hash' => $appointmentData['order_hash'] ?? null,
            
            'client_id' => $client?->id,
            'consultant_id' => $consultant?->id,
            
            'client_name' => $appointmentData['full_name'],
            'client_email' => $appointmentData['email'],
            'client_phone' => $appointmentData['phone'] ?? null,
            'client_timezone' => 'Australia/Melbourne',
            
            'appointment_datetime' => Carbon::parse($appointmentData['appointment_datetime']),
            'timeslot_full' => $appointmentData['appointment_time'] ?? null,
            'duration_minutes' => $appointmentData['duration_minutes'] ?? 15,
            'location' => $appointmentData['location'],
            'inperson_address' => $this->mapInpersonAddress($appointmentData['location']),
            'meeting_type' => $appointmentData['meeting_type'] ?? 'in_person',
            'preferred_language' => $appointmentData['preferred_language'] ?? 'English',
            
            'service_id' => $this->mapServiceId($appointmentData),
            'noe_id' => $this->mapNoeId($appointmentData),
            'enquiry_type' => $appointmentData['enquiry_type'] ?? null,
            'service_type' => $appointmentData['service_type'] ?? null,
            'enquiry_details' => $appointmentData['enquiry_details'] ?? null,
            
            'status' => $status,
            'confirmed_at' => $status === 'confirmed' ? now() : null,
            
            'is_paid' => $appointmentData['is_paid'] ?? false,
            'amount' => $appointmentData['amount'] ?? 0,
            'discount_amount' => $appointmentData['discount_amount'] ?? 0,
            'final_amount' => $appointmentData['final_amount'] ?? 0,
            'promo_code' => $appointmentData['promo_code'] ?? null,
            'payment_status' => $this->mapPaymentStatus($appointmentData),
            'payment_method' => $appointmentData['payment']['payment_method'] ?? null,
            'paid_at' => !empty($appointmentData['payment']['paid_at']) 
                ? Carbon::parse($appointmentData['payment']['paid_at']) 
                : null,
            
            'synced_from_bansal_at' => now(),
            'last_synced_at' => now(),
            'sync_status' => 'synced',
        ]);

        Log::info('Created new appointment', [
            'bansal_id' => $bansalId,
            'crm_id' => $appointment->id,
            'client_id' => $client?->id,
            'consultant_id' => $consultant?->id
        ]);

        return 'new';
    }

    /**
     * Map location to inperson_address (legacy compatibility)
     */
    protected function mapInpersonAddress(string $location): ?int
    {
        return match($location) {
            'adelaide' => 1,
            'melbourne' => 2,
            default => null
        };
    }

    /**
     * Map service_id from Bansal data
     */
    protected function mapServiceId(array $appointmentData): ?int
    {
        // Check if paid
        if (!empty($appointmentData['is_paid']) && $appointmentData['is_paid'] === true) {
            return 1; // Paid
        }
        
        if (!empty($appointmentData['final_amount']) && $appointmentData['final_amount'] > 0) {
            return 1; // Paid
        }

        return 2; // Free
    }

    /**
     * Map noe_id from enquiry_type
     */
    protected function mapNoeId(array $appointmentData): ?int
    {
        $enquiryType = $appointmentData['enquiry_type'] ?? null;

        return match($enquiryType) {
            'tr' => 1,
            'jrp' => 2,
            'skill_assessment' => 3,
            'tourist' => 4,
            'education' => 5,
            'pr_complex' => 6,
            default => null
        };
    }

    /**
     * Map status from Bansal to CRM
     */
    protected function mapStatus(string $bansalStatus): string
    {
        return match($bansalStatus) {
            'pending' => 'pending',
            'confirmed' => 'confirmed',
            'completed' => 'completed',
            'cancelled' => 'cancelled',
            'no_show' => 'no_show',
            default => 'pending'
        };
    }

    /**
     * Map payment status
     */
    protected function mapPaymentStatus(array $appointmentData): ?string
    {
        if (empty($appointmentData['payment'])) {
            return null;
        }

        $paymentStatus = $appointmentData['payment']['status'] ?? null;

        return match($paymentStatus) {
            'completed', 'succeeded' => 'completed',
            'pending', 'processing' => 'pending',
            'failed' => 'failed',
            'refunded' => 'refunded',
            default => null
        };
    }

    /**
     * Backfill historical appointments
     */
    public function backfillHistoricalData(Carbon $startDate, Carbon $endDate): array
    {
        $this->syncLog = AppointmentSyncLog::create([
            'sync_type' => 'backfill',
            'started_at' => now(),
            'status' => 'running'
        ]);

        $stats = [
            'fetched' => 0,
            'new' => 0,
            'skipped' => 0,
            'failed' => 0
        ];

        try {
            Log::info('Starting backfill', [
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString()
            ]);

            $page = 1;
            $hasMore = true;

            while ($hasMore) {
                $response = $this->apiClient->getAppointmentsByDateRange(
                    $startDate->toDateString(),
                    $endDate->toDateString(),
                    $page
                );

                $appointments = $response['data'] ?? [];
                $pagination = $response['pagination'] ?? [];

                $stats['fetched'] += count($appointments);

                foreach ($appointments as $appointmentData) {
                    try {
                        $result = $this->processAppointment($appointmentData);
                        if ($result === 'new') {
                            $stats['new']++;
                        } else {
                            $stats['skipped']++;
                        }
                    } catch (Exception $e) {
                        $stats['failed']++;
                        Log::error('Backfill: Failed to process appointment', [
                            'appointment_id' => $appointmentData['id'] ?? null,
                            'error' => $e->getMessage()
                        ]);
                    }
                }

                // Check if more pages
                $hasMore = !empty($pagination['current_page']) && 
                          !empty($pagination['last_page']) && 
                          $pagination['current_page'] < $pagination['last_page'];
                $page++;

                // Add delay between pages to avoid rate limiting
                if ($hasMore) {
                    sleep(2);
                }
            }

            $this->syncLog->update([
                'completed_at' => now(),
                'status' => 'success',
                'appointments_fetched' => $stats['fetched'],
                'appointments_new' => $stats['new'],
                'appointments_skipped' => $stats['skipped'],
                'appointments_failed' => $stats['failed']
            ]);

            Log::info('Backfill completed', $stats);

            return $stats;
        } catch (Exception $e) {
            $this->syncLog->update([
                'completed_at' => now(),
                'status' => 'failed',
                'error_message' => $e->getMessage()
            ]);

            throw $e;
        }
    }
}
```

### **Service 5: Notification Service**

```php
<?php
// app/Services/BansalAppointmentSync/NotificationService.php

namespace App\Services\BansalAppointmentSync;

use App\Models\BookingAppointment;
use App\Services\Sms\UnifiedSmsManager;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    protected UnifiedSmsManager $smsManager;

    public function __construct(UnifiedSmsManager $smsManager)
    {
        $this->smsManager = $smsManager;
    }

    /**
     * Send detailed follow-up confirmation email
     * (This is sent AFTER customer already got instant confirmation from Bansal website)
     */
    public function sendDetailedConfirmationEmail(BookingAppointment $appointment): bool
    {
        try {
            // Only send if not already sent
            if ($appointment->confirmation_email_sent) {
                return true;
            }

            $details = [
                'client_name' => $appointment->client_name,
                'appointment_datetime' => $appointment->appointment_datetime,
                'timeslot_full' => $appointment->timeslot_full,
                'location' => $appointment->location,
                'consultant' => $appointment->consultant?->name,
                'service_type' => $appointment->service_type,
                'admin_notes' => $appointment->admin_notes,
            ];

            Mail::to($appointment->client_email)->send(
                new \App\Mail\AppointmentDetailedConfirmation($details)
            );

            $appointment->update([
                'confirmation_email_sent' => true,
                'confirmation_email_sent_at' => now()
            ]);

            Log::info('Sent detailed confirmation email', [
                'appointment_id' => $appointment->id,
                'email' => $appointment->client_email
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send confirmation email', [
                'appointment_id' => $appointment->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send reminder SMS (24 hours before appointment)
     */
    public function sendReminderSms(BookingAppointment $appointment): bool
    {
        try {
            if ($appointment->reminder_sms_sent) {
                return true;
            }

            $phone = $appointment->client_phone;
            if (empty($phone)) {
                Log::warning('No phone number for reminder SMS', [
                    'appointment_id' => $appointment->id
                ]);
                return false;
            }

            $message = "BANSAL IMMIGRATION: Reminder - You have an appointment tomorrow at {$appointment->timeslot_full} at our {$appointment->location} office. Please be on time. If you need to reschedule, call us at 1300 859 368.";

            $result = $this->smsManager->sendSms($phone, $message, 'reminder', [
                'appointment_id' => $appointment->id,
                'client_id' => $appointment->client_id,
            ]);

            if ($result['success']) {
                $appointment->update([
                    'reminder_sms_sent' => true,
                    'reminder_sms_sent_at' => now()
                ]);

                Log::info('Sent reminder SMS', [
                    'appointment_id' => $appointment->id,
                    'phone' => $phone
                ]);
            }

            return $result['success'];
        } catch (\Exception $e) {
            Log::error('Failed to send reminder SMS', [
                'appointment_id' => $appointment->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send reminders for upcoming appointments (24 hours ahead)
     */
    public function sendUpcomingReminders(): array
    {
        $tomorrow = now()->addDay()->startOfDay();
        $endOfTomorrow = now()->addDay()->endOfDay();

        $appointments = BookingAppointment::where('reminder_sms_sent', false)
            ->where('status', 'confirmed')
            ->whereBetween('appointment_datetime', [$tomorrow, $endOfTomorrow])
            ->get();

        $stats = [
            'total' => $appointments->count(),
            'sent' => 0,
            'failed' => 0
        ];

        foreach ($appointments as $appointment) {
            if ($this->sendReminderSms($appointment)) {
                $stats['sent']++;
            } else {
                $stats['failed']++;
            }
        }

        Log::info('Sent appointment reminders', $stats);

        return $stats;
    }
}

/**
 * 📄 NOTE: NotificationService uses UnifiedSmsManager
 * 
 * UnifiedSmsManager EXISTS in: app/Services/Sms/UnifiedSmsManager.php ✅
 * 
 * Features:
 * - Automatic provider selection (Cellcast for AU, Twilio for others)
 * - Activity logging to client timeline
 * - SMS delivery tracking
 * - Template support
 * 
 * Usage in this service:
 * $this->smsManager->sendSms($phone, $message, 'reminder', $context)
 */
```

---

## **📦 MODELS**

### **Model 1: BookingAppointment**

```php
<?php
// app/Models/BookingAppointment.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class BookingAppointment extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'booking_appointments';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'bansal_appointment_id',
        'order_hash',
        'client_id',
        'consultant_id',
        'assigned_by_admin_id',
        'client_name',
        'client_email',
        'client_phone',
        'client_timezone',
        'appointment_datetime',
        'timeslot_full',
        'duration_minutes',
        'location',
        'inperson_address',
        'meeting_type',
        'preferred_language',
        'service_id',
        'noe_id',
        'enquiry_type',
        'service_type',
        'enquiry_details',
        'status',
        'confirmed_at',
        'completed_at',
        'cancelled_at',
        'cancellation_reason',
        'is_paid',
        'amount',
        'discount_amount',
        'final_amount',
        'promo_code',
        'payment_status',
        'payment_method',
        'paid_at',
        'admin_notes',
        'follow_up_required',
        'follow_up_date',
        'confirmation_email_sent',
        'confirmation_email_sent_at',
        'reminder_sms_sent',
        'reminder_sms_sent_at',
        'synced_from_bansal_at',
        'last_synced_at',
        'sync_status',
        'sync_error',
    ];

    /**
     * 📄 IMPORTANT: Add scopes and helpers from APPOINTMENT_SYNC_MISSING_COMPONENTS.md (Section 3)
     * 
     * Required scopes:
     * - scopeActive()
     * - scopeUpcoming()
     * - scopePast()
     * - scopeToday()
     * - scopeStatus()
     * 
     * Required attributes:
     * - getStatusBadgeAttribute()
     * - getFormattedDateAttribute()
     * - getFormattedTimeAttribute()
     * 
     * Required methods:
     * - isUpcoming()
     * - isOverdue()
     * - shouldSendReminder()
     */

    /**
     * Get the attributes that should be cast.
     * 
     * Laravel 12: Use casts() method instead of $casts property
     */
    protected function casts(): array
    {
        return [
            'appointment_datetime' => 'datetime',
            'confirmed_at' => 'datetime',
            'completed_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'paid_at' => 'datetime',
            'follow_up_date' => 'date',
            'confirmation_email_sent_at' => 'datetime',
            'reminder_sms_sent_at' => 'datetime',
            'synced_from_bansal_at' => 'datetime',
            'last_synced_at' => 'datetime',
            'is_paid' => 'boolean',
            'follow_up_required' => 'boolean',
            'confirmation_email_sent' => 'boolean',
            'reminder_sms_sent' => 'boolean',
            'amount' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'final_amount' => 'decimal:2',
        ];
    }

    /**
     * Get the client that owns the appointment.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    public function consultant(): BelongsTo
    {
        return $this->belongsTo(AppointmentConsultant::class, 'consultant_id');
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'assigned_by_admin_id', 'id');
    }

    /**
     * Scopes
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('appointment_datetime', '>=', now());
    }

    public function scopeByLocation($query, string $location)
    {
        return $query->where('location', $location);
    }

    public function scopeByCalendarType($query, string $calendarType)
    {
        return $query->whereHas('consultant', function ($q) use ($calendarType) {
            $q->where('calendar_type', $calendarType);
        });
    }

    /**
     * Laravel 12: Use Attribute class for accessors/mutators
     */
    
    /**
     * Get the formatted appointment date.
     */
    protected function formattedDate(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->appointment_datetime->format('d/m/Y'),
        );
    }

    /**
     * Get the formatted appointment time.
     */
    protected function formattedTime(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->appointment_datetime->format('h:i A'),
        );
    }

    /**
     * Get the status badge color.
     */
    protected function statusBadge(): Attribute
    {
        return Attribute::make(
            get: fn () => match($this->status) {
                'pending' => 'warning',
                'confirmed' => 'success',
                'completed' => 'info',
                'cancelled' => 'danger',
                'no_show' => 'secondary',
                default => 'secondary'
            }
        );
    }
}
```

### **Model 2: AppointmentConsultant**

```php
<?php
// app/Models/AppointmentConsultant.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AppointmentConsultant extends Model
{
    protected $table = 'appointment_consultants';

    protected $fillable = [
        'name',
        'email',
        'calendar_type',
        'location',
        'specializations',
        'is_active',
    ];

    protected $casts = [
        'specializations' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Relationships
     */
    public function appointments(): HasMany
    {
        return $this->hasMany(BookingAppointment::class, 'consultant_id');
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCalendarType($query, string $type)
    {
        return $query->where('calendar_type', $type);
    }

    public function scopeMelbourne($query)
    {
        return $query->where('location', 'melbourne');
    }

    public function scopeAdelaide($query)
    {
        return $query->where('location', 'adelaide');
    }

    /**
     * Accessors
     */
    public function getCalendarTypeDisplayAttribute(): string
    {
        return match($this->calendar_type) {
            'paid' => 'Paid Services (Others)',
            'jrp' => 'JRP/Skill Assessment',
            'education' => 'Education/Student Visa',
            'tourist' => 'Tourist Visa',
            'adelaide' => 'Adelaide Office',
            default => ucfirst($this->calendar_type)
        };
    }
}
```

### **Model 3: AppointmentSyncLog**

```php
<?php
// app/Models/AppointmentSyncLog.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppointmentSyncLog extends Model
{
    protected $table = 'appointment_sync_logs';

    protected $fillable = [
        'sync_type',
        'started_at',
        'completed_at',
        'status',
        'appointments_fetched',
        'appointments_new',
        'appointments_updated',
        'appointments_skipped',
        'appointments_failed',
        'error_message',
        'sync_details',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'sync_details' => 'array',
    ];

    /**
     * Scopes
     */
    public function scopeRecent($query, int $limit = 20)
    {
        return $query->orderBy('started_at', 'desc')->limit($limit);
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeSuccess($query)
    {
        return $query->where('status', 'success');
    }

    /**
     * Accessors
     */
    public function getDurationAttribute(): ?int
    {
        if ($this->started_at && $this->completed_at) {
            return $this->started_at->diffInSeconds($this->completed_at);
        }
        return null;
    }
}
```

---

## **⚙️ CONSOLE COMMANDS**

### **Command 1: Sync Appointments (Main Polling)**

```php
<?php
// app/Console/Commands/SyncBansalAppointments.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\BansalAppointmentSync\AppointmentSyncService;
use Symfony\Component\Console\Attribute\AsCommand;

use function Laravel\Prompts\{info, error, warning, table};

#[AsCommand(name: 'booking:sync-appointments')]
class SyncBansalAppointments extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'booking:sync-appointments 
                            {--minutes=10 : Number of minutes to look back}';

    /**
     * The console command description.
     */
    protected $description = 'Sync recent appointments from Bansal Immigration API';

    /**
     * Execute the console command.
     */
    public function handle(AppointmentSyncService $syncService): int
    {
        $minutes = (int)$this->option('minutes');

        // Laravel 12: Use new prompts helpers
        info("Starting appointment sync (last {$minutes} minutes)...");

        try {
            $stats = $syncService->syncRecentAppointments($minutes);

            info('✓ Sync completed successfully!');
            $this->newLine();
            
            // Laravel 12: Enhanced table output
            table(
                headers: ['Metric', 'Count'],
                rows: [
                    ['Fetched', $stats['fetched']],
                    ['New', $stats['new']],
                    ['Updated', $stats['updated']],
                    ['Skipped', $stats['skipped']],
                    ['Failed', $stats['failed']],
                ]
            );

            if ($stats['failed'] > 0) {
                warning("⚠ {$stats['failed']} appointments failed to sync");
                return self::FAILURE;
            }

            return self::SUCCESS;
        } catch (\Exception $e) {
            error('✗ Sync failed: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}
```

### **Command 2: Backfill Historical Data**

```php
<?php
// app/Console/Commands/BackfillBansalAppointments.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\BansalAppointmentSync\AppointmentSyncService;
use Carbon\Carbon;

class BackfillBansalAppointments extends Command
{
    protected $signature = 'booking:backfill 
                            {start_date : Start date (Y-m-d)}
                            {end_date : End date (Y-m-d)}';

    protected $description = 'Backfill historical appointments from Bansal API';

    public function handle(AppointmentSyncService $syncService): int
    {
        $startDate = Carbon::parse($this->argument('start_date'));
        $endDate = Carbon::parse($this->argument('end_date'));

        if ($startDate->isAfter($endDate)) {
            $this->error('Start date must be before end date');
            return self::FAILURE;
        }

        $this->info("Backfilling appointments from {$startDate->toDateString()} to {$endDate->toDateString()}");
        
        if (!$this->confirm('This may take a while. Continue?')) {
            return self::SUCCESS;
        }

        try {
            $stats = $syncService->backfillHistoricalData($startDate, $endDate);

            $this->info('✓ Backfill completed!');
            $this->newLine();
            $this->table(
                ['Metric', 'Count'],
                [
                    ['Fetched', $stats['fetched']],
                    ['New', $stats['new']],
                    ['Skipped', $stats['skipped']],
                    ['Failed', $stats['failed']],
                ]
            );

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error('✗ Backfill failed: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}
```

### **Command 3: Send Reminders**

```php
<?php
// app/Console/Commands/SendAppointmentReminders.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\BansalAppointmentSync\NotificationService;

class SendAppointmentReminders extends Command
{
    protected $signature = 'booking:send-reminders';

    protected $description = 'Send SMS reminders for appointments happening tomorrow';

    public function handle(NotificationService $notificationService): int
    {
        $this->info('Sending appointment reminders...');

        try {
            $stats = $notificationService->sendUpcomingReminders();

            $this->info("✓ Sent {$stats['sent']} reminders");
            
            if ($stats['failed'] > 0) {
                $this->warn("⚠ {$stats['failed']} reminders failed");
            }

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error('✗ Failed: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}
```

### **Command 4: Test API Connection**

```php
<?php
// app/Console/Commands/TestBansalApiConnection.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\BansalAppointmentSync\BansalApiClient;

class TestBansalApiConnection extends Command
{
    protected $signature = 'booking:test-api';

    protected $description = 'Test connection to Bansal Immigration API';

    public function handle(BansalApiClient $apiClient): int
    {
        $this->info('Testing API connection...');

        try {
            if ($apiClient->testConnection()) {
                $this->info('✓ API connection successful!');
                
                // Try to fetch recent appointments
                $appointments = $apiClient->getRecentAppointments(10);
                $this->info("✓ Fetched " . count($appointments) . " recent appointments");
                
                return self::SUCCESS;
            } else {
                $this->error('✗ API connection failed');
                return self::FAILURE;
            }
        } catch (\Exception $e) {
            $this->error('✗ Error: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}
```

---

## **🔧 CONFIGURATION**

### **Update config/services.php**

```php
<?php
// config/services.php

return [
    // ... existing services ...

    'bansal_api' => [
        'url' => env('BANSAL_API_URL', 'https://bansalimmigration.com/api/crm'),
        'token' => env('BANSAL_API_TOKEN'),
        'timeout' => env('BANSAL_API_TIMEOUT', 30),
    ],
];
```

### **Update .env**

```env
# Bansal Immigration API Configuration
BANSAL_API_URL=https://bansalimmigration.com/api/crm
BANSAL_API_TOKEN=your_token_here
BANSAL_API_TIMEOUT=30
```

### **Register Service Provider**

**📄 See complete code in:** `APPOINTMENT_SYNC_MISSING_COMPONENTS.md` (Section 6)

```php
<?php
// app/Providers/AppointmentSyncServiceProvider.php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\BansalAppointmentSync\BansalApiClient;
use App\Services\BansalAppointmentSync\AppointmentSyncService;
use App\Services\BansalAppointmentSync\ClientMatchingService;
use App\Services\BansalAppointmentSync\ConsultantAssignmentService;
use App\Services\BansalAppointmentSync\NotificationService;

class AppointmentSyncServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Register with proper dependency injection
        $this->app->singleton(BansalApiClient::class, function ($app) {
            return new BansalApiClient(
                config('services.bansal_api.url'),
                config('services.bansal_api.token'),
                config('services.bansal_api.timeout', 30)
            );
        });

        $this->app->singleton(ClientMatchingService::class);
        $this->app->singleton(ConsultantAssignmentService::class);
        $this->app->singleton(NotificationService::class);
        
        $this->app->singleton(AppointmentSyncService::class, function ($app) {
            return new AppointmentSyncService(
                $app->make(BansalApiClient::class),
                $app->make(ClientMatchingService::class),
                $app->make(ConsultantAssignmentService::class)
            );
        });
    }

    public function boot(): void
    {
        // Register authorization gates
        $this->registerPolicies();
    }

    protected function registerPolicies(): void
    {
        \Illuminate\Support\Facades\Gate::define('manage-sync', function ($user) {
            // Only admin (role=1) or super admin (role=12) can manage sync
            return in_array($user->role, [1, 12]);
        });
    }
}
```

**Register in `config/app.php`:**

```php
'providers' => [
    // ...
    App\Providers\AppointmentSyncServiceProvider::class,
],
```

---

## **🎨 ADMIN UI INTEGRATION**

### **Navigation - Add New Icon Next to Current Appointments**

The new "Website Bookings" system will appear as a **separate icon with dropdown** next to your current "Appointments" icon in the top bar.

```
Top Bar Layout:
[Dashboard] [Appointments ▼] [Website Bookings ▼] [Office Visits] [Tasks] [Clients ▼] [Accounts ▼]
             └─ 5 Calendars    └─ New System!
```

### **Update Navigation Header**

**File: `resources/views/Elements/Admin/header_client_detail.blade.php`**

Add this **right after** the existing Appointments dropdown (after line 21):

```php
{{-- Existing Appointments dropdown --}}
<div class="icon-dropdown js-dropdown">
    <a href="{{ route('appointments.index') }}" class="icon-btn" title="Appointments"><i class="fas fa-calendar-alt"></i></a>
    <div class="icon-dropdown-menu">
        <a class="dropdown-item" href="{{ route('appointments.index') }}"><i class="far fa-calendar-alt mr-2"></i> Listings</a>
        <div class="dropdown-divider"></div>
        <a class="dropdown-item" href="{{URL::to('/admin/appointments-others')}}"><i class="far fa-calendar-check mr-2"></i> Arun Calendar</a>
        <a class="dropdown-item" href="{{URL::to('/admin/appointments-jrp')}}"><i class="far fa-calendar mr-2"></i> Tr Calendar</a>
        <a class="dropdown-item" href="{{URL::to('/admin/appointments-education')}}"><i class="fas fa-graduation-cap mr-2"></i> Education</a>
        <a class="dropdown-item" href="{{URL::to('/admin/appointments-tourist')}}"><i class="fas fa-plane mr-2"></i> Tourist Visa</a>
        <a class="dropdown-item" href="{{URL::to('/admin/appointments-adelaide')}}"><i class="fas fa-city mr-2"></i> Adelaide</a>
    </div>
</div>

{{-- ✨ NEW: Website Bookings System --}}
<div class="icon-dropdown js-dropdown">
    <a href="{{ route('booking.appointments.index') }}" class="icon-btn" title="Website Bookings" style="position: relative;">
        <i class="fas fa-globe"></i>
        @php
            $pendingCount = \App\Models\BookingAppointment::where('status', 'pending')->count();
        @endphp
        @if($pendingCount > 0)
            <span class="badge badge-danger" style="position: absolute; top: -5px; right: -5px; font-size: 10px; padding: 2px 5px; border-radius: 10px;">{{ $pendingCount }}</span>
        @endif
    </a>
    <div class="icon-dropdown-menu">
        <a class="dropdown-item" href="{{ route('booking.appointments.index') }}">
            <i class="fas fa-list mr-2"></i> All Bookings
        </a>
        <a class="dropdown-item" href="{{ route('booking.appointments.index', ['status' => 'pending']) }}">
            <i class="fas fa-clock mr-2"></i> Pending 
            @if($pendingCount > 0)
                <span class="badge badge-warning ml-1">{{ $pendingCount }}</span>
            @endif
        </a>
        <div class="dropdown-divider"></div>
        <a class="dropdown-item" href="{{ route('booking.appointments.calendar', ['type' => 'paid']) }}">
            <i class="far fa-calendar-check mr-2"></i> Paid Services
        </a>
        <a class="dropdown-item" href="{{ route('booking.appointments.calendar', ['type' => 'jrp']) }}">
            <i class="far fa-calendar mr-2"></i> JRP Calendar
        </a>
        <a class="dropdown-item" href="{{ route('booking.appointments.calendar', ['type' => 'education']) }}">
            <i class="fas fa-graduation-cap mr-2"></i> Education
        </a>
        <a class="dropdown-item" href="{{ route('booking.appointments.calendar', ['type' => 'tourist']) }}">
            <i class="fas fa-plane mr-2"></i> Tourist Visa
        </a>
        <a class="dropdown-item" href="{{ route('booking.appointments.calendar', ['type' => 'adelaide']) }}">
            <i class="fas fa-city mr-2"></i> Adelaide
        </a>
        <div class="dropdown-divider"></div>
        <a class="dropdown-item" href="{{ route('booking.sync.dashboard') }}">
            <i class="fas fa-sync mr-2"></i> Sync Status
        </a>
        @if(Auth::user() && (Auth::user()->role == 1 || Auth::user()->role == 12))
        <a class="dropdown-item" href="{{ route('booking.sync.manual') }}">
            <i class="fas fa-download mr-2"></i> Manual Sync
        </a>
        @endif
    </div>
</div>
```

---

## **🛣️ ROUTES FOR UI**

**File: `routes/applications.php`**

Add these routes at the bottom:

```php
/*
|--------------------------------------------------------------------------
| Website Booking Appointments (Synced from Bansal Website)
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\Admin\BookingAppointmentsController;

// Laravel 12: Use controller groups and route model binding
Route::controller(BookingAppointmentsController::class)
    ->prefix('booking')
    ->name('booking.')
    ->group(function () {
        
        // Appointment List & Views
        Route::get('/appointments', 'index')
            ->name('appointments.index');
        
        // Laravel 12: Route model binding with typed parameter
        Route::get('/appointments/{appointment:id}', 'show')
            ->name('appointments.show')
            ->whereNumber('appointment');
        
        // Calendar Views (by type)
        Route::get('/appointments/calendar/{type}', 'calendar')
            ->name('appointments.calendar')
            ->whereIn('type', ['paid', 'jrp', 'education', 'tourist', 'adelaide']);
        
        // Update Actions - Laravel 12: Use middleware for CSRF
        Route::middleware(['web', 'auth:admin'])->group(function () {
            Route::post('/appointments/{appointment}/update-status', 'updateStatus')
                ->name('appointments.update-status');
            
            Route::post('/appointments/{appointment}/update-consultant', 'updateConsultant')
                ->name('appointments.update-consultant');
            
            Route::post('/appointments/{appointment}/add-note', 'addNote')
                ->name('appointments.add-note');
            
            // Sync Management
            Route::get('/sync/dashboard', 'syncDashboard')
                ->name('sync.dashboard');
            
            Route::post('/sync/manual', 'manualSync')
                ->name('sync.manual')
                ->can('manage-sync'); // Laravel 12: Inline authorization
        });
        
        // API endpoints for datatables
        Route::get('/api/appointments', 'getAppointments')
            ->name('api.appointments');
    });
```

---

## **🎛️ CONTROLLER FOR UI**

**File: `app/Http/Controllers/Admin/BookingAppointmentsController.php`**

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BookingAppointment;
use App\Models\AppointmentConsultant;
use App\Models\AppointmentSyncLog;
use App\Services\BansalAppointmentSync\AppointmentSyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class BookingAppointmentsController extends Controller
{
    protected AppointmentSyncService $syncService;

    public function __construct(AppointmentSyncService $syncService)
    {
        $this->middleware('auth:admin');
        $this->syncService = $syncService;
    }

    /**
     * Display appointment list
     */
    public function index(Request $request)
    {
        $status = $request->get('status');
        $consultants = AppointmentConsultant::active()->get();
        
        return view('Admin.booking.appointments.index', compact('status', 'consultants'));
    }

    /**
     * Get appointments for DataTables
     */
    public function getAppointments(Request $request)
    {
        $query = BookingAppointment::with(['client', 'consultant']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by consultant
        if ($request->filled('consultant_id')) {
            $query->where('consultant_id', $request->consultant_id);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('appointment_datetime', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('appointment_datetime', '<=', $request->date_to);
        }

        return DataTables::of($query)
            ->addColumn('client_info', function ($appointment) {
                $clientLink = $appointment->client_id 
                    ? route('admin.clients.detail', base64_encode(convert_uuencode($appointment->client_id)))
                    : '#';
                
                return '<a href="' . $clientLink . '" target="_blank">' . 
                       '<strong>' . e($appointment->client_name) . '</strong><br>' .
                       '<small>' . e($appointment->client_email) . '</small>' .
                       '</a>';
            })
            ->addColumn('appointment_info', function ($appointment) {
                return '<strong>' . $appointment->appointment_datetime->format('d/m/Y') . '</strong><br>' .
                       '<small>' . $appointment->timeslot_full . '</small>';
            })
            ->addColumn('consultant_info', function ($appointment) {
                return $appointment->consultant 
                    ? '<span class="badge badge-info">' . e($appointment->consultant->name) . '</span>'
                    : '<span class="badge badge-secondary">Unassigned</span>';
            })
            ->addColumn('status_badge', function ($appointment) {
                $color = $appointment->status_badge;
                $label = ucfirst($appointment->status);
                return '<span class="badge badge-' . $color . '">' . $label . '</span>';
            })
            ->addColumn('payment_info', function ($appointment) {
                if ($appointment->is_paid) {
                    return '<span class="badge badge-success">Paid</span><br>' .
                           '<small>$' . number_format($appointment->final_amount, 2) . '</small>';
                }
                return '<span class="badge badge-secondary">Free</span>';
            })
            ->addColumn('actions', function ($appointment) {
                return '<a href="' . route('booking.appointments.show', $appointment->id) . '" class="btn btn-sm btn-primary">' .
                       '<i class="fas fa-eye"></i>' .
                       '</a>';
            })
            ->rawColumns(['client_info', 'appointment_info', 'consultant_info', 'status_badge', 'payment_info', 'actions'])
            ->make(true);
    }

    /**
     * Show appointment detail
     */
    public function show($id)
    {
        $appointment = BookingAppointment::with(['client', 'consultant', 'assignedBy'])->findOrFail($id);
        $consultants = AppointmentConsultant::active()->get();
        
        return view('Admin.booking.appointments.detail', compact('appointment', 'consultants'));
    }

    /**
     * Calendar view by type
     */
    public function calendar($type)
    {
        $validTypes = ['paid', 'jrp', 'education', 'tourist', 'adelaide'];
        
        if (!in_array($type, $validTypes)) {
            abort(404);
        }

        $appointments = BookingAppointment::with(['client', 'consultant'])
            ->whereHas('consultant', function ($query) use ($type) {
                $query->where('calendar_type', $type);
            })
            ->where('appointment_datetime', '>=', now()->subDays(30))
            ->get();

        return view('Admin.booking.appointments.calendar', compact('type', 'appointments'));
    }

    /**
     * Update appointment status
     */
    public function updateStatus(Request $request, $id)
    {
        $appointment = BookingAppointment::findOrFail($id);
        
        $request->validate([
            'status' => 'required|in:pending,confirmed,completed,cancelled,no_show'
        ]);

        $oldStatus = $appointment->status;
        $appointment->status = $request->status;

        // Set timestamp based on status
        switch ($request->status) {
            case 'confirmed':
                $appointment->confirmed_at = now();
                break;
            case 'completed':
                $appointment->completed_at = now();
                break;
            case 'cancelled':
                $appointment->cancelled_at = now();
                $appointment->cancellation_reason = $request->cancellation_reason;
                break;
        }

        $appointment->save();

        // Log activity
        activity()
            ->performedOn($appointment)
            ->causedBy(Auth::user())
            ->withProperties([
                'old_status' => $oldStatus,
                'new_status' => $request->status
            ])
            ->log('Updated appointment status');

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully'
        ]);
    }

    /**
     * Update consultant assignment
     */
    public function updateConsultant(Request $request, $id)
    {
        $appointment = BookingAppointment::findOrFail($id);
        
        $request->validate([
            'consultant_id' => 'required|exists:appointment_consultants,id'
        ]);

        $appointment->consultant_id = $request->consultant_id;
        $appointment->assigned_by_admin_id = Auth::id();
        $appointment->save();

        return response()->json([
            'success' => true,
            'message' => 'Consultant assigned successfully'
        ]);
    }

    /**
     * Add admin note
     */
    public function addNote(Request $request, $id)
    {
        $appointment = BookingAppointment::findOrFail($id);
        
        $request->validate([
            'note' => 'required|string'
        ]);

        $appointment->admin_notes = $appointment->admin_notes 
            ? $appointment->admin_notes . "\n\n[" . now()->format('Y-m-d H:i') . " - " . Auth::user()->first_name . "]\n" . $request->note
            : "[" . now()->format('Y-m-d H:i') . " - " . Auth::user()->first_name . "]\n" . $request->note;
        
        $appointment->save();

        return response()->json([
            'success' => true,
            'message' => 'Note added successfully'
        ]);
    }

    /**
     * Sync dashboard
     */
    public function syncDashboard()
    {
        $recentLogs = AppointmentSyncLog::recent(20)->get();
        $stats = [
            'total_appointments' => BookingAppointment::count(),
            'pending' => BookingAppointment::where('status', 'pending')->count(),
            'confirmed' => BookingAppointment::where('status', 'confirmed')->count(),
            'completed' => BookingAppointment::where('status', 'completed')->count(),
            'last_sync' => AppointmentSyncLog::latest('started_at')->first(),
            'failed_syncs' => AppointmentSyncLog::failed()->count()
        ];

        return view('Admin.booking.sync.dashboard', compact('recentLogs', 'stats'));
    }

    /**
     * Manual sync trigger (admin only)
     */
    public function manualSync(Request $request)
    {
        if (!Auth::user() || !in_array(Auth::user()->role, [1, 12])) {
            abort(403);
        }

        try {
            $minutes = $request->input('minutes', 60);
            $stats = $this->syncService->syncRecentAppointments($minutes);

            return response()->json([
                'success' => true,
                'message' => 'Sync completed successfully',
                'stats' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sync failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
```

---

## **📄 BASIC VIEWS**

### **Appointment List View**

**File: `resources/views/Admin/booking/appointments/index.blade.php`**

```php
@extends('layouts.admin_console')

@section('title', 'Website Bookings')

@section('content')
<div class="section-body">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-globe mr-2"></i> Website Bookings (Synced from Bansal Website)</h4>
                    <div class="card-header-action">
                        <a href="{{ route('booking.sync.dashboard') }}" class="btn btn-sm btn-info">
                            <i class="fas fa-sync"></i> Sync Status
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <select class="form-control" id="filter-status">
                                <option value="">All Status</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="confirmed">Confirmed</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-control" id="filter-consultant">
                                <option value="">All Consultants</option>
                                @foreach($consultants as $consultant)
                                    <option value="{{ $consultant->id }}">{{ $consultant->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="date" class="form-control" id="filter-date-from" placeholder="From Date">
                        </div>
                        <div class="col-md-3">
                            <input type="date" class="form-control" id="filter-date-to" placeholder="To Date">
                        </div>
                    </div>

                    <!-- DataTable -->
                    <div class="table-responsive">
                        <table class="table table-striped" id="appointments-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Client</th>
                                    <th>Appointment</th>
                                    <th>Service</th>
                                    <th>Consultant</th>
                                    <th>Status</th>
                                    <th>Payment</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    var table = $('#appointments-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("booking.api.appointments") }}',
            data: function(d) {
                d.status = $('#filter-status').val();
                d.consultant_id = $('#filter-consultant').val();
                d.date_from = $('#filter-date-from').val();
                d.date_to = $('#filter-date-to').val();
            }
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'client_info', name: 'client_name' },
            { data: 'appointment_info', name: 'appointment_datetime' },
            { data: 'service_type', name: 'service_type' },
            { data: 'consultant_info', name: 'consultant_id' },
            { data: 'status_badge', name: 'status' },
            { data: 'payment_info', name: 'is_paid' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        order: [[0, 'desc']]
    });

    // Reload table on filter change
    $('#filter-status, #filter-consultant, #filter-date-from, #filter-date-to').change(function() {
        table.ajax.reload();
    });
});
</script>
@endpush
@endsection
```

---

### **Visual Mockup of Top Bar:**

```
┌────────────────────────────────────────────────────────────────────┐
│  ☰  [🏠] [📅 ▼] [🌐 ▼ 🔴3] [👤] [✓] [👥 ▼] [💼 ▼]     🔍 Search  │
│        │      │        │                                            │
│        │      │        └─ NEW! Website Bookings (with badge)       │
│        │      └─ Old Appointments (5 calendars)                    │
│        └─ Dashboard                                                 │
└────────────────────────────────────────────────────────────────────┘

Dropdown for "Website Bookings" (🌐):
┌─────────────────────────────────┐
│ 📋 All Bookings                 │
│ 🕐 Pending (3)                  │
│ ─────────────────────────────── │
│ ✓ Paid Services                 │
│ 📝 JRP Calendar                 │
│ 🎓 Education                    │
│ ✈️ Tourist Visa                 │
│ 🏙️ Adelaide                     │
│ ─────────────────────────────── │
│ 🔄 Sync Status                  │
│ ⬇️ Manual Sync (Admin Only)     │
└─────────────────────────────────┘
```

---

## **🎨 APPOINTMENT INTERACTION UI - Popup/Context Menu**

### **📱 Appointment Quick Actions (Click on Appointment)**

When staff clicks on any appointment in the list or calendar view, a **popup modal** appears with quick actions:

---

### **Visual Mockup: Appointment Detail Popup**

```
┌─────────────────────────────────────────────────────────────────────┐
│  Appointment Details - #156                                    [✕]  │
├─────────────────────────────────────────────────────────────────────┤
│                                                                      │
│  👤 CLIENT INFORMATION                                               │
│  ┌──────────────────────────────────────────────────────────────┐  │
│  │ Name:     John Smith                    [View Client Profile] │  │
│  │ Email:    john.smith@email.com                                │  │
│  │ Phone:    +61 412 345 678                                     │  │
│  │ Status:   🟡 Pending                        [Change Status ▼] │  │
│  └──────────────────────────────────────────────────────────────┘  │
│                                                                      │
│  📅 APPOINTMENT DETAILS                                              │
│  ┌──────────────────────────────────────────────────────────────┐  │
│  │ Date:       Monday, 21 Oct 2024                               │  │
│  │ Time:       10:00 AM - 10:15 AM                               │  │
│  │ Location:   Melbourne Office                                  │  │
│  │ Service:    TR (Temporary Residency)                          │  │
│  │ Consultant: Arun Kumar (Paid Services)  [Reassign ▼]         │  │
│  │ Meeting:    In Person                                         │  │
│  └──────────────────────────────────────────────────────────────┘  │
│                                                                      │
│  💰 PAYMENT INFORMATION                                              │
│  ┌──────────────────────────────────────────────────────────────┐  │
│  │ Status:     ✅ Paid via Stripe                                │  │
│  │ Amount:     $150.00 AUD                                       │  │
│  │ Paid At:    21 Oct 2024, 09:45 AM                            │  │
│  │ Promo:      WELCOME10 (10% off)                               │  │
│  └──────────────────────────────────────────────────────────────┘  │
│                                                                      │
│  📝 CLIENT NOTES                                                     │
│  ┌──────────────────────────────────────────────────────────────┐  │
│  │ "Need help with 485 visa. Currently on student visa expiring │  │
│  │  in 2 months. Have work experience in IT."                    │  │
│  └──────────────────────────────────────────────────────────────┘  │
│                                                                      │
│  📋 ADMIN NOTES (Internal)                             [+ Add Note] │
│  ┌──────────────────────────────────────────────────────────────┐  │
│  │ [21 Oct 2024 09:50 - Sarah]                                   │  │
│  │ Confirmed appointment via email. Client bringing all docs.    │  │
│  │                                                                │  │
│  │ [21 Oct 2024 10:15 - Admin]                                   │  │
│  │ Rescheduled from 10:30 AM to 10:00 AM per client request.    │  │
│  └──────────────────────────────────────────────────────────────┘  │
│                                                                      │
│  🔄 SYNC INFORMATION                                                 │
│  ┌──────────────────────────────────────────────────────────────┐  │
│  │ Bansal ID:   #1234                                            │  │
│  │ Synced:      21 Oct 2024, 09:48 AM                            │  │
│  │ Last Update: 21 Oct 2024, 10:15 AM                            │  │
│  └──────────────────────────────────────────────────────────────┘  │
│                                                                      │
│  QUICK ACTIONS:                                                      │
│  ┌──────────────────────────────────────────────────────────────┐  │
│  │  [✓ Confirm]  [✅ Complete]  [❌ Cancel]  [🔄 Reschedule]     │  │
│  │  [📧 Send Reminder]  [📱 Send SMS]  [🖨️ Print Details]       │  │
│  └──────────────────────────────────────────────────────────────┘  │
│                                                                      │
│                    [Save Changes]  [Close]                           │
└─────────────────────────────────────────────────────────────────────┘
```

---

### **🎯 Status Change Menu (Dropdown)**

When clicking **"Change Status"**, a dropdown appears:

```
┌─────────────────────────────────────────┐
│ Change Appointment Status:              │
├─────────────────────────────────────────┤
│ 🟡 Pending         (Current)            │
│ ✅ Confirmed       ← Click to confirm   │
│ 🔵 In Progress                          │
│ ✔️ Completed                            │
│ ❌ Cancelled                            │
│ 👻 No Show                              │
├─────────────────────────────────────────┤
│ If cancelling, provide reason:          │
│ ┌─────────────────────────────────────┐ │
│ │ [Text area for cancellation reason] │ │
│ └─────────────────────────────────────┘ │
│                                         │
│        [Update Status]  [Cancel]        │
└─────────────────────────────────────────┘
```

**Status Colors & Meanings:**
- 🟡 **Pending** (Yellow) - Booked but not confirmed by staff
- ✅ **Confirmed** (Green) - Staff confirmed appointment
- 🔵 **In Progress** (Blue) - Client checked in, meeting happening
- ✔️ **Completed** (Dark Green) - Meeting finished
- ❌ **Cancelled** (Red) - Cancelled by client or staff
- 👻 **No Show** (Gray) - Client didn't show up

---

### **👨‍💼 Consultant Reassignment Menu**

When clicking **"Reassign"**:

```
┌─────────────────────────────────────────┐
│ Reassign to Consultant:                 │
├─────────────────────────────────────────┤
│ ○ Arun Kumar (Paid Services)  ← Current │
│ ○ Shubham/Yadwinder (JRP)               │
│ ○ Education Team                        │
│ ○ Tourist Visa Team                     │
│ ○ Adelaide Office                       │
├─────────────────────────────────────────┤
│ Reason for reassignment:                │
│ ┌─────────────────────────────────────┐ │
│ │ [Optional text area]                │ │
│ └─────────────────────────────────────┘ │
│                                         │
│        [Reassign]  [Cancel]             │
└─────────────────────────────────────────┘
```

---

### **📝 Add Note Modal**

When clicking **"+ Add Note"**:

```
┌─────────────────────────────────────────┐
│ Add Admin Note (Internal Only)          │
├─────────────────────────────────────────┤
│ ┌─────────────────────────────────────┐ │
│ │                                     │ │
│ │  Type your note here...             │ │
│ │                                     │ │
│ │  (This is only visible to staff,   │ │
│ │   not to the client)                │ │
│ │                                     │ │
│ └─────────────────────────────────────┘ │
│                                         │
│ Note will be saved as:                  │
│ [21 Oct 2024 14:30 - Sarah Johnson]    │
│                                         │
│        [Add Note]  [Cancel]             │
└─────────────────────────────────────────┘
```

**Note History Shows:**
- Timestamp
- Staff member who added it
- Full note content
- Chronological order (newest first)

---

### **📧 Send Reminder Options**

When clicking **"Send Reminder"**:

```
┌─────────────────────────────────────────┐
│ Send Appointment Reminder               │
├─────────────────────────────────────────┤
│ Send to: john.smith@email.com           │
│                                         │
│ Select method:                          │
│ ☑ Email (Detailed confirmation)        │
│ ☑ SMS (+61 412 345 678)                │
│                                         │
│ Message preview:                        │
│ ┌─────────────────────────────────────┐ │
│ │ Hi John,                            │ │
│ │                                     │ │
│ │ Reminder: You have an appointment   │ │
│ │ with Bansal Immigration on Monday,  │ │
│ │ 21 Oct 2024 at 10:00 AM.           │ │
│ │                                     │ │
│ │ Location: Level 8/278 Collins St,   │ │
│ │           Melbourne VIC 3000        │ │
│ │                                     │ │
│ │ Please arrive 5 mins early.         │ │
│ └─────────────────────────────────────┘ │
│                                         │
│        [Send Now]  [Cancel]             │
└─────────────────────────────────────────┘
```

---

### **🔄 Reschedule Appointment**

When clicking **"Reschedule"**:

```
┌─────────────────────────────────────────┐
│ Reschedule Appointment                  │
├─────────────────────────────────────────┤
│ Current:  Monday, 21 Oct 2024, 10:00 AM │
│                                         │
│ New Date:                               │
│ ┌─────────────────────────────────────┐ │
│ │ 📅 [Date Picker]                    │ │
│ └─────────────────────────────────────┘ │
│                                         │
│ New Time Slot:                          │
│ ┌─────────────────────────────────────┐ │
│ │ ○ 09:00 AM - 09:15 AM (Available)  │ │
│ │ ○ 09:15 AM - 09:30 AM (Available)  │ │
│ │ ● 10:00 AM - 10:15 AM (Current)    │ │
│ │ ○ 10:15 AM - 10:30 AM (Available)  │ │
│ │ ✗ 10:30 AM - 10:45 AM (Booked)     │ │
│ └─────────────────────────────────────┘ │
│                                         │
│ ☑ Send email notification to client    │
│ ☑ Send SMS notification                │
│                                         │
│ Reason:                                 │
│ ┌─────────────────────────────────────┐ │
│ │ Client requested earlier time       │ │
│ └─────────────────────────────────────┘ │
│                                         │
│        [Reschedule]  [Cancel]           │
└─────────────────────────────────────────┘
```

---

### **📋 Complete Code: Appointment Detail Modal**

**File: `resources/views/Admin/booking/appointments/partials/appointment-detail-modal.blade.php`**

```php
<!-- Appointment Detail Modal -->
<div class="modal fade" id="appointmentDetailModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-calendar-check mr-2"></i>
                    Appointment Details - #<span id="modal-appointment-id"></span>
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Client Information -->
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        <i class="fas fa-user"></i> Client Information
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <p><strong>Name:</strong> <span id="modal-client-name"></span></p>
                                <p><strong>Email:</strong> <span id="modal-client-email"></span></p>
                                <p><strong>Phone:</strong> <span id="modal-client-phone"></span></p>
                            </div>
                            <div class="col-md-4 text-right">
                                <a href="#" id="modal-view-client" class="btn btn-sm btn-info" target="_blank">
                                    <i class="fas fa-external-link-alt"></i> View Profile
                                </a>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-12">
                                <p>
                                    <strong>Status:</strong> 
                                    <span id="modal-status-badge" class="badge"></span>
                                    <button class="btn btn-sm btn-outline-primary ml-2" onclick="showStatusChangeModal()">
                                        <i class="fas fa-edit"></i> Change Status
                                    </button>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Appointment Details -->
                <div class="card mb-3">
                    <div class="card-header bg-info text-white">
                        <i class="fas fa-calendar"></i> Appointment Details
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Date:</strong> <span id="modal-date"></span></p>
                                <p><strong>Time:</strong> <span id="modal-time"></span></p>
                                <p><strong>Location:</strong> <span id="modal-location"></span></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Service:</strong> <span id="modal-service"></span></p>
                                <p><strong>Meeting Type:</strong> <span id="modal-meeting-type"></span></p>
                                <p>
                                    <strong>Consultant:</strong> 
                                    <span id="modal-consultant"></span>
                                    <button class="btn btn-sm btn-outline-info ml-2" onclick="showReassignModal()">
                                        <i class="fas fa-user-edit"></i> Reassign
                                    </button>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Information (if paid) -->
                <div class="card mb-3" id="modal-payment-section" style="display: none;">
                    <div class="card-header bg-success text-white">
                        <i class="fas fa-dollar-sign"></i> Payment Information
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Status:</strong> <span id="modal-payment-status"></span></p>
                                <p><strong>Amount:</strong> <span id="modal-amount"></span></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Payment Method:</strong> <span id="modal-payment-method"></span></p>
                                <p><strong>Paid At:</strong> <span id="modal-paid-at"></span></p>
                            </div>
                        </div>
                        <p id="modal-promo-code" style="display: none;">
                            <strong>Promo Code:</strong> <span id="modal-promo"></span>
                        </p>
                    </div>
                </div>

                <!-- Client Notes -->
                <div class="card mb-3">
                    <div class="card-header bg-warning text-dark">
                        <i class="fas fa-comment"></i> Client Notes
                    </div>
                    <div class="card-body">
                        <p id="modal-client-notes" class="mb-0"></p>
                    </div>
                </div>

                <!-- Admin Notes -->
                <div class="card mb-3">
                    <div class="card-header bg-secondary text-white">
                        <i class="fas fa-sticky-note"></i> Admin Notes (Internal)
                        <button class="btn btn-sm btn-light float-right" onclick="showAddNoteModal()">
                            <i class="fas fa-plus"></i> Add Note
                        </button>
                    </div>
                    <div class="card-body">
                        <div id="modal-admin-notes">
                            <p class="text-muted">No admin notes yet.</p>
                        </div>
                    </div>
                </div>

                <!-- Sync Information -->
                <div class="card mb-3">
                    <div class="card-header">
                        <i class="fas fa-sync"></i> Sync Information
                    </div>
                    <div class="card-body">
                        <small>
                            <p><strong>Bansal ID:</strong> <span id="modal-bansal-id"></span></p>
                            <p><strong>Synced At:</strong> <span id="modal-synced-at"></span></p>
                            <p><strong>Last Updated:</strong> <span id="modal-updated-at"></span></p>
                        </small>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-bolt"></i> Quick Actions
                    </div>
                    <div class="card-body text-center">
                        <button class="btn btn-success mr-2 mb-2" onclick="confirmAppointment()">
                            <i class="fas fa-check"></i> Confirm
                        </button>
                        <button class="btn btn-primary mr-2 mb-2" onclick="completeAppointment()">
                            <i class="fas fa-check-circle"></i> Complete
                        </button>
                        <button class="btn btn-danger mr-2 mb-2" onclick="cancelAppointment()">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button class="btn btn-info mr-2 mb-2" onclick="showRescheduleModal()">
                            <i class="fas fa-calendar-alt"></i> Reschedule
                        </button>
                        <button class="btn btn-warning mr-2 mb-2" onclick="sendReminder()">
                            <i class="fas fa-envelope"></i> Send Reminder
                        </button>
                        <button class="btn btn-secondary mr-2 mb-2" onclick="sendSMS()">
                            <i class="fas fa-sms"></i> Send SMS
                        </button>
                        <button class="btn btn-outline-dark mb-2" onclick="printDetails()">
                            <i class="fas fa-print"></i> Print
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for Modal Actions -->
<script>
let currentAppointmentId = null;

function openAppointmentModal(appointmentId) {
    currentAppointmentId = appointmentId;
    
    // Fetch appointment details via AJAX
    $.ajax({
        url: '/admin/booking/appointments/' + appointmentId,
        method: 'GET',
        success: function(response) {
            populateModal(response.data);
            $('#appointmentDetailModal').modal('show');
        },
        error: function() {
            alert('Failed to load appointment details');
        }
    });
}

function populateModal(appointment) {
    $('#modal-appointment-id').text(appointment.id);
    $('#modal-client-name').text(appointment.client_name);
    $('#modal-client-email').text(appointment.client_email);
    $('#modal-client-phone').text(appointment.client_phone);
    
    // Status badge
    const statusColors = {
        'pending': 'warning',
        'confirmed': 'success',
        'in_progress': 'info',
        'completed': 'primary',
        'cancelled': 'danger',
        'no_show': 'secondary'
    };
    $('#modal-status-badge')
        .removeClass()
        .addClass('badge badge-' + statusColors[appointment.status])
        .text(appointment.status.replace('_', ' ').toUpperCase());
    
    $('#modal-date').text(appointment.formatted_date);
    $('#modal-time').text(appointment.timeslot_full);
    $('#modal-location').text(appointment.location);
    $('#modal-service').text(appointment.service_type);
    $('#modal-meeting-type').text(appointment.meeting_type);
    $('#modal-consultant').text(appointment.consultant ? appointment.consultant.name : 'Unassigned');
    
    // Payment info
    if (appointment.is_paid) {
        $('#modal-payment-section').show();
        $('#modal-payment-status').html('<span class="badge badge-success">Paid</span>');
        $('#modal-amount').text('$' + appointment.final_amount + ' AUD');
        $('#modal-payment-method').text(appointment.payment_method || 'Stripe');
        $('#modal-paid-at').text(appointment.paid_at);
        
        if (appointment.promo_code) {
            $('#modal-promo-code').show();
            $('#modal-promo').text(appointment.promo_code);
        }
    }
    
    // Notes
    $('#modal-client-notes').text(appointment.enquiry_details || 'No notes provided');
    
    if (appointment.admin_notes) {
        $('#modal-admin-notes').html('<pre>' + appointment.admin_notes + '</pre>');
    }
    
    // Sync info
    $('#modal-bansal-id').text(appointment.bansal_appointment_id);
    $('#modal-synced-at').text(appointment.synced_from_bansal_at);
    $('#modal-updated-at').text(appointment.updated_at);
    
    // View client link
    if (appointment.client_id) {
        $('#modal-view-client').attr('href', '/admin/clients/detail/' + btoa(appointment.client_id));
    }
}

function confirmAppointment() {
    if (confirm('Confirm this appointment?')) {
        updateStatus('confirmed');
    }
}

function completeAppointment() {
    if (confirm('Mark this appointment as completed?')) {
        updateStatus('completed');
    }
}

function cancelAppointment() {
    const reason = prompt('Cancellation reason:');
    if (reason) {
        updateStatus('cancelled', reason);
    }
}

function updateStatus(status, reason = null) {
    $.ajax({
        url: '/admin/booking/appointments/' + currentAppointmentId + '/update-status',
        method: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            status: status,
            cancellation_reason: reason
        },
        success: function(response) {
            if (response.success) {
                alert('Status updated successfully!');
                $('#appointmentDetailModal').modal('hide');
                // Reload table
                $('#appointments-table').DataTable().ajax.reload();
            }
        },
        error: function() {
            alert('Failed to update status');
        }
    });
}

// More functions: sendReminder(), sendSMS(), showRescheduleModal(), etc.
</script>
```

---

### **🎨 Calendar View with Popup**

When clicking on appointment in **calendar view**:

```
Calendar (Month View):
┌─────────────────────────────────────────────────┐
│  October 2024           [< Today >]             │
├───────┬───────┬───────┬───────┬───────┬─────────┤
│  Mon  │  Tue  │  Wed  │  Thu  │  Fri  │  Sat    │
├───────┼───────┼───────┼───────┼───────┼─────────┤
│   14  │   15  │   16  │   17  │   18  │   19    │
├───────┼───────┼───────┼───────┼───────┼─────────┤
│   21  │   22  │   23  │   24  │   25  │   26    │
│       │       │       │       │       │         │
│ [10AM]│       │ [2PM] │       │       │         │
│ John ←── Click here!                            │
│ Smith │       │ Sarah │       │       │         │
│ TR    │       │ JRP   │       │       │         │
│ 🟡    │       │ ✅    │       │       │         │
└───────┴───────┴───────┴───────┴───────┴─────────┘
         ↓
    Opens popup modal (same as above)
```

---

### **📊 Appointment List View with Context Menu**

**Right-click** on appointment row:

```
┌─────────────────────────────┐
│ Quick Actions:              │
├─────────────────────────────┤
│ 👁️ View Details            │
│ ✅ Confirm                  │
│ ✔️ Complete                 │
│ ❌ Cancel                   │
│ 🔄 Reschedule              │
│ ──────────────────────────  │
│ 📧 Send Email              │
│ 📱 Send SMS                │
│ ──────────────────────────  │
│ 📝 Add Note                │
│ 👨‍💼 Reassign Consultant     │
│ ──────────────────────────  │
│ 🖨️ Print Details           │
│ 👤 View Client Profile     │
└─────────────────────────────┘
```

---

## **⏰ CRON SCHEDULE**

### **Console Commands Required**

**📄 All 4 console commands are in:** `APPOINTMENT_SYNC_MISSING_COMPONENTS.md` (Section 1)

Commands you need to create:
1. ✅ `SyncAppointments.php` - Main polling sync
2. ✅ `SendAppointmentReminders.php` - Daily reminder sender
3. ✅ `TestBansalApi.php` - API connection tester
4. ✅ `BackfillAppointments.php` - Historical data import

### **Update app/Console/Kernel.php**

```php
<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Laravel 12: Enhanced scheduling with better logging
        $schedule->command('booking:sync-appointments --minutes=15')
            ->everyTenMinutes()
            ->withoutOverlapping(maxLockTime: 300) // Laravel 12: Max lock time
            ->onOneServer() // Laravel 12: Run on single server in load-balanced setup
            ->appendOutputTo(storage_path('logs/appointment-sync.log'))
            ->emailOutputOnFailure(config('mail.admin_email'))
            ->runInBackground(); // Laravel 12: Non-blocking execution

        // Send reminders daily at 9 AM
        $schedule->command('booking:send-reminders')
            ->dailyAt('09:00')
            ->timezone('Australia/Melbourne') // Laravel 12: Explicit timezone
            ->withoutOverlapping(maxLockTime: 600)
            ->onOneServer()
            ->appendOutputTo(storage_path('logs/appointment-reminders.log'))
            ->emailOutputOnFailure(config('mail.admin_email'));
    }

    /**
     * Register the commands for the application.
     * 
     * Laravel 12: Commands are auto-discovered, no need to register manually
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
```

---

## **📅 IMPLEMENTATION TIMELINE**

### **Week 1: Foundation**
- [ ] Create migrations (3 tables)
- [ ] Run migrations
- [ ] Seed appointment_consultants
- [ ] Configure .env with API token
- [ ] Test API connection

### **Week 2: Core Services**
- [ ] Create BansalApiClient service
- [ ] Create ClientMatchingService
- [ ] Create ConsultantAssignmentService
- [ ] Write unit tests for services

### **Week 3: Sync Service**
- [ ] Create AppointmentSyncService
- [ ] Create BookingAppointment model
- [ ] Create console command
- [ ] Test with sample data

### **Week 4: Testing & Refinement**
- [ ] Test polling sync
- [ ] Fix bugs and edge cases
- [ ] Test client matching logic
- [ ] Test consultant assignment

### **Week 5: Backfill & Notifications**
- [ ] Create backfill command
- [ ] Backfill historical data
- [ ] Create NotificationService
- [ ] Test email/SMS sending

### **Week 6: Admin UI (Optional)**
- [ ] Create appointment list view
- [ ] Add filtering by calendar type
- [ ] Create detail view
- [ ] Add manual sync button

### **Week 7-8: Production Deployment**
- [ ] Set up cron job
- [ ] Monitor sync logs
- [ ] Train staff
- [ ] Handle any issues

### **Week 9-10: Buffer & Documentation**
- [ ] Fix any production issues
- [ ] Document system
- [ ] Create troubleshooting guide

---

## **✅ SUCCESS CRITERIA**

### **Functional**
- [ ] Can sync appointments every 10 minutes
- [ ] Can match existing clients by email/phone
- [ ] Can create new clients automatically
- [ ] Can assign to correct calendar/consultant
- [ ] Can send reminder SMS 24h before
- [ ] Can backfill historical data

### **Non-Functional**
- [ ] Sync completes in < 30 seconds
- [ ] No duplicate appointments created
- [ ] Proper error logging
- [ ] API token security maintained
- [ ] All data fields mapped correctly

---

## **🚨 TROUBLESHOOTING**

### **Issue: API Connection Fails**
```bash
# Test connection
php artisan booking:test-api

# Check logs
tail -f storage/logs/laravel.log

# Verify token
php artisan tinker
>>> config('services.bansal_api.token')
```

### **Issue: Duplicate Clients Created**
- Check email/phone matching logic in `ClientMatchingService`
- Verify existing clients in database
- Check for typos in email/phone format

### **Issue: Consultant Not Assigned**
- Check `appointment_consultants` table has data
- Verify `noe_id` and `service_id` mapping
- Check consultant `is_active` status

### **Issue: Cron Not Running**
```bash
# Manual sync test
php artisan booking:sync-appointments

# Check cron status
php artisan schedule:list

# Run scheduler manually
php artisan schedule:run
```

---

## **📖 NEXT STEPS AFTER IMPLEMENTATION**

### **Phase 2 Enhancements (Future)**
1. Add webhook receiver endpoint (real-time sync)
2. Create admin dashboard for sync monitoring
3. Add two-way sync (CRM updates push back to Bansal)
4. Generate Google Calendar invites
5. Automated follow-up task creation
6. Analytics and reporting

---

**END OF IMPLEMENTATION PLAN**

---

## **🚀 QUICK START GUIDE**

### **Step 1: Review Documents**

1. ✅ Read this complete plan
2. ✅ Open `APPOINTMENT_SYNC_MISSING_COMPONENTS.md`
3. ✅ Review both documents together

### **Step 2: Dependencies Check**

```bash
# Verify Laravel 12 is installed
php artisan --version
# Should show: Laravel Framework 12.x

# Check required packages (already in composer.json)
composer show yajra/laravel-datatables-oracle
composer show spatie/laravel-activitylog
composer show twilio/sdk
```

### **Step 3: Create Files**

**From APPOINTMENT_SYNC_MISSING_COMPONENTS.md:**
- ✅ 4 Console Commands
- ✅ 1 Mail Class
- ✅ 1 Service Provider
- ✅ Model Scopes (add to existing models)

**From this document:**
- ✅ 3 Migrations
- ✅ 1 Seeder
- ✅ 5 Service Classes
- ✅ 3 Models
- ✅ 1 Controller
- ✅ Route definitions

### **Step 4: Configuration**

```bash
# 1. Update config/services.php
# Add bansal_api section (see MISSING_COMPONENTS.md Section 4)

# 2. Update config/app.php
# Add AppointmentSyncServiceProvider to providers array

# 3. Update .env
# Add BANSAL_API_* variables (see MISSING_COMPONENTS.md Section 5)
```

### **Step 5: Database Setup**

```bash
# 1. Create migrations (copy code from this document, Section: DATABASE SCHEMA)
php artisan make:migration create_appointment_consultants_table
php artisan make:migration create_booking_appointments_table
php artisan make:migration create_appointment_sync_logs_table

# 2. Run migrations
php artisan migrate

# 3. Create and run seeder
php artisan make:seeder AppointmentConsultantSeeder
php artisan db:seed --class=AppointmentConsultantSeeder
```

### **Step 6: Test Setup**

```bash
# Test API connection
php artisan booking:test-api --show-config

# If successful, run first sync
php artisan booking:sync-appointments --minutes=30

# Check results
php artisan tinker
>>> App\Models\BookingAppointment::count()
>>> App\Models\AppointmentSyncLog::latest()->first()
```

### **Step 7: Setup Cron**

```bash
# Add to crontab
crontab -e

# Add this line:
* * * * * cd /path/to/migrationmanager && php artisan schedule:run >> /dev/null 2>&1
```

### **Step 8: Verify Everything**

```bash
# Check scheduled tasks
php artisan schedule:list

# Manual sync test
php artisan booking:sync-appointments --minutes=60

# Check logs
tail -f storage/logs/laravel.log
tail -f storage/logs/appointment-sync.log
```

---

## **📚 DOCUMENT REFERENCES**

### **Main Implementation Plan (THIS DOCUMENT)**
- Database schema and migrations
- Service classes (5 services)
- Models (3 models)
- Controller (BookingAppointmentsController)
- Routes
- Views (partial code)
- Architecture overview

### **Missing Components Document**
📄 `APPOINTMENT_SYNC_MISSING_COMPONENTS.md`
- Console commands (complete code)
- Mail classes (complete code)
- Model scopes (complete code)
- Config updates (complete code)
- .env variables
- Service provider (complete code with gates)
- Troubleshooting guide
- Quick start checklist

---

## **✅ IMPLEMENTATION CHECKLIST**

Use this checklist to track your progress:

**Phase 1: Setup (Day 1)**
- [ ] Review both documents
- [ ] Update .env with API credentials
- [ ] Update config/services.php
- [ ] Register AppointmentSyncServiceProvider

**Phase 2: Database (Day 1-2)**
- [ ] Create 3 migrations
- [ ] Run migrations
- [ ] Create seeder
- [ ] Seed consultants

**Phase 3: Services (Day 3-5)**
- [ ] Create BansalApiClient
- [ ] Create ClientMatchingService
- [ ] Create ConsultantAssignmentService
- [ ] Create AppointmentSyncService
- [ ] Create NotificationService

**Phase 4: Models (Day 5-6)**
- [ ] Create BookingAppointment model
- [ ] Create AppointmentConsultant model
- [ ] Create AppointmentSyncLog model
- [ ] Add all scopes and helpers

**Phase 5: Commands (Day 6-7)**
- [ ] Create SyncAppointments command
- [ ] Create SendAppointmentReminders command
- [ ] Create TestBansalApi command
- [ ] Create BackfillAppointments command

**Phase 6: Mail (Day 7)**
- [ ] Create AppointmentDetailedConfirmation mail
- [ ] Create email view
- [ ] Test email sending

**Phase 7: Controller & Routes (Day 8-9)**
- [ ] Create BookingAppointmentsController
- [ ] Add routes to routes/applications.php
- [ ] Test routes

**Phase 8: Testing (Day 10-12)**
- [ ] Test API connection
- [ ] Run manual sync
- [ ] Test client matching
- [ ] Test consultant assignment
- [ ] Test notifications

**Phase 9: UI (Day 13-15)**
- [ ] Create appointment list view
- [ ] Create appointment detail view
- [ ] Create calendar views
- [ ] Create sync dashboard
- [ ] Update navigation

**Phase 10: Production (Day 16+)**
- [ ] Setup cron job
- [ ] Monitor sync logs
- [ ] Train staff
- [ ] Document any issues

---

## **🎯 SUCCESS CRITERIA**

Before marking this project complete, verify:

**Functional Requirements:**
- ✅ Sync runs every 10 minutes automatically
- ✅ Can match existing clients by email/phone
- ✅ Creates new clients when not found
- ✅ Assigns consultants to correct calendars
- ✅ Sends reminder SMS 24h before appointments
- ✅ Can backfill historical data
- ✅ Staff can view and manage synced appointments

**Technical Requirements:**
- ✅ No duplicate appointments created
- ✅ All data fields mapped correctly
- ✅ Proper error logging and handling
- ✅ API authentication working
- ✅ SMS integration working (Cellcast/Twilio)
- ✅ Email notifications working

**Performance Requirements:**
- ✅ Sync completes in < 30 seconds (for 10 minutes of data)
- ✅ No performance impact on existing system
- ✅ Database queries optimized

---

**🎉 Copy both documents to start implementation!**

**Main Plan:** `APPOINTMENT_SYNC_IMPLEMENTATION_PLAN_COMPLETE.md` (this file)  
**Missing Components:** `APPOINTMENT_SYNC_MISSING_COMPONENTS.md` ✨

