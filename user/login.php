<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

$errors = [];

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize_input($_POST['username']);
    $password = $_POST['password'];

    if (empty($username)) {
        $errors[] = trans('err_username_required');
    }

    if (empty($password)) {
        $errors[] = trans('err_password_required');
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];

            if (isset($_SESSION['redirect_to'])) {
                $redirect_url = $_SESSION['redirect_to'];
                unset($_SESSION['redirect_to']);
                header("Location: " . $redirect_url);
                exit();
            }

            header("Location: dashboard.php");
            exit();
        } else {
            $errors[] = trans('err_login_failed');
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
                <?php echo trans('login'); ?>
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
                <form action="login.php" method="post">
                    <div class="form-group">
                        <label for="username"><?php echo trans('username'); ?></label>
                        <input type="text" name="username" id="username" class="form-control" value="<?php echo isset($username) ? htmlspecialchars($username) : ''; ?>">
                    </div>
                    <div class="form-group">
                        <label for="password"><?php echo trans('password'); ?></label>
                        <input type="password" name="password" id="password" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-primary"><?php echo trans('login'); ?></button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
