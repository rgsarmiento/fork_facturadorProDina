<style>
    .text-center {
        text-align: center;
    }
    .font-weight {
        font-weight: bold;
    }
</style>
@php
$col_span = 25;
@endphp
<table>
    <tr>
        <td colspan="{{ $col_span }}">{{ $company['name'] }}</td>
    </tr>
    <tr>
        <td colspan="{{ $col_span }}">{{ $company['number'] }}</td>
    </tr>
    <tr>
        <td colspan="{{ $col_span }}">Moneda: SOLES</td>
    </tr>
    <tr>
        <td colspan="{{ $col_span }}" class="text-center font-weight">FORMATO 14.1 : "REGISTRO DE VENTAS E INGRESOS  DEL PERIODO {{ $period }}"</td>
    </tr>
    <tr>
        <td colspan="2">
            NUMERO CORRELATIVO DEL REGISTRO O CUO.
        </td>
        <td>
            FECHA DE EMISION DEL COMPROBANTE DE PAGO O EMISION DEL DOCUMENTO
        </td>
        <td>
            FECHA VENC.
        </td>
        <td colspan="3">
            COMPROBANTE DE PAGO
        </td>
        <td colspan="3">
            INFORMACON DE CLIENTE
        </td>
        <td>
            VALOR<br/>FACTURADO<br/>EXPORTACION
        </td>
        <td>
            BASE<br/>IMPONIBLE<br/>GRAVADA
        </td>
        <td colspan="2">
            IMPORTE TOTAL
        </td>
        <td>
            ISC
        </td>
        <td>VENTA DIFERIDA</td>
        <td>
            IGV Y/O<br/>IMP.
        </td>
        <td>
            OTROS<br/>TRIBUTOS
        </td>
        <td>
            IMPORTE TOTAL
        </td>
        <td>
            TIPO DE<br/>CAMBIO
        </td>
        <td>
            MONEDA
        </td>
        <td colspan="4">
            REFERENCIA DEL COMPROBANTE O<br/>
            DOC. ORIGINAL QUE SE MODIFICA
        </td>
    </tr>
    <tr>
        <td colspan="2"></td>
        <td></td>
        <td></td>
        <td>TIPO</td>
        <td>SERIE</td>
        <td>NUMERO</td>
        <td>TIPO</td>
        <td>R.U.C.</td>
        <td>APELLIDOS Y NOMBRES</td>
        <td></td>
        <td></td>
        <td>EXONERADA</td>
        <td>INAFECTA</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td>FECHA</td>
        <td>TIPO</td>
        <td>SERIE</td>
        <td>Nro.COMP.</td>
    </tr>
    @foreach($records as $row)
    <tr>
        <td>06</td>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $row['date_of_issue'] }}</td>
        <td></td>
        <td>{{ $row['document_type_id'] }}</td>
        <td>{{ $row['series'] }}</td>
        <td>{{ $row['number'] }}</td>
        <td>{{ $row['customer_identity_document_type_id'] }}</td>
        <td>{{ $row['customer_number'] }}</td>
        <td>{{ $row['customer_name'] }}</td>
        <td>{{ $row['total_exportation'] }}</td>
        <td>{{ $row['total_taxed'] }}</td>
        <td>{{ $row['total_exonerated'] }}</td>
        <td>{{ $row['total_unaffected'] }}</td>
        <td></td>
        <td></td>
        <td></td>
        <td>{{ $row['total_igv'] }}</td>
        <td>{{ $row['total'] }}</td>
        <td>{{ $row['exchange_rate_sale'] }}</td>
        <td>{{ $row['currency_type_symbol'] }}</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    @endforeach
</table>