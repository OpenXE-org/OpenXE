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
    .meta h2 { margin: 0 0 8px 0; border-bottom: 2px solid #333; }
    .block { margin-top: 16px; }
    .label { font-weight: bold; margin-bottom: 4px; }
    .actions { margin-top: 24px; border-top: 1px solid #eee; padding-top: 12px; }
    .actions a, .actions button { margin-right: 12px; }
    
    .message { border-left: 4px solid #ccc; padding: 10px; margin-bottom: 12px; background: #f9f9f9; }
    .message.customer { border-color: #2271b1; }
    .message.staff { border-color: #666; }
    .message.system { border-color: #ffb900; background: #fffcf5; }
    .message .meta { font-size: 0.85rem; color: #555; margin-bottom: 4px; }
    
    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    th, td { text-align: left; padding: 8px; border-bottom: 1px solid #ddd; }
    th { background: #f2f2f2; }

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

  <div class="meta">
    <h2>Angebote</h2>
    [OFFERS_HTML]
  </div>

  <div class="meta">
    <h2>Nachrichtenverlauf</h2>
    [MESSAGES_HTML]
  </div>

  <div class="actions">
    <button type="button" onclick="window.print()">Drucken</button>
    <a href="[DOWNLOAD_URL]">Download</a>
  </div>
</body>
</html>
