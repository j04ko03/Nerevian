<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Bill of Lading - {{ $operacion->codi_referencia }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 10pt; color: #333; margin: 0; padding: 0; }
        .container { padding: 30px; }
        .header { border-bottom: 2px solid #1a8a7d; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { color: #1a8a7d; margin: 0; font-size: 24pt; }
        .grid { display: table; width: 100%; border-collapse: collapse; }
        .row { display: table-row; }
        .col { display: table-cell; border: 1px solid #ccc; padding: 10px; vertical-align: top; width: 50%; }
        .label { font-weight: bold; font-size: 8pt; color: #666; text-transform: uppercase; margin-bottom: 5px; }
        .value { font-size: 11pt; }
        .details-table { width: 100%; margin-top: 20px; border-collapse: collapse; }
        .details-table th { background: #f2f2f2; border: 1px solid #ccc; padding: 8px; text-align: left; font-size: 8pt; }
        .details-table td { border: 1px solid #ccc; padding: 10px; font-size: 10pt; }
        .footer { margin-top: 40px; border-top: 1px solid #ccc; padding-top: 10px; font-size: 8pt; text-align: center; color: #999; }
        .stamp { border: 2px solid #1a8a7d; color: #1a8a7d; padding: 10px; display: inline-block; font-weight: bold; transform: rotate(-5deg); margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>BILL OF LADING</h1>
            <div style="float: right; margin-top: -35px;">
                <strong>REF: {{ $operacion->codi_referencia }}</strong><br>
                <span>Date: {{ $operacion->data_inici }}</span>
            </div>
        </div>

        <div class="grid">
            <div class="row">
                <div class="col">
                    <div class="label">Shipper / Exporter</div>
                    <div class="value">
                        <strong>{{ $solicitud->client->empresa ?? $solicitud->client->usuari->nom }}</strong><br>
                        {{ $solicitud->client->adreca ?? 'N/A' }}<br>
                        {{ $solicitud->client->poblacio ?? '' }}
                    </div>
                </div>
                <div class="col">
                    <div class="label">Consignee (To order of)</div>
                    <div class="value">TO ORDER</div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="label">Port of Loading</div>
                    <div class="value">{{ $solicitud->port_origen->nom ?? 'N/A' }}</div>
                </div>
                <div class="col">
                    <div class="label">Port of Discharge</div>
                    <div class="value">{{ $solicitud->port_desti->nom ?? 'N/A' }}</div>
                </div>
            </div>
        </div>

        <table class="details-table">
            <thead>
                <tr>
                    <th>Container No. / Marks & Nos.</th>
                    <th>No. of Pkgs.</th>
                    <th>Description of Goods</th>
                    <th>Gross Weight</th>
                    <th>Measurement</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $solicitud->tipus_contenidor->tipus ?? 'Standard' }}</td>
                    <td>1</td>
                    <td>
                        {{ $solicitud->tipus_carrega->tipus }}<br>
                        Incoterm: {{ $solicitud->incoterm->codi ?? 'N/A' }}
                    </td>
                    <td>{{ $solicitud->pes_brut }} KGS</td>
                    <td>{{ $solicitud->volum ?? '-' }} CBM</td>
                </tr>
            </tbody>
        </table>

        <div style="margin-top: 20px;">
            <div class="label">Freight & Charges</div>
            <div class="value">FREIGHT PREPAID</div>
        </div>

        <div style="text-align: right;">
            <div class="stamp">ORIGINAL</div>
            <p style="margin-top: 40px;">
                _________________________________<br>
                Authorized Signature (Nerevian Logistics)
            </p>
        </div>

        <div class="footer">
            Nerevian Logistics SL - Specialized Maritime Transport Services<br>
            Subject to the terms and conditions of the Nerevian Bill of Lading.
        </div>
    </div>
</body>
</html>
