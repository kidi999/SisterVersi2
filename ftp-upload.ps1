# SISTER FTP Upload Script
$ErrorActionPreference = "Continue"

$ftpServer = "103.241.192.78"
$ftpUsername = "unic"
$ftpPassword = "qwert12345"
$remotePath = "/home/unic/sister"
$localPath = "C:\xampp\htdocs\sister"

Write-Host ""
Write-Host "SISTER - Uploading to Production Server" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Get files to upload (exclude specific patterns)
Write-Host "[1/3] Collecting files..." -ForegroundColor Yellow

$filesToUpload = Get-ChildItem -Path $localPath -Recurse -File | Where-Object {
    $_.FullName -notlike "*.git*" -and
    $_.FullName -notlike "*node_modules*" -and
    $_.FullName -notlike "*vendor*" -and
    $_.FullName -notlike "*\.env" -and
    $_.FullName -notlike "*.log" -and
    $_.FullName -notlike "*.bat" -and
    $_.FullName -notlike "*.ps1" -and
    $_.FullName -notlike "*.md" -and
    $_.FullName -notlike "*.zip" -and
    $_.FullName -notlike "*storage\logs*" -and
    $_.FullName -notlike "*storage\framework\cache*" -and
    $_.FullName -notlike "*storage\framework\sessions*" -and
    $_.FullName -notlike "*storage\framework\views*"
}

$totalFiles = $filesToUpload.Count
Write-Host "  Found: $totalFiles files" -ForegroundColor Green
Write-Host ""

# Upload files
Write-Host "[2/3] Uploading to ftp://$ftpServer$remotePath ..." -ForegroundColor Yellow
Write-Host ""

$uploadedCount = 0
$failedCount = 0

foreach ($file in $filesToUpload) {
    $relativePath = $file.FullName.Substring($localPath.Length).Replace("\", "/")
    $remoteFilePath = "$remotePath$relativePath"
    
    try {
        $ftpRequest = [System.Net.FtpWebRequest]::Create("ftp://$ftpServer$remoteFilePath")
        $ftpRequest.Credentials = New-Object System.Net.NetworkCredential($ftpUsername, $ftpPassword)
        $ftpRequest.Method = [System.Net.WebRequestMethods+Ftp]::UploadFile
        $ftpRequest.UseBinary = $true
        $ftpRequest.UsePassive = $true
        
        $fileBytes = [System.IO.File]::ReadAllBytes($file.FullName)
        $ftpRequest.ContentLength = $fileBytes.Length
        
        $requestStream = $ftpRequest.GetRequestStream()
        $requestStream.Write($fileBytes, 0, $fileBytes.Length)
        $requestStream.Close()
        
        $response = $ftpRequest.GetResponse()
        $response.Close()
        
        $uploadedCount++
        Write-Host "  OK: $relativePath" -ForegroundColor Green
        
    } catch {
        $failedCount++
        Write-Host "  FAIL: $relativePath" -ForegroundColor Red
    }
    
    if ((($uploadedCount + $failedCount) % 20) -eq 0) {
        $percent = [math]::Round((($uploadedCount + $failedCount) / $totalFiles) * 100)
        Write-Host "  Progress: $percent% complete..." -ForegroundColor Cyan
    }
}

Write-Host ""
Write-Host "[3/3] Upload Results:" -ForegroundColor Yellow
Write-Host "  Success: $uploadedCount files" -ForegroundColor Green
if ($failedCount -gt 0) {
    Write-Host "  Failed: $failedCount files" -ForegroundColor Red
}
Write-Host ""

if ($uploadedCount -eq 0) {
    Write-Host "ERROR: No files were uploaded!" -ForegroundColor Red
    Write-Host "Please use FileZilla for manual upload:" -ForegroundColor Yellow
    Write-Host "  Download: https://filezilla-project.org" -ForegroundColor Cyan
    Write-Host "  Host: $ftpServer" -ForegroundColor White
    Write-Host "  User: $ftpUsername" -ForegroundColor White
    Write-Host "  Pass: $ftpPassword" -ForegroundColor White
    Write-Host ""
    exit 1
}

Write-Host "========================================" -ForegroundColor Green
Write-Host "SUCCESS! Files uploaded to server." -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Green
Write-Host ""
Write-Host "NEXT STEPS:" -ForegroundColor Yellow
Write-Host ""
Write-Host "1. SSH to server:" -ForegroundColor White
Write-Host "   ssh $ftpUsername@$ftpServer" -ForegroundColor Cyan
Write-Host ""
Write-Host "2. Run these commands:" -ForegroundColor White
Write-Host "   cd $remotePath" -ForegroundColor Cyan
Write-Host "   nano .env" -ForegroundColor Cyan
Write-Host "   (Copy content from .env.production, save with Ctrl+O, Exit with Ctrl+X)" -ForegroundColor Gray
Write-Host ""
Write-Host "   php artisan key:generate --force" -ForegroundColor Cyan
Write-Host "   composer install --no-dev --optimize-autoloader" -ForegroundColor Cyan
Write-Host "   php artisan migrate --force" -ForegroundColor Cyan
Write-Host "   chmod -R 775 storage bootstrap/cache" -ForegroundColor Cyan
Write-Host "   php artisan optimize" -ForegroundColor Cyan
Write-Host ""
Write-Host "3. Setup domain in cPanel:" -ForegroundColor White
Write-Host "   Document Root: $remotePath/public" -ForegroundColor Cyan
Write-Host ""
Write-Host "4. Test website:" -ForegroundColor White
Write-Host "   https://sister.unic.ac.id" -ForegroundColor Cyan
Write-Host ""
