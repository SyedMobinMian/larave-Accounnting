Symfony\Component\Routing\Exception\RouteNotFoundException
vendor\laravel\framework\src\Illuminate\Routing\UrlGenerator.php:534
Route [filament.admin.resources.bank-accounts.index] not defined.











ArgumentCountError
app\Filament\Admin\Resources\ClientResource.php:32
Too few arguments to function App\Filament\Admin\Resources\ClientResource::form(), 0 passed in C:\laragon\www\laravel-Accounting\app\Filament\Admin\Resources\ClientResource.php on line 183 and exactly 1 expected

ArgumentCountError
app\Filament\Admin\Resources\InvoiceResource.php:38
Too few arguments to function App\Filament\Admin\Resources\InvoiceResource::form(), 0 passed in C:\laragon\www\laravel-Accounting\app\Filament\Admin\Resources\InvoiceResource.php on line 254 and exactly 1 expected

ArgumentCountError
app\Filament\Admin\Resources\LeadResource.php:26
Too few arguments to function App\Filament\Admin\Resources\LeadResource::form(), 0 passed in C:\laragon\www\laravel-Accounting\app\Filament\Admin\Resources\LeadResource.php on line 211 and exactly 1 expected

ArgumentCountError
app\Filament\Admin\Resources\VendorResource.php:31
Too few arguments to function App\Filament\Admin\Resources\VendorResource::form(), 0 passed in C:\laragon\www\laravel-Accounting\app\Filament\Admin\Resources\VendorResource.php on line 102 and exactly 1 expected

Illuminate\Database\QueryException
vendor\laravel\framework\src\Illuminate\Database\Connection.php:838
SQLSTATE[42S22]: Column not found: 1054 Unknown column 'bank_accounts.account_name' in 'field list' (Connection: mysql, Host: 127.0.0.1, Port: 3306, Database: perfex_crm_db, SQL: select `bank_accounts`.`account_name`, `bank_accounts`.`id` from `bank_accounts` order by `bank_accounts`.`account_name` asc)

