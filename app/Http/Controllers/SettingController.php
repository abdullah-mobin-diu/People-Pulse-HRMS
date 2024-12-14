<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use App\Models\CompanySettings;
use App\Models\RolesPermissions;
use Brian2694\Toastr\Facades\Toastr;
class SettingController extends Controller
{
    /** company/settings/page */
    public function companySettings()
    {
        $companySettings = CompanySettings::where('id',1)->first();
        return view('settings.companysettings',compact('companySettings'));
    }

    /** save record company settings */
    public function saveRecord(Request $request)
    {
        /** validate form */
        $request->validate([
            'company_name'   =>'required',
            'contact_person' =>'required',
            'address'        =>'required',
            'country'        =>'required',
            'city'           =>'required',
            'state_province' =>'required',
            'postal_code'    =>'required',
            'email'          =>'required',
            'phone_number'   =>'required',
            'mobile_number'  =>'required',
            'fax'            =>'required',
            'website_url'    =>'required',
        ]);

        try {
            
            /** save or update to databases CompanySettings table */
            $saveRecord = CompanySettings::updateOrCreate(['id' => $request->id]);
            $saveRecord->company_name   = $request->company_name;
            $saveRecord->contact_person = $request->contact_person;
            $saveRecord->address        = $request->address;
            $saveRecord->country        = $request->country;
            $saveRecord->city           = $request->city;
            $saveRecord->state_province = $request->state_province;
            $saveRecord->postal_code    = $request->postal_code;
            $saveRecord->email          = $request->email;
            $saveRecord->phone_number   = $request->phone_number;
            $saveRecord->mobile_number  = $request->mobile_number;
            $saveRecord->fax            = $request->fax;
            $saveRecord->website_url    = $request->website_url;
            $saveRecord->save();
            
            DB::commit();
            Toastr::success('Save CompanySettings successfully :)','Success');
            return redirect()->back();
        } catch(\Exception $e) {
            \Log::info($e);
            DB::rollback();
            Toastr::error('Save CompanySettings fail :)','Error');
            return redirect()->back();
        }
    }
    
    /** Roles & Permissions  */
    public function rolesPermissions()
    {
        $rolesPermissions = RolesPermissions::All();
        return view('settings.rolespermissions',compact('rolesPermissions'));
    }

    /** add role permissions */
    public function addRecord(Request $request)
    {
        $request->validate([
            'roleName' => 'required|string|max:255',
        ]);
        
        DB::beginTransaction();
        try{

            $roles = RolesPermissions::where('permissions_name', '=', $request->roleName)->first();
            if ($roles === null)
            {
                // roles name doesn't exist
                $role = new RolesPermissions;
                $role->permissions_name = $request->roleName;
                $role->save();
            }else{

                // roles name exits
                DB::rollback();
                Toastr::error('Roles name exits :)','Error');
                return redirect()->back();
            }

            DB::commit();
            Toastr::success('Create new role successfully :)','Success');
            return redirect()->back();
        }catch(\Exception $e){
            DB::rollback();
            Toastr::error('Add Role fail :)','Error');
            return redirect()->back();
        }
    }

    /** edit roles permissions */
    public function editRolesPermissions(Request $request)
    {
        DB::beginTransaction();
        try{
            $id        = $request->id;
            $roleName  = $request->roleName;
            
            $update = [
                'id'               => $id,
                'permissions_name' => $roleName,
            ];

            RolesPermissions::where('id',$id)->update($update);
            DB::commit();
            Toastr::success('Role Name updated successfully :)','Success');
            return redirect()->back();

        }catch(\Exception $e){
            DB::rollback();
            Toastr::error('Role Name update fail :)','Error');
            return redirect()->back();
        }
    }

    /** delete roles permissions */
    public function deleteRolesPermissions(Request $request)
    {
        try{
            RolesPermissions::destroy($request->id);
            Toastr::success('Role Name deleted successfully :)','Success');
            return redirect()->back();
        
        }catch(\Exception $e){
            DB::rollback();
            Toastr::error('Role Name delete fail :)','Error');
            return redirect()->back();
        }
    }

    /** localization */
    public function localizationIndex()
    {
        return view('settings.localization');
    }

    /** salary settings */
    public function salarySettingsIndex()
    {
        return view('settings.salary-settings');
    }

    /** email Settings */
    public function emailSettingsIndex()
    {
        return view('settings.email-settings');
    }
}
