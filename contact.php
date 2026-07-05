<?php
/**
 * Base segura para el formulario de Kalli. Durante la Fase 3 se conectará
 * el formulario principal del sitio a este endpoint.
 */
require_once __DIR__ . '/includes/contact-config.php';

function kalli_clean(string $value): string {
    return trim(strip_tags($value));
}

function kalli_is_valid_phone(string $phone): bool {
    return (bool) preg_match('/^[0-9+()\s.-]{7,24}$/', $phone);
}

$success = false;
$error = '';
$values = [
    'name' => '',
    'phone' => '',
    'email' => '',
    'interest' => '',
    'message' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $values['name'] = kalli_clean($_POST['name'] ?? '');
    $values['phone'] = kalli_clean($_POST['phone'] ?? '');
    $values['email'] = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $values['interest'] = kalli_clean($_POST['interest'] ?? '');
    $values['message'] = kalli_clean($_POST['message'] ?? '');
    $honeypot = trim($_POST['website'] ?? '');
    $token = $_POST['form_token'] ?? '';

    if (!empty($honeypot)) {
        $error = 'No fue posible enviar tu solicitud. Inténtalo nuevamente.';
    } elseif (!hash_equals(KALLI_FORM_TOKEN, $token)) {
        $error = 'Tu sesión de formulario expiró. Recarga la página e inténtalo nuevamente.';
    } elseif ($values['name'] === '' || $values['email'] === '' || $values['interest'] === '') {
        $error = 'Completa los campos obligatorios para continuar.';
    } elseif (!filter_var($values['email'], FILTER_VALIDATE_EMAIL)) {
        $error = 'Escribe un correo electrónico válido.';
    } elseif ($values['phone'] !== '' && !kalli_is_valid_phone($values['phone'])) {
        $error = 'Escribe un teléfono válido.';
    } elseif (KALLI_CONTACT_EMAIL === 'REEMPLAZA_CON_TU_CORREO@DOMINIO.COM') {
        $error = 'Falta configurar el correo de recepción en includes/contact-config.php.';
    } else {
        $subject = 'Nueva solicitud desde Kalli';
        $body = "Nueva solicitud de información\n\n";
        $body .= "Nombre: {$values['name']}\n";
        $body .= "Teléfono: {$values['phone']}\n";
        $body .= "Correo: {$values['email']}\n";
        $body .= "Interés: {$values['interest']}\n";
        $body .= "Mensaje: {$values['message']}\n";
        $headers = [
            'MIME-Version: 1.0',
            'Content-type: text/plain; charset=UTF-8',
            'From: ' . KALLI_FROM_NAME . ' <' . KALLI_FROM_EMAIL . '>',
            'Reply-To: ' . $values['email']
        ];

        if (@mail(KALLI_CONTACT_EMAIL, $subject, $body, implode("\r\n", $headers))) {
            $success = true;
            $values = array_fill_keys(array_keys($values), '');
        } else {
            $error = 'No pudimos enviar tu solicitud en este momento. Inténtalo nuevamente o contáctanos por WhatsApp.';
        }
    }
}
?>
<!doctype html>
<html lang="es-MX">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="theme-color" content="#10255c">
  <meta name="description" content="Agenda una asesoría con Kalli Inmobiliaria & Arquitectura.">
  <meta name="robots" content="noindex,follow">
  <link rel="icon" href="favicon.ico" sizes="any">
  <link rel="icon" type="image/png" sizes="512x512" href="favicon-512.png">
  <link rel="apple-touch-icon" href="apple-touch-icon.png">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <title>Agenda una asesoría | Kalli</title>
  <link rel="stylesheet" href="css/styles.css">
  <style>
    .contact-page { min-height: 100vh; padding: 9.5rem 0 5rem; background: linear-gradient(135deg, var(--mist), var(--sky-100)); }
    .contact-page__wrap { display: grid; grid-template-columns: minmax(0,.88fr) minmax(0,1.12fr); gap: clamp(2rem, 8vw, 7rem); align-items: start; }
    .contact-page__aside { padding-top: clamp(.5rem, 3vw, 2rem); }
    .contact-page__title { max-width: 610px; margin: 0; color: var(--navy-900); font-family: var(--font-display); font-size: clamp(3.5rem, 6.8vw, 6.6rem); font-weight: 400; letter-spacing: -.055em; line-height: .9; }
    .contact-page__copy { max-width: 460px; margin: 1.55rem 0 0; color: var(--slate); font-size: 1rem; line-height: 1.8; }
    .contact-page__notes { display: grid; gap: .8rem; margin: 2.4rem 0 0; padding: 0; list-style: none; color: var(--navy-800); font-size: .72rem; font-weight: 800; letter-spacing: .07em; text-transform: uppercase; }
    .contact-page__notes li { display: flex; gap: .65rem; align-items: center; }
    .contact-page__notes span { width: 6px; height: 6px; border-radius: 50%; background: var(--teal-500); }
    .contact-card { padding: clamp(1.4rem, 3vw, 2.7rem); background: var(--paper); border: 1px solid rgba(16,37,92,.1); border-radius: var(--radius-lg); box-shadow: var(--shadow); }
    .contact-card__label { margin: 0 0 .55rem; color: var(--blue-700); font-size: .7rem; font-weight: 800; letter-spacing: .13em; text-transform: uppercase; }
    .contact-card h2 { margin: 0 0 1.9rem; color: var(--navy-900); font-family: var(--font-display); font-size: 2.4rem; font-weight: 400; letter-spacing: -.035em; line-height: 1; }
    .form-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; }
    .form-field { display: grid; gap: .5rem; }
    .form-field--full { grid-column: 1 / -1; }
    .form-field label { color: var(--navy-900); font-size: .68rem; font-weight: 800; letter-spacing: .09em; text-transform: uppercase; }
    .form-field input, .form-field select, .form-field textarea { width: 100%; padding: .95rem 1rem; color: var(--ink); background: #f8fcfc; border: 1px solid rgba(16,37,92,.16); border-radius: .72rem; outline: none; transition: border-color .2s ease, box-shadow .2s ease, background .2s ease; }
    .form-field textarea { min-height: 140px; resize: vertical; }
    .form-field input:focus, .form-field select:focus, .form-field textarea:focus { background: var(--paper); border-color: var(--teal-500); box-shadow: 0 0 0 4px rgba(85,187,194,.16); }
    .form-notice { margin: 0 0 1.3rem; padding: .95rem 1rem; border-radius: .72rem; font-size: .88rem; }
    .form-notice--success { color: #0f5b52; background: #def3ef; }
    .form-notice--error { color: #8b2f38; background: #fbe8ea; }
    .form-hp { position: absolute; left: -9999px; opacity: 0; pointer-events: none; }
    .contact-card .button--primary { margin-top: 1.5rem; }
    .contact-page__back { display: inline-flex; gap: .55rem; align-items: center; margin-top: 2.3rem; color: var(--navy-900); font-size: .7rem; font-weight: 800; letter-spacing: .09em; text-transform: uppercase; }
    .contact-page__back span { font-size: 1rem; transition: transform .2s ease; }
    .contact-page__back:hover span { transform: translateX(-3px); }
    .contact-header { position: fixed; z-index: 100; inset: 0 0 auto; background: rgba(255,255,255,.97); border-bottom: 1px solid var(--line); box-shadow: 0 8px 28px rgba(16,37,92,.06); }
    .contact-header__inner { display: flex; justify-content: space-between; gap: 1rem; align-items: center; min-height: 84px; }
    .contact-header .brand { width: 165px; }
    @media (max-width: 800px) { .contact-page { padding-top: 7.8rem; } .contact-page__wrap { grid-template-columns: 1fr; gap: 2.3rem; } .contact-page__aside { padding-top: 0; } }
    @media (max-width: 520px) { .contact-header__inner { min-height: 73px; } .contact-header .brand { width: 128px; } .contact-header .button { min-height: 39px; padding: .64rem .76rem; font-size: .56rem; } .contact-page { padding-top: 6.7rem; } .form-grid { grid-template-columns: 1fr; } .form-field--full { grid-column: auto; } .contact-card { border-radius: var(--radius); } }
  </style>
</head>
<body>
  <header class="contact-header">
    <div class="container contact-header__inner">
      <a class="brand" href="index.html#inicio" aria-label="Kalli, volver al inicio"><img src="img/logo/kalli-logo-official.png" width="927" height="510" alt="Kalli Inmobiliaria & Arquitectura"></a>
      <a class="button button--small button--nav" href="index.html#inicio">Volver al inicio <span aria-hidden="true">↖</span></a>
    </div>
  </header>
  <main class="contact-page">
    <div class="container contact-page__wrap">
      <section class="contact-page__aside" aria-labelledby="contact-title">
        <p class="section-kicker">Conversemos</p>
        <h1 class="contact-page__title" id="contact-title">Tu siguiente espacio puede empezar hoy.</h1>
        <p class="contact-page__copy">Compártenos qué tienes en mente. Un asesor de Kalli podrá orientarte sobre opciones, proyectos y los siguientes pasos para llevar tu visión a algo real.</p>
        <ul class="contact-page__notes" aria-label="Beneficios de la asesoría"><li><span aria-hidden="true"></span>Atención personalizada</li><li><span aria-hidden="true"></span>Una conversación clara y sin compromiso</li><li><span aria-hidden="true"></span>Inmobiliaria & Arquitectura</li></ul>
        <a class="contact-page__back" href="index.html#inicio"><span aria-hidden="true">←</span> Volver al inicio</a>
      </section>
      <section class="contact-card" aria-labelledby="form-title">
        <p class="contact-card__label">Agenda una asesoría</p>
        <h2 id="form-title">Hablemos de lo que imaginas.</h2>
        <?php if ($success): ?>
          <p class="form-notice form-notice--success" role="status">Gracias. Recibimos tu solicitud y nos pondremos en contacto contigo pronto.</p>
        <?php elseif ($error): ?>
          <p class="form-notice form-notice--error" role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
        <form method="post" action="contact.php" novalidate>
          <input type="hidden" name="form_token" value="<?= htmlspecialchars(KALLI_FORM_TOKEN, ENT_QUOTES, 'UTF-8') ?>">
          <div class="form-hp" aria-hidden="true"><label>Website<input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>
          <div class="form-grid">
            <div class="form-field"><label for="name">Nombre *</label><input id="name" name="name" type="text" maxlength="100" autocomplete="name" required value="<?= htmlspecialchars($values['name'], ENT_QUOTES, 'UTF-8') ?>"></div>
            <div class="form-field"><label for="phone">Teléfono</label><input id="phone" name="phone" type="tel" maxlength="24" autocomplete="tel" value="<?= htmlspecialchars($values['phone'], ENT_QUOTES, 'UTF-8') ?>"></div>
            <div class="form-field form-field--full"><label for="email">Correo electrónico *</label><input id="email" name="email" type="email" maxlength="150" autocomplete="email" required value="<?= htmlspecialchars($values['email'], ENT_QUOTES, 'UTF-8') ?>"></div>
            <div class="form-field form-field--full"><label for="interest">Me interesa *</label><select id="interest" name="interest" required><option value="">Selecciona una opción</option><?php foreach (['Conocer opciones inmobiliarias', 'Iniciar un proyecto arquitectónico', 'Explorar una inversión', 'Agendar una asesoría', 'Otro'] as $option): ?><option value="<?= htmlspecialchars($option, ENT_QUOTES, 'UTF-8') ?>"<?= $values['interest'] === $option ? ' selected' : '' ?>><?= htmlspecialchars($option, ENT_QUOTES, 'UTF-8') ?></option><?php endforeach; ?></select></div>
            <div class="form-field form-field--full"><label for="message">Cuéntanos un poco más</label><textarea id="message" name="message" maxlength="1000" placeholder="¿Qué tipo de espacio, proyecto u oportunidad estás buscando?"><?= htmlspecialchars($values['message'], ENT_QUOTES, 'UTF-8') ?></textarea></div>
          </div>
          <button class="button button--primary" type="submit">Enviar solicitud <span aria-hidden="true">↗</span></button>
        </form>
      </section>
    </div>
  </main>
</body>
</html>
