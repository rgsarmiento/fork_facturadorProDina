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
            <p align="center" class="title"><strong>Reporte Documentos</strong></p>
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
                        <p><strong>Ruc: </strong>{{$company->number}}</p>
                    </td>
                    <td>
                        <p><strong>Establecimiento: </strong>{{$establishment->address}} - {{$establishment->department->description}} - {{$establishment->district->description}}</p>
                    </td>
                </tr>
            </table>
        </div>
        @if(!empty($records))
            <div class="">
                <div class=" ">
                    <table class="">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Tipo Doc</th>
                                <th>Número</th>
                                <th>Fecha emisión</th>
                                <th>Doc. Afectado</th>

                                <th>Cliente</th>
                                <th>RUC</th>
                                <th>Estado</th>
                                <th class="">Moneda</th>
                                <!-- <th>Total Exonerado</th>
                                <th>Total Inafecto</th>
                                 <th>Total Gratutio</th> -->
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($records as $key => $value)
                            
                                @php
                                    $serie_affec = '';
                                @endphp
                                <tr>
                                    <td class="celda">{{$loop->iteration}}</td>
                                    <td class="celda">{{$value->type_document->name}}</td>
                                    <td class="celda">{{$value->series}}-{{$value->number}}</td>
                                    <td class="celda">{{$value->date_of_issue->format('Y-m-d')}}</td>
                                       
                                        @php 
                                            
                                            if(in_array($value->type_document_id,[2,3]) && $value->reference){

                                                $series = $value->reference->series; 
                                                $number =  $value->reference->number;
                                                $serie_affec = $series.' - '.$number;
                                            }

                                        @endphp
                                        


                                    <td class="celda">{{  $serie_affec }} </td>
                                    <td class="celda">{{$value->customer->name}}</td>
                                    <td class="celda">{{$value->customer->number}}</td>
                                    <td class="celda">{{$value->state_document->name}}</td>
                                    
                                    <td class="celda">{{$value->currency_type_id}}</td>
                                    <td class="celda">{{$value->total}}</td>
                                   
                                </tr>

                            
                                {{-- @php
                                
                                    if($value->currency_type_id == 'PEN'){

                                        /*$acum_total_taxed +=  $signal != '07' ? $value->total_taxed : -$value->total_taxed ;
                                        $acum_total_igv +=  $signal != '07' ? $value->total_igv : -$value->total_igv ;
                                        $acum_total += $signal != '07' ? $value->total : -$value->total ;*/

                                        if(($signal == '07' && $state !== '11')){

                                            $acum_total += -$value->total;
                                            $acum_total_taxed += -$value->total_taxed;
                                            $acum_total_igv += -$value->total_igv;
  

                                        }elseif($signal != '07' && $state == '11'){

                                            $acum_total += 0;
                                            $acum_total_taxed += 0;
                                            $acum_total_igv += 0;
 

                                        }else{

                                            $acum_total += $value->total;
                                            $acum_total_taxed += $value->total_taxed;
                                            $acum_total_igv += $value->total_igv; 
                                        }

                                    }else if($value->currency_type_id == 'USD'){

                                        if(($signal == '07' && $state !== '11')){

                                            $acum_total_usd += -$value->total;
                                            $acum_total_taxed_usd += -$value->total_taxed;
                                            $acum_total_igv_usd += -$value->total_igv;

                                        }elseif($signal != '07' && $state == '11'){

                                            $acum_total_usd += 0;
                                            $acum_total_taxed_usd += 0;
                                            $acum_total_igv_usd += 0;

                                        }else{

                                            $acum_total_usd += $value->total;
                                            $acum_total_taxed_usd += $value->total_taxed;
                                            $acum_total_igv_usd += $value->total_igv;

                                        }

                                    }
                                @endphp --}}
                            @endforeach
                            {{-- <tr>
                                <td class="celda" colspan="8"></td>
                                <td class="celda" >Totales PEN</td>
                                <td class="celda">{{$acum_total_taxed}}</td>
                                <td class="celda">{{$acum_total_igv}}</td>
                                <td class="celda">{{$acum_total}}</td>
                            </tr>
                            <tr>
                                <td class="celda" colspan="8"></td>
                                <td class="celda" >Totales USD</td>
                                <td class="celda">{{$acum_total_taxed_usd}}</td>
                                <td class="celda">{{$acum_total_igv_usd}}</td>
                                <td class="celda">{{$acum_total_usd}}</td>
                            </tr> --}}
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="callout callout-info">
                <p>No se encontraron registros.</p>
            </div>
        @endif
    </body>
</html>
