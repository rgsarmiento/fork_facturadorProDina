<?php

namespace App\Http\Controllers\System;

use App\Jobs\Tenant\ConfigureTenantJob;
use Hyn\Tenancy\Contracts\Repositories\{
    HostnameRepository,
    WebsiteRepository
};
use App\Http\Controllers\Controller;
use App\Http\Requests\System\{
    CompanyUpdateRequest,
    CompanyRequest
};
use App\Models\System\Company;
use Illuminate\Http\Request;
use Hyn\Tenancy\Environment;
use Hyn\Tenancy\Models\{
    Hostname,
    Website
};
use App\Models\Tenant\{
    Company as TenantCompany,
    User
};

use App\Models\TenantService\{
    Company as TenantServiceCompany
};
use Carbon\Carbon;
use DB;

use App\Models\SystemService\{
    Country as ServiceCountry,
   // Department as ServiceDepartment,
    Language as ServiceLanguage,
    Tax as ServiceTax,
    TypeEnvironment as ServiceTypeEnvironment,
    TypeOperation as ServiceTypeOperation,
    TypeDocumentIdentification as ServiceTypeDocumentIdentification,
    TypeCurrency as ServiceTypeCurrency,
    TypeOrganization as ServiceTypeOrganization,
    TypeRegime as ServiceTypeRegime,
    TypeLiability as ServiceTypeLiability,
    Department as ServiceDepartment,
    Municipality as ServiceMunicipality,
    Company as ServiceCompany
};





class CompanyController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CompanyRequest $request) {

        $base_url = env("SERVICE_FACT", "");
        $number = $request->identification_number;
        $dv = $request->dv;
        $ch = curl_init("{$base_url}ubl2.1/config/{$number}/{$dv}");
        $bodyContent = [
            'type_document_identification_id'=> $request->type_document_identification_id,
            'type_organization_id'=> $request->type_organization_id,
            'type_regime_id'=> $request->type_regime_id,
            'type_liability_id'=> $request->type_liability_id,
            'business_name'=> $request->name,
            'merchant_registration'=> $request->merchant_registration,
            'municipality_id'=> $request->municipality_id,
            'address'=> $request->address,
            'phone'=> $request->phone,
            'email'=> $request->email,
            'language_id'=> $request->language_id,
            'tax_id'=> $request->tax_id,
            'type_environment_id'=> $request->type_environment_id,
            'type_operation_id'=> $request->type_operation_id,
            'country_id'=> $request->country_id,
            'type_currency_id'=> $request->type_currency_id
        ];
        $data_companiee = json_encode($bodyContent);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS,($data_companiee));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Accept: application/json',
        ));
        $response = curl_exec($ch);
        curl_close($ch);
        $respuesta = json_decode($response);

        if( !property_exists( $respuesta, 'password' ) || !property_exists( $respuesta, 'token' )  )
        {
            return [
                'message' => "Error al registrar Compañía en ApiDian",
                'response' => $response,
                'success' => false
            ];
        }


        DB::connection('system')->beginTransaction();
        
        try {
            // Website
            $website = new Website;
            $website->uuid = env('DB_DATABASE', 'factura')."_{$request->subdomain}";
            
            app(WebsiteRepository::class)
                ->create($website);
            
            // Hostname
            $hostname = new Hostname;
            $hostname->fqdn = "{$request->subdomain}.".env('APP_URL_BASE', 'factura');
            $hostname = app(HostnameRepository::class)
                ->create($hostname);
            
            app(HostnameRepository::class)
                ->attach($hostname, $website);
            
            $company = Company::create([
                'identification_number' => $request->identification_number,
                'name' => $request->name,
                'email' => $request->email,
                'subdomain' => $request->subdomain,
                'limit_documents' => $request->limit_documents,
                'hostname_id' => $hostname->id,
                'economic_activity_code' => $request->economic_activity_code,
                'ica_rate' => $request->ica_rate

            ]);

            $companyservice = ServiceCompany::create([
                'user_id' => 1, //por default
                'identification_number' => $request->identification_number,
                'dv' => $request->dv, //por default

                'language_id' =>79,
                'tax_id' => 1,
                'type_environment_id' =>  2,
                
                'type_operation_id' =>  10,
                'type_document_identification_id' => $request->type_document_identification_id,
                'country_id' => 46,
                'department_id' => $request->department_id,
                'type_currency_id' =>  35,
                'type_organization_id' => $request->type_organization_id,
                'type_regime_id' => $request->type_regime_id,
                'type_liability_id' => 19,
                'municipality_id' => $request->municipality_id,
                'merchant_registration' => $request->merchant_registration,
                'address' => $request->address,
                'phone' => $request->phone,
            ]);
            
            // Switch
            $tenancy = app(Environment::class);
            $tenancy->tenant($website);
            
            DB::connection('tenant')->beginTransaction();
        }
        catch (\Exception $e) {
            DB::connection('system')->rollBack();
            
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
        
        try {

            //lleno la data maestra
            \Artisan::call('db:seed', array('--class' => 'DataMasterTenantSeeder'));
            //lleno data mestra del servicio
            \Artisan::call('db:seed', array('--class' => 'DataServiceMasterTenantSeeder'));

        
            DB::connection('tenant')
                ->table('users')
                ->insert([
                    'name' => 'Administrador',
                    'email' => $request->email,
                    'password' => bcrypt($request->password),
                    'api_token' => str_random(60),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);


            DB::connection('tenant')
                ->table('companies')
                ->insert([
                    'identification_number' => $company->identification_number,
                    'name' => $company->name,
                    'email' => $company->email,
                    'subdomain' => $company->subdomain,
                    'limit_documents' => $company->limit_documents,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                    'version_ubl_id' => 1,
                    'ambient_id' => 1,
                    'type_identity_document_id' => 1,
                    'type_regime_id' => 1, // estos valores son por default
                    'currency_id' => 170, //// estos valores son por default
                    'economic_activity_code' => $request->economic_activity_code,
                    'ica_rate' => $request->ica_rate
                ]);

                //aqui ingreso la data por q es igual a la data q guarda la api
            DB::connection('tenant')
                   ->table('service_companies')
                   ->insert([
                        'response_data_api' => json_encode($respuesta),
                        'message' => $respuesta->message,
                        'password' => $respuesta->password,
                        'api_token' => $respuesta->token,
                        'user_id' => 1, //por default
                        'identification_number' => $request->identification_number,
                        'dv' => $dv, //por default
                        'language_id' => 79,
                        'tax_id' => 1,
                        'type_environment_id' => 2,
                        'type_operation_id' => 10,
                        'type_document_identification_id' => $request->type_document_identification_id,
                        'country_id' =>46,
                        'department_id' => $request->department_id,
                        'type_currency_id' => 35,
                        'type_organization_id' => $request->type_organization_id,
                        'type_regime_id' => $request->type_regime_id,
                        'type_liability_id' => 19,
                        'municipality_id' => $request->municipality_id,
                        'merchant_registration' => $request->merchant_registration,
                        'address' => $request->address,
                        'phone' => $request->phone,

                    ]);


                    //actualixo los type_tax_id en taxes tenant
                    DB::connection('tenant')
                        ->table('taxes')
                        ->where('id', 1)
                        ->update([
                            'type_tax_id' => 1
                        ]);
                    DB::connection('tenant')
                        ->table('taxes')
                        ->where('id', 2)
                        ->update([
                            'type_tax_id' => 2
                        ]);
                    DB::connection('tenant')
                        ->table('taxes')
                        ->where('id', 3)
                        ->update([
                            'type_tax_id' => 7
                        ]);
                    DB::connection('tenant')
                        ->table('taxes')
                        ->where('id', 4)
                        ->update([
                            'type_tax_id' => 4
                        ]);
                    DB::connection('tenant')
                        ->table('taxes')
                        ->where('id', 5)
                        ->update([
                            'type_tax_id' => 6
                        ]);
                    DB::connection('tenant')
                        ->table('taxes')
                        ->where('id', 6)
                        ->update([
                            'type_tax_id' => 5
                        ]);


                        //ACTULIZO TYPE UNITS CON EL CODE DE LA API
                        DB::connection('tenant')
                        ->table('type_units')
                        ->where('id', 1)
                        ->update([
                            'code' => 70
                        ]);

                        DB::connection('tenant')
                        ->table('type_units')
                        ->where('id', 2)
                        ->update([
                            'code' => 359
                        ]);

                        DB::connection('tenant')
                        ->table('type_units')
                        ->where('id', 3)
                        ->update([
                            'code' => 70
                        ]);

                        DB::connection('tenant')
                        ->table('type_units')
                        ->where('id', 4)
                        ->update([
                            'code' => 44
                        ]);

                        DB::connection('tenant')
                        ->table('type_units')
                        ->where('id', 5)
                        ->update([
                            'code' => 640
                        ]);

                        DB::connection('tenant')
                        ->table('type_units')
                        ->where('id', 6)
                        ->update([
                            'code' => 821
                        ]);
                        DB::connection('tenant')
                        ->table('type_units')
                        ->where('id', 7)
                        ->update([
                            'code' => 813
                        ]);
                        DB::connection('tenant')
                        ->table('type_units')
                        ->where('id', 8)
                        ->update([
                            'code' => 808
                        ]);
                        DB::connection('tenant')
                        ->table('type_units')
                        ->where('id', 9)
                        ->update([
                            'code' => 770
                        ]);
                        DB::connection('tenant')
                        ->table('type_units')
                        ->where('id', 10)
                        ->update([
                            'code' => 70
                        ]);
                        DB::connection('tenant')
                        ->table('type_units')
                        ->where('id', 11)
                        ->update([
                            'code' => 825
                        ]);

                        DB::connection('tenant')
                        ->table('type_units')
                        ->where('id', 12)
                        ->update([
                            'code' => 730
                        ]);
                            
            
            DB::connection('system')->commit();
            DB::connection('tenant')->commit();
        }
        catch (\Exception $e) {
            DB::connection('system')->rollBack();
            DB::connection('tenant')->rollBack();
            
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
        
        // Switch
        $tenancy = app(Environment::class);
        $tenancy->tenant(app(\Hyn\Tenancy\Environment::class)->website());
        
        config(['database.default' => 'system']);
        
        //dispatch((new ConfigureTenantJob)->onTenant($website->id)); ya no estara en cola
        
        return [
            'message' => "Se registro con éxito la compañía {$company->name}.",
            'company' => $company,
            'success' => true
        ];
    }
    
    /**
     * All
     * @return \Illuminate\Http\Response
     */
    public function all() {
        return  [
                    'company' =>  Company::all(),
                    'servicecompany' => ServiceCompany::all()
                ];
    }
    
    /**
     * Update
     * @param  \App\Models\System\Company $company
     * @param  \App\Http\Requests\System\CompanyUpdateRequest $request
     * @return \Illuminate\Http\Response
     */
    public function update(Company $company, CompanyUpdateRequest $request) {
        $company->update([
            'limit_documents' => $request->limit_documents,
            'economic_activity_code' => $request->economic_activity_code,
            'ica_rate' => $request->ica_rate

        ]);

        ServiceCompany::find($request->id_service)
        ->update(
            [
                'type_document_identification_id' => $request->type_document_identification_id,
                'department_id' => $request->department_id,
                'type_organization_id' => $request->type_organization_id,
                'type_regime_id' => $request->type_regime_id,
                'municipality_id' => $request->municipality_id,
                'merchant_registration' => $request->merchant_registration,
                'address' => $request->address,
                'phone' => $request->phone,
            ]
        );

        
        app(Environment::class)
            ->tenant($company->hostname->website);
        
        TenantCompany::firstOrFail()
            ->update([
                'limit_documents' => $request->limit_documents,
                'economic_activity_code' => $request->economic_activity_code,
                'ica_rate' => $request->ica_rate
            ]);
        
        if ($request->password != null) {
            User::firstOrFail()
                ->update([
                    'password' => bcrypt($request->password),
                ]);
        }


        TenantServiceCompany::firstOrFail()
        ->update(
            [
                'type_document_identification_id' => $request->type_document_identification_id,
                'department_id' => $request->department_id,
                'type_organization_id' => $request->type_organization_id,
                'type_regime_id' => $request->type_regime_id,
                'municipality_id' => $request->municipality_id,
                'merchant_registration' => $request->merchant_registration,
                'address' => $request->address,
                'phone' => $request->phone,
            ]
        );
        

        return [
            'message' => "Se actualizo con éxito la compañía {$company->name}.",
            'success' => true
        ];
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Company $company) {
        $hostname = Hostname::findOrFail($company->hostname_id);
        $website = Website::findOrFail($hostname->website_id);
        
        app(HostnameRepository::class)
            ->delete($hostname, true);
        
        app(WebsiteRepository::class)
            ->delete($website, true);
        
        DB::table('service_companies')->where('identification_number', $company->identification_number)->delete();
        Company::destroy($company->id);

        $this->deleteApi($company);

        return [
            'success' => true,
            'message' => "Se elimino compañía {$company->name}."
        ];
    }

    public function deleteApi($company)
    {
        $base_url = env("SERVICE_FACT", "");
        $number = $company->identification_number;
        $email = $company->email;
        $ch = curl_init("{$base_url}ubl2.1/config/delete/{$number}/{$email}");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Accept: application/json',
        ));
        $response = curl_exec($ch);
        curl_close($ch);
        $respuesta = json_decode($response);
    }

    public function tables()
    {

        $id_country = 46; //colombia
        $department = ServiceDepartment::where('country_id', $id_country)->get();
        return [
            //'country' => ServiceCountry::all(),
            'deparments' => $department,
            'municipalities' => ServiceMunicipality::whereIn('department_id',  $department->pluck('id'))->get(),
         //   'language' => ServiceLanguage::all(),
          //  'tax' => ServiceTax::all(),
           // 'type_enviroment' => ServiceTypeEnvironment::all(),
          //  'type_operation' => ServiceTypeOperation::all(),
            'type_documentation_identification' => ServiceTypeDocumentIdentification::all(),
          //  'type_currency' => ServiceTypeCurrency::all(),
            'type_organization' => ServiceTypeOrganization::all(),
            'type_regime' => ServiceTypeRegime::all(),
          //  'type_liability' => ServiceTypeLiability::all()
          
        ];
    }
    public function cascade(Request $request)
    {
      $name = $request->name;
      $value = $request->value;
      $data = [];

      switch ($name) {
          case 'country':
              $data = ServiceDepartment::where('country_id', $value)->get();
              break;
          case 'department':
              $data = ServiceMunicipality::where('department_id', $value)->get();
              break;
      }

      return $data;
    }

    public function getInformationDocument($nit, $desde = NULL, $hasta = NULL)
    {
        $base_url = env("SERVICE_FACT", "");
        $ch2 = curl_init("{$base_url}information/{$nit}/{$desde}/{$hasta}");

        curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch2, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch2, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Accept: application/json',
        ));
        $response_data = curl_exec($ch2);
        $err = curl_error($ch2);
        curl_close($ch2);
       // $response_encode = json_decode($response_data);
        if($err){
            return [
                'success' => false,
                'message'=> 'Error en Api'
            ];
        }
        else{
            return $response_data;
        }

    }
}
