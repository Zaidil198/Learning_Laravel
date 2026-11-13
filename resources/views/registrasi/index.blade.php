<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Mahasiswa Baru - Langkah {{ $step }}</title>
    <style>
    @include('partials.style') body {
        max-width: 900px;
        background-color: #f7f8fa;
    }

    .wizard-shell {
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 28px;
        margin-top: 10px;
    }

    .wizard-header h1 {
        margin-bottom: 4px;
    }

    .wizard-header p {
        margin-top: 0;
        color: #555;
    }

    .stepper {
        display: flex;
        gap: 8px;
        list-style: none;
        padding: 0;
        margin: 24px 0 20px;
        flex-wrap: wrap;
    }

    .stepper li {
        flex: 1 1 150px;
        display: flex;
        align-items: center;
        padding: 10px 12px;
        border: 1px solid #ddd;
        border-radius: 6px;
        background: #fafafa;
        color: #777;
        font-size: 14px;
    }

    .stepper li a {
        display: flex;
        align-items: center;
        gap: 8px;
        color: inherit;
        text-decoration: none;
        width: 100%;
    }

    .stepper li.active {
        border-color: #155eef;
        background: #eef4ff;
        color: #0b3ea8;
        font-weight: bold;
    }

    .stepper li.done {
        border-color: #2da44e;
        background: #eef9f0;
        color: #176b2c;
    }

    .step-number {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 26px;
        height: 26px;
        margin-right: 8px;
        border-radius: 50%;
        background: #e6e6e6;
        font-weight: bold;
        flex: 0 0 auto;
    }

    .stepper li a .step-number {
        margin-right: 0;
    }

    .stepper li.active .step-number {
        background: #155eef;
        color: #fff;
    }

    .stepper li.done .step-number {
        background: #2da44e;
        color: #fff;
    }

    .form-card {
        border-top: 4px solid #155eef;
        padding-top: 18px;
    }

    .form-card h2 {
        margin-top: 0;
    }

    .wizard-form {
        display: block;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 4px 18px;
    }

    .field {
        margin-bottom: 14px;
    }

    .field.full {
        grid-column: 1 / -1;
    }

    .field>label {
        display: block;
        margin-bottom: 5px;
    }

    .field input[type="text"],
    .field input[type="email"],
    .field input[type="tel"],
    .field input[type="password"],
    .field input[type="number"],
    .field input[type="date"],
    .field input[type="file"],
    .field select,
    .field textarea {
        width: 100%;
        box-sizing: border-box;
        padding: 9px;
        border: 1px solid #b9b9b9;
        border-radius: 4px;
        font-family: inherit;
        font-size: 14px;
        background: #fff;
    }

    .field textarea {
        min-height: 90px;
        resize: vertical;
    }

    .field input:focus,
    .field select:focus,
    .field textarea:focus {
        outline: 2px solid #b6cdf8;
        border-color: #155eef;
    }

    .field input.is-invalid,
    .field select.is-invalid,
    .field textarea.is-invalid {
        border-color: #a40000;
        background: #fff7f7;
    }

    .radio-group,
    .checkbox-group {
        display: flex;
        flex-wrap: wrap;
        gap: 10px 16px;
        padding-top: 4px;
    }

    .radio-group label,
    .checkbox-group label {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-weight: normal;
        margin: 0;
        cursor: pointer;
    }

    .field input[type="radio"],
    .field input[type="checkbox"] {
        width: auto;
        margin: 0;
    }

    .field-error {
        display: block;
        margin-top: 4px;
        color: #a40000;
        font-size: 13px;
    }

    .error-summary {
        padding: 12px 12px 12px 16px;
        background: #ffecec;
        border: 1px solid #e0a0a0;
        border-radius: 6px;
        color: #a40000;
        margin-bottom: 18px;
    }

    .error-summary ul {
        margin: 8px 0 0 18px;
        padding: 0;
    }

    .form-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        margin-top: 24px;
        padding-top: 18px;
        border-top: 1px solid #e5e5e5;
    }

    .summary-box {
        background: #f8f9fb;
        border: 1px solid #ddd;
        border-radius: 6px;
        padding: 14px 16px;
        margin-bottom: 20px;
    }

    .summary-box h3 {
        margin: 0 0 8px;
        font-size: 16px;
    }

    .summary-list {
        display: grid;
        grid-template-columns: 180px 1fr;
        gap: 4px 12px;
        margin: 0;
        font-size: 14px;
    }

    .summary-list dt {
        color: #555;
    }

    .summary-list dd {
        margin: 0;
        font-weight: 600;
    }

    .required-mark {
        color: #a40000;
    }

    .file-note {
        font-size: 12px;
        color: #666;
        margin-top: 4px;
    }

    @media (max-width: 640px) {
        .wizard-shell {
            padding: 18px;
        }

        .form-grid {
            grid-template-columns: 1fr;
        }

        .summary-list {
            grid-template-columns: 1fr;
        }

        .form-actions {
            flex-direction: column-reverse;
            align-items: stretch;
        }

        .form-actions .btn,
        .form-actions button {
            text-align: center;
        }
    }
    </style>
</head>

<body>
    <div class="wizard-shell">
        <div class="wizard-header">
            <h1>Pendaftaran Mahasiswa Baru</h1>
            <p>Lengkapi data dalam 4 langkah. Data sementara disimpan di session sampai Anda mengirim pendaftaran.</p>
        </div>

        @php
        $labels = [
        1 => 'Akun & Kontak',
        2 => 'Data Pribadi',
        3 => 'Pendidikan',
        4 => 'Berkas & Konfirmasi',
        ];
        @endphp

        <ol class="stepper">
            @foreach ($labels as $number => $label)
            @php
            $isActive = $number === $step;
            $isDone = in_array($number, $completedSteps, true);
            @endphp

            <li class="{{ $isActive ? 'active' : ($isDone ? 'done' : '') }}">
                @if ($isDone || $isActive)
                <a href="{{ route('registrasi.step', $number) }}">
                    <span class="step-number">{{ $isDone && ! $isActive ? '✓' : $number }}</span>
                    <span>{{ $label }}</span>
                </a>
                @else
                <span class="step-number">{{ $number }}</span>
                <span>{{ $label }}</span>
                @endif
            </li>
            @endforeach
        </ol>

        @if (session('success'))
        <p class="success">{{ session('success') }}</p>
        @endif

        @if (session('error'))
        <div class="error-summary">{{ session('error') }}</div>
        @endif

        @if ($errors->any())
        <div class="error-summary">
            <strong>Ada {{ $errors->count() }} data yang perlu diperbaiki:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="form-card">
            <h2>Langkah {{ $step }}: {{ $labels[$step] }}</h2>

            <form class="wizard-form" method="POST" action="{{ route('registrasi.store', $step) }}"
                enctype="multipart/form-data">
                @csrf

                @include('registrasi.steps.step-'.$step)

                <div class="form-actions">
                    @if ($step > 1)
                    <a href="{{ route('registrasi.step', $step - 1) }}" class="btn">Kembali</a>
                    @else
                    <span></span>
                    @endif

                    <button type="submit">
                        {{ $step === 4 ? 'Kirim Pendaftaran' : 'Lanjut ke Langkah '.($step + 1) }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <p class="hint" style="text-align: center; margin-top: 16px;">
        Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a>.
    </p>
</body>

</html>