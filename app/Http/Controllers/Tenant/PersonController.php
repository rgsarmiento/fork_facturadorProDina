<?php
namespace App\Http\Controllers\Tenant;

use App\Http\Requests\Tenant\PersonRequest;
use App\Http\Resources\Tenant\PersonCollection;
use App\Http\Resources\Tenant\PersonResource;
use App\Imports\PersonsImport;
use App\Models\Tenant\Catalogs\Country;
use App\Models\Tenant\Catalogs\Department;
use App\Models\Tenant\Catalogs\District;
use App\Models\Tenant\Catalogs\IdentityDocumentType;
use App\Models\Tenant\Catalogs\Province;
use App\Http\Controllers\Controller;
use App\Models\Tenant\Person;
use App\Models\Tenant\PersonType;
use Exception;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Excel;

use Modules\Factcolombia1\Models\Tenant\{
    TypeIdentityDocument,
    TypePerson,
    TypeRegime,
    Country as CoCountry,
};
use App\Exports\PersonExport;
use Carbon\Carbon;


class PersonController extends Controller
{
    public function index($type)
    {
        $api_service_token = config('configuration.api_service_token');
        return view('tenant.persons.index', compact('type','api_service_token'));
    }

    public function columns()
    {
        return [
            'name' => 'Nombre',
            'number' => 'Número'
        ];
    }

    public function records($type, Request $request)
    {
      //  return 'sd';
        $records = Person::where($request->column, 'like', "%{$request->value}%")
                            ->where('type', $type)
                            ->orderBy('name');

        return new PersonCollection($records->paginate(config('tenant.items_per_page')));
    }

    public function create()
    {
        return view('tenant.customers.form');
    }

    public function tables()
    {
        // $countries = Country::whereActive()->orderByDescription()->get();
        // $departments = Department::whereActive()->orderByDescription()->get();
        // $provinces = Province::whereActive()->orderByDescription()->get();
        // $districts = District::whereActive()->orderByDescription()->get();
        $identity_document_types = IdentityDocumentType::whereActive()->get();
        $person_types = PersonType::get();
        // $locations = $this->getLocationCascade();
        $api_service_token = config('configuration.api_service_token');

        $typeIdentityDocuments = TypeIdentityDocument::all();
        $typeRegimes = TypeRegime::all();
        $typePeople = TypePerson::all();
        $countries = CoCountry::all();

        return compact('countries', 'identity_document_types','person_types','api_service_token', 'typeIdentityDocuments', 'typeRegimes',
                        'typePeople');
    }

    public function record($id)
    {
        $record = new PersonResource(Person::findOrFail($id));

        return $record;
    }

    public function store(PersonRequest $request)
    {

        if($request->state){
            if($request->state != "ACTIVO"){
                return [
                    'success' => false,
                    'message' =>'El estado del contribuyente no es activo, no puede registrarlo',
                ];
            }
        }

        $id = $request->input('id');
        $person = Person::firstOrNew(['id' => $id]);
        $person->fill($request->all());
        $person->save();

        $person->addresses()->delete();
        $addresses = $request->input('addresses');
        foreach ($addresses as $row)
        {
            $person->addresses()->updateOrCreate( ['id' => $row['id']], $row);
        }

        $person_type = ($person->type == 'customers') ? 'Cliente':'Proveedor';
        
        return [
            'success' => true,
            'message' => ($id)? "{$person_type} editado con éxito":"{$person_type} registrado con éxito",
            'id' => $person->id
        ];
    }

    public function destroy($id)
    {
        try {

            $person = Person::findOrFail($id);
            $person_type = ($person->type == 'customers') ? 'Cliente':'Proveedor';
            $person->delete();

            return [
                'success' => true,
                'message' => $person_type.' eliminado con éxito'
            ];

        } catch (Exception $e) {

            return ($e->getCode() == '23000') ? ['success' => false,'message' => "El {$person_type} esta siendo usado por otros registros, no puede eliminar"] : ['success' => false,'message' => "Error inesperado, no se pudo eliminar el {$person_type}"];

        }

    }

    public function import(Request $request)
    {
        if ($request->hasFile('file')) {
            try {
                $import = new PersonsImport();
                $import->import($request->file('file'), null, Excel::XLSX);
                $data = $import->getData();
                return [
                    'success' => true,
                    'message' =>  __('app.actions.upload.success'),
                    'data' => $data
                ];
            } catch (Exception $e) {
                return [
                    'success' => false,
                    'message' =>  $e->getMessage()
                ];
            }
        }
        return [
            'success' => false,
            'message' =>  __('app.actions.upload.error'),
        ];
    }

    public function getLocationCascade()
    {
        $locations = [];
        $departments = Department::where('active', true)->get();
        foreach ($departments as $department)
        {
            $children_provinces = [];
            foreach ($department->provinces as $province)
            {
                $children_districts = [];
                foreach ($province->districts as $district)
                {
                    $children_districts[] = [
                        'value' => $district->id,
                        'label' => $district->description
                    ];
                }
                $children_provinces[] = [
                    'value' => $province->id,
                    'label' => $province->description,
                    'children' => $children_districts
                ];
            }
            $locations[] = [
                'value' => $department->id,
                'label' => $department->description,
                'children' => $children_provinces
            ];
        }

        return $locations;
    }

    
    public function enabled($type, $id)
    {

        $person = Person::findOrFail($id);
        $person->enabled = $type;
        $person->save();

        $type_message = ($type) ? 'habilitado':'inhabilitado';

        return [
            'success' => true,
            'message' => "Cliente {$type_message} con éxito"
        ];

    }

    
    public function coExport($type)
    {
        $records = Person::where('type', $type)
                            ->get();

        $name = $type == "customers" ? "Clientes":"Proveedores";

        return (new PersonExport)
                ->records($records)
                ->download($name.Carbon::now().'.xlsx');

    }

}
