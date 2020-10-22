<?php

namespace App\Services;

use Exception;
use Modules\Factcolombia1\Models\Tenant\Tax;

class TaxReportService
{

    public function getTotalExcento($items)
    {
        $tax = Tax::where('name', 'IVA0')->first();
        if(!$tax)
        {
            //throw new Exception("Tax IVA0 no encontrado");
            return null;
        }
        $sum = collect($items)->where('tax_id', $tax->id )->sum('total');

        return $sum;
    }

    public function getTotalIva5($items)
    {
        $tax = Tax::where('name', 'IVA5')->first();
        if(!$tax)
        {
            //throw new Exception("Tax IVA5 no encontrado");
            return null;
        }
        $sum = collect($items)->where('tax_id', $tax->id )->sum('total');

        return $sum * 1.05;
    }

    public function getTotalIva19($items)
    {
        $tax = Tax::where('name', 'IVA')->first();
        if(!$tax)
        {
            throw new Exception("Tax IVA no encontrado");
        }
        $sum = collect($items)->where('tax_id', $tax->id )->sum('total');

        return $sum * 1.19;
    }


}
