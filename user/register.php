<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize_input($_POST['username']);
    $email = sanitize_input($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($username)) {
        $errors[] = trans('err_username_required');
    }

    if (empty($email)) {
        $errors[] = trans('err_email_required');
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = trans('err_email_invalid');
    }

    if (empty($password)) {
        $errors[] = trans('err_password_required');
    } elseif (strlen($password) < 6) {
        $errors[] = trans('err_password_min_len');
    }

    if ($password !== $confirm_password) {
        $errors[] = trans('err_passwords_do_not_match');
    }

    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $hashed_password);

        if ($stmt->execute()) {
            header("Location: login.php");
            exit();
        } else {
            $errors[] = trans('err_registration_failed');
        }

        $stmt->close();
    }
}

include '../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <?php echo trans('register'); ?>
            </div>
            <div class="card-body">
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul>
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo $error; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                <form action="register.php" method="post">
                    <div class="form-group">
                        <label for="username"><?php echo trans('username'); ?></label>
                        <input type="text" name="username" id="username" class="form-control" value="<?php echo isset($username) ? htmlspecialchars($username) : ''; ?>">
                    </div>
                    <div class="form-group">
                        <label for="email"><?php echo trans('email'); ?></label>
                        <input type="email" name="email" id="email" class="form-control" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>">
                    </div>
                    <div class="form-group">
                        <label for="password"><?php echo trans('password'); ?></label>
                        <input type="password" name="password" id="password" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="confirm_password"><?php echo trans('confirm_password'); ?></label>
                        <input type="password" name="confirm_password" id="confirm_password" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-primary"><?php echo trans('register'); ?></button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
