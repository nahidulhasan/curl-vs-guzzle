<?php

namespace App\Models;

use App\Services\ApiBaseService;
use App\Services\Banglalink\BanglalinkCustomerService;
use App\Services\Banglalink\CustomerConnectionTypeService;
use App\Services\Banglalink\CustomerPackageService;
use App\Services\CustomerService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Customer extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
       'name', 'email', 'phone','customer_account_id','msisdn','birth_date','profile_image','mobile'
    ];

    protected $hidden = ['balance_transfer_pin'];

    /**
     * @return BelongsToMany
     */
    public function shortcuts()
    {
        return $this->belongsToMany(
            Shortcut::class,
            'shortcut_user',
            'user_id',
            'shortcut_id'
        );
    }

    public static function package(Customer $customer)
    {
        $package_service = new CustomerPackageService();

        return $package_service->getPackageInfo($customer->customer_account_id);
    }

    public static function connectionType(Customer $customer)
    {
        $customer_service = new CustomerConnectionTypeService();
        return $customer_service->getConnectionTypeInfo($customer->msisdn);
    }

}
