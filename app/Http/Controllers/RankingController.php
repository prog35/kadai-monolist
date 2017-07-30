<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Item;

class RankingController extends Controller
{
    public function want()
    {
        $items = \DB::table('item_user')
            ->join('items', 'item_user.item_id', '=', 'items.id')
            ->select('items.*', \DB::raw('COUNT(*) as count'))
            ->where('type', 'want')
            ->groupby('items.id')
            ->orderby('count', 'DESC')
            ->take(10)
            ->get();
        
        return view('ranking.want', [
            'items' => $items,
        ]);
    }
    
    
    public function Have()
    {
        $items = \DB::table('item_user')
            ->join('items', 'item_user.item_id', '=', 'items.id')
            ->select('items.*', \DB::raw('COUNT(*) as count'))
            ->where('type', 'have')
            ->groupby('items.id')
            ->orderby('count', 'DESC')
            ->take(10)
            ->get();
        
        return view('ranking.have', [
            'items' => $items,
        ]);
    }
    
}
