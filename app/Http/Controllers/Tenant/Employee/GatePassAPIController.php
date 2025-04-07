<?php


namespace App\Http\Controllers\Tenant\Employee;


use App\Http\Controllers\Controller;
use App\Models\Tenant\Employee\GatePass;
use App\Models\Tenant\Employee\GatePassUser;
use App\Models\Tenant\Employee\Helmet;
use App\Models\Tenant\Employee\HelmetUser;
use http\Env\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use phpDocumentor\Reflection\Types\Null_;

class GatePassAPIController extends Controller
{
    public function index()
    {
        return GatePass::select('id','name')->get();
    }
    public function index_free()
    {
        $used_gate_passes=GatePassUser::whereNull('end_date')->pluck('gate_pass_id ')->all();
                Log::info($used_gate_passes);


        return GatePass::select('id','name')->whereNotIn('id',$used_gate_passes)->get();

//        return Helmet::select('id','imei')->whereNotIn(HelmetUser::select('helmet_id')->whereNotNull('end_date')->get())->get();
//        return Helmet::select('id','imei')->whereNotIn('id',$used_helmets)->get();


    }
    public function release($gate_pass)
    {
        $gate_pass1 = GatePassUser::where('gate_pass_id',$gate_pass)->where('end_date',null)->get();
        Log::info('Gate Pass detached:=> '.$gate_pass1.' //boudgeau');

        // HelmetUser::where('helmet_id',$helmet)->where('end_date',null)->update(['end_date',today()]);
//        $helmets->end_date = today();
//        $helmets->save();
        DB::table('gate_pass_user')
            ->where('gate_pass_id', $gate_pass)
            ->where('end_date',null)
            ->update(['end_date' => today()]);



        return detached_response('GatePass');
    }
}
