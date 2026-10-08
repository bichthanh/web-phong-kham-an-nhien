[Console]::OutputEncoding = [System.Text.Encoding]::UTF8
Clear-Host
Write-Host "======================================================================" -ForegroundColor Cyan
Write-Host "   HE THONG PHONG KHAM DA KHOA AN NHIEN - NAM DINH (NHOM 12)" -ForegroundColor Green
Write-Host "   DANG KHOI TAO DUONG LINK ONLINE MIEN PHI QUA CLOUDFLARE..." -ForegroundColor Yellow
Write-Host "======================================================================" -ForegroundColor Cyan
Write-Host "Vui long doi khoang 5 giay..." -ForegroundColor Gray

$scriptDir = if ($PSScriptRoot) { $PSScriptRoot } else { Split-Path -Parent $MyInvocation.MyCommand.Path }
$cloudflaredPath = Join-Path $scriptDir "cloudflared.exe"

if (-not (Test-Path $cloudflaredPath)) {
    Write-Host "[LOI] Khong tim thay cloudflared.exe tai: $cloudflaredPath" -ForegroundColor Red
    pause
    exit
}

# Kiem tra web server port 8000
$tcp = New-Object System.Net.Sockets.TcpClient
$isConnected = $false
try {
    $tcp.Connect("127.0.0.1", 8000)
    $isConnected = $true
    $tcp.Close()
} catch {
    $isConnected = $false
}

if (-not $isConnected) {
    Write-Host "[*] Dang tu dong khoi dong may chu web (run_server.bat)..." -ForegroundColor Yellow
    Start-Process cmd.exe -ArgumentList "/k `"$scriptDir\run_server.bat`""
    Start-Sleep -Seconds 3
}

$pinfo = New-Object System.Diagnostics.ProcessStartInfo
$pinfo.FileName = $cloudflaredPath
$pinfo.Arguments = "tunnel --url http://127.0.0.1:8000"
$pinfo.RedirectStandardError = $true
$pinfo.RedirectStandardOutput = $true
$pinfo.UseShellExecute = $false
$pinfo.CreateNoWindow = $true

$proc = New-Object System.Diagnostics.Process
$proc.StartInfo = $pinfo
$null = $proc.Start()

$tunnelUrl = $null
$regex = 'https://[a-zA-Z0-9-]+\.trycloudflare\.com'

$timeout = [System.DateTime]::Now.AddSeconds(30)
while (-not $proc.HasExited -and -not $tunnelUrl -and ([System.DateTime]::Now -lt $timeout)) {
    $line = $proc.StandardError.ReadLine()
    if ($line -match $regex) {
        $tunnelUrl = $matches[0]
        break
    }
}

if ($tunnelUrl) {
    # Sao chep vao Clipboard
    try {
        Set-Clipboard -Value $tunnelUrl
    } catch {
        $tunnelUrl | clip
    }

    # Tu dong mo trinh duyet
    Start-Process $tunnelUrl

    Clear-Host
    Write-Host "======================================================================" -ForegroundColor Green
    Write-Host "       DA TAO DUONG LINK ONLINE THANH CONG CHO TRANG WEB!" -ForegroundColor Green
    Write-Host "======================================================================" -ForegroundColor Green
    Write-Host ""
    Write-Host "  LINK TRUY CAP ONLINE CUA BAN LA:" -ForegroundColor White
    Write-Host "  >> $tunnelUrl <<" -ForegroundColor Yellow
    Write-Host ""
    Write-Host "  [OK] DA TU DONG COPY LINK VAO BO NHO TAM (Nhan Ctrl + V de gui cho ban be)" -ForegroundColor Cyan
    Write-Host "  [OK] DA TU DONG BAT CHROME MO TRANG WEB CHO BAN KIEM TRA" -ForegroundColor Cyan
    Write-Host ""
    Write-Host "  ============================ LUU Y ============================" -ForegroundColor Red
    Write-Host "  1. Hay GIU NGUYEN CUA SO NAY de link online tiep tuc hoat dong." -ForegroundColor Yellow
    Write-Host "  2. Gui link nay cho bat ky ai (dien thoai, may tinh khac deu xem duoc)." -ForegroundColor Yellow
    Write-Host "  3. Khi nao nghi khong muon chia se nua thi tat cua so nay di." -ForegroundColor Yellow
    Write-Host "======================================================================" -ForegroundColor Green
    Write-Host ""
    Write-Host "Dang duy tri ket noi online... (Nhan Ctrl + C hoac dong cua so de ket thuc)" -ForegroundColor Gray

    $proc.WaitForExit()
} else {
    Write-Host "[!] Khong the tao link tunnel. Vui long thu lai." -ForegroundColor Red
    $proc.Kill()
    pause
}
