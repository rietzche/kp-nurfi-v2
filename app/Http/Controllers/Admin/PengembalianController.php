<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller as BaseController;
use Illuminate\Http\Request;
use App\Acc;
use App\History;
use App\Peminjaman;
use App\Barang;
use Session;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Illuminate\Support\Facades\Auth;

class PengembalianController extends BaseController
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        $data = Acc::all();
        $now = Carbon::now('Asia/Jakarta');
        return view('admin.showpengembalian')->with('val', $data)
                                             ->with('now', $now);
    }

    public function kembali($id)
    {
        $x = Acc::find($id);
        $x->update([
            'activate' => 2,
            'by' => Auth::user()->name,
        ]);
        History::create([
            'kode' => Acc::find($id)->kode,
            'activate' => 2,
            'by' => Auth::user()->name,
        ]);
        $c = Peminjaman::all()->where('kode', $x->kode);
        foreach ($c as $d){
            $stok = Barang::find($d->barang_id)->stock;
            $newstok = $stok + $d->jumlah;
            Barang::find($d->barang_id)->update([
                'stock' => $newstok
            ]);
        }
        Session::flash('message', 'Pengembalian data berhasil!');
        return redirect()->route('admin/pengembalian');
    }
}