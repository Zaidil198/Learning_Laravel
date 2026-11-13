@php
$kelasTerpilih = $kelas->firstWhere('id', (int) ($data['kelas_id'] ?? 0));
@endphp

<div class="summary-box">
    <h3>Ringkasan Data Sebelumnya</h3>
    <dl class="summary-list">
        <dt>Email</dt>
        <dd>{{ $data['email'] ?? '-' }}</dd>

        <dt>Nomor HP</dt>
        <dd>{{ $data['no_hp'] ?? '-' }}</dd>

        <dt>Nama Lengkap</dt>
        <dd>{{ $data['nama_lengkap'] ?? '-' }}</dd>

        <dt>NIK</dt>
        <dd>{{ $data['nik'] ?? '-' }}</dd>

        <dt>Tanggal Lahir</dt>
        <dd>{{ isset($data['tanggal_lahir']) ? \Illuminate\Support\Carbon::parse($data['tanggal_lahir'])->format('d-m-Y') : '-' }}
        </dd>

        <dt>Jenis Kelamin</dt>
        <dd>{{ config('pendaftaran.jenis_kelamin.'.($data['jenis_kelamin'] ?? ''), '-') }}</dd>

        <dt>Agama</dt>
        <dd>{{ $data['agama'] ?? '-' }}</dd>

        <dt>Kewarganegaraan</dt>
        <dd>{{ $data['kewarganegaraan'] ?? '-' }}</dd>

        <dt>Program Studi</dt>
        <dd>{{ $data['program_studi'] ?? '-' }} ({{ $data['jenjang'] ?? '-' }})</dd>

        <dt>Kelas</dt>
        <dd>{{ $kelasTerpilih?->nama_kelas ?? '-' }}</dd>
    </dl>
</div>

<div class="form-grid">
    <div class="field full">
        <label>Minat & Bakat <span class="required-mark">*</span></label>
        <div class="checkbox-group">
            @foreach (config('pendaftaran.minat') as $minat)
            <label for="minat-{{ strtolower($minat) }}">
                <input type="checkbox" id="minat-{{ strtolower($minat) }}" name="minat[]" value="{{ $minat }}"
                    {{ in_array($minat, (array) old('minat', $data['minat'] ?? []), true) ? 'checked' : '' }}>
                {{ $minat }}
            </label>
            @endforeach
        </div>
        @include('registrasi.partials.error', ['field' => 'minat'])
        <span class="hint">Pilih minimal 1 dan maksimal 3 minat.</span>
    </div>

    <div class="field full">
        <label>Sumber Informasi <span class="required-mark">*</span></label>
        <div class="radio-group">
            @foreach (config('pendaftaran.sumber_informasi') as $sumber)
            <label for="sumber-{{ strtolower(str_replace(' ', '-', $sumber)) }}">
                <input type="radio" id="sumber-{{ strtolower(str_replace(' ', '-', $sumber)) }}" name="sumber_informasi"
                    value="{{ $sumber }}"
                    {{ old('sumber_informasi', $data['sumber_informasi'] ?? '') === $sumber ? 'checked' : '' }}>
                {{ $sumber }}
            </label>
            @endforeach
        </div>
        @include('registrasi.partials.error', ['field' => 'sumber_informasi'])
    </div>

    <div class="field full">
        <label for="catatan">Catatan Tambahan</label>
        <textarea id="catatan" name="catatan" maxlength="500"
            placeholder="Opsional. Contoh: membutuhkan informasi beasiswa."
            class="@error('catatan') is-invalid @enderror">{{ old('catatan', $data['catatan'] ?? '') }}</textarea>
        @include('registrasi.partials.error', ['field' => 'catatan'])
        <span class="hint">Opsional, maksimal 500 karakter.</span>
    </div>

    <div class="field">
        <label for="foto">Foto Diri <span class="required-mark">*</span></label>
        <input type="file" id="foto" name="foto" accept="image/jpeg,image/png,image/webp"
            class="@error('foto') is-invalid @enderror" required>
        @include('registrasi.partials.error', ['field' => 'foto'])
        <span class="file-note">JPG/JPEG/PNG/WebP, maksimal 2 MB, minimal 200 x 200 piksel.</span>
    </div>

    <div class="field">
        <label for="berkas">Berkas Pendukung <span class="required-mark">*</span></label>
        <input type="file" id="berkas" name="berkas" accept="application/pdf"
            class="@error('berkas') is-invalid @enderror" required>
        @include('registrasi.partials.error', ['field' => 'berkas'])
        <span class="file-note">PDF, maksimal 5 MB. Contoh: scan ijazah atau rapor.</span>
    </div>

    <div class="field full">
        <label for="setuju_syarat" style="font-weight: normal; display: flex; gap: 8px; align-items: flex-start;">
            <input type="checkbox" id="setuju_syarat" name="setuju_syarat" value="1"
                {{ old('setuju_syarat') ? 'checked' : '' }} required>
            <span>
                Saya menyatakan bahwa data yang saya isi benar dan menyetujui syarat serta ketentuan
                pendaftaran. <span class="required-mark">*</span>
            </span>
        </label>
        @include('registrasi.partials.error', ['field' => 'setuju_syarat'])
    </div>

    <div class="field full">
        <p class="hint" style="margin: 0;">
            Catatan: jika validasi gagal, file yang sudah dipilih harus dipilih ulang karena browser
            tidak mengirim ulang isi file secara otomatis.
        </p>
    </div>
</div>