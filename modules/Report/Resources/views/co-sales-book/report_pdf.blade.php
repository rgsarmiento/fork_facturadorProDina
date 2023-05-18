<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="Content-Type" content="application/pdf; charset=utf-8" />
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Libro Ventas</title>
        
        @include('report::commons.styles')

        <style>
            @page {
              margin: 10;
            }
            
            html {
                font-family: sans-serif;
                font-size: 12px;
            }
        </style>
    </head>
    <body>
        @include('report::commons.header')
        
        <div>
            <p align="left" class="title"><strong>Libro Ventas</strong></p>
        </div>

        {{-- @include('report::co-items-sold.partials.filters') --}}

        @php
            // dd($records, $taxes);
        @endphp
        @if($records->count() > 0)
            <div class="">
                <div class="">
                    <table class="">
                        <thead>
                            <tr>
                                <th colspan="8"></th>
                                
                                @foreach($taxes as $tax)
                                    @php
                                        // dd($tax);
                                    @endphp
                                    <th colspan="2">
                                        IMPUESTO #{{ $loop->iteration }}
                                        <br>
                                        {{ $tax->name }} - ({{ $tax->rate }}%)
                                    </th>

                                @endforeach
                            </tr>
                            <tr>
                                <th>F. Emisión</th>
                                <th>Nro/Doc</th>
                                <th>Nombre</th>
                                <th>Recargos</th>
                                <th>Total/Neto</th>
                                <th>Total + Impuesto</th>
                                <th>Total/Retenido</th>
                                <th>Total/Excento</th>
                                
                                @foreach($taxes as $tax)
                                    <th>Base</th>
                                    <th>Impuesto</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($records as $value)
                                @php
                                    $row = $value->getDataReportSalesBook();

                                    // dd($value->items);
                                @endphp
                                <tr>
                                    <td class="celda">{{ $row['date_of_issue'] }}</td>
                                    <td class="celda">{{$row['type_document_name']}} <br/> {{ $row['number_full'] }}</td>
                                    <td class="celda">{{ $row['customer_name'] }}</td>

                                    <td class="celda"></td>
                                    <td class="celda">{{ $row['sale'] }}</td>
                                    <td class="celda">{{ $row['total'] }}</td>
                                    <td class="celda"></td>
                                    <td class="celda"></td>
                                    
                                    @foreach($taxes as $tax)

                                        @php
                                            $filter_items = $value->items->where('tax_id', $tax->id);
                                            $tax_amount = $filter_items->sum('total_tax');
                                            $taxable_amount = $filter_items->sum('total') - $tax_amount;
                                        @endphp
                                        
                                        @if ($filter_items->count() > 0)

                                            <td class="celda">{{ $taxable_amount }}</td>
                                            <td class="celda">{{ $tax_amount }}</td>
                                            
                                        @else

                                            <td class="celda">0.00</td>
                                            <td class="celda">0.00</td>

                                        @endif

                                    @endforeach

                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="callout callout-info">
                <p><strong>No se encontraron registros.</strong></p>
            </div>
        @endif
    </body>
</html>
