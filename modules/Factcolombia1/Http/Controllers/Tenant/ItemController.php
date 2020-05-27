<?php

namespace Modules\Factcolombia1\Http\Controllers\Tenant;

use Modules\Factcolombia1\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Factcolombia1\Imports\Tenant\ItemsImport;
use Modules\Factcolombia1\Http\Requests\Tenant\{
    ItemImportRequest,
    ItemUpdateRequest,
    ItemRequest
};
use Illuminate\Http\Request;
use Modules\Factcolombia1\Exports\Tenant\{
    ItemsFormatExport,
    ItemsExport
};
use Modules\Factcolombia1\Models\Tenant\{
    TypeUnit,
    Item,
    Tax
};

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return view('item.tenant.index');
    }
    
    /**
     * All
     * @return \Illuminate\Http\Response
     */
    public function all() {
        return [
            'typeUnits' => TypeUnit::all(),
            'taxes' => Tax::query()
                ->where('is_retention', false)
                ->get(),
            'items' => Item::query()
                ->with('typeUnit', 'tax')
                ->get()
        ];
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Tenant\ItemRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ItemRequest $request) {
        $item = Item::create([
            'name' => mb_strtoupper($request->name),
            'code' => mb_strtoupper($request->code),
            'type_unit_id' => $request->type_unit_id,
            'price' => $request->price,
            'tax_id' => $request->tax_id
        ]);
        
        return [
            'success' => true,
            'message' => "Se registro con éxito el producto {$item->name}."
        ];
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Tenant\ItemUpdateRequest  $request
     * @param  \App\Models\Tenant\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function update(ItemUpdateRequest $request, Item $item) {
        $item->name = mb_strtoupper($request->name);
        $item->code = mb_strtoupper($request->code);
        $item->type_unit_id = $request->type_unit_id;
        $item->price = $request->price;
        $item->tax_id = $request->tax_id;
        
        $item->save();
        
        return [
            'success' => true,
            'message' => "Se actualizo con éxito el producto {$item->name}."
        ];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Tenant\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function destroy(Item $item) {
        $item->forceDelete();
        
        return [
            'success' => true,
            'message' => "Se elimino con éxito el producto {$item->name}."
        ];
    }
    
    /**
     * Format import
     * @return \Illuminate\Http\Response
     */
    public function formatImport() {
        return Excel::download(new ItemsFormatExport, 'Formato productos.xlsx');
    }
    
    /**
     * Import
     * @param  \App\Http\Requests\Tenant\ItemImportRequest $request
     * @return \Illuminate\Http\Response
     */
    public function import(ItemImportRequest $request) {
        try {
            if ($request->hasFile('file')) Excel::import(new ItemsImport, $request->file('file'));
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
        return Excel::download(new ItemsExport, 'productos.xlsx');
    }
}
