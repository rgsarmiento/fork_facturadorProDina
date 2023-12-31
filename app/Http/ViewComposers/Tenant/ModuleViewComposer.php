<?php

namespace App\Http\ViewComposers\Tenant;

use App\Models\Tenant\Module;

class ModuleViewComposer
{
    public function compose($view)
    {
        $modules = auth()->user()->modules()->pluck('value')->toArray();
//        if (is_object(auth()->user())) {
//            $modelName = get_class(auth()->user());
//            \Log::debug($modelName);
//        }
        if(count($modules) > 0) {
            $view->vc_modules = $modules;
        } else {
            $view->vc_modules = Module::all()->pluck('value')->toArray();
        }
    }
}
