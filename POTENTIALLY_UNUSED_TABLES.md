# Potentially Unused/Obsolete Database Tables

This document lists tables in the `migration_manager_crm` database that may no longer be needed and could be candidates for deletion.

## Analysis Date
**Date:** October 22, 2025

## Categories of Potentially Unused Tables

### 1. BACKUP TABLES (High Priority for Deletion)
These appear to be backup tables created during development and should be removed:
- `theme_options_bkk_14aug2025` - Backup of theme_options table

### 2. TABLES WITHOUT CORRESPONDING MODELS
These tables exist in the database but don't have corresponding Eloquent models in `app/Models/`:

#### A. Website/Marketing Related (May be obsolete if not using public website)
- `banners` - No Banner model found
- `blogs` - No Blog model found
- `blog_categories` - No BlogCategory model found
- `cms_pages` - No CmsPage model found
- `faqs` - No Faq model found
- `free_downloads` - No FreeDownload model found
- `media_images` - No MediaImage model found
- `offers` - No Offer model found
- `packages` - No Package model found
- `popups` - No Popup model found
- `promotions` - No Promotion model found
- `reviews` - No Review model found
- `why_chooseuses` - No WhyChooseUs model found
- `wishlists` - No Wishlist model found

#### B. E-commerce/Service Booking Related
- `cashbacks` - No Cashback model found
- `coupons` - No Coupon model found
- `promo_codes` - No PromoCode model found  
- `promocode_uses` - No PromocodeUse model found
- `client_monthly_rewards` - No model found
- `client_ratings` - No ClientRating model found

#### C. Travel/Hotel Related (May be obsolete)
- `airports` - No Airport model found
- `cities` - No City model found
- `destinations` - No Destination model found
- `hotels` - No Hotel model found

#### D. Education/Mentoring Related
- `academic_requirements` - No model found
- `education` - No Education model found
- `mentorings` - No Mentoring model found
- `subjects` - No Subject model found
- `subject_areas` - No SubjectArea model found

#### E. Financial/Accounting Related
- `account_all_invoice_receipts` - No model found (Invoice model exists but not this)
- `account_client_receipts` - No model found
- `currencies` - No Currency model found
- `fee_types` - No FeeType model found
- `fee_options` - No FeeOption model found
- `fee_option_types` - No FeeOptionType model found (separate from ApplicationFeeOptionType)
- `income_sharings` - No IncomeSharingmodel found
- `markups` - No Markup model found
- `quotations` - No Quotation model found
- `quotation_infos` - No QuotationInfo model found
- `taxes` - No Tax model found
- `tax_rates` - No TaxRate model found

#### F. Product/Service Management
- `check_applications` - No model found
- `check_partners` - No model found
- `check_products` - No model found
- `products` - No Product model found
- `product_area_levels` - No ProductAreaLevel model found
- `product_types` - No ProductType model found
- `services` - No Service model found (OurService exists)
- `our_services` - Has OurService model (This one is USED)
- `service_accounts` - No ServiceAccount model found
- `interested_services` - No InterestedService model found

#### G. Navigation/Menu Related
- `navmenus` - No NavMenu model found

#### H. Partner/Agent Management
- `partners` - No Partner model found (Agent exists)
- `partner_branches` - No PartnerBranch model found
- `partner_types` - No PartnerType model found
- `representing_partners` - No RepresentingPartner model found

#### I. Appointment/Booking Systems
- `book_services` - Has BookService model (USED)
- `book_service_disable_slots` - Has BookServiceDisableSlot model (USED)
- `book_service_slot_per_persons` - Has BookServiceSlotPerPerson model (USED)
- `tbl_paid_appointment_payment` - No model found (old naming convention)

#### J. Template/Design Related
- `templates` - No Template model found
- `template_infos` - No TemplateInfo model found
- `theme_options` - No ThemeOption model found

#### K. Location/Office Related
- `branches` - Has Branch model (USED)
- `our_offices` - No OurOffice model found

#### L. Other Misc Tables
- `api_tokens` - No ApiToken model found (personal_access_tokens exists and is used)
- `attach_files` - No AttachFile model found (attachments exists)
- `checklists` - No Checklist model found (DocumentChecklist exists)
- `client_married_details` - No ClientMarriedDetail model found
- `client_occupation_lists` - No ClientOccupationList model found
- `client_service_takens` - Has clientServiceTaken model (USED - note inconsistent naming)
- `contacts` - No Contact model found
- `device_tokens` - Has DeviceToken model (USED)
- `download_schedule_dates` - No model found
- `email_uploads` - No EmailUpload model found
- `enquiries` - No Enquiry model found (Lead exists)
- `enquiry_sources` - No EnquirySource model found
- `followups` - No Followup model found (LeadFollowup, InvoiceFollowup exist)
- `followup_types` - No FollowupType model found
- `groups` - Has Group model (USED)
- `home_contents` - Has HomeContent model (USED)
- `message_recipients` - No MessageRecipient model found
- `nature_of_enquiry` - Has NatureOfEnquiry model (USED)
- `omrs` - No Omr model found
- `online_forms` - Has OnlineForm model (USED)
- `profiles` - No Profile model found
- `refresh_tokens` - Has RefreshToken model (USED)
- `schedule_items` - No ScheduleItem model found
- `sources` - No Source model found
- `to_do_groups` - No ToDoGroup model found
- `upload_checklists` - Has UploadChecklist model (USED)
- `user_logs` - Has UserLog model (USED)
- `user_roles` - Has UserRole model (USED)
- `user_types` - Has UserType model (USED)
- `verified_numbers` - No VerifiedNumber model found
- `verify_users` - Has VerifyUser model (USED)
- `visa_types` - No VisaType model found
- `wallets` - No Wallet model found
- `website_settings` - Has WebsiteSetting model (USED)
- `workflows` - Has Workflow model (USED)
- `workflow_stages` - Has WorkflowStage model (USED)

### 3. OAUTH TABLES (Keep if using Laravel Passport)
- `oauth_access_tokens`
- `oauth_auth_codes`
- `oauth_clients`
- `oauth_personal_access_clients`
- `oauth_refresh_tokens`

### 4. SYSTEM TABLES (DO NOT DELETE)
- `cache`
- `cache_locks`
- `failed_jobs`
- `jobs`
- `migrations`
- `password_resets`
- `password_reset_links`
- `personal_access_tokens`
- `sessions`

---

## HIGH PRIORITY DELETION CANDIDATES

Based on the analysis, here are the tables most likely safe to delete:

### Tier 1: Almost Certainly Unused (No Model, Likely Obsolete Features)
1. `theme_options_bkk_14aug2025` - Obvious backup table
2. `airports` - No model, travel feature likely unused
3. `hotels` - No model, travel feature likely unused
4. `destinations` - No model, travel feature likely unused
5. `cities` - No model (unless used for address management)
6. `cashbacks` - No model, e-commerce feature
7. `wishlists` - No model, e-commerce feature
8. `client_ratings` - No model, review system
9. `client_monthly_rewards` - No model, rewards system
10. `mentorings` - No model, education feature
11. `academic_requirements` - No model, education feature
12. `education` - No model (unless storing client education)
13. `subjects` - No model
14. `subject_areas` - No model
15. `promocode_uses` - No model (separate from promo_codes)
16. `tbl_paid_appointment_payment` - Old naming convention, likely replaced

### Tier 2: Likely Unused (Website/Marketing Features)
1. `banners` - Marketing feature
2. `popups` - Marketing feature
3. `offers` - Marketing feature
4. `promotions` - Marketing feature
5. `free_downloads` - Marketing feature
6. `media_images` - Media library
7. `why_chooseuses` - Static content
8. `navmenus` - Navigation management (may be hardcoded)

### Tier 3: Verify Before Deletion (Business Logic)
1. `check_applications` - Unknown purpose
2. `check_partners` - Unknown purpose
3. `check_products` - Unknown purpose
4. `products` - May be replaced by services
5. `product_area_levels` - Related to products
6. `product_types` - Related to products
7. `partners` - May be replaced by agents
8. `partner_branches` - Related to partners
9. `partner_types` - Related to partners
10. `representing_partners` - Related to partners
11. `services` - vs our_services (may be duplicate)
12. `service_accounts` - Unknown purpose
13. `interested_services` - Lead services tracking?
14. `enquiries` - vs leads (may be duplicate/old)
15. `enquiry_sources` - Related to enquiries
16. `contacts` - vs clients (may be duplicate)
17. `followups` - vs lead_followups/invoice_followups
18. `followup_types` - Related to followups

### Tier 4: Financial Tables (Verify with Accounting)
1. `quotations` - Quote system
2. `quotation_infos` - Related to quotations
3. `markups` - Pricing management
4. `currencies` - Multi-currency support
5. `taxes` - Tax system
6. `tax_rates` - Tax rates
7. `fee_types` - vs application_fee_option_types
8. `fee_options` - vs application_fee_options/service_fee_options
9. `fee_option_types` - Duplicate system?
10. `income_sharings` - Revenue sharing
11. `account_all_invoice_receipts` - Accounting
12. `account_client_receipts` - Accounting

### Tier 5: CMS/Template Tables (Verify if CMS is Used)
1. `blogs` - Blog system
2. `blog_categories` - Blog categories
3. `cms_pages` - CMS pages
4. `faqs` - FAQ system
5. `reviews` - Review system
6. `testimonials` - Has Testimonial model (USED)
7. `templates` - Template system
8. `template_infos` - Template metadata

---

## RECOMMENDATION

**Before deleting any tables:**
1. ✅ Backup the entire database
2. ✅ Check if any raw SQL queries reference these tables
3. ✅ Verify with business stakeholders about features
4. ✅ Check if any external systems use these tables
5. ✅ Start with Tier 1 tables (most obvious candidates)
6. ✅ Monitor application logs after each batch deletion

**Estimated space savings:** Depends on data volume, but removing 50-80 unused tables could significantly reduce database complexity and improve maintainability.

---

## Total Tables Analysis
- **Total Tables:** 205
- **Tables with Models:** ~80
- **Tables without Models:** ~125
- **Backup Tables:** 1
- **System Tables:** 10
- **Potential Deletion Candidates:** 80-100 tables

