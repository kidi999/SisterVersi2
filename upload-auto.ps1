# Auto Upload SISTER to Production Server via FTP
# =================================================

param(
    [switch]$SkipZip = $false
)

$ErrorActionPreference = "Continue"

# Configuration
$ftpHost = "103.241.192.78"
$ftpUser = "unic"
$ftpPass = "qwert12345"
$remotePath = "/home/unic/sister"
$localPath = $PSScriptRoot

Write-Host "`n╔════════════════════════════════════════════╗" -ForegroundColor Cyan
Write-Host "║   SISTER - Auto Upload to Production    ║" -ForegroundColor Cyan
Write-Host "╚════════════════════════════════════════════╝`n" -ForegroundColor Cyan

# Function to upload file via FTP
function Upload-FileToFTP {
    param(
        [string]$LocalFile,
        [string]$RemoteFile
    )
    
    try {
        $ftpUri = "ftp://$ftpHost$RemoteFile"
        $request = [System.Net.FtpWebRequest]::Create($ftpUri)
        $request.Method = [System.Net.WebRequestMethods+Ftp]::UploadFile
        $request.Credentials = New-Object System.Net.NetworkCredential($ftpUser, $ftpPass)
        $request.UseBinary = $true
        $request.UsePassive = $true
        $request.KeepAlive = $false
        
        $fileContent = [System.IO.File]::ReadAllBytes($LocalFile)
        $request.ContentLength = $fileContent.Length
        
        $stream = $request.GetRequestStream()
        $stream.Write($fileContent, 0, $fileContent.Length)
        $stream.Close()
        
        $response = [System.Net.FtpWebResponse]$request.GetResponse()
        $response.Close()
        
        return $true
    } catch {
        Write-Host "  ✗ Error: $($_.Exception.Message)" -ForegroundColor Red
        return $false
    }
}

# Function to create remote directory
function Create-FTPDirectory {
    param([string]$RemoteDir)
    
    try {
        $ftpUri = "ftp://$ftpHost$RemoteDir"
        $request = [System.Net.FtpWebRequest]::Create($ftpUri)
        $request.Method = [System.Net.WebRequestMethods+Ftp]::MakeDirectory
        $request.Credentials = New-Object System.Net.NetworkCredential($ftpUser, $ftpPass)
        $request.UsePassive = $true
        
        $response = $request.GetResponse()
        $response.Close()
        return $true
    } catch {
        return $false
    }
}

# Exclude patterns
$excludePatterns = @(
    '.git',
    '.env',
    'node_modules',
    'vendor',
    'storage\logs',
    'storage\framework\cache',
    'storage\framework\sessions',
    'storage\framework\views',
    '.log',
    'deploy-direct.bat',
    'deploy.bat',
    'upload.bat',
    'upload-simple.bat',
    'upload-ftp.ps1',
    'upload-auto.ps1',
    'setup-server.bat',
    'manual-upload-guide.ps1',
    '.zip',
    '.md',
    '.gitignore',
    '.gitattributes'
)

Write-Host "[1/5] Mengumpulkan daftar file..." -ForegroundColor Yellow

# Get all files
$allFiles = Get-ChildItem -Path $localPath -Recurse -File | Where-Object {
    $file = $_
    $relativePath = $file.FullName.Substring($localPath.Length + 1)
    
    $shouldExclude = $false
    foreach ($pattern in $excludePatterns) {
        if ($relativePath -like "*$pattern*") {
            $shouldExclude = $true
            break
        }
    }
    
    -not $shouldExclude
}

$totalFiles = $allFiles.Count
Write-Host "  ✓ Ditemukan $totalFiles file untuk diupload" -ForegroundColor Green

Write-Host "`n[2/5] Membuat struktur folder di server..." -ForegroundColor Yellow

# Get unique directories
$directories = $allFiles | ForEach-Object {
    $relativePath = $_.FullName.Substring($localPath.Length + 1)
    $remoteFile = "$remotePath/$($relativePath -replace '\\', '/')"
    Split-Path $remoteFile -Parent
} | Select-Object -Unique | Sort-Object

foreach ($dir in $directories) {
    $null = Create-FTPDirectory -RemoteDir $dir
}

Write-Host "  ✓ Struktur folder dibuat" -ForegroundColor Green

Write-Host "`n[3/5] Mengupload file ke server..." -ForegroundColor Yellow
Write-Host "  Server: ftp://$ftpHost$remotePath" -ForegroundColor Gray
Write-Host "  User: $ftpUser`n" -ForegroundColor Gray

$uploaded = 0
$failed = 0
$failedFiles = @()

foreach ($file in $allFiles) {
    $relativePath = $file.FullName.Substring($localPath.Length + 1)
    $remoteFile = "$remotePath/$($relativePath -replace '\\', '/')"
    
    $percent = [math]::Round((($uploaded + $failed) / $totalFiles) * 100, 1)
    Write-Host "  [$percent%] $relativePath" -ForegroundColor Cyan -NoNewline
    
    $uploadResult = Upload-FileToFTP -LocalFile $file.FullName -RemoteFile $remoteFile
    if ($uploadResult) {
        Write-Host " ✓" -ForegroundColor Green
        $uploaded++
    } else {
        Write-Host " ✗" -ForegroundColor Red
        $failed++
        $failedFiles += $relativePath
    }
}

Write-Host "`n[4/5] Hasil Upload:" -ForegroundColor Yellow
Write-Host "  ✓ Berhasil: $uploaded file" -ForegroundColor Green
if ($failed -gt 0) {
    Write-Host "  ✗ Gagal: $failed file" -ForegroundColor Red
    Write-Host "`n  File yang gagal:" -ForegroundColor Yellow
    foreach ($failedFile in $failedFiles) {
        Write-Host "    - $failedFile" -ForegroundColor Red
    }
}

if ($uploaded -eq 0) {
    Write-Host "`n[ERROR] Tidak ada file yang berhasil diupload!" -ForegroundColor Red
    Write-Host "`nKemungkinan masalah:" -ForegroundColor Yellow
    Write-Host "  1. Koneksi FTP terblokir firewall" -ForegroundColor White
    Write-Host "  2. Kredensial FTP salah" -ForegroundColor White
    Write-Host "  3. Server FTP tidak aktif" -ForegroundColor White
    Write-Host "`nSolusi: Gunakan FileZilla untuk upload manual" -ForegroundColor Cyan
    Write-Host "  Download: https://filezilla-project.org`n" -ForegroundColor Cyan
    exit 1
}

Write-Host "`n[5/5] Upload selesai! 🎉" -ForegroundColor Green

Write-Host "`n╔════════════════════════════════════════════╗" -ForegroundColor Green
Write-Host "║     LANGKAH SELANJUTNYA DI SERVER       ║" -ForegroundColor Green
Write-Host "╚════════════════════════════════════════════╝`n" -ForegroundColor Green

Write-Host "1. Login SSH ke server:" -ForegroundColor Yellow
Write-Host "   ssh $ftpUser@$ftpHost`n" -ForegroundColor White

Write-Host "2. Cek file yang sudah terupload:" -ForegroundColor Yellow
Write-Host "   cd $remotePath" -ForegroundColor White
Write-Host "   ls -la`n" -ForegroundColor White

Write-Host "3. Buat file .env (PENTING!):" -ForegroundColor Yellow
Write-Host "   nano .env" -ForegroundColor White
Write-Host "   # Copy isi dari file .env.production di project lokal" -ForegroundColor Gray
Write-Host "   # Save: Ctrl+O, Enter, Ctrl+X`n" -ForegroundColor Gray

Write-Host "4. Setup aplikasi:" -ForegroundColor Yellow
Write-Host @"
   php artisan key:generate --force
   composer install --no-dev --optimize-autoloader
   php artisan migrate --force
   php artisan db:seed --force
   chmod -R 775 storage bootstrap/cache
   php artisan optimize
"@ -ForegroundColor White

Write-Host "`n5. Setup domain di cPanel:" -ForegroundColor Yellow
Write-Host "   Login: https://$ftpHost`:2083" -ForegroundColor White
Write-Host "   Domain: sister.unic.ac.id" -ForegroundColor White
Write-Host "   Document Root: $remotePath/public" -ForegroundColor Cyan
Write-Host "   (HARUS ke folder /public!)`n" -ForegroundColor Red

Write-Host "6. Test website:" -ForegroundColor Yellow
Write-Host "   https://sister.unic.ac.id`n" -ForegroundColor Cyan

Write-Host "╚════════════════════════════════════════════╝`n" -ForegroundColor Green

Write-Host "Catatan: File .env BELUM diupload (exclude)." -ForegroundColor Yellow
Write-Host "Anda harus membuat file .env manual di server!`n" -ForegroundColor Yellow
