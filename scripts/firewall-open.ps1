param(
    [string]$port = "8000"
)

$ruleName = "ZERP Laravel Server Port $port"

$existing = netsh advfirewall firewall show rule name="$ruleName" 2>&1
if ($LASTEXITCODE -eq 0) {
    Write-Host "Firewall rule '$ruleName' already exists." -ForegroundColor Yellow
    exit 0
}

Write-Host "Creating firewall rule '$ruleName' for port $port..." -ForegroundColor Cyan
netsh advfirewall firewall add rule name="$ruleName" dir=in action=allow protocol=TCP localport=$port profile=any description="Allow ZERP Laravel dev server"

if ($LASTEXITCODE -eq 0) {
    Write-Host " Done! Firewall rule created successfully." -ForegroundColor Green
} else {
    Write-Host ""
    Write-Host " Failed to create firewall rule." -ForegroundColor Red
    Write-Host "Please run this script AS ADMINISTRATOR:" -ForegroundColor Yellow
    Write-Host "  Right-click PowerShell -> Run as Administrator" -ForegroundColor Yellow
    Write-Host "  Then run: .\scripts\firewall-open.ps1" -ForegroundColor Yellow
}
