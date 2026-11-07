<div class="form-grid">
    <div class="field full">
        <label for="nama_lengkap">Nama Lengkap <span class="required-mark">*</span></label>
        <input
            type="text"
            id="nama_lengkap"
            name="nama_lengkap"
            value="{{ old('nama_lengkap', $data['nama_lengkap'] ?? '') }}"
            maxlength="100"
            autocomplete="name"
            class="@error('nama_lengkap') is-invalid @enderror"
            required
        >
        @include('registrasi.partials.error', ['field' => 'nama_lengkap'])
    </div>

    <div class="field">
        <label for="nik">NIK <span class="required-mark">*</span></label>
        <input
            type="text"
            id="nik"
            name="nik"
            value="{{ old('nik', $data['nik'] ?? '') }}"
            maxlength="16"
            inputmode="numeric"
            placeholder="16 digit angka"
            class="@error('nik') is-invalid @enderror"
            required
        >
        @include('registrasi.partials.error', ['field' => 'nik'])
        <span class="hint">Harus tepat 16 digit angka.</span>
    </div>

    <div class="field">
        <label for="tempat_lahir">Tempat Lahir <span class="required-mark">*</span></label>
        <input
            type="text"
            id="tempat_lahir"
            name="tempat_lahir"
            value="{{ old('tempat_lahir', $data['tempat_lahir'] ?? '') }}"
            maxlength="60"
            class="@error('tempat_lahir') is-invalid @enderror"
            required
        >
        @include('registrasi.partials.error', ['field' => 'tempat_lahir'])
    </div>

    <div class="field">
        <label for="tanggal_lahir">Tanggal Lahir <span class="required-mark">*</span></label>
        <input
            type="date"
            id="tanggal_lahir"
            name="tanggal_lahir"
            value="{{ old('tanggal_lahir', $data['tanggal_lahir'] ?? '') }}"
            max="{{ now()->format('Y-m-d') }}"
            class="@error('tanggal_lahir') is-invalid @enderror"
            required
        >
        @include('registrasi.partials.error', ['field' => 'tanggal_lahir'])
        <span class="hint">Usia minimal 15 tahun.</span>
    </div>

    <div class="field">
        <label>Jenis Kelamin <span class="required-mark">*</span></label>
        <div class="radio-group">
            @foreach (config('pendaftaran.jenis_kelamin') as $value => $label)
                <label for="jenis-kelamin-{{ strtolower($value) }}">
                    <input
                        type="radio"
                        id="jenis-kelamin-{{ strtolower($value) }}"
                        name="jenis_kelamin"
                        value="{{ $value }}"
                        {{ old('jenis_kelamin', $data['jenis_kelamin'] ?? '') === $value ? 'checked' : '' }}
                    >
                    {{ $label }}
                </label>
            @endforeach
        </div>
        @include('registrasi.partials.error', ['field' => 'jenis_kelamin'])
    </div>

    <div class="field">
        <label for="agama">Agama <span class="required-mark">*</span></label>
        <select id="agama" name="agama" class="@error('agama') is-invalid @enderror" required>
            <option value="">-- Pilih Agama --</option>
            @foreach (config('pendaftaran.agama') as $agama)
                <option value="{{ $agama }}" {{ old('agama', $data['agama'] ?? '') === $agama ? 'selected' : '' }}>
                    {{ $agama }}
                </option>
            @endforeach
        </select>
        @include('registrasi.partials.error', ['field' => 'agama'])
    </div>

    <div class="field full">
        <label>Kewarganegaraan <span class="required-mark">*</span></label>
        <div class="radio-group">
            @foreach (config('pendaftaran.kewarganegaraan') as $value => $label)
                <label for="kewarganegaraan-{{ strtolower($value) }}">
                    <input
                        type="radio"
                        id="kewarganegaraan-{{ strtolower($value) }}"
                        name="kewarganegaraan"
                        value="{{ $value }}"
                        {{ old('kewarganegaraan', $data['kewarganegaraan'] ?? 'WNI') === $value ? 'checked' : '' }}
                    >
                    {{ $label }}
                </label>
            @endforeach
        </div>
        @include('registrasi.partials.error', ['field' => 'kewarganegaraan'])
    </div>

    <div class="field full" id="negara-asal-field" style="display: none;">
        <label for="negara_asal">Negara Asal</label>
        <input
            type="text"
            id="negara_asal"
            name="negara_asal"
            value="{{ old('negara_asal', $data['negara_asal'] ?? '') }}"
            maxlength="60"
            class="@error('negara_asal') is-invalid @enderror"
        >
        @include('registrasi.partials.error', ['field' => 'negara_asal'])
        <span class="hint">Wajib diisi jika memilih WNA.</span>
    </div>

    <div class="field full">
        <label for="alamat">Alamat Lengkap <span class="required-mark">*</span></label>
        <textarea
            id="alamat"
            name="alamat"
            maxlength="255"
            minlength="10"
            class="@error('alamat') is-invalid @enderror"
            required
        >{{ old('alamat', $data['alamat'] ?? '') }}</textarea>
        @include('registrasi.partials.error', ['field' => 'alamat'])
        <span class="hint">Minimal 10 karakter.</span>
    </div>

    <div class="field">
        <label for="kode_pos">Kode Pos <span class="required-mark">*</span></label>
        <input
            type="text"
            id="kode_pos"
            name="kode_pos"
            value="{{ old('kode_pos', $data['kode_pos'] ?? '') }}"
            maxlength="5"
            inputmode="numeric"
            class="@error('kode_pos') is-invalid @enderror"
            required
        >
        @include('registrasi.partials.error', ['field' => 'kode_pos'])
        <span class="hint">5 digit angka.</span>
    </div>
</div>

<script>
    (function () {
        const wna = document.getElementById('kewarganegaraan-wna');
        const field = document.getElementById('negara-asal-field');

        if (!wna || !field) {
            return;
        }

        const toggle = () => {
            field.style.display = wna.checked ? '' : 'none';
        };

        document.querySelectorAll('input[name="kewarganegaraan"]').forEach((input) => {
            input.addEventListener('change', toggle);
        });

        toggle();
    })();
</script>