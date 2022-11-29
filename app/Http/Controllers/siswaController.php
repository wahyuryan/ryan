<?php

namespace App\Http\Controllers;

use Session;
use File;
use App\Models\siswa;
use Illuminate\Http\Request;

class siswaController extends Controller
{

    public function __construct()
    {
        $this->middleware('admin')->except(['show','index']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Siswa::all();
        return view('mastersiswa', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('tambahsiswa');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $message=[
          'required' => ':attribute harus ada isinya!',
          'min'      => ':attribute minimal harus :min',
          'max'      => ':attribute maksimal harus :max',
          'numeric'  => ':attribute ini harus Angka saja',
          'mimes'    => ':attribute ini harus png atau jpg'
        ];
        $this->validate($request,[
            'nama'   => 'required|min:3|max:30',
            'nisn'   => 'required|numeric',
            'alamat' => 'required',
            'jk'     => 'required',
            'foto'   => 'required|mimes:jpg,png',
            'about'  => 'required|min:10'
        ], $message);

        //ambil parameter
        $file = $request->file('foto');

        //rename
        $nama_file = time()."_".$file->getClientOriginalName();

        //proses upload
        $tujuan_upload = './template/img';
        $file->move($tujuan_upload,$nama_file);

        //insert data
        Siswa::create([
            'nama'   => $request -> nama,
            'nisn'   => $request -> nisn,
            'alamat' => $request -> alamat,
            'jk'     => $request -> jk,
            'foto'   => $nama_file,
            'about'  => $request -> about,
        ]);

        session::flash('success', 'Data berhasil di tambahkan');
        return redirect('/mastersiswa');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $siswa=Siswa::find($id);
        $kontaks = $siswa->kontak()->get();
        // return($kontak);
        return view('showsiswa', compact('siswa','kontaks'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $siswa=Siswa::find($id);
        return view('editsiswa', compact('siswa'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $message=[
          'required' => ':attribute harus ada isinya!',
          'min'      => ':attribute minimal harus :min',
          'max'      => ':attribute maksimal harus :max',
          'numeric'  => ':attribute ini harus Angka saja',
          'mimes'    => ':attribute ini harus png atau jpg'
          ];
          $this->validate($request,[
              'nama'   => 'required|min:3|max:30',
              'nisn'   => 'required|numeric',
              'alamat' => 'required',
              'jk'     => 'required',
              'foto'   => 'required|mimes:jpg,png',
              'about'  => 'required|min:5'
          ], $message);
          
        if ($request->foto !='') {

        //1.Hapus anjir foto lama
        $siswa=Siswa::find($id);
        file::delete('./template/img/'. $siswa->foto);

        //ambil parameter
        $file = $request->file('foto');

        //rename
        $nama_file = time()."_".$file->getClientOriginalName();

        //4.Proses upload anjir
        $tujuan_upload = './template/img';
        $file->move($tujuan_upload,$nama_file);

        //5.Menyimpan data ke database anjir
        $siswa->nama   = $request -> nama;
        $siswa->nisn   = $request -> nisn;
        $siswa->alamat = $request -> alamat;
        $siswa->jk     = $request -> jk;
        $siswa->foto   = $nama_file;
        $siswa->about  = $request -> about;
        $siswa->save();
        session::flash('success', 'Data berhasil di edit');
        return redirect('/mastersiswa');

        }else{
            $siswa=Siswa::find($id);
            $siswa->nama   = $request -> nama;
            $siswa->nisn   = $request -> nisn;
            $siswa->alamat = $request -> alamat;
            $siswa->jk     = $request -> jk;
            $siswa->about  = $request -> about;
            $siswa->save();
            session::flash('success', 'Data berhasil di hapus');
            return redirect('/mastersiswa');    
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
        //
    }

     /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function hapus($id)
    {
        $siswa=Siswa::find($id);
        $siswa->delete();
        Session::flash('success', 'Data Berhasil Dihapus');
        return redirect('/mastersiswa');
    }
}
