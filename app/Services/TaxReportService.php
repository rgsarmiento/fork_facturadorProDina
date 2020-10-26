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
            throw new Exception("Tax IVA0 no encontrado");
            //return 0;
        }
        $sum = collect($items)->where('tax_id', $tax->id )->sum('total');

        return  $sum;
    }

    public function getTotalIva5($items, $total_sale = false)
    {
        $tax = Tax::where('name', 'IVA5')->first();
        if(!$tax)
        {
            throw new Exception("Tax IVA5 no encontrado");
            //return 0;
        }
        $sum = collect($items)->where('tax_id', $tax->id )->sum('total');

        return  ($total_sale ? ($sum / 1.05) : $sum);
    }

    public function getTotalIva19($items, $total_sale = false)
    {
        $tax = Tax::where('name', 'IVA')->first();
        if(!$tax)
        {
            throw new Exception("Tax IVA no encontrado");
            //return 0;
        }
        $sum = collect($items)->where('tax_id', $tax->id )->sum('total');

        return ($total_sale ? ($sum / 1.19) : $sum);
    }

    public function getTotalIC8($items, $total_sale = false)
    {
        $tax = Tax::where('code', '02')->first(); //impuesto al consumo
        if(!$tax)
        {
            throw new Exception("Tax IMPUESTO AL CONSUMO no encontrado");
            //return 0;
        }
        $sum = collect($items)->where('tax_id', $tax->id )->sum('total');

        return ($total_sale ? ($sum / 1.08) : $sum);
    }

    public function getDiscounts($items)
    {
        $discount = collect($items)->sum('discount');
        return $discount;
    }


}
