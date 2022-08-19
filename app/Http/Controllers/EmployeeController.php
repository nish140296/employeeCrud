<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['employees'] = Employee::orderBy('id','desc')->paginate(5);
        
        return view('employees.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('employees.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'employee_name' => 'required',
            'address' => 'required',
            'email_address' => 'required|email|unique:employees',
            'phone' => 'required|numeric',
            'date_of_birth' => 'required|date|before:-18 years',
            'employee_image' => 'required|image|mimes:jpg,png,jpeg,gif,svg|max:2048'
        ],[
            'employee_name.required'    => 'Employee name is required',
            'address.required'          => 'Address is required',
            'email_address.required'    => 'Email address is required',
            'phone.required'            => 'Phone number is required',
            'date_of_birth.required'    => 'Birth date is required',
            'date_of_birth.before'      => 'Birth date should be enter before 18 years',
        ]);
        $path = $request->file('employee_image')->store('public/employee_images');
        $employee = new Employee;
        $employee->employee_name = $request->employee_name;
        $employee->address = $request->address;
        $employee->email_address = $request->email_address;
        $employee->phone = $request->phone;
        $employee->date_of_birth = $request->date_of_birth;
        $employee->employee_image = $path;
        $employee->save();

        return redirect()->route('employees.index')
                        ->with('success','Employee has been created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function show(Employee $employee)
    {
        return view('employees.show',compact('employee'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function edit(Employee $employee)
    {
        return view('employees.edit',compact('employee'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Employee $employee)
    {
        $request->validate([
            'employee_name' => 'required',
            'address' => 'required',
            'email_address' => 'required|email|unique:employees,email_address,'.$employee->id,
            'phone' => 'required|numeric',
            'date_of_birth' => 'required|date|before:-18 years',
        ],[
            'employee_name.required'    => 'Employee name is required',
            'address.required'          => 'Address is required',
            'email_address.required'    => 'Email address is required',
            'phone.required'            => 'Phone number is required',
            'date_of_birth.required'    => 'Birth date is required',
            'date_of_birth.before'      => 'Birth date should be enter before 18 years',
        ]);
        
        $employeeDetail = Employee::find($employee->id);
        if($request->hasFile('employee_image')){
            $request->validate([
              'employee_image' => 'required|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
            ]);
            $path = $request->file('employee_image')->store('public/employee_images');
            $employeeDetail->employee_image = $path;
        }
        $employeeDetail->employee_name = $request->employee_name;
        $employeeDetail->address = $request->address;
        $employeeDetail->email_address = $request->email_address;
        $employeeDetail->phone = $request->phone;
        $employeeDetail->date_of_birth = $request->date_of_birth;
        $employeeDetail->save();

        return redirect()->route('employees.index')
                        ->with('success','Employee updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function destroy(Employee $employee)
    {
        $employee->delete();
    
        return redirect()->route('employees.index')
                        ->with('success','Employee has been deleted successfully');
    }
}
