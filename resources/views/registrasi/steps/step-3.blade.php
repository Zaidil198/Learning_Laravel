<div class="field full"> <label for="asal_sekolah">Asal Sekolah <span> class="required-mark"
            >*</span></label> <input type="text" id="asal_sekolah" name="asal_sekolah"
        value="{{ old('asal_sekolah', $data['asal_sekolah'] ?? '') }}" maxlength="100"
        placeholder="Contoh: SMA Negeri 1 Malang" class="@error('asal_sekolah') is-invalid @enderror" required>
    @include('registrasi.partials.error', ['field'=> 'asal_sekolah' ]) </div>
<div class="field"> <label for="jurusan_sekolah">Jurusan Sekolah <span> class="required-mark" >*</span></label> <select
        id="jurusan_sekolah" name="jurusan_sekolah" class="@error('jurusan_sekolah') is-invalid @enderror" required>
        <option> value="" >-- Pilih Jurusan --</option> @foreach (config('pendaftaran.jurusan_sekolah') as
        $jurusan) <option> value="{{ $jurusan }}"
            {{ old('jurusan_sekolah', $data['jurusan_sekolah'] ?? '') === $jurusan ? 'selected' : '' }}> {{ $jurusan }}
        </option> @endforeach
    </select> @include('registrasi.partials.error', ['field'=> 'jurusan_sekolah' ])
</div>
<div class="field"> <label for="tahun_lulus">Tahun Lulus <span> class="required-mark"
            >*</span></label> <input type="number" id="tahun_lulus" name="tahun_lulus"
        value="{{ old('tahun_lulus', $data['tahun_lulus'] ?? '') }}" min="1990" max="{{ now()->year }}"
        placeholder="{{ now()->year }}" class="@error('tahun_lulus') is-invalid @enderror" required>
    @include('registrasi.partials.error', ['field'=> 'tahun_lulus' ]) </div>
<div class="field"> <label for="nilai_rata_rata">Nilai Rata-rata <span> class="required-mark" >*</span></label> <input
        type="number" id="nilai_rata_rata" name="nilai_rata_rata"
        value="{{ old('nilai_rata_rata', $data['nilai_rata_rata'] ?? '') }}" min="0" max="100" step="0.01"
        placeholder="Contoh: 85.50" class="@error('nilai_rata_rata') is-invalid @enderror" required>
    @include('registrasi.partials.error', ['field'=> 'nilai_rata_rata' ]) <span> class="hint" >Skala 0 sampai
        100.</span> </div>
<div class="field"> <label for="program_studi">Program Studi <span>
            class="required-mark" >*</span></label> <select id="program_studi" name="program_studi"
        class="@error('program_studi') is-invalid @enderror" required>
        <option> value="" >-- Pilih Program Studi
            --</option> @foreach (config('pendaftaran.program_studi') as $prodi) <option> value="{{ $prodi }}"
            {{ old('program_studi', $data['program_studi'] ?? '') === $prodi ? 'selected' : '' }}> {{ $prodi }}
        </option>
        @endforeach
    </select> @include('registrasi.partials.error', ['field'=> 'program_studi' ]) </div>
<div class="field"> <label>Jenjang <span> class="required-mark" >*</span></label>
    <div class="radio-group"> @foreach (config('pendaftaran.jenjang') as $jenjang) <label>
            for="jenjang-{{ strtolower($jenjang) }}" > <input type="radio" id="jenjang-{{ strtolower($jenjang) }}"
                name="jenjang" value="{{ $jenjang }}"
                {{ old('jenjang', $data['jenjang'] ?? '') === $jenjang ? 'checked' : '' }}> {{ $jenjang }} </label>
        @endforeach </div> @include('registrasi.partials.error', ['field'=> 'jenjang'
    ])
</div>
<div class="field full"> <label for="kelas_id">Kelas <span> class="required-mark"
            >*</span></label> <select id="kelas_id" name="kelas_id" class="@error('kelas_id') is-invalid @enderror"
        required>
        <option> value="" >-- Pilih Kelas --</option> @foreach ($kelas as $k) <option>
            value="{{ $k->id }}"
            {{ (string) old('kelas_id', $data['kelas_id'] ?? '') === (string) $k->id ? 'selected' : '' }}>
            {{ $k->nama_kelas }} - {{ $k->jurusan }} ({{ $k->angkatan }}) </option> @endforeach
    </select>
    @include('registrasi.partials.error', ['field'=> 'kelas_id' ]) <span> class="hint" >Pilihan ini divalidasi
        dengan aturan exists ke tabel kelas.</span> </div>
<div class="field full"> <label> for="prestasi"
        >Prestasi / Catatan Pendidikan</label> <textarea> id="prestasi" name="prestasi" maxlength="500"
    placeholder="Opsional. Contoh: Juara 2 Olimpiade Matematika tingkat provinsi."
    class="@error('prestasi') is-invalid @enderror" >{{ old('prestasi', $data['prestasi'] ?? '') }}</textarea>
    @include('registrasi.partials.error', ['field'=> 'prestasi' ]) <span> class="hint" >Opsional, maksimal 500
        karakter.</span> </div>
</div>