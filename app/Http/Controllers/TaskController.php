<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Task;
use App\User;
use Validator;

class TaskController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getTasks()
    {
        try{
            
            $tasks = Task::where("user_id", auth()->user()->id)->get();
            return response()->json($tasks);
        } catch (\Exception $e) {

            return response()->json(["error" => $e->getMessage()]);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(),[
                'name' => 'required|string',
                'description' => 'required|string',
                'status' => 'required',
            ]);
    
            if ($validator->fails()) {
    
                return response()->json(["status" => 0, "errors" => $validator->errors()], 400);
            }else{
    
                $task = new Task();
                $task->name = $request->name;
                $task->description = $request->description;
                $task->status = $request->status;
                $task->user_id = auth()->user()->id;
                $task->save();

                return response()->json(["status" => 1, "task" => $task]);
            }
          
        } catch (\Exception $e) {

            return response()->json(["status" => 1, "error" => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {

            $validator = Validator::make($request->all(),[
                'name' => 'required|string',
                'description' => 'required|string',
                'status' => 'required',
            ]);
    
            if ($validator->fails()) {
    
                return response()->json(["status" => 0, "errors" => $validator->errors()], 400);
            }else{

                $task = Task::find($id);
                $task->name = $request->name;
                $task->description = $request->description;
                $task->status = $request->status;
                $task->user_id = auth()->user()->id;
                $task->save();
        
                return response()->json(["status" => 1, "task" => $task]);
            }

        } catch (\Exception $e) {
          
            return response()->json(["error" => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{

            $task = Task::find($id);
            $task->delete();
    
            return response()->json(["status" => 1, "task" => $task]);
        } catch (\Exception $e) {
            
            return response()->json(["error" => $e->getMessage()]);
        }
    }
}
