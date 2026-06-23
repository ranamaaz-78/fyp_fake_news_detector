# Start Laravel with increased upload limits (large CSV training files)
Set-Location $PSScriptRoot\..
php artisan serve --port=8000 --host=127.0.0.1 @args
