# Upload SISTER to Production Server
# PowerShell Native FTP Upload

$ErrorActionPreference = "Stop"

# Server Configuration
$ftpServer = "103.241.192.78"
$ftpUser = "unic"
$ftpPass = "qwert12345"
$remotePath = "/home/unic/sister"

Write-Host "`n========================================" -ForegroundColor Cyan
Write-Host "   SISTER - Upload ke Production Server" -ForegroundColor Cyan
Write-Host "========================================`n" -ForegroundColor Cyan

# Create ZIP file
$zipFile = "sister_deploy.zip"
$zipPath = Join-Path $PSScriptRoot $zipFile

Write-Host "[INFO] Membuat archive file..." -ForegroundColor Yellow

# Remove old zip if exists
if (Test-Path $zipPath) {
    Remove-Item $zipPath -Force
}

# Files and folders to exclude
$excludePatterns = @(
    ".git",
    ".env",
    "node_modules",
    "vendor",
    "storage\logs",
    "storage\framework\cache",
    "storage\framework\sessions",
    "storage\framework\views",
    "*.log",
    "deploy*.bat",
    "upload*.bat",
    "upload*.ps1",
    "setup*.bat",
    "*.zip",
    "*.md"
)

# Get all files
$allFiles = Get-ChildItem -Path $PSScriptRoot -Recurse -File | Where-Object {
    $file = $_
    $shouldInclude = $true
    
    foreach ($pattern in $excludePatterns) {
        if ($file.FullName -like "*$pattern*") {
            $shouldInclude = $false
            break
        }
    }
    
    $shouldInclude
}

Write-Host "[INFO] Menemukan $($allFiles.Count) file untuk di-upload..." -ForegroundColor Green

# Create ZIP
Compress-Archive -Path $allFiles.FullName -DestinationPath $zipPath -CompressionLevel Optimal -Force

$zipSize = [math]::Round((Get-Item $zipPath).Length / 1MB, 2)
Write-Host "[SUCCESS] Archive dibuat: $zipFile ($zipSize MB)" -ForegroundColor Green

# Upload using FTP
Write-Host "`n[INFO] Mengupload ke server..." -ForegroundColor Yellow
Write-Host "Server: ftp://$ftpServer" -ForegroundColor Gray
Write-Host "User: $ftpUser" -ForegroundColor Gray
Write-Host "Path: $remotePath" -ForegroundColor Gray

try {
    # Create FTP request
    $ftpUri = "ftp://$ftpServer$remotePath/$zipFile"
    $request = [System.Net.FtpWebRequest]::Create($ftpUri)
    $request.Method = [System.Net.WebRequestMethods+Ftp]::UploadFile
    $request.Credentials = New-Object System.Net.NetworkCredential($ftpUser, $ftpPass)
    $request.UseBinary = $true
    $request.KeepAlive = $false
    
    # Read file
    $fileContent = [System.IO.File]::ReadAllBytes($zipPath)
    $request.ContentLength = $fileContent.Length
    
    # Upload
    $requestStream = $request.GetRequestStream()
    $requestStream.Write($fileContent, 0, $fileContent.Length)
    $requestStream.Close()
    
    # Get response
    $response = $request.GetResponse()
    $response.Close()
    
    Write-Host "`n[SUCCESS] File berhasil diupload!" -ForegroundColor Green
    
} catch {
    Write-Host "`n[ERROR] Upload gagal: $($_.Exception.Message)" -ForegroundColor Red
    Write-Host "`n[INFO] Silakan gunakan FileZilla untuk upload manual:" -ForegroundColor Yellow
    Write-Host "1. Buka FileZilla" -ForegroundColor White
    Write-Host "2. Connect ke:" -ForegroundColor White
    Write-Host "   Host: $ftpServer" -ForegroundColor Cyan
    Write-Host "   Username: $ftpUser" -ForegroundColor Cyan
    Write-Host "   Password: $ftpPass" -ForegroundColor Cyan
    Write-Host "   Port: 21" -ForegroundColor Cyan
    Write-Host "3. Upload file: $zipFile ke folder: $remotePath" -ForegroundColor White
    Write-Host "4. Extract di server (lihat instruksi di bawah)" -ForegroundColor White
    exit 1
}

# Clean up
Remove-Item $zipPath -Force

Write-Host "`n========================================" -ForegroundColor Green
Write-Host "   LANGKAH SELANJUTNYA DI SERVER" -ForegroundColor Green
Write-Host "========================================`n" -ForegroundColor Green

Write-Host "1. Login ke Terminal/SSH:" -ForegroundColor Yellow
Write-Host "   ssh $ftpUser@$ftpServer`n" -ForegroundColor White

Write-Host "2. Extract dan update:" -ForegroundColor Yellow
Write-Host @"
   cd $remotePath
   
   # Backup .env
   cp .env .env.backup
   
   # Extract file
   unzip -o $zipFile
   
   # Remove zip file
   rm $zipFile
   
   # Update aplikasi
   php artisan migrate --force
   php artisan optimize
   
   # Fix permissions
   chmod -R 775 storage bootstrap/cache
"@ -ForegroundColor White

Write-Host "`n3. Test website:" -ForegroundColor Yellow
Write-Host "   https://sister.unic.ac.id`n" -ForegroundColor Cyan

Write-Host "========================================`n" -ForegroundColor Green
