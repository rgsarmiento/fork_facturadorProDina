<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="Content-Type" content="application/pdf; charset=utf-8" />
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Document</title>
        <style>
            html {
                font-family: sans-serif;
                font-size: 12px;
            }

            table {
                width: 100%;
                border-spacing: 0;
                border: 1px solid black;
            }

            .celda {
                text-align: center;
                padding: 5px;
                border: 0.1px solid black;
            }

            th {
                padding: 5px;
                text-align: center;
                border-color: #0088cc;
                border: 0.1px solid black;
            }

            .title {
                font-weight: bold;
                padding: 5px;
                font-size: 20px !important;
                text-decoration: underline;
            }

            p>strong {
                margin-left: 5px;
                font-size: 13px;
            }

            thead {
                font-weight: bold;
                background: #0088cc;
                color: white;
                text-align: center;
            }
        </style>
    </head>
    <body>
        <div>
            <p align="center" class="title"><strong>Informe Fiscal</strong></p>
        </div>
        <div style="margin-top:20px; margin-bottom:20px;">
            <table>
                <tr>
                    <td>
                        <p><strong>Empresa: </strong>{{$company->name}}</p>
                    </td>
                    <td>
                        <p><strong>Fecha: </strong>{{date('Y-m-d')}}</p>
                    </td>
                </tr>
                <tr>
                    <td>
                        <p><strong>N° Documento: </strong>{{$company->number}}</p>
                    </td>
                    <td>
                        <p><strong>Establecimiento: </strong>{{$establishment->address}} - {{$establishment->country->name}} - {{$establishment->department->name}} - {{$establishment->city->name}}</p>
                    </td>
                </tr>
            </table>
        </div>
            @inject('report', 'App\Services\TaxReportService')

            @php
                $excento = $report->getTotalExcento($items);

                $sale_total_iva5 = $report->getTotalIva5($items, true);
                $sale_total_iva19 = $report->getTotalIva19($items, true);
                $sale_total_ic8= $report->getTotalIC8($items, true);

                $base_imp_iva5 = $report->getTotalIva5($items);
                $base_imp_iva19 = $report->getTotalIva19($items);
                $base_imp_ic8 = $report->getTotalIC8($items);

                $discount = $report->getDiscounts($items);

                $total_iva = ( ($sale_total_iva5 + $sale_total_iva19 ) - (  $base_imp_iva5 + $base_imp_iva19 ) );
                $total_ic = ( $sale_total_ic8 - $base_imp_ic8);

                $total_desc = ($total_sale - $discount);


            @endphp
            <div class="">
                <table class="">
                        <tr>
                            <td>Venta Total:</td>
                            <td> {{ number_format($total_sale, 2) }} </td>
                        </tr>

                        <tr style="margin-left: 4px;">
                            <td>Excento:</td>
                            <td> {{ number_format($excento, 2) }} </td>
                        </tr>


                        <tr style="margin-left: 4px;">
                            <td>Grav IVA 5:</td>
                            <td> {{ number_format( $sale_total_iva5, 2) }} </td>
                        </tr>

                        <tr style="margin-left: 4px;">
                            <td>Grav IVA 19:</td>
                            <td> {{ number_format($sale_total_iva19, 2) }} </td>
                        </tr>

                        <tr style="margin-left: 4px;">
                            <td>Grav I.C 8:</td>
                            <td> {{ number_format($sale_total_ic8, 2) }} </td>
                        </tr>

                    <tr>
                        <td>Base Imp. :</td>
                        <td> {{ number_format($total_sale_base, 2) }} </td>
                    </tr>

                        <tr>
                            <td>Excento:</td>
                            <td> {{ number_format($excento, 2) }} </td>
                        </tr>

                        <tr>
                            <td>Grav IVA 5:</td>
                            <td> {{ number_format( $salbase_imp_iva5e_total_iva5, 2) }} </td>
                        </tr>

                        <tr>
                            <td>Grav IVA 19:</td>
                            <td> {{ number_format($base_imp_iva19, 2) }} </td>
                        </tr>

                        <tr>
                            <td>Grav I.C 8:</td>
                            <td> {{ number_format($base_imp_ic8, 2) }} </td>
                        </tr>

                    <tr>
                        <td>Total Iva:</td>
                        <td> {{ $total_iva }} </td>
                    </tr>

                    <tr>
                        <td>Total IC:</td>
                        <td> {{ $total_ic }} </td>
                    </tr>

                    <tr>
                        <td>Total Descuento:</td>
                        <td> {{ $discount }} </td>
                    </tr>

                    <tr>
                        <td>Total - Desc.:</td>
                        <td> {{ $total_desc}} </td>
                    </tr>

                </table>
            </div>
    </body>
</html>
