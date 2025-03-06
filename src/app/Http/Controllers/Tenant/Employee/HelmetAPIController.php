<?php


namespace App\Http\Controllers\Tenant\Employee;


use App\Http\Controllers\Controller;
use App\Models\Tenant\Employee\Helmet;
use App\Models\Tenant\Employee\HelmetUser;
use http\Env\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use phpDocumentor\Reflection\Types\Null_;

class HelmetAPIController extends Controller
{
    public function index()
    {
        return Helmet::select('id','imei','pme_barcode')->whereNotNull('pme_barcode')->get();
    }
    public function index_free()
    {
        $used_helmets=HelmetUser::whereNull('end_date')->pluck('helmet_id')->all();
        Log::info($used_helmets);


        return Helmet::select('id','imei','pme_barcode')->whereNotNull('pme_barcode')->whereNotIn('id',$used_helmets)->get();

//        return Helmet::select('id','imei')->whereNotIn(HelmetUser::select('helmet_id')->whereNotNull('end_date')->get())->get();
//        return Helmet::select('id','imei')->whereNotIn('id',$used_helmets)->get();


    }
    public function release($helmet)
    {
        $helmets1 = HelmetUser::where('helmet_id',$helmet)->where('end_date',null)->get();
        Log::info('Helmets detached:=> '.$helmets1.' //boudgeau');

        // HelmetUser::where('helmet_id',$helmet)->where('end_date',null)->update(['end_date',today()]);
//        $helmets->end_date = today();
//        $helmets->save();
        DB::table('helmet_user')
            ->where('helmet_id', $helmet)
            ->where('end_date',null)
            ->update(['end_date' => today()]);



        return detached_response('helmet');
    }
}
