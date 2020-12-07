<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Http\Requests;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return response()->json(
            Client::where('first_name', 'LIKE', $request->q.'%')
                ->orWhere('last_name', 'LIKE', $request->q."%")
                ->orWhere('email', 'LIKE', $request->q.'%')
                ->orderBy('id', 'DESC')
                ->paginate('10')
        );

        // return response()->json(Client::orderBy('id', 'DESC')->paginate('10'));
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
        //add or update
        $put = str_contains($request->getPathInfo(), 'clients');
        $client = $put ? Client::findOrFail($request->client_id) : new Client();

        //if new entry, validate
        if(!$put){
            $request->validate([
                'first_name' => 'required',
                'last_name' => 'required',
                'avatar' => 'required|dimensions:min_width=100,min_height=100",',
                'email' => 'required' 
            ]);
        }
        $avatarUrl = !$put || (!str_contains($request->avatar, 'storage') && !str_contains($request->avatar, 'http')) ? Client::SaveAvatar($request) : $client->avatar;
        $client->first_name = $request->first_name;
        $client->last_name = $request->last_name;
        $client->email = $request->email;
        $client->avatar = $avatarUrl;
        $client = $client->save();

        return response()->json($client);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function destroy(Client $client, $id)
    {
        Client::find($id)->delete();
        return 1;
    }
}
