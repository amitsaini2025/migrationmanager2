# Dashboard & booking calendar (website bookings)

**Status:** implemented (local).  
**Scope:** CRM dashboard calendar widget, booking calendar (`calendar-v6`), Admin Console staff default calendar, shared appointment modal.

This document covers the calendar work done to align the dashboard widget with the full booking calendar UX, without merging the two into one page.

---

## 1. What exists

| Surface | Route / location | Role |
|---|---|---|
| Dashboard calendar | `/dashboard` (`x-dashboard.staff-calendar`) | Glance view: one home calendar + agenda list |
| Booking calendar | `/booking/calendar/{type}` (`calendar-v6`) | Operations: status, transfer, reschedule, cancel |
| Admin Console | Staff create / edit / view | Set per-staff default website calendar |

Both calendars read **website bookings** (`booking_appointments`) filtered by consultant `calendar_type`.

---

## 2. Calendar types

Shared keys (`StaffPersonalCalendarFeedService::CALENDAR_TYPES`):

| Key | Label |
|---|---|
| `paid` | Employer Sponsored |
| `jrp` | JRP |
| `education` | Education |
| `tourist` | Vijay |
| `adelaide` | Adelaide |
| `adelaide_education` | Adelaide Education |
| `ajay` | Ajay |
| `arun` | Arun |

---

## 3. Default calendar for a staff member

Resolved by `StaffPersonalCalendarFeedService::defaultTypeForStaff()` in this order:

1. **Admin Console** `staff.default_calendar_type` if set  
2. Else **name/email hint** (`STAFF_CALENDAR_HINTS`):  
   - Ajay → `ajay`  
   - Vijay → `tourist`  
   - Shubham / Yadwinder → `jrp`  
   - Arun → `paid` (Employer Sponsored, not Arun Calendar)  
3. Else **Employer Sponsored** (`paid`)

If Admin Console is left on **Automatic**, step 1 is skipped.

### Admin Console field

- Column: `staff.default_calendar_type` (nullable string, migration `2026_09_15_143341_add_default_calendar_type_to_staff_table`)
- UI: **Default website calendar** on staff create / edit / view  
- Partial: `resources/views/AdminConsole/staff/partials/default-calendar-type.blade.php`  
- Saved in `StaffController` store/update with validation against known calendar keys

---

## 4. Desktop switcher UX

Both dashboard and booking calendar show:

- **One primary calendar** (the selected / home type)
- **Other calendars** dropdown for the rest

Dashboard switches type in-page (refetch events + agenda).  
Booking calendar navigates to `/booking/calendar/{type}`.

---

## 5. Dashboard vs booking calendar (intentional differences)

| | Dashboard | Booking calendar |
|---|---|---|
| Feed | `/dashboard/calendar-events` via `StaffPersonalCalendarFeedService` | `/booking/api/appointments?format=calendar` |
| Cancelled / no-show | Hidden | Shown |
| Stats | Today / This week / Upcoming | Month, today, pending, paid, no-show, etc. |
| Agenda sidebar | Yes | No |
| Status colour legend | Removed | Kept |
| Click appointment | Shared modal | Full calendar modal (inline script on v6) |

Do **not** treat these differences as sync bugs. Unifying data feeds is optional future work.

---

## 6. Appointment popup (dashboard)

Dashboard grid and right-hand list open the same **Appointment Details** modal as the booking calendar (status, reschedule, consultant transfer, payment, cancel reason).

| Piece | Path |
|---|---|
| Modal markup | `resources/views/crm/booking/appointments/partials/event-modals.blade.php` |
| Modal actions (dashboard) | `public/js/booking-appointment-modal.js` |
| Contrast styles | `public/css/booking-appointment-modal.css` |
| Consultants for transfer | `DashboardController::bookingConsultantsForModal()` → `window.consultantsData` |

Event payload fields needed by the modal (status, consultant, payment, language, etc.) are included in `StaffPersonalCalendarFeedService::payloadFromBookingAppointment()`.

**Note:** Opening from the agenda list JSON-encodes dates as strings; duration calculation must parse with `new Date(...)` (not call `.getTime()` on the raw string).

---

## 7. Dashboard agenda list layout

Slim row layout:

```
[time]  Client name (Meeting type)     [Status badge]
        location
```

- Status badge sits on the **right** of the name row  
- Status colour **legend** under the calendar filters is removed (grid colours still apply)

Files: `public/js/dashboard-calendar.js`, `public/css/dashboard-calendar.css`, `resources/views/components/dashboard/staff-calendar.blade.php`

---

## 8. Modal contrast

Outline action buttons in the appointment modal use darker text on tinted backgrounds (scoped under `#eventModal`) so Confirmed / Cancelled / No Show remain readable on white. Shared CSS loads via the event-modals partial (dashboard and booking calendar).

---

## 9. Key files

| Area | Files |
|---|---|
| Feed / defaults | `app/Services/StaffPersonalCalendarFeedService.php` |
| Dashboard | `app/Http/Controllers/CRM/DashboardController.php`, `resources/views/crm/dashboard-optimized.blade.php`, `resources/views/components/dashboard/staff-calendar.blade.php`, `public/js/dashboard-calendar.js`, `public/css/dashboard-calendar.css` |
| Booking calendar | `app/Http/Controllers/CRM/BookingAppointmentsController.php`, `resources/views/crm/booking/appointments/calendar-v6.blade.php` |
| Shared modal | `partials/event-modals.blade.php`, `public/js/booking-appointment-modal.js`, `public/css/booking-appointment-modal.css` |
| Admin Console | `app/Http/Controllers/AdminConsole/StaffController.php`, staff create/edit/view + `partials/default-calendar-type.blade.php`, `app/Models/Staff.php` |
| Tests | `tests/Unit/Services/StaffPersonalCalendarFeedServiceTest.php` |

---

## 10. Deploy / verify checklist

1. Run migration for `default_calendar_type` on the target DB  
2. Hard-refresh `/dashboard` (Ctrl+F5) so calendar JS/CSS reload  
3. As Ajay (no Admin override): primary pill should be **Ajay**  
4. As a normal staff user with Automatic: primary should be **Employer Sponsored** unless a name/email hint matches  
5. Set a default in Admin Console → Staff → Edit → confirm dashboard opens that calendar  
6. Click a grid event and a list row → modal opens (not a new tab)  
7. Confirm status badge is on the right; no status legend under the filters  

---

## 11. Optional follow-ups (not done)

- Point both UIs at one shared appointments query (same date window + status rules)  
- Load shared modal JS on booking calendar-v6 instead of the large inline script  
- Persist last-selected calendar in session/localStorage after switching via “Other calendars”
