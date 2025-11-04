<?php

namespace App\Models;

use App\Services\CurrencyConverterService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use  HasFactory, SoftDeletes;

    protected $table = 'services';

    protected $primaryKey = 'id';

    protected $fillable = [
        'provider_id',
        'price',
        'currency_id',
        'duration_minutes',
        'is_processing',
    ];

    protected $casts = [
        'id' => 'integer',
        'provider_id' => 'integer',
        'price' => 'decimal:2',
        'currency_id' => 'integer',
        'duration_minutes' => 'integer',
        'is_processing' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $attributes = [
        'is_processing' => true, // default 1
        'currency_id' => 1,      // default currency
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */
    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
    public function photos()
    {
        return $this->morphMany(Photo::class, 'imageable');
    }
    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'favorites', 'service_id', 'user_id');
    }
    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
    public function translations()
    {
        return $this->hasMany(ServiceTranslation::class);
    }

    /*
    |--------------------------------------------------------------------------
    | others
    |--------------------------------------------------------------------------
    */
    public function favoritedByUser($userId)
    {
        return $this->favoritedBy()->where('user_id', $userId);
    }

    public function scopeFilter($query)
    {
        $search = request('name');
        $provider_id = request('provider_id');
        $priceFrom = request('priceFrom');
        $priceTo = request('priceTo');

        if( $provider_id OR $search OR $priceFrom OR $priceTo ) request()->merge(['page' => 1]); // Force page 1

        $currency = request()->header('Currency');
        $converter = app(CurrencyConverterService::class);

        // apply filters
        $returnQuery = $query->when($search, function ($q, $search) {
            $q->whereHas('translations', function ($q) use ($search) {
                    return $q->where('name', 'like', "%{$search}%")->where('language_id', Language::getCurrentLanguageId() );
            });
        }) // filter by provider
        ->when($provider_id, function ($q, $provider_id) {
                return $q->where('provider_id', $provider_id);
        })// filter by price range
        ->when( is_numeric($priceFrom), function ($q) use ($priceFrom, $currency, $converter) {
                $convertedAmount = $converter->convert($priceFrom, $currency, 'USD'); // e.g. 450.00
                //    echo $convertedAmount;
                return $q->where('price_usd', '>=', $convertedAmount);
        }) // filter by price range
        ->when( is_numeric($priceTo), function ($q) use ($priceTo, $currency, $converter) {
                $convertedAmount = $converter->convert($priceTo, $currency, 'USD'); // e.g. 450.00
                // echo $convertedAmount;
                return $q->where('price_usd', '<=', $convertedAmount);
        });

        return $returnQuery;
    }
}
