@php
    $establishment = $document->establishment;
    $customer = $document->customer;
    $invoice = $document->invoice;
    //$path_style = app_path('CoreFacturalo'.DIRECTORY_SEPARATOR.'Templates'.DIRECTORY_SEPARATOR.'pdf'.DIRECTORY_SEPARATOR.'style.css');
    $tittle = $document->series.'-'.str_pad($document->number, 8, '0', STR_PAD_LEFT);
    $payments = $document->payments;

    $config_pos = \App\Models\Tenant\ConfigurationPos::first();

@endphp
<html>
<head>

</head>
<body>

@if($company->logo)
    <div class="text-center company_logo_box pt-5">
        <img src="data:{{mime_content_type(public_path("storage/uploads/logos/{$company->logo}"))}};base64, {{base64_encode(file_get_contents(public_path("storage/uploads/logos/{$company->logo}")))}}" alt="{{$company->name}}" class="company_logo_ticket contain">
    </div>
@endif
<table class="full-width">
    <tr>
        <td class="text-center"><h4>{{ $company->name }}</h4></td>
    </tr>
    <tr>
        <td><h5>Nit: {{ $company->identification_number }} - {{ $company->type_regime->name}} </h5></td>
    </tr>
    <tr>
        <td><h5>  {{ ($establishment->address !== '-')? $establishment->address : '' }} </h5></td>
    </tr>
    <tr>
        <td> <h6>Telefonos: {{ ($establishment->telephone !== '-')? $establishment->telephone : '' }}</h6> </td>
    </tr>
    <tr>
        <td> <h6>Email: {{ ($establishment->email !== '-')? $establishment->email : '' }}</h6> </td>
    </tr>
    <tr>
        <td> <h6>Factura de Venta: {{ $tittle }}</h6> </td>
    </tr>
    <tr>
        <td> <h6>Vendedor:  {{ $document->user->name }} </h6></td>
    </tr>
    <tr>
        <td><h6>Caja: 01</h6></td>
    </tr>
    <tr>
        <td><h6>Fecha: {{ $document->date_of_issue->format('d-m-Y')}} Vence: {{$document->date_of_issue->format('d-m-Y') }}</h6></td>
    </tr>

    <tr>
        <td> <h6>Doc Cliente: {{ $customer->code }} </h6></td>
    </tr>
    <tr>
        <td><h6> Nombre: {{ $customer->name }}</h6></td>
    </tr>
    <tr>
        <td> <h6>Direccion: {{ $customer->address }} </h6></td>
    </tr>
    <tr>
        <td> <h6>Ciudad: {{ ($customer->city_id)? ''.$customer->city->name : '' }} </h6></td>
    </tr>
    <tr>
        <td> <h6>Tipo Venta: CONTADO 0 días </h6></td>
    </tr>

</table>

<table class="full-width mt-10 mb-10">
    <thead class="">
    <tr>
        <th class="border-top-bottom desc-9 text-left">CODIGO</th>
        <th class="border-top-bottom desc-9 text-left">DESCRIPCIÓN</th>
    </tr>
    </thead>
    <tbody>
    @foreach($document->items as $row)
        <tr>
            <td class="desc-9 align-top"> {{ $row->item->internal_id }}</td>
            <td class="text-left desc-9 align-top">
                {!!$row->item->name!!} @if (!empty($row->item->presentation)) {!!$row->item->presentation->description!!} @endif
                @if($row->attributes)
                    @foreach($row->attributes as $attr)
                        <br/>{!! $attr->description !!} : {{ $attr->value }}
                    @endforeach
                @endif
                @if($row->discount > 0)
                <br>
                {{ $row->discount }}
                @endif
            </td>
        </tr>
        <tr>
            <td colspan="2" class="border-bottom"></td>
        </tr>
    @endforeach
    </tbody>
</table>
<table class="full-width">
    <tr>

        @foreach(array_reverse((array) $document->legends) as $row)
            <tr>
                @if ($row->code == "1000")
                    <td class="desc pt-3">Son: <span class="font-bold">{{ $row->value }} {{ $document->currency_type->description }}</span></td>
                    @if (count((array) $document->legends)>1)
                    <tr><td class="desc pt-3"><span class="font-bold">Leyendas</span></td></tr>
                    @endif
                @else
                    <td class="desc pt-3">{{$row->code}}: {{ $row->value }}</td>
                @endif
            </tr>
        @endforeach
    </tr>


</table>
<table class="full-width">
    <tr>
            <td colspan="2" class="text-right font-bold desc">TOTAL VENTA: {{ $document->currency->symbol }}</td>
            <td class="text-right font-bold desc">{{ $document->sale }}</td>
        </tr>
        <tr >
            <td colspan="2" class="text-right font-bold desc">TOTAL DESCUENTO (-): {{ $document->currency->symbol }}</td>
            <td class="text-right font-bold desc">{{ $document->total_discount }}</td>
        </tr>

        <tr>
            <td colspan="2" class="text-right font-bold desc">SUBTOTAL: {{ $document->currency->symbol }}</td>
            <td class="text-right font-bold desc">{{ $document->subtotal }}</td>
        </tr>
        
        @foreach ($document->taxes as $tax)
            @if ((($tax->total > 0) && (!$tax->is_retention)))
                <tr >
                    <td colspan="2" class="text-right font-bold desc">
                        {{$tax->name}}(+): {{ $document->currency->symbol }}
                    </td>
                    <td class="text-right font-bold desc">{{number_format($tax->total, 2)}} </td>
                </tr>
            @endif
        @endforeach



        <tr>
            <td colspan="2" class="text-right font-bold desc">TOTAL A PAGAR: {{ $document->currency_type->symbol }}</td>
            <td class="text-right font-bold desc">{{ number_format($document->total, 2) }}</td>
        </tr>
</table>
<table class="full-width">
    <tr><td><h6>PAGOS:</h6> </td></tr>
    @php
        $payment = 0;
    @endphp
    @foreach($payments as $row)
        <tr><td>- {{ $row->date_of_payment->format('d/m/Y') }} - {{ $row->payment_method_type->description }} - {{ $row->reference ? $row->reference.' - ':'' }} {{ $document->currency_type->symbol }} {{ $row->payment }}</td></tr>
        @php
            $payment += (float) $row->payment;
        @endphp
    @endforeach
    <tr><td><h6>SALDO:</h6> {{ $document->currency_type->symbol }} {{ number_format($document->total - $payment, 2) }}</td></tr>
</table>
<table style="margin-top:3px" class="full-width">
    <tr>
        <td> <h6>Resol. DIAN #: {{ $config_pos->resolution_number }} de {{ $config_pos->resolution_date->format('d-m-Y') }}</h6> </td>
    </tr>
    <tr>
        <td>
            <h6>  Desde la
            Factura: {{ $config_pos->from }} a la
            Factura: {{ $config_pos->to }}
            </h6>
        </td>
    </tr>
    <tr>
        <td>Vigencia: 24 Meses.</td>
    </tr>
    <tr>
        <td class="text-center">GRACIAS POR SU COMPRA</td>
    </tr>
</table>
</body>
</html>
