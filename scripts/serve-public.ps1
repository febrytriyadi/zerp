param(
    [string]$port = "8000"
)

$ip = (Get-NetIPAddress -AddressFamily IPv4 | Where-Object { $_.IPAddress -notmatch '^(127\.|169\.254\.|172\.)' } | Select-Object -First 1).IPAddress

if (-not $ip) {
    $ip = (Get-NetIPAddress -AddressFamily IPv4 | Where-Object { $_.IPAddress -notlike '127.*' } | Select-Object -First 1).IPAddress
}

Write-Host "============================================" -ForegroundColor Cyan
Write-Host "  ZERP - Starting Public Server" -ForegroundColor Green
Write-Host "============================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "  Local:    http://localhost:$port" -ForegroundColor Yellow
Write-Host "  Network:  http://${ip}:$port" -ForegroundColor Yellow
Write-Host ""
Write-Host "  Access from other devices: http://${ip}:$port" -ForegroundColor Green
Write-Host ""
Write-Host "  Press Ctrl+C to stop" -ForegroundColor Gray
Write-Host "============================================" -ForegroundColor Cyan
Write-Host ""

Set-Location -LiteralPath "$PSScriptRoot\.."

php artisan serve --host=0.0.0.0 --port=$port
