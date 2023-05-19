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

        @include('report::co-sales-book.partials.filters')

        @if($records->count() > 0)
            <div class="">
                <div class="">
                    <table class="">
                        <thead>
                            <tr>
                                <th colspan="7"></th>
                                
                                @foreach($taxes as $tax)
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
                                <th>Moneda</th>
                                <th>Total/Neto</th>
                                <th>Total <br>+<br> Impuesto</th>
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
                                    <td class="celda">{{ $row['currency_code'] }}</td>
                                    <td class="celda text-right-td">{{ $row['net_total'] }}</td>
                                    <td class="celda text-right-td">{{ $row['total'] }}</td>
                                    <td class="celda text-right-td"> {{ $row['total_exempt'] }} </td>
                                    
                                    @foreach($taxes as $tax)

                                        @php
                                            $item_values = $value->getItemValuesByTax($tax->id);
                                        @endphp
                                        
                                        <td class="celda text-right-td">{{ $item_values['taxable_amount'] }}</td>
                                        <td class="celda text-right-td">{{ $item_values['tax_amount'] }}</td>

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
