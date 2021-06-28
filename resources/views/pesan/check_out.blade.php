@extends('layouts.app')
@section('content')
<link href="http://maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <a href="{{ url('home') }}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Kembali</a>
        </div>
        <div class="col-md-12 mt-2">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('home') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Check Out</li>
                </ol>
            </nav>
        </div>
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h3><i class="fa fa-shopping-cart"></i> Check Out</h3>
                    @if(!empty($pesanan))
                    <p align="right">Tanggal Pesan : {{ $pesanan->tanggal }}</p>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Gambar</th>
                                <th>Nama Barang</th>
                                <th>Jumlah</th>
                                <th>Harga</th>
                                <th>Total Harga</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; ?>
                            @foreach($detail_pesanan as $detail_pesanan)
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>
                                    <img src="{{ url('images') }}/{{ $detail_pesanan->barang->gambar }}" width="100" alt="...">
                                </td>
                                <td>{{ $detail_pesanan->barang->nama_barang }}</td>
                                <td>{{ $detail_pesanan->jumlah }} pcs</td>
                                <td align="left">Rp. {{ number_format($detail_pesanan->barang->harga) }}</td>
                                <td align="left">Rp. {{ number_format($detail_pesanan->jumlah_harga) }}</td>
                                <td>
                                    <form action="{{ url('check-out') }}/{{ $detail_pesanan->id }}" method="post">
                                        @csrf
                                        {{ method_field('DELETE') }}
										
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Anda yakin akan menghapus data ?');"><i class="fa fa-trash"></i>Hapus</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                            <tr>
                                <td colspan="5" align="right"><strong>Total Harga :</strong></td>
                                <td align="right"><strong>Rp. {{ number_format($pesanan->jumlah_harga) }}</strong></td>
                                <td>
                                    <a href="{{ url('konfirmasi-check-out') }}" class="btn btn-success" onclick="return confirm('Anda yakin akan Check Out ?');">
                                        <i class="fa fa-shopping-cart"></i> Check Out
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    @endif
                </div>
            </div>
        </div>
        
    </div>
</div>
@endsection