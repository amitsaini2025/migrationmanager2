# Quick database backup - Direct pg_dump command
# Usage: .\backup_db.ps1 -DatabaseName "your_database_name"

param(
    [Parameter(Mandatory=$true)]
    [string]$DatabaseName,
    
    [string]$DbHost = "127.0.0.1",
    [string]$DbPort = "5432",
    [string]$Username = "postgres",
    [string]$Password = ""
)

# Prompt for password if not provided
if ([string]::IsNullOrEmpty($Password)) {
    $SecurePassword = Read-Host "Enter database password" -AsSecureString
    $BSTR = [System.Runtime.InteropServices.Marshal]::SecureStringToBSTR($SecurePassword)
    $Password = [System.Runtime.InteropServices.Marshal]::PtrToStringAuto($BSTR)
}

# Create backups directory
if (-not (Test-Path "backups")) {
    New-Item -ItemType Directory -Path "backups" | Out-Null
}

# Generate backup filename with timestamp
$Timestamp = Get-Date -Format "yyyyMMdd_HHmmss"
$BackupFile = "backups\${DatabaseName}_backup_${Timestamp}.sql"

Write-Host "Backing up database: $DatabaseName" -ForegroundColor Cyan
Write-Host "Output file: $BackupFile" -ForegroundColor Gray
Write-Host ""

# Set password environment variable
$env:PGPASSWORD = $Password

# Run pg_dump directly
pg_dump -h $DbHost -p $DbPort -U $Username -d $DatabaseName -F p -f $BackupFile --verbose --no-owner --no-privileges

# Check result
if ($LASTEXITCODE -eq 0) {
    $FileSize = (Get-Item $BackupFile).Length / 1MB
    Write-Host ""
    Write-Host "✓ Backup completed successfully!" -ForegroundColor Green
    Write-Host "  File: $BackupFile" -ForegroundColor Green
    Write-Host "  Size: $([math]::Round($FileSize, 2)) MB" -ForegroundColor Green
} else {
    Write-Host ""
    Write-Host "✗ Backup failed with exit code: $LASTEXITCODE" -ForegroundColor Red
}

# Clear password
Remove-Item Env:\PGPASSWORD -ErrorAction SilentlyContinue

