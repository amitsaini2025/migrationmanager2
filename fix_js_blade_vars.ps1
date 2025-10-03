# Script to replace all Blade template variables in detail-main.js

$filePath = "public/js/admin/clients/detail-main.js"
$content = Get-Content $filePath -Raw -Encoding UTF8

# Replace URL patterns
$content = $content -replace "'\{\{route\('admin\.clients\.filter\.emails'\)\}\}'", "window.ClientDetailConfig.urls.filterEmails"
$content = $content -replace "'{{ route\('admin\.clients\.filter\.emails'\) }}'", "window.ClientDetailConfig.urls.filterEmails"
$content = $content -replace "'\{\{route\('admin\.clients\.filter\.sentmails'\)\}\}'", "window.ClientDetailConfig.urls.filterSentMails"
$content = $content -replace "'{{ route\('admin\.clients\.filter\.sentmails'\) }}'", "window.ClientDetailConfig.urls.filterSentMails"
$content = $content -replace "'\{\{route\('admin\.clients\.saveRating'\)\}\}'", "window.ClientDetailConfig.urls.saveRating"
$content = $content -replace "'{{ route\('admin\.clients\.saveRating'\) }}'", "window.ClientDetailConfig.urls.saveRating"
$content = $content -replace "'\{\{route\(['""]check\.star\.client['""]\)\}\}'", "window.ClientDetailConfig.urls.checkStarClient"
$content = $content -replace "'{{ route\(['""]check\.star\.client['""]\) }}'", "window.ClientDetailConfig.urls.checkStarClient"
$content = $content -replace "'\{\{route\(['""]clients\.generateagreement['""]\)\}\}'", "window.ClientDetailConfig.urls.generateAgreement"
$content = $content -replace "'{{ route\(['""]clients\.generateagreement['""]\) }}'", "window.ClientDetailConfig.urls.generateAgreement"
$content = $content -replace "'\{\{route\(['""]clients\.uploadAgreement['""],[^)]+\)\}\}'", "window.ClientDetailConfig.urls.uploadAgreement"
$content = $content -replace "'{{ route\(['""]clients\.uploadAgreement['""],[^)]+\) }}'", "window.ClientDetailConfig.urls.uploadAgreement"

# Replace URL::to patterns
$content = $content -replace "'\{\{URL::to\('/admin/clients/getInfoByReceiptId'\)\}\}'", "window.ClientDetailConfig.urls.getInfoByReceiptId"
$content = $content -replace "'\{\{URL::to\('/admin/not-picked-call'\)\}\}'", "window.ClientDetailConfig.urls.notPickedCall"
$content = $content -replace "'\{\{URL::to\('/getdatetimebackend'\)\}\}'", "window.ClientDetailConfig.urls.getDateTimeBackend"
$content = $content -replace "'\{\{URL::to\('/getdisableddatetime'\)\}\}'", "window.ClientDetailConfig.urls.getDisabledDateTime"
$content = $content -replace "'\{\{URL::to\('/admin/promo-code/checkpromocode'\)\}\}'", "window.ClientDetailConfig.urls.checkPromoCode"
$content = $content -replace "'\{\{URL::to\('/admin/clients/getMigrationAgentDetail'\)\}\}'", "window.ClientDetailConfig.urls.getMigrationAgentDetail"
$content = $content -replace "'\{\{URL::to\('/admin/clients/check-cost-assignment'\)\}\}'", "window.ClientDetailConfig.urls.checkCostAssignment"
$content = $content -replace "'\{\{URL::to\('/admin/clients/getVisaAggreementMigrationAgentDetail'\)\}\}'", "window.ClientDetailConfig.urls.getVisaAgreementAgent"
$content = $content -replace "'\{\{URL::to\('/admin/clients/getCostAssignmentMigrationAgentDetail'\)\}\}'", "window.ClientDetailConfig.urls.getCostAssignmentAgent"
$content = $content -replace "'\{\{URL::to\('/admin/clients/getCostAssignmentMigrationAgentDetailLead'\)\}\}'", "window.ClientDetailConfig.urls.getCostAssignmentAgentLead"
$content = $content -replace "'\{\{URL::to\('/admin/clients/fetchClientContactNo'\)\}\}'", "window.ClientDetailConfig.urls.fetchClientContactNo"
$content = $content -replace "'\{\{URL::to\('/admin/deletecostagreement'\)\}\}'", "window.ClientDetailConfig.urls.deleteCostagreement"
$content = $content -replace "'\{\{URL::to\('/admin/pinnote'\)\}\}'", "window.ClientDetailConfig.urls.pinNote"
$content = $content -replace "'\{\{URL::to\('/admin/pinactivitylog'\)\}\}'", "window.ClientDetailConfig.urls.pinActivityLog"
$content = $content -replace "'\{\{URL::to\('/admin/clients/get-recipients'\)\}\}'", "window.ClientDetailConfig.urls.getRecipients"
$content = $content -replace "'\{\{URL::to\('/admin/viewnotedetail'\)\}\}'", "window.ClientDetailConfig.urls.viewNoteDetail"
$content = $content -replace "'\{\{URL::to\('/admin/viewapplicationnote'\)\}\}'", "window.ClientDetailConfig.urls.viewApplicationNote"
$content = $content -replace "'\{\{URL::to\('/admin/getpartnerbranch'\)\}\}'", "window.ClientDetailConfig.urls.getPartnerBranch"
$content = $content -replace "'\{\{URL::to\('/admin/change-client-status'\)\}\}'", "window.ClientDetailConfig.urls.changeClientStatus"
$content = $content -replace "'\{\{URL::to\('/admin/get-templates'\)\}\}'", "window.ClientDetailConfig.urls.getTemplates"
$content = $content -replace "'\{\{URL::to\('/admin/getpartner'\)\}\}'", "window.ClientDetailConfig.urls.getPartner"
$content = $content -replace "'\{\{URL::to\('/admin/getproduct'\)\}\}'", "window.ClientDetailConfig.urls.getProduct"
$content = $content -replace "'\{\{URL::to\('/admin/getbranch'\)\}\}'", "window.ClientDetailConfig.urls.getBranch"
$content = $content -replace "'\{\{URL::to\('/admin/convertapplication'\)\}\}'", "window.ClientDetailConfig.urls.convertApplication"
$content = $content -replace "'\{\{URL::to\('/admin/renamedoc'\)\}\}'", "window.ClientDetailConfig.urls.renameDoc"
$content = $content -replace "'\{\{URL::to\('/admin/renamechecklistdoc'\)\}\}'", "window.ClientDetailConfig.urls.renameChecklistDoc"
$content = $content -replace "'\{\{URL::to\('/admin/delete-education'\)\}\}'", "window.ClientDetailConfig.urls.deleteEducation"
$content = $content -replace "'\{\{URL::to\('/admin/getsubjects'\)\}\}'", "window.ClientDetailConfig.urls.getSubjects"
$content = $content -replace "'\{\{URL::to\('/admin/getAppointmentdetail'\)\}\}'", "window.ClientDetailConfig.urls.getAppointmentDetail"
$content = $content -replace "'\{\{URL::to\('/admin/getEducationdetail'\)\}\}'", "window.ClientDetailConfig.urls.getEducationDetail"
$content = $content -replace "'\{\{URL::to\('/admin/getintrestedservice'\)\}\}'", "window.ClientDetailConfig.urls.getInterestedService"
$content = $content -replace "'\{\{URL::to\('/admin/getintrestedserviceedit'\)\}\}'", "window.ClientDetailConfig.urls.getInterestedServiceEdit"
$content = $content -replace "'\{\{URL::to\('/admin/clients/fetchClientMatterAssignee'\)\}\}'", "window.ClientDetailConfig.urls.fetchClientMatterAssignee"
$content = $content -replace "'\{\{URL::to\('/admin/addscheduleinvoicedetail'\)\}\}'", "window.ClientDetailConfig.urls.addScheduleInvoiceDetail"
$content = $content -replace "'\{\{URL::to\('/admin/updatestage'\)\}\}'", "window.ClientDetailConfig.urls.updateStage"
$content = $content -replace "'\{\{URL::to\('/admin/completestage'\)\}\}'", "window.ClientDetailConfig.urls.completeStage"
$content = $content -replace "'\{\{URL::to\('/admin/updatebackstage'\)\}\}'", "window.ClientDetailConfig.urls.updateBackStage"
$content = $content -replace "'\{\{URL::to\('/admin/getapplicationnotes'\)\}\}'", "window.ClientDetailConfig.urls.getApplicationNotes"
$content = $content -replace "'\{\{URL::to\('/admin/scheduleinvoicedetail'\)\}\}'", "window.ClientDetailConfig.urls.scheduleInvoiceDetail"
$content = $content -replace "'\{\{URL::to\('/admin/application/checklistupload'\)\}\}'", "window.ClientDetailConfig.urls.checklistUpload"
$content = $content -replace "'\{\{URL::to\('/admin/delete_action'\)\}\}'", "window.ClientDetailConfig.urls.deleteAction"
$content = $content -replace "'\{\{URL::to\('/admin/application/publishdoc'\)\}\}'", "window.ClientDetailConfig.urls.publishDoc"
$content = $content -replace "'\{\{URL::to\('/admin/clients/update-session-completed'\)\}\}'", "window.ClientDetailConfig.urls.updateSessionCompleted"

# Replace URL patterns with concatenation
$content = $content -replace "'\{\{URL::to\('/'\)\}\}'/\+'admin/clients/followup/store'", "window.ClientDetailConfig.urls.followupStore"
$content = $content -replace "'\{\{URL::to\('/admin/'\)\}\}'/\+", "window.ClientDetailConfig.urls.admin+'/"

# Replace {{ url() }} patterns  
$content = $content -replace "'\{\{ url\(['""]\/admin\/download-document['""]\) \}\}'", "window.ClientDetailConfig.urls.downloadDocument"
$content = $content -replace "'\{\{ url\(['""]\/admin\/clients\/sendToHubdoc['""]\) \}\}'/\+", "window.ClientDetailConfig.urls.sendToHubdoc+'/"
$content = $content -replace "'\{\{ url\(['""]\/admin\/clients\/checkHubdocStatus['""]\) \}\}'/\+", "window.ClientDetailConfig.urls.checkHubdocStatus+'/"

# Replace client ID references
$content = $content -replace "'{{ \`$fetchedData->id }}'", "window.ClientDetailConfig.clientId"
$content = $content -replace "'{{ \`$encodeId }}'", "window.ClientDetailConfig.encodeId"
$content = $content -replace "'\{\{\`$fetchedData->id\}\}'", "window.ClientDetailConfig.clientId"
$content = $content -replace "'\{\{\`$encodeId\}\}'", "window.ClientDetailConfig.encodeId"
$content = $content -replace "'\{\{\@\`$fetchedData->id\}\}'", "window.ClientDetailConfig.clientId"
$content = $content -replace "'{{ \`$fetchedData->first_name \?\? 'user' }}'", "(window.ClientDetailConfig.clientFirstName || 'user')"

# Replace CSRF token
$content = $content -replace "'{{ csrf_token\(\) }}'", "window.ClientDetailConfig.csrfToken"
$content = $content -replace "'\{\{ csrf_token\(\) \}\}'", "window.ClientDetailConfig.csrfToken"

# Replace date patterns
$content = $content -replace "'{{ date\(['""]Y-m-d['""]\) }}'", "(new Date().toISOString().split('T')[0])"

# Remove PHP blocks (lines 6451-6474 and similar) - mark them for manual review
$content = $content -replace "<\?php[\s\S]*?\?>", "/* PHP BLOCK REMOVED - NEEDS CONVERSION TO AJAX */"

# Save the file
$content | Set-Content $filePath -Encoding UTF8 -NoNewline

Write-Host "Replacement complete! File updated: $filePath"

