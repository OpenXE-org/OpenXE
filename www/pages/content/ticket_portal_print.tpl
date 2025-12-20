<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="utf-8">
  <title>Ticket Ausdruck</title>
  <style>
    body { font-family: Arial, sans-serif; color: #111; margin: 24px; }
    .header { display: flex; justify-content: space-between; align-items: flex-start; }
    .qr { border: 1px solid #ccc; padding: 8px; display: inline-block; }
    .meta { margin-top: 16px; }
    .meta h2 { margin: 0 0 8px 0; }
    .block { margin-top: 16px; }
    .label { font-weight: bold; }
    .actions { margin-top: 24px; }
    .actions a, .actions button { margin-right: 12px; }
    @media print {
      .actions { display: none; }
      body { margin: 0; }
    }
  </style>
</head>
<body>
  <div class="header">
    <div>
      <h1>Ticket Ausdruck</h1>
      <div class="label">Ticketnummer:</div>
      <div>[TICKETNUMMER]</div>
    </div>
    <div class="qr">
      [QR_HTML]
    </div>
  </div>

  <div class="meta">
    <h2>Kundendaten</h2>
    <div>[KUNDENADRESSE]</div>
    <div class="block"><span class="label">E-Mail:</span> [EMAIL]</div>
  </div>

  <div class="block">
    <div class="label">Betreff:</div>
    <div>[BETREFF]</div>
  </div>

  <div class="block">
    <div class="label">Fehlerbeschreibung:</div>
    <div>[FEHLERBESCHREIBUNG]</div>
  </div>

  <div class="block">
    <div class="label">QR Ziel (Mitarbeiter):</div>
    <div>[STAFF_URL]</div>
  </div>

  <div class="actions">
    <button type="button" onclick="window.print()">Drucken</button>
    <a href="[DOWNLOAD_URL]">Download</a>
  </div>
</body>
</html>
