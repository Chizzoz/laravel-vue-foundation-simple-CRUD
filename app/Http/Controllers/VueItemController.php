<?php

namespace App\Http\Controllers;

use App\Item;
use Illuminate\Http\Request;

class VueItemController extends Controller
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
     * Manage vue items.
     *
     * @return \Illuminate\Http\Response
     */
    public function manageVue()
    {
        return view('manage-vue');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $items = Item::latest()->paginate(5);
		
		$response = [
			'pagination' => [
				'total' => $items->total(),
				'per_page' => $items->perPage(),
				'current_page' => $items->currentPage(),
				'last_page' => $items->lastPage(),
				'from' => $items->firstItem(),
				'to' => $items->lastItem(),
			],
			'data' => $items
		];
		
		return response()->json($response);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
			'title' => 'required',
			'description' => 'required',
		]);
		
		$create = Item::create($request->all());
		
		return response()->json($create);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $item)
    {
        $this->validate($request, [
			'title' => 'required',
			'description' => 'required',
		]);
		
		$edit = Item::find($item)->update($request->all());
		
		return response()->json($edit);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function destroy($item)
    {
        Item::find($item)->delete();
		return response()->json(['done']);
    }
}
