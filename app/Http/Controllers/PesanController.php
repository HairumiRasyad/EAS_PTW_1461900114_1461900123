<?php

namespace App\Http\Controllers;
use App\Barang;
use App\Pesanan;
use App\User;
use App\DetailPesanan;
use Auth;
use Alert;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PesanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index($id)
    {
        $barang = Barang::where('id', $id)->first();

        return view('pesan.index', compact('barang'));
    }

    public function pesan(Request $request, $id)
    {   
		$barang = Barang::where('id', $id)->first();
		$tanggal = Carbon::now();
		
		//validasi apakah melebihi stok
		if ($request->jumlah_pesan > $barang->stok)
		{
			return redirect ('pesan/', $id);
		}
		//cek validasi
		$cek_pesanan = Pesanan::where('user_id', Auth::user()->id)->where('status',0)->first();
		//simpan ke database pesanan
		if(empty($cek_pesanan))
		{
			$pesanan = new Pesanan;
			$pesanan->user_id = Auth::user()->id;
			$pesanan->tanggal = $tanggal;
			$pesanan->status = 0;
			$pesanan->jumlah_harga=0;
			$pesanan->save();
		}
		//simpan ke detail pesanan
		$pesanan_baru = Pesanan::where('user_id', Auth::user()->id)->where('status',0)->first();
		
		//cek detail pesanan
		$cek_detail_pesanan = DetailPesanan::where('barang_id', $barang->id)->where('pesanan_id', 
		$pesanan_baru->id)->first();
		if(empty($cek_detail_pesanan))
		{
			$detail_pesanan=new DetailPesanan;
			$detail_pesanan->barang_id = $barang->id;
			$detail_pesanan->pesanan_id = $pesanan_baru->id;
			$detail_pesanan->jumlah = $request->jumlah_pesan;
			$detail_pesanan->jumlah_harga = $barang->harga*$request->jumlah_pesan;
			$detail_pesanan->save();
		}else
		{
			$detail_pesanan = DetailPesanan::where('barang_id', $barang->id)->where('pesanan_id', 
			$pesanan_baru->id)->first();
			$detail_pesanan->jumlah = $detail_pesanan->jumlah+$request->jumlah_pesan;
			
			//harga sekarang
			$harga_detail_pesanan_baru = $barang->harga*$request->jumlah_pesan;
			$detail_pesanan->jumlah_harga = $detail_pesanan->jumlah_harga+$harga_detail_pesanan_baru;
			$detail_pesanan->update();
		}
		//jumlah total
		$pesanan = $pesanan_baru = Pesanan::where('user_id', Auth::user()->id)->where('status',0)->first();
		$pesanan->jumlah_harga = $pesanan->jumlah_harga+$barang->harga*$request->jumlah_pesan;
		$pesanan->update();
		Alert::success('Pesanan Telah Masuk Keranjang', 'Success');
		return redirect('check-out');
		
		}
		
		public function check_out()
		{
        $pesanan = Pesanan::where('user_id', Auth::user()->id)->where('status',0)->first();	
        $detail_pesanan = [];
		 if(!empty($pesanan))
		 {
			 $detail_pesanan = DetailPesanan::where('pesanan_id', $pesanan->id)->get();
			 
		 }
		
		return view('pesan.check_out', compact('pesanan', 'detail_pesanan'));
		}
		
		 public function delete($id)
		 {
			$detail_pesanan = DetailPesanan::where('id', $id)->first();

			$pesanan = Pesanan::where('id', $detail_pesanan->pesanan_id)->first();
			$pesanan->jumlah_harga = $pesanan->jumlah_harga-$detail_pesanan->jumlah_harga;
			$pesanan->update();


			$detail_pesanan->delete();

			Alert::error('Pesanan Sukses Dihapus', 'Hapus');
			
			return redirect('check-out');
			}
			
		public function konfirmasi()
		
		{
			$user = User::where('id', Auth::user()->id)->first();
			
			if(empty($user->alamat))
			{
				Alert::error('Identitasi Harap dilengkapi', 'Error');
				return redirect('profile');
			}

			if(empty($user->nohp))
			{
				Alert::error('Identitasi Harap dilengkapi', 'Error');
				return redirect('profile');
			}

			$pesanan = Pesanan::where('user_id', Auth::user()->id)->where('status',0)->first();
			$pesanan_id = $pesanan->id;
			$pesanan->status = 1;
			$pesanan->update();

			$detail_pesanan = DetailPesanan::where('pesanan_id', $pesanan_id)->get();
			foreach ($detail_pesanan as $detail_pesanans) {
            $barang = Barang::where('id', $detail_pesanans->barang_id)->first();
            $barang->stok = $barang->stok-$detail_pesanans->jumlah;
            $barang->update();
			
			}
			
			Alert::success('Pesanan Sukses Check Out Silahkan Lanjutkan Proses Pembayaran', 'Success');
			return redirect('history/'.$pesanan_id);

    }

	
	
		
} 