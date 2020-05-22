<?php

namespace App\Http\Controllers\Tenant;

use App\Imports\Tenant\ClientsImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\{
    ClientUpdateRequest,
    ClientImportRequest,
    ClientRequest
};
use Illuminate\Http\Request;
use App\Exports\Tenant\{
    ClientsFormatExport,
    ClientsExport
};
use App\Models\Tenant\{
    TypeIdentityDocument,
    TypePerson,
    TypeRegime,
    Country,
    Client
};

use App\Models\TenantService\{
    Company as ServiceCompany
};




class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

            /*$headers = ['Content-Type' => 'application/json', 'Accept' => 'application/json'];
            $bodyContent = [
               "type_document_identification_id"=> 6,
                "type_organization_id"=> 1,
                "type_regime_id"=> 2,
                "type_liability_id"=> 19,
                "business_name"=> "yoo",
                "merchant_registration"=> "99454567-12",
                "municipality_id"=> 1006,
                "address"=> "CALLsssE 1 1C 1",
                "phone"=> 3216547,
                "email"=> "yokj@factura.com"
            ];
            $number = 78748448855;
            $client = new \GuzzleHttp\Client();
            $request_guzzle = $client->post("http://127.0.0.1:8000/api/ubl2.1/config/{$number}/3", ['headers' => $headers, 'json' => $bodyContent]);
            $response = $request_guzzle->getBody();
            $respuesta = json_decode($response);

            return ($respuesta->api_token);*/




            /*$number = 134340912222;
            $ch = curl_init("http://127.0.0.1:8000/api/ubl2.1/config/{$number}/3");
            $data = [
                "type_document_identification_id"=> 6,
                "type_organization_id"=> 1,
                "type_regime_id"=> 2,
                "type_liability_id"=> 19,
                "business_name"=> "yoo",
                "merchant_registration"=> "9554567-12",
                "municipality_id"=> 1006,
                "address"=> "CALLE 1 1C 1",
                "phone"=> 3216547,
                "email"=> "vvvvdfoo@test.test"
            ];
            $datax = json_encode($data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS,($datax));
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Accept: application/json'
            ));
            $response = curl_exec($ch);

            return ( $response);*/
            

           return view('client.tenant.index');
    }
    
    /**
     * All
     * @return \Illuminate\Http\Response
     */
    public function all() {
        return [
            'typeIdentityDocuments' => TypeIdentityDocument::all(),
            'typeRegimes' => TypeRegime::all(),
            'typePeople' => TypePerson::all(),
            'countries' => Country::all(),
            'clients' => Client::query()
                ->with('typePerson', 'typeRegime', 'typeIdentityDocument', 'country', 'department', 'city')
                ->get()
        ];
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Tenant\ClientRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ClientRequest $request) {
        $client = Client::create([
            'type_person_id' => $request->type_person_id,
            'type_regime_id' => $request->type_regime_id,
            'type_identity_document_id' => $request->type_identity_document_id,
            'identification_number' => $request->identification_number,
            'name' => $request->name,
            'country_id' => $request->country_id,
            'department_id' => $request->department_id,
            'city_id' => $request->city_id,
            'address' => $request->address,
            'phone' => $request->phone,
            'email' => $request->email,
            'code' => $request->code,
            'dv' => $request->dv
        ]);
        
        return [
            'success' => true,
            'message' => "Se registro con éxito el cliente {$client->name}."
        ];
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Tenant\ClientUpdateRequest  $request
     * @param  \App\Models\Tenant\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function update(ClientUpdateRequest $request, Client $client) {
        $client->update([
            'type_person_id' => $request->type_person_id,
            'type_regime_id' => $request->type_regime_id,
            'type_identity_document_id' => $request->type_identity_document_id,
            'identification_number' => $request->identification_number,
            'name' => $request->name,
            'country_id' => $request->country_id ,
            'department_id' => $request->department_id,
            'city_id' => $request->city_id,
            'address' => $request->address,
            'phone' => $request->phone,
            'email' => $request->email,
            'dv' => $request->dv
        ]);
        
        $client->save();
        
        return [
            'success' => true,
            'message' => "Se actualizo con éxito el cliente {$client->name}."
        ];
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Tenant\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function destroy(Client $client) {
        $client->forceDelete();
        
        return [
            'success' => true,
            'message' => "Se elimino con éxito el cliente {$client->name}."
        ];
    }
    
    /**
     * Format import
     * @return \Illuminate\Http\Response
     */
    public function formatImport() {
        return Excel::download(new ClientsFormatExport, 'Formato clientes.xlsx');
    }
    
    /**
     * Import
     * @param  \App\Http\Requests\Tenant\ClientImportRequest $request
     * @return \Illuminate\Http\Response
     */
    public function import(ClientImportRequest $request) {
        try {
            if ($request->hasFile('file')) Excel::import(new ClientsImport, $request->file('file'));
        }
        catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
        
        return [
            'success' => true,
            'message' => 'Importación exítosa.'
        ];
    }
    
    /**
     * Export
     * @return \Illuminate\Http\Response
     */
    public function export() {
        return Excel::download(new ClientsExport, 'clientes.xlsx');
    }
}
