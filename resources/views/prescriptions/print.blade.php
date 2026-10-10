<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ ucfirst($prescription->assessment_type) }} Assessment - {{ $prescription->prescription_no }} -
        {{ $prescription->patient->full_name }}</title>
    <style>
        :root {
            --bg: #e9ebef;
            --ink: #111;
            --red: #c8202f;
            --blue: #1f4e9c;
            --pen: #0a2a7a
        }

        * {
            box-sizing: border-box
        }

        body {
            margin: 0;
            padding: 16px;
            background: var(--bg);
            font-family: Arial, Helvetica, sans-serif;
            color: var(--ink);
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact
        }

        .bar {
            max-width: 820px;
            margin: 0 auto 12px;
            display: flex;
            justify-content: space-between;
            gap: 8px;
            flex-wrap: wrap
        }

        .bar a,
        .bar button {
            border: 0;
            padding: 9px 16px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 13px;
            cursor: pointer;
            text-decoration: none
        }

        .bar a {
            background: #fff;
            color: #111
        }

        .bar button {
            background: var(--red);
            color: #fff
        }

        .sheet {
            background: #fff;
            max-width: 820px;
            margin: 0 auto;
            padding: 18px 26px 22px;
            box-shadow: 0 2px 12px #0004
        }

        .lh {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            align-items: flex-start
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 8px
        }

        .logo img {
            height: 46px;
            width: auto
        }

        .badge {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: var(--red);
            color: #fff;
            font-weight: 900;
            display: none;
            place-items: center;
            font-size: 20px
        }

        .sd {
            font: italic 900 15px Arial;
            color: #555;
            letter-spacing: 1px
        }

        .iiams {
            font-size: 10px;
            font-weight: 800;
            color: #444;
            border-left: 2px solid #999;
            padding-left: 8px
        }

        .clinic {
            font: 900 26px Georgia, serif;
            color: var(--red);
            text-shadow: 1px 1px 0 #999;
            margin: 2px 0 4px;
            text-transform: uppercase
        }

        .pills {
            display: flex;
            gap: 6px;
            margin-bottom: 4px
        }

        .pills span {
            background: var(--blue);
            color: #fff;
            font-size: 9px;
            font-weight: 800;
            padding: 2px 9px;
            border-radius: 9px;
            letter-spacing: .5px
        }

        .addr {
            font-size: 10.5px;
            line-height: 1.45
        }

        .addr b {
            background: var(--red);
            color: #fff;
            padding: 0 4px;
            font-size: 9px;
            margin-right: 4px
        }

        .rt {
            text-align: right;
            font-size: 12px;
            min-width: 200px
        }

        .rt ul {
            list-style: none;
            margin: 0 0 10px;
            padding: 0;
            font-weight: 800;
            color: var(--blue)
        }

        .rt ul li:before {
            content: "• ";
            color: var(--red)
        }

        .rt ul li:last-child {
            color: var(--red)
        }

        .dr b {
            font-size: 14px
        }

        .dr div {
            font-size: 9.5px;
            color: #333
        }

        .rule {
            border-top: 3px solid var(--blue);
            border-radius: 2px;
            margin: 6px 0 10px
        }

        h2 {
            font: 700 19px Georgia, serif;
            text-decoration: underline;
            text-align: center;
            margin: 4px 0 10px
        }

        .meta {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            color: #444;
            margin-bottom: 4px
        }

        .date {
            text-align: right;
            font-size: 13px
        }

        .r3 {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr;
            gap: 14px
        }

        .ln {
            display: flex;
            align-items: flex-end;
            gap: 6px;
            margin: 5px 0;
            font-size: 13px
        }

        .ln label {
            white-space: nowrap
        }

        .v,
        .ink {
            color: var(--pen);
            font-weight: 600
        }

        .ln .v {
            flex: 1;
            border-bottom: 1px solid #222;
            padding: 0 3px;
            min-height: 18px
        }

        .sec {
            font-weight: 700;
            font-size: 13.5px;
            margin: 12px 0 3px;
            border-bottom: 1px solid #ccc;
            page-break-after: avoid
        }

        .row {
            display: grid;
            grid-template-columns: 200px 10px 1fr;
            gap: 4px 6px;
            align-items: end;
            font-size: 13px;
            margin: 5px 0;
            page-break-inside: avoid
        }

        .row.sub label {
            padding-left: 22px
        }

        .row .v {
            border-bottom: 1px dotted #777;
            min-height: 18px;
            padding: 0 3px;
            white-space: pre-line
        }

        .ch {
            display: flex;
            gap: 6px;
            flex-wrap: wrap
        }

        .ch span {
            border: 1px solid #222;
            padding: 1px 9px;
            font-size: 12px;
            border-radius: 3px
        }

        .ch span.on {
            background: var(--blue);
            color: #fff;
            border-color: var(--blue)
        }

        .note {
            font-size: 11px;
            color: #444;
            display: block;
            margin-top: 2px
        }

        .two {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4px 22px
        }

        .five {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 4px 10px;
            font-size: 13px
        }

        .five div {
            border-bottom: 1px dotted #777
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            margin: 4px 0
        }

        th,
        td {
            border: 1px solid #333;
            padding: 4px 6px;
            text-align: left;
            vertical-align: top
        }

        th {
            background: #f1f1f1;
            font-size: 11px;
            text-transform: uppercase
        }

        td.n {
            color: var(--pen);
            font-weight: 600
        }

        .sp {
            display: grid;
            grid-template-columns: 200px 1fr;
            gap: 4px 6px;
            font-size: 13px;
            margin: 5px 0;
            page-break-inside: avoid
        }

        .sp .t {
            display: flex;
            gap: 22px;
            flex-wrap: wrap;
            border-bottom: 1px dotted #777
        }

        .foot {
            margin-top: 18px;
            padding-top: 10px;
            border-top: 3px solid var(--blue);
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 14px;
            page-break-inside: avoid
        }

        .ft {
            font-size: 12px;
            line-height: 1.7
        }

        .ft b {
            background: #111;
            color: #fff;
            font-size: 10px;
            padding: 1px 6px
        }

        .ft small {
            display: block;
            font-size: 9px;
            color: #777
        }

        .sign {
            text-align: center;
            min-width: 220px
        }

        .sign .l {
            height: 40px;
            border-bottom: 1px solid #333;
            display: flex;
            align-items: flex-end;
            justify-content: center;
            font: italic 700 16px Georgia, serif;
            color: var(--pen)
        }

        .sign small {
            display: block;
            font-size: 10px;
            color: #444
        }

        @media(max-width:600px) {
            .sheet {
                padding: 12px
            }

            .row,
            .sp {
                grid-template-columns: 1fr
            }

            .row>i {
                display: none
            }

            .row.sub label {
                padding-left: 10px
            }

            .r3,
            .two,
            .five {
                grid-template-columns: 1fr
            }

            .clinic {
                font-size: 20px
            }

            .lh,
            .foot {
                flex-direction: column
            }

            .rt {
                text-align: left
            }
        }

        @media print {
            @page {
                size: A4;
                margin: 10mm
            }

            body {
                background: #fff;
                padding: 0
            }

            .bar {
                display: none
            }

            .sheet {
                box-shadow: none;
                max-width: none;
                padding: 0
            }
        }
    </style>
</head>

<body>

    @php
        $data = $prescription->assessment_data ?? [];
        $pt = $prescription->patient;
        $prescribedClinic = $prescription->clinic ?? $pt?->clinic ?? $clinic;
        $docName = $prescription->doctor->name ?? $prescribedClinic->doctor_name ?? 'Dr. Mahesh Sahu PT';
        $docQual = $prescription->doctor->qualification ?? 'M.P.T Neuro, FOMT Australia, CHIROPRACTIC Sweden';
        $docReg = $prescription->doctor->registration_no ?? $prescribedClinic->doctor_reg_no ?? 'M.I.A.P. L-40612, MPPC-32862';
        $isNeuro = ($prescription->assessment_type === 'neurological');

        // chips: show every option, highlight the selected ones (extra saved values are appended)
        $chips = function ($opts, $sel) {
            $sel = array_values(array_filter((array) $sel, fn($x) => $x !== null && $x !== ''));
            $low = array_map('strtolower', $sel);
            $all = $opts;
            foreach ($sel as $s) {
                if (!in_array(strtolower($s), array_map('strtolower', $opts)))
                    $all[] = $s;
            }
            $out = '';
            foreach ($all as $o) {
                $out .= '<span class="' . (in_array(strtolower($o), $low) ? 'on' : '') . '">' . e($o) . '</span>';
            }
            return new \Illuminate\Support\HtmlString($out);
        };

        $sideRaw = strtolower($data['affected_side'] ?? 'Right (R)');
        $sideSel = [];
        if (str_contains($sideRaw, 'both') || str_contains($sideRaw, 'bilateral'))
            $sideSel[] = 'Both';
        elseif (str_contains($sideRaw, 'left') || str_contains($sideRaw, '(l)') || $sideRaw === 'l')
            $sideSel[] = 'L';
        else
            $sideSel[] = 'R';

        $vasScore = $data['vas_scale'] ?? (isset($data['vas_pain_score']) ? round($data['vas_pain_score'] / 7) : 4);
        $consc = $data['neuro_consciousness_level'] ?? $data['level_of_consciousness'] ?? 'Alert';
    @endphp

    <!-- TOOLBAR -->
    <div class="bar">
        <a href="{{ route('prescriptions.show', $prescription->id) }}">← Back to Details</a>
        <button onclick="window.print()">Print Official Chart &amp; Prescription</button>
    </div>

    <div class="sheet">

        <!-- LETTERHEAD -->
        <div class="lh">
            <div>
                <div class="logo">
                    <img src="{{ asset('logo.png') }}" alt="SDPC"
                        onerror="this.style.display='none';this.nextElementSibling.style.display='grid'">
                    <div class="badge">+</div>
                    <span class="sd">SDPC</span><span class="iiams">IIAMS</span>
                </div>
                <div class="clinic">{{ $prescribedClinic->name ?? 'SD Physiotherapy Clinic' }}</div>
                <div class="pills"><span>PHYSIOTHERAPY</span><span>OSTEOPATHY</span><span>CHIROPRACTIC</span></div>
                <div class="addr">
                    <b>Branch 1</b>562, Khatiwala Tank, Indore Ph. 0731-4976163<br>
                    <b>Branch 2</b>MR6-110, Mahalaxmi Nagar, Indore Ph. 0731-4979170<br>
                    ☎ {{ $prescribedClinic->phone ?? '098933 62477' }} &nbsp; {{ $prescribedClinic->email ??
                    'maheshsahu1983@gmail.com' }}<br>
                    🌐 www.sdphysiotherapy.in
                </div>
            </div>
            <div class="rt">
                <ul>
                    <li>Drug Free</li>
                    <li>Surgery Free</li>
                    <li>Pain Free</li>
                    <li>Spine Specialist</li>
                </ul>
                <div class="dr"><b>{{ $docName }}</b>
                    <div>{{ $docQual }}<br>{{ $docReg }}</div>
                </div>
            </div>
        </div>
        <div class="rule"></div>

        <h2>{{ $isNeuro ? 'Neurological Assessment Chart' : 'Musculo Skeletal Assessment Chart' }}</h2>

        <div class="meta">
            <span>Patient ID: <b class="ink">{{ $pt->patient_id }}</b> &nbsp;|&nbsp; Rx No: <b
                    class="ink">{{ $prescription->prescription_no }}</b></span>
            <span class="date">दिनांक <b class="ink">{{ $prescription->prescription_date->format('d/m/Y') }}</b></span>
        </div>

        <!-- DEMOGRAPHICS -->
        <div class="r3">
            <div class="ln"><label>Name</label>
                <div class="v">{{ $pt->full_name }}</div>
            </div>
            <div class="ln"><label>Age</label>
                <div class="v">{{ $pt->age }} Yrs</div>
            </div>
            <div class="ln"><label>Sex</label>
                <div class="v">{{ $pt->gender }}</div>
            </div>
        </div>
        <div class="r3">
            <div class="ln"><label>Occupation</label>
                <div class="v">{{ $data['occupation'] ?? $pt->occupation ?? 'General' }}</div>
            </div>
            <div class="ln" style="grid-column:span 2"><label>Affected Side</label>
                <div class="ch">{!! $chips(['R', 'L', 'Both'], $sideSel) !!}</div>
            </div>
        </div>
        <div class="ln"><label>Address</label>
            <div class="v">{{ $pt->address ?? '-' }}{{ $pt->city ? ', ' . $pt->city : '' }}</div>
        </div>

        @if(!$isNeuro)
            <!-- ================= MUSCULO SKELETAL ================= -->
            <div class="row"><label>Chief Complaint</label><i>:</i>
                <div class="v">{{ $data['chief_complaint'] ?? $data['chief_complaints'] ?? '-' }}</div>
            </div>
            <div class="row"><label>Duration</label><i>:</i>
                <div class="v">{{ $data['duration'] ?? '-' }}</div>
            </div>
            <div class="row"><label>Pain Aggravation</label><i>:</i>
                <div>
                    <div class="ch">{!! $chips(['Day', 'Night', 'Activities'], $data['pain_aggravation'] ?? []) !!}</div>
                    @if(!empty($data['pain_aggravation_activities']))<span
                    class="note ink">{{ $data['pain_aggravation_activities'] }}</span>@endif
                </div>
            </div>
            <div class="row"><label>Pain – VAS Scale</label><i>:</i>
                <div>
                    <div class="ch">@for($i = 0; $i <= 10; $i++)<span
                    class="{{ (int) $vasScore === $i ? 'on' : '' }}">{{ $i }}</span>@endfor</div>
                    <span class="note">Score: <b class="ink">{{ $vasScore }} / 10</b>
                        ({{ $vasScore <= 3 ? 'Mild' : ($vasScore <= 6 ? 'Moderate' : 'Severe') }})</span>
                </div>
            </div>
            <div class="row"><label>Associated Factors</label><i>:</i>
                <div>
                    <div class="ch">
                        {!! $chips(['Radiating pain', 'Swelling', 'Numbness'], $data['associate_factors'] ?? []) !!}</div>
                    @if(!empty($data['associate_factors_notes']))<span
                    class="note ink">{{ $data['associate_factors_notes'] }}</span>@endif
                </div>
            </div>
            <div class="row"><label>Past History</label><i>:</i>
                <div class="v">{{ $data['past_history'] ?? 'None reported' }}</div>
            </div>

            <div class="sec">Observation</div>
            <div class="row"><label>Posture</label><i>:</i>
                <div class="v">{{ $data['observation_posture'] ?? $data['posture'] ?? '-' }}</div>
            </div>
            <div class="row"><label>Tenderness</label><i>:</i>
                <div class="v">{{ $data['observation_tenderness'] ?? $data['tenderness'] ?? '-' }}</div>
            </div>
            <div class="row"><label>Gait</label><i>:</i>
                <div class="v">{{ $data['observation_gait'] ?? $data['gait'] ?? 'Normal' }}</div>
            </div>

            <div class="sec">Range of Motion</div>
            <table>
                <thead>
                    <tr>
                        <th>Movement</th>
                        <th style="width:130px">Right (R)</th>
                        <th style="width:130px">Left (L)</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Flexion</td>
                        <td class="n">{{ $data['rom_flexion_r'] ?? $data['rom_right'] ?? '-' }}</td>
                        <td class="n">{{ $data['rom_flexion_l'] ?? $data['rom_left'] ?? '-' }}</td>
                        <td class="n">{{ $data['rom_flexion_notes'] ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Abduction</td>
                        <td class="n">{{ $data['rom_abduction_r'] ?? '-' }}</td>
                        <td class="n">{{ $data['rom_abduction_l'] ?? '-' }}</td>
                        <td class="n">{{ $data['rom_abduction_notes'] ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Extension</td>
                        <td class="n">{{ $data['rom_extension_r'] ?? '-' }}</td>
                        <td class="n">{{ $data['rom_extension_l'] ?? '-' }}</td>
                        <td class="n">{{ $data['rom_extension_notes'] ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>ER (External Rotation)</td>
                        <td class="n">{{ $data['rom_er_r'] ?? '-' }}</td>
                        <td class="n">{{ $data['rom_er_l'] ?? '-' }}</td>
                        <td class="n">{{ $data['rom_er_notes'] ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>IR (Internal Rotation)</td>
                        <td class="n">{{ $data['rom_ir_r'] ?? '-' }}</td>
                        <td class="n">{{ $data['rom_ir_l'] ?? '-' }}</td>
                        <td class="n">{{ $data['rom_ir_notes'] ?? '-' }}</td>
                    </tr>
                </tbody>
            </table>
            <div class="row"><label>MMT</label><i>:</i>
                <div class="v">R: {{ $data['mmt_right'] ?? '-' }} &nbsp;|&nbsp; L:
                    {{ $data['mmt_left'] ?? '-' }}@if(!empty($data['mmt_notes'])) ({{ $data['mmt_notes'] }})@endif</div>
            </div>
            <div class="row"><label>Advice</label><i>:</i>
                <div>
                    <div class="ch">{!! $chips(['X-ray', 'MRI'], $data['advice_imaging'] ?? []) !!}</div>
                    @if(!empty($data['advice_notes']))<span class="note ink">{{ $data['advice_notes'] }}</span>@endif
                </div>
            </div>

            <div class="sec">Special Tests</div>
            <div class="sp"><label>Shoulder</label>
                <div class="t"><span>Drop arm : <b
                            class="ink">{{ $data['st_drop_arm'] ?? '-ve' }}</b></span><span>Impingement : <b
                            class="ink">{{ $data['st_impingement'] ?? '-ve' }}</b></span></div>
            </div>
            <div class="sp"><label>Knee</label>
                <div class="t"><span>Drawer ant. : <b
                            class="ink">{{ $data['st_drawer_ant'] ?? '-ve' }}</b></span><span>McMurray : <b
                            class="ink">{{ $data['st_mcmurray'] ?? '-ve' }}</b></span></div>
            </div>
            <div class="sp"><label>Hip</label>
                <div class="t"><span>FABER : <b class="ink">{{ $data['st_faber'] ?? '-ve' }}</b></span><span>Trendelenburg :
                        <b class="ink">{{ $data['st_trendelenburg'] ?? '-ve' }}</b></span></div>
            </div>
            <div class="sp"><label>Spine</label>
                <div class="t"><span>SLR : <b class="ink">{{ $data['st_slr'] ?? '-ve' }}</b></span><span>Slump : <b
                            class="ink">{{ $data['st_slump'] ?? '-ve' }}</b></span></div>
            </div>
            <div class="sp"><label>Cervical</label>
                <div class="t"><span>Compression : <b
                            class="ink">{{ $data['st_cervical_compression'] ?? '-ve' }}</b></span><span>Spurling : <b
                            class="ink">{{ $data['st_spurling'] ?? '-ve' }}</b></span></div>
            </div>
            @if(!empty($data['special_test_notes']))
                <div class="row"><label>Notes</label><i>:</i>
                    <div class="v">{{ $data['special_test_notes'] }}</div>
                </div>
            @endif

        @else
            <!-- ================= NEUROLOGICAL ================= -->
            <div class="row"><label>Chief Complaint</label><i>:</i>
                <div class="v">{{ $data['neuro_chief_complaint'] ?? $data['neuro_complaints'] ?? '-' }}</div>
            </div>

            <div class="sec">History</div>
            <div class="row sub"><label>Past H/O</label><i>:</i>
                <div class="v">{{ $data['neuro_past_ho'] ?? $data['neuro_ho'] ?? '-' }}</div>
            </div>
            <div class="row sub"><label>Surgical H/O</label><i>:</i>
                <div class="v">{{ $data['neuro_surgical_ho'] ?? '-' }}</div>
            </div>
            <div class="row sub"><label>Family H/O</label><i>:</i>
                <div class="v">{{ $data['neuro_family_ho'] ?? '-' }}</div>
            </div>
            <div class="row sub"><label>Associated Factors</label><i>:</i>
                <div class="v">{{ $data['neuro_associate_factors'] ?? '-' }}</div>
            </div>

            <div class="sec">Observation</div>
            <div class="row sub"><label>Posture</label><i>:</i>
                <div class="v">{{ $data['neuro_posture'] ?? '-' }}</div>
            </div>
            <div class="row sub"><label>Gait</label><i>:</i>
                <div class="v">{{ $data['neuro_gait'] ?? '-' }}</div>
            </div>
            <div class="row sub"><label>Deformity</label><i>:</i>
                <div class="v">{{ $data['neuro_deformity'] ?? '-' }}</div>
            </div>
            <div class="row sub"><label>Functional Aids</label><i>:</i>
                <div><span class="ink">{{ implode(', ', (array) ($data['neuro_functional_aids'] ?? [])) ?: 'None' }}</span>
                    <span class="note">walking aids / catheter @if(!empty($data['neuro_functional_aids_notes']))— <b
                    class="ink">{{ $data['neuro_functional_aids_notes'] }}</b>@endif</span>
                </div>
            </div>
            <div class="row sub"><label>Protective Aids</label><i>:</i>
                <div><span class="ink">{{ implode(', ', (array) ($data['neuro_protective_aids'] ?? [])) ?: 'None' }}</span>
                    <span class="note">brace / prosthetics @if(!empty($data['neuro_protective_aids_notes']))— <b
                    class="ink">{{ $data['neuro_protective_aids_notes'] }}</b>@endif</span>
                </div>
            </div>

            <div class="sec">Examination</div>
            <div class="row sub"><label>Consciousness Level</label><i>:</i>
                <div class="ch">{!! $chips(['Alert', 'Lethargy', 'Confusion', 'Coma'], $consc) !!}</div>
            </div>
            <div class="row sub"><label>Orientation</label><i>:</i>
                <div class="v">{{ $data['neuro_orientation'] ?? 'Intact' }}</div>
            </div>
            <div class="row sub"><label>Behaviour</label><i>:</i>
                <div class="v">{{ $data['neuro_behaviour'] ?? 'Cooperative' }}</div>
            </div>
            <div class="row sub"><label>Memory</label><i>:</i>
                <div class="v">{{ $data['neuro_memory'] ?? 'Intact' }}</div>
            </div>
            <div class="row sub"><label>Special Sense</label><i>:</i>
                <div class="five">
                    <div>Vision: <b class="ink">{{ $data['neuro_sense_vision'] ?? 'Normal' }}</b></div>
                    <div>Hearing: <b class="ink">{{ $data['neuro_sense_hearing'] ?? 'Normal' }}</b></div>
                    <div>Smell: <b class="ink">{{ $data['neuro_sense_smell'] ?? 'Normal' }}</b></div>
                    <div>Taste: <b class="ink">{{ $data['neuro_sense_taste'] ?? 'Normal' }}</b></div>
                    <div>Tactile: <b class="ink">{{ $data['neuro_sense_tactile'] ?? 'Normal' }}</b></div>
                </div>
            </div>

            <div class="sec">Sensory Examination</div>
            <div class="row"><label>Sensory Examination</label><i>:</i>
                <div class="v">
                    {{ $data['neuro_sensory_exam'] ?? $data['sensory_deficit'] ?? 'Superficial & deep sensations intact' }}
                </div>
            </div>

            <div class="sec">Motor Examination</div>
            <div class="row sub"><label>ROM</label><i>:</i>
                <div class="v">{{ $data['neuro_motor_rom'] ?? '-' }}</div>
            </div>
            <div class="row sub"><label>MMT</label><i>:</i>
                <div class="v">{{ $data['neuro_motor_mmt'] ?? $data['neuro_mmt'] ?? '-' }}</div>
            </div>
            <div class="row sub"><label>Tone</label><i>:</i>
                <div class="v">{{ $data['neuro_motor_tone'] ?? $data['neuro_tone'] ?? '-' }}</div>
            </div>

            <div class="sec">Reflex</div>
            <div class="row sub"><label>DTR</label><i>:</i>
                <div class="v">{{ $data['neuro_reflex_dtr'] ?? $data['deep_tendon_reflexes'] ?? '-' }}</div>
            </div>
            <div class="row sub"><label>Pattern</label><i>:</i>
                <div class="ch">
                    {!! $chips(['Flexors synergic pattern', 'Extensor synergic pattern'], $data['neuro_synergic_pattern'] ?? []) !!}
                </div>
            </div>

            <div class="sec">Cortical Level Reflex</div>
            <div class="row sub"><label>Balance</label><i>:</i>
                <div class="v">{{ $data['neuro_cortical_balance'] ?? '-' }}</div>
            </div>
            <div class="row sub"><label>Equilibrium</label><i>:</i>
                <div class="v">{{ $data['neuro_cortical_equilibrium'] ?? '-' }}</div>
            </div>
            <div class="row sub"><label>Coordination</label><i>:</i>
                <div class="v">{{ $data['neuro_cortical_coordination'] ?? '-' }}</div>
            </div>

            <div class="sec">Investigation</div>
            <div class="row"><label>Investigation</label><i>:</i>
                <div>
                    <div class="ch">
                        {!! $chips(['CT', 'MRI', 'EMG', 'NCV'], $data['neuro_investigation'] ?? $data['investigations'] ?? []) !!}
                    </div>
                    @if(!empty($data['neuro_investigation_notes']) || !empty($data['investigation_notes']))
                        <span class="note ink">{{ $data['neuro_investigation_notes'] ?? $data['investigation_notes'] }}</span>
                    @endif
                </div>
            </div>
            <div class="row"><label>Treatment Plan</label><i>:</i>
                <div class="v">
                    {{ $data['neuro_treatment_plan'] ?? $prescription->treatment_plan ?? 'Physiotherapy Neuro-Rehabilitation Protocol' }}
                </div>
            </div>
        @endif

        <!-- DIAGNOSIS -->
        <div class="sec">Diagnosis</div>
        <div class="row"><label>Diagnosis</label><i>:</i>
            <div class="v">{{ $prescription->diagnosis_summary }}</div>
        </div>

        <!-- PRESCRIBED REHAB PROTOCOL -->
        <div class="sec">Prescribed Physiotherapy Rehabilitation Protocol
            @if($prescription->treatment_days)<span style="float:right;font-weight:600;font-size:12px">Treatment
            Duration: <span class="ink">{{ $prescription->treatment_days }} Days</span></span>@endif
        </div>
        @if($prescription->modalities)
            <div class="row"><label>Modalities / Electrotherapy</label><i>:</i>
                <div class="ch">@foreach(explode(',', $prescription->modalities) as $mod)<span
                class="on">{{ trim($mod) }}</span>@endforeach</div>
            </div>
        @endif

        @if(!empty($prescription->prescribed_exercises) && count($prescription->prescribed_exercises) > 0)
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Exercise Name</th>
                        <th>Target Area</th>
                        <th>Sets</th>
                        <th>Reps</th>
                        <th>Duration / Hold</th>
                        <th>Instructions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($prescription->prescribed_exercises as $idx => $ex)
                        <tr>
                            <td>{{ $idx + 1 }}</td>
                            <td class="n">{{ $ex['name'] ?? 'Exercise' }}</td>
                            <td class="n">{{ $ex['target'] ?? '-' }}</td>
                            <td class="n">{{ $ex['sets'] ?? '-' }}</td>
                            <td class="n">{{ $ex['reps'] ?? '-' }}</td>
                            <td class="n">{{ $ex['duration'] ?? '-' }}</td>
                            <td class="n">{{ $ex['instructions'] ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <!-- MEDICINES -->
        @if($prescription->items->count() > 0)
            <div class="sec"><span style="font:italic 900 18px Georgia,serif;color:var(--blue)">℞</span> Prescribed
                Medications / Supplements</div>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Medicine Details</th>
                        <th>Dosage</th>
                        <th>Frequency</th>
                        <th>Duration</th>
                        <th>Timing &amp; Instructions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($prescription->items as $idx => $item)
                        <tr>
                            <td>{{ $idx + 1 }}</td>
                            <td class="n">{{ $item->medicine_name }}</td>
                            <td class="n">{{ $item->dosage }}</td>
                            <td class="n">{{ $item->frequency }}</td>
                            <td class="n">{{ $item->duration }}</td>
                            <td class="n">{{ $item->timing }} {{ $item->instructions ? '• ' . $item->instructions : '' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <!-- ADVICE & FOLLOW UP -->
        <div class="sec">Advice &amp; Follow-up</div>
        <div class="row"><label>Ergonomic Advice &amp; Precautions</label><i>:</i>
            <div class="v">
                {{ $prescription->advice ?? 'Perform prescribed exercises twice daily. Maintain ergonomic spine posture while sitting. Avoid heavy lifting.' }}
            </div>
        </div>
        <div class="row"><label>Next Scheduled Review</label><i>:</i>
            <div class="v">
                {{ $prescription->follow_up_date ? $prescription->follow_up_date->format('d M Y') : 'After completing treatment course / SOS' }}
            </div>
        </div>
        <div class="note">Kindly bring this chart during every physiotherapy session.</div>

        <!-- FOOTER -->
        <div class="foot">
            <div class="ft">
                <div><span>Changed Address :</span> <b>Branch 1</b> 584-C, Khatiwala Tank, Indore, Web:
                    www.sdpcindore.com</div>
                <div><b>Branch 2</b> MR6-110, Mahalaxmi Nagar, Indore Ph. 0731-4979170</div>
                <small>Prescription generated electronically by CarePoint Clinic System. Valid across all
                    branches.</small>
            </div>
            <div class="sign">
                <div class="l">{{ $docName }}</div>
                <b style="font-size:12px">{{ $docName }}</b>
                <small>{{ $docQual }}</small>
                <small>Doctor's Digital Signature &amp; Stamp</small>
            </div>
        </div>

    </div>
</body>

</html>