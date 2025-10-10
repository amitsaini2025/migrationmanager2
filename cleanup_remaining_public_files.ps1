# Cleanup Remaining Public Folder Files
# This script removes the remaining unused files identified in the analysis
# Run this script from the project root directory

Write-Host "==================================" -ForegroundColor Cyan
Write-Host "Public Folder Cleanup - Phase 2" -ForegroundColor Cyan
Write-Host "==================================" -ForegroundColor Cyan
Write-Host ""

$filesDeleted = 0
$directoriesDeleted = 0
$spaceFreed = 0

# Function to safely remove file
function Remove-SafelyFile {
    param([string]$Path, [string]$Description)
    
    if (Test-Path $Path) {
        try {
            $size = (Get-Item $Path).Length
            Remove-Item $Path -Force -ErrorAction Stop
            $script:filesDeleted++
            $script:spaceFreed += $size
            Write-Host "[✓] Deleted: $Description" -ForegroundColor Green
            return $true
        }
        catch {
            Write-Host "[✗] Failed to delete: $Description - $_" -ForegroundColor Red
            return $false
        }
    }
    else {
        Write-Host "[i] Already deleted: $Description" -ForegroundColor Yellow
        return $false
    }
}

# Function to safely remove directory
function Remove-SafelyDirectory {
    param([string]$Path, [string]$Description)
    
    if (Test-Path $Path) {
        try {
            Remove-Item $Path -Force -Recurse -ErrorAction Stop
            $script:directoriesDeleted++
            Write-Host "[✓] Deleted directory: $Description" -ForegroundColor Green
            return $true
        }
        catch {
            Write-Host "[✗] Failed to delete directory: $Description - $_" -ForegroundColor Red
            return $false
        }
    }
    else {
        Write-Host "[i] Already deleted: $Description" -ForegroundColor Yellow
        return $false
    }
}

Write-Host "Phase 1: Empty Directories" -ForegroundColor Cyan
Write-Host "-------------------------" -ForegroundColor Cyan
Remove-SafelyDirectory "public\assets" "public\assets (empty directory)"
Remove-SafelyDirectory "public\captcha" "public\captcha (empty directory)"
Write-Host ""

Write-Host "Phase 2: Test/Development Files" -ForegroundColor Cyan
Write-Host "-------------------------------" -ForegroundColor Cyan
Remove-SafelyFile "public\production-websocket-test.html" "WebSocket test file (production)"
Remove-SafelyFile "public\simple-websocket-test.html" "WebSocket test file (simple)"
Remove-SafelyFile "public\hot" "Vite hot reload file"
Write-Host ""

Write-Host "Phase 3: Unused JavaScript Files" -ForegroundColor Cyan
Write-Host "--------------------------------" -ForegroundColor Cyan
Remove-SafelyFile "public\js\pace.js" "pace.js (not referenced)"
Remove-SafelyFile "public\js\demo.js" "demo.js (not referenced)"
Remove-SafelyFile "public\js\dashboard2.js" "dashboard2.js (not referenced)"
Remove-SafelyFile "public\js\sessionpopup.js" "sessionpopup.js (not referenced)"
Write-Host ""

Write-Host "Phase 4: Unused CSS Files" -ForegroundColor Cyan
Write-Host "------------------------" -ForegroundColor Cyan
Remove-SafelyFile "public\css\pace.css" "pace.css (companion to pace.js)"
Write-Host ""

Write-Host "Phase 5: Unused Images (Optional)" -ForegroundColor Cyan
Write-Host "---------------------------------" -ForegroundColor Cyan
Write-Host "[?] The following files might be unused. Review before uncommenting:" -ForegroundColor Yellow
Write-Host "    - public\images\contact-support.jpg" -ForegroundColor Gray
Write-Host "    - public\images\ajax-loader.gif (was used by CKFinder only)" -ForegroundColor Gray
Write-Host ""
Write-Host "To delete these, uncomment the lines in this script." -ForegroundColor Yellow
# Uncomment the lines below if you want to delete these images:
# Remove-SafelyFile "public\images\contact-support.jpg" "contact-support.jpg (not referenced)"
# Remove-SafelyFile "public\images\ajax-loader.gif" "ajax-loader.gif (was for CKFinder)"
Write-Host ""

# Summary
Write-Host "==================================" -ForegroundColor Cyan
Write-Host "Cleanup Summary" -ForegroundColor Cyan
Write-Host "==================================" -ForegroundColor Cyan
Write-Host "Files deleted: $filesDeleted" -ForegroundColor Green
Write-Host "Directories deleted: $directoriesDeleted" -ForegroundColor Green
Write-Host "Approximate space freed: $([math]::Round($spaceFreed / 1KB, 2)) KB" -ForegroundColor Green
Write-Host ""
Write-Host "Next Steps:" -ForegroundColor Yellow
Write-Host "1. Test your application thoroughly" -ForegroundColor White
Write-Host "2. Check for any broken links or missing resources" -ForegroundColor White
Write-Host "3. Clear browser cache and test again" -ForegroundColor White
Write-Host "4. If everything works, consider deleting the optional images" -ForegroundColor White
Write-Host ""
Write-Host "Done!" -ForegroundColor Green

