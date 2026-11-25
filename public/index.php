<?php
declare(strict_types=1);
error_reporting(E_ALL);

require __DIR__ . '/../src/validate.php';

/** Escape for HTML output. */
function e(string $s): string
{
    return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Read a POST field safely as a trimmed string (arrays/missing -> ''). */
function post_str(string $key): string
{
    $v = $_POST[$key] ?? '';
    return is_string($v) ? trim($v) : '';
}

$values = ['name' => '', 'email' => '', 'message' => ''];
$errors = [];

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    foreach ($values as $k => $_) {
        $values[$k] = post_str($k);
    }

    if (!validate_required($values['name'])) {
        $errors['name'] = 'Name is required.';
    } elseif (!validate_length($values['name'], 2, 60)) {
        $errors['name'] = 'Name must be 2–60 characters.';
    }

    if (!validate_required($values['email'])) {
        $errors['email'] = 'Email is required.';
    } elseif (!validate_email($values['email'])) {
        $errors['email'] = 'Please enter a valid email address.';
    }

    if (!validate_required($values['message'])) {
        $errors['message'] = 'Message is required.';
    } elseif (!validate_length($values['message'], 10, 1000)) {
        $errors['message'] = 'Message must be 10–1000 characters.';
    }

    if (!$errors) {
        // Post/Redirect/Get: never render a success page from the POST itself.
        header('Location: ?sent=1', true, 303);
        exit;
    }
    // Invalid: fall through and redisplay with status 200.
}

$sent = ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'GET' && isset($_GET['sent']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Contact Form</title>
    <style>
        body { font-family: sans-serif; max-width: 480px; margin: 2rem auto; }
        label { display: block; margin-top: 1rem; font-weight: bold; }
        input, textarea { width: 100%; padding: .4rem; box-sizing: border-box; }
        .error { color: #b00020; font-size: .9rem; margin: .2rem 0 0; }
        .success { background: #e6f4ea; padding: .8rem; border-radius: 4px; }
    </style>
</head>
<body>
<h1>Contact Us</h1>

<?php if ($sent): ?>
    <p class="success">Thank you! Your message was sent.</p>
<?php endif; ?>

<form method="post" action="" novalidate>
    <label for="name">Name</label>
    <input type="text" id="name" name="name" value="<?= e($values['name']) ?>">
    <?php if (isset($errors['name'])): ?>
        <p class="error"><?= e($errors['name']) ?></p>
    <?php endif; ?>

    <label for="email">Email</label>
    <input type="email" id="email" name="email" value="<?= e($values['email']) ?>">
    <?php if (isset($errors['email'])): ?>
        <p class="error"><?= e($errors['email']) ?></p>
    <?php endif; ?>

    <label for="message">Message</label>
    <textarea id="message" name="message" rows="6"><?= e($values['message']) ?></textarea>
    <?php if (isset($errors['message'])): ?>
        <p class="error"><?= e($errors['message']) ?></p>
    <?php endif; ?>

    <p><button type="submit">Send</button></p>
</form>
</body>
</html>