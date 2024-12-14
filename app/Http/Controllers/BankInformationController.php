<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use App\Models\BankInformation;

class BankInformationController extends Controller
{
    /** save record */
    public function saveRecord(Request $request)
    {
        $request->validate([
            'bank_name'       => 'required|string|max:255',
            'bank_account_no' => 'required|string|max:255',
            'ifsc_code'       => 'required|string|max:255',
            'pan_no'          => 'required|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            
            $bankInformation = BankInformation::firstOrNew(
                ['user_id' =>  $request->user_id],
            );
            $bankInformation->user_id         = $request->user_id;
            $bankInformation->bank_name       = $request->bank_name;
            $bankInformation->bank_account_no = $request->bank_account_no;
            $bankInformation->ifsc_code       = $request->ifsc_code;
            $bankInformation->pan_no          = $request->pan_no;
            $bankInformation->save();

            DB::commit();
            Toastr::success('Add bank information successfully :)','Success');
            return redirect()->back();
        } catch(\Exception $e) {
            DB::rollback();
            Toastr::error('Add bank information fail :)','Error');
            return redirect()->back();
        }
    }
}
