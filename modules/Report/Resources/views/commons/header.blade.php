<div style="margin-top:10px; margin-bottom:10px;">
    <table>
        <tr>
            <td width="80%">
                <p><strong>Empresa: </strong>{{$company->name}}</p>
                <p><strong>Establecimiento: </strong>{{$establishment->description}}</p>
                <p><strong>Teléfono: </strong>{{$establishment->telephone}} - <strong>Email: </strong>{{$establishment->email}}</p>
                <p><strong>Dirección: </strong>{{$establishment->address}}</p>
            </td>
            <td width="15%">
                @if($company->logo)
                    <img src="data:{{mime_content_type(public_path("storage/uploads/logos/{$company->logo}"))}};base64, {{base64_encode(file_get_contents(public_path("storage/uploads/logos/{$company->logo}")))}}" alt="{{$company->name}}" alt="{{ $company->name }}"  class="company_logo" style="max-width: 300px">
                @endif
            </td>
        </tr>
    </table>
</div>