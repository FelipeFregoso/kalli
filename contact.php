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
  <meta name="theme-color" content="#1e3029">
  <title>Agenda una visita | Kalli</title>
  <link rel="icon" type="image/svg+xml" href="favicon.svg">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;0,700;1,600&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/styles.css">
  <style>
    .contact-page { min-height: 100vh; padding: 7.75rem 0 4rem; background: var(--ivory-100); }
    .contact-page__wrap { display: grid; grid-template-columns: .86fr 1.14fr; gap: clamp(2rem, 7vw, 6rem); align-items: start; }
    .contact-page__aside { padding-top: 1rem; }
    .contact-page__title { max-width: 540px; margin: 0; color: var(--forest-900); font-family: var(--font-display); font-size: clamp(3rem, 6vw, 5.6rem); font-weight: 600; line-height: .9; letter-spacing: -.05em; }
    .contact-page__copy { max-width: 420px; margin-top: 1.5rem; color: #59635b; }
    .contact-card { padding: clamp(1.35rem, 3vw, 2.5rem); background: var(--white); border: 1px solid rgba(111,122,89,.18); border-radius: var(--radius-lg); box-shadow: var(--shadow-md); }
    .contact-card__label { margin: 0 0 .45rem; color: var(--olive-600); font-size: .7rem; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; }
    .contact-card h2 { margin: 0 0 1.8rem; color: var(--forest-900); font-family: var(--font-display); font-size: 2.1rem; font-weight: 600; line-height: 1; }
    .form-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; }
    .form-field { display: grid; gap: .5rem; }
    .form-field--full { grid-column: 1 / -1; }
    .form-field label { color: var(--forest-900); font-size: .74rem; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; }
    .form-field input, .form-field select, .form-field textarea { width: 100%; padding: .9rem 1rem; color: var(--charcoal-900); background: var(--ivory-100); border: 1px solid rgba(111,122,89,.24); border-radius: .6rem; outline: none; transition: border-color .2s ease, box-shadow .2s ease; }
    .form-field textarea { min-height: 130px; resize: vertical; }
    .form-field input:focus, .form-field select:focus, .form-field textarea:focus { border-color: var(--olive-600); box-shadow: 0 0 0 3px rgba(111,122,89,.14); }
    .form-notice { margin: 0 0 1.25rem; padding: .9rem 1rem; border-radius: .6rem; font-size: .86rem; }
    .form-notice--success { color: #17492e; background: #dfeee4; }
    .form-notice--error { color: #6d2e2d; background: #f4e3e0; }
    .form-hp { position: absolute; left: -9999px; opacity: 0; pointer-events: none; }
    .contact-page .button--primary { margin-top: 1.4rem; color: var(--ivory-100); background: var(--forest-900); }
    .contact-page .button--primary:hover { background: var(--forest-800); }
    .contact-page__back { display: inline-flex; gap: .55rem; align-items: center; margin-top: 2rem; color: var(--forest-900); font-size: .72rem; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; }
    @media (max-width: 800px) { .contact-page__wrap { grid-template-columns: 1fr; gap: 2rem; } .contact-page__aside { padding-top: 0; } }
    @media (max-width: 520px) { .contact-page { padding-top: 6.3rem; } .form-grid { grid-template-columns: 1fr; } .form-field--full { grid-column: auto; } }
  </style>
</head>
<body>
  <header class="site-header is-scrolled">
    <div class="site-header__inner container">
      <a class="brand" href="index.html#inicio" aria-label="Kalli, volver al inicio"><img src="img/logo/kalli-logo-light.svg" width="126" height="50" alt="Kalli"></a>
      <div></div>
      <a class="button button--outline" href="index.html#inicio">Volver al inicio</a>
    </div>
  </header>
  <main class="contact-page">
    <div class="container contact-page__wrap">
      <section class="contact-page__aside" aria-labelledby="contact-title">
        <p class="eyebrow" style="color: var(--olive-600);"><span style="background: var(--gold-500);"></span>Conoce Kalli</p>
        <h1 class="contact-page__title" id="contact-title">El primer paso hacia tu próximo hogar.</h1>
        <p class="contact-page__copy">Compártenos tus datos y un asesor podrá brindarte información sobre Kalli, disponibilidad y el proceso para agendar una visita.</p>
        <a class="contact-page__back" href="index.html#inicio"><span aria-hidden="true">←</span> Volver al inicio</a>
      </section>
      <section class="contact-card" aria-labelledby="form-title">
        <p class="contact-card__label">Agenda una visita</p>
        <h2 id="form-title">Hablemos de tu siguiente etapa.</h2>
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
            <div class="form-field form-field--full"><label for="interest">Me interesa *</label><select id="interest" name="interest" required><option value="">Selecciona una opción</option><?php foreach (['Conocer el desarrollo', 'Agendar una visita', 'Disponibilidad', 'Inversión', 'Otro'] as $option): ?><option value="<?= htmlspecialchars($option, ENT_QUOTES, 'UTF-8') ?>"<?= $values['interest'] === $option ? ' selected' : '' ?>><?= htmlspecialchars($option, ENT_QUOTES, 'UTF-8') ?></option><?php endforeach; ?></select></div>
            <div class="form-field form-field--full"><label for="message">Mensaje</label><textarea id="message" name="message" maxlength="1000" placeholder="Cuéntanos qué información te gustaría recibir."><?= htmlspecialchars($values['message'], ENT_QUOTES, 'UTF-8') ?></textarea></div>
          </div>
          <button class="button button--primary" type="submit">Enviar solicitud <span aria-hidden="true">↗</span></button>
        </form>
      </section>
    </div>
  </main>
</body>
</html>
