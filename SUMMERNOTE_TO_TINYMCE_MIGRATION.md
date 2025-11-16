# Summernote to TinyMCE Migration List

## Overview
This document lists all files where Summernote is used and needs to be replaced with TinyMCE.

## JavaScript Files

### 1. `public/js/scripts.js`
- **Lines 363-391**: Main Summernote initialization for `.summernote` and `.summernote-simple`
- **Action**: Replace with TinyMCE initialization

### 2. `public/js/crm/clients/detail-main.js`
- **Line 3285-3287**: Reading from `#note_description` Summernote editor
- **Line 3353-3357**: Checking and setting `#assignnote` Summernote editor
- **Line 7739-7745**: Checking and reading `#assignnote` Summernote editor
- **Line 8953, 8957, 8995, 8999**: Reset and clear Summernote editors
- **Line 9333-9335, 9408-9410**: Setting content in Summernote editors
- **Line 9750-9754, 9846-9850**: Email modal Summernote operations
- **Action**: Replace all Summernote API calls with TinyMCE equivalents

### 3. `public/js/custom-form-validation.js`
- **Line 2512-2514**: Reset Summernote editor
- **Action**: Replace with TinyMCE reset

## Blade Template Files (Layouts)

### 4. `resources/views/layouts/crm_client_detail.blade.php`
- **Line 17**: CSS link `<link rel="stylesheet" href="{{asset('css/summernote-bs4.css')}}">`
- **Line 1509**: JS script `<script src="{{asset('js/summernote-bs4.js')}}"></script>`
- **Action**: Remove Summernote includes, add TinyMCE if not present

### 5. `resources/views/layouts/crm_client_detail_dashboard.blade.php`
- **Line 16**: CSS link for Summernote
- **Line 525**: JS script for Summernote
- **Action**: Remove Summernote includes, add TinyMCE if not present

### 6. `resources/views/layouts/crm_client_detail_appointment.blade.php`
- **Line 16**: CSS link for Summernote
- **Line 260**: JS script for Summernote
- **Action**: Remove Summernote includes, add TinyMCE if not present

## Blade Template Files (Views)

### 7. `resources/views/crm/clients/index.blade.php`
- **Line 703**: Textarea with class `summernote-simple`
- **Line 971-973**: JavaScript setting Summernote content
- **Action**: Replace textarea class and JavaScript calls

### 8. `resources/views/crm/clients/detail.blade.php`
- **Note**: Already uses TinyMCE for email modals (lines 668, 735, 1308-1447)
- **Action**: Already migrated

### 9. `resources/views/crm/clients/modals/notes.blade.php`
- **Line 46, 180, 270**: Textareas with class `summernote-simple`
- **Line 500, 752-789**: Summernote-specific styling and initialization code
- **Action**: Replace all Summernote references

### 10. `resources/views/crm/clients/modals/activities.blade.php`
- **Line 248**: Textarea with class `summernote-simple` (readonly)
- **Action**: Replace with TinyMCE readonly mode

### 11. `resources/views/crm/clients/editclientmodal.blade.php`
- **Line 56**: Textarea with class `summernote-simple`
- **Action**: Replace textarea class

### 12. `resources/views/crm/clients/addclientmodal.blade.php`
- **Line 271-272**: Setting content in Summernote editor
- **Action**: Replace with TinyMCE setContent

### 13. `resources/views/crm/clients/clientsmatterslist.blade.php`
- **Line 718-720**: JavaScript setting Summernote content
- **Action**: Replace with TinyMCE setContent

### 14. `resources/views/crm/assignee/index.blade.php`
- **Line 186**: Textarea with `summernote-simple` class and id `assignnote`
- **Action**: Replace textarea class

### 15. `resources/views/crm/assignee/assign_by_me.blade.php`
- **Line 207**: Textarea with `summernote-simple` class and id `assignnote`
- **Action**: Replace textarea class

### 16. `resources/views/crm/assignee/assign_to_me.blade.php`
- **Line 187, 363**: Textareas with `summernote-simple` class and id `assignnote`
- **Action**: Replace textarea classes

### 17. `resources/views/crm/assignee/action.blade.php`
- **Line 1428**: Commented Summernote initialization
- **Action**: Remove or replace with TinyMCE

### 18. `resources/views/crm/assignee/action_completed.blade.php`
- **Line 330**: Textarea with `summernote-simple` class and id `assignnote`
- **Action**: Replace textarea class

### 19. `resources/views/crm/assignee/completed.blade.php`
- **Line 170**: Textarea with `summernote-simple` class and id `assignnote`
- **Action**: Replace textarea class

### 20. `resources/views/crm/leads/index.blade.php`
- **Line 764-766**: JavaScript setting Summernote content
- **Action**: Replace with TinyMCE setContent

### 21. `resources/views/crm/leads/detail.blade.php`
- **Line 378**: Textarea with `summernote-simple` class
- **Line 459-461**: JavaScript setting Summernote content
- **Action**: Replace all Summernote references

### 22. `resources/views/crm/leads/history.blade.php`
- **Line 172**: Textarea with `summernote-simple` class and id `remindernote`
- **Line 290**: Textarea with `summernote-simple` class and id `description`
- **Line 371**: Textarea with `summernote-simple` class
- **Line 549-551**: JavaScript setting Summernote content
- **Line 566-574**: Summernote initialization with custom config
- **Action**: Replace all Summernote references

### 23. `resources/views/crm/email_template/create.blade.php`
- **Note**: Already uses TinyMCE (lines 63, 89-143)
- **Line 105**: Comment about Summernote colors (can be removed)
- **Action**: Remove comment, already migrated

### 24. `resources/views/crm/email_template/edit.blade.php`
- **Line 64**: Textarea with `summernote-simple` class
- **Action**: Replace with TinyMCE

### 25. `resources/views/crm/officevisits/index.blade.php`
- **Line 407**: Textarea with `summernote-simple` class
- **Action**: Replace textarea class

### 26. `resources/views/crm/officevisits/waiting.blade.php`
- **Line 401**: Textarea with `summernote-simple` class
- **Action**: Replace textarea class

### 27. `resources/views/crm/officevisits/completed.blade.php`
- **Line 392**: Textarea with `summernote-simple` class
- **Action**: Replace textarea class

### 28. `resources/views/crm/officevisits/attending.blade.php`
- **Line 392**: Textarea with `summernote-simple` class
- **Action**: Replace textarea class

### 29. `resources/views/crm/officevisits/archived.blade.php`
- **Line 406**: Textarea with `summernote-simple` class
- **Action**: Replace textarea class

### 30. `resources/views/crm/documents/index.blade.php`
- **Line 256**: Textarea with `summernote-simple` class and id `compose_email_message`
- **Line 307, 311**: CSS and JS includes for Summernote
- **Line 400, 415-416**: JavaScript setting Summernote content
- **Action**: Replace all Summernote references

### 31. `resources/views/documents/index.blade.php`
- **Line 256**: Textarea with `summernote-simple` class and id `compose_email_message`
- **Line 307, 311**: CSS and JS includes for Summernote
- **Line 400, 415-416**: JavaScript setting Summernote content
- **Action**: Replace all Summernote references

### 32. `resources/views/AdminConsole/features/crmemailtemplate/create.blade.php`
- **Action**: Check if uses Summernote (file not fully scanned)

### 33. `resources/views/AdminConsole/features/crmemailtemplate/edit.blade.php`
- **Line 62**: Textarea with `summernote-simple` class
- **Action**: Replace with TinyMCE

## CSS Files

### 34. `public/css/summernote-bs4.css`
- **Action**: Can be removed after migration (verify no custom styles)

### 35. `public/css/summernote-bs4_1.css`
- **Action**: Can be removed after migration (verify no custom styles)

## JavaScript Files (Library)

### 36. `public/js/summernote-bs4.js`
- **Action**: Can be removed after migration

### 37. `public/js/summernote-bs4.min.js`
- **Action**: Can be removed after migration

## Font Files (Summernote-specific)

### 38. `public/fonts/summernote*.{eot,woff,ttf}`
- **Files**: 
  - `summernote.eot`, `summernote.woff`, `summernote.ttf`
  - `summernoted41d.eot`
  - `summernote4c4d.woff`, `summernote4c4d.ttf`, `summernote4c4d.eot`
- **Action**: Can be removed after migration

## Migration Strategy

1. Replace all `.summernote-simple` class with `.tinymce-editor` or appropriate TinyMCE class
2. Replace all `summernote('code')` calls with `tinymce.get('editorId').getContent()`
3. Replace all `summernote('code', content)` calls with `tinymce.get('editorId').setContent(content)`
4. Replace all `summernote('reset')` calls with `tinymce.get('editorId').setContent('')`
5. Replace Summernote initialization with TinyMCE initialization
6. Remove Summernote CSS/JS includes and add TinyMCE includes where needed
7. Create a shared TinyMCE configuration similar to the one in `crm/clients/detail.blade.php`

## TinyMCE Configuration Reference

Based on `resources/views/crm/clients/detail.blade.php`:
- Height: 300px for email modals, 150-200px for simple editors
- Plugins: 'lists', 'link', 'autolink' (minimal), can add more as needed
- Toolbar: 'bold italic underline strikethrough | forecolor | bullist numlist | link'
- Color map: Match Summernote color palette
- Setup: Auto-save on change

