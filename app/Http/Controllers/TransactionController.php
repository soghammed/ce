<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Client;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(Transaction::orderBy('id', 'DESC')->paginate('10'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $put = str_contains($request->getPathInfo(), 'transactions');
        $transaction = $put ? Transaction::findOrFail($request->transaction_id) : new Transaction();

        //if new entry, validate
        if(!$put){
            $request->validate([
                'client' => 'required',
                'transaction_date' => 'required',
                'amount' => 'required'
            ]);
        }
        $transaction->client = $request->client;
        $transaction->transaction_date = $request->transaction_date;
        $transaction->amount = $request->amount;
        $transaction = $transaction->save();
        return response()->json($transaction);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function destroy(Transaction $transaction, $id)
    {
        Transaction::find($id)->delete();
        return 1;
    }

    public function clients()
    {
        // dd(Client::all());
        return response()->json(Client::orderBy('first_name')->get());
    }
}
