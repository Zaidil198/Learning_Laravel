<div class="form-grid">
    <div class="field full">
        <label for="email">Email <span class="required-mark">*</span></label>
        <input type="email" id="email" name="email" value="{{ old('email', $data['email'] ?? '') }}" maxlength="100"
            autocomplete="email" placeholder="nama@email.com" class="@error('email') is-invalid @enderror" required>
        @include('registrasi.partials.error', ['field' => 'email'])
        <span class="hint">Email ini akan menjadi akun login Anda.</span>
    </div>

    <div class="field">
        <label for="no_hp">Nomor HP <span class="required-mark">*</span></label>
        <input type="tel" id="no_hp" name="no_hp" value="{{ old('no_hp', $data['no_hp'] ?? '') }}" maxlength="16"
            autocomplete="tel" placeholder="081234567890" class="@error('no_hp') is-invalid @enderror" required>
        @include('registrasi.partials.error', ['field' => 'no_hp'])
        <span class="hint">Format: 08..., 628..., atau +628....</span>
    </div>

    <div class="field">
        <label for="password">Password <span class="required-mark">*</span></label>
        <input type="password" id="password" name="password" minlength="8" autocomplete="new-password"
            class="@error('password') is-invalid @enderror" required>
        @include('registrasi.partials.error', ['field' => 'password'])
        <span class="hint">Minimal 8 karakter, mengandung huruf dan angka.</span>
    </div>

    <div class="field">
        <label for="password_confirmation">Konfirmasi Password <span class="required-mark">*</span></label>
        <input type="password" id="password_confirmation" name="password_confirmation" minlength="8"
            autocomplete="new-password" required>
        <span class="hint">Ulangi password yang sama.</span>
    </div>
</div>