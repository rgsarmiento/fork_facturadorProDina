<?php

namespace App\Models\Tenant;

// use App\Models\Tenant\Catalogs\Country;
// use App\Models\Tenant\Catalogs\Department;
use App\Models\Tenant\Catalogs\District;
// use App\Models\Tenant\Catalogs\IdentityDocumentType;
use App\Models\Tenant\Catalogs\Province;
use Illuminate\Database\Eloquent\Builder;

use Modules\Factcolombia1\Models\Tenant\{
    TypePerson,
    TypeRegime,
    TypeIdentityDocument,
    Country,
    Department,
    City,
};

class Person extends ModelTenant
{
    protected $table = 'persons';
    protected $with = ['identity_document_type', 'country', 'department', 'province', 'district'];
    protected $fillable = [
        'type',
        'identity_document_type_id',
        'number',
        'name',
        'trade_name',
        'country_id',
        'department_id',
        'province_id',
        'district_id',
        'address',
        'email',
        'telephone',
        'perception_agent',
        'state',
        'condition',
        'percentage_perception',
        'person_type_id',
        'comment',
        'enabled',

        'type_person_id',
        'type_regime_id',
        'city_id',
        'code',
        'dv',


    ];

    // protected static function boot()
    // {
    //     parent::boot();

    //     static::addGlobalScope('active', function (Builder $builder) {
    //         $builder->where('status', 1);
    //     });
    // }
    
    public function typePerson() {
        return $this->belongsTo(TypePerson::class);
    }
    
    public function typeRegime() {
        return $this->belongsTo(TypeRegime::class);
    }
    
    public function identity_document_type()
    {
        return $this->belongsTo(TypeIdentityDocument::class, 'identity_document_type_id');
    }
    
    public function country() {
        return $this->belongsTo(Country::class);
    }
    
    public function department() {
        return $this->belongsTo(Department::class);
    }
    
    public function city() {
        return $this->belongsTo(City::class);
    }

    
    public function addresses()
    {
        return $this->hasMany(PersonAddress::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'customer_id');
    }

    // public function country()
    // {
    //     return $this->belongsTo(Country::class);
    // }

    // public function department()
    // {
    //     return $this->belongsTo(Department::class);
    // }

    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function scopeWhereType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function getAddressFullAttribute()
    {
        $address = trim($this->address);
        $address = ($address === '-' || $address === '')?'':$address.' ,';
        if ($address === '') {
            return '';
        }
        return "{$address} {$this->department->description} - {$this->province->description} - {$this->district->description}";
    }

    public function more_address()
    {
        return $this->hasMany(PersonAddress::class);
    }

    public function person_type()
    {
        return $this->belongsTo(PersonType::class);
    }
    
    public function scopeWhereIsEnabled($query)
    {
        return $query->where('enabled', true);
    }

}
